<?php  namespace Athill\Utils;
/**
 * XML menu interface
 *
 * 
 *
 * @package includes
 * @author andy hill 1 2009
 * @version 1.0
 *
 */


class Menujson {
	 	
 	/**
	 * Simple Xml Object
	 *
	 */  
 	public $json;
 	
 	
 	/**
	 * constructor
	 *
	 * create an instance of the Menu class
	 * pass in the persistence type(i.e. database, file, standard out)
	 * have a factory create the persistence object that log events are sent to
	 *
	 * @param string
	 */ 
 	function __construct($json) {
		$this->json = $json;	
 	}	
	

////TODO fix so works in intranet
	public function topNav($ulAtts='') {
		global $h, $site;
		$links = array();
//		print("in here");
//		print_r($this->xml);
		foreach ($this->json as $elem) {
			if (isset($elem['inmenu']) && $elem['inmenu'] == 'false') {
				continue;	
			}
			$display = (string)$elem['display'];
			$href = (string)$elem['href'];
			$atts = '';
			if (stripos($_SERVER['SCRIPT_NAME'], $href) > 0) {
				$atts = 'class="active"';
			}
			if (preg_match("/\[intranet\]/", $href)) {
				$href=preg_replace("/\[intranet\]/", "https://".$_SERVER['HTTP_HOST'].$site['webroot'], $href);
				
			} else if (strpos($href, '/') === 0) {
				$href = "https://".$_SERVER['HTTP_HOST'] . $site['webroot'] . $href;
			}
			

			$links[] = array("href" => $href, "display" => $display, "liAtts" => $atts);
		}
		$h->linkList($links, $ulAtts);
	}
	
	function menuList($script='default', $json='') {
		global $site, $h;
		if ($json == '') $json = $this->json;
		if ($script == 'default') {
			$script = dirname(str_replace($site['webroot'], '', $_SERVER['SCRIPT_NAME'])).'/';	
		}
		$children = $this->getChildren($script);
		$lis = array();
		foreach ($children as $child) {
			$lis[] = array('href'=>$child['href'], 'display'=>$child['display']);
		}
		$h->linkList($lis);		
	}
	

	function buildPathAndSetTitle($json, $path="", $depth=0, $nodes=array()) {
		global $h, $site;
		foreach ($json as $elem) {
			$node = preg_replace("/\//", "", $elem['href']);
			$comp = $site['script'][$depth];
//			$h->tbr($node.'|'.$comp);
			if ($node == $comp || $node == $comp.'?'.$_SERVER['QUERY_STRING']) {		
				$path .= $elem['href'];
				if (!preg_match("/^http/", $path)) {
					if (array_key_exists('secure', $elem)) {
						$path = "https://".$_SERVER['HTTP_HOST'].$path;	
					} else {
						$path = "http://".$_SERVER['HTTP_HOST'].$path;	
					}
				} else  {
					if (isset($elem['secure']) && $elem['secure'] == 'true' 
							&& preg_match("/^http:/", $path)) {
						$path = str_replace('http:', 'https:', $path);
					} else if ((!isset($elem['secure']) || $elem['secure'] == 'false') 
							&& preg_match("/^https:/", $path)) {
						$path = str_replace('https:', 'http:', $path);	
					}
				}
				$site['pageTitle'] = (string) $elem['display'];
				if ($depth + 1 == count($site['script'])) {
					$nodes[] = (string) $elem['display'];
					
					return $nodes;
				} else {
					$h->startBuffer();			
					
					$h->a($path, $elem['display'], '');	
					$nodes[] = $h->endBuffer();
					if(array_key_exists('children', $elem)) {
						return $this->buildPathAndSetTitle($elem['children'], $path, ++$depth, $nodes);
					} else {
						return $nodes;
					}
				}
				
			} else if ($depth + 1 == count($site['script'])) {
				//return $nodes;	
			}
			
		}
	}
	
	function parseData($options=array()) {
		global $h, $site;
		$defaults = array(
			'json' => $this->json,	////MenuXml to parse -- used in recursion
			'script' => str_replace('index.php', '', $_SERVER['PHP_SELF']),
			'path'=>$site['webroot'],	////Build the path -- used in recursion
			'depth'=>0,				////Current depth -- used in recursion
			'return'=> array(
						'breadcrumbs'=> array(
							array('href'=>'/', 'display'=>'UIRR Home')
						), ////seq of assoc: href,display
						'pageTitle'=>"",		////pagetitle
						'complete'=>false,		////full url matched
					)
		);
		$opts = $h->extend($defaults, $options);
		$isRoot = $opts['script'] == $site['webroot'].'/';
		////Main loop over children
		foreach ($opts['json']['children'] as $elem) {
			// print_r($elem);
			if (array_key_exists('redirect', $elem)) {
				continue;
			}
			//// root exception
			if ($elem['href'] == '/') {
				if ($isRoot) {
					$opts['return']['pageTitle'] = (string)$elem['display'];
					$opts['return']['complete'] = true;
					return $opts['return'];
				} else {
					continue;
				}
			}
			$compare = $opts['path'].$elem['href'];
			//// match
			if (strpos($opts['script'], $compare) === 0 || strpos($opts['script'].'?'.$_SERVER['QUERY_STRING'],$compare) === 0) {
				$display = (string)$elem['display'];
				$opts['path'] = $compare;
				$opts['depth']++;
				$opts['return']['breadcrumbs'][] = array(
					'href'=>$compare,
					'display'=>$display,
				);
				$opts['return']['pageTitle'] = $display;
				// echo 'cpm: '.$opts['script'].' '.$compare."\n";
				if ($opts['script'] === $compare) {
					$opts['return']['complete'] = true;
					return $opts['return'];
				} else if (array_key_exists('children',$elem)) {
					$opts['json'] = $elem;
					return $this->parseData($opts);
				}
			}
		}
		return $opts['return'];
	}	
	
	function renderBreadcrumbs($options) {
		global $h, $site;
		$defaults = array(
			'breadcrumbs'=>array(),
			'delimiter'=>' &gt; '
		);

		$opts = $h->extend($defaults, $options);		
		$len = count($opts['breadcrumbs']);
		$lis = array();
		foreach ($opts['breadcrumbs'] as $i => $bc) {
			$lis[] = ($i < $len - 1) ?
				$h->rtn('a', array($bc['href'], $bc['display'])) :
				$bc['display'];
		}

		$h->liArray('ul', $lis);
		return;
	}	



	
	
	function displayPath() {
		global $h, $breadcrumbs;
		for ($i = 0; $i < count($breadcrumbs); $i++) {
			$h->tnl($breadcrumbs[$i]);
			if ($i < count($breadcrumbs) - 1) $h->tnl(" &raquo; ");
		}
	}

	function displayMenu() {
		global $h, $site;
		global $view;
		if ($site['menuStyle'] == "popup") {
			$h->odiv('id="dhtmlgoodies_menu"');
			$h->local("${view}&menuStyle=tree", "Tree Menu");
			$h->oul();
		} else {
			$h->odiv();
			$h->local("${view}&menuStyle=popup", "Popup Menu");
			$h->oul('class="mktree"');
		}
		$this->generateMenu($this->json);
		$h->cul();
		$h->cdiv();
	}

	function generateMenu($json, $path="", $depth=0) {
		global $h, $site;
		foreach ($json as $elem) {
			$h->startBuffer();
			if ($elem['redirect']) $h->a($elem['redirect'], $elem['display']);
			else $h->local($path.$elem['href'], $elem['display']);
			$link = trim($h->endBuffer());
			////Recur if appropriate (limit popup style depth)
			if (array_key_exists('children', $elem) && 
					($site['menuStyle'] == "tree" 
					|| $depth < 1 )
					&& count($elem['children']) > 0) {
				$h->oli();
				$h->tnl($link);
				$depth++;
				$h->oul();
				$this->generateMenu($elem['children'], $path.$elem['href'], $depth);
				$depth--;
				$h->cul();
				$h->cli();
			} else {	
				$h->li($link);
			}
		}
	}
	function getChildren($path) {
		
		$node = $this->getNode($path);
		return array_key_exists('children', $node) ? $node['children'] : array(); 
	}
	
	function setChildren($path, $children, $nodes='', $tempPath = '') {
		global $h;
		
		if($nodes == '') $nodes = $this->json;
		foreach($nodes as $i=>$v) {
			if($tempPath.$v['href'] == trim($path)) {
				$nodes[$i]['children'] = $children;
			} else if(strpos($path,$tempPath.$v['href']) === 0) {
				if(array_key_exists('children', $v)) {
				 $nodes[$i]['children'] = $this->setChildren($path, $children, $nodes[$i]['children'] , $tempPath.$v['href']);
				}
			}
		}
		return $nodes;
	}
	
	function getNode($path, $nodes = '', $tempPath=''){
		if($nodes == '') $nodes = $this->json;
		foreach($nodes as $i=> $v) {
			if($tempPath.$v['href'] == trim($path)) {
				return $nodes[$i] ;
			} else if(strpos($path,$tempPath.$v['href']) === 0) {
				if(array_key_exists('children', $v)) {
				 	return $this->getNode($path, $nodes[$i]['children'] , $tempPath.$v['href']);
				}
			}
		}
		return array();
	}
	
}
?>