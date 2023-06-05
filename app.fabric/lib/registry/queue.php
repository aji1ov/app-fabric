<?php

namespace App\Fabric\Registry;

use App\Fabric\Job;
use Bitrix\Main\Entity;

class QueueTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'app_fabric_job_queue';
    }

    /**
     * @return Entity\IntegerField[]
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Entity\TextField('CALLABLE', [
                'required' => true
            ]),
            new Entity\IntegerField('TIMESTAMP', [
                'required' => true
            ]),
        ];
    }

    public static function load(): ?string
    {
        $params = [
            'order' => ['ID' => 'ASC'], //get oldest
            'filter' => ['<=TIMESTAMP' => time()]
        ];
        if($row = static::getList($params)->fetch())
        {
            $job = $row['CALLABLE'];
            static::delete($row['ID']);

            return $job;
        }

        return null;
    }

    public static function dump(string $job, int $timestamp)
    {
        static::add(['CALLABLE' => $job, 'TIMESTAMP' => $timestamp]);
    }

    public static function getFirst(int $limit = 10)
    {
        $result = [];
        $rs = static::getList([
            'order' => ['ID' => 'ASC'], //get oldest
            'filter' => ['<=TIMESTAMP' => time()],
            'limit' => $limit
        ]);
        while($row = $rs->fetch())
        {
            $result[] = $row;
        }

        return $result;
    }

}