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
     * Отправить запрос на API.
     *
     * @throws GuzzleException
     */
    private function request(Request $request): ?array
    {
        $c = new GClient([
            'base_uri' => 'https://developer.starline.ru/',
            'cookies' => true
        ]);

        // первый запрос
        if ($result = $this->send($c, $request)) {
            return $result;
        }

        // при ошибке пробуем авторизоваться и снова сделать запрос
        if ($this->is_authorized = Auth::auth($this->config)) {
            if ($result = $this->send($c, $request)) {
                return $result;
            }
        }

        $this->is_authorized = false;
        return null;
    }

    private function send(GClient $client, Request $request)
    {
        $jar = CookieJar::fromArray([
            'slnet' => $this->config->getSlnetToken()
        ], 'developer.starline.ru');
        $response = $client->send($request, [
            RequestOptions::COOKIES => $jar
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        if ($response->getStatusCode() === 200 && isset($result['code']) && $result['code'] === 200) {
            return $result;
        }
        return null;
    }

    /**
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    private function getUserData(): array
    {
        if (!$this->isReady()) {
            throw new Exception('Нельзя произвести запрос. Авторизация невозможна.');
        }

        $request = new Request('GET', '/json/v3/user/' . $this->config->getUserId() . '/data');
        $result = $this->request($request);

        if (empty($result['user_data']['devices']) || !is_array($result['user_data']['devices'])) {
            throw new Exception('Получен неожиданный формат данных от API.');
        }

        return $result;
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

            $devices = [];
            foreach ($user_data['user_data']['devices'] as $device_data) {
                $devices[] = $this->mapper()->map(
                    Device::class,
                    Source::array($device_data)
                );
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