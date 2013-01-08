<!DOCTYPE html>
<?php

//loginto sql

include('config.php'); // contains DB & important versioning
// include('logger.php'); // contains activity logger

include('auth.php'); // contains user auth
$log = new logmein(); // instantiate the class
$log->dbconnect();  // connect to the database
$log->encrypt = true;   // set to true if password is md5 encrypted. Default is false.
// parameters are (SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck(@$_SESSION['loggedin'], "ownership", "key", "name") == false){
    //do something if NOT logged in. For example, redirect to login page or display message.

  $pre = '<!DOCTYPE html><html><head><title>openHTML - Login</title><link rel="stylesheet" href="' . ROOT . 'css/style.css" type="text/css" /></head><body><div id="control"><div class="control"><div class="buttons"><div id="auth"><span id="logo">openHTML</span></div></div></div></div><div id="bin" class="stretch">';

  $post = '</div></body></html>';

  $log->loginform("loginformname", "loginformid", ROOT."login.php", $pre, $post);

  die();
}else{
    //do something else if logged in.

}
?>

<!-- containers / styling -->
<head>
<!-- style -->
<style type="text/css">

#cssReplay, #htmlReplay {
	float: left;
	width: 500px;
	background-color: #c0c0c0;
	border-right: dotted;
	border-width: 1px;
	padding-left: 10px;
	margin: 10px;

 }
</style>

<!-- script -->
<script>

// Timer functions

//variables
var t, timer, i, speed;
t = -1;
i = 0;
speed = 10;

<?php $history = retrieveReplay("agugay"); $js_history = json_encode($history); ?>
	var history = <?php echo $js_history; ?>;

function startTimer(){
	timer = self.setInterval("addTime()", 1)
}

function stopTimer(){
	clearInterval(timer);
	timer = null;
}

function addTime(){
	t++;
	populate();
	document.getElementById("frame").innerHTML = t;
	
}

function skip(){
	i++;
	t = history[i]['time']/speed;
	t = (history[i]['time'])/speed;
	populate();
	
}

function reset(){
	t = -1;
	i = 0;
	stopTimer();
	document.getElementById("cssReplay").innerHTML = history[i]['css'];
	document.getElementById("htmlReplay").innerHTML = history[i]['html'];
	document.getElementById("special").innerHTML = history[i]['special'];
	
}

function changeSpeed(){
	speed = document.getElementById("speed").value;
	document.getElementById("speedval").innerHTML = speed;
}


function populate(){
	 if((t*speed) >= history[i]['time']){
	 	document.getElementById("cssReplay").innerHTML = history[i]['css'];
	 	document.getElementById("htmlReplay").innerHTML = history[i]['html'];
	 	document.getElementById("special").innerHTML = history[i]['special'];
	 	i++;
	 }

function update(){
		document.getElementById("cssReplay").innerHTML = history[i]['css'];
	 	document.getElementById("htmlReplay").innerHTML = history[i]['html'];
	}
}

</script>


</head>

<!-- buttons -->
<div id="top">
	<button value=start name=start onClick="startTimer()">Start</button>
	<button type=button value=stop name=stop onClick="stopTimer()">Stop</button>
	<button type=button value=stop name=stop onClick="skip()">Skip</button>
	<button type=button value=stop name=stop onClick="reset()">Reset</button>
	Speed: <input type="range" id="speed" min="0" max="50" step="1"  value="10" onChange="changeSpeed()"/><span id="speedval">10</span> ||
	Frame: <span id="frame">0</span>

</div>
<div id="ReplayContainer">

	<pre id = "cssReplay">
		CSS
	</pre>

	<pre id = "htmlReplay">
		HTML
	</pre>

	<pre id = "special">
		Events
	</pre>

</div>







<?php

//debug
var_dump($js_history);


//Retrieves replay history from the database
function retrieveReplay($url){

	$sql = "SELECT * FROM replay WHERE url = '" . mysql_escape_string($url) . "' ORDER BY time ASC";
	$result = mysql_query($sql);
		

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$history[] = $row;
		
	}

	$history = formatReplay($history);
	return $history;
}


//Accepts array of replay history in ascending order to format the timestamps for replay or any other formatting which may be required in the future
function formatReplay($data){

	$origTime = $data[0][time];

	foreach($data as $key => $value){

		$data[$key][time] -= $origTime;
	}

	return $data;
}
?>