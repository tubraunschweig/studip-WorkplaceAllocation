<?php
/**
 * show template
 * shows list of workplaces
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 10.05.16
 * Time: 16:59
 *
 * @var Workplace[] $workplaces array of workplaces
 */


?>
<table class="default">
    <caption>
        Arbeitspl&auml;tze
    </caption>
    <colgroup>
        <col>
        <col width="20">
        <col>
    </colgroup>
    <thead>
    <tr class="sortable">
        <th>Name</th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(sizeof($workplaces) == 0)
    {
        ?>
        <tr>
            <td colspan="3" style="text-align: center;">Es wurden noch keine Arbeitspl&auml;tze eingerichtet.</td>
        </tr>
        <?php
    }
    foreach ($workplaces as $workplace)
    {
        if($workplace->isActive()) {

            $quickBookDay = new DateTime();
            $quickBookDay->add($workplace->getRule()->getRegistrationStart())->sub($workplace->getRule()->getStart());

            /** @var \Studip\Button $quickBookButton */
            $quickBookButton = Studip\Button::create('Termin am '.$quickBookDay->format('d.m.Y').' buchen', null, array("type" => "submit"));

            ?>
            <tr>
                <td>
                    <a href="<?= PluginEngine::getLink("WorkplaceAllocation", array("wp_id" => $workplace->getId()), "timetable") ?>"><?= $workplace->getName() ?></a>
                </td>
                <td>
                    <?= /** @noinspection PhpParamsInspection */
                    tooltipIcon($workplace->getDescription()) ?>
                </td>
                <td>
                    <form action="<?= PluginEngine::getLink("WorkplaceAllocation", array("wp_id" => $workplace->getId(), "day" => $quickBookDay->format('d.m.Y')), "timetable") ?>" method="post">
                        <input type="hidden" name="next_schedule" value="true">
                        <?= $quickBookButton ?>
                    </form>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
