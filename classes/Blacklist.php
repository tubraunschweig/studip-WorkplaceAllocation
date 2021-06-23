<?php

/**
 * Blacklist class
 * class to manage blacklist entries, backed by database table wp_blacklist
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 25.10.16
 * Time: 15:10
 */
class Blacklist implements IteratorAggregate
{
    /** @var string */
    private $context_id;

    /**
     * Get Blacklist of an specific context
     *
     * @param null|string $context_id if null then use actual context
     * @return Blacklist
     */
    public static function getBlacklist($context_id = null)
    {
        if ($context_id == null) {
            $context_id = Request::get('cid');
        }
        $return = new Blacklist();
        $return->context_id = $context_id;
        return $return;
    }

    /**
     * get the size of the Blacklist
     *
     * @return int size of list
     */
    public function list_size()
    {
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_blacklist WHERE context_id = ?", [$this->context_id]);

        return sizeof($data);
    }

    /**
     * Check if user is on blacklist
     *
     * @param string $user_id
     * @return bool
     */
    public function isOnList($user_id)
    {
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_blacklist WHERE context_id = ? AND user_id = ?", [$this->context_id, $user_id]);

        return sizeof($data) != 0;
    }

    /**
     * get DateTime of user blacklist expiration
     *
     * @param string $user_id id of requested user
     * @return DateTime|null datetime of expiration or null if no no expiration set or user not on list
     */
    public function getExpiration($user_id)
    {
        if (!$this->isOnList($user_id)) {
            return null;
        }

        $data = DBManager::get()->fetchFirst("SELECT * FROM wp_blacklist WHERE context_id = ? AND user_id = ?", [$this->context_id, $user_id]);

        if ($data['expiration'] == null) {
            return null;
        } else {
            return new DateTime('@' . $data['expiration']);
        }
    }

    /**
     * Add user to Blacklist
     *
     * @param string $user_id
     * @param int|null $expiration unix Timestamp of automatic deletion
     */
    public function addToList($user_id, $expiration = null)
    {
        if (!$this->isOnList($user_id)) {
            DBManager::get()->execute("INSERT INTO wp_blacklist SET context_id = ?, user_id = ?, expiration = ?", [$this->context_id, $user_id, $expiration]);
            $notification = new WpNotifications('insert_blacklist', $user_id, PluginEngine::getLink('WorkplaceAllocation', [], 'show'));
            $notification->setContext($this->context_id);
            $notification->sendNotification();
        }
    }

    /**
     * Delete user from blacklist
     *
     * @param string $user_id
     */
    public function deleteFromList($user_id)
    {
        if ($this->isOnList($user_id)) {
            DBManager::get()->execute("DELETE FROM wp_blacklist WHERE context_id = ? AND user_id = ?", [$this->context_id, $user_id]);
            $notification = new WpNotifications('delete_blacklist', $user_id, PluginEngine::getLink('WorkplaceAllocation', [], 'show'));
            $notification->setContext($this->context_id);
            $notification->sendNotification();
        }
    }


    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        $data = DBManager::get()->fetchAll("SELECT user_id, expiration FROM wp_blacklist WHERE context_id = ?", [$this->context_id]);
        $usersInList = [];

        foreach ($data as $uid) {
            $entry = [];
            $entry['user'] = User::findFull($uid['user_id']);

            if ($uid['expiration'] != null) {
                $entry['expiration'] = new DateTime('@' . $uid['expiration']);
            } else {
                $entry['expiration'] = null;
            }
            $usersInList[] = $entry;
        }
        return new ArrayIterator($usersInList);
    }
}
