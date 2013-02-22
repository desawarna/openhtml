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
	<link rel="stylesheet" href="./css/font-awesome.css">
	<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>

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
	width: 100%;
	height: 35px;
	/*padding-top:5px;*/
	/*padding-bottom: 5px;*/
	background-color: #222;
	color: white;
	font-weight: bolder;
	font-size: 16px;
	font-family: proxima-nova,"Helvetica Neue",Helvetica,Arial,sans-serif;	
}

#controls {
	width: 400px;
	margin-top: 7px;
	display: inline-block;
	float: left;
}

#ReplayContainer {
	box-sizing: border-box;
	height: 100%;
	padding-top: 33px;
}

.pane {
	box-sizing: border-box;
	float:left;
	overflow: scroll;
	vertical-align: top;
	display: inline-block;
	height: 100%;
	margin: 0;
	background: none;
	border-right: solid 3px #ccc;
	border-width: 0 3px 0 0;
	word-wrap: break-word;
}

.code {
	padding: 10px;
	font-family: 'Inconsolata', sans-serif;
	font-size: 16px;
 	white-space: pre-wrap;
 	white-space: -moz-pre-wrap;
 	white-space: break-word;
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
 	float: right;
 	padding-right: 5px;
 	clear: left;
 }

 #scroll-wrap {
 	box-sizing: border-box;
 	height: 100%;
 	/*width: 40%;*/
 	margin-left: 400px;
 	/*margin-top: 7px;*/
 	/*padding: 3px;*/
 	background-color: #DEF1FC;
 	border: solid 3px #222;
 }

/* #scroll-wrap:hover {
 	height: 15px;
 }
*/
 #speed_range {
 	top: 2px;
 }

 #elapsed {
 	height: 100%;
 	width: 0%;
 	vertical-align: middle;
 	background-color: #2ba6cb;
 }
/*
 #elapsed:hover {
 	height: 100%;
 }*/

#date {
	margin-left: 20px;
}

 .top {
position: absolute;
}

 .button {
 	display: inline-block;
 	height: 20px;
 	width: 40px;
 	background-color: rgba(255, 255, 255, 0.35);
 	/*border: solid 1px rgba(255, 255, 255, 0.8);*/
 	border-radius: 5px;
 	/*padding-top: 5px;*/
 	margin-left: 5px;
 	text-align: center;
 	font-size: 14px;
    color: #FFF;
    cursor: pointer;
 }

 .button:hover {
 	color: #2ba6cb;
 	background-color: rgba(255, 255, 255, 0.98);
 }

 .button:active {
 	opacity: .5;
 }

</style>

</head>

<body>

<!-- buttons -->
<div id="top">
	<div id="controls">
		<span id="play" title="Play" class="button"> <i class="icon-play"></i> </span>
		<span id="skipBackward" class="button"><i class="icon-step-backward"></i></span>
		<span id="skipForward" class="button"><i class="icon-step-forward"></i></span>
		<!-- <span id="speeddown" title="Slow Down" class="button"><i class="icon-fast-backward"></i></span>
		<span id="speedup" title="Speed Up" class="button"><i class="icon-fast-forward"></i></span> -->
		<!-- Speed: <span id="speedval">5</span> || -->
		<span id="speed" class="button">1x</span>
		<!-- <input type="range" id="speed_range" min="0" max="50" step="1"  value="10" onChange="changeSpeed()"/> -->
		<span id="date">Date</span>
		<!-- <span id="special">Events</span> -->
	</div>

	<div id="scroll-wrap">
		<div id="current"></div>
		<div id="elapsed"></div>
	</div>
</div>



<div id="ReplayContainer">

	<pre id="cssReplay" class="pane code">
		CSS
	</pre><pre id="htmlReplay" class="pane code">
		HTML
	</pre>

	<div id="previewReplay" class="pane">
	</div>
</div>

<!-- script -->
<script type="text/javascript" src="<?php echo ROOT?>js/vendor/jquery.js"></script>
<script type="text/javascript" src="<?php echo ROOT?>js/vendor/jquery.scoped.js"></script>
<script type="text/javascript">

// Timer functions

//variables
var timer,
	i,
	t,
	speed,
	frame;


	t = 0;
	speed = 5;
	frame = 0;

//retrieve php variables
<?php

date_default_timezone_set('America/New_York');
$history = json_encode(retrieveReplay(mysql_real_escape_string($_GET['url'])));

?>

var history = <?php echo $history; ?>;
var end = history.length;

$("#play").toggle(function(){
	startTimer();
	$("#play").html("<i class='icon-pause'></i>");
}, function(){
	stopTimer();
	$("#play").html("<i class='icon-play'></i>");
});

$('#skipBackward').click(function(){

	if (frame > 1) {
		frame = frame - 2;
		t = (history[frame]['clock']);
		populate();
	}

});

$('#skipForward').click(function(){

	if (frame < history.length-1) {
		frame++;
		t = (history[frame]['clock']);
		populate();
	}

});

$("#speed").toggle(function(){
	speed = 50;
	$(this).text("10x");
}, function(){
	speed = 5;
	$(this).text("1x");
});

// $("#speedup").click(function(){
// 	speed += 5;
// 	document.getElementById("speedval").innerHTML = speed;
// });

// $("#speeddown").click(function(){
// 	speed -= 5;
// 	document.getElementById("speedval").innerHTML = speed;
// });

$("#scroll-wrap").click(function(pos) {

	var newpercent = ((pos.pageX-$(this).offset().left)/($(this).width()));
	t = newpercent*history[end-1]['clock'];

	for(index = 1; index < history.length; index++) {
		 if((t > history[index-1]['clock']) && (t <= history[index]['clock'])) {
			frame = index;
			update();
			break;
		}
	}

});

$('body').keyup(function(e) {

   if (e.keyCode == 32){
       // user has pressed space
       $('#play').click();
   }

   if (e.keyCode == 39){
       // user has pressed right arrow
       $('#skipForward').click();
   }

   if (e.keyCode == 37){
       // user has pressed left arrow
       $('#skipBackward').click();
   }

   if ((e.keyCode == 38) && ($('#speed').text() == "1x")) {
       // user has pressed up arrow
       $('#speed').click();
   }

   if ((e.keyCode == 40) && ($('#speed').text() == "10x")) {
       // user has pressed down arrow
       $('#speed').click();
   }

});

function startTimer() {
	timer = self.setInterval(addTime, 1);
	if (frame == history.length) {
		reset();
	};

}

function stopTimer(){
	self.clearInterval(timer);
}

function addTime(){

	t += speed;
	populate();
	// document.getElementById("t").innerHTML = t;
	// document.getElementById("time").innerHTML = (history[i+1]['clock']/1000);
	// document.getElementById("nextactive").innerHTML = ((history[i+1]['clock'])-(t*speed))/1000;
	// document.getElementById("play").value = (t*speed/1000);
	// document.getElementById("playval").innerHTML = (t*speed/1000);
	updateElapsed();
}

function reset(){
	t = -1;
	frame = 1;
	update();
}

function changeSpeed(){
	speed = document.getElementById("speed").value;
	document.getElementById("speedval").innerHTML = speed;
}

function populate(){

	if (t >= history[frame]['clock']){
	 	frame++;
	 	update();
	}

	// if((t) < history[frame]['clock']){
	//  	frame--;
	//  	update();
	// }

}

function update(){
			
	if (frame < history.length) {
		document.getElementById("cssReplay").innerHTML = history[frame]['css'];
	 	document.getElementById("htmlReplay").innerHTML = history[frame]['html'];
	 	document.getElementById("previewReplay").innerHTML = history[frame]['live'];
		document.getElementById("date").innerHTML = history[frame]['stamp'];

	 	// if(history[frame]['special']){
		 // 	document.getElementById("special").innerHTML = history[frame]['special'];
	 	// }

	 	$.scoped();
		updateElapsed();

	} else {
		$("#play").click();
	}
}

function updateElapsed(){
	var percent = ((t)/parseInt(history[end-1]['clock']))*100;
	if(percent >= 100) {percent = 100};
	$("#elapsed").css("width", percent+"%");
}

function html_entity_decode(str){
 var tarea = document.createElement('textarea');
 tarea.innerHTML = str; return tarea.value;
 tarea.parentNode.removeChild(tarea);
}

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

	$history = "";

	$sql = "SELECT session FROM replay_sessions WHERE url = '" . mysql_real_escape_string($url) . "' ORDER BY time ASC";
	$result = mysql_query($sql);

	if(!mysql_num_rows($result)){
		exit;
	}	
		
	while ($row = mysql_fetch_assoc($result, MYSQL_ASSOC)) {
		$history .= $row['session'];
	}	
	
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
