var $revert = $('#revert'); //.next().addClass('left').end();
var warn_on_unload = null;

$(document).bind('codeChange', function (event, revert, onload) {
  // if (revert == undefined) {
  //   revert = false;
  // } else {
  //   $revert.removeClass('enable');
  // }
  
  updateTitle(revert, onload);
});

$(window).bind('beforeunload', function(){
    return warn_on_unload;
});

$('.save').click(function(){
  warn_on_unload = null;
});

function updateTitle(revert, onload) {
  var title = !documentTitle ? 'openHTML' : documentTitle;
  // if (jsbin.settings.home) title = jsbin.settings.home + '@' + title;
  if (editors.html.ready && editors.javascript.ready) {
    if (!revert) {
      $('#save').addClass('unsaved');
      document.title = title + ' [unsaved]';
      warn_on_unload = "You have unsaved changes!";
      // if ($revert.addClass('enable').is(':hidden')) {
      //   $revert[onload ? 'show' : 'fadeIn']().next().removeClass('left');
      // }
    } else {
      document.title = title;
      
    }
  }
}
