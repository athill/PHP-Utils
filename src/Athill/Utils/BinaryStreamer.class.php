<?php  namespace Athill\Utils;
class BinaryStreamer {
	private $root ='';		//// root of location of binary files

	function __construct($root){
		$this->root = $root;
	}


	function stream($path){
		global $h;
		// open the file in a binary mode
		$name = $this->root.$path;
		$fp = fopen($name, 'rb');
		$filename = basename($path);
		// send the right headers
		$extension = $this->getExtension($path);
		header("Content-Type: ".$this->getMimeType($extension));
		header('Content-Disposition: inline;filename="'.$filename.'"');
		//header('Content-Disposition: inline;filename='.urlencode(basename($path)));
		header("Content-Length: " . filesize($name));
		// dump the picture and stop the script
		fpassthru($fp);
		exit;		
	}
	
	function getExtension($name){
		return preg_replace('/.*\.([^.]+)$/','$1',$name);
	}
	
	
	function getMimeType($extension) {
		$types = array(
			'xls'=>'application/ms-excel',
			'xlsx'=>'application/ms-excel',
			'doc'=>'application/ms-word',
			'docx'=>'application/ms-word',
			'pdf'=>'application/pdf',
			'zip'=>'application/zip',
			'gif'=>'image/gif',
			'jpg'=>'image/jpeg',
			'txt'=>'text/plain',
			'htm'=>'text/html',
			'html'=>'text/html',
			'shtml'=>'text/html'
		);
		if (array_key_exists($extension,$types)) {
			return $types[$extension];
		} else {
			//throw(message='Unknow extension in BinaryStreamer: '&extension);
			
		}
	}
}

?>