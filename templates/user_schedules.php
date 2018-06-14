<?php
/**
 * user_schedule template
 * prints table of schedules of a user
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 07.06.18
 * Time: 09:05
 *
 * @var Schedule[] $schedules array of schedules
 *
 */
?>

<table class="default">
    <caption>
        Eingetragenen Termine
    </caption>
    <colgroup>
        <col>
        <col>
        <col>
    </colgroup>
    <thead>
    <tr class="sortable">
        <th>Einrichtung</th>
        <th>Arbeitsplatz</th>
        <th>Zeit</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (sizeof($schedules) == 0)
    {
        ?>
        <tr>
            <td colspan="3" style="text-align: center;">Es wurde noch kein Termin bei einem Arbeitsplatz gebucht.</td>
        </tr>
        <?php
    }
    foreach ($schedules as $schedule) {
        /** @var Institute $institute */
        $institute = Institute::find($schedule->getWorkplace()->getContextId());
        ?>
        <tr>
            <td>
                <a href="<?= PluginEngine::getLink("WorkplaceAllocation", array("wp_id" => $schedule->getWorkplace()->getId(), "cid" => $institute->institut_id, "day" => $schedule->getStart()->format('d.m.Y')), "timetable") ?>">
                    <?= $institute->name ?>
                </a>
            </td>
            <td>
                <?= $schedule->getWorkplace()->getName() ?>
            </td>
            <td>
                <?= $schedule->getStart()->format('d.m.Y H:i') ?> Uhr
                -
                <?= $schedule->getStart()->add($schedule->getDuration())->format('d.m.Y H:i') ?> Uhr

            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
