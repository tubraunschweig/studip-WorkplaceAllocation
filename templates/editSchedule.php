<?php
/**
 * editSchedule template
 * shows form to edit a schedule
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 27.09.16
 * Time: 14:03
 *
 * @var Schedule $schedule schedule to edit
 * @var bool $admin is current user admin
 * @var string[] $messageBoxes array of message boxes possibly printed on user response case
 */

foreach ($messageBoxes as $box) {
    print($box);
}


?>

<h1>Termin bearbeiten <?= $schedule->getStart()->format('d.m.Y - H:i') ?> Uhr</h1>
<a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $schedule->getWorkplace()->getId(), 'day' => $schedule->getStart()->format('d.m.Y')), $admin ? 'addSchedule' : 'timetable') ?>">zur&uuml;ck</a>
<form action="<?= PluginEngine::getLink('WorkplaceAllocation', array('s_id' => $schedule->getId()), 'editSchedule') ?>" method="post" class="studip-form">
    <section>
        <label for="s_owner">Besitzer</label>
        <?php
        if($admin):
            $search = new SQLSearch("SELECT user_id, CONCAT(Vorname, ' ', Nachname, ' (', username, ')') FROM auth_user_md5 WHERE Nachname LIKE :input OR username LIKE :input OR Vorname LIKE :input", _('Benutzer'), 'username');
            $quickSearch = QuickSearch::get('s_owner', $search);
            $quickSearch->setInputClass('size-m');
            $quickSearch->defaultValue($schedule->getOwner()->getUserid(), $schedule->getOwner()->getGivenname().' '.$schedule->getOwner()->getSurname().' ('.$schedule->getOwner()->getUsername().')');
            print($quickSearch->render());
        else:
        ?>
            <input type="text" name="owner" id="s_owner" class="size-m" value="<?= $schedule->getOwner()->getGivenname() ?> <?= $schedule->getOwner()->getSurname() ?> (<?= $schedule->getOwner()->getUsername() ?>)" disabled>
        <?php
        endif;
        ?>
    </section>
    <section>
        <label for="s_comment">Kommentar</label>
        <textarea id="s_comment" name="s_comment" cols="75" rows="4" <?= $schedule->getOwner()->getUserid() != get_userid() ? 'disabled' : '' ?>><?= $schedule->getComment() ?></textarea>
    </section>
    <? if($admin): ?>
    <section>
        <label for="s_duration">Dauer</label>
        <select id="s_duration" name="s_duration">
            <option value="PT0H15M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT0H15M" ? 'selected' : '' ?>>&frac14; Stunde</option>
            <option value="PT0H30M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT0H30M" ? 'selected' : '' ?>>&frac12; Stunde</option>
            <option value="PT0H45M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT0H45M" ? 'selected' : '' ?>>&frac34; Stunde</option>
            <option value="PT1H0M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT1H0M" ? 'selected' : '' ?>>1 Stunde</option>
            <option value="PT1H15M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT1H15M" ? 'selected' : '' ?>>1&frac14; Stunden</option>
            <option value="PT1H30M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT1H30M" ? 'selected' : '' ?>>1&frac12; Stunden</option>
            <option value="PT1H45M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT1H45M" ? 'selected' : '' ?>>1&frac34; Stunden</option>
            <option value="PT2H0M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT2H0M" ? 'selected' : '' ?>>2 Stunden</option>
            <option value="PT2H15M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT2H15M" ? 'selected' : '' ?>>2&frac14; Stunden</option>
            <option value="PT2H30M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT2H30M" ? 'selected' : '' ?>>2&frac12; Stunden</option>
            <option value="PT2H45M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT2H45M" ? 'selected' : '' ?>>2&frac34; Stunden</option>
            <option value="PT3H0M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT3H0M" ? 'selected' : '' ?>>3 Stunden</option>
            <option value="PT3H15M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT3H15M" ? 'selected' : '' ?>>3&frac14; Stunden</option>
            <option value="PT3H30M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT3H30M" ? 'selected' : '' ?>>3&frac12; Stunden</option>
            <option value="PT3H45M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT3H45M" ? 'selected' : '' ?>>3&frac34; Stunden</option>
            <option value="PT4H0M" <?= $schedule->getDuration()->format('PT%hH%iM') == "PT4H0M" ? 'selected' : '' ?>>4 Stunden</option>
            <?php
            $duration = $schedule->getDuration();
            if($duration->format('%h%I') > 400):
            ?>
                <option value="<?= $duration->format('P%yY%mM%dDT%hH%iM%sS') ?>" selected><?= $duration->format('%h') ?> Stunden <?= $duration->format('%i') ?> Minuten</option>
            <?php
            endif;
            ?>
        </select>
    </section>
    <? endif;?>
    <?=\Studip\Button::create(
        "Speichern",
        null,
        array(
            "type" => "submit",
            "class" => "accept"
        )
    )
    ?>
</form>