<?php

/**
 * Rule class
 * class to manage access rules of workplaces
 * backed by database table wp_rules
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 23.05.16
 * Time: 13:45
 */
class Rule
{
    /** @var  string */
    private $id;
    /** @var  DateInterval */
    private $start;
    /** @var  DateInterval */
    private $end;
    /** @var DateInterval */
    private $pauseStart;
    /** @var DateInterval */
    private $pauseEnd;
    /** @var DateInterval */
    private $registrationStart;
    /** @var DateInterval */
    private $registrationEnd;
    /** @var DateInterval */
    private $slotDuration;
    /** @var bool */
    private $oneScheduleByDayAndUser;
    /** @var bool[7] */
    private $days;

    /**
     * Rule constructor.
     * @param string $id
     * @param int $start
     * @param int $end
     * @param $pauseStart
     * @param $pauseEnd
     * @param int $registrationStart
     * @param int $registrationEnd
     * @param $slotDuration
     * @param bool $oneScheduleByDayAndUser
     * @param array $days
     */
    private function __construct($id, $start, $end, $pauseStart, $pauseEnd, $registrationStart, $registrationEnd, $slotDuration, $oneScheduleByDayAndUser = false, $days = array(true, true, true, true, true, true, true))
    {
        $this->id = $id;
        $this->start = new DateInterval($start);
        $this->end = new DateInterval($end);
        if($pauseStart == null || $pauseEnd == null) {
            $this->pauseStart = null;
            $this->pauseEnd = null;
        } else {
            $this->pauseStart = new DateInterval($pauseStart);
            $this->pauseEnd = new DateInterval($pauseEnd);
        }
        $this->registrationStart = new DateInterval($registrationStart);
        $this->registrationEnd = new DateInterval($registrationEnd);
        $this->slotDuration = new DateInterval($slotDuration);
        $this->oneScheduleByDayAndUser = $oneScheduleByDayAndUser;
        $this->days = $days;
    }

    /**
     * Get slot Duration
     *
     * @return DateInterval
     */
    public function getSlotDuration()
    {
        return $this->slotDuration;
    }

    /**
     * Get interval between 00:00 and first possible slot
     *
     * @return DateInterval
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get interval between 00:00 and end and of last possible slot
     *
     * @return DateInterval
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get interval between 00:00 and start of pause
     *
     * @return DateInterval
     */
    public function getPauseStart() {
        return $this->pauseStart;
    }

    /**
     * Get interval between 00:00 and end of pause
     *
     * @return DateInterval
     */
    public function getPauseEnd() {
        return $this->pauseEnd;
    }

    /**
     * Get interval between the first possible slot of day and the first possible time to register for this day
     *
     * @return DateInterval
     */
    public function getRegistrationStart()
    {
        return $this->registrationStart;
    }

    /**
     * Get interval between the end of the slot day and the nd of the register time
     *
     * @return DateInterval
     */
    public function getRegistrationEnd()
    {
        return $this->registrationEnd;
    }

    /**
     * Get state of day
     *
     * @param int $numberOfDay
     *
     * Mon = 0
     * Tue = 1
     * Wed = 2
     * Thu = 3
     * Fri = 4
     * Sat = 5
     * Sun = 6
     * 
     * @return bool true if day is enabled false if disabled
     */
    public function getDay($numberOfDay)
    {
        return $this->days[$numberOfDay];
    }

    /**
     * Get rule id
     *
     * @return string id of this rule
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get "is one schedule by day and user"-flag
     *
     * @return boolean true if a user is only allowed to register for one schedule per day otherwise false
     */
    public function isOneScheduleByDayAndUser()
    {
        return $this->oneScheduleByDayAndUser;
    }



    /**
     * Set start of first possible stol in relation to day start in seconds
     *
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = new DateInterval($start);
        DBManager::get()->execute("UPDATE wp_rules SET start = ? WHERE  id = ?", array($start, $this->id));
    }

    /**
     * Set end of last possible slot in relation to day start in seconds
     *
     * @param int $end
     */
    public function setEnd($end)
    {
        $this->end = new DateInterval($end);
        DBManager::get()->execute("UPDATE wp_rules SET end = ? WHERE  id = ?", array($end, $this->id));
    }

    /**
     * Set pause start in relation to day start in seconds
     *
     * @param string $pauseStart
     */
    public function setPauseStart($pauseStart) {
        if($pauseStart == null) {
            $this->pauseStart = null;
        } else {
            $this->pauseStart = new DateInterval($pauseStart);
        }
        DBManager::get()->execute("UPDATE wp_rules SET pause_start = ? WHERE id = ?", array($pauseStart, $this->id));
    }

    /**
     * Set pause end in relation to day start in seconds
     *
     * @param string $pauseEnd
     */
    public function setPauseEnd($pauseEnd) {
        if($pauseEnd == null) {
            $this->pauseEnd = null;
        } else {
            $this->pauseEnd = new DateInterval($pauseEnd);
        };
        DBManager::get()->execute("UPDATE wp_rules SET pause_end = ? WHERE id = ?", array($pauseEnd, $this->id));
    }

    /**
     * Set start of registration in relation to first schedule of day as DateInterval constructor string
     *
     * @param string $registrationStart
     */
    public function setRegistrationStart($registrationStart)
    {
        $this->registrationStart = new DateInterval($registrationStart);
        DBManager::get()->execute("UPDATE wp_rules SET registration_start = ? WHERE  id = ?", array($registrationStart, $this->id));
    }

    /**
     * Set end of registrration in relation to day end of the slots as DateInterval constructor string
     *
     * @param string $registrationEnd
     */
    public function setRegistrationEnd($registrationEnd)
    {
        $this->registrationEnd = new DateInterval($registrationEnd);
        DBManager::get()->execute("UPDATE wp_rules SET registration_end = ? WHERE  id = ?", array($registrationEnd, $this->id));
    }

    /**
     * Set default slot duration in seconds
     *
     * @param int $slotDuration
     */
    public function setSlotDuration($slotDuration)
    {
        $this->slotDuration = new DateInterval($slotDuration);
        DBManager::get()->execute("UPDATE wp_rules SET slot_duration = ? WHERE  id = ?", array($slotDuration, $this->id));
    }

    /**
     * Set state of day
     *
     * @param int $dayOfWeek number of day
     *
     * Mon = 0
     * Tue = 1
     * Wed = 2
     * Thu = 3
     * Fri = 4
     * Sat = 5
     * Sun = 6
     *
     * @param bool $state true if day should be active or false if not
     */
    public function setDay($dayOfWeek, $state)
    {
        $this->days[$dayOfWeek] = $state;


        DBManager::get()->execute("UPDATE wp_rules SET days = ? WHERE id = ?", array(self::daysArrayToNumber($this->days), $this->id));
    }

    /**
     * Set "one schedule by day and user"-flag
     *
     * @param boolean $oneScheduleByDayAndUser true if one user should only register one schedule by day
     */
    public function setOneScheduleByDayAndUser($oneScheduleByDayAndUser)
    {
        $this->oneScheduleByDayAndUser = $oneScheduleByDayAndUser;

        DBManager::get()->execute("UPDATE wp_rules SET one_schedule_by_day_and_user = ? WHERE id = ?", array($oneScheduleByDayAndUser, $this->id));
    }


    /**
     * look if schedule is bookable
     *
     * @param DateTime $start start of the possible schedule
     * @param DateInterval $duration duration of the possible schedule
     * @param Workplace $workplace target workplace
     * @param bool $admin is booker an admin
     * @return bool true if day is bookable false if not
     */
    public function isBookable($start, $duration, $workplace, $admin = false) {
        if(Blacklist::getBlacklist()->isOnList(get_userid())){
            return false;
        }
        $end = clone $start;
        $end->add($duration);
        $day = date_create($start->format('d.m.Y'));
        $data = DBManager::get()->fetchAll("SELECT id FROM wp_schedule WHERE (workplace_id = ? AND start > ? ) AND ( start < ? )",
            array($workplace->getId(), $day->getTimestamp(), $day->getTimestamp() + 86400));
        foreach ($data as $schedule_id) {
            $schedule = Schedule::getSchedule($schedule_id);
            $scheduleEnd = clone $schedule->getStart();
            $scheduleEnd->add($schedule->getDuration());
            if(($schedule->getStart() <= $start && $scheduleEnd > $start) || ($schedule->getStart() < $end && $schedule->getStart() > $start)){
                return false;
            }
            if($this->oneScheduleByDayAndUser && $schedule->getOwner()->getUserid() == get_userid() && !$admin) {
                return false;
            }
        }
        return true;
    }

    /**
     * check if day is bookable
     *
     * @param DateTime $time any time of day that should be checked
     * @param bool $admin is booked an admin
     * @param Workplace|null $workplace target workplace. Is only needed if it should be checked against the "one schedule by day and user"-flag
     * @return bool true if day is bookable, false if not
     */
    public function isDayBookable($time, $admin, $workplace = null) {
        $day = new DateTime($time->format('d.m.Y'));

        $numberOfDayISO = $day->format('N');
        $numberOfDay = $numberOfDayISO - 1;

        $now = new DateTime();

        $feiertageConnector = LPLib_Feiertage_Connector::getInstance();

        $startCheckTime = clone $now;
        $endCheckTime = clone $now;
        if($startCheckTime->add($this->registrationStart)->sub($this->start) < $day && !$admin) {
            return false;
        }
        if($endCheckTime->add($this->registrationEnd)->sub(new DateInterval('PT23H23M')) > $day && !$admin) {
            return false;
        }
        if($workplace != null && $this->isOneScheduleByDayAndUser() && !$admin) {
            $dayStart = clone $day;
            $dayStart->add($this->getStart());
            $dayEnd = clone $day;
            $dayEnd->add($this->getEnd());
            $data = DBManager::get()->fetchAll("SELECT id FROM wp_schedule WHERE workplace_id = ? AND start >= ? AND start <= ? AND user_id = ?",
                array($workplace->getId(), $dayStart->getTimestamp(), $dayEnd->getTimestamp(), get_userid()));
            if (sizeof($data) > 0) {
                return false;
            }
        }
        if(Blacklist::getBlacklist($_GET['cid'])->isOnList(get_userid())) {
            return false;
        }
        if(!$this->getDay($numberOfDay)) {
            return false;
        }
        if($feiertageConnector->isFeiertagInLand($day->format('d.m.Y'), LPLib_Feiertage_Connector::LAND_NIEDERSACHSEN)) {
            return false;
        }
        return true;
    }

    /**
     * check if rule contains pause information
     *
     * @return bool true if rule contains pause, false if not
     */
    public function hasPause() {
        if($this->pauseStart == null || $this->pauseEnd == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * book the first available schedule of a specific day
     *
     * @param Workplace $workplace target workplace
     * @param DateTime $time any time on day that should booked
     * @param bool $admin is booker admin
     * @return bool true if schedule is booked, false if not
     */
    public function bookFirstPossibleSchedule($workplace, $time, $admin = false) {

        $day = new DateTime($time->format('d.m.Y'));
        $endOfDay = clone $day;
        $endOfDay->add(new DateInterval('PT23H59M59S'));

        $endTime = clone $day;
        $endTime->add($this->end);

        $pauseStart = clone $day;
        $pauseStart->add($this->pauseStart);
        $pauseEnd = clone $day;
        $pauseEnd->add($this->pauseEnd);

        $schedules = DBManager::get()->fetchAll("SELECT id FROM wp_schedule WHERE workplace_id = ? AND start > ? AND start < ? ORDER BY start ASC", array($workplace->getId(), $day->getTimestamp(), $endOfDay->getTimestamp()));

        $time = clone $day;
        $time->add($this->start);
        foreach($schedules as $id) {
            $schedule = Schedule::getSchedule($id['id']);

            $testTime = clone $time;
            $testTime->add($this->slotDuration);


            if($testTime >= $pauseStart && $time < $pauseEnd) {
                $time = clone  $pauseEnd;
            } else {
                if($schedule->getStart() > $time && $time->diff($schedule->getStart())->format('%h%I') >= $this->slotDuration->format('%h%I') && $this->isBookable($time,$this->slotDuration,$workplace, $admin)) {
                    Schedule::newSchedule(get_userid(), $workplace->getId(), $time->getTimestamp(), $this->slotDuration->format('P%yY%mM%dDT%hH%iM%sS'));
                    return true;
                }
                $time = clone $schedule->getStart();
                $time->add($schedule->getDuration());
            }
        }
        $testTime = clone $time;
        if($testTime->add($this->slotDuration) <= $endTime && $this->isBookable($time, $this->slotDuration, $workplace, $admin)){
            Schedule::newSchedule(get_userid(), $workplace->getId(), $time->getTimestamp(), $this->slotDuration->format('P%yY%mM%dDT%hH%iM%sS'));
            return true;
        }
        return false;
    }


    /*
     * static functions
     */

    /**
     * Get rule by id
     *
     * @param string $id rule id
     * @return null|Rule Rule if id exist, null if not
     */
    public static function getRule($id)
    {
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_rules WHERE id = ?", array($id));

        if(sizeof($data) < 1)
        {
            return null;
        }

        $data = $data[0];

        return new Rule(
            $data['id'],
            $data['start'], 
            $data['end'],
            $data['pause_start'],
            $data['pause_end'],
            $data['registration_start'], 
            $data['registration_end'], 
            $data['slot_duration'],
            $data['one_schedule_by_day_and_user'],
            self::daysNumberToArray($data['days'])
        );
    }

    /**
     * create new rule
     *
     * @param int $start start of first possible slot in relation to day start in seconds
     * @param int $end end of last possible slot in relation to day start in seconds
     * @param int $pauseStart start of pause in relation to day start in seconds
     * @param int $pauseEnd start of pause end in relation to day start in seconds
     * @param int $registrationStart start if registration period in relation to first possible slot of day in seconds
     * @param int $registrationEnd end of registration period in relation to day end in seconds
     * @param int $slotDuration default slot duration in seconds
     * @param bool $oneScheduleByDayAndUser "one schedule by day and user"-flag
     * @param array $days array of day states in boolean (<Mon>, <Tue>, <Wed>, <Thu>, <Fri>, <Sat>, <Sun>)
     *
     * @return null|Rule Rule if all ok, null on error
     */
    public static function newRule($start, $end, $pauseStart, $pauseEnd, $registrationStart, $registrationEnd, $slotDuration, $oneScheduleByDayAndUser = false, $days = array(false, false, false, false, false, false, false))
    {


        $id = sha1(rand());
        DBManager::get()->execute(
            "INSERT INTO wp_rules (id, start, end, pause_start, pause_end, registration_start, registration_end, slot_duration, one_schedule_by_day_and_user, days) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($id, $start, $end, $pauseStart, $pauseEnd, $registrationStart, $registrationEnd, $slotDuration, $oneScheduleByDayAndUser, self::daysArrayToNumber($days)));

        return self::getRule($id);
    }


    /**
     * convert days array to number saved in database
     *
     * @param array $days array of days (<Mon>, <Tue>, <Wed>, <Thu>, <Fri>, <Sat>, <Sun>)
     * @return int number to store in database
     */
    private static function daysArrayToNumber($days) {
        $daysNumber = 0;
        for($i = 0; $i <= 6; $i++) {
            if($days[$i]){
                $daysNumber += pow(2, $i);
            }
        }
        return $daysNumber;
    }

    /**
     * convert number stored in database to days array
     *
     * @param int $daysNumber number stored in database
     * @return array days array (<Mon>, <Tue>, <Wed>, <Thu>, <Fri>, <Sat>, <Sun>)
     */
    private static function daysNumberToArray($daysNumber) {
        $days = array(false, false, false, false, false, false, false);
        for($i = 6; $i >=0; $i --) {
            if($daysNumber >= pow(2, $i)) {
                $days[$i] = true;
                $daysNumber -= pow(2, $i);
            }
        }
        return $days;
    }
}