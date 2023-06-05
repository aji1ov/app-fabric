<?php

namespace App\Fabric\Install;

class DB
{
    public static function install()
    {
        Spl::load(__DIR__.'/../registry/*.php');
        foreach(Spl::filter('App\\Fabric\\Registry\\') as $dbclass)
        {
            if(is_a($dbclass, \Bitrix\Main\Entity\DataManager::class, true))
            {
                $connection = \Bitrix\Main\Application::getInstance()->getConnection();
                if(!$connection->isTableExists($dbclass::getTableName()))
                {
                    $dbclass::getEntity()->createDbTable();
                }
            }
        }
    }
}