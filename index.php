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
<title>openHTML!!!!</title>
<link rel="stylesheet" href="<?php echo ROOT?>css/style.css?<?php echo VERSION?>" type="text/css" />
</head>
<!--[if lt IE 7 ]><body class="source ie ie6"><![endif]-->
<!--[if lt IE 8 ]><body class="source ie ie7"><![endif]-->
<!--[if gte IE 8 ]><body class="source ie"><![endif]-->
<!--[if !IE]><!--><body class="source"><!--<![endif]-->
<div id="control">
  <div class="control">
    <div class="buttons">
      <a id="account" class="tab button group light left" href="<?php echo ROOT?>list">Page List<?php echo $is_owner?></a>
      <a id="account" class="tab button group light right gap" href="<?php echo ROOT?>">New Page</a>
      <!--<a class="tab button source group left" accesskey="1" href="#source">Code</a>
      <a class="tab button preview group right gap" accesskey="2" href="#preview">Preview</a>-->
      <a title="Revert" class="button light group left" id="revert" href="#"><img class="enabled" src="<?php echo ROOT?>images/revert.png" /><img class="disabled" src="<?php echo ROOT?>images/revert-disabled.png" /></a>
    <?php if ($code_id) : ?>
      <a id="jsbinurl" target="<?php echo $code_id?>" class="button group light left" href="http://<?php echo $_SERVER['HTTP_HOST'] . ROOT . $code_id?>"><?php echo $_SERVER['HTTP_HOST'] . ROOT . $code_id ?></a>


						<?php if ($ownership) :?>
			     	 <div class="button group gap right tall">
				        <a href="<?php echo ROOT?>save" class="save title">Save</a>
				        <a id="save" title="Save a new revision" class="button light save group" href="<?php echo $code_id_path?>save">Save</a>
				        <a id="clone" title="Create a new copy" class="button clone group light" href="<?php echo ROOT?>clone">Copy</a>

	      		<?php else : ?>

				     	 <div class="button group gap right short">
				        <a title="Create a new copy" class="clone title" href="<?php echo ROOT?>clone">Copy</a>
				        <a id="clone" title="Create a new copy" class="button clone group light" href="<?php echo ROOT?>clone">Copy</a>

						<?php endif ?>
      <?php else : ?>
        <div class="button group gap left right">
          <a href="<?php echo ROOT?>save" class="save title">Save</a>
          <a id="save" title="Save new bin" class="button save group" href="<?php echo ROOT?>save">Save</a>
      <?php endif ?>
          <a id="download" title="Save to drive" class="button download group light" href="<?php echo ROOT?>download">Download</a>
          <!-- <a id="startingpoint" title="Set as starting code" class="button group" href="<?php echo ROOT?>save">As template</a> -->
      </div>

      <span id="panelsvisible" class="gap">View:
        <input type="checkbox" data-panel="javascript" data-uri="css" id="showjavascript"><label for="showjavascript">CSS</label>
        <input type="checkbox" data-panel="html" data-uri="html" id="showhtml"><label for="showhtml">HTML</label>
        <input type="checkbox" data-panel="live" data-uri="live" id="showlive"><label for="showlive">Live</label>
      </span>

      <div id="userinfo">
        <a id="account" class="button group light left" href="<?php echo ROOT?>list"><?php echo $_SESSION['name']; ?></a>
        <a id="logout" class="button group light right" href="<?php echo ROOT?>logout">Logout</a>
      <span id="logo">openHTML</span>

    </div>
    </div>
  </div>


</div>
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
  <form method="post" action="<?php echo $code_id_path?>save">
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
<script src="<?php echo ROOT?>js/<?php echo VERSION?>/jsbin.js"></script>

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
