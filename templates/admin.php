<?php
/**
 * admin template
 * shows a list of all workplaces with administrative options
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 11.05.16
 * Time: 11:00
 *
 * @var Workplace[] $workplaces array of workplaces
 */

?>

<form action="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'saveActivation') ?>" method="post" id="action_form">
<?= CSRFProtection::tokenTag() ?>
</form>
<table class="default">
    <caption>
        Arbeitspl&auml;tze
    </caption>
    <colgroup>
        <col width="20">
        <col>
        <col width="20">
        <col width="100">
    </colgroup>
    <thead>
        <tr class="sortable">
            <th>Aktiv</th>
            <th>Name</th>
            <th></th>
            <th class="actions" colspan="4">Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php

    if(sizeof($workplaces) == 0) {
        ?>
        <tr>
            <td colspan="3" style="text-align: center;">Es wurden noch keine Arbeitsplätze eingerichtet.</td>
        </tr>
        <?php
    }

    foreach ($workplaces as $workplace) {
        ?>
        <tr>
            <td style="text-align: center;">
                <input type="checkbox" name="<?= $workplace->getId() ?>" class="itemboxes" form="action_form" <? if($workplace->isActive()) {print("checked");} ?> title="Arbeitsplatz aktivieren/deaktivieren">
            </td>
            <td><a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId(), 'week' => 1), 'addSchedule') ?>"><?= $workplace->getName() ?></a></td>
            <td>
                <?= /** @noinspection PhpParamsInspection */ !empty($workplace->getDescription()) ? tooltipIcon($workplace->getDescription()) : "" ?>
            </td>
            <td class="actions" width="20">
                <a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId()), 'pdf') ?>" title="<?=_('Heutigen Tag drucken')?>" target="_blank">
                    <?= (new Icon('print'))->asImg() ?>
                </a>
            </td>
            <td class="actions" width="20">
                <a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId()), 'addSchedule') ?>" title="<?=_('Termine verwalten')?>">
                    <?= (new Icon('schedule'))->asImg() ?>
                </a>
            </td>
            <td class="actions" width="20">
                <a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId()), 'editWorkplace') ?>" title="<?=_('Arpeitsplatz bearbeiten')?>">
                    <?= ($workplace->getRule() == null) ? (new Icon('edit+new'))->asImg() : (new Icon('edit'))->asImg() ?>
                </a>
            </td>
            <td class="actions" width="20">
                <form action="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'delWorkplace') ?>" method="post" >
                    <input type="hidden" name="wp_id" value="<?= $workplace->getId() ?>">
                    <?= CSRFProtection::tokenTag() ?>
                    <input type="image" src="<?= (Icon::create('trash', 'clickable'))->asImagePath() ?>" title="Arpeitsplatz löschen" alt="Submit">
                </form>
            </td>
        </tr>
        <?php
    }

    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align: center">
                <?= \Studip\Button::create('Speichern', null, array('type' => 'submit', 'form' => 'action_form', 'class' => 'accept')) ?>
            </td>
        </tr>
    </tfoot>
</table>
