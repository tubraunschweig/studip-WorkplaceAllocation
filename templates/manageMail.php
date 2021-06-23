<?php
/**
 * manageMail template
 * shows list of all hook points to trigger StudIP messages on and administrative options
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 16.11.16
 * Time: 12:00
 *
 * @var string[] $defaultMessageTexts hook point configuration array
 */

?>


<table class="default">
    <caption>
Benachrichtigungstexte
    </caption>
    <colgroup>
        <col>
        <col>
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>Einhängepunkt</th>
            <th>Aktiv</th>
            <th style="text-align: right;">Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php

    foreach ($defaultMessageTexts as $hookPoint => $defaultMessageText) {
        /** @var WpMessages $studipMessage */
        $studipMessage = $defaultMessageText['studip_message']
        ?>
        <tr>
            <td><?= $defaultMessageText['meta'] ?></td>
            <td><?= $studipMessage !== null && $studipMessage->active ? Assets::img('icons/20/green/accept') . ' (aktiv)' : Assets::img('icons/20/red/decline') . ' (inaktiv)' ?></td>
            <td style="text-align: right;">
                <a href="<?= PluginEngine::getLink('WorkplaceAllocation', ['hook_point' => $hookPoint], 'editMailtext') ?>">
                    <?= Assets::img('icons/20/blue/edit') ?>
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>
