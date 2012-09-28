<?php
//start session
session_start();
class logmein {
	//database setup 
       //MAKE SURE TO FILL IN DATABASE INFO
	var $hostname_logon = 'localhost';		//Database server LOCATION
	var $database_logon = 'jsbin';		//Database NAME
	var $username_logon = 'jsbin-user';		//Database USERNAME
	var $password_logon = 'password';		//Database PASSWORD
	
	//table fields
	var $user_table = 'ownership';			//Users table name
	var $user_column = 'name';		//USERNAME column (value MUST be valid email)
	var $pass_column = '`key`';		//PASSWORD column
	// var $user_level = 'userlevel';		//(optional) userlevel column
	// var $userid_column = 'userid';
	
	//encryption
	var $encrypt = true;		//set to true to use md5 encryption for the password

	//connect to database
	function dbconnect(){
		$connections = mysql_connect($this->hostname_logon, $this->username_logon, $this->password_logon) or die ('Unable to connect to the database');
		mysql_select_db($this->database_logon) or die ('Unable to select database');	
		return;
	}
	
	//login function
	function login($table, $username, $password){
		//connect to DB
		$this->dbconnect();
		//make sure table name is set
		if($this->user_table == ""){
			$this->user_table = $table;
		}
		//check if encryption is used
		if($this->encrypt == true){
			$password = sha1($password);	
		}

		//execute login via qry function that prevents MySQL injections
		$result = $this->qry("SELECT * FROM ".$this->user_table." WHERE ".$this->user_column."='?' AND ".$this->pass_column." = '?';" , $username, $password);
		$row=mysql_fetch_assoc($result);
		
		if($row != "Error"){
			if($row[$this->user_column] !="" && $row['key'] !=""){
				//register sessions
				//you can add additional sessions here if needed
				$_SESSION['name'] = $username;
				// $_SESSION['userid'] = $row[$this->userid_column];
				
				$_SESSION['loggedin'] = $row['key'];
				//userlevel session is optional. Use it if you have different user levels
				// $_SESSION['userlevel'] = $row[$this->user_level];

				setcookie('home', $_SESSION['name'], time() + 7*24*60*60*1000);
				setcookie('key', $_SESSION['loggedin'], time() + 7*24*60*60*1000);
				return true;	
			}else{
				session_destroy();
				return false;
			}
		}else{
			return false;
		}
		
	}


	function create($table, $username, $password, $email, $section) {
		
		if ((strlen($username) < 3) || (strlen($password) < 3)) {
			return false;			
		}
		
		//connect to DB
		$this->dbconnect();
		
		//make sure table name is set
		if($this->user_table == ""){
			$this->user_table = $table;
		}
		
		//check if encryption is used
		if($this->encrypt == true){
			$password = sha1($password);	
		}
		
		$username = strtolower($username);
		
		$result = $this->qry("SELECT * FROM ".$this->user_table." WHERE ".$this->user_column."='?';" , $username);
		// $row=mysql_fetch_assoc($result);
		
		if(mysql_num_rows($result) > 0){  
				//username taken
				return false;
		
		}else{  
				//username available
				$sql = sprintf('insert into '.$this->user_table.' (name, `key`, email, section) values ("%s", "%s", "%s", "%s")', mysql_real_escape_string($username), mysql_real_escape_string($password), mysql_real_escape_string($email), mysql_real_escape_string($section));	
				$result = mysql_query($sql) or die(mysql_error());
				return true;
		}
	}
	
	//prevent injection
	function qry($query) {
	  $this->dbconnect();
      $args  = func_get_args();
      $query = array_shift($args);
      $query = str_replace("?", "%s", $query);
      $args  = array_map('mysql_real_escape_string', $args);
      array_unshift($args,$query);
      $query = call_user_func_array('sprintf',$args);
      $result = mysql_query($query) or die(mysql_error());
		  if($result){
		  	return $result;
		  }else{
		 	 $error = "Error";
		 	 return $result;
		  }
    }
	
	//logout function 
	function logout(){
		setcookie ('home', '', time() - 3600, '/');
		setcookie ('key', '', time() - 3600, '/');
		session_destroy();
  		header('Location: ./');
		return;
	}
	
	//check if loggedin
	function logincheck($logincode, $user_table, $pass_column, $user_column){
		//connect to DB
		$this->dbconnect();
		//make sure password column and table are set
		if($this->pass_column == ""){
			$this->pass_column = $pass_column;	
		}
		if($this->user_column == ""){
			$this->user_column = $user_column;	
		}
		if($this->user_table == ""){
			$this->user_table = $user_table;	
		}
		//execute query
		$result = $this->qry("SELECT * FROM ".$this->user_table." WHERE ".$this->pass_column." = '?';" , $logincode);
		$rownum = mysql_num_rows($result);
		//return true if logged in and false if not
		// if($row != "Error"){
			if($rownum > 0){
				return true;	
			}else{
				return false;	
			}
		// }
	}
	
	//reset password
	function passwordreset($username, $user_table, $pass_column, $user_column){
		//conect to DB
		$this->dbconnect();
		//generate new password
		$newpassword = $this->createPassword();
		
		//make sure password column and table are set
		if($this->pass_column == ""){
			$this->pass_column = $pass_column;	
		}
		if($this->user_column == ""){
			$this->user_column = $user_column;	
		}
		if($this->user_table == ""){
			$this->user_table = $user_table;	
		}
		//check if encryption is used
		if($this->encrypt == true){
			$newpassword = sha1($newpassword);	
		}
		
		//update database with new password
		$qry = "UPDATE ".$this->user_table." SET ".$this->pass_column."='".$newpassword."' WHERE ".$this->user_column."='".stripslashes($username)."'";
		$result = mysql_query($qry) or die(mysql_error());
		
		$to = stripslashes($username);
		//some injection protection
		$illigals=array("n", "r","%0A","%0D","%0a","%0d","bcc:","Content-Type","BCC:","Bcc:","Cc:","CC:","TO:","To:","cc:","to:");
		$to = str_replace($illigals, "", $to);
		$getemail = explode("@",$to);
		
		//send only if there is one email
		if(sizeof($getemail) > 2){
			return false;	
		}else{
			//send email
			$from = $_SERVER['SERVER_NAME'];
			$subject = "Password Reset: ".$_SERVER['SERVER_NAME'];
			$msg = "<p>Your new password is: ".$newpassword."</p>";
			
			//now we need to set mail headers
			$headers = "MIME-Version: 1.0 rn" ;
			$headers .= "Content-Type: text/html; rn" ;
			$headers .= "From: $from  rn" ;
			
			//now we are ready to send mail
			$sent = mail($to, $subject, $msg, $headers);
			if($sent){
				return true; 
			}else{
				return false;	
			}
		}
	}
	
	//create random password with 8 alphanumerical characters
	function createPassword() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
	
	//login form
	function loginform($formname, $formclass, $formaction, $formpre, $formpost){
		//connect to DB
		$this->dbconnect();
		echo $formpre.
		'<form name="'.$formname.'" method="post" id="'.$formname.'" class="'.$formclass.'" enctype="application/x-www-form-urlencoded" action="'.$formaction.'">
				<h1>Login</h1>
				<div><label for="username">Username</label>
				<input name="username" id="username" type="text" maxlength="20" autofocus></div>
				<div><label for="password">Password</label>
				<input name="password" id="password" type="password"></div>
				<input name="action" id="action" value="login" type="hidden">
				<div><input name="submit" id="submit" value="Login" type="submit"></div>
			</form>
			<p style="text-align: center;"><a href="create.php"  style="color: #888;">Create Account</a></p>'.
			$formpost;
			
	}
	//reset password form
	function resetform($formname, $formclass, $formaction){
		//conect to DB
		$this->dbconnect();
		echo'<form name="'.$formname.'" method="post" id="'.$formname.'" class="'.$formclass.'" enctype="application/x-www-form-urlencoded" action="'.$formaction.'">
				<div><label for="username">Username</label>
				<input name="username" id="username" type="text"></div>
				<input name="action" id="action" value="resetlogin" type="hidden">
				<div><input name="submit" id="submit" value="Reset Password" type="submit"></div>
			</form>';
	}
	
	//function to install logon table
	function createtable($tablename){
		//conect to DB
		$this->dbconnect();
		$qry = "CREATE TABLE IF NOT EXISTS ".$tablename." (
			  userid int(11) NOT NULL auto_increment,
			  useremail varchar(50) NOT NULL default '',
			  password varchar(50) NOT NULL default '',
			  userlevel int(11) NOT NULL default '0',
			  PRIMARY KEY  (userid)
			)";
		$result = mysql_query($qry) or die(mysql_error());
		return;
	}
}
?>