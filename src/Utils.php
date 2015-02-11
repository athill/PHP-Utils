<?php  namespace Athill\Utils;
class Utils {
	function handleDirectory($dir, $callback) {
			
	}
	
	function filterDir($dir) {
		$dir = preg_replace('/\/$/', '', trim($dir));
		echo $dir;
		$rtn = array();
		$ls = scandir($dir);	
		foreach ($ls as $item) {
			if (!in_array($item, array('.', '..', '.htaccess'))) {
				$rtn[] = $item;	
			}
		}
		return $rtn;
	}
	
	function makeDir($dir){
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}		
	}
	
	function removeDir($dir){
		$dir = preg_replace('/\/$/', '', trim($dir));
		if(file_exists($dir)){
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
    			$path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
			}
			rmdir($dir);
		}
	}
	
	function isJson($check_string){
		json_decode($check_string);
 		return (json_last_error() == JSON_ERROR_NONE);
	}
}
?>