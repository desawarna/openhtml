<?php

include('app.php');

if (false && (@$_POST['html'] || @$_POST['javascript'])) {
  $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
  if (@$_POST['html']) {
    $html = str_replace($jsonReplaces[0], $jsonReplaces[1], $_POST['html']);
  } else {
    $html = '';
  }
  if (@$_POST['javascript']) {
    $javascript = str_replace($jsonReplaces[0], $jsonReplaces[1], $_POST['javascript']);
  } else {
    $javascript = '';
  }

  if ($html == '') {
    // if there's no HTML, let's pop some simple HTML in place to give the JavaScript
    // some context to run inside of
    list($latest_revision, $defhtml, $defjavascript) = getCode($code_id, $revision, true);
    $html = $defhtml;
  }
} else {
  list($code_id, $revision) = getCodeIdParams($request);

  $edit_mode = false;

  if ($code_id) {
    list($latest_revision, $html, $javascript) = getCode($code_id, $revision, true);
  } else {
    list($latest_revision, $html, $javascript) = defaultCode();
  }
}

$latest_revision = getMaxRevision($code_id);
if ($revision != $latest_revision && $revision) {
  $code_id .= '/' . $revision;
}
$code_id_path = ROOT;
if ($code_id) {
  $code_id_path = ROOT . $code_id . '/';
}

$ownership = checkOwner($code_id, $revision, $_SESSION['name']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<meta name="robots" content="noindex">
<title>openHTML</title>
<!-- <link rel="stylesheet" href="<?php echo ROOT?>css/foundation.min.css?<?php echo VERSION?>" type="text/css" /> -->
<link rel="stylesheet" href="<?php echo ROOT?>css/style.css?<?php echo VERSION?>" type="text/css" />
</head>
<!--[if lt IE 7 ]><body class="source ie ie6"><![endif]-->
<!--[if lt IE 8 ]><body class="source ie ie7"><![endif]-->
<!--[if gte IE 8 ]><body class="source ie"><![endif]-->
<!--[if !IE]><!--><body class="source"><!--<![endif]-->


<!-- top panel -->
<?php 
  connect();
  $username = $_SESSION['name'];
  $query = "select * from group_membership where name='{$username}'";
  $result = mysql_fetch_array(mysql_query($query));

  if($result){$dash = 1;} else {$dash=0;}
  include("nav.php"); 

?>

<!-- text area -->
<div id="bin" class="stretch" style="opacity: 0; filter:alpha(opacity=0);">
  <div id="source" class="binview stretch">
    <div class="code stretch javascript">
      <div class="label"><p><strong id="jslabel">CSS</strong></p></div>
      <div class="editbox">
        <textarea spellcheck="false" autocapitalize="off" autocorrect="off" id="javascript"></textarea>
      </div>
    </div>
    <div class="code stretch html">
      <div class="label"><p>HTML</p></div>
      <div class="editbox">
        <textarea spellcheck="false" autocapitalize="off" autocorrect="off" id="html"></textarea>
      </div>
    </div>
  </div>
  <div id="live" class="stretch livepreview"><a href="<?php echo ROOT ?>live" target="_new" id="popout" class="popout button light left right">Pop out</a></div>
  <div id="preview" class="binview stretch"></div>
  <form id="saveform" method="post" action="<?php echo $code_id_path?>save">
    <input type="hidden" name="method" />
  </form>

  <!-- sends validation data -->
<form id="validateform" action="<?php echo ROOT ?>validate.php" target="_blank" method="post">
  <input type="hidden" name="method" />
</form>

</div>


<script>

<?php
  // assumes http - if that's not okay, this need to be changed
  $latest_revision = getMaxRevision($code_id);
  $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $code_id . ($revision == $latest_revision ? '' : '/' . $revision);
  if (!$ajax) {
    echo 'var template = ';
  }
  // doubles as JSON
  echo '{"url":"' . $url . '","html" : ' . encode($html) . ',"javascript":' . encode($javascript) . '}';
?>
</script>
<script>jsbin = { root: "<?php echo HOST ?>", version: "<?php echo VERSION?>" }; tips = <?php echo file_get_contents('tips.json')?>;</script>
<script src="<?php echo ROOT?>js/<?php echo VERSION?>/jsbin.js">
</script>

<!-- <script>
    window.onbeforeunload = function() {
        if (document.title.substr(-9) == '[unsaved]'){
          return 'You have unsaved changes. You should save first.';
        }
      };
</script> -->
<?php if (!OFFLINE) : ?>
<script>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-26530551-1']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>
<?php endif ?>
</body>
</html>
