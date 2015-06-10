<?php namespace Athill\Utils;
class Error {
		//// set by overridable get methods.
		protected $email = '';
		protected $user='';
		protected $logfile;

		//// used internally
		protected $errors = array();
		protected $hasError = false;
		protected $isPrd;
		
		// define an assoc array of error string
		// in reality the only entries we should
		// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
		// E_USER_WARNING and E_USER_NOTICE
		protected $errortype = array (
					E_ERROR              => 'Error',
					E_WARNING            => 'Warning',
					E_PARSE              => 'Parsing Error',
					E_NOTICE             => 'Notice',
					E_CORE_ERROR         => 'Core Error',
					E_CORE_WARNING       => 'Core Warning',
					E_COMPILE_ERROR      => 'Compile Error',
					E_COMPILE_WARNING    => 'Compile Warning',
					E_USER_ERROR         => 'User Error',
					E_USER_WARNING       => 'User Warning',
					E_USER_NOTICE        => 'User Notice',
					E_STRICT             => 'Runtime Notice',
					E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
		);


	function __construct($instance='prd') {
		$this->isPrd = strtolower($instance) === 'prd';
		$this->logfile = $this->getLogfile();
		$this->user = $this->getUser();	
		$this->email = $this->getEmail();
		//// we will do our own error handling
		error_reporting(0);
				
		$old_error_handler = set_error_handler(array($this, 'error_handler'));
		register_shutdown_function(array($this, 'shutdown'));
	}

	protected function getLogfile() {
		return $_SERVER["DOCUMENT_ROOT"].'/error.log';
	}



	protected function getUser() {
		$user = '';
		//// try various way to get current user
		if (isset($_SESSION['user'])) {
			$user = $_SESSION['user'];
		} else if (isset($_SERVER['REMOTE_USER'])) {
			$user = $_SERVER['REMOTE_USER'];
		}
		return $user;
	}

	protected function getEmail() {
		$email = '';
		if (isset($_ENV['error']['email'])) {
			$email = $_ENV['error']['email'];
		}
		return $email;
	}


	function error_handler($number, $message, $file, $line){
		$args = func_get_args();
		//// error message
		$error = call_user_func_array([$this, 'getErrorMessage'], $args);
		//// add backtrace
		$backtrace = $this->getBacktrace(debug_backtrace(false));
		//// items (parts of error message)
		$items = [
			'Error' => $error,
			'Stack Trace' => print_r($backtrace, 1),
		];
		//// add to page errors
		$this->errors[] = $items;		

		//// first error?
		if (!$this->hasError) {
			//// production (show friendly message)
			if ($this->isPrd) {
				echo $this->getPublicErrorMessage();	
			//// non-production (display first error + scopes)
			} else {
				//// add variable scope to items
				$items = array_merge($items, $this->getScopeDumps());
				//// build content
				$content = '<pre>';
				foreach ($items as $key => $value) {
					$content .= $this->getItemString($key, $value);
				}
				$content .= '</pre>';
				//// render
				echo $content;
			}				
		}
		$this->hasError = true;
	}

	protected function getItemString($key, $value) {
		return $key.":\n".$value."\n".$this->getDelimString();
	}

	protected function getDelimString() {
		return str_repeat('-', 30)."\n";
	}

	protected function getScopeDumps() {
		$scopes = [
			'POST'=>$_POST,
			'GET'=>$_GET,
			'SERVER'=>$_SERVER,
			'SESSION'=>$_SESSION
		];
		
		foreach ($scopes as $label => $scope) {
			$scopes[$label] = print_r($scope, 1);
		}
		return $scopes;
	}

	protected function getBacktrace($backtrace) {
		for ($i=0; $i<count($backtrace); $i++){
			unset($backtrace[$i]['args']);
		}
		return $backtrace;
	}


	protected function getErrorMessage($number, $message, $file, $line) {
		$errormssg = $this->errortype[$number];
		$message = "An error ($errormssg) occurred on line $line 
			in file: $file.
			$message 
			User: $this->user";
		return $message;
	}
	
	protected function getPublicErrorMessage() {
		echo '<div class="error-message">We\'re sorry, an error has occured. The webmaster 
			has been notified</div>';		
	}

	function shutdown() {
        $isError = false;
        if ($error = error_get_last()){
            switch($error['type']){
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    $isError = true;
                    break;
            }
        }

        if ($isError){
			$message = "Script execution halted: <pre class='error-dump'>{$error['message']} in file: {$error['file']} on line {$error['line']}</pre>";
            $message .= "<p>User: $this->user</p>"; 
			if($this->isPrd){
				if (!$this->hasError) {
					$this->errorMessage();	
					$this->hasError = true;
				}
							
				$this->errors[] = $message;
			}else{
				//print_r($error);
				echo $message;
			}
		}
		if (count($this->errors) > 0) {
			$subject = 'Subject: Error on '.$_SERVER['HTTP_HOST'].': '.$_SERVER['SCRIPT_NAME'];
			//$headers = 'Content-type: text/html; charset=iso-8859-1'."\r\n".$subject."\r\n".'Cc:'.$this->emailcc;
			$content = '';
			//// render errors
			$delim = $this->getDelimString();
			foreach ($this->errors as $error) {
				foreach ($error as $key => $value) {
					$content .= $this->getItemString($key, $value);
				}
				$content .= $delim;
			}
			$content.= $delim;
			$scopes = $this->getScopeDumps();
			foreach ($scopes as $key => $value) {
				$content .= $this->getItemString($key, $value);
			}
			// error_log($content, 1, $this->emailTo,$headers);	
			error_log($content, 3, $this->logfile);
		}
    }	
}