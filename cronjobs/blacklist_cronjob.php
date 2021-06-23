<?php
/**
 * blacklist_cronjob cronjob
 * cronjob that automatically remove users from blacklist if configured
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 02.11.16
 * Time: 11:34
 */

class BlacklistCronJob extends CronJob
{

    /**
     * Return the name of the cronjob.
     */
    public static function getName()
    {
        return _('Arbeitsplatzvergabe Sperrliste');
    }

    /**
     * Return the description of the cronjob.
     */
    public static function getDescription()
    {
        return _('Cronjob, der die abgelaufenen Sperrungen auf der Sperrliste der Arbeitsplatzvergabe entfernt.');
    }

    /**
     * Execute the cronjob.
     *
     * @param mixed $last_result What the last execution of this cronjob
     *                           returned.
     * @param array $parameters  Parameters for this cronjob instance which
     *                           were defined during scheduling.
     */
    public function execute($last_result, $parameters = [])
    {
        $now = new DateTime();
        DBManager::get()->execute("DELETE FROM wp_blacklist WHERE expiration < ?", [$now->getTimestamp()]);
    }
}
