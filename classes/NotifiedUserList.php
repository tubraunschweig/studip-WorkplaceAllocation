<?php

/**
 * NotifiedUserList class
 * class to manage the list of to be notified users
 * backed by database table wp_notified_user_list
 *
 * Created by Visual Studio Code.
 * User: christmu
 * Date: 01.09.20
 * Time: 17:30
 */
class NotifiedUserList implements IteratorAggregate
{
    /** @var string */
    private $context_id;

    /**
     * Get NotifiedUserList of a specific context
     *
     * @param null|string $context_id if null then use actual context
     * @return NotifiedUserList
     */
    static public function getNotifiedUserList($context_id = null) 
    {
        if($context_id == null) {
            $context_id = $_GET['cid'];
        }

        $return = new NotifiedUserList();
        $return->context_id = $context_id;

        return $return;
    }

    /**
     * get the size of the NotifiedUserList
     *
     * @return int size of list
     */
    public function list_size() 
    {
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_notified_user_list WHERE context_id = ?", array($this->context_id));

        return sizeof($data);
    }

    /**
     * Check if user is on NotifiedUserList
     *
     * @param string $user_id
     * @return bool
     */
    public function isOnList($user_id) 
    {
        $data = DBManager::get()->fetchAll("SELECT * FROM wp_notified_user_list WHERE user_id = ? AND context_id = ?", array($user_id, $this->context_id));

        return sizeof($data) != 0;
    }

    /**
     * Add user to NotifiedUserList
     *
     * @param string $user_id
     */
    public function addToList($user_id) 
    {
        if(!$this->isOnList($user_id)) {
            $id = sha1(rand());
            DBManager::get()->execute("INSERT INTO wp_notified_user_list (id, user_id, context_id) VALUES (?, ?, ?)", array($id, $user_id, $this->context_id));
        }

    }

    /**
     * Delete user from NotifiedUserList
     *
     * @param string $user_id
     */
    public function deleteFromList($user_id) 
    {
        if($this->isOnList($user_id)) {
            DBManager::get()->execute("DELETE FROM wp_notified_user_list WHERE user_id = ? AND context_id = ?", array($user_id, $this->context_id));
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
        $data = DBManager::get()->fetchAll("SELECT user_id FROM wp_notified_user_list WHERE context_id = ?", array($this->context_id)); 
        $usersInList = array();
        
        foreach($data as $uid){
            $usersInList[] = USER::findFull($uid['user_id']);
        }
        
        return new ArrayIterator($usersInList);
    }
}