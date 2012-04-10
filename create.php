<?php
//instantiate if needed
include("auth.php");
include('logger.php');
$log = new logmein();
$log->encrypt = true; //set encryption


if($_REQUEST['action'] == 'create'){
    if($log->create("ownership", $_POST['username'], $_POST['password'], $_POST['email']) == true){
        //do something on successful creation
        
        // logger('login');

			header('Location: ./');
    }else{
        //do something on FAILED login
		
			echo '	<!DOCTYPE html>
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
										<input name="action" id="action" value="create" type="hidden">
										<div><input name="submit" id="submit" value="Create Account" type="submit"></div>
									</form>
									<p style="text-align: center;"><a href="./"  style="color: #888;">Already Have Account</a></p>
									<div class="warning">Please try again.</div>

						</body>
				</html>
			';
			
			die();
    }
}




?>


<!DOCTYPE html>
	<html>
		<head>
				<title>openHTML - Create Account</title>
				<link rel="stylesheet" href="css/style.css" type="text/css" />
		</head>
		<body>
			<div id="control">
				<div class="control">
					<div class="buttons">
						<div id="auth">
							<span id="logo">openHTML</span>
						</div>
					</div>
					</div>
				</div>
			<div id="bin" class="stretch">

				<form name="createformname" method="post" id="createformname" class="loginformid" enctype="application/x-www-form-urlencoded" action="/openhtml/create.php">
						<h1>Create Account</h1>
						<div>
							<label for="username">Username (required)</label>
							<input name="username" id="username" class="required" type="text" maxlength="20" autofocus>
						</div>
						<div>
							<label for="password">Password (required)</label>
							<input name="password" id="password" class="required" type="password">
						</div>
						<div>
							<label for="email">Email (optional)</label>
							<input name="email" id="email" type="text">
						</div>
						<input name="action" id="action" value="create" type="hidden">
						<div><input name="submit" id="submit" value="Create Account" type="submit"></div>
			</form>
					<p style="text-align: center;"><a href="./" style="color: #888;">Already Have Account</a></p>
				
				</div>
		</body>
</html>


