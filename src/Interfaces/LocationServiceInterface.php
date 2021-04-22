<?php


namespace Bx\Geolocation\Interfaces;


use Bx\Geolocation\Models\Location;
use Bx\Model\ModelCollection;

interface LocationServiceInterface extends ModelServiceInterface
{
    /**
     * Местоположение пользователя по ip
     * @param string|null $ip
     * @return Location|null
     */
    public function getByIp(string $ip = null): ?Location;

    /**
     * Список местоположений (текущее местоположение + родительские местоположения)
     * @param string|null $ip
     * @return Location[]|ModelCollection
     */
    public function getCollectionByIp(string $ip = null): ModelCollection;

    /**
     * Возвращает местоположение по названию
     * @param string $name
     * @param bool $strict
     * @return Location|null
     */
    public function getByName(string $name, bool $strict = true): ?Location;

    /**
     * Возвращает местоположение по названию + родительские местоположения
     * @param string $name
     * @param bool $strict
     * @return Location[]|ModelCollection
     */
    public function getCollectionByName(string $name, bool $strict = true): ModelCollection;

    /**
     * @param GeolocationServiceInterface $geolocationService
     * @return void
     */
    public function setGeolocationService(GeolocationServiceInterface $geolocationService);

    /**
     * @param float $lat
     * @param float $lon
     * @return Location|null
     */
    public function getByCoords(float $lat, float $lon, int $distLimit = 50): ?Location;

    /**
     * @param float $lat
     * @param float $lon
     * @return Location[]|ModelCollection
     */
    public function getCollectionByCoords(float $lat, float $lon, int $distLimit = 50): ModelCollection;

    /**
     * @param Location $location
     * @return Location[]|ModelCollection
     */
    public function getChildParentCollection(Location $location): ModelCollection;

    /**
     * @param ModelCollection $locationCollection
     * @return ModelCollection
     */
    public function buildTree(ModelCollection $locationCollection): ModelCollection;
}