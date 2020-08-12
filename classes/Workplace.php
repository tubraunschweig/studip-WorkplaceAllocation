<?php
/**
 * Workplace class
 * class to manage workplaces
 * backed by database table wp_workplace
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 11.05.16
 * Time: 14:27
 */
class Workplace
{
    /** @var string $id */
    private $id;
    /** @var string $name */
    private $name;
    /** @var string $description */
    private $description;
    /** @var  string $active*/
    private $active;
    /** @var string $contextId */
    private $contextId;
    /** @var  null|Rule */
    private $rule;

    /**
     * Workplace constructor.
     *
     * @param string $id workplace id
     * @param string $name workplace name
     * @param string $description workplace description
     * @param string $active is workplace active
     * @param string $contextId context workplace is assigned to
     * @param null|Rule $rule rule for this workplace
     */
    private function __construct($id, $name, $description, $active, $contextId, $rule = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->active = $active;
        $this->contextId = $contextId;
        
        if($rule != null)
        {
            $this->rule = Rule::getRule($rule);
        }
        else
        {
            $this->rule = null;
        }
    }

    /**
     * get workplace id
     *
     * @return string workplace id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get workplace name
     *
     * @return string workplace name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * get workplace description
     *
     * @return string workplace description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * check if workplace is active
     *
     * @return boolean true if workplace is active
     */
    public function isActive()
    {
        return $this->active == 'on';
    }

    /**
     * get assigned rule
     *
     * @return null|Rule null if rule is not set
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * get context id
     *
     * @return string context id
     */
    public function getContextId()
    {
        return $this->contextId;
    }
    

    /**
     * set workplace description
     *
     * @param string $description workplace description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        DBManager::get()->execute("UPDATE wp_workplaces SET description = ? WHERE id = ?", array($description, $this->id));
    }

    /**
     * set workplace name
     *
     * @param string $name workplace name
     */
    public function setName($name)
    {
        $this->name = $name;
        DBManager::get()->execute("UPDATE wp_workplaces SET name = ? WHERE id = ?", array($name, $this->id));
    }

    /**
     * activate workplace
     *
     * @return bool workplace activation state
     */
    public function activate()
    {
        if($this->active == 'on')
        {
            return true;
        }
        if($this->rule == null)
        {
            return false;
        }
        $this->active = 'on';
        DBManager::get()->execute("UPDATE wp_workplaces SET active = 'on' WHERE id = ?", array($this->id));
        return true;
    }

    /**
     * deactivate workplace
     *
     * @return bool workplace activation state
     */
    public function deactivate()
    {
        if($this->active == 'off')
        {
            return true;
        }
        $this->active = 'off';
        DBManager::get()->execute("UPDATE wp_workplaces SET active = 'off' WHERE id = ?", array($this->id));
        return true;
    }


    /**
     * create Rule for this workplace
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
     */
    public function createRule($start, $end, $pauseStart, $pauseEnd, $registrationStart, $registrationEnd, $slotDuration, $oneScheduleByDayAndUser = false, $days = array(true, true, true, true, true, true, true))
    {
        if($this->rule != null)
        {
            return;
        }
        $this->rule = Rule::newRule($start, $end, $pauseStart, $pauseEnd, $registrationStart, $registrationEnd, $slotDuration, $oneScheduleByDayAndUser, $days);
        DBManager::get()->execute("UPDATE wp_workplaces SET rule_id = ? WHERE id = ?", array($this->rule->getId(), $this->getId()));
    }

    /**
     * delete this Workplace
     */
    public function deleteWorkplace()
    {
        DBManager::get()->execute("DELETE FROM wp_workplaces WHERE id = ?", array($this->id));
        DBManager::get()->execute("DELETE FROM wp_rules WHERE id = ?", array($this->getRule()->getId()));
        DBManager::get()->execute("DELETE FROM wp_schedule WHERE workplace_id = ?", array($this->id));
    }

    /**
     * get all schedules at a special day on this workplace
     *
     * @param DateTime $time any time on wanted day is possible
     * @return Schedule[] return array of schedules, is empty if no schedule is found
     */
    public function getSchedulesByDay($time) {
        $day = new DateTime($time->format('d.m.Y'));

        $data = DBManager::get()->fetchAll('SELECT id FROM wp_schedule WHERE workplace_id = ? AND start > ? and start < ?', array($this->id, $day->format('U'), $day->format('U') + 86400));

        $schedules = array();
        foreach($data as $d)
        {
            $schedules[] = Schedule::getSchedule($d['id']);
        }

        return $schedules;
    }

    /**
     * refill this workplace schedules from waiting list at given day
     *
     * @param DateTime $day any time on wanted day is given
     */
    public function refillFromWaitingList($day) {
        $realDay = new DateTime($day->format('d.m.Y'));

        if(WaitingList::peek($this, $realDay) == null) {
            return;
        }

        $daySchedules = $this->getSchedulesByDay($day);

        $dayStartTime = clone $realDay;
        $dayStartTime->add($this->getRule()->getStart());
        $dayEndTime = clone $realDay;
        $dayEndTime->add($this->getRule()->getEnd());
        $pauseStartTime = null;
        $pauseEndTime = null;
        if($this->getRule()->hasPause()){
            $pauseStartTime = clone $day;
            $pauseStartTime->add($this->getRule()->getPauseStart());
            $pauseEndTime = clone $day;
            $pauseEndTime->add($this->getRule()->getPauseEnd());
        }

        for ($time = clone $dayStartTime;
                $time <= $dayEndTime;
                $time->add($this->rule->getSlotDuration()))
        {

            $startTime = clone $time;
            $endTime = clone $time;
            $endTime->add($this->rule->getSlotDuration());

            $foundSchedule = null;
            foreach ($daySchedules as $schedule) {
                if ($schedule->getStart() >= $startTime && $schedule->getStart() < $endTime) {
                    $foundSchedule = $schedule;
                }
            }

            if ($foundSchedule != null) {
                $time = clone $foundSchedule->getStart();
                $time->add($foundSchedule->getDuration())->sub($this->rule->getSlotDuration());
            } else if($this->getRule()->hasPause() && $endTime > $pauseStartTime && $startTime < $pauseEndTime) {
                $time = clone $pauseEndTime;
                $time->sub($this->rule->getSlotDuration());
            } else if ($endTime <= $dayEndTime) {
                $nextFromWaitingList = WaitingList::pop($this, $realDay);
                Schedule::newSchedule($nextFromWaitingList->getUserid(), $this->id, $startTime->getTimestamp(), $this->rule->getSlotDuration()->format('P%yY%mM%dDT%hH%iM%sS'));
            }
        }
    }

    

    /*
     * Static functions
     */
    /**
     * get workplace by id
     *
     * @param string $id id of workplace
     * @return null|Workplace null if id not found
     */
    public static function getWorkplace($id)
    {
        $data = \DBManager::get()->fetchAll("SELECT * FROM wp_workplaces WHERE id = ?", array($id));
        if(sizeof($data) < 1){
            return null;
        }

        $data = $data[0];

        return new Workplace($data['id'], $data['name'], $data['description'], $data['active'], $data['context_id'], $data['rule_id']);
    }

    /**
     * create new workplace
     *
     * @param string $name name of workplace
     * @param string $description description of workplace
     * @param string $contextId context workplace will be assigned to
     * @return null|Workplace null on error
     */
    public static function newWorkplace($name, $description, $contextId)
    {
        $id = sha1(rand());
        \DBManager::get()->execute(
            "INSERT INTO wp_workplaces (id, name, description, context_id) VALUES (?, ?, ?, ?)",
            array($id, $name, $description, $contextId));
        
        return Workplace::getWorkplace($id);
    }

    /**
     * get all workplaces on given context
     *
     * @param string $contextId context id
     * @return Workplace[] array of workplaces. empty if no workplace found
     */
    public static function getWorkplacesByContext($contextId)
    {
        $data = DBManager::get()->fetchAll("SELECT id FROM wp_workplaces WHERE context_id = ?", array($contextId));

        $workplaceList = array();

        foreach($data as $item)
        {
            $workplaceList[] = self::getWorkplace($item['id']);
        }

        function cmp($a, $b) {
            /** @var Workplace $a */
            /** @var workplace $b */
            return strcmp($a->getName(), $b->getName());
        }

        usort($workplaceList, 'cmp');

        return $workplaceList;
    }
}