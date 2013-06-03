<?php

set_time_limit(500);
ini_set("memory_limit","999M");

//loginto sql

include('config.php'); // contains DB & important versioning

include('auth.php'); // contains user auth
$log = new logmein(); // instantiate the class
$log->dbconnect();  // connect to the database
$log->encrypt = true;   // set to true if password is md5 encrypted. Default is false.
// parameters are (SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck(@$_SESSION['loggedin'], "ownership", "key", "name") == false){
    //do something if NOT logged in. For example, redirect to login page or display message.

  $pre = '<!DOCTYPE html><html><head><meta charset=utf-8 /><title>openHTML - Login</title><link rel="stylesheet" href="' . ROOT . 'css/style.css" type="text/css" /></head><body><div id="control"><div class="control"><div class="buttons"><div id="auth"><span id="logo">openHTML</span></div></div></div></div><div id="bin" class="stretch">';

  $post = '</div></body></html>';

  $log->loginform("loginformname", "loginformid", ROOT."login.php", $pre, $post);

  die();
}else{


}

 $url = $_GET['url'];
    $out =  retrieveReplay($url);
    // var_dump($out);
    echo json_encode($out[80][css]);

function retrieveReplay($url) {

	$history = "";
	$historyarray = array();

	$sql = "SELECT session FROM replay_combined WHERE url = '" . mysql_real_escape_string($url) . "' ORDER BY time ASC";
	$result = mysql_query($sql);

	if(!mysql_num_rows($result)){
		error_log("no rows returned");
		exit;
	}	
		
	while ($row = mysql_fetch_assoc($result, MYSQL_ASSOC)) {
		$history .= $row['session'];
		set_time_limit(500);
		// $historyarray[] = json_decode($row['session']);
		// array_merge($historyarray, json_decode($row['session']));
		// error_log(serialize(json_decode($row['session'])));
	}	
	
	$history = str_replace('][', ',', $history);
	error_log($history);
	// $history = $historyarray;
	$history = json_decode($history, true);
	// $history = formatReplay($history);

	return $history;
}


//Accepts array of replay history in ascending order to format the timestamps for replay or any other formatting which may be required in the future
function formatReplay($data) {

	$origTime = $data[0]['clock'];

	foreach($data as $key => $value){
		$data[$key]['stamp'] = date("m/d/y h:i:s a", ($data[$key]['clock']/1000));
		$data[$key]['live']	= formatCompletedCode($data[$key]['html'], $data[$key]['css']);
		$data[$key]['html'] = htmlentities($data[$key]['html']);
		$data[$key]['css'] = htmlentities($data[$key]['css']);
		$data[$key]['clock'] -= $origTime;
		// if((($data[$key]['clock'])-($data[$key-1]['clock'])) > 300) {
		// 	$session['end'] = $data[$key-1]['clock'];
		// 	$session['start'] = $data[$key]['clock'];
		// }
	}
	return $data;
}



function formatCompletedCode($html, $javascript) {

  $javascript = preg_replace('@</script@', "<\/script", $javascript);

  if ($html && stripos($html, '%code%') === false && strlen($javascript)) {
    $parts = explode("</head>", $html);
    $html = $parts[0];
    $close = count($parts) == 2 ? '</head>' . $parts[1] : '';
    $html .= "<style scoped>\n " . $javascript . "\n</style>\n" . $close;
  } else if ($javascript && $html) {
    // removed the regex completely to try to protect $n variables in JavaScript
    $htmlParts = explode("%code%", $html);
    $html = $htmlParts[0] . $javascript . $htmlParts[1];

    $html = preg_replace("/%code%/", $javascript, $html);
  }

  return array($html);
}
?>