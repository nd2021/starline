Бибилиотека для работы с API Stratline.

## Установка

Пакет можно установить через composer:

```shell
composer require nd2021/starline --prefer-dist
```

## Требования

- PHP 8.1 и выше

## Использование

```php
use StarLine\Client;
use StarLine\Config;

$config = new Config();

$config->setAppId('you-app-id')
    ->setAppSecret('you-app-secret')
    ->setLogin('you-login')
    ->setPassword('you-password');

$client = new Client($config);

$devices = $client->getUserDevices();

foreach ($devices as $device) {
    //получение имени устройства
    $title = $device->alias;
    
    //получение баланса устройства
    $balance = $device->balance[0]->value;
    
    //получение координат
    $x = $device->position->x;
    $y = $device->position->y;
}
```

## Документация

Описание всех возвращаемых параметров можно посомтреть в [src/Entity/Device.php](src/Entity/Device.php)

## Документация API Starline

https://developer.starline.ru/

## Лицензия
[Apache-2.0](http://www.apache.org/licenses/LICENSE-2.0)