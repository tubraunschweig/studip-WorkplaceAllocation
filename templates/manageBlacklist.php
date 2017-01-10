<?php
/**
 * manageBlacklist template
 * shows table with all blacklist entries and administrative options
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 27.10.16
 * Time: 10:21
 *
 * @var Blacklist $blacklist blacklist to manage
 */

?>

<table class="default">
    <caption>
        Sperrliste
    </caption>
    <colgroup>
        <col>
        <col>
        <col>
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>Nachname, Vorname</th>
            <th>Nutzername</th>
            <th>Ablauf</th>
            <th style="text-align: right;">Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach($blacklist as $entry){
        /** @var StudIPUser $user */
        $user = $entry['user'];
        /** @var DateTime $expiration */
        $expiration = $entry['expiration']
        ?>
        <tr>
            <td><?= $user->getSurname() ?>, <?= $user->getGivenname() ?></td>
            <td><?= $user->getUsername() ?></td>
            <td><?= $expiration != null ? $expiration->format('d.m.Y') : _('Unbegrenzt') ?></td>
            <td style="text-align: right;">
                <form action="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'manageBlacklist') ?>" method="post">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="<?= $user->getUserid() ?>">
                    <button type="submit" class="link_button"><?= Assets::img('icons/20/blue/trash') ?></button>
                </form>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">
                <form action="<?= PluginEngine::getLink("WorkplaceAllocation", array(), "manageBlacklist") ?>" method="post">
                    <input type="hidden" name="action" value="add">
                    <?php
                    $search = new SQLSearch("SELECT user_id, CONCAT(Vorname, ' ', Nachname, ' (', username, ')') FROM auth_user_md5 WHERE Nachname LIKE :input OR username LIKE :input OR Vorname LIKE :input", _('Benutzer'), 'username');
                    $quickSearch = QuickSearch::get('user_id', $search);
                    $quickSearch->setInputClass('size-m');
                    print($quickSearch->render());
                    ?><br>
                    <label for="bl_expiration">Ablauf der Sperrung in Tagen<small>(Leer für unbegrenzt)</small></label>
                    <input type="number" name="expiration" id="bl_expiration" class="size-s"><br>
                    <?= Studip\Button::create("Hinzufügen") ?>
                </form>
            </td>
        </tr>
    </tfoot>
</table>
