<?php
class WaplComponent extends Object {

	var $components = array('RequestHandler', 'Session');
	var $devKey;
	var $force = false;
	var $path = true;
	var $test = false;

	function initialize(&$controller, $settings = array()) {
		$this->controller =& $controller;
		$this->devKey = $settings['devKey'];
		if (array_key_exists('force', $settings)) {
			$this->force = $settings['force'];
		}
		if (array_key_exists('path', $settings)) {
			$this->path = $settings['path'];
		}
		if (array_key_exists('test', $settings)) {
			$this->test = $settings['test'];
		}
	}

	function startup(&$controller) {
		if (!$this->Session->check('User.isMobile') || $this->force) {
			$sClient = @new SoapClient('http://webservices.wapple.net/wapl.wsdl');
			if($sClient) {
				$headers = array();
				foreach($_SERVER as $k => $v) {
					$headers[] = array('name' => $k, 'value' => $v);
				}
				$params = array(
					'devKey' => $this->devKey,
					'deviceHeaders' => $headers
				);
			}
			$isMobile = false;
			if($sClient->isMobileDevice($params)) {
				$isMobile = true;
			}
			if (!$this->force) {
				$this->Session->write('User.isMobile', $isMobile);
			}
		}
		if ($this->Session->read('User.isMobile') || $this->test) {
			$this->RequestHandler->respondAs('xml');
			if ($this->path) {
				$this->controller->layoutPath .= 'wapl';
				$this->controller->viewPath .= DS.'wapl';
			} else {
				$this->controller->ext = '.wapl';
			}
		}
	}

	function beforeRender(&$controller) {
		if ($this->Session->read('User.isMobile') || $this->test) {
			$helpers = array_diff($this->controller->helpers, array('Wapl.Wapl'));
			$helpers['Wapl.Wapl'] = array('devKey' => $this->devKey, 'test' => $this->test);
			$this->controller->helpers = $helpers;
		}
	}

}
?>