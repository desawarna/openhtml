<?php
	
	include('config.php'); // contains DB & important versioning

	$custom_name = $_POST['customName'];
	$url = $_POST['url'];
	$revision = $_POST['revision'];

  	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);    
  	mysql_select_db(DB_NAME, $link);

    $sql = sprintf('update sandbox set customname="%s" where url="%s" and revision="%s"', mysql_real_escape_string($custom_name), mysql_real_escape_string($url), mysql_real_escape_string($revision));
    mysql_query($sql);

?>