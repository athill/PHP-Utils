<?php namespace Athill\Utils\Templates;
//namespace templates;
class DefaultTemplate extends \Athill\Utils\TemplateBase {

	protected function beginRender() {
		global $h;
		$h->odiv('class="container" id="container"');
	}

	protected function endRender() {
		global $h;
		$h->cdiv('/#container');
	}

	protected function heading() {
		global $h, $site;
		$h->oheader('id="header"');
		$h->odiv('class="banner"');
		$h->h1($site['sitename']);
		$h->cdiv('/.banner');
		$h->cheader();
	}

	protected function beginLayout() {
		global $site, $h;
		$h->odiv('class="container-fluid"');
		$h->odiv('class="grid"');
		$h->odiv('id="layout" class="row"');
		$leftsidebar = $site['layout']['leftsidebar'];
		$rightsidebar = $site['layout']['rightsidebar'];
		$contentcols = 12;
		if (count($leftsidebar) > 0) {
			$h->odiv('class="col-md-2"');
			$contentcols -= 2;
			$this->sidebar('left-sidebar', $leftsidebar);
			$h->cdiv();
		}
		if (count($rightsidebar) > 0) {
			$contentcols -= 2;
		}
		$h->odiv('id="content" class="col-md-'.$contentcols.'"');
	}

	protected function endLayout() {
		global $site, $h;
		$rightsidebar = $site['layout']['rightsidebar'];
		$h->cdiv('/#content');
		if (count($rightsidebar) > 0) {
			$h->odiv('class="col-md-2"');
			$this->sidebar('right-sidebar', $rightsidebar);
			$h->cdiv();
		}
		$h->cdiv('#layout');
		$h->cdiv('/.grid');
		$h->cdiv('/.container-fluid');
	}

	protected function footer() {
		global $h, $site;
		$h->ofooter();
		$h->tnl('&copy; '. $site['meta']['copyright']);
		$h->cfooter();
	}

/*
	protected $bodyAtts = 'id="default" class="default"';	
	public $stylesheets = "/global/css/import.css,//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css";
	public $scripts = "/global/js/jquery-1.9.1.min.js,/global/js/nav.js,/global/js/zebrarows.js";
	protected $base;
	
	public function __construct($base) {
		global $site;
		$this->base = $base;	
		$migrate = ($site['isPRD']) ? 'jquery-migrate-1.2.1.min.js' : 'jquery-migrate-1.2.1.js';
		$this->scripts .= ',/global/js/'.$migrate;			
	}
	
	public function heading() {
	  global $h, $site;
	  $pageTitle = $site['pageTitle'];
	  ////Hidden navigation for impaired
	  $this->base->skipNav();
	  ////Page structure
	  $h->odiv('id="wrapper"');
//	  if ($site['hasLoginLink']) {
//	  	$this->base->loginLink();
//	  }
	  $h->odiv('id="container"');
	  //////////////////
	  ////Header/////////////
	  ////////////////
	  $h->odiv('id="header"');
	  if ($site['hasIuLogo']) {
	  	$this->iuLogo();
	  }
	  ////Search form
	  if ($site['hasSearch']) {
	  	$this->base->displaySearch();
	  }
	  ////site title
	  $this->siteTitle();
	  $h->cdiv();	////Close header div
	  ////Top Nav
	  if ($site['hasTopNav']) {
	  	$this->displayTopNav();
	  }
	  if ($site['subTopNavContent'] != '') {
	  	$h->div($site['subTopNavContent'], 'id="subTopNavContent"');
	  }
	  if ($site['hasBreadcrumbs'] && $site['breadcrumbsBeforeSubmenu']) {
	  	$this->breadcrumbs();
	  }	  
	  	  ////submenus
	  if (array_key_exists('submenu', $site)) {
		include_once("/ip/uirr/inc/Submenu.class.php");
		$sm = new Submenu($site['submenu']);  
	  }
	  if (array_key_exists('subsubmenu', $site)) {
		include_once("/ip/uirr/inc/Submenu.class.php");
		$sm = new Submenu($site['subsubmenu'], "subsubmenu"); 
		$h->br(); 
	  }	

	  ////Side Nav
	  if ($site["leftSideBar"] != "none" || $site["leftSideBarContent"] != "") {
		  $this->displaySideNav();
	  } 
	  ////Content
	  if ($site["leftSideBar"] == "none" && $site["leftSideBarContent"] == "" && $site["rightSideBar"] == "none") {
		  $h->odiv('id="column123"');
	  }
	  else if ($site["rightSideBar"] == "none") $h->odiv('id="column23"');
	  else $h->odiv('id="column2"');
  
	  $h->name("skip1");
	  $h->odiv('id="content"');
	  ////breadcrumbs
	  if ($site['hasBreadcrumbs'] && !$site['breadcrumbsBeforeSubmenu']) {
	  	$this->breadcrumbs();
	  }
	  if ($site["useImageHeader"]) {
		  $h->odiv('id="image4_hd_content"');
	  }
	  if ($site['hasPageTitle'] && $site["pageTitle"] != '') {
	  	$h->h(3, $site["pageTitle"]);
	  }
	  if ($site["useImageHeader"]) {
		  $h->cdiv();
	  }
	  if ($site['isPRD']) {
		$h->script("
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		  
		  ga('create', 'UA-44596600-1', 'iu.edu');
		  ga('send', 'pageview');	  
		");
	  }
	}
	
	protected function iuLogo() {
	  global $h;
	  $h->startBuffer();
	  $h->img("/global/img/iulogo.gif", "Indiana University - University Student Services and Systems", 'width="273" height="42"');
	  $img = trim($h->endBuffer());	
	  $h->startBuffer();
	  $h->a("http://www.iu.edu/", $img);
	  $h->h(1, trim($h->endBuffer()));	  	
	}
	
	protected function siteTitle() {
	  global $h;
	  $h->startBuffer();
	  $h->a("/~uirr/", "University Institutional Research and Reporting");
	  $h->h(2, trim($h->endBuffer()), 'id="sitetitle"');		
	}

	
	private function displayTopNav() {
		
		global $h;
		$h->odiv('id="nav_horizontal"');
		$h->h(3, "Primary Navigation", 'id="skip3"');
		$this->base->menu->topNav();
		$h->cdiv();
		
	}
	
	protected function displaySideNav() {
		global $h;
		$h->odiv('id="column1"');
		$this->base->leftSideBar();
		$h->cdiv();
		////close column1	
		
	}
	
	public function breadcrumbs() {
	  global $h;
	  $h->odiv('id="breadcrumb"');
	  $this->base->breadcrumbs();
	  $h->cdiv();		
	}
	
	public function footer() {
		global $h, $site;
		$h->cdiv();	////close content
		$h->cdiv(); ////close column2
		if ($site["rightSideBar"] != "none") {
			$this->base->rightSideBar();
		}
		if ($site['hasMiniIndex']) {
			$h->odiv('id="mini-index" class="four"');
			$h->h3('Mini Index', 'id="skip5"');
			include($site['fileroot'].'/wwws/ssi/_mini-index-4.shtml');
			$h->cdiv('/#mini-index');		
		}
		$h->cdiv('/#container'); ////close container
		$h->cdiv();	////close wrapper
		$h->odiv('id="footer"');
		$h->hr();
		$h->otag("p");
		$h->startBuffer();
		$h->img("/global/img/footer/blockiu.gif", "Block IU", 'width="22" height="28"');
		$h->a("http://www.iu.edu/", trim($h->endBuffer()), 'title="Indiana University" id="blockiu"');
		$h->tnl(" ");
		$h->a("http://www.iu.edu/comments/copyright.shtml", "Copyright");
		$h->tnl(" &copy; ".date("Y")." The Trustees of ");
		$h->a("http://www.iu.edu/", "Indiana University");
		$h->tnl("  &#124; ");
		$h->a("http://www.iu.edu/comments/complaint.shtml", "Copyright Complaints");
		$h->tnl(" | ");
		$h->a('https://usss.iu.edu/Pages/University-Student-Services-and-Systems-Privacy-Notice.aspx', "Privacy Notice", 'target="_blank"');
		$h->br();
		////Maintain
		$h->otag('span', 'class="maintain"');
		$h->tnl("Site Developed and Maintained by ");
		$h->a("https://usss.iu.edu/sites/ts/default.aspx", "University Student Services and Systems Technology Services", 'target="_blank"');
		$h->ctag('span');	////close maintain		
		$h->ctag("p");
		$links = array(
			array('href' => "/site/", 'display' => "Site Index"),
			array('href' => "/about/request.php", 'display' => "Contact Us"),
			array('href' => "https://usss.iu.edu/Pages/ContactUs.aspx", 'display'=>"Technical Support"),
			array('href' => "/find/", 'display' => "Find People")
		);
		$h->linkList($links);
		$h->cdiv();
		
		$h->chtml();			
	}*/
}
