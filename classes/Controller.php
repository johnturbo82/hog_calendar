<?php
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
		$this->template = !empty($request['view']) ? trim($request['view'], '/') : 'booking_table';
		$this->id = !empty($request['id']) ? trim($request['id'], '/') : null;
	}

	/**
	 * Show content depeding on GET view
	 * @return String application content
	 */
	public function display()
	{
		$view = new View();
		switch ($this->template) {
			case 'booking_table':
				$view->assign('event_list', $this->get_event_list());
				break;
			case 'book':
				$view->assign('event', $this->get_event());
				break;
			case 'manage':
				$view->assign('event_list', $this->get_event_list());
				break;
			default:
				$this->template = "404";
				break;
		}
		$view->setTemplate($this->template);
		$this->view->setTemplate('site');
		$this->view->assign('content', $view->loadTemplate());
		return $this->view->loadTemplate();
	}

	/**
	 * Get array of events
	 * @return Array of events
	 */
	public function get_event_list()
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
			$event_obj = new Event($event->id, $event->summary, $from, $to, $event->location, $this->model->get_booking_count($event->id));
			$events[$from . " " . $event->id] = $event_obj;
		}
		ksort($events);
		return $events;
	}


	/**
	 * Get event
	 * @return Event
	 */
	public function get_event()
	{
		if (!isset($this->id)) {
			die("Kein Event gefunden. Bitte Event ID überprüfen.");
		}
		$event = $this->load_specific_event();
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
		return new Event($event->id, $event->summary, $from, $to, $event->location, $this->model->get_booking_count($event->id));
	}

	/**
	 * Load eventlist from Google calendar
	 */
	public function load_events()
	{
		$json_url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events?key=" . ACCESS_TOKEN;
		$json = file_get_contents($json_url);
		return json_decode($json);
	}

	/**
	 * Load specific event from Google calendar
	 */
	public function load_specific_event()
	{
		$json_url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events/" . $this->id . "?key=" . ACCESS_TOKEN;
		$json = file_get_contents($json_url);
		return json_decode($json);
	}
}
