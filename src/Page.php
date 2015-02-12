<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site, $directory;

		////Local Settings override global and directory
		if (isset($options['jsModules'])) {
			$options['jsModules'] = array_merge($site['jsModules'], $options['jsModules']) or die("^^^!!");
		}
		if (isset($options)) $site = array_merge($site, $options) or die("^^^");		
		////Template
		$this->template = new Template($site['menu'], $site["template"]);		
		$this->template->head();
		$this->template->heading();
	}

	public function end() {
		$this->template->footer();
	}
}
?>