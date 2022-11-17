<?php

namespace StarLine;

use Exception;
use GuzzleHttp\Client as GClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use StarLine\Objects\Device;

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
    private function send(Request $request): ResponseInterface
    {
        $c = new GClient([
            'base_uri' => 'https://developer.starline.ru/'
        ]);

        /** @var Request $request */
        $request = $request->withHeader('Cookie', 'slnet=' . $this->config->getSlnetToken());

        // первый запрос
        $response = $c->send($request);
        if ($response->getStatusCode() === 200) {
            return $response;
        }

        // при ошибке пробуем авторизоваться и снова сделать запрос
        if ($this->is_authorized = Auth::auth($this->config)) {
            $response = $c->send($request);
            if ($response->getStatusCode() !== 200) {
                $this->is_authorized = false;
            }
        }

        return $response;
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
        $response = $this->send($request);
        if ($response->getStatusCode() !== 200) {
            throw new Exception('Не удалось получить данные пользователя.');
        }
        $content = $response->getBody()->getContents();
        $result = json_decode($content, true);

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
                $devices[] = new Device($device_data);
            }
        } catch (Exception $e) {
            $this->exception = $e;
            return null;
        } catch (GuzzleException $e) {
            $this->exception = new Exception($e->getMessage(), $e->getCode(), $e);
            return null;
        }

        return $devices;
    }
}