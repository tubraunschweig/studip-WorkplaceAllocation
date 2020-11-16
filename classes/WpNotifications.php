<?php

/**
 * WpNotifications class
 * class to manage plugin notifications
 * backed by config file conf/default_message_texts.php
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 02.11.16
 * Time: 14:45
 */
class WpNotifications
{
    private $notificationId;
    private $userIds = array();
    private $url;
    private $avatar;
    private $contextId;

    /**
     * WpNotifications constructor.
     *
     * @param string $notificationId
     * @param string $userId
     * @param string $url
     */
    public function __construct($notificationId, $userId, $url)
    {
        $this->notificationId = $notificationId;
        $this->userIds[] = get_username($userId);
        $this->url = $url;

        $this->avatar = Assets::url('images/icons/blue/computer.svg');
    }

    /**
     * Add additional User to notification recipients
     *
     * @param string $userId id of recipient
     */
    public function addUserId($userId)
    {
        $this->userIds[] = get_username($userId);
    }

    public function setContext($contextId) 
    {
        $this->contextId = $contextId;
    }

    /**
     *  Send notification
     */
    public function sendNotification()
    {
        $html_id = md5(join('',$this->userIds).time().'workplace'.$this->notificationId);
        PersonalNotifications::add($this->userIds, $this->url, $this->getMessage(), $html_id, $this->avatar);
        $messages = WpMessages::findBySQL('context_id = ? AND hook_point = ?', array($this->contextId, $this->notificationId));

        if(sizeof($messages) > 0) {

            foreach ($messages as $message){
                /** @var WpMessages $message*/
                if($message->active) {
                    Message::send(null, $this->userIds, $message->subject, $this->parseMessage($message->message));
                }
            }
        }

    }


    /**
     * get message for this notification
     *
     * @param bool $parsed on true returns message parsed and replaced placeholders
     * @return string message
     */
    private function getMessage($parsed = true)
    {
        require_once(__DIR__.'/../conf/default_mesage_texts.php');
        global $defaultMessageTexts;
        $message = $defaultMessageTexts[$this->notificationId]['message'];

        if($parsed) {
            return $this->parseMessage($message);
        } else {
            return $message;
        }

    }

    /**
     * Parse notification message content
     *
     * @param string $message message to parse
     * @return string parsed message
     */
    private function parseMessage($message)
    {
        //replace [context]
        /** @var Institute $institute */
        if(isset($this->contextId)) {
            $institute = Institute::find($this->contextId);
        } else {
            $institute = Institute::findCurrent();
        }

        $message = preg_replace('/\[context\]/', $institute->getFullname(), $message);

        return $message;
    }
}