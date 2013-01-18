<?php
//instantiate if needed
include("auth.php");
include('logger.php');
include("config.php");
$log = new logmein();
$log->encrypt = true; //set encryption


$sections = getGroups();
foreach($sections as $section){
	$section_option = $section_option . "<option>".$section."</option>";									
}



$loginform = '	<!DOCTYPE html>
					<html>
						<head>
								<title>openHTML - Create Account</title>
								<link rel="stylesheet" href="css/style.css" type="text/css" />
						</head>
						<body>
							<div id="control"><div class="control"><div class="buttons"><div id="auth"><span id="logo">openHTML</span></div></div></div></div><div id="bin" class="stretch">

								<form name="createformname" method="post" id="createformname" class="loginformid" enctype="application/x-www-form-urlencoded" action="/openhtml/create.php">
										<h1>Create Account</h1>
										<div><label for="username">Username (required)</label>
										<input name="username" id="username" type="text" maxlength="20" autofocus></div>
										<div><label for="password">Password (required)</label>
										<input name="password" id="password" type="password"></div>
										<div><label for="email">Email (optional)</label>
										<input name="email" id="email" type="text"></div>
										<div><label for="section">Group (optional)</label>
										<select name="section">
											<option>None</option>'.
											$section_option

										.'</select>
										</div>
										<input name="action" id="action" value="create" type="hidden">
										<div style="text-align: center;"><input name="submit" id="submit" class="button medium" style="height:auto; float:none;" value="Create Account" type="submit"></div>
									</form>
									<p style="text-align: center;"><a href="./"  style="color: #888;">Already Have Account</a></p>
						</body>
				</html>
			';

// <div class="message">
// <input name="consent" id="section" type="checkbox" checked="checked" style="display: block; float:left; margin-right: 15px;" />
// <label for="section" style="margin-left:0px;">
// Allow anonymous data to be used for research.</label>
// </div>
										

if(isset($_REQUEST['action']) == 'create'){
    if($log->create("ownership", $_POST['username'], $_POST['password'], $_POST['email'], $_POST['section'], $_POST['consent']) == true){
        //do something on successful creation
        
        // logger('login');
			$log->logout();
			header('Location: ./');
    }else{
        //do something on FAILED login
		//echo loginform and fail msg

			echo $loginform .
								'
								<div class="warning" style="text-align: center;">Please try again.</div>';
			
			die();
    }
}


echo $loginform;


function getGroups(){

	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $link);

	$query = "SELECT section FROM groups order by section asc";
	$result = mysql_query($query);

	while($group = mysql_fetch_array($result)){
		$groups[] = $group['section'];
	}


	return $groups;
}

?>


