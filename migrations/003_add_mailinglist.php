<?php
/**
 * 003_add_mailinglist
 *
 * User: christmu
 * Date: 10.09.20
 * Time: 12:00
 */

class AddMailinglist extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        $db->exec("CREATE TABLE IF NOT EXISTS wp_notified_user_list(id VARCHAR(255) PRIMARY KEY NOT NULL, user_id VARCHAR(255), context_id VARCHAR(255))ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;");
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE IF EXISTS wp_notified_user_list;");
    }
}
