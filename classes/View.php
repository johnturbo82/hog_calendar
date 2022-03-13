<?php
class View {

	private $path = 'templates';
	private $template = 'index';

	/**
	 * Vars for template
	 */
	private $_ = array();

	/**
	 * @param String $key Key
	 * @param String $value Value
	 */
	public function assign($key, $value){
		$this->_[$key] = $value;
	}


	/**
	 * @param String $template Name of template
	 */
	public function setTemplate($template = 'site'){
		$this->template = $template;
	}


	/**
	 * Load template file and return
	 *
	 * @param string $tpl Name of template file
	 * @return string Output of template
	 */
	public function loadTemplate(){
		$tpl = $this->template;
		$file = $this->path . DIRECTORY_SEPARATOR . $tpl . '.php';
		$exists = file_exists($file);

		if ($exists){
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
		else {
			// No template
			return 'could not find template';
		}
	}
}
?>