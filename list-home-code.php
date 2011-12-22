<?php if ( ! defined('ROOT')) exit('No direct script access allowed');
function plural($num) {
	if ($num != 1)
		return "s";
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<title>openHTML - <?php echo $name ?>'s Pages</title>
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
  width: 70%;
  font-size: 13px;
  padding: 10px 0;
  position: relative;
  margin-top: 51px;
}

#preview {
  border-left: 1px solid #ccc;
  position: fixed;
  top: 0;
  width: 30%;
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
  width: 25%;
  padding-left: 20px;
  padding-right: 20px;
}

#bins .url a {
  color: #0097fe;
}

#bins .url a span {
  color: #000;
  visibility: hidden;
}

#bins .url span.first {
  visibility: visible;
}

#bins .created {
  width: 25%;
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
#bins tr.hover span {
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


</style>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script type="text/javascript">
        $().ready(function() {
            $('.child').hide();
            $('.action').click(function(){
                var id = $(this).closest('.parent').attr('id');
                $("."+id).toggle();
                if($(this).html() == '▶') {
                  $(this).html('▼');
                } else {
                  $(this).html('▶');
                }
            });
        });

    </script>
</head>
<body class="list">
<div id="control"><div class="control">
    <div class="buttons">

    <a class="tab button source group left" accesskey="1" href="./">New Page</a>
    <div id="userinfo">
        <a id="account" class="button group light left list" href="<?php echo ROOT?>list"><?php echo $_SESSION['name']; ?></a>
        <a id="logout" class="button group light right" href="<?php echo ROOT?>logout">Logout</a><span id="logo">openHTML</span>
    </div></div></div></div>
<div id="bins">
<h2><?php echo $name ?>'s Pages</h2>
<table>
<tbody>
<?php 
$last = null;
arsort($order);
foreach ($order as $key => $value) {
  foreach ($bins[$key] as $bin) {
    $code = $bin['url'];
    $revision = $bin['revision'];
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
    <td class="url"><?=($firstTime && $revision > 1) ? '<span class="action">▶</span>': ''?> <a href="<?=$url?>edit"><span<?=($firstTime ? ' class="first"' : '') . '>' . $bin['url']?>/</span><?=$bin['revision']?>/</a></td>
    <td class="created"><a pubdate="<?=$bin['created']?>" href="<?=$url?>edit"><?=getRelativeTime($bin['created'])?></a></td>
    <td class="title"><a href="<?=$url?>edit"><?=substr($title, 0, 200)?></a></td>
  </tr>
<?php
    $last = $bin['url'];
  } 
} ?>
</tbody>
</table>
</div>
<div id="preview">
<h2>Preview</h2>
<p id="viewing"></p>
<iframe id="iframe" hidden></iframe>
</div>
<script>
function render(url) {
  iframe.src = url + 'quiet';
  iframe.removeAttribute('hidden');
  viewing.innerHTML = '<?=$_SERVER['HTTP_HOST']?>' + url;
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
</script>
</body>
</html>
