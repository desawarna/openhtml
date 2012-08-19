<?php 

include('functions.php');

if($_POST['member']) {
  
  include('config.php');
  include('auth.php'); // contains user auth

  $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
  mysql_select_db(DB_NAME, $link);

  $member = $_POST['member'];

  // check member is really in user's section
  $sql = sprintf('select section from ownership where name="%s"', mysql_real_escape_string($member));
  $result = mysql_query($sql);
  $row = mysql_fetch_object($result);

  if ($row->section == $_SESSION['name']) {
    $sql = sprintf('select * from owners where name="%s" order by url, revision desc', mysql_real_escape_string($member));
    $result = mysql_query($sql);
  }


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

    $output = $output . '<a href="' . $url . 'edit"><span' . ($firstTime ? ' class="first"' : '') . '>' . ($bin['customname'] ? $bin['customname'] : $bin['url']);

    $output = $output . '</span> <span class="revision">' . $bin['revision'] . '</span></a></td>';

    $output = $output . '<td class="created"><a pubdate="' . $bin['created'] . '"href="' . $url . 'edit">' . getRelativeTime($bin['created']) . '</a></td></tr>';

    print $output;

    $last = $bin['url'];
  } 
}

?>
