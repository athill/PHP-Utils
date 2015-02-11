<?php  namespace Athill\Utils;
class Mysql extends PDO {
	
    public $recordCount = 0;
	
   
    public function __construct($database, $userid, $host) {
        $engine = 'mysql';
		$auth = new \Athill\Utils\Auth();
		$userpasswd = $auth->get($userid);
		$user = $userpasswd['username'];
		$pass = $userpasswd['password'];
        $dns = $engine.':dbname='.$database.";host=".$host;

		parent::__construct( $dns, $user, $pass );
		$this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
		//$this->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
    }
	
	public function queryDisplay($sql,$args=array()){
		foreach($args as $arg){
			$sql = preg_replace('/\?/', "'$arg'", $sql, 1);
		}
		echo $sql;
	}
	
	public function query($sql, $args=array()) {
		return $this->safequery($sql, $args);	
	}
	
	public function output($sql, $args=array()) {
		foreach ($args as $k=>$v) {	
			$sql = str_replace($k, "'$v'", $sql);
		}
		print($sql);
	}
	
	public function safequery($sql, $args=array()) {
		$rtn = array();
		$stm = parent::prepare($sql);
		$stm->execute($args);
		$rtn = $stm->fetchAll();
		$this->recordCount = count($rtn);
		return $rtn;
	
	}
	
	public function exec($sql) {
		$count = parent::exec($sql) ;
		return $count;
	}
	
	public function insert($table, $args) {
		//// regular insert
		if (!isset($args[0])) {
			$values = array_values($args);
			$keys = array_keys($args);
			$escapes = array_fill(0, count($keys), '?');
			$sql = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) ' .
				'VALUES (:'.implode(', :', $keys).')';	
		//// extended insert
		} else {
			$sql = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $args[0]).'`) VALUES';
			$escapes = array_fill(0, count($args[0]), '?');
			$args = array_slice($args, 1);
			$args2 = array();
			for ($i = 0; $i < count($args); $i++) {
				$sql .= '('.implode(', ', $escapes).')';
				if ($i < count($args) - 1) {
					$sql .= ', ';	
				}
				$args2 = array_merge($args2, $args[$i]);
			};
			$args = $args2;
		}
		$stmt = $this->prepare($sql);
		$stmt->execute($args);
		return $stmt->rowCount();		
	}
	
	function upsert($table, $nonKeyArgs, $keyArgs=array()) {
		$args = array_merge($nonKeyArgs, $keyArgs);
		$values = array_merge(array_values($args), array_values($nonKeyArgs));
    	$keys = array_keys($args);
		$escapes = array_fill(0, count($keys), '?');
		$update = '';
		$updates = array();
		foreach ($nonKeyArgs as $key => $value) {
			$updates[] = $key."=?";
		}
		$sql = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) ' .
			'VALUES ('.implode(',', $escapes).') 
			ON DUPLICATE KEY UPDATE '.implode(', ', $updates);	
		$stmt = parent::prepare($sql);
		$stmt->execute($values);
		return $stmt->rowCount();		
	}
	
	public function update($table, $args, $where, $whereargs=array()) {
		$values = array_merge(array_values($args), $whereargs);
		$sql = "UPDATE ".$table." SET ";
		$updates = array();
		foreach ($args as $key => $value) {
			$updates[] = $key."=?";
		}
		$sql .= implode(', ', $updates). "WHERE ".$where;
		$stmt = parent::prepare($sql);
		$stmt->execute($values);
		return $stmt->rowCount();
	}
	
	public function delete($table, $where, $whereargs=array()) {
		$sql = "DELETE FROM ".$table." WHERE ".$where;
		$stmt = parent::prepare($sql);
		$stmt->execute($whereargs);
		return $stmt->rowCount();
	}
	
	public function dt($date) {
		$time = (is_numeric($date) && (int)$date == $date) ? $date : strtotime($date);	
		return date('Y-m-d', $time);
	}
	
	public function value_array($result, $column) {
		$return = array();
		foreach ($result as $row) {
			$return[] = $row[$column];	
		}
		return $return;
	}
	
	function resultToTable($result) {
		$h = \Athill\Utils\Html::singleton();
		if (count($result) == 0) {
			return;	
		}
		$cols = array();
		foreach ($result[0] as $col=>$value) {
			if (!is_numeric($col)) {
				$cols[] = $col;	
			}
		}
		$data = array();
		foreach ($result as $row) {
			$tr = array();
			foreach ($cols as $col) {
				$tr[] = $row[$col];	
			}
			$data[] = $tr;
		}
		$h->simpleTable(array(
			'headers'=>$cols,
			'data'=>$data,
			'atts'=>'border="1"'
		));
	}
}


?>