<?php
// if ( ! defined('ROOT')) exit('No direct script access allowed');
// function plural($num) {
//  if ($num != 1)
//    return "s";
// }



function getRelativeTime($date) {
  $time = @strtotime($date);
  // $diff = time() - $time;
  // if ($diff<60)
  //  return $diff . " second" . plural($diff) . " ago";
  // $diff = round($diff/60);
  // if ($diff<60)
  //  return $diff . " minute" . plural($diff) . " ago";
  // $diff = round($diff/60);
  // if ($diff<24)
  //  return $diff . " hour" . plural($diff) . " ago";
  // $diff = round($diff/24);
  // if ($diff<7)
  //  return $diff . " day" . plural($diff) . " ago";
  // $diff = round($diff/7);
  // if ($diff<4)
  //  return $diff . " week" . plural($diff) . " ago";
  //  if (date('Y', $time) != date('Y', time())) 
  //    return date("j-M Y", $time);
  // return date("j-M", $time);
  return date("n/j/y h:i a", $time);
}



//Get Sections

  if(isset($_POST['section'])){
    $name = $_POST['section'];
   
  }
  
  $query = "SELECT * FROM group_membership WHERE name='{$name}' ORDER BY id desc";
  $result = mysql_query($query);
  while ($section = mysql_fetch_object($result)) {
    $sections[] = $section->section;
  }

//Get users and docs
  $sql = sprintf('select * from ownership where section="%s" order by name', mysql_real_escape_string($sections[0]));
  $result = mysql_query($sql);
  while ($member = mysql_fetch_object($result)) {
    $members[] = $member->name;
  }

  $sql = sprintf('select * from owners where name="%s" order by url, revision desc', mysql_real_escape_string($members[0]));
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

?>
<!-- formatting  -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<title>openHTML - <?php echo $name ?>'s Dashboard</title>
<link rel="stylesheet" href="<?php echo ROOT?>css/style.css?<?php echo VERSION?>" type="text/css" />
<style>
/* Font via http://robey.lag.net/2010/06/21/mensch-font.html */
@font-face {
  font-family: 'MenschRegular';
  src: url('/openhtml/font/mensch-webfont.eot');
  src: url('/openhtml/font/mensch-webfont.eot?#iefix') format('eot'),
       url('/openhtml/font/mensch-webfont.woff') format('woff'),
       url('/openhtml/font/mensch-webfont.ttf') format('truetype'),
       url('/openhtml/font/mensch-webfont.svg#webfont0UwCC656') format('svg');
  font-weight: normal;
  font-style: normal;
}

body {
  font-family: MenschRegular, Menlo, Monaco, consolas, monospace;
  padding: 0;
  margin: 0;
  font-size: 13px;
  min-width: 976px;
  overflow-y: scroll;
}

#bins a {
  font-weight: normal;
  text-decoration: none;
  color: #000;
}

#bins a:hover {
  text-shadow: none;
}

.thumb {
  border: 1px solid #ccc;
  overflow: hidden;
  height: 145px;
  width: 193px;
  margin: 10px 0;
}

#iframe {
  width: 100%;
  height: 100%;
/*  -moz-transform:    scale(0.8);
  -moz-transform-origin: 0 0;
  -o-transform:      scale(0.8);
  -o-transform-origin: 0 0;
  -webkit-transform: scale(0.8);
  -webkit-transform-origin: 0 0;
  transform:         scale(0.8);
  transform-origin: 0 0;
  /* IE8+ - must be on one line, unfortunately */ 
  -ms-filter: "progid:DXImageTransform.Microsoft.Matrix(M11=0.8, M12=0, M21=0, M22=0.8, SizingMethod='auto expand')";
  
  /* IE6 and 7 */ 
  filter: progid:DXImageTransform.Microsoft.Matrix(
           M11=0.8,
           M12=0,
           M21=0,
           M22=0.8,
           SizingMethod='auto expand');
  overflow: visible;*/
}

#bins {
  width: 35%;
  font-size: 13px;
  padding: 10px 0;
  position: relative;
  margin-top: 51px;
}

#preview {
  border-left: 1px solid #ccc;
  position: fixed;
  top: 0;
  width: 65%;
  right: 0;
  height: 100%;
  padding-top: 10px;
  margin-top: 51px;
  background: #fff;
}

h2 {
  margin: 0;
  font-size: 14px;
  font-family: "Helvetica Neue", Helvetica, Arial;
  font-size: 13px;
  padding: 0 20px;
}

#bins h2 {
  margin-bottom: 10px;
}

#bins table {
  border-collapse: collapse;
  table-layout: fixed;
  width: 100%;
  position: relative;
}

#bins td {
  margin: 0;
  padding: 3px 0;
}

#bins .url {
  text-align: right;
  width: 70%;
  padding-left: 20px;
  padding-right: 20px;
}

#bins .url .revision {
  color: #0097fe;
  visibility: visible;
}

#bins .url a span {
  color: #000;
  visibility: hidden;
}

#bins .url span.first {
  visibility: visible;
}

#bins .rename {
  cursor: pointer;
  color: #ddd !important;
}

#bins .created {
  width: 30%;
}

#bins .created a {
  color: #ccc;
}

#bins .title {
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}

#bins tr:hover *,
#bins tr.hover *,
#bins tr:hover span,
#bins tr.hover span,
#bins tr.hover span.revision {
  background: #0097fe;
  color: #fff;
  /*cursor: pointer;*/
}

#bins tr[data-type=spacer]:hover * {
  background: #fff;
  cursor: default;
}

iframe {
  border: 0;
  display: block;
  margin: 0 auto;
  width: 90%;
}

#viewing {
  font-size: 10px;
  margin-left: 20px;
}

.action {
  cursor: pointer;
}

/* for bar */

#control {
  top: 0;
  font-family: "Helvetica Neue", Helvetica, Arial;
  position: fixed;
  
  background: url(/openhtml/images/jsbin-bg.gif) repeat-x 0 -10px;
  background-attachment:fixed;
}

#control .members {
  margin-left: 20px;
  padding-top: 2px;
}


</style>
</head>
<body class="list">
<div id="control">
  <div class="control">
    <div class="buttons">
      

      <!-- Dropdown for sections -->
      <span class="members">Section:</span>
      <span class="members">
          <?php

          if (!empty($sections)) {
            echo '<select id="sections">';

            foreach ($sections as $section) {
              echo '<option value="' .$section. '">' .$section. '</option>';
            }
            echo '</select>';

          } else {
            echo '<select id="sections"><option>No Sections</option></select>';
          }
          ?>
        </select>
      </span>


      <!-- Dropdown for users -->
      <span class="members">Users: </span>
      <span class="members">
          <?php

          if (!empty($members)) {
           echo '<select id="users">';

            foreach ($members as $member) {
              echo '<option value="' .$member. '">' .$member. '</option>';
            }
            echo '</select>';

          } else {
            echo '<select id="users"><option>No users</option></select>';
          }
          ?>
        </select>
      </span>
      <div id="userinfo">
          <a id="account" class="button group light left" href="<?php echo ROOT?>list">Page List<?php //echo $is_owner?></a> 
          <div class="button group gap right tall">
            <a id="admin" class="title" href="#"><?php echo $_SESSION['name']; ?></a>
            <a id="dashboard" title="Dashboard" class="button light group" href="<?php echo ROOT?>dashboard">Dashboard</a>
            <a id="change" title="Change Password" class="button light group" href="<?php echo ROOT?>changepassword">Password</a>
            <a id="logout" title="Logout" class="button group light" href="<?php echo ROOT?>logout">Logout</a>
          </div>
          <span id="logo">openHTML</span>
      </div>
    </div>
  </div>
</div>
<div id="bins">
<table>
<tbody>

  <?php
  $last = null;
  arsort($order);
  foreach ($order as $key => $value) {
    foreach ($bins[$key] as $bin) {
      if ($bin == null) {break;}
      $code = $bin['url'];
      $revision = $bin['revision'];
      $customName = $bin['customname'];

      $url = formatURL($bin['url'], $bin['revision']);
      preg_match('/<title>(.*?)<\/title>/', $bin['html'], $match);
      preg_match('/<body.*?>(.*)/s', $bin['html'], $body);
      $title = '';
      if (count($body)) {
        $title = $body[1];
        if (get_magic_quotes_gpc() && $body[1]) {
          $title = stripslashes($body[1]);
        }
        $title = trim(preg_replace('/\s+/', ' ', strip_tags($title)));
      }
      if (!$title && $bin['javascript']) {
        $title = preg_replace('/\s+/', ' ', $bin['javascript']);
      }

      if (!$title && count($match)) {
        $title = get_magic_quotes_gpc() ? stripslashes($match[1]) : $match[1];
      }

      $firstTime = $bin['url'] != $last;

      if ($firstTime && $last !== null) : ?>
    <tr data-type="spacer"><td colspan=3></td></tr>
      <?php endif ?>
    <tr data-url="<?=$url?>" <?=($firstTime ? ' class="parent" id="' : ' class="child ')  . $code . '">' ?>
      <td class="url"><?=($firstTime && $revision > 1) ? '<span class="action">▶</span> ': '<span class="inaction">&nbsp;</span>'?> <a href="<? echo ROOT . $url?>edit"><span<?=($firstTime ? ' class="first"' : '') . '>' . ($bin['customname'] ? $bin['customname'] : $bin['url']) ?></span> <span class="revision"><?=$bin['revision']?></span></a></td>
      <td class="created"><a pubdate="<?=$bin['created']?>" href="<? echo ROOT . $url?>edit"><?=getRelativeTime($bin['created'])?></a></td>
      <!--<td class="title"><a href="<?=$url?>edit"><?=substr($title, 0, 200)?></a></td>-->
    </tr>
  <?php
      $last = $bin['url'];
    } 
  } ?>

</tbody>
</table>
</div>
<div id="preview">
<h2><span id="view">Preview</span><span id="download"></span></h2>

<p id="viewing"></p>

<iframe id="iframe" hidden></iframe>
</div>
<script type="text/javascript" src="<?php echo ROOT?>js/vendor/jquery.js"></script>
<script type="text/javascript">


function collapsePages() {

  $('.child').hide();
  $('.rename').hide();

  $('.action').click(function(){
      var id = $(this).closest('.parent').attr('id');
      $("."+id).toggle();
      if($(this).html() == '▶') {
        $(this).html('▼');
      } else {
        $(this).html('▶');
      }
  });
}

collapsePages();

function render(url) {
  iframe.src = url + 'quiet';
  iframe.removeAttribute('hidden');
  view.innerHTML = '<a href="<? echo ROOT ?>'+url+'">Preview</a>';
  download.innerHTML = ' | <a href='+url+'downloadsingle>Download</a>';
  viewing.innerHTML = '<?=$_SERVER['HTTP_HOST']?><?=ROOT?>' + url;
}

function matchNode(el, nodeName) {
  if (el.nodeName == nodeName) {
    return el;
  } else if (el.nodeName == 'BODY') {
    return false;
  } else {
    return matchNode(el.parentNode, nodeName);
  }
}

function removeHighlight() {
  // var i = trs.length;
  // while (i--) {
  //   // trs[i].className = '';
  // }

  $('.hover .rename').hide();
  $('.hover').removeClass('hover');
}

function visit() {
  window.location = this.getAttribute('data-url') + 'edit';
}

var preview = document.getElementById('preview'),
    iframe = document.getElementById('iframe');
    bins = document.getElementById('bins'),
    trs = document.getElementsByTagName('tr'),
    current = null,
    download = document.getElementById('download'),
    view = document.getElementById('view'),
    viewing = document.getElementById('viewing'),
    hoverTimer = null;

// this is some nasty code - just because I couldn't be
// bothered to bring jQuery to the party.
bins.onmouseover = function (event) {
  clearTimeout(hoverTimer);
  event = event || window.event;
  var url, target = event.target || event.srcElement;
  if (target = matchNode(event.target, 'TR')) {
    removeHighlight();
    if (target.getAttribute('data-type') !== 'spacer') {
      // target.className = 'hover';
      $('.rename', target).show();
      $(target).addClass('hover');


      // target.onclick = visit;
      url = target.getAttribute('data-url');
      if (current !== url) {
        hoverTimer = setTimeout(function () {
          current = url;
          render(url);
        }, 200);
      }
    }
  }
};

$('#users').change(function(){
  var member = $(this).val();

  $.ajax({
    type: "POST",
    url: "<?php echo ROOT ?>list-dashboard-code.php",
    data: 'member=' + member,
    dataType: "html",
    cache: false,
    success: function(i) {
      $("#bins tbody").html(i);
      collapsePages();

    } 
  });
});

$('#sections').change(function(){
  var section = $(this).val();

  $.ajax({
    type: "POST",
    url: "<?php echo ROOT ?>list-dashboard-code.php",
    data: 'section=' + section,
    dataType: "html",
    cache: false,
    success: function(i) {

        $('#users').html(i).change();
    } 
  });
});



</script>
</body>
</html>
