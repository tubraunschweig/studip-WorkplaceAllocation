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

class CronJobBlacklist extends Migration {

    const FILENAME = __DIR__.'/cronjobs/blacklist_cronjob.php';

    public function up(){
        $task_id = CronjobScheduler::registerTask(self::FILENAME, true);

        CronjobScheduler::schedulePeriodic($task_id, 20, 0);
    }

    public function down()
    {
        $task_id = CronjobTask::findOneByFilename(self::FILENAME)->task_id;
        CronjobScheduler::unregisteredTask($task_id);
    }
}