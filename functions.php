<?php

function formatURL($code_id, $revision) {
  $latest_revision = getMaxRevision($code_id);
  if ($revision != $latest_revision && $revision) {
    $code_id .= '/' . $revision;
  }
  $code_id_path = ROOT;
  if ($code_id) {
    // $code_id_path = ROOT . $code_id . '/';
    $code_id_path = $code_id . '/';
  }
  return $code_id_path;
}

function getMaxRevision($code_id) {
  $sql = sprintf('select max(revision) as rev from sandbox where url="%s"', mysql_real_escape_string($code_id));
  $result = mysql_query($sql);
  $row = mysql_fetch_object($result);
  return $row->rev ? $row->rev : 0;
}


function getRelativeTime($date) {
  $time = @strtotime($date);
  $diff = time() - $time;
  if ($diff<60)
    return $diff . " second" . plural($diff) . " ago";
  $diff = round($diff/60);
  if ($diff<60)
    return $diff . " minute" . plural($diff) . " ago";
  $diff = round($diff/60);
  if ($diff<24)
    return $diff . " hour" . plural($diff) . " ago";
  $diff = round($diff/24);
  if ($diff<7)
    return $diff . " day" . plural($diff) . " ago";
  $diff = round($diff/7);
  if ($diff<4)
    return $diff . " week" . plural($diff) . " ago";
  if (date('Y', $time) != date('Y', time())) 
    return date("j-M Y", $time);
  return date("j-M", $time);
}

function plural($num) {
	if ($num != 1)
		return "s";
}


?>