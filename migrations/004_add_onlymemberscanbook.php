<?php
/**
 * 004_add_onlyMembersCanBook
 *
 * User: christmu
 * Date: 22.09.20
 * Time: 16:30
 */

class AddOnlyMembersCanBook extends Migration
{
    function up() {
        $db = DBManager::get();
        $db->exec("ALTER TABLE wp_rules ADD COLUMN only_members_can_book TINYINT(1);");
    }

    function down() {
        $db = DBManager::get();
        $db->exec("ALTER TABLE wp_rules DROP COLUMN only_members_can_book;"); 
    }
}