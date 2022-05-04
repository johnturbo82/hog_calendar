<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
class Controller

{
	private $request = null;
	private $template = '';
	private $view = null;
	private $id = null;

	/**
	 * Controller constructor
	 * @param Array $request Array from $_GET & $_POST.
	 */
	public function __construct($request)
	{
		$this->request = $request;
		$this->view = new View();
		$this->model = new Model();
		$this->template = $this->process_request();
		$this->event_id = !empty($request['event_id']) ? trim($request['event_id'], '/') : null;
	}

	/**
	 * If view not set forward
	 */
	private function process_request()
	{
		if (empty($this->request['view'])) {
			$heading = "Location: " . SITE_ADDRESS . "?view=events";
			header($heading);
			exit();
		} else {
			return trim($this->request['view'], '/');
		}
	}

	/**
	 * Show content depeding on GET view
	 * @return String application content
	 */
	public function display()
	{
		$view = new View();
		switch ($this->template) {
			case 'events':
				$view->assign('event_list', $this->get_event_list());
				$view->setTemplate($this->template);
				$this->view->assign('menu', true);
				break;
			case 'bookable_events':
				$event_ids = $this->model->get_recent_bookable_events();
				$event_list = array();
				foreach ($event_ids as $event_id) {
					$event = $this->get_event($event_id);
					$event_list[$event->from . " " . $event->id] = $event;
				}
				ksort($event_list);
				$view->assign('event_list', $event_list);
				$view->setTemplate($this->template);
				$this->view->assign('title', "Aktuell offene Terminbuchungen");
				break;
			case 'book':
				$event = $this->get_event();
				if (isset($_COOKIE['booking_name']) && isset($_COOKIE['booking_givenname'])) {
					$booking = $this->model->get_booking_by_name($event->id, $_COOKIE['booking_name'], $_COOKIE['booking_givenname']);
					if ($booking != null) {
						$view->assign('event', $event);
						$view->assign('booking', $booking);
						$view->setTemplate("booking_exists");
						$this->view->assign('title', "Event \"" . $event->name . "\" buchen...");
						break;
					}
				}
				$view->assign('event', $event);
				$view->setTemplate($this->template);
				$this->view->assign('title', "Event \"" . $event->name . "\" buchen...");
				break;
			case 'bookme':
				$this->set_booking_cookies();
				$this->check_booking_closed();
				if ($this->request['persons'] == 0) {
					$view->assign('event_id', $this->request['event_id']);
					$view->assign('name', $this->request['name']);
					$view->assign('givenname', $this->request['givenname']);
					$view->assign('email', $this->request['email']);
					$view->assign('persons', $this->request['persons']);
					$view->assign('from', $this->request['from']);
					$view->assign('eventname', $this->request['eventname']);
					$view->assign('mailtext', $this->request['mailtext']);
					$view->setTemplate("booking_zero");
				} else if ($this->model->booking_exists($this->request['event_id'], $this->request['name'], $this->request['givenname'])) {
					$event = $this->get_event();
					$booking = $this->model->get_booking_by_name($event->id, $this->request['name'], $this->request['givenname']);
					if ($booking != null) {
						$view->assign('event', $event);
						$view->assign('booking', $booking);
						$view->setTemplate("booking_exists");
						$this->view->assign('title', "Event \"" . $event->name . "\" buchen...");
						break;
					}
					$view->setTemplate("404");
				} else {
					if ($this->model->new_booking($this->request['event_id'], $this->request['eventname'], $this->request['from'], $this->request['name'], $this->request['givenname'], $this->request['email'], $this->request['persons'])) {
						$this->send_booking_success_mail();
						$view->setTemplate("booked");
					} else {
						$view->setTemplate("404");
					}
				}
				break;
			case 'bookings':
				$view->assign('event', $this->get_event());
				$view->assign('bookings', $this->model->get_bookings($this->event_id));
				$view->setTemplate($this->template);
				break;
			case 'change_persons':
				$this->model->change_persons($this->request['event_id'], $this->request['booking_id'], $this->request['persons']);
				$view->setTemplate('change_success');
				break;
			case 'storno':
			case 'delete_booking':
				if (!$this->model->delete_booking($this->request['booking_id'], $this->request['event_id'])) {
					die("ERROR");
				}
				if ($this->template == 'storno') {
					$heading = "Location: " . SITE_ADDRESS . "?view=manage&event_id=" . $this->request['event_id'];
				} else {
					$heading = "Location: " . SITE_ADDRESS . "?view=delete_success";
				}
				header($heading);
				exit();
				break;
			case 'delete_success':
				$view->setTemplate($this->template);
			case 'cancel':
				$view->setTemplate($this->template);
				break;
			case 'manage':
				$view->assign('event', $this->get_event());
				$view->assign('bookings', $this->model->get_bookings($this->event_id));
				$view->assign('stornos', $this->model->get_stornos($this->event_id));
				$view->setTemplate($this->template);
				break;
			case 'close_event':
				$this->model->close_event($this->request['event_id']);
				$heading = "Location: " . SITE_ADDRESS . "?view=events";
				header($heading);
				exit();
				break;
			case 'polls':
				$view->assign('polls', $this->get_poll_list());
				$view->assign('inactive_polls', $this->get_poll_list(0));
				$view->setTemplate($this->template);
				$this->view->assign('menu', true);
				break;
			case 'new_poll':
				$view->setTemplate($this->template);
				break;
			case 'create_poll':
				$this->model->create_poll($this->request['name'], $this->request['description'], $this->request['options'], $this->request['multichoice']);
				$heading = "Location: " . SITE_ADDRESS . "?view=polls";
				header($heading);
				break;
			case 'poll':
				$voted = $_COOKIE["poll_" . $this->request['poll_id']];
				if ($voted == "voted") {
					$heading = "Location: " . SITE_ADDRESS . "?view=poll_result&poll_id=" . $this->request['poll_id'];
					header($heading);
				}
				$poll = $this->get_poll($this->request['poll_id']);
				if (!$poll->active) {
					$heading = "Location: " . SITE_ADDRESS . "?view=poll_result&poll_id=" . $this->request['poll_id'];
					header($heading);
				}
				$view->assign('poll', $poll);
				$view->setTemplate($this->template);
				$this->view->assign('title', "An Abstimmung \"" . $poll->name . "\" teilnehmen...");
				break;
			case 'poll_result':
				$voted = $_COOKIE["poll_" . $this->request['poll_id']];
				if ($voted == "voted") {
					$view->assign('voted', "Du hast bereits abgestimmt! Vielen Dank.");
				}
				$poll = $this->get_poll($this->request['poll_id']);
				$view->assign('poll', $poll);
				$view->assign('results', $this->model->get_poll_results($this->request['poll_id']));
				$view->setTemplate($this->template);
				$this->view->assign('title', "Ergebnisse \"" . $poll->name . "\"");
				break;
			case 'inactivate_poll':
				$this->model->change_poll_status($this->request['poll_id'], 0);
				$heading = "Location: " . SITE_ADDRESS . "?view=polls";
				header($heading);
				break;
			case 'activate_poll':
				$this->model->change_poll_status($this->request['poll_id'], 1);
				$heading = "Location: " . SITE_ADDRESS . "?view=polls";
				header($heading);
				break;
			case 'vote':
				$this->set_cookie("poll_" . $this->request['poll_id'], "voted");
				if ($this->model->vote_exists($this->request['poll_id'], $this->request['name'], $this->request['givenname'])) {
					$heading = "Location: " . SITE_ADDRESS . "?view=poll_result&poll_id=" . $this->request['poll_id'];
					header($heading);
				} else {
					$this->model->process_vote($this->request['poll_id'], $this->request['vote'], $this->request['name'], $this->request['givenname'], $this->request['email']);
					$heading = "Location: " . SITE_ADDRESS . "?view=poll_result&poll_id=" . $this->request['poll_id'];
					header($heading);
				}
				break;
			default:
				$view->setTemplate("404");
				$this->view->assign('menu', true);
				break;
		}
		$this->view->setTemplate('site');
		$this->view->assign('content', $view->loadTemplate());
		return $this->view->loadTemplate();
	}

	/**
	 * Get array of events
	 * @return Array of events
	 */
	private function get_event_list()
	{
		$events_json = $this->load_events();
		$events = array();

		foreach ($events_json->items as $event) {
			if ($event->visibility == "private") {
				continue;
			}
			if (isset($event->start->dateTime)) {
				$from = $event->start->dateTime;
				$to = $event->end->dateTime;
			} else {
				$from = $event->start->date;
				$to = $event->end->date;
			}
			$now = new DateTime("now");
			$event_date = new DateTime(date("Y-m-d", strtotime($from)));
			$event_date->add(new DateInterval('P1D'));
			if ($event_date <= $now) {
				continue;
			}
			$event_obj = new Event($event->id, $event->summary, $from, $to, $event->location, $this->model->get_booking_count($event->id), $this->model->is_event_closed($event->id));
			$events[$from . " " . $event->id] = $event_obj;
		}
		ksort($events);
		return $events;
	}


	/**
	 * Get event
	 * @return Event
	 */
	private function get_event($event_id = null)
	{
		if (!isset($this->event_id) && ($event_id == null)) {
			die("Kein Event gefunden. Bitte Event ID überprüfen.");
		}
		if ($event_id == null) {
			$event_id = $this->event_id;
		}
		$event = $this->load_specific_event($event_id);
		if (!isset($event->summary)) {
			die("Kein Event gefunden. Bitte Event ID überprüfen.");
		}
		if ($event->visibility == "private") {
			die("Privates Event!");
		}
		if (isset($event->start->dateTime)) {
			$from = $event->start->dateTime;
			$to = $event->end->dateTime;
		} else {
			$from = $event->start->date;
			$to = $event->end->date;
		}
		return new Event($event->id, $event->summary, $from, $to, $event->location, $this->model->get_booking_count($event->id), $this->model->is_event_closed($event->id));
	}

	/**
	 * Load eventlist from Google calendar
	 */
	private function load_events()
	{
		$json_url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events?key=" . ACCESS_TOKEN;
		$json = file_get_contents($json_url);
		return json_decode($json);
	}

	/**
	 * Load specific event from Google calendar
	 */
	private function load_specific_event($event_id)
	{
		$json_url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events/" . $event_id . "?key=" . ACCESS_TOKEN;
		$json = file_get_contents($json_url);
		return json_decode($json);
	}

	/**
	 * Get specific poll and results from db
	 */
	private function get_poll($poll_id)
	{
		$poll = $this->model->get_poll($poll_id);
		$poll_results = $this->model->get_poll_results($poll_id);
		return new Poll($poll['id'], $poll['name'], $poll['description'], $poll['options'], $poll['multichoice'], $poll['active'], $poll['create_date'], $poll_results);
	}

	/**
	 * Get array of polls
	 * @return Array of polls
	 */
	private function get_poll_list($active = 1)
	{
		$polls = $this->model->get_polls($active);
		$results = array();
		foreach ($polls as $poll) {
			$poll_results = $this->model->get_poll_results($poll['id']);
			$results[] = new Poll($poll['id'], $poll['name'], $poll['description'], $poll['options'], $poll['multichoice'], $poll['active'], $poll['create_date'], $poll_results);
		}
		return $results;
	}

	/**
	 * Set user cookies to load again when returning
	 */
	private function set_booking_cookies()
	{
		setcookie("booking_name", $this->request['name'], time() + 3600 * 24 * 365 * 5);
		setcookie("booking_givenname", $this->request['givenname'], time() + 3600 * 24 * 365 * 5);
		setcookie("booking_email", $this->request['email'], time() + 3600 * 24 * 365 * 5);
	}

	/**
	 * Set cookie
	 */
	private function set_cookie($name, $value)
	{
		setcookie($name, $value, time() + 3600 * 24 * 365 * 5);
	}

	/**
	 * Interrrupt booking if closed
	 */
	private function check_booking_closed()
	{
		$now = new DateTime('NOW');
		$dt = new DateTime($this->request['from']);
		$hour_str = '-' . HOURS_TO_EVENT_TO_CLOSE_BOOKING  . ' hours';
		if ($now >= $dt->modify($hour_str)) {
			die("Buchungen geschlossen.");
		}
	}

	/**
	 * Send mail after successful booking in db
	 */
	private function send_booking_success_mail()
	{
		$event = $this->get_event();
		$props = array(
			'location' => $event->location,
			'dtstart' => $event->from,
			'dtend' => $event->to,
			'summary' => $event->name,
		);
		$ics = new ICS($props);
		$ical = $ics->to_string();

		if (isset($this->request['email']) && $this->request['email'] != "") {
			$mail = new PHPMailer(true);
			try {
				$mail->isSMTP();
				$mail->Host       = SMTP_SERVER;
				$mail->SMTPAuth   = SMTP_AUTH;
				$mail->Username   = SMTP_USER;
				$mail->Password   = SMTP_PASSWORD;
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				$mail->Port       = SMTP_PORT;

				//Recipients
				$mail->setFrom('webmaster@ingolstadt-chapter.de', 'H.O.G. Ingolstadt Chapter');
				$mail->addAddress($this->request['email']);

				//Attachments
				if (!empty($ical)) {
					$mail->addStringAttachment($ical, 'ical.ics', 'base64', 'text/calendar');
				}

				//Content
				$mail->isHTML(false);
				$mail->Subject = "Event " . $this->request['eventname'] . " erfolgreich gebucht";
				$mail->Body    = $this->request['mailtext'];

				$mail->send();
				return true;
			} catch (Exception $e) {
				die("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
			}
		}
	}
}
