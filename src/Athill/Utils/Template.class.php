<?php namespace Athill\Utils;

class Template {
	private $template;
	private $templateText = "default";
	private $home;
	public $menu;
	private $includes = array();
	
	
	public function __construct($menu, $templateText="default") {
		global $site,$h;
		$incroot = $site['incroot'];
		$this->templateText = $templateText;
		if (strtolower($templateText) != 'none') {
			include_once($site['fileroot']."/inc/templates/".$this->templateText.".class.php");
			$this->templateText .= 'Template';
			$this->template = new $this->templateText($this);
			$this->menu = $menu;
		}
		if (count($site['authorized']) == 0){
			//// Using /conf/admin/config.json
			$contents = file_get_contents($site['fileroot'].'/conf/admin/config.json');
//			var_dump($getContents);
			$conf = json_decode($contents, true);
//			var_dump($conf);
			$webdir = str_replace($site['webroot'], '', dirname($_SERVER['SCRIPT_NAME']));
			$webdir = preg_replace('/\/admin$/', '', $webdir);
			if (isset($conf[$webdir])){
				$site['authorized'] = $conf[$webdir]['users'];
			}
		}
		if (count($site['authorized']) > 0){
			$site['hasLoginLink'] = true;
			$ref = dirname($_SERVER['SCRIPT_NAME']);
			if (preg_match('/\/admin$/', $ref)){
				$site['restricted'] = true;
			}
		}
	}
	
	public function head() {
	  global $h, $pageTitle, $site;
	  if (strtolower($this->templateText) == 'none') return;
	  ////Add scripts/sheets from template
	  $scripts = explode(",", $this->template->scripts);
	  $sheets = explode(",", $this->template->stylesheets);
	  $this->includes = array_merge($this->includes, $scripts, $sheets);

	  
	  $jsMods = $site['jsModule'];
	  foreach ($site['jsModules'] as $module => $bool) {
		  if ($bool) {
				$mod = $jsMods->modules[$module];
				$this->includes = array_merge($this->includes, $mod['scripts'], $mod['styles']);
		  }
	  }
	  
 	  ////Add scripts/sheets from $GLOBALS
	  if (!is_array($site['scripts'])) $site['scripts'] = array($site['scripts']);
	  if (!is_array($site['stylesheets'])) $site['stylesheets'] = array($site['stylesheets']);
	  $this->includes = array_merge($this->includes, $site['scripts'], $site['stylesheets']);
	  $this->includes[] = '/global/js/utils.js';
	  
	  ////HTML/head
	  $h->ohtml($site["title"], $this->includes, $site['meta']);
//	  echo 'herre`1';
	  //$this->jsCheckMobile();
	  if (array_key_exists('editor', $site['jsModules']) && $site['jsModules']['editor']) {
	  	$h->script('utils.editorManager.editor("tinymce");');
	  }
	  $h->script("(function() {
			var cx = '006133819605845577508:um3mzfomaog';
    		var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
	    	gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        	'//www.google.com/cse/cse.js?cx=' + cx;
    		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  		})();");
	  if (array_key_exists('headerExtra', $site)) {
		$h->tnl($site['headerExtra']);  
	  }
	  $h->body($this->template->bodyAtts);
	  if ($site['hasLoginLink']) {
	  	$this->loginLink();
	  }	  
	  $this->banners();		
	}
	
	function jsCheckMobile() {
		global $h,$site;
		$h->oscript();
		if ($site['isMobile']) $h->tnl('var isMobile=true;');
		else $h->tnl('var isMobile=false;');
		if ($site['isTablet']) $h->tnl('var isTablet=true;');
		else $h->tnl('var isTablet=false;');		
		$h->cscript();	
	}
	
	/***********************************
	 * If not authenticated, show link to authenticate
	 ***********************************/
	public function loginLink() {
		global $h,$site;
		
		if (!array_key_exists("user", $_SESSION)) {
			
			$cas = new CAS2();	
			if (isset($_GET['casticket']) || isset($_SERVER['REMOTE_USER'])) {
				if (isset($_GET['casticket'])) {
					$cas->validate();
				} else {
					$_SESSION['user'] = $_SERVER['REMOTE_USER'];
				}
				$_SESSION['adsgroups'] = $site['ads']->usersGroups($_SESSION['user']);
				$_SESSION['userdata'] = $site['ads']->getUserData($_SESSION['user']);
				$site['security'] = new Security();	
			} else {
				$cas->setTempVars();
				$h->startBuffer();
				$h->tnl("Sign In ");
				$h->img("/global/img/LogOff_16x16.png", "Login");			
				$display = $h->endBuffer();
				$uri = $cas->loginurl;
				$h->a($uri, $display, 'id="login"');  
			}
		} 
	}
	
	
	////For accessibility
	public function skipNav() {
	  global $h;
	  $h->odiv('id="skip"');
	  $h->tag("p", '', "Skip to:", true);
	  $links = array(
	  	array("display" => "Content"),
		array("display" => "Search"),
		array("display" => "Primary Navigation"),
		array("display" => "Secondary Navigation"),
		array("display" => "Mini Index")
	  );
	  for ($i = 0; $i < count($links); $i++) $links[$i]['href'] = "#skip".($i+1);
	  $h->linkList($links);
	  $h->cdiv();		
	}
	////Website header
	public function heading() {
		global $site, $h ;
		if (strtolower($this->templateText) == 'none') return;
		//// show desired template
		$this->template->heading();
		///// show any flash messages
		if (array_key_exists('message', $_SESSION)) {
			$message = (is_array($_SESSION['message'])) ?
				$h->rtn('liArray', array('ul', $_SESSION['message'])) :
				$_SESSION['message'];	
			unset($_SESSION['message']);
			$h->div($message, 'class="alert" id="messages"');
		}
		//// is user allowed to see this page
		$cli = php_sapi_name() == 'cli';
		$restricted = $site['restricted'];	
		$authenticated = array_key_exists('user', $_SESSION);
		$authorized = count($site['authorized']) == 0 || ($authenticated 
			&& ($site['security']->isAuthorized($site['authorized'])));
		if (!$cli && $restricted && (!$authenticated || !$authorized)) {
			//// error
			$h->div('You either need to log in or do not have access to administer these reports', 'class="error"');
			$h->br();
			$this->footer();
			exit();	
		}		
	}
	
	public function displaySearch() {
		global $h, $site;
		$base = ($site['isTST']) ? 
			'http://webtest.iu.edu/~uirr/searchresults.php' : 
			'http://www.iu.edu/~uirr/searchresults.php';

		$h->tnl('<gcse:searchbox-only resultsUrl="'.$base.'"></gcse:searchbox-only>');
	}	
	
	////Left side bar
	public function leftSideBar() {
		global $h, $site;
		$leftSideBar = $site['leftSideBar'];
		$links = array();
		if ($site['leftSideBarContent'] != "") {
			$h->tnl($site['leftSideBarContent']);
			return;	
		}
		switch ($site['leftSideBar']) {
			case "":
			case "none":
				break;
			case "about":
				$h->h(3, "Secondary Navigation", 'id="skip4"');
				$h->odiv('id="nav_vertical" class="subnav"');
				$pre = '/about/';
				$children = $this->menu->getChildren($pre);
				$links = array();
				foreach ($children as $node) {
					$atts = '';
					$href = (string)$pre.$node['href'];
					if (stripos($_SERVER['SCRIPT_NAME'], $href)) $atts = 'class="active"';
					$links[] = array('href' => $href, 'display' => $node['display'], 'liAtts' => $atts);
				}
				$h->linkList($links);
				$h->cdiv();	////close nav_vertical
				break;	
			case "reports":
				$h->h(3, "Secondary Navigation", 'id="skip4"');
				$h->odiv('id="nav_vertical" class="subnav"');
				include_once($site['fileroot']."/wwws/ssi/_nav-vertical.shtml");
				$h->cdiv();	////close nav_vertical
				break;
			case "pnc":
				$h->h(3, "Secondary Navigation", 'id="skip4"');
				$h->odiv('id="nav_vertical" class="subnav"');
				include_once($site['fileroot']."/wwws/reports/census/inc/sidemenu.inc.php");
				$h->cdiv();	////close nav_vertical
				break;
			case 'featuredreports':
				include_once($site['fileroot']."/wwws/ssi/featuredreports.inc.php");
				break;				
			default: 
				die("Bad sideNav type in Template.class.php");
		}
				
	}
	
	public function rightSideBar($class='box_wrap'){
		global $h,$site;
		$h->odiv('id="column3"');
			if ($site["rightSideBar"] != "") {
				$atts = '';
				if ($class != '') {
					$atts = 'class="'.$class.'"';
				}
				$h->odiv($atts);
				
				$column3 = (is_array($site["rightSideBar"])) ? 
					$site["rightSideBar"] : 
					explode(",", $site["rightSideBar"]);
				for ($i = 0; $i < count($column3); $i++) {
					$h->odiv('class="box"');
					include($column3[$i]);
					echo "\n";	
					$h->cdiv();
				}
				$h->cdiv();
			}
			$h->cdiv();	
	}
	
	public function breadcrumbs() {
	  global $h, $site;
	  //$h->script('KW_breadcrumbs("UIRR Home","&raquo;",0,1,"index.php",4,5)');
	  $this->menu->displayPath();
	}
	
	public function footer() {
		if (strtolower($this->templateText) == 'none') return;
		$this->template->footer();	
	}
	
	
	function banners() {
		global $h,$site;
		////Identify instnace on DEV nad TST
		if ($site['isTST']) {
			$h->odiv('id="instanceId"');
			//instance = IIf(Request.isDev, DE("Development"), DE("Test"));
			$h->tnl("Alert: This is a non production environment (Test)");
			$h->cdiv(); 	////Close instanceId			
		}
		//// Determine admin access
		$authenticated = array_key_exists('user', $_SESSION);
		if ($authenticated) {
			$this->toolbar();
		}	
	}
	
	function toolbar(){
		global $h,$site;
		$h->odiv('id="toolbar"');
		//// Using $site['authorized']
		//var_dump($site['authorized']);
		$admin = count($site['authorized']) > 0 && 
			$site['security']->isAuthorized($site['authorized']);
		$dir = dirname($_SERVER['SCRIPT_FILENAME']);
		if ($admin) {
			if (preg_match('/admin$/', $dir)) {
				array_unshift($site['toolbar'], 
					$h->rtn('a', array('../', 'Home')),
					$h->rtn('a', array('./', 'Administration', 'class="active"'))
				);
			} else {
				$ls = scandir($dir);
				if (in_array('admin', $ls)) {
					array_unshift($site['toolbar'], 
						$h->rtn('a', array('./', 'Home', 'class="active"')),
						$h->rtn('a', array('admin/', 'Administration'))
					);
				}
			}
			
		}
		if (count($site['toolbar']) > 0) {
			foreach ($site['toolbar'] as $item) {
				$h->div($item, 'class="toolbar-item"');
			}
		} else {
			$h->tnl("&nbsp;");
		}
		//// right toolbar
		$h->odiv('id="toolbar-right"');
		//// show authenticated user
		$message = 'Logged in as '.$_SESSION['user'];
		if($_SESSION['userdata']['name'] != ''){
			$message .= ' ('.$_SESSION['userdata']['name'].')'; 
		}
		$h->div($message, 'class="toolbar-item"');
		$h->cdiv('/#toolbar-right');
		$h->cdiv('/#toolbar'); 			
	}
}
?>