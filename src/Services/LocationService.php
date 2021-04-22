<?php


namespace Bx\Geolocation\Services;


use Bx\Geolocation\Entities\LocationTable;
use Bx\Geolocation\Models\Location;
use Bx\Model\AbsOptimizedModel;
use Bx\Model\ModelCollection;
use Bx\Model\BaseModelService;
use Bx\Geolocation\Interfaces\GeolocationServiceInterface;
use Bx\Geolocation\Interfaces\LocationServiceInterface;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bx\Model\Interfaces\UserContextInterface;
use Exception;

class LocationService extends BaseModelService implements LocationServiceInterface
{
    /**
     * @var GeolocationServiceInterface
     */
    private $geolocationService;

    public function __construct(GeolocationServiceInterface $geolocationService = null)
    {
        $this->geolocationService = $geolocationService ?? new BitrixGeolocationService();
    }

    /**
     * @return DataManager
     */
    private function getTableEntity()
    {
        return LocationTable::class;
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return Location[]|ModelCollection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(array $params, UserContextInterface $userContext = null): ModelCollection
    {
        $params['select'] = $params['select'] ?? ['*', 'location_name' => 'NAME.NAME', 'location_type' => 'TYPE.CODE'];

        $table = $this->getTableEntity();
        $list = $table::getList($params);

        return new ModelCollection($list, Location::class);
    }

    /**
     * @param array $params
     * @param UserContextInterface|null $userContext
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getCount(array $params, UserContextInterface $userContext = null): int
    {
        $countParams = [
            'filter' => $params['filter'] ?? [],
            'select' => [
                'ID'
            ],
            'count_total' => true,
        ];
        $table = $this->getTableEntity();

        return $table::getList($countParams)->getCount();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return Location|AbsOptimizedModel|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getById(int $id, UserContextInterface $userContext = null): ?AbsOptimizedModel
    {
        $params = [
            'filter' => [
                '=id' => $id,
            ],
        ];

        return $this->getList($params, $userContext)->first();
    }

    /**
     * @param int $id
     * @param UserContextInterface|null $userContext
     * @return Result
     * @throws SystemException
     * @throws Exception
     */
    public function delete(int $id, UserContextInterface $userContext = null): Result
    {
        throw new NotImplementedException('Not implemented method');
    }

    /**
     * @param Location|AbsOptimizedModel $model
     * @param UserContextInterface|null $userContext
     * @return Result
     * @throws Exception
     */
    public function save(AbsOptimizedModel $model, UserContextInterface $userContext = null): Result
    {
        throw new NotImplementedException('Not implemented method');
    }

    static protected function getSortFields(): array
    {
        return [];
    }

    static protected function getFilterFields(): array
    {
        return [
            'name' => 'NAME.NAME',
            'type' => 'TYPE.CODE',
        ];
    }

    /**
     * @param string|null $ip
     * @return Location|null
     */
    public function getByIp(string $ip = null): ?Location
    {
        return $this->geolocationService->getLocationByIp($this, $ip);
    }

    /**
     * @param Location $location
     * @return Location[]|ModelCollection
     */
    public function getChildParentCollection(Location $location): ModelCollection
    {
        return $this->getList([
            'filter' => [
                '>=RIGHT_MARGIN' => $location->getRightMargin(),
                '<=LEFT_MARGIN' => $location->getLeftMargin(),
            ],
        ]);
    }

    /**
     * @param string|null $ip
     * @return Location[]|ModelCollection
     */
    public function getCollectionByIp(string $ip = null): ModelCollection
    {
        $location = $this->getByIp($ip);
        if (empty($location)) {
            return new ModelCollection([], Location::class);
        }

        return $this->getChildParentCollection($location);
    }

    /**
     * @param string $name
     * @param bool $strict
     * @return Location|null
     */
    public function getByName(string $name, bool $strict = true): ?Location
    {
        $filter = $strict ? ['=NAME.NAME' => $name] : ['%NAME.NAME' => $name];

        return $this->getList([
            'filter' => $filter,
            'limit' => 1,
        ])->first();
    }

    /**
     * @param string $name
     * @param bool $strict
     * @return Location[]|ModelCollection
     */
    public function getCollectionByName(string $name, bool $strict = true): ModelCollection
    {
        $location = $this->getByName($name, $strict);
        if (empty($location)) {
            return new ModelCollection([], Location::class);
        }

        return $this->getChildParentCollection($location);
    }

    /**
     * @param GeolocationServiceInterface $geolocationService
     */
    public function setGeolocationService(GeolocationServiceInterface $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * @param float $lat
     * @param float $lon
     * @param int $distLimit
     * @return Location|null
     * @throws SystemException
     */
    public function getByCoords(float $lat, float $lon, int $distLimit = 50): ?Location
    {
        return $this->getList([
            'runtime' => [
                'geodist' => new ExpressionField(
                    'geodist',
                    "(
                          3959 * acos (
                          cos ( radians({$lat}) )
                          * cos( radians( %s ) )
                          * cos( radians( %s ) - radians({$lon}) )
                          + sin ( radians({$lat}) )
                          * sin( radians( %s ) )
                        )
                    )",
                    ['LATITUDE', 'LONGITUDE', 'LATITUDE']
                ),
            ],
            'select' => [
                '*',
                'location_name' => 'NAME.NAME',
                'geodist',
            ],
            'filter' => [
                '<=geodist' => $distLimit
            ],
            'order' => [
                'geodist' => 'asc',
            ],
            'limit' => 1,
        ])->first();
    }

    /**
     * @param float $lat
     * @param float $lon
     * @param int $distLimit
     * @return Location[]|ModelCollection
     */
    public function getCollectionByCoords(float $lat, float $lon, int $distLimit = 50): ModelCollection
    {
        $location = $this->getByCoords($lat, $lon);
        if (empty($location)) {
            return new ModelCollection([], Location::class);
        }

        return $this->getChildParentCollection($location);
    }

    /**
     * @param Location[]|ModelCollection $locationCollection
     * @return Location[]|ModelCollection
     */
    public function buildTree(ModelCollection $locationCollection): ModelCollection
    {
        foreach ($locationCollection as $location) {
            $parentId = $location->getParentId();
            if (!$parentId) {
                continue;
            }

            $parent = $locationCollection->findByColumn('ID', $parentId);
            if ($parent instanceof Location) {
                $location->setParent($parent);
                $parent->addChildren($location);
            }
        }

        return $locationCollection->filter(function (Location $location) {
            return !$location->getParent();
        });
    }
}