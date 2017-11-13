<?php
namespace admin\components;

use Yii;

class DateTime {

    /*
     * pastikan field timezone ada pada tabel user!
     * untuk list timezone bisa lihat di : http://php.net/manual/en/timezones.php
     */

    private $timeString;
    private $serverTimeZone = 'GMT';

    public function timeFromString($timeString){
        $this->timeString = $timeString;
        return $this;
    }

    public function timeToServerZone($timeString){
        $userTimezone   = new \DateTimeZone(Yii::$app->user->identity->timezone);
        $serverTime     = new \DateTime($timeString, new \DateTimeZone($this->serverTimeZone));
        $offset         = $userTimezone->getOffset($serverTime);
        $myInterval     = \DateInterval::createFromDateString((string)$offset . 'seconds');
        $serverTime->add($myInterval);
        return $serverTime;
    }

    public function timeFromServerZone($timeString){
        $userTimezone   = new \DateTimeZone(Yii::$app->user->identity->timezone);
        $serverTime     = new \DateTime($timeString, new \DateTimeZone($this->serverTimeZone));
        $offset         = $userTimezone->getOffset($serverTime);
        $myInterval     = \DateInterval::createFromDateString((string)$offset . 'seconds');
        $serverTime->sub($myInterval);
        return $serverTime;
    }

    public function serverTime($timeString){
        $serverTime     = new \DateTime('now', new \DateTimeZone($this->serverTimeZone));
        return $serverTime->format($timeString);
    }
}
