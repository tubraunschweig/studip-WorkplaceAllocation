<?php
/**
 * default_mesage_texts config
 * this config file stores the default notification messages for this plugin every new notification message is an array
 * with the following fields
 *  - meta : name of the notification point
 *  - message : notification message
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 02.11.16
 * Time: 10:39
 */

global $defaultMessageTexts;
$defaultMessageTexts = array();


$defaultMessageTexts['insert_blacklist']['meta'] = "Hinzuf&#252;gen zur Sperrliste";
$defaultMessageTexts['insert_blacklist']['message'] = "Sie wurden in der Einrichtung/Veranstaltung \"[context]\" f?r die Anmeldung an den Arbeitspl?tzen gesperrt";

$defaultMessageTexts['delete_blacklist']['meta'] = "Entfernen von der Sperrliste";
$defaultMessageTexts['delete_blacklist']['message'] = "Sie wurden in der Einrichtung/Veranstaltung \"[context]\" von der Sperrliste entfernt.";

$defaultMessageTexts['create_schedule']['meta'] = "Anlegen eines Termins";
$defaultMessageTexts['create_schedule']['message'] = "Es wurde ein Arbeitsplatztermin in der Einrichtung/Veranstaltung \"[context]\" für Sie reserviert.";

$defaultMessageTexts['delete_schedule']['meta'] = "L&#246;schen eines Termins";
$defaultMessageTexts['delete_schedule']['message'] = "Ihr Arbeitsplatztermin in der Einrichtung/Veranstaltung \"[context]\" wurde gelöscht";

$defaultMessageTexts['change_schedule']['meta'] = "&#196;ndern eines Termins";
$defaultMessageTexts['change_schedule']['message'] = "Ihr Arbeitsplatztermin in der Einrichtung/Veranstaltung \"[context]\" wurde geändert";

