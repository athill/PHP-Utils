<?php namespace Athill\Utils\Templates;

class BasicTemplate {


	public $bodyAtts = '';	
	public $stylesheets = "";
	public $scripts = "/global/js/jquery.min.js";
	private $base;
	
	public function __construct($base) {
		$this->base = $base;	
	}
	
	public function heading() {
	  global $h, $pageTitle;
	}
	private function displaySearch() {
	}
	
	private function displayTopNav() {
		
		global $h;
		$h->odiv('id="nav_horizontal"');
		$h->h(3, "Primary Navigation", 'id="skip3"');
		$this->base->menu->topNav();
		$h->cdiv();
		
	}
	
	private function displaySideNav() {
		global $h;
		$h->odiv('id="column1"');
		$h->h(3, "Secondary Navigation", 'id="skip4"');
		$h->odiv('id="nav_vertical" class="subnav"');
		$this->base->leftSideBar();
		$h->cdiv();	////close nav_vertical
		$h->cdiv();	////close column1	
		
	}
	
	public function breadcrumbs() {
	  global $h;
	  $h->odiv('id="breadcrumb"');
	  $this->base->breadcrumbs();
	  $h->cdiv();		
	}

	
	public function footer() {
		global $h;
		$h->chtml();			
	}
}

?>