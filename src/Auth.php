<?php

namespace StarLine;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;

class Auth
{
    /**
     * Сервис для авторизации пользователей starline-online.ru и других сервисов СтарЛайн
     *
     * @var string
     */
    private string $star_line_id_url = 'https://id.starline.ru/';
    /**
     * API сервис для доступа к данным устройств телематического сервиса starline-online.ru
     *
     * @var string
     */
    private string $star_line_api_url = 'https://developer.starline.ru/';

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    private function getClient($base_uri): Client
    {
        return new Client([
            'base_uri' => $base_uri
        ]);
    }

    public static function auth(Config $config): bool
    {
        $auth = new self($config);

        // Пробуем сразу авторизоваться на API, если есть SLID токен
        if ($auth->APIAuth()) {
            return true;
        }
        // Иначе проходим полную авторизацию
        return $auth->SLIDAuth() && $auth->APIAuth();
    }

    /**
     * Авторизация пользователя в StarLineId (SLID).
     * В результате будет получен токен со сроком действия 1 год для авторизации в WebAPI.
     *
     * @return bool
     */
    public function SLIDAuth(): bool
    {
        if (!$this->config->checkMain()) {
            return false;
        }
        try {
            $app_code = $this->getAppCode($this->config->getAppId(), $this->config->getAppSecret());
            $app_token = $this->getAppToken($this->config->getAppId(), $this->config->getAppSecret(), $app_code);
            $slid_token = $this->getSlidUserToken($app_token, $this->config->getLogin(), $this->config->getPassword());
        } catch (Exception|GuzzleException $e) {
            return false;
        }

        $this->config
            ->setAppCode($app_code)
            ->setAppToken($app_token)
            ->setSlidToken($slid_token);

        return true;
    }

    /**
     * Авторизация в WebAPI.
     * В результате будет получен токен со сроком действия 24 часа, используемый для запросов в API.
     *
     * @return bool
     */
    public function APIAuth(): bool
    {
        $slid_token = $this->config->getSlidToken();
        if (empty($slid_token)) {
            return false;
        }
        try {
            [$slnet_token, $user_id] = $this->getSlnetToken($slid_token);
        } catch (Exception|GuzzleException $e) {
            return false;
        }

        $this->config
            ->setSlnetToken($slnet_token)
            ->setUserId($user_id);

        return true;
    }

    /**
     * Получить код приложения. Действителен 1 час
     *
     * @param string $app_id
     * @param string $app_secret
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAppCode(string $app_id, string $app_secret): string
    {
        $response = $this->getClient($this->star_line_id_url)
            ->request('GET', '/apiV3/application/getCode', [
                'query' => [
                    'appId' => $app_id,
                    'secret' => md5($app_secret),
                ]
            ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Не удалось получить AppCode');
        }
        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);
        $code = $result['desc']['code'] ?? null;
        if (empty($code)) {
            throw new Exception('Не удалось получить AppCode');
        }
        return $code;
    }

    /**
     * Получить токен приложения. Действителен 4 часа
     *
     * @param string $app_id
     * @param string $app_secret
     * @param string $app_code
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAppToken(string $app_id, string $app_secret, string $app_code): string
    {
        $response = $this->getClient($this->star_line_id_url)
            ->request('GET', '/apiV3/application/getToken', [
                'query' => [
                    'appId' => $app_id,
                    'secret' => md5($app_secret . $app_code),
                ]
            ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Не удалось получить AppToken');
        }
        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);
        $token = $result['desc']['token'] ?? null;
        if (empty($token)) {
            throw new Exception('Не удалось получить AppToken');
        }
        return $token;
    }

    /**
     * Получить slid-токен пользователя. Действителен 1 год
     *
     * Аутентификация пользователя по логину и паролю.
     * Неверные данные авторизации или слишком частое выполнение запроса авторизации с одного
     * ip-адреса может привести к запросу капчи.
     * Для того чтобы сервер SLID корректно обрабатывал клиентский IP,
     * необходимо проксировать его в параметре user_ip.
     * В противном случае все запросы авторизации будут фиксироваться для IP-адреса сервера приложения,
     * что приведет к частому требованию капчи.
     *
     * @param string $app_token
     * @param string $login
     * @param string $password
     * @return string
     * @throws GuzzleException
     * @throws Exception
     */
    public function getSlidUserToken(string $app_token, string $login, string $password): string
    {
        $response = $this->getClient($this->star_line_id_url)
            ->request('POST', '/apiV3/user/login', [
                'query' => [
                    'token' => $app_token
                ],
                'form_params' => [
                    'login' => $login,
                    'pass' => sha1($password),
//                'user_ip' => '',
//                'smsCode' => '',
//                'captchaSid' => '',
//                'captchaCode' => ''
                ]
            ]);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Не удалось получить SlidUserToken');
        }
        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);
        $token = $result['desc']['user_token'] ?? null;
        if (empty($token)) {
            throw new Exception('Не удалось получить SlidUserToken');
        }
        return $token;
    }

    /**
     * Авторизоваться на StarLineAPI сервере
     * С полученным slid-токеном можно обращаться к API-метода сервера StarLineAPI
     * slid-токен действителен 24 часа
     *
     * @param string $slid_token
     * @return array [$slnet_token, $user_id]
     * @throws GuzzleException
     * @throws Exception
     */
    public function getSlnetToken(string $slid_token): array
    {
        $jar = new CookieJar();
        $response = $this->getClient($this->star_line_api_url)
            ->request('POST', '/json/v2/auth.slid', [
                'json' => [
                    'slid_token' => $slid_token
                ],
                'cookies' => $jar
            ]);
        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);
        $user_id = $result['user_id'] ?? null;
        $slnet_token = $jar->getCookieByName('slnet')?->getValue();

        if ($response->getStatusCode() !== 200 || empty($slnet_token) || empty($user_id)) {
            throw new Exception('Не удалось получить slnet_token/user_id');
        }

        return [$slnet_token, $user_id];
    }
}