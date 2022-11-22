<?php

namespace StarLine;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Exception;
use GuzzleHttp\Client as GClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use StarLine\Entity\Device;

class Client
{
    /**
     * @var bool|null успешна ли авторизация
     */
    private ?bool $is_authorized = null;

    private ?Exception $exception = null;

    /**
     * @var array|null результат запроса, сохраняется при получении ответа от АПИ
     */
    private ?array $result = null;

    public function __construct(
        private readonly Config $config
    ) {
        // проверка наличия токена для запросов к АПИ, если нет, то пробуем авторизоваться
        if (!$this->config->checkAPIToken()) {
            $this->is_authorized = Auth::auth($this->config);
        }
        // если токен есть, то доп запроса на авторизацию не делаем (токен всё ещё может быть устаревшим)
        return $this->isReady();
    }

    public function isReady(): bool
    {
        return $this->is_authorized !== false;
    }

    public function getException(): ?Exception
    {
        return $this->exception;
    }

    /**
     * Отправить запрос на API
     *
     * @param Request $request
     * @return bool
     */
    private function request(Request $request): bool
    {
        // первый запрос
        if ($this->send($request)) {
            return true;
        }

        // при ошибке пробуем авторизоваться и снова сделать запрос
        return ($this->is_authorized = Auth::auth($this->config)) && $this->send($request);
    }

    private function send(Request $request): bool
    {
        static $client = null;

        if (is_null($client)) {
            $client = new GClient([
                'base_uri' => 'https://developer.starline.ru/'
            ]);
        }
        $jar = CookieJar::fromArray([
            'slnet' => $this->config->getSlnetToken()
        ], 'developer.starline.ru');

        $response = $client->send($request, [
            RequestOptions::COOKIES => $jar
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() === 200 && is_array($result)) {
            $this->result = $result;
            return isset($result['code']) && $result['code'] === 200;
        }
        $this->result = null;
        return false;
    }

    /**
     * @return array|null
     * @throws Exception
     */
    private function getUserData(): ?array
    {
        if (!$this->isReady()) {
            throw new Exception('Нельзя произвести запрос. Авторизация невозможна.');
        }

        $request = new Request('GET', '/json/v3/user/' . $this->config->getUserId() . '/data');

        if ($this->request($request)) {
            if (empty($this->result['user_data']['devices']) || !is_array($this->result['user_data']['devices'])) {
                throw new Exception('Получен неожиданный формат данных от API.');
            }
        }

        return $this->result;
    }

    /**
     * Получить информацию об устройствах пользователя
     *
     * @return Device[]|null
     */
    public function getUserDevices(): ?array
    {
        try {
            $user_data = $this->getUserData();
            if ($user_data) {
                $devices = [];
                foreach ($user_data['user_data']['devices'] as $device_data) {
                    $devices[] = $this->mapper()->map(
                        Device::class,
                        Source::array($device_data)
                    );
                }
            }
        } catch (MappingError|Exception $e) {
            $this->exception = $e;
            return null;
        } catch (GuzzleException $e) {
            $this->exception = new Exception($e->getMessage(), $e->getCode(), $e);
            return null;
        }

        return $devices;
    }

    private function mapper(): TreeMapper
    {
        static $mapper = null;
        if (is_null($mapper)) {
            $mapper = (new MapperBuilder())
                ->enableFlexibleCasting()
                ->allowPermissiveTypes()
//                ->allowSuperfluousKeys()
                ->mapper();
        }
        return $mapper;
    }
}