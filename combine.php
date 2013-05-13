<?php

//Variables
	$pages = array(); // Contains a Row each entry which includes consolidated data for each page from the old data set
	$combined = "[";
	$url = "";
	$purl = "x";
	$i = 0;

//Connect to DB

	include('config.php'); // contains DB & important versioning

	include('auth.php'); // contains user auth
	$log = new logmein(); // instantiate the class
	$log->dbconnect();  // connect to the database
	$log->encrypt = true;   // set to true if password is md5 encrypted. Default is false.

	$q = "SELECT * FROM replay ORDER BY url, time";
	$s = mysql_query($q);
	// $r = mysql_fetch_assoc($s);

	while($r = mysql_fetch_assoc($s, MYSQL_ASSOC)){
		// echo $r[url]."-".$r[customname]."-".$r[time]."-".$r[html]."-".$r[css]."-".$r[special]."***<br />";
		// echo '{
		// 		"clock":'		. 	$r[time] . ', ' .
		// 		'"html":'		. 	$r["html"] . ', ' .
		// 		'"css":' 		. 	$r["css"] . ', ' .
		// 		'"special":'	.	$r["special"] . '} <br>';
		$url = $r["url"];

// Add JSON to row once all input has been combined for a single url
		if($i != 0 && $purl != $url){
			$combined = substr_replace($combined, "", -2);
			$combined .= "]";
			$pages[$purl] = array(
									"url" => $purl,
									"time" => $ptime,
									"session" => $combined
								);

			$combined = "[";
		}

		$combined .= '{
				"clock":"'		. 	$r["time"] . '", ' .
				'"html":"'		. 	$r["html"] . '", ' .
				'"css":"' 		. 	$r["css"] . '", ' .
				'"special":"'	.	$r["special"] . '"}, ';
		$ptime = $r["time"];
		$purl = $r["url"];
		$i++;
	}

	$combined .= "]";

//Export Sorted Data into a duplicate table to merge old data set and new data set
	mysql_query("CREATE TABLE replay_combined LIKE replay_sessions");
	mysql_query("INSERT INTO replay_combined SELECT * FROM replay_sessions");

	foreach ($pages as $key => $value) {
		if ($pages[$key]['url'] == "") $pages[$key]['url'] = "UNDEFINED";
		 $query = "INSERT INTO replay_combined (url, time, session) VALUES ('{$pages[$key]['url']}', '{$pages[$key]['time']}', '{$pages[$key]['session']}')";

		 // VALUES ({$pages[$key]['url']}, {$pages[$key]['time']}, {$pages[$key]['session']})
		$run = mysql_query($query);
		echo $query;
	}



	
	// var_dump($pages);
?>

































