<?php  namespace Athill\Utils;
//require_once('/ip/uirr/inc/lib/owasp-esapi/src/ESAPI.php');
include('/ip/uirr/inc/lib/htmlpurifier/library/HTMLPurifier.auto.php');
class Sanitizer {
	var $entities; 
	var $badTags;
	var $attacks = array();
	var $testing = false;
	var $attributeTests = array();
	var $purifier;

	function __construct(){
		$this->entities = array(
			"&iquest;" => chr(191),
			"&lsquo;" => chr(8216),
			"&ldquo;" => chr(8220),
			"&rdquo;" => chr(8221),
			"&rsquo;" => chr(8217),
			// "&quot;" = chr(34),$
			// "&##39;" = chr(39),
			"&amp;" => chr(38),
			// "&gt;" = chr(62),
			// "&lt;" = chr(60),
			"&ndash;" => chr(8211),
			"&mdash;" => chr(8212)
			
		);	
		$this->badTags = array('iframe','script','style','input');	
		$this->attributeTests = array(
			'noquotes' => '/(.*attribute\s*=\s*)([^\s\/>]+)(.*)/i',
			'singlequotes' => "/(.*attribute\s*=\s*'')([^''>]+)(.*)/i",
			'doublequotes' => '/(.*attribute\s*=\s*")([^"]+)(.*)/i'
		);	
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', 'UTF-8');
		$config->set('Core.RemoveInvalidImg',true);
		$config->set('HTML.Allowed','p,b,a[href],i,img[src|alt],span[style],li,ul,div[style],br,strong');
		$this->purifier = new HTMLPurifier($config);
	}

	public function sanitize($scope) {
		global $h;
		$this->attacks = array();
		$methods = array('canonicalize', 'sanitizeHtmlEventAttributes');
		//$methods = array('canonicalize','sanitizeBadTags', 'sanitizeImgSrcScript');
		//$methods = array();
		//$methods = array('canonicalize', 'sanitizeHtmlEventAttributes');
		$noerrormethods = array('canonicalize');
		
		//// clean field names
		foreach ($methods as $methodname) {
			$badNames = array();
			foreach($scope as $fieldName=>$val) {
				$newName = call_user_func_array(array($this, $methodname), array($fieldName));
				if($newName != $fieldName) {
					$badNames[$fieldName]= $newName;
				}
			}
			foreach($badNames as $oldName=>$val) {
				$newName = $badNames[$oldName];
				$scope[$newName] = $scope[$oldName];
				unset($scope[$oldName]);
				if(!in_array($methodname,$noerrormethods)) {
					$this->attacks[] = array(
						'type' => $methodname,
						'key/val' =>'key',
						'field' =>$newName,
						'original' => $oldName,
						'sanitized' => $newName 
					);
				}
			 }
		 }
		//// clean values
		foreach($scope as $fieldName=>$val) {
			foreach($methods as $methodname){
				if (is_array($scope[$fieldName])) {
					$newVal = array();
					foreach($scope[$fieldName] as $item){
						$newVal[] = call_user_func_array(array($this, $methodname), array($item));
					}
				} else {
					$newVal = call_user_func_array(array($this, $methodname), array($scope[$fieldName]));
				}
				if($scope[$fieldName] != $newVal) {
					$scope[$fieldName] = $newVal;
					if(!in_array($methodname,$noerrormethods)) {
						$this->attacks[] = array(
						'type' => $methodname,
						'key/val' =>'value',
						'field' => $fieldName,
						'original' => $val,
						'sanitized' => $newVal 
						);	
					}
				}
		    }
			//$scope[$fieldName]= $this->entify($scope[$fieldName]);
		}
		//print_r($this->attacks);
		if(count($this->attacks) > 0 && !$this->testing){
			$this->email();
		}
		return $scope;	
		
	}
	
	public function canonicalize($value) {
		global $h;
		// echo 'before canonicalize \r\n';
		 //$h->pa($value);
		 $replace_strings = array("%00");
		 $value = str_replace($replace_strings,'',$value);
		 $value = urldecode($value);
		 $value = html_entity_decode($value);
		 $value = stripslashes($value);
		 //echo 'after canonicalize \r\n ';
		 //$h->pa($value);
/*		echo 'here';	
		try {
			
			$input = ESAPI::getEncoder()->canonicalize($value);
			
		} catch (Exception $e){
			//echo($e->getUserMessage());
			print_r($e);
			exit();
		}
*/
		return $value;	
	}
	public function sanitizeBadTags($value){
		
		foreach ($this->badTags as $badTag){
			$value = str_ireplace('<'.$badTag,'<badTag',$value);
			$value = str_ireplace('</'.$badTag,'</badTag',$value);
		}
		return $value;
	}
	
	public function sanitizeImgSrcScript($value){
		global $h;
		$matches = $this->getTagMatches('img', $value);
		foreach($matches[0] as $match){
			foreach($this->attributeTests as $test) {
				$test = str_replace('attribute', 'src', $test);
				if(preg_match($test,$match)){
					$src = preg_replace($test,'$2',$match);
					if(preg_match('/javascript:/i', $src) == 1){
						$value = preg_replace($test,'$1badImg$3',$value);
					}
				 	break;
				}
			}
		}
		return $value;
	}
	
	private function getTagMatches($tag, $value) {
		$pattern = "/<".$tag."[^>]+>/i";
		preg_match_all($pattern,$value,$matches);
		return $matches;
	}
	
	function sanitizeHtmlEventAttributes($value) {
		global $h;
		//echo 'before html purifier \r\n';
		//$h->pa($value);
		$value = $this->purifier->purify($value);
		/*$events = array('onmouseover', 'onclick', 'onfocus', 'onerror');
		$matches = $this->getTagMatches('img', $value);
		foreach($matches[0] as $match){
			foreach ($events as $event) {
				foreach($this->attributeTests as $test) {
					$test = str_replace('attribute', $event, $test);
					if(preg_match($test,$match)) {
						$h->p('before');
						$h->pa($value);
						$value = preg_replace($test, '', $value);
						$h->p('after');
						$h->pa($value);
						
					}
				}
			}
		}*/
		//echo 'after html purifier \r\n';
		//$h->pa($value);
		return $value;
	}
	
	public function entify($value){
		foreach ($this->entities as $entity=>$entity_val) {
			$value = str_ireplace($entity_val,$entity,$value);
		}
	return $value;
	}
	
	private function email() {
		global $h, $site;
		$h->startBuffer();
		$page = $_SERVER['SCRIPT_NAME'];
		$user =  isset($_SESSION['user'])? $_SESSION['user'] : array_key_exists('REMOTE_USER',$_SERVER) ? $_SERVER['REMOTE_USER'] :'';
		$attributes = array(
			'IP'   => $_SERVER['REMOTE_ADDR'],
			'Page' => $page,
			'User' => $user,
			'GET'  => $_SERVER['QUERY_STRING'],
			'POST' => json_encode($_POST)
 		);
		foreach($attributes as $att=>$value) {
			$h->tbr('<strong>'.$att.':</strong>'.$attributes[$att]);
		}
		////
		$headers = array_keys($this->attacks[1]);
		$hlen = count($headers);
		$data = array();
		foreach($this->attacks as $attack){
			$row = array();
			foreach($headers as $field){
				$row[] = $attack[$field];
			}
			$data[] = $row;
		}
		$h->simpleTable( array(
				'headers' => $headers,
				'data'    => $data,
				'atts'	  => 'border="1"'
		    ));
		$content = $h->endBuffer();
		$site['mailer']->type = 'html';
		$site['mailer']->send($site['webmaster'], 'Possible XSS attack on '.$page, $content);		
		//$site['mailer']->send($site['webmaster'], 'testing input validation '.$page, $content);	
	}	
	
}
?>