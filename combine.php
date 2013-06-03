<?php

	set_time_limit(0);
	ini_set("memory_limit", "400M");

//Variables
	$pages = array(); // Contains a Row each entry which includes consolidated data for each page from the old data set
	$combined = array();
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
		
		if ($r["url"] == "") continue;
		$url = $r["url"];

// Add JSON to row once all input has been combined for a single url
		if(($i != 0 && $purl != $url) || $i > 200){
			// $combined = substr_replace($combined, "", -2);
			$json = json_encode($combined);
			$pages[] = array(
								"url" => $purl,
								"time" => $ptime,
								"session" => $json
							);
			unset($combined);
			$i = 0;
		}

		$combined[] = array(
			'clock' => $r["time"],
			'html' => $r["html"],
			'css' => $r["css"],
			'special' => $r["special"]
			);
		$ptime = $r["time"];
		$purl = $r["url"];
		$i++;
	}


//Export Sorted Data into a duplicate table to merge old data set and new data set
	mysql_query("CREATE TABLE replay_combined LIKE replay_sessions");
	mysql_query("INSERT INTO replay_combined SELECT * FROM replay_sessions");

	echo "Number of rows in Old Data: " . count($pages) . "\n";

	foreach ($pages as $key => $value) {
		// if ($pages[$key]['url'] == "") $pages[$key]['url'] = "UNDEFINED";
		echo $key . ": " . var_dump($value) . "\n";
		
		$time = substr($value['time'], 0, 10);
		$query = sprintf("INSERT INTO replay_combined (url, time, session) VALUES ('%s', '%s', '%s')", mysql_real_escape_string($value['url']), mysql_real_escape_string($time), mysql_real_escape_string($value['session']) );
		// $query = "INSERT INTO replay_combined (url, time, session) VALUES ('{$value['url']}', '{$value['time']}', '{$value['session']}')";

		 // VALUES ({$pages[$key]['url']}, {$pages[$key]['time']}, {$pages[$key]['session']})
		$run = mysql_query($query);
		mysql_ping();
		// echo $query;
	}



	
	// var_dump($pages);
?>

































