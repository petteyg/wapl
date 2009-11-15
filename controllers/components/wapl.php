<?php
class WaplComponent extends Object {

	var $components = array('RequestHandler', 'Session');
	var $devKey;
	var $settings = array('force' => false, 'path' => true, 'test' => false);
	var $Wapl;
	var $params;
	var $url = 'http://webservices.wapl/net/';

	function initialize(&$controller, $settings = array()) {
		$this->controller =& $controller;
		$this->devKey = $settings['devKey'];
		$this->settings = array_merge($this->settings, $settings);
	}

	function startup(&$controller) {
		if (!$this->Session->check('User.isMobile') || $this->settings['force']) {
			$headers = array();
			foreach($_SERVER as $k => $v) {
				$headers[] = array('name' => $k, 'value' => $v);
			}
			$this->params = array(
				'devKey' => $this->devKey,
				'deviceHeaders' => $headers
			);
		}
		$isMobile = false;
		if (extension_loaded('soap')) {
			$this->Wapl = @new SoapClient($this->url.'wapl.wsdl');
			if ($this->Wapl->isMobileDevice($params)) {
				$isMobile = true;
			}
		} else {
			App::import('Core', 'HttpSocket');
			$this->Wapl = new HttpSocket();
			$result = $this->Wapl->post($this->url.'isMobileDevice.php', $params);
			if ($result) {
				$isMobile = true;
			}
		}
		if (!$this->settings['force'] {
			$this->Session->write('User.isMobile', $isMobile);
		}
		if ($this->Session->read('User.isMobile') || $this->settings['test']) {
			$this->RequestHandler->respondAs('xml');
			if ($this->settings['path']) {
				$controller->layoutPath .= 'wapl';
				$controller->viewPath .= DS.'wapl';
			} else {
				$controller->ext = '.wapl';
			}
		}
	}

	function beforeRender(&$controller) {
		if ($this->Session->read('User.isMobile') || $this->settings['test']) {
			$helpers = array_diff($this->controller->helpers, array('Wapl.Wapl'));
			$this->controller->helpers = $helpers;
		}
	}

	function shutdown(&$controller) {
		if ($this->settings['test'] != 'pre') {
			$this->params['wapl'] = urlencode($controller->output);
			if (extension_loaded('soap')) {
				$xml = simplexml_load_string($this->Wapl->getMarkupFromWapl($this->params));
			} else {
				$xml = simplexml_load_string($this->Wapl->post($this->url.'getMarkupFromWapl.php', $params));
			}
			foreach ($xml->header->item as $v) {
				header($v);
			}
			$controller->output = trim($xml->markup);
		}
	}

}
?>