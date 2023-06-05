<?php

namespace App\Fabric\Registry;

use Bitrix\Main\Entity;

class ScheduleTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'app_fabric_schedule';
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
            new Entity\StringField('SCHEDULE_ID', [
                'required' => true
            ]),
            new Entity\IntegerField('TIME_EXEC', [
                'required' => true
            ]),
        ];
    }

    public static function dropOldTasks(array $ids)
    {
        $rs = static::getList(['filter' => ['!SCHEDULE_ID' => $ids]]);
        while($row = $rs->fetch())
        {
            static::delete($row['ID']);
        }
    }

    public static function createNewTasks(array $tasks)
    {
        $rs = static::getList(['filter' => ['SCHEDULE_ID' => array_keys($tasks)]]);
        while($row = $rs->fetch())
        {
            unset($tasks[$row['SCHEDULE_ID']]);
        }

        foreach($tasks as $schedule_id => $next_time_exec)
        {
            static::add([
                'SCHEDULE_ID' => $schedule_id,
                'TIME_EXEC' => $next_time_exec
            ]);
        }
    }

    public static function getActiveTasks(int $now)
    {
        if(!$now) $now = time();

        $result = [];
        $rs = static::getList(['filter' => ['<=TIME_EXEC' => $now]]);
        while($row = $rs->fetch())
        {
            $result[] = $row;
        }

        return $result;
    }

    public static function getAllTasks(): array
    {
        $result = [];
        $rs = static::getList();
        while($row = $rs->fetch())
        {
            $result[$row['SCHEDULE_ID']] = $row['TIME_EXEC'];
        }

        return $result;
    }

    public static function updateTaskTime(int $row_id, int $time)
    {
        static::update($row_id, ['TIME_EXEC' => $time]);
    }
}