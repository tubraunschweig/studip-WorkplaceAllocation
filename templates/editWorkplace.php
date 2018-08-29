<?php
/**
 * editWorkplace template
 * shows form to edit workplace and the assigned rule
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 17.05.16
 * Time: 12:35
 *
 * @var Workplace $workplace workplace to edit
 * @var String[] $messageBoxes message boxes to display
 */

if (isset($messageBoxes) && sizeof($messageBoxes) > 0) {
    foreach ($messageBoxes as $messageBox) {
        print($messageBox);
    }
}

$rule = $workplace->getRule();
/** @var \Studip\Button $submitButton */
$submitButton = \Studip\Button::create(_("Speichern"), null, array("type" => "submit"));

?>
<h1>Arbeitsplatz bearbeiten</h1>
<form method="post" class="default">
    <section>
        <label for="wp_name"><?=_("Name")?></label>
        <input id="wp_name" name="wp_name" type="text" class="size-m" value="<?= $workplace->getName(); ?>">
    </section>
    <section>
        <label for="wp_description"><?=_("Beschreibung")?></label>
        <textarea id="wp_description" name="wp_description" cols="75" rows="4"><?= $workplace->getDescription(); ?></textarea>
    </section>
    <h2>Anmelderegeln:</h2>
    <section>
    <label>Regelmäßige Wochentage:</label>
        <table>
            <tr>
                <td>Mo</td>
                <td>Di</td>
                <td>Mi</td>
                <td>Do</td>
                <td>Fr</td>
                <td>Sa</td>
                <td>So</td>
            </tr>
            <tr>
                <td style="text-align: center">
                    <input title="Montag" type="checkbox" name="day[]" value="0" id="wp_rule_days_mon" <?if($rule != null && $rule->getDay(0)){print("checked");}?>>
                </td>
                <td style="text-align: center">
                    <input title="Dienstag" type="checkbox" name="day[]" value="1" id="wp_rule_days_tue" <?if($rule != null && $rule->getDay(1)){print("checked");}?>>
                </td>
                <td style="text-align: center">
                    <input title="Mittwoch" type="checkbox" name="day[]" value="2" id="wp_rule_days_wed" <?if($rule != null && $rule->getDay(2)){print("checked");}?>>
                </td>
                <td style="text-align: center">
                    <input title="Donnerstag" type="checkbox" name="day[]" value="3" id="wp_rule_days_thu" <?if($rule != null && $rule->getDay(3)){print("checked");}?>>
                </td>
                <td style="text-align: center">
                    <input title="Freitag" type="checkbox" name="day[]" value="4" id="wp_rule_days_fri" <?if($rule != null && $rule->getDay(4)){print("checked");}?>>
                </td>
                <td style="text-align: center">
                    <input title="Samstag" type="checkbox" name="day[]" value="5" id="wp_rule_days_sat" <?if($rule != null && $rule->getDay(5)){print("checked");}?>>
                </td>
                <td style="text-align: center">
                    <input title="Sonntag" type="checkbox" name="day[]" value="6" id="wp_rule_days_sun" <?if($rule != null && $rule->getDay(6)){print("checked");}?>>
                </td>
            </tr>
        </table>
    </section>
    <section>
        <label>Tägliche Öffnungzeit:</label>
    </section>
    von
    <input type="text" name="daily_start_hour" title="Beginn Stunde" style="width: 2em;" value="<?= ($rule != null) ? $rule->getStart()->format('%H') : '00' ?>">
    :<input type="text" name="daily_start_minute" title="Beginn Minute" style="width: 2em;" value="<?= ($rule != null) ? $rule->getStart()->format('%I') : '00' ?>">
    bis
    <input type="text" name="daily_end_hour" title="Ende Stunde" style="width: 2em;" value="<?= ($rule != null) ? $rule->getEnd()->format('%H') : '00' ?>">
    :<input type="text" name="daily_end_minute" title="Ende Minute" style="width: 2em;" value="<?= ($rule != null) ? $rule->getEnd()->format('%I') : '00' ?>">
    <section>
        <label>
            Pause:
            <input type="checkbox" name="daily_pause_exist" id="daily_pause_exist" <?= ($rule != null && $rule->hasPause()) ? 'checked' : ''?> title="Tägliche Pause aktivieren/deaktivieren"> Aktiv<br>
        </label>
        von
        <input type="text" class="daily_pause" name="daily_pause_start_hour" title="Beginn Stunde" style="width: 2em;" value="<?= ($rule != null && $rule->getPauseStart() != null) ? $rule->getPauseStart()->format('%H') : '00' ?>" <?= ($rule != null && $rule->hasPause()) ? '' : 'disabled' ?>>
        :<input type="text" class="daily_pause" name="daily_pause_start_minute" title="Beginn Minute" style="width: 2em;" value="<?= ($rule != null && $rule->getPauseStart() != null) ? $rule->getPauseStart()->format('%I') : '00' ?>" <?= ($rule != null && $rule->hasPause()) ? '' : 'disabled' ?>>
        bis
        <input type="text" class="daily_pause" name="daily_pause_end_hour" title="Ende Stunde" style="width: 2em;" value="<?= ($rule != null && $rule->getPauseEnd() != null) ? $rule->getPauseEnd()->format('%H') : '00' ?>" <?= ($rule != null && $rule->hasPause()) ? '' : 'disabled' ?>>
        :<input type="text" class="daily_pause" name="daily_pause_end_minute" title="Ende Minute" style="width: 2em;" value="<?= ($rule != null && $rule->getPauseEnd() != null) ? $rule->getPauseEnd()->format('%I') : '00' ?>" <?= ($rule != null && $rule->hasPause()) ? '' : 'disabled' ?>>
    </section>
    <script type="application/javascript">
        $('#daily_pause_exist').change(function () {
            if (this.checked) {
                $('.daily_pause').prop('disabled', false);
            } else {
                $('.daily_pause').prop('disabled', true);
            }
        });
    </script>
    <section>
        <label for="wp_rule_slot_duration">Slot Länge</label>
        <select name="slot_duration" id="wp_rule_slot_duration">
            <option value="PT0H30M" <? if($rule != null && $rule->getSlotDuration()->format('PT%hH%iM') == 'PT0H30M'){print("selected");} ?>>&frac12; Stunde</option>
            <option value="PT1H0M" <? if($rule != null && $rule->getSlotDuration()->format('PT%hH%iM') == 'PT1H0M'){print("selected");} ?>>1 Stunde</option>
            <option value="PT1H30M" <? if($rule != null && $rule->getSlotDuration()->format('PT%hH%iM') == 'PT1H30M'){print("selected");} ?>>1&frac12; Stunden</option>
            <option value="PT2H0M" <? if($rule != null && $rule->getSlotDuration()->format('PT%hH%iM') == 'PT2H0M'){print("selected");} ?>>2 Stunden</option>
        </select>
    </section>
    <section>
        <label>Anmeldezeitraum</label>
        Im Voraus buchbar für x<br>
        <select title="Anmeldezeitraum Start" name="registration_start">
            <option value="P7D" <? if($rule != null && $rule->getRegistrationStart()->format('P%dD') == 'P7D'){print("selected");} ?>>1 Woche</option>
            <option value="P14D" <? if($rule != null && $rule->getRegistrationStart()->format('P%dD') == 'P14D'){print("selected");} ?>>2 Wochen</option>
            <option value="P28D" <? if($rule != null && $rule->getRegistrationStart()->format('P%dD') == 'P28D'){print("selected");} ?>>4 Wochen</option>
        </select>
        <br>
        Buchbar bis x vor Termin<br>
        <select title="Anmeldezeitraum Ende" name="registration_end">
            <option value="P0D" <? if($rule != null && $rule->getRegistrationEnd()->format('P%dD') == 'P0D'){print("selected");} ?>>unmitelbar</option>
            <option value="P1D" <? if($rule != null && $rule->getRegistrationEnd()->format('P%dD') == 'P1D'){print("selected");} ?>>1 Tag</option>
            <option value="P7D" <? if($rule != null && $rule->getRegistrationEnd()->format('P%dD') == 'P7D'){print("selected");} ?>>1 Woche</option>
            <option value="P14D" <? if($rule != null && $rule->getRegistrationEnd()->format('P%dD') == 'P14D'){print("selected");} ?>>2 Wochen</option>
        </select>
    </section>
    <section>
        <label>Weitere Optionen</label>
        <input type="checkbox" name="one_schedule_by_day_and_user" id="one_schedule_by_day_and_user" <?= ($rule != null && $rule->isOneScheduleByDayAndUser()) ? 'checked' : '' ?> title="Option aktivieren/deaktivieren: Nur ein termin pro Tag und Nutzer"> Für jeden Nutzer nur einen Termin am Tag zulassen<br>
    </section>
    <?= $submitButton ?>
</form>
