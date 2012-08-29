var $revert = $('#revert'); //.next().addClass('left').end();
$(document).bind('codeChange', function (event, revert, onload) {
  // if (revert == undefined) {
  //   revert = false;
  // } else {
  //   $revert.removeClass('enable');
  // }
  
  // updateTitle(revert, onload);
});

// $(window).bind('beforeunload', function(revert){
//   if (document.title.indexOf('[unsaved]') != -1){
//     return 'You should stay and save your page first.\n';
//   }
// });

function updateTitle(revert, onload) {
  var title = !documentTitle ? 'openHTML' : documentTitle;
  // if (jsbin.settings.home) title = jsbin.settings.home + '@' + title;
  if (editors.html.ready && editors.javascript.ready) {
    if (!revert) {
      document.title = title + ' [unsaved]';
      if ($revert.addClass('enable').is(':hidden')) {
        $revert[onload ? 'show' : 'fadeIn']().next().removeClass('left');
      }
    } else {
      document.title = title;
    }    
  }
}
