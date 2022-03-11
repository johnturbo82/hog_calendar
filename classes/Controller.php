<?php
class Controller{

	private $request = null;
	private $template = '';
    private $view = null;

	/**
	 *
	 * @param Array $request Array from $_GET & $_POST.
	 */
	public function __construct($request){
		$this->request = $request;
        $this->view = new View();
		$this->template = !empty($request['view']) ? $request['view'] : 'index';
	}

	/**
	 * Show content
	 *
	 * @return String application content
	 */
	public function display(){
		$view = new View();
		switch($this->template){
			case 'booking_table':
			default:
				$view->setTemplate('booking_table');
				$view->assign('booking_table', 'test');
		}
		$this->view->setTemplate('site');
		$this->view->assign('content', $view->loadTemplate());
		return $this->view->loadTemplate();
	}
}
?>