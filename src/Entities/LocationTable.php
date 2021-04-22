<?php


namespace Bx\Geolocation\Entities;


use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;

class LocationTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        Loader::includeModule('sale');
        return \Bitrix\Sale\Location\LocationTable::getTableName();
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap(): array
    {
        Loader::includeModule('sale');
        $lid = Application::getInstance()->getContext()->getLanguage();
        $map = \Bitrix\Sale\Location\LocationTable::getMap();
        $map['NAME'] = new ReferenceField(
            'NAME',
            \Bitrix\Sale\Location\Name\LocationTable::class,
            Join::on('this.ID', 'ref.LOCATION_ID')->where('ref.LANGUAGE_ID', $lid)
        );

        return $map;
    }
}