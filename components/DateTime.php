<?php
namespace admin\components;

use Yii;

class DateTime {

    /*
     * pastikan field timezone ada pada tabel user!
     * untuk list timezone bisa lihat di : http://php.net/manual/en/timezones.php
     */

    private $timeString;
    private $serverTimeZone = 'UTC';

    public function timeFromString($timeString){
        $this->timeString = $timeString;
        return $this;
    }

    public function timeToServerZone($timeString){
        $serverTime     = new \DateTime($timeString, new \DateTimeZone($this->serverTimeZone));
        return $serverTime;
    }

    public function timeFromServerZone($timeString){
        $userTimezone   = new \DateTimeZone(Yii::$app->params['timezone']);
        $serverTime     = new \DateTime($timeString, new \DateTimeZone($this->serverTimeZone));
        return $serverTime->setTimezone($userTimezone);
    }

    public function serverTime($timeString){
        $serverTime     = new \DateTime('now', new \DateTimeZone($this->serverTimeZone));
        return $serverTime->format($timeString);
    }
}
