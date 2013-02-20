<?php

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
    //do something else if logged in.

}
?>

<!DOCTYPE html>
<html>
<!-- containers / styling -->
<head>
	<link rel="stylesheet" href="./css/site.css">
	<link rel="stylesheet" href="./css/prettify.css">
	<link rel="stylesheet" href="./css/font-awesome.css">

<!-- style -->
<style type="text/css">

html, body {
	height: 100%;
	margin: 0;
	padding: 0;
}

#top {
	position: fixed;
	top: 0;
	background-color: orange;
	width: 100%;
	border: solid 1px #ccc;
}

#ReplayContainer {
	box-sizing: border-box;
	height: 100%;
	padding-top: 43px;
}

.pane {
	box-sizing: border-box;
	float:left;
	vertical-align: top;
	display: inline-block;
	height: 100%;
	margin: 0;
	background: none;
	border-right: solid 3px #ccc;
	border-width: 0 3px 0 0;
	word-wrap:break-word;
}

#cssReplay {
	width: 20%;
 }

#htmlReplay {
	width: 30%;
 }

#previewReplay {
	padding: 5px;
	border: none;
	width: 50%;
}

 #special {
 	clear: left;
 }

 #scroll-wrap {
 	width: 100%;
 	height: 5px;
 	margin-top:7px;
 	padding: 3px;
 	background-color: yellow;
 }

/* #scroll-wrap:hover {
 	height: 20px;
 }*/

 #speed {
 	top: 2px;
 }

 #elapsed {
 	height:5px;
 	width: 1%;
 	vertical-align: middle;
 	background-color: orange;
 }

 .top {
position: absolute;
}

 pre {
 	white-space: pre-wrap;
 	white-space: -moz-pre-wrap;
 	white-space: break-word;
 }

 .button {
 	display: inline-block;
 	height: 20px;
 	width: 20px;
 	padding: 1px 10px 1px 10px;
 	/*margin-right:10px;*/
 	font-size: 20px;
    color: #FFF;
 }

 .button:active {
 	opacity: .5;
 }

</style>

</head>

<body>

<!-- buttons -->
<div id="top">
	<div class="button" value=start name=start onClick="startTimer()"> <i class="icon-play"></i> </div>
	<div class="button" value=stop name=stop onClick="stopTimer()"><i class="icon-pause"></i></div>
	<div class="button" value=stop name=stop onClick="back()"><i class="icon-step-backward"></i></div>
	<div class="button" value=stop name=stop onClick="skip()"><i class="icon-step-forward"></i></div>
	<div class="button" value=stop name=stop onClick="reset()"><i class="icon-stop"></i></div>
	Speed: <span id="speedval">10</span>  <input type="range" id="speed" min="0" max="50" step="1"  value="10" onChange="changeSpeed()"/>
	Event: <span id="special">Events</span>
	Date: <span id="date">cdas</span>
	<!-- T: <span id="t">0</span>
	Time: <span id="time">0</span> ||
	<input type="range" id="play" min="0" max="<?php echo $end['clock']/1000; ?>" step="1"  value="0" /> <?php echo $end['clock']/1000; ?>||
	Current: <span id="playval">0</span> ||
	Next Active: <span id="nextactive">0</span> Seconds -->
	<div id="scroll-wrap">
		<div id="current"></div>
		<div id="elapsed"></div>
	</div>
</div>



<div id="ReplayContainer">

	<pre id = "cssReplay" class="pane">
		CSS
	</pre><pre id = "htmlReplay" class="pane">
		HTML
	</pre>

	<div id = "previewReplay" class="pane">
	</div>
</div>

<!-- script -->
<script type="text/javascript" src="<?php echo ROOT?>js/vendor/jquery.js"></script>
<script type="text/javascript" src="<?php echo ROOT?>js/vendor/jquery.scoped.js"></script>
<script type="text/javascript">

// Timer functions

//variables
var t, timer, i, speed, play;
t = 0;
i = 0;
speed = 10;
play = 0;

//retrieve php variables
<?php
date_default_timezone_set('America/New_York');
$history = retrieveReplay(mysql_real_escape_string($_GET['url']));
// $history = retrieveReplay("ibubiw"); // ankur's test
// $history = retrieveReplay("ipabuc"); // tom's test
$js_history = json_encode($history);
$end = end($history);

?>

var history = <?php echo $js_history; ?>;
console.log(history);

function startTimer(){
	timer = self.setInterval("addTime()", 1)
}

function stopTimer(){
	console.log("Stop");
	self.clearInterval(timer);
	timer = null;
}

function addTime(){
	t++;
	populate();
	// document.getElementById("t").innerHTML = t;
	// document.getElementById("time").innerHTML = (history[i+1]['clock']/1000);
	// document.getElementById("nextactive").innerHTML = ((history[i+1]['clock'])-(t*speed))/1000;
	// document.getElementById("play").value = (t*speed/1000);
	// document.getElementById("playval").innerHTML = (t*speed/1000);
	document.getElementById("date").innerHTML = history[i-1]['stamp'];
	var end = history.length;
	var percent = t*speed/parseInt(history[end-1]['clock'])*100;
	$("#elapsed").css("width", percent+"%");
}

function skip(){
	t = (history[i]['clock'])/speed;
	populate();
	
}

function back(){
	i = i-2;
	t = (history[i]['clock'])/speed;
	populate();
}

function reset(){
	t = -1;
	i = 0;
	update();
	stopTimer();	
}

function changeSpeed(){
	speed = document.getElementById("speed").value;
	document.getElementById("speedval").innerHTML = speed;
}



function populate(){
	 if((t*speed) >= history[i]['clock']){
	 	update();
	 	i++;
	 }

function update(){
		// if(typeof history[i+1] != 'undefined'){
		var end = history.length;
		var percent = t*speed/parseInt(history[end-1]['clock'])*100;
		$("#elapsed").css("width", percent+"%");	
			
		if(i < (history.length-1)){
			document.getElementById("cssReplay").innerHTML = history[i]['css'];
		 	document.getElementById("htmlReplay").innerHTML = history[i]['html'];
		 	// document.getElementById("previewReplay").innerHTML = html_entity_decode(history[i]['html'])
		 	document.getElementById("previewReplay").innerHTML = history[i]['live'];
		 	document.getElementById("special").innerHTML = history[i]['special'];
		 	$.scoped();
		} else {stopTimer(); }
	}
}

function html_entity_decode(str){
 var tarea = document.createElement('textarea');
 tarea.innerHTML = str; return tarea.value;
 tarea.parentNode.removeChild(tarea);
}



$("#scroll-wrap").click(function(pos){
	$("#current").offset({left:pos.pageX});
});

</script>

</body>
</html>





<?php

//debug
	// var_dump($js_history);
	//var_dump($history);
	// var_dump($combined);
$session = array();

//Retrieves replay history from the database
function retrieveReplay($url) {



	$sql = "SELECT session FROM replay_sessions WHERE url = '" . mysql_real_escape_string($url) . "' ORDER BY time ASC";
	$result = mysql_query($sql);
		

	while ($row = mysql_fetch_assoc($result, MYSQL_ASSOC)) {
		$history .= $row['session'];
	}

	// foreach($history as $key => $value){
	// 	$java_object .= $history[$key]["session"];
	// }


	
	$history = str_replace('][', ',', $history);
	$history = json_decode($history, true);


	$history = formatReplay($history);
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
		//if((($data[$key]['clock'])-($data[$key-1]['clock'])) > 300) $session['clock'];
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
  } else if ($javascript) {
    // removed the regex completely to try to protect $n variables in JavaScript
    $htmlParts = explode("%code%", $html);
    $html = $htmlParts[0] . $javascript . $htmlParts[1];

    $html = preg_replace("/%code%/", $javascript, $html);
  }

  return array($html);
}


?>
