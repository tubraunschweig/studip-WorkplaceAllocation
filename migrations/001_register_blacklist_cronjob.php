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

class RegisterBlacklistCronjob extends Migration {

    private $FILENAME;

    public function __construct()
    {
        $this->FILENAME = str_replace('/migrations', '', __DIR__) . '/cronjobs/blacklist_cronjob.php';

        $this->FILENAME = substr($this->FILENAME, strpos($this->FILENAME, 'public/'));
    }

    public function up(){
        $task_id = CronjobScheduler::registerTask($this->FILENAME, true);

        CronjobScheduler::schedulePeriodic($task_id, 20, 0);
    }

    public function down()
    {
        $task_id = CronjobTask::findOneByFilename($this->FILENAME)->task_id;
        CronjobScheduler::unregisterTask($task_id);
    }
}