<?php
/**
 * addWorkplace template
 * shows form to collect information to add a workplace
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 11.05.16
 * Time: 16:42
 *
 * @var bool $error if an error is occurred on the form entries
 * @var string $errorDetails details of the occurred error
 */

if($error) {
    print(MessageBox::error(_('Bitte beheben Sie erst folgende Fehler, bevor Sie fortfahren:'), $errorDetails));
}


/** @var \Studip\Button $submitButton */
$submitButton = \Studip\Button::create(_('Hinzufügen'), null, array('type' => 'submit'));

?>
<h1><?= _('Arbeitsplatz hinzufügen')?></h1>

<form action="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'addWorkplace') ?>" method="post" class="default">
    <section>
        <label for="wp_name">
            <span class="required"><?=_("Name")?></span>
            <input id="wp_name" name="wp_name" type="text" class="size-m" value="<?= Request::get('wp_name') ?>">
        </label>
    </section>
    <section>
        <label for="wp_description">
            <?=_('Beschreibung')?>
            <textarea id="wp_description" name="wp_description" cols="75" rows="4"><?= Request::get('wp_description') ?></textarea>
        </label>
    </section>
    <?= CSRFProtection::tokenTag() ?>
    <?= $submitButton ?>
</form>