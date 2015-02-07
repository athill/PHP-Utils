<?php  namespace Athill\Utils;

class Auth {
	private $authfile;		//// location to store authentication info
	private $authkey;		//// random string to encrypt/decrypt data
	private $iv;			//// second random key to encrypt/decript data
	private $algorithm;		//// encryption algorithm
	var $data = array();
	
	//// Initialize the data
	public function __construct($authfile, $authkey, $iv, $algorithm='aes-256-cbc') {
		$this->authfile = $authfile;
		$this->authkey = $authkey;
		$this->iv = $iv;
		$this->algorithm = $algorithm;
		if(is_file($this->authfile)) {
			 $cryptdata = file_get_contents($this->authfile);
			 $jsondata = openssl_decrypt($cryptdata,$this->algorithm,$this->authkey, true, $this->iv); 
			 $this->data = json_decode($jsondata,true);
		}
	}
	
	//// Retrieves a username/password struct
	public function get($id){
		if(!array_key_exists($id, $this->data)) {
			return false; 
		}
		return $this->data[$id];
	}
	
	//// Returns an array of the keys of user/pass structs
	public function keys() {
		return array_keys($this->data);
	}
	
	//// Adds a new user/pass to the array	
	public function set($id, $username, $password) {
		$this->data[$id] = array( 'username' => stripslashes($username), 'password' => stripslashes($password) );
	}
	
	//// Delete a user/pass from the array
	public function delete($id) {
		unset($this->data[$id]);
	}
	
	//// saves changes to data 
	public function write() {
		$jsondata = json_encode($this->data);
		// $iv = substr($this->authkey,32,16);
		 $cryptdata = openssl_encrypt($jsondata,$this->algorithm,$this->authkey, true, $this->iv);
		 file_put_contents($this->authfile, $cryptdata);
	}

}

?>