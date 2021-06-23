<?php
/**
 * 001_register_blacklist_cronjob migration
 *
 * this migration is used to register blacklist_cronjob cronjob
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 02.11.16
 * Time: 11:01
 */

require_once __DIR__.'/../cronjobs/blacklist_cronjob.php';

class RegisterBlacklistCronjob extends Migration
{
    private $FILENAME;

    public function __construct($verbose = false)
    {
        parent::__construct($verbose);
        $this->FILENAME = str_replace('/migrations', '', __DIR__) . '/cronjobs/blacklist_cronjob.php';

        $this->FILENAME = substr($this->FILENAME, strpos($this->FILENAME, 'public/'));
    }

    public function up()
    {
        BlacklistCronJob::register()->schedulePeriodic(20, 0);
    }

    public function down()
    {
        BlacklistCronJob::unregister();
    }
}
