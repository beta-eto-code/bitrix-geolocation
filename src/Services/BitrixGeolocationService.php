<?php

namespace Bx\Geolocation\Services;


use Bx\Geolocation\BaseGeoLocationService;
use Bx\Geolocation\Interfaces\LocationServiceInterface;
use Bitrix\Main\Service\GeoIp;

final class BitrixGeolocationService extends BaseGeoLocationService
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale = null)
    {
        $this->locale = $locale ?? 'ru';
    }

    /**
     * @param LocationServiceInterface $locationService
     * @param string|null $ip
     * @return string
     */
    protected function getLocationName(LocationServiceInterface $locationService, string $ip = null): string
    {
        if (empty($ip)) {
            $ip = GeoIp\Manager::getRealIp();
        }

        $result = GeoIp\Manager::getDataResult($ip, $this->locale, ['cityName']);
        if (empty($result)) {
            return '';
        }

        $data = $result->getGeoData();

        return empty($data) || empty($data->cityName) ? '' : $data->cityName;
    }
}