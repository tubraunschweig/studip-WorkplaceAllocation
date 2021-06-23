<?php

/**
 * WpMessages class
 * class to manage custom message texts
 * backed by database table wp_messages
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 10.11.16
 * Time: 10:42
 *
 * @property string id message id
 * @property string context_id
 * @property string hook_point
 * @property string subject
 * @property string message
 * @property boolean active
 */
class WpMessages extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'wp_messages';
        parent::configure($config);
    }
}
