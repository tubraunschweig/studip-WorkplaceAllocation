<?php
/**
 * manageNotifiedUsers template
 * shows and manipulates list of all users that receive StudIP messages when new schedules are created
 *
 * Created by Visual Studio Code.
 * User: christmu
 * Date: 19.08.20
 * Time: 11:00
 *
 * @var NotifiedUserList $userlist array of User objects to notify
 * @var String[] $messageBoxes message boxes to display
 */

if (isset($messageBoxes) && sizeof($messageBoxes) > 0) {
    foreach ($messageBoxes as $messageBox) {
        print($messageBox);
    }
}

?>


<table class="default">
    <caption>
Benachrichtigte Nutzer
    </caption>
    <colgroup>
        <col>
        <col>
        <col>
    </colgroup>
    <thead>
        <tr>
            <th>Name</th>
            <th>Stud.IP Nutzername</th>
            <th style="text-align: right;">Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if($userlist->list_size() > 0) {
        foreach($userlist as $user){
            ?>
            <tr>
                <form action="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'manageNotifiedUsers') ?>" method="post" style="display:inline">
                <?php $id = $user->user_id; ?>
                <td><?= ($user->vorname . ' ' . $user->nachname) ?></td>
                <td><?= $user->username ." " ?></td>
                <td style="text-align: right;">
                    <input type="hidden" name="user_id" value="<?= $id ?>">
                    <input type="hidden" name="action" value="delete">
                    <?= CSRFProtection::tokenTag() ?>
                    <input type="image" src="<?= (Icon::create('trash', 'clickable'))->asImagePath() ?>" title="Eintrag löschen" alt="Submit">
                </td>
                </form>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <th></th>
            <th>Es wurden noch keine zu benachrichtigenden Nutzer hinzugefügt.</th>
            <th></th>
        </tr>
        <?php
    }
    ?>
    <tr>
        <form action="<?= PluginEngine::getLink('WorkplaceAllocation', array(), 'manageNotifiedUsers') ?>" method="post" >
        <td><input type="hidden" name="action" value="add"></td>
        <?= CSRFProtection::tokenTag() ?>
        <td><input type="text" id="mnu_add_id" name="username" value="Stud.IP Nutzername" /></td> 
        <td><?= Studip\Button::create("Hinzufügen") ?></td>
        </form>
    </tr>
</tbody>