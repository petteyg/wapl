<?php
class WaplHelper extends AppHelper {

	var $tags = array(
		'cell' => "<cell>%s</cell>\n",
		'chars' => "<chars%s>\n<value>%s</value>\n</chars>\n",
		'css' => "<css>\n%s</css>\n",
		'easyChars' => "<easyChars>\n<value>%s</value>\n</easyChars>\n",
		'externalImage' => "<externalImage%s>%s</externalImage>",
		'head' => "<head>\n%s</head>\n",
		'item' => "[*]%s[/*]\n",
		'layout' => "<layout>\n%s</layout>\n",
		'list' => "[list]\n%s[/list]\n",
		'row' => "<row>\n%s\n</row>\n",
		'span' => "[span=%s]%s[/span]",
		'title' => "<title>%s</title>\n",
		'url' => "<url>%s</url>",
		'words' => "<wordsChunk>\n<display_as>%s</display_as>\n<quick_text>%s</quick_text>\n</wordsChunk>\n",
	);

	var $devKey;
	var $test = false;

	function __construct($settings) {
		$this->devKey = $settings['devKey'];
		$this->test = $settings['test'];
	}

	function _parseAttributes($data) {
		$attributes = '';
		foreach($data as $name => $value) {
			$attributes .= ' '.$name.'="'.$value.'"';
		}
		return $attributes;
	}

	function _parseItems($data) {
		$items = '';
		if (is_array($data)) {
			foreach($data as $item) {
				$items .= sprintf($this->tags['item'], $item);
			}
		} else {
			$items .= sprintf($this->tags['item'], $data)."\n";
		}
		return $items;
	}

	function _parseUrls($data) {
		$urls = '';
		if (is_array($data)) {
			foreach($data as $url) {
				$urls .= sprintf($this->tags['url'], $url)."\n";
			}
		} else {
			$urls .= sprintf($this->tags['url'], $data)."\n";
		}
		return $urls;
	}

	function chars($data, $options = array()) {
		return $this->output(sprintf($this->tags['chars'], $this->_parseAttributes($options), $data));
	}

	function css($data) {
		return $this->output(sprintf($this->tags['css'], $this->_parseUrls($data)));
	}

	function easyChars($data) {
		return $this->output(sprintf($this->tags['easyChars'], $data));
	}

	function externalImage($data, $options = array()) {
		return $this->output(sprintf($this->tags['externalImage'], $this->_parseAttributes($options), sprintf($this->tags['url'], $data)));
	}

	function head($data) {
		return $this->output(sprintf($this->tags['head'], $data));
	}

	function layout($data) {
		return $this->output(sprintf($this->tags['layout'], $data));
	}

	function ul($data) {
		return $this->output(sprintf($this->tags['list'], $this->_parseItems($data)));
	}

	function span($data) {
		return $this->output(sprintf($this->tags['span'], $data));
	}

	function title($data) {
		return $this->output(sprintf($this->tags['title'], $data));
	}

	function wapl($data) {
		return $this->output(sprintf('<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'."\n".'<wapl xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://wapl.wapple.net/wapl.xsd">'."\n".'%s</wapl>'."\n", $data));
	}

	function waplend() {
		return $this->output('</wapl>');
	}

	function waplstart() {
		$begin = '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>';
		$begin .= '<wapl xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://wapl.wapple.net/wapl.xsd">';
		return $this->output($begin);
	}

	function words($data, $type = 0) {
		switch($type) {
			case 1:
				$type = 'h1';
				break;
			case 2:
				$type = 'h2';
				break;
			case 3:
				$type = 'h3';
				break;
			case 4:
				$type = 'h4';
				break;
			case 5:
				$type = 'h5';
				break;
			case 6:
				$type = 'h6';
				break;
			default:
				$type = 'p';
				break;
		}
		return $this->output(sprintf($this->tags['words'], $type, $data));
	}

}
?>