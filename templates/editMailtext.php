<?php
/**
 * editMailtext template
 * shows a form to edit a custom message text
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 22.11.16
 * Time: 13:15
 *
 * @var WpMessages $message message to edit
 */

require_once(__DIR__.'/../conf/default_mesage_texts.php');
global $defaultMessageTexts;
?>

<h1>Benachrichtigungstexte bearbeiten</h1>
<a href="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'manageMail') ?>">zurück</a>
<br>
<br>
<b>Einhängepunkt:</b> <?= $defaultMessageTexts[$_GET['hook_point']]['meta'] ?>

<form action="<?= PluginEngine::getLink('WorkplaceAllocation', array('hook_point' => $_GET['hook_point']), 'editMailtext') ?>" method="post" class="studip-form">
    <section>
        <label for="subject">Betreff:</label>
        <input type="text" name="subject" id="subject" class="size-m" value="<?= $message == null ? '' : $message->subject ?>">
    </section>
    <section>
        <input type="checkbox" name="active" <?= ($message != null && $message->active) ? 'checked' : ''?> title="Nachricht aktivieren/deaktivieren"> Aktiv
    </section>
    <section>
        <label for="text">Nachricht</label>
        <textarea id="text" name="text" cols="75" rows="30"><?= $message != null ? $message->message : '' ?></textarea>
    </section>
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