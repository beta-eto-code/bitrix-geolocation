# Bitrix Geolocation

Пример использования:

```php
use Bx\Geolocation\Services\BitrixGeolocationService;
use Bx\Geolocation\Services\LocationService;

$someIp = 'XXX.XXX.XXX.XXX';
$locationService = new LocationService();
$geolocationService = new BitrixGeolocationService('ru');
$location = $geolocationService->getLocationByIp($locationService, $someIp);
$location->getLocationName();
$location->getLatitude();
$location->getLongitude();
$location->getCode();
$location->getLocationType();
```

Пример реализации собстрвенного сервиса геолокации:

```php
use Bx\Geolocation\BaseGeoLocationService;
use Bx\Geolocation\Interfaces\LocationServiceInterface;

class MyAwesomeGeoService extends BaseGeoLocationService 
{
    protected function getLocationName(LocationServiceInterface $locationService, string $ip = null): string
    {
        // код реализующий поиск по ip и возвращающий название местоположения
    }
}
```

Пример совместного использования нескольких сервисов геолокации:

```php
use Bx\Geolocation\Services\BitrixGeolocationService;
use Bx\Geolocation\Services\LocationService;
use Bx\Geolocation\Interfaces\GeolocationServiceInterface;

$someIp = 'XXX.XXX.XXX.XXX';
$locationService = new LocationService();

/**
* @var GeolocationServiceInterface $myAwesomeGeoService
*/
$myAwesomeGeoService = new MyAwesomeGeoService();
$bitrixGeolocation = new BitrixGeolocationService('ru');
$myAwesomeGeoService->setNext($bitrixGeolocation); // объединяем сервисы в цепочку

/**
* сначала произойдет поиск через MyAwesomeGeoService а в случае неудачи через BitrixGeolocationService
*/
$location = $myAwesomeGeoService->getLocationByIp($locationService, $someIp);
```