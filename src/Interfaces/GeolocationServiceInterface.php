<?php


namespace Bx\Geolocation\Interfaces;


use Bx\Geolocation\Models\Location;

interface GeolocationServiceInterface
{
    /**
     * @param LocationServiceInterface $locationService
     * @param string|null $ip
     * @return Location|null
     */
    public function getLocationByIp(LocationServiceInterface $locationService, string $ip = null): ?Location;

    /**
     * @param GeolocationServiceInterface $geolocationService
     * @return void
     */
    public function setNext(GeolocationServiceInterface $geolocationService);
}