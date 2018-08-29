<?php

/**
 * Schedule class
 * class to manage schedules
 * backed by database table wp_schedule
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 22.08.16
 * Time: 14:26
 */
class Schedule
{

    /** @var  String */
    private $id;
    /** @var  User */
    private $owner;
    /** @var  Workplace */
    private $workplace;
    /** @var  DateTime */
    private $start;
    /** @var  DateInterval */
    private $duration;
    /** @var  String */
    private $comment;
    /** @var  bool */
    private $blocked;

    /**
     * Schedule constructor.
     *
     * @param String $id schedule id
     * @param String $userID owner user id
     * @param String $workplaceID assigned workplace's id
     * @param int $start start time as unixtime
     * @param String $duration duration as DateInterval construction string
     * @param String $comment comment
     * @param bool $blocked true if schedule is bocked by an admin
     */
    private function __construct($id, $userID, $workplaceID, $start, $duration, $comment = "", $blocked = false) {

        $this->id = $id;
        $this->owner = User::findFull($userID);
        $this->workplace = Workplace::getWorkplace($workplaceID);
        $this->start = new DateTime('@'.$start);
        $this->start->setTimezone(new DateTimeZone('Europe/Berlin'));
        $this->duration = new DateInterval($duration);
        $this->comment = $comment;
        $this->blocked = $blocked;
    }

    /**
     * Get schedule id
     *
     * @return String schedule id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get schedule owner
     *
     * @return User schedule owner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * get assigned workplace
     *
     * @return Workplace assigned workplace
     */
    public function getWorkplace()
    {
        return $this->workplace;
    }

    /**
     * get schedule start
     *
     * @return DateTime schedule start
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * get schedule duration
     *
     * @return DateInterval schedule duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * get comment
     *
     * @return String comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * check if this is a blocked schedule
     *
     * @return boolean true if blocked
     */
    public function isBlocked()
    {
        return $this->blocked;
    }

    /**
     * set schedule owner
     *
     * @param User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        DBManager::get()->execute("UPDATE wp_schedule SET user_id = ? WHERE id = ?", array($this->owner->user_id, $this->id));
    }

    /**
     * set schedule start
     *
     * @param DateTime $start
     * @param bool $recursive if true the directly following schedules will be moved to
     * @return bool false if an error occurred
     */
    public function setStart($start, $recursive = false)
    {

        $oldEndTime = clone $this->start;
        $oldEndTime->add($this->duration);

        $data = DBManager::get()->fetchAll("SELECT id FROM wp_schedule WHERE workplace_id = ? AND start < ? ORDER BY start DESC LIMIT 1",
            array($this->workplace->getId(), $this->start->getTimestamp()));
        if(sizeof($data) > 0) {
            $lastSchedule = Schedule::getSchedule($data[0]['id']);
            $lastScheduleEndTime = clone $lastSchedule->getStart();
            $lastScheduleEndTime->add($lastSchedule->getDuration());
        } else {
            $lastScheduleEndTime = null;
        }

        if($lastScheduleEndTime != null && $lastScheduleEndTime > $this->start){
            return false;
        }
        $this->start = $start;
        DBManager::get()->execute("UPDATE wp_schedule SET start = ? WHERE id = ?", array($this->start->format("U"), $this->id));

        $notification = new WpNotifications(
            'change_schedule',
            $this->owner->user_id,
            PluginEngine::getURL(
                'WorkplaceAllocation',
                array(
                    'cid' => $this->workplace->getContextId(),
                    'wp_id' => $this->workplace->getId(),
                    's_id' => $this->id
                ),
                'editSchedule'
            )
        );
        $notification->setContext($this->workplace->getContextId());
        $notification->sendNotification();

        if($recursive) {
            $data = DBManager::get()->fetchAll("SELECT id FROM wp_schedule WHERE workplace_id = ? AND start = ?",
                array($this->workplace->getId(), $oldEndTime->getTimestamp()));
            if (sizeof($data) > 0) {
                $newEndTime = clone $this->start;
                $newEndTime->add($this->duration);

                $nextSchedule = Schedule::getSchedule($data[0]['id']);
                $nextSchedule->setStart($newEndTime, true);
            }
        }

        return true;
    }

    /**
     * set schedule duration
     * upward change wil only work if time after schedule is free
     *
     * @param DateInterval $duration
     * @return bool false if error occurred
     */
    public function setDuration($duration)
    {

        $data = DBManager::get()->fetchAll("SELECT id FROM wp_schedule WHERE workplace_id = ? AND start > ? ORDER BY start ASC LIMIT 1",
            array($this->workplace->getId(), $this->start->getTimestamp()));
        if(sizeof($data) > 0) {
            $nextSchedule = Schedule::getSchedule($data[0]['id']);
        } else {
            $nextSchedule = null;
        }

        $newEnd = clone $this->getStart();
        $newEnd->add($duration);
        if(($newEnd->format('Hi') > $this->getWorkplace()->getRule()->getEnd()->format('%h%I')) OR ($nextSchedule != null AND $newEnd > $nextSchedule->getStart())) {
            return false;
        }

        $this->duration = $duration;
        DBManager::get()->execute("UPDATE wp_schedule SET duration = ? WHERE id = ?", array($this->duration->format('P%yY%mM%dDT%hH%iM%sS'), $this->id));

        $notification = new WpNotifications(
            'change_schedule',
            $this->owner->user_id,
            PluginEngine::getURL(
                'WorkplaceAllocation',
                array(
                    'cid' => $this->workplace->getContextId(),
                    'wp_id' => $this->workplace->getId(),
                    's_id' => $this->id
                ),
                'editSchedule'
            )
        );
        $notification->setContext($this->workplace->getContextId());
        $notification->sendNotification();

        return true;
    }

    /**
     * set comment
     *
     * @param String $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        DBManager::get()->execute("UPDATE wp_schedule SET comment = ? WHERE id = ?", array($this->comment, $this->id));
    }

    /**
     * set schedule as blocked or not
     *
     * @param boolean $blocked
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        if($this->blocked) {
            $type = 'blocked';
        } else {
            $type = 'normal';
        }

        DBManager::get()->execute("UPDATE wp_schedule SET type = ? WHERE id = ?", array($type, $this->id));
    }


    /**
     * delete this schedule
     */
    public function deleteSchedule()
    {
        DBManager::get()->execute("DELETE FROM wp_schedule WHERE id = ?", array($this->id));
        $nextFromWaitingList = WaitingList::pop($this->workplace, $this->start);
        var_dump($nextFromWaitingList);
        if($nextFromWaitingList != null) {
            Schedule::newSchedule($nextFromWaitingList->getUserid(), $this->workplace->getId(), $this->start->getTimestamp(), $this->duration->format('P%yY%mM%dDT%hH%iM%sS'));
        }

        $notification = new WpNotifications(
            'delete_schedule',
            $this->owner->user_id,
            PluginEngine::getURL(
                'WorkplaceAllocation',
                array(
                    'cid' => $this->workplace->getContextId(),
                    'wp_id' => $this->workplace->getId(),
                    'day' => $this->start->format('d.m.Y'),
                ),
                'timetable'
            )
        );
        $notification->setContext($this->workplace->getContextId());
        $notification->sendNotification();
    }

    /*
     * Static Functions
     */

    /**
     * get schedule by id
     *
     * @param string $id schedule id
     * @return null|Schedule null if id not exist
     */
    static public function getSchedule($id) {
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_schedule WHERE id = ?", array($id));

        if(sizeof($data) < 1) {
            return null;
        }

        $data = $data[0];

        return new Schedule(
            $id,
            $data['user_id'],
            $data['workplace_id'],
            $data['start'],
            $data['duration'],
            $data['comment'],
            $data['type'] == 'blocked'
        );
    }

    /**
     * create new schedule
     *
     * @param string $userID owner id
     * @param string $workplaceID assigned workplace
     * @param int $start start as unixtime
     * @param string $duration duration as DateInterval constructor string
     * @param string $comment comment
     * @param bool $blocked true in schedule is blocked by admin
     * @return null|Schedule null if error occurred
     */
    static public function newSchedule($userID, $workplaceID, $start, $duration, $comment = "", $blocked = false) {
        $id = sha1(rand());

        if($blocked){
            $type = 'blocked';
        } else {
            $type = 'normal';
        }

        DBManager::get()->execute("INSERT INTO wp_schedule (id, user_id, workplace_id, start, duration, comment, type) VALUES (?, ?, ?, ?, ?, ?, ?)",
            array($id, $userID, $workplaceID, $start, $duration, $comment, $type));

        $workplace = Workplace::getWorkplace($workplaceID);
        $notification = new WpNotifications(
            'create_schedule',
            $userID,
            PluginEngine::getURL(
                'WorkplaceAllocation',
                array(
                    'cid' => $workplace->getContextId(),
                    'wp_id' => $workplace->getId(),
                    's_id' => $id
                ),
                'editSchedule'
            )
        );
        $notification->setContext($workplace->getContextId());
        $notification->sendNotification();

        return self::getSchedule($id);
    }

    /**
     *
     * get all schedules of given user
     *
     * @param int $userId
     * @return Schedule[] schedules of given user
     */
    static public function getSchedulesByUser($userId)
    {
        $data = DBManager::get()->fetchAll('SELECT id FROM wp_schedule WHERE user_id = ?', array($userId));

        $returnArray = [];

        foreach ($data as $d) {
            $schedule = self::getSchedule($d['id']);
            $schedule_end = clone $schedule->getStart();
            $schedule_end->add($schedule->getDuration());
            $now = new DateTime();
            if ($schedule_end > $now) {
                $returnArray[] = $schedule;
            }
        }

        return $returnArray;
    }
}