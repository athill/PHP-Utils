<?php  namespace Athill\Utils;
/*
Copyright 2012 andy hill

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

 */

class Xml {

	private $tabs = 0;
	private $buffer = array();
	protected $bufferIndex = -1;
	private $output = true;
	public $strictIndent = false;


 	/**
	 * constructor
	 */ 
 	function __construct() {
 		$this->tabs       = 0;
 		$this->buffer      = array();
 		$this->bufferIndex = -1;
		$this->output      = true;
		$this->strictIndent = true;
 	}		

	public function __call( $name, $arguments ) {
		$type = $name;
		if (substr($type, 0, 1) == 'o') {
			$type = substr($name, 1);
			$atts = (count($arguments) > 0) ? $arguments[0] : '';
			$this->otag($type, $atts);
		} else if (substr($name, 0, 1) == 'c') {
			$type = substr($name, 1);
			$comments = (count($arguments) > 0) ? $arguments[0] : '';
			$this->ctag($type, $comments);
		} else {
			$content = (count($arguments) > 0) ? $arguments[0] : '';
			$atts = (count($arguments) > 1) ? $arguments[1] : '';
			$this->tag($type, $atts, $content);
		}
	}


	/**
	 * Start buffering results of public void functions rather than outputting
	 */
	public function startBuffer() {
		array_push($this->buffer, "");
		$this->bufferIndex++;
		$this->output = false;
	}

	/**
	 * Stop buffering results of funcitons, return buffer
	 * @return	string	buffered HTML
	 */
	public function endBuffer() {
		$buf = "";
		if ($this->bufferIndex > -1) {
			$buf = array_pop($this->buffer);
			$this->bufferIndex--;
		}
		if ($this->bufferIndex == -1) $this->output = true;
		return $buf;
	}

	public function sb() {
		$this->startBuffer();
	}

	public function eb() {
		return $this->endBuffer();
	}

	/**
	 * Returns value of XML tag, rather than outputting it, e.g., h.rtn('a', {href='index.cfm', display='Home'})
	 */	
	public function rtn($methodName, $args) {
		if ($methodName == 'rtn') throw 'Recursion fail in rtn() in Xml';
		//else if ()
		////review later
	}


	/*
	 * Add space befoer attributes and/or convert attribute struct to string
	 */
	public function fixAtts($atts) {
		if (!is_array($atts)) {
			if ($atts != '' && substr($atts, 0, 1) != ' ') return ' '.$atts;
			else return $atts;
		} else {
			$str = '';
			foreach ($atts as $k => $v) {
				$str .= ' '.strtolower($k).'="'.$v.'"';
			}
			return $str;
		}
	}

	/**
	 * Base output public void function. Outputs to screen or adds to buffer, depending on whether in buffer
	 */
	public function wo($str) {
		if ($this->output) echo $str;
		else $this->buffer[$this->bufferIndex] .= $str;
	}

	/**
	 * Output str followed by a newline
	 */
	public function wonl($str) {
		$this->wo($str . chr(10));
	}

	/**
	 * Output str followed by &lt;br /&gt; and newline
	 */	
	public function wobr($str) {
		$this->wonl($str.'<br />');
	}

	/**
	 * Outputs appropriate number of tabs based on current value of tabs
	 */
	public function tab() {
		if ($this->tabs < 0) $this->tabs == 0;
		$this->wo(str_repeat(chr(9), $this->tabs));
	}

	/**
	 * Outputs tab(s),then str, then newline
	 */
	public function tnl($str) {
		$this->wonl($this->tab() . $str);
	}

	/**
	 * Outputs tab, str, &lt;br /&gt;, and newline
	 */
	public function tbr($str) {
		$this->wobr($this->tab() . $str);
	}

	/**
	 * Simple XML tag
	 */
	public function tag($type, $atts='', $content='', $inline=false, $empty=true) {
		$atts = $this->fixAtts($atts);
		if ($content == '') {
			if ($empty) $this->tnl('<'.$type.$atts.'/>');
			else $this->tnl('<'.$type.$atts.'></'.$type.'>');
		} else {
			if ($inline) {
				$this->tnl('<'.$type.$atts.'>'.$content.'</'.$type.'>');
			} else {
				$this->otag($type, $atts);
				$this->tnl($content);
				$this->ctag($type);
			}
		}
	}

	/**
	 * Opens an XML Tag
	 */
	public function otag($type, $atts='', $indent=false) {
		$atts = $this->fixAtts($atts);
		$this->tnl('<'.$type.$atts.'>');
		if ($indent) $this->tabs++;
	}

	/**
	 * Closes an XML tag
	 */
	public function ctag($type, $indent=false, $comment='') {
		if ($indent) $this->tabs--;
		$str = '</'.$type.'>';
		if ($comment != '') $str .= ' <!-- '.$comment.' -->';
		$this->tnl($str);
	}
}
?>