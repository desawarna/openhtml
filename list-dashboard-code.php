<?php 


if(isset($_POST['section'])){
  include('config.php');
  include('auth.php'); // contains user auth

  $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
  mysql_select_db(DB_NAME, $link);

   
    $section_name = $_POST['section'];
    $_SESSION['section'] = $_POST['section'];

  
  $query = "SELECT name FROM ownership WHERE section='{$section_name}'";
  $result = mysql_query($query);

  while($member = mysql_fetch_array($result)){
    $members[] = $member['name'];
  }
  
 
          if (!empty($members)) {            
            foreach ($members as $member) {
              echo '<option value="' .$member. '">' .$member. '</option>';
            }            
          } 
          else {
            echo '<option>No users</option>';
          }
  
 exit(); 
}

if(isset($_POST['member'])) {
  
  include('config.php');
  include('auth.php'); // contains user auth


  $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
  mysql_select_db(DB_NAME, $link);

  $member = $_POST['member'];

  // check member is really in user's section
  $sql = sprintf('select section from ownership where name="%s"', mysql_real_escape_string($member));
  $result = mysql_query($sql);
  $row = mysql_fetch_object($result);


    $sql = sprintf('select * from owners where name="%s" order by url, revision desc', mysql_real_escape_string($member));
    $result = mysql_query($sql);
  


  $bins = array();
  $order = array();

  while ($saved = mysql_fetch_object($result)) {


    $sql = sprintf('select * from sandbox where url="%s" and revision="%s"', mysql_real_escape_string($saved->url), mysql_real_escape_string($saved->revision));
    $binresult = mysql_query($sql);
    $bin = mysql_fetch_array($binresult);

    if (!isset($bins[$saved->url])) {
      $bins[$saved->url] = array();
    }

    $bins[$saved->url][] = $bin;

    if (isset($order[$saved->url])) {
      if (@strtotime($order[$saved->url]) < @strtotime($bin['created'])) {
        $order[$saved->url] = $bin['created'];
      }
    } else {
      $order[$saved->url] = $bin['created'];
    }
  }
} 


$last = null;
arsort($order);
foreach ($order as $key => $value) {
  foreach ($bins[$key] as $bin) {

    $output = '';

    if ($bin == null) {break;}
    $code = $bin['url'];
    $revision = $bin['revision'];
    $customName = $bin['customname'];


    $url = formatURL($bin['url'], $bin['revision']);


    $firstTime = $bin['url'] != $last;


    if ($firstTime && $last !== null) {
      $output = $output . '<tr data-type="spacer"><td colspan=3></td></tr>';
    }

    $output = $output . '<tr data-url="' . $url . '"' . ($firstTime ? ' class="parent" id="' : ' class="child ') . $code . '">';

    $output = $output . '<td class="url">' . (($firstTime && $revision > 1) ? '<span class="action">▶</span> ': '<span class="inaction">&nbsp;</span>');

    $output = $output . '<a href="' . ROOT . $url . 'edit"><span' . ($firstTime ? ' class="first"' : '') . '>' . ($bin['customname'] ? $bin['customname'] : $bin['url']);

    $output = $output . '</span> <span class="revision">' . $bin['revision'] . '</span></a></td>';

    $output = $output . '<td class="created"><a pubdate="' . $bin['created'] . '"href="' . ROOT . $url . 'edit">' . getRelativeTime($bin['created']) . '</a></td></tr>';

    print $output;

    $last = $bin['url'];
  } 
}

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
  // $diff = time() - $time;
  // if ($diff<60)
  //   return $diff . " second" . plural($diff) . " ago";
  // $diff = round($diff/60);
  // if ($diff<60)
  //   return $diff . " minute" . plural($diff) . " ago";
  // $diff = round($diff/60);
  // if ($diff<24)
  //   return $diff . " hour" . plural($diff) . " ago";
  // $diff = round($diff/24);
  // if ($diff<7)
  //   return $diff . " day" . plural($diff) . " ago";
  // $diff = round($diff/7);
  // if ($diff<4)
  //   return $diff . " week" . plural($diff) . " ago";
  // if (date('Y', $time) != date('Y', time())) 
  //   return date("j-M Y", $time);
  // return date("j-M", $time);
  return date("n/j/y h:i a", $time);
}

function plural($num) {
	if ($num != 1)
		return "s";
}

?>
