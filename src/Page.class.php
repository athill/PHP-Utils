<?php  namespace Athill\Utils;

class Page {
	public $template;

	function __construct($options=array()) {
		global $h, $site, $directory;

		// $site = $h->extend($site, $options);	//// TODO: 	why this don't work?
		////Local Settings override global and directory
		if (isset($options['jsModules'])) {
			$options['jsModules'] = array_merge($site['jsModules'], $options['jsModules']) or die("^^^!!");
		}
		if (isset($options)) $site = array_merge($site, $options) or die("^^^");		
		////Template
		//include_once($site['incroot']."/site/Template.class.php");
		// echo $site['template'];
		$this->template = new Template($site['menu'], $site["template"]);		
		$this->template->head();
		$this->template->heading();
	}

	public function end() {
		$this->template->footer();
	}
}
?>