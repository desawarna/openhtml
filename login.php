<?php
//instantiate if needed
include("auth.php");
include('logger.php');
$log = new logmein();
$log->encrypt = true; //set encryption
if($_REQUEST['action'] == 'login'){
    if($log->login("ownership", $_POST['username'], $_POST['password']) == true){
        //do something on successful login
        
        // logger('login');

			header('Location: ./list');
    }else{
        //do something on FAILED login
		$pre = '<!DOCTYPE html><html><head><title>openHTML - Login</title><link rel="stylesheet" href="css/style.css" type="text/css" /></head><body><div id="control"><div class="control"><div class="buttons"><div id="auth"><span id="logo">openHTML</span></div></div></div></div><div id="bin" class="stretch">';
		$post = '<div class="warning">Login failed. Please try again.</div></div></body></html>';

		$log->loginform("loginformname", "loginformid", "login.php", $pre, $post);
    }
}


?>