<?php  namespace Athill\Utils;
class CSV {
	function __construct() {
		
	}
	
	
	function csv_to_array($filename='', $delimiter=',') {
		if(!file_exists($filename) || !is_readable($filename))
			return FALSE;
	
		$header = NULL;
		$data = array();
		if (($handle = fopen($filename, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				if(!$header)
					$header = $row;
				else
					$data[] = array_combine($header, $row);
			}
			fclose($handle);
		}
		return $data;
	}	
	
/*	function parse($file) {
		$data = str_split(file_get_contents($file));
		$result = array();
		$headers = array();
		$inquotes = false;
		$row = 0;
		$field = 0;
		$fielddata = '';
		$lastchar = '';
		foreach ($data as $char) {
			//echo $char."\n";
			if ($char == '"') {
				$inquotes != $inquotes;
			} else if (!$inquotes && $char == ',') {
				if ($row == 0) {
					$headers[] = $fielddata;
				} else {
					echo 'adding '.$fielddata." comma<br />";
					$result[$row][$headers[$field]]	= $fielddata;
				}
				$field++;
				$fielddata = '';
			} else if (!$inquotes && preg_match("/\n/", $char)) {
				if ($row == 0) {
					$headers[] = $fielddata;
				} else {
					echo 'adding '.$fielddata.' '.ord($char)."<br />";
					$result[$row][$headers[$field]]	= $fielddata;
				}
				$field = 0;
				if ($row == 0) print_r($headers);
				$row++;
				$fielddata = '';		
			} else {
				$fielddata .= $char;	
			}
		}
		return $result;
	}*/
	
}
?>