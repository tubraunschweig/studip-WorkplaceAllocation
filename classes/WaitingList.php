<?php

/**
 * WaitingList class
 * class to manage the waiting list
 * backed by database table wp_waiting_list
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 06.10.16
 * Time: 15:05
 */
class WaitingList
{
    /**
     * add actual user to waiting list
     *
     * @param Workplace $workplace target workplace
     * @param DateTime $day any time on target day
     * @return int|null waiting list position of null on error
     */
    public static function push($workplace, $day)
    {
        $realDay = new DateTime($day->format('d.m.Y'));
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_waiting_list WHERE workplace_id = ? AND day = ? AND user_id = ?", [$workplace->getId(), $realDay->getTimestamp(), get_userid()]);

        if (sizeof($data) == 0) {
            DBManager::get()->execute("INSERT INTO wp_waiting_list (workplace_id, user_id, day, insertion_timestamp) VALUES (?, ?, ?, ?)", [$workplace->getId(), get_userid(), $realDay->getTimestamp(), time()]);
        }

        $data = DBManager::get()->fetchAll("SELECT * FROM wp_waiting_list WHERE workplace_id = ? AND day = ? ORDER BY insertion_timestamp ASC", [$workplace->getId(), $realDay->getTimestamp()]);
        $counter = 1;
        foreach ($data as $d) {
            if ($d['user_id'] == get_userid()) {
                return $counter;
            } else {
                $counter++;
            }
        }
        return null;
    }

    /**
     * get first position of waiting list
     *
     * @param Workplace $workplace target workplace
     * @param DateTime $day any time on target day
     * @return User|null first user on list or null on error
     */
    public static function peek($workplace, $day)
    {
        $realDay = new DateTime($day->format('d.m.Y'));
        $data = DBManager::get()->fetchAll("SELECT user_id FROM wp_waiting_list WHERE workplace_id = ? AND day = ? ORDER BY insertion_timestamp ASC", [$workplace->getId(), $realDay->getTimestamp()]);

        if (sizeof($data) > 0) {
            return User::findFull($data[0]['user_id']);
        } else {
            return null;
        }
    }

    /**
     * get first position on waiting list an delete it from list
     *
     * @param Workplace $workplace target workplace
     * @param DateTime $day any time on target day
     * @return null|User first user on list or error
     */
    public static function pop($workplace, $day)
    {
        $realDay = new DateTime($day->format('d.m.Y'));
        $user = self::peek($workplace, $day);

        if ($user != null) {
            DBManager::get()->execute("DELETE FROM wp_waiting_list WHERE workplace_id = ? AND day = ? AND user_id = ?", [$workplace->getId(), $realDay->getTimestamp(), $user->user_id]);
        }
        return $user;
    }
}
