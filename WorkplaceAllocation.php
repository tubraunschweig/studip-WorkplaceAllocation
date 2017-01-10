<?php

/**
 * WorkplaceAllocation class
 * main class of Workplace allocation plugin
 * manages the views and controllers of the plugin
 *
 * Workplace allocation Plugin:
 * allows admins of institutes and courses to manage the registration of students to institute or course related workplaces.
 *
 * Created by PhpStorm.
 * User: jayjay
 * Date: 09.05.16
 * Time: 10:26
 */
class WorkplaceAllocation extends StudIPPlugin implements StandardPlugin
{
    /** @var Flexi_TemplateFactory */
    private $templateFactory;

    /**
     * WorkplaceAllocation constructor.
     *
     * called by StudIP core
     */
    public function __construct()
    {
        parent::__construct();
        
        require_once __DIR__."/classes/Workplace.php";
        require_once __DIR__."/classes/Rule.php";
        require_once __DIR__."/classes/Schedule.php";
        require_once __DIR__."/classes/WaitingList.php";
        require_once __DIR__."/lib/LPLib_Feiertage_Connector.php";
        require_once __DIR__."/classes/Blacklist.php";
        require_once __DIR__."/classes/WpNotifications.php";
        require_once __DIR__."/classes/WpMessages.php";
        
        $this->templateFactory = new Flexi_TemplateFactory($this->getPluginPath().'/templates');


    }

    /**
     * Return a template (an instance of the Flexi_Template class)
     * to be rendered on the course summary page. Return NULL to
     * render nothing for this plugin.
     *
     * The template will automatically get a standard layout, which
     * can be configured via attributes set on the template:
     *
     *  title        title to display, defaults to plugin name
     *  icon_url     icon for this plugin (if any)
     *  admin_url    admin link for this plugin (if any)
     *  admin_title  title for admin link (default: Administration)
     *
     * @param $course_id
     * @return Flexi_Template template object to render or NULL
     */
    function getInfoTemplate($course_id)
    {
        return null;
    }

    /**
     * Return a navigation object representing this plugin in the
     * course overview table or return NULL if you want to display
     * no icon for this plugin (or course). The navigation object's
     * title will not be shown, only the image (and its associated
     * attributes like 'title') and the URL are actually used.
     *
     * By convention, new or changed plugin content is indicated
     * by a different icon and a corresponding tooltip.
     *
     * @param  string $course_id course or institute range id
     * @param  int $last_visit time of user's last visit
     * @param  string $user_id the user to get the navigation for
     *
     * @return object   navigation item to render or NULL
     */
    function getIconNavigation($course_id, $last_visit, $user_id)
    {
        return null;
    }

    /**
     * Return a navigation object representing this plugin in the
     * course overview table or return NULL if you want to display
     * no icon for this plugin (or course). The navigation object's
     * title will not be shown, only the image (and its associated
     * attributes like 'title') and the URL are actually used.
     *
     * By convention, new or changed plugin content is indicated
     * by a different icon and a corresponding tooltip.
     *
     * @param  string $course_id course or institute range id
     *
     * @return array    navigation item to render or NULL
     */
    function getTabNavigation($course_id)
    {

        if (!$this->isActivated($course_id))
        {
            return null;
        }
        
        $workplaceAllocation = new Navigation("Arbeitspl&#228;tze", PluginEngine::getURL("WorkplaceAllocation", array(), 'show'));
        $workplaceAllocation->setImage('icons/16/white/computer');
        $workplaceAllocation->setActiveImage('icons/16/black/computer');

        if($this->user_has_admin_perm($course_id)) {
            $workplacesAdminNav = new Navigation('Arbeitspl&#228;tze', PluginEngine::getURL("WorkplaceAllocation", array(), 'admin'));
            $workplacesAdminNav->setDescription("Richten Sie Anmeldungen zu Arbeitspl&#228;tzen f&#252;r Ihre Studierenden ein.");

            /** @var Navigation $courseAdminNav */
            $courseAdminNav = Navigation::getItem("course/admin");
            $courseAdminNav->addSubNavigation("workplaces", $workplacesAdminNav);
        }


        return array(
            "workplaces" => $workplaceAllocation
        );
    }

    /**
     * return a list of ContentElement-objects, containing
     * everything new in this module
     *
     * @param  string $course_id the course-id to get the new stuff for
     * @param $since
     * @param  string $user_id the user to get the notifcation-objects for
     * @return array an array of ContentElement-objects
     * @internal param int $last_visit when was the last time the user visited this module
     */
    function getNotificationObjects($course_id, $since, $user_id)
    {
        return array();
    }


    /**
     * check if actual user has admin permissions in the actual context
     *
     * @param string $course_id context id
     * @return bool
     */
    private function user_has_admin_perm($course_id) {
        $status = $GLOBALS['perm']->get_studip_perm($course_id, get_userid());

        if($status == "dozent" || $status == "tutor" || $status == "admin" || $status == "root") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * main route for students
     */
    public function show_action()
    {
        
        Navigation::activateItem('/course/workplaces');
        
        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('show');
        $template->set_attribute('workplaces', Workplace::getWorkplacesByContext($_GET['cid']));
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        print($template->render());
    }

    /**
     * main route for admins
     *
     * @throws AccessDeniedException
     */
    public function admin_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }
        
        $actionsWidget = new ActionsWidget();
        $actionsWidget->addLink(
            "Arbeitsplatz hinzuf&#252;gen", 
            PluginEngine::getLink("WorkplaceAllocation", array(), "addWorkplace"),
            "icons/blue/16/add"
        );
        $actionsWidget->addLink(
            "Sperrungen verwalten",
            PluginEngine::getLink("WorkplaceAllocation", array(), "manageBlacklist"),
            "icons/blue/16/community"
        );
        $actionsWidget->addLink(
            "Mailbenachrichtigungen verwalten",
            PluginEngine::getLink("WorkplaceAllocation", array(), "manageMail"),
            "icons/blue/16/mail"
        );
        $actionsWidget->addLink(
            "Alle Arbeitsplätze drucken",
            PluginEngine::getLink("WorkplaceAllocation", array(), "pdf"),
            "icons/blue/16/print",
            array('target' => '_blank')
        );
        
        Sidebar::Get()->addWidget($actionsWidget);
        
        Navigation::activateItem('/course/admin/workplaces');
        
        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('admin');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('workplaces', Workplace::getWorkplacesByContext($_GET['cid']));
        
        print($template->render());
    }

    /**
     * save activation route
     * set an new activation state for all workplaces in the $_POST array
     *
     * @throws AccessDeniedException
     */
    public function saveActivation_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }

        if(Request::isPost())
        {
            foreach ($_POST as $id => $state)
            {
                $workplace = Workplace::getWorkplace($id);

                switch($state)
                {
                    case 'on':
                        $workplace->activate();
                        break;
                    case 'off':
                        $workplace->deactivate();
                        break;
                }
            }
        }
        header('Location: '.PluginEngine::getLink('WorkplaceAllocation', array(), 'admin'));
        exit;
    }

    /**
     * route to create new workplace
     *
     * @throws AccessDeniedException
     */
    public function addWorkplace_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }
        $errorDetails = array();
        $error = false;
        
        if(Request::isPost())
        {
            
            if(empty($_POST["wp_name"])) {
                $error = true;
                $errorDetails[] = _("Bitte geben Sie einen Namen f&#252;r den Arbeitsplatz an.");
            }
            else
            {
                Workplace::newWorkplace($_POST['wp_name'], $_POST['wp_description'], $_GET['cid']);
                header('Location: '.PluginEngine::getLink('WorkplaceAllocation', array(), 'admin'));
            }
        } else {
            $_POST['wp_name'] = "";
            $_POST['wp_description'] = "";
        }
        
        Navigation::activateItem('/course/admin/workplaces');

        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('addWorkplace');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute("error", $error);
        $template->set_attribute("errorDetails", $errorDetails);

        print($template->render());
    }

    /**
     * route to delete a workplace
     * first ask if you really would like to delete this workplace
     *
     * @throws AccessDeniedException
     */
    public function delWorkplace_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }


        $workplace = Workplace::getWorkplace($_GET['wp_id']);

        if(isset($_GET['delete']))
        {
            if($_GET['delete'])
            {
                $workplace->deleteWorkplace();
            }
            header('Location: '.PluginEngine::getLink('WorkplaceAllocation', array(), 'admin'));

        }
        else
        {
            $this->admin_action();

            print(createQuestion(
                "M&#246;chten Sie den Arbeitsplatz \"".$workplace->getName()."\" wirklich l&#246;schen ?", 
                array("delete" => true, "wp_id" => $workplace->getId()), 
                array("delete" => false, "wp_id" => $workplace->getId())
            ));
        }
    }

    /**
     * route to edit workplace
     *
     * @throws AccessDeniedException
     */
    public function editWorkplace_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }

        Navigation::activateItem('/course/admin/workplaces');

        $workplace = Workplace::getWorkplace($_GET['wp_id']);
        $rule = $workplace->getRule();


        $errorDetails = array();
        $error = false;
        
        if(Request::isPost())
        {
            if(isset($_POST['day']) && (empty($_POST['daily_start_hour'])
                || empty($_POST['daily_start_minute'])
                || empty($_POST['daily_end_hour'])
                || empty($_POST['daily_end_minute'])))
            {
                $error = true;
                $errorDetails[] = "Bitte geben sie einen korrekten Wert f&#252;r die t&#228;gliche &#214;ffnungszeit an.";
            }
            else
            {
                $workplace->setDescription($_POST['wp_description']);
                $workplace->setName($_POST['wp_name']);

                $start = 'PT'.$_POST['daily_start_hour'].'H'.$_POST['daily_start_minute'].'M';
                $end = 'PT'.$_POST['daily_end_hour'].'H'.$_POST['daily_end_minute'].'M';
                if($_POST['daily_pause_exist'] == 'on') {
                    $pauseStart = 'PT'.$_POST['daily_pause_start_hour'].'H'.$_POST['daily_pause_start_minute'].'M';
                    $pauseEnd = 'PT'.$_POST['daily_pause_end_hour'].'H'.$_POST['daily_pause_end_minute'].'M';
                } else {
                    $pauseStart = null;
                    $pauseEnd = null;
                }
                if($rule == null)
                {
                    $workplace->createRule($start, $end, $pauseStart, $pauseEnd, $_POST['registration_start'], $_POST['registration_end'], $_POST['slot_duration']);
                    $rule = $workplace->getRule();
                }
                else
                {
                    $rule->setStart($start);
                    $rule->setEnd($end);
                    $rule->setPauseStart($pauseStart);
                    $rule->setPauseEnd($pauseEnd);
                    $rule->setRegistrationStart($_POST['registration_start']);
                    $rule->setRegistrationEnd($_POST['registration_end']);
                    $rule->setSlotDuration($_POST['slot_duration']);
                }
                if(isset($_POST['one_schedule_by_day_and_user']) && $_POST['one_schedule_by_day_and_user'] == 'on') {
                    $rule->setOneScheduleByDayAndUser(true);
                } else {
                    $rule->setOneScheduleByDayAndUser(false);
                }
                if(is_array($_POST['day'])) {
                    for ($i = 0; $i < 7; $i++) {
                        if (in_array($i, $_POST['day'])) {
                            $rule->setDay($i, true);
                        } else {
                            $rule->setDay($i, false);
                        }
                    }
                }

            }
        }
        
        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('editWorkplace');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('workplace', $workplace);
        $template->set_attribute('error', $error);
        $template->set_attribute('errorDetails', $errorDetails);
        
        print($template->render());
    }

    /**
     * route to add schedule
     *
     * @param bool $isSetNavigation if this is an embedded route set true
     */
    public function addSchedule_action($isSetNavigation = false)
    {

        if(!$isSetNavigation)
        {
            Navigation::activateItem('/course/admin/workplaces');
        }

        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            $admin = false;
        } else {
            $admin = true;
        }

        $workplace = Workplace::getWorkplace($_GET['wp_id']);
        $nowTime = new DateTime();
        if(isset($_GET['day']))
        {
            $day = new DateTime($_GET['day']);
        } else {
            $day = new DateTime($nowTime->format('d.m.Y'));
        }

        $messageBox = null;

        if(Request::isPost())
        {
            if(isset($_POST['next_schedule']) && $_POST['next_schedule'] == 'true') {
                if(!$workplace->getRule()->bookFirstPossibleSchedule($workplace, $day, $admin)) {
                    if(!$workplace->getRule()->isDayBookable($day, $admin, $workplace)) {
                        $messageBox = MessageBox::error('Der Termin konnte nicht gebucht werden, dies kann verschiedene Ursachen haben', array('Sie wurden gesperrt.', 'Es ist nur ein Termin pro Nutzer und Tag zugelassen.', 'Es ist zu einer Kollision gekommen, in diesem Falle versuche in der &#220;bersicht nochmal einen Termin f&#252;r diesen Tag zu buchen um einen Platz auf der Warteliste zu bekommen.'));
                    } else {
                        $waitingListPlacement = WaitingList::push($workplace, $day);
                        if ($waitingListPlacement != null) {
                            $messageBox = MessageBox::error('Am ' . $day->format('d.m.Y') . ' ist kein Termin mehr frei. Sie wurden in die Warteliste auf Platz ' . $waitingListPlacement . ' eingetragen');
                        }
                    }
                }
            } else if (isset($_POST['action']) && $_POST['action'] == 'move_up' && isset($_POST['wp_schedule_id']) && isset($_POST['wp_schedule_new_start'])) {
                $schedule = Schedule::getSchedule($_POST['wp_schedule_id']);
                $newStart = new DateTime('@'.$_POST['wp_schedule_new_start']);
                $schedule->setStart($newStart, true);
                $workplace->refillFromWaitingList($day);
            } else {
                $start = $_POST['wp_schedule_start'];
                $duration = $_POST['wp_schedule_duration'];
                if ($workplace->getRule()->isBookable(new DateTime('@' . $start), new DateInterval($duration), $workplace, $admin)) {
                    $blocked = false;
                    if (isset($_POST['wp_schedule_type']) && $_POST['wp_schedule_type'] == 'blocked') {
                        $blocked = true;
                    }
                    Schedule::newSchedule(get_userid(), $workplace->getId(), $start, $duration, "", $blocked);
                } else {
                    $messageBox = MessageBox::error('Der Termin konnte nicht gebucht werden, dies kann verschiedene Ursachen haben', array('Der Termin ist bereits belegt.', 'Es ist nur ein Termin pro Nutzer und Tag zugelassen.'));
                }
            }
        }

        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('addSchedule');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('messageBox', $messageBox);
        $template->set_attribute('workplace', $workplace);
        $template->set_attribute('admin', $admin);
        $template->set_attribute('day', $day);

        PageLayout::addStylesheet($this->getPluginURL().'/assets/stylesheets/timetable.css');
        print($template->render());

    }

    /**
     * route to timetable view
     */
    public function timetable_action()
    {
        Navigation::activateItem('/course/workplaces');
        $this->addSchedule_action(true);
    }

    /**
     * route to edit a specific schedule
     */
    public function editSchedule_action() {
        if(!$this->user_has_admin_perm($_GET['cid']))
        {
            Navigation::activateItem('/course/workplaces');
            $admin = false;
        } else {
            Navigation::activateItem('/course/admin/workplaces');
            $admin = true;
        }



        $schedule = Schedule::getSchedule($_GET['s_id']);

        $messageBoxes = array();

        if(Request::isPost())
        {
            if(isset($_POST['s_duration']) && $admin) {
                $duration = new DateInterval($_POST['s_duration']);
                if(!$schedule->setDuration($duration)) {
                    $messageBoxes[] = MessageBox::error('Die &#196;nderung der Terminl&#228;nge ist nicht zul&#228;ssig');
                }
            }
            if(isset($_POST['s_comment']) && $schedule->getOwner()->getUserid() == get_userid()) {
                $schedule->setComment($_POST['s_comment']);
            }
            if(isset($_POST['s_owner']) && $admin) {
                $newOwner = new StudipUser($_POST['s_owner']);
                $schedule->setOwner($newOwner);
            }
        }

        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('editSchedule');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('schedule', $schedule);
        $template->set_attribute('admin', $admin);
        $template->set_attribute('messageBoxes', $messageBoxes);

        print($template->render());
    }

    /**
     * route to remove schedule
     * first asks if you really like to remove this schedule
     *
     * @throws AccessDeniedException
     */
    public function removeSchedule_action() {
        $schedule = Schedule::getSchedule($_GET['s_id']);

        $admin = $this->user_has_admin_perm($_GET['cid']);

        if(!($this->user_has_admin_perm($_GET['cid']) || $schedule->getOwner()->getUserid() == get_userid())) {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }
        $start = $schedule->getStart();

        if(isset($_GET['delete'])) {
            if($_GET['delete']) {
                $schedule->deleteSchedule();
            }
            header('Location: '.PluginEngine::getURL('WorkplaceAllocation', array('wp_id' => $_GET['wp_id'], 'day' => $start->format('d.m.Y')), $admin ? 'addSchedule': 'timetable'));
        } else {
            $_GET['day'] = $schedule->getStart()->format('d.m.Y');
            $admin ? $this->addSchedule_action() : $this->timetable_action();
            print(createQuestion(
                "M&#246;chten Sie den Termin wirklich l&#246;schen ?",
                array('delete' => true, 's_id' => $schedule->getId(), 'wp_id' => $schedule->getWorkplace()->getId()),
                array('delete' => false, 's_id' => $schedule->getId(), 'wp_id' => $schedule->getWorkplace()->getId())));
        }


    }

    /**
     * route to manage blacklist
     *
     * @throws AccessDeniedException
     */
    public function manageBlacklist_action() {
        if(!$this->user_has_admin_perm($_GET['cid'])) {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }

        if(Request::isPost()) {
            if(isset($_POST['action']) && isset($_POST['user_id'])) {
                $user = new StudIPUser($_POST['user_id']);
                switch ($_POST['action']) {
                    case 'delete':
                        if(!isset($_POST['delete'])){
                            $trueResponse = $_POST;
                            $trueResponse['delete'] = true;
                            $falseResponse = $_POST;
                            $falseResponse['delete'] = false;
                            print(createQuestion2(
                                "Möchten sie den Nutzer ".$user->getGivenname()." ".$user->getSurname()." (".$user->getUsername().") wirklich von der Sperrliste entfernen?",
                                $trueResponse,
                                $falseResponse,
                                "?cid=".$_GET['cid']
                            ));
                        }
                        if($_POST['delete']) {
                            Blacklist::getBlacklist()->deleteFromList($user->getUserid());
                        }
                        break;
                    case 'add':
                        if(!isset($_POST['add'])){
                            $trueResponse = $_POST;
                            $trueResponse['add'] = true;
                            $falseResponse = $_POST;
                            $falseResponse['add'] = false;
                            print(createQuestion2(
                                "Möchten sie den Nutzer \"".$user->getGivenname()." ".$user->getSurname()." (".$user->getUsername().")\" wirklich zur Sperrliste hinzufügen?",
                                $trueResponse,
                                $falseResponse,
                                "?cid=".$_GET['cid']
                            ));
                        }
                        if($_POST['add']) {
                            $expiration = null;
                            if(sizeof($_POST['expiration']) > 0 && $_POST['expiration'] > 0){
                                $time = new DateTime();
                                $today = new DateTime($time->format('d.m.Y'));
                                $expiration = $today->getTimestamp() + ($_POST['expiration'] * 24 * 60 * 60) -1;
                            }
                            Blacklist::getBlacklist()->addToList($user->getUserid(), $expiration);
                        }
                        break;
                }
            }
        }

        Navigation::activateItem('/course/admin/workplaces');

        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('manageBlacklist');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('blacklist', Blacklist::getBlacklist($_GET['cid']));

        PageLayout::addStylesheet($this->getPluginURL().'/assets/stylesheets/link_button.css');

        print($template->render());
    }

    /**
     * route to manage custom StudIP mail texts
     *
     * @throws AccessDeniedException
     */
    public function manageMail_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid'])){
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }

        Navigation::activateItem('/course/admin/workplaces');

        require_once(__DIR__.'/conf/default_mesage_texts.php');
        global $defaultMessageTexts;

        foreach ($defaultMessageTexts as $messageTextId => $messageTextDetails) {
            $studipMessage = WpMessages::findBySQL("context_id = ? AND hook_point = ?", array($_GET['cid'], $messageTextId));
            if(sizeof($studipMessage) > 0) {
                $defaultMessageTexts[$messageTextId]['studip_message'] = $studipMessage[0];
            } else {
                $defaultMessageTexts[$messageTextId]['studip_message'] = null;
            }
        }
        if(Request::isPost()) {

        }

        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('manageMail');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('defaultMessageTexts', $defaultMessageTexts);

        print($template->render());
    }

    /**
     * route to edit custom StudIP message texts
     *
     * @throws AccessDeniedException
     */
    public function editMailtext_action()
    {
        if(!$this->user_has_admin_perm($_GET['cid'])) {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }

        $message = WpMessages::findBySQL('context_id = ? AND hook_point = ?', array($_GET['cid'], $_GET['hook_point']));

        if(sizeof($message) == 0){
            /** @var WpMessages $message */
            $message = null;
        } else {
            /** @var WpMessages $message */
            $message = $message[0];
        }

        if(Request::isPost()) {
            $data = array(
                'context_id' => $_GET['cid'],
                'hook_point' => $_GET['hook_point'],
                'subject' => $_POST['subject'],
                'message' => $_POST['text'],
                'active' => isset($_POST['active']) && $_POST['active'] == 'on'
            );
            if($message == null) {
                $message = new WpMessages();
                $message->id = $message->getNewId();
            }
            foreach ($data as $key => $item) {
                //$message->setValue($key, $item);
                $message->$key = $item;
            }
            $message->store();
        }

        Navigation::activateItem('/course/admin/workplaces');


        /** @var Flexi_Template $template */
        $template = $this->templateFactory->open('editMailtext');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->set_attribute('message', $message);

        print($template->render());

    }

    /**
     * route to get pdf export of timetable
     *
     * @throws AccessDeniedException
     */
    public function pdf_action() {

        if (!$this->user_has_admin_perm($_GET['cid'])) {
            throw new AccessDeniedException("Du hast nicht die nötigen Rechte zum Aufruf dieser Seite");
        }

        if(isset($_GET['wp_id'])) {
            $workplace = Workplace::getWorkplace($_GET['wp_id']);
            $workplaces = array($workplace);
        } else {
            $workplaces = Workplace::getWorkplacesByContext($_GET['cid']);
        }

        require_once(__DIR__.'/lib/TCPDF/tcpdf.php');

        /** @noinspection PhpUndefinedClassInspection */
        $pdf = new TCPDF();

        //document information
        $pdf->SetCreator('Stud.IP Arbeitsplatz Vergabe Plugin');
        $pdf->SetAuthor('Stud.IP');
        if(isset($workplace)) {
            $pdf->SetTitle('Arbeitsplatz ' . $workplace->getName() . " " . date('d.m.Y'));
        } else {
            $pdf->SetTitle('Arbeitsplätze ' . date('d.m.Y'));
        }
        $pdf->SetSubject('Stud.IP Arbeitsplätze');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);


        foreach ($workplaces as $wp) {
            #Collecting information

            $startTime = new DateTime(date('d.m.Y'));
            $startTime->add($wp->getRule()->getStart());

            $endTime = new DateTime(date('d.m.Y'));
            $endTime->add($wp->getRule()->getEnd());

            $schedules = $wp->getSchedulesByDay(new DateTime());

            $availableHeight = 200;
            $steps = $endTime->getTimestamp() - $startTime->getTimestamp();
            $stepHeight = $availableHeight / $steps;

            $topStart = 70;


            $pdf->AddPage();

            #Header
            $pdf->Image(__DIR__.'/img/studip-logo.png', 148.67, 17.5, 53.33, 12.5, "PNG");
            $pdf->Image(__DIR__.'/img/tubs_logo.jpg', 17, 17.5, 63, 23, "JPG");
            $pdf->Rect(80, 35, 122, 0.5, 'F', array(), array(190, 30, 60));

            #Headline
            $pdf->SetXY(27, 49);
            $pdf->SetFontSize(20);
            $pdf->SetFont(null, 'b');
            $pdf->Cell(155.5, 10, $wp->getName());

            #Date
            $pdf->SetXY(27, 55);
            $pdf->SetFont(null, 'n', 12);
            $pdf->Cell(155.5, 10, date('d.m.Y'));

            #Timetable
            $pdf->SetXY(27, $topStart);

            for($i = 0; $i< $steps; $i+=(60*30)) {
                $pdf->Rect(27, $topStart+$i*$stepHeight, 175, 0.5, 'F', array(), array(200, 200, 200));
                $pdf->SetXY(27, $topStart+$i*$stepHeight);
                $pdf->Cell(30, (60*30)*$stepHeight, date('H:i', $i+$startTime->getTimestamp()));
            }

            foreach ($schedules as $schedule) {
                $timeString = '1970-01-01 '.$schedule->getStart()->format('H:i:s');
                $scheduleStartTableStart = new DateTime($timeString, new DateTimeZone('UTC'));
                $scheduleStartTableStart->sub($wp->getRule()->getStart());
                $scheduleDurationTime = new DateTime('@0');
                $scheduleDurationTime->add($schedule->getDuration());
                $pdf->Rect(47, $topStart+$scheduleStartTableStart->getTimestamp()*$stepHeight, 155, $scheduleDurationTime->getTimestamp()*$stepHeight, 'FD',array('all' => array('width' => 0.5, 'color' => array(35, 64, 153))), array(255,255,255));
                $pdf->SetXY(50, $topStart+$scheduleStartTableStart->getTimestamp()*$stepHeight);
                $pdf->Cell(150, $scheduleDurationTime->getTimestamp()*$stepHeight, $schedule->getOwner()->getGivenname().' '.$schedule->getOwner()->getSurname());
            }


            #Footer
            $pdf->SetXY(24, 276);
            $pdf->SetFontSize(10);
            $pdf->Cell(50, 5, "Seite ".$pdf->getAliasNumPage()." von ".$pdf->getAliasNbPages());
        }

        print($pdf->Output('studip_arbeitsplatz.pdf', 'I'));
    }
}