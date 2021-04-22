<?php


namespace Bx\Geolocation\Models;

use Bx\Model\AbsOptimizedModel;
use Bx\Model\ModelCollection;

class Location extends AbsOptimizedModel
{

    /**
     * @var Location
     */
    private $parentLocation;
    /**
     * @var Location[]|ModelCollection
     */
    private $children;

    protected function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'type' => $this->getLocationType(),
            'sort' => $this->getSort(),
            'lat' => $this->getLatitude(),
            'lon' => $this->getLongitude(),
            'name' => $this->getLocationName(),
            'children' => $this->getChildren()->getApiModel(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this['ID'];
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this['CODE'];
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return (int)$this['SORT'];
    }

    /**
     * @return string
     */
    public function getLocationName(): string
    {
        return (string)$this['location_name'];
    }

    /**
     * @return string
     */
    public function getLocationType(): string
    {
        return strtolower((string)$this['location_type']);
    }

    /**
     * @return int
     */
    public function getLeftMargin(): int
    {
        return (int)$this['LEFT_MARGIN'];
    }

    /**
     * @return int
     */
    public function getRightMargin(): int
    {
        return (int)$this['RIGHT_MARGIN'];
    }

    /**
     * @return int
     */
    public function getDepthLevel(): int
    {
        return (int)$this['DEPTH_LEVEL'];
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return (float)$this['LATITUDE'];
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return (float)$this['LONGITUDE'];
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return (int)$this['PARENT_ID'];
    }

    /**
     * @return Location|null
     */
    public function getParent(): ?Location
    {
        return $this->parentLocation instanceof Location ? $this->parentLocation : null;
    }

    /**
     * @param Location $location
     */
    public function setParent(Location $location)
    {
        $this->parentLocation = $location;
    }

    /**
     * @param Location $location
     */
    public function addChildren(Location $location)
    {
        $this->getChildren()->addModel($location);
    }

    /**
     * @return ModelCollection
     */
    public function getChildren(): ModelCollection
    {
        if (!($this->children instanceof ModelCollection)) {
            $this->children = new ModelCollection([], Location::class);
        }

        return $this->children;
    }
}