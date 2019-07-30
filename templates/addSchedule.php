<?php
/**
 * addSchedule template
 * prints a timetable to register for an schedule
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 23.08.16
 * Time: 13:03
 *
 * @var Workplace $workplace assigned workplace
 * @var boolean $admin is current user admin?
 * @var DateTime $day timetable day with time 00:00
 * @var String $messageBox string with containing message box for for user response
 **/

echo $messageBox;

print('<h1>'.$workplace->getName().'</h1>');

if($workplace->getRule() == null){
    ?>
    <p>
        Dieser Arbeitsplatz ist noch nicht freigeschaltet.<br>
    </p>
    <?php
} else {

    ?>

    <form class="studip-form">
        <section>
            <label for="wp_day"><?= _("Datum") ?></label>
            <input id="wp_day" name="day" value="<?= $day->format('d.m.Y') ?>" data-min-date="">
            <script>
                $(function () {
                    $("#wp_day").datepicker({
                        onSelect: function (dateText) {
                            window.location = '<?= PluginEngine::getLink(
                                    "WorkplaceAllocation",
                                    array(),
                                    $admin ? "addSchedule" : "timetable") ?>' + '&day=' + dateText + '&wp_id=<?=$workplace->getId()?>&week=<?= isset($_GET['week'])? $_GET['week'] : '0' ?>';
                        }
                    });
                })
            </script>
        </section>
    </form>
    <?php
    $nowTime = new DateTime();
    $timetableSpacingDuration = new DateInterval('PT30M');
    $tableStartTime = clone $day;
    $tableStartTime->add($workplace->getRule()->getStart());
    $tableEndTime = clone $day;
    $tableEndTime->add($workplace->getRule()->getEnd());
    if($workplace->getRule()->hasPause()){
        $pauseStartTime = clone $day;
        $pauseStartTime->add($workplace->getRule()->getPauseStart());
        $pauseEndTime = clone $day;
        $pauseEndTime->add($workplace->getRule()->getPauseEnd());
    }

    if (! isset($_GET['week']) || $_GET['week'] != '1')
    {
        ?>
        <a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId(), 'day' => $day->format('d.m.Y'), 'week' => 1), $admin ? "addSchedule" : "timetable") ?>">
            <?= Studip\Button::create('gesamte Woche zeigen') ?>
        </a>
        <?php
    }
    else
    {
        ?>
        <a href="<?= PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId(), 'day' => $day->format('d.m.Y')), $admin ? "addSchedule" : "timetable") ?>">
            <?= Studip\Button::create('nur Tag zeigen') ?>
        </a>
        <?php
    }
    if ($admin):
    ?>
        <form action="<?=PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId(), "day" => $day->format('d.m.Y')), "addSchedule")?>" method="post">
            <input type="hidden" name="wp_schedule_start" value="<?= $tableStartTime->getTimestamp() ?>">
            <input type="hidden" name="wp_schedule_duration" value="<?= $tableStartTime->diff($tableEndTime)->format('P%yY%mM%dDT%hH%iM%sS') ?>">
            <input type="hidden" name="wp_schedule_type" value="blocked">
            <?= Studip\Button::create('Ausgewählten Tag blocken', null, array("type" => "submit"))?>
        </form>
    <?php

    if(!$workplace->getRule()->isDayBookable($day, $admin, $workplace)) {
        $mb = MessageBox::error('Für diesen Tag kann (noch) kein Termin reserviert werden.');
        print($mb);
    }

    endif;
    ?>

    <div class="timetable">
        <?
        $heightCount = 0;
        print("<div style='height: 2rem;'></div>");
        for ($time = clone $tableStartTime;
             $time < $tableEndTime;
             $time->add($timetableSpacingDuration)) {
            print('<div class="time">' . $time->format("H:i") . "</div>");
            $heightCount++;
        }
        $days = [];
        $selectedDay = clone $day;
        if(isset($_GET['week']) && $_GET['week'] == '1')
        {
            $dayOne = $day->sub(new DateInterval('P'.(intval($day->format('N'))-1).'D'));
            $days[0] = (clone $dayOne);
            $days[1] = (clone $dayOne)->add(new DateInterval('P1D'));
            $days[2] = (clone $dayOne)->add(new DateInterval('P2D'));
            $days[3] = (clone $dayOne)->add(new DateInterval('P3D'));
            $days[4] = (clone $dayOne)->add(new DateInterval('P4D'));
            $days[5] = (clone $dayOne)->add(new DateInterval('P5D'));
            $days[6] = (clone $dayOne)->add(new DateInterval('P6D'));
        }
        else
        {
            $days[0] = $day;
        }
        ?>
        <div class="all_schedules" style="height: <?= ($heightCount+2)*2 ?>rem">
        <?php
        foreach ($days as $day) {
            $tableStartTime = clone $day;
            $tableStartTime->add($workplace->getRule()->getStart());
            $tableEndTime = clone $day;
            $tableEndTime->add($workplace->getRule()->getEnd());
            if($workplace->getRule()->hasPause()){
                $pauseStartTime = clone $day;
                $pauseStartTime->add($workplace->getRule()->getPauseStart());
                $pauseEndTime = clone $day;
                $pauseEndTime->add($workplace->getRule()->getPauseEnd());
            }
            ?>
            <div class="schedules">
                <?php
                $dayHeader = '<div class="schedule" style="height: 2rem; text-align: center">'.strftime('%A, %e. %h %Y',$day->getTimestamp()).'</div>';
                if ($day == $selectedDay){
                    $dayHeader = "<b>".$dayHeader."</b>";
                }
                print($dayHeader);
                $slotDuration = $workplace->getRule()->getSlotDuration();
                $daySchedules = $workplace->getSchedulesByDay($day);
                $freeSpace = 0;

                for ($time = clone $tableStartTime;
                     $time < $tableEndTime;
                     $time->add($slotDuration)) {
                    if ($freeSpace != 0) {
                        print('<div class="schedule free" style="min-height: ' . $freeSpace . 'rem; max-height: ' . $freeSpace . 'rem;"></div>');
                        $freeSpace = 0;
                    }

                    $height = ((date_create('@0')->add($slotDuration)->getTimestamp() / date_create('@0')->add($timetableSpacingDuration)->getTimestamp()) * 2);
                    $startTime = clone $time;
                    $endTime = clone $time;
                    $endTime->add($slotDuration);

                    $foundSchedule = null;
                    foreach ($daySchedules as $schedule) {
                        if ($schedule->getStart() >= $startTime && $schedule->getStart() < $endTime) {
                            $foundSchedule = $schedule;
                        }
                    }

                    if ($foundSchedule != null) {
                        $diff = date_create('@0')->add($startTime->diff($foundSchedule->getStart()))->getTimestamp();
                        if ($diff != 0) {
                            $diffHeight = (($diff / date_create('@0')->add($timetableSpacingDuration)->getTimestamp()) * 2);
                            if ($admin) {
                                /** @var \Studip\Button $fillButton */
                                $fillButton = \Studip\Button::create(
                                    _("Nachfolgende Termine aufrücken"),
                                    null,
                                    array(
                                        "type" => "submit",
                                        "class" => "schedule",
                                        "style" => "max-height: " . $diffHeight . "rem; 
                                    min-height: " . $diffHeight . "rem;"));
                                $linkToAddSchedule = PluginEngine::getLink(
                                    'WorkplaceAllocation',
                                    array(
                                        'wp_id' => $workplace->getId(),
                                        'day' => $day->format('d.m.Y')),
                                    $admin ? "addSchedule" : "timetable");

                                print('<form action="' . $linkToAddSchedule . '" method="post">');
                                print('<input type="hidden" name="action" value="move_up">');
                                print('<input type="hidden" name="wp_schedule_id" value="' . $foundSchedule->getId() . '">');
                                print('<input type="hidden" name="wp_schedule_new_start" value="' . $startTime->getTimestamp() . '">');
                                print($fillButton);
                                print('</form>');
                            } else {
                                print('<div class="schedule diff" style="min-height: ' . $diffHeight . 'rem; max-height: ' . $diffHeight . 'rem;"></div>');
                            }
                        }
                        $foundScheduleDurationSeconds = date_create('@0')->add($foundSchedule->getDuration())->getTimestamp();
                        $scheduleHeight = (($foundScheduleDurationSeconds / date_create('@0')->add($timetableSpacingDuration)->getTimestamp()) * 2);

                        if (!$foundSchedule->isBlocked() || $admin) {
                            /*Print Red Box*/
                            print('<div class="schedule booked" style="min-height: ' . $scheduleHeight . 'rem; max-height: ' . $scheduleHeight . 'rem;">');
                            if ($admin || $foundSchedule->getOwner()->user_id == get_userid()) {
                                print('<span>');
                                print(($foundSchedule->isBlocked() ? 'Geblockt von' : '') . ' ' . $foundSchedule->getOwner()->vorname . ' ' . $foundSchedule->getOwner()->nachname);
                                print('<a href="' . PluginEngine::getLink(
                                        "WorkplaceAllocation",
                                        array("wp_id" => $workplace->getId(), "s_id" => $foundSchedule->getId()),
                                        "editSchedule") . '" title="Termin bearbeiten">
                                    ' . (new Icon('edit'))->asImg() . '
                                </a>');
                                if ($foundSchedule->getStart() > new DateTime()){
                                    print('<a href="' . PluginEngine::getLink(
                                            "WorkplaceAllocation",
                                            array("wp_id" => $workplace->getId(), "s_id" => $foundSchedule->getId()),
                                            "removeSchedule") . '" title="Termin löschen">
                                        ' . (new Icon('trash'))->asImg() . '
                                    </a>');
                                }
                                if($admin){
                                    ?>
                                    <form action="<?= PluginEngine::getLink("WorkplaceAllocation", array(), "manageBlacklist") ?>" method="post" style="display:inline">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="expiration" value="7" >
                                    <input type="hidden" name="user_id" value="<?= $foundSchedule->getOwner()->user_id ?>">
                                    <input type="image" src="<?= (Icon::create('remove-circle', 'clickable'))->asImagePath() ?>" title="Benutzer 7 Tage sperren" value="sperren" alt="Submit">
                                    </form>      
                                    <?php
                                }
                                print('</span>');

                                print('<div class="print-only timetable-comment">' . $foundSchedule->getComment() . '</div>' );
                            } else {
                                print('<span>Der Termin ist bereits vergeben</span>');
                            }
                            print('</div>');
                        } else {
                            print('<div class="schedule blocked" style="min-height: ' . $scheduleHeight . 'rem; max-height: ' . $scheduleHeight . 'rem;"></div>');
                        }

                        $time = clone $foundSchedule->getStart();
                        $time->add($foundSchedule->getDuration())->sub($slotDuration);
                    } else if ($workplace->getRule()->hasPause() && $endTime > $pauseStartTime && $startTime < $pauseEndTime) {
                        $diff = date_create('@0')->add($startTime->diff($pauseStartTime))->getTimestamp();
                        if ($diff != 0) {
                            $diffHeight = (($diff / date_create('@0')->add($timetableSpacingDuration)->getTimestamp()) * 2);
                            print('<div class="schedule diff" style="min-height: ' . $diffHeight . 'rem; max-height: ' . $diffHeight . 'rem;"></div>');
                        }

                        $pauseDuration = date_create('@0')->add($pauseStartTime->diff($pauseEndTime))->getTimestamp();
                        $scheduleHeight = (($pauseDuration / date_create('@0')->add($timetableSpacingDuration)->getTimestamp()) * 2);
                        print('<div class="schedule booked" style="min-height: ' . $scheduleHeight . 'rem; max-height: ' . $scheduleHeight . 'rem;">');
                        print('<span>Pause</span>');
                        print('</div>');
                        $time = clone $pauseEndTime;
                        $time->sub($slotDuration);
                    } else if ($startTime >= $nowTime && $workplace->getRule()->isDayBookable($day, $admin) && $endTime <= $tableEndTime) {
                        /** @var \Studip\Button $bookButton */
                        $bookButton = \Studip\Button::create(
                            _($startTime->format('H:i') . " Uhr bis " . $endTime->format('H:i') . " Uhr buchen"),
                            null,
                            array(
                                "type" => "submit",
                                "class" => "schedule",
                                "style" => "max-height: " . $height . "rem; 
                            min-height: " . $height . "rem;"));

                        print('<form action="' . PluginEngine::getLink('WorkplaceAllocation', array('wp_id' => $workplace->getId(), "day" => $day->format('d.m.Y')), $admin ? "addSchedule" : "timetable") . '" method="post">');
                        print('<input type="hidden" name="wp_schedule_start" value="' . $startTime->getTimestamp() . '">');
                        print('<input type="hidden" name="wp_schedule_duration" value="' . $slotDuration->format('P%yY%mM%dDT%hH%iM%sS') . '">');
                        print($bookButton);
                        print('</form>');
                    } else {
                        $freeSpace += $height;
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
        </div>
    </div>
    <?php
}