<?php


namespace Bx\Geolocation;


use Bx\Geolocation\Models\Location;
use Bx\Geolocation\Interfaces\GeolocationServiceInterface;
use Bx\Geolocation\Interfaces\LocationServiceInterface;

abstract class BaseGeoLocationService implements GeolocationServiceInterface
{
    /**
     * @var GeolocationServiceInterface|null
     */
    private $nextGeolocation;

    /**
     * @param LocationServiceInterface $locationService
     * @param string|null $ip
     * @return string
     */
    abstract protected function getLocationName(LocationServiceInterface $locationService, string $ip = null): string;

    /**
     * @param LocationServiceInterface $locationService
     * @param string|null $ip
     * @return Location|null
     */
    public function getLocationByIp(LocationServiceInterface $locationService, string $ip = null): ?Location
    {
        $locationName = $this->getLocationName($locationService, $ip);
        if (empty($locationName)) {
            return $this->nextGeolocation instanceof GeolocationServiceInterface ?
                $this->nextGeolocation->getLocationByIp($locationService, $ip) :
                null;
        }

        return $locationService->getByName($locationName, true);
    }

    /**
     * @param GeolocationServiceInterface $geolocationService
     */
    public function setNext(GeolocationServiceInterface $geolocationService)
    {
        $this->nextGeolocation = $geolocationService;
    }
}