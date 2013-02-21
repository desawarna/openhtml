//= require "codemirror"
//= require "mobileCodeMirror"
//= require "library"
//= require "unsaved"
var focusPanel = 'javascript';
var editors = {};
var activity;
// var row = {};
var sql = new Array();

window.editors = editors;

editors.html = CodeMirror.fromTextArea(document.getElementById('html'), {
  parserfile: [],
  tabMode: 'shift',
  mode: 'text/html',
  onChange: changecontrol,
  theme: "eclipse",
  indentUnit: 4,
  lineNumbers: true
});

editors.javascript = CodeMirror.fromTextArea(document.getElementById('javascript'), {
  mode: 'css',
  tabMode: 'shift',
  onChange: changecontrol,
  theme: "eclipse",
  matchBrackets: true,
  indentUnit: 4,
  lineNumbers: true
});

setupEditor('javascript');
setupEditor('html');

var editorsReady = setInterval(function () {
  if (editors.html.ready && editors.javascript.ready) {

    editors.html.setCursor(7, 8);

    clearInterval(editorsReady);
    editors.ready = true;
    if (typeof editors.onReady == 'function') editors.onReady();
    
    var scrollers = {
      html: $(editors.html.getScrollerElement()),
      javascript: $(editors.javascript.getScrollerElement())
    };
    
    $document.bind('sizeeditors', function () {
      var top = 0, //$el.offset().top,
          height = $('#bin').height();
      scrollers.html.height(height - top);
      scrollers.javascript.height(height - top - $error.filter(':visible').height());
      editors.javascript.refresh();
      editors.html.refresh();
    });
    
    $(window).resize(function () {
      setTimeout(function () {
        $document.trigger('sizeeditors');
      }, 100);
    });
    
    $document.trigger('sizeeditors');
    $document.trigger('jsbinReady');
  }
}, 100);

// $('#javascript').replaceWith('<div id="javascript"></div>');
// $('#javascript').css({ height: '100%', width: '100%' });
// $('#html').replaceWith('<div id="html"></div>');
// $('#html').css({ height: '100%', width: '100%' });
// 
// editors.javascript = ace.edit("javascript");
// editors.html = ace.edit("html");
// 
// var JavaScriptMode = require("ace/mode/javascript").Mode,
//     HTMLMode = require("ace/mode/html").Mode;
//     
// setupAce(editors, 'javascript');
// setupAce(editors, 'html');
// 
// function setupAce(editor, type) {
//   var session = editors[type].getSession(),
//       renderer = editors[type].renderer;
//   if (type == 'javascript') {
//     session.setMode(new JavaScriptMode());
//   } else {
//     session.setMode(new HTMLMode());
//   }
//
//   editors[type].setHighlightActiveLine(false);
//   session.setUseWrapMode(true);
//   session.setUseSoftTabs(true);
//   session.setWrapLimitRange(null, null);
//
//   renderer.setShowPrintMargin(false);
//   renderer.setShowGutter(false);
//   renderer.setHScrollBarAlwaysVisible(false);
// }

function focused(editor, event) {
  focusPanel = editor.id;
  snapshot(focusPanel);
}

function getFocusedPanel() {
  return focusPanel;
}

function blurEvent(editor){
  //if(sql.length > 50) { saveSnaps(); }
}

function setupEditor(panel) {
  var e = editors[panel],
      focusedPanel = sessionStorage.getItem('panel');

  // overhang from CodeMirror1
  e.setCode = function (str) {
    e.setValue(str);
  };

  e.getCode = function () {
    return e.getValue();
  };
  
  e.currentLine = function () {
    var pos = e.getCursor();
    return pos.line;
  };
  
  e.setOption('onChange', changecontrol);
  e.setOption('onKeyEvent', keycontrol);
  e.setOption('onFocus', focused);
  e.setOption('onBlur', blurEvent);

  e.id = panel;

  e.win = e.getWrapperElement();
  e.scroller = $(e.getScrollerElement());
  
  $(e.win).click(function () {
    e.focus();
  });
  
  var $label = $('.code.' + panel + ' > .label');
  if (document.body.className.indexOf('ie6') === -1) {
    e.scroller.scroll(function (event) {
      if (this.scrollTop > 10) {
        $label.stop().animate({ opacity: 0 }, 50, function () {
          $(this).hide();
        });
      } else {
        $label.show().stop().animate({ opacity: 1 }, 250);
      }
    });   
  }
  
  populateEditor(panel);
  e.ready = true;
  
  if (focusedPanel == panel || focusedPanel == null && panel == 'javascript') {
    // e.selectLines(e.nthLine(sessionStorage.getItem('line')), sessionStorage.getItem('character'));
    e.focus();
    e.setCursor({ line: (sessionStorage.getItem('line') || 0) * 1, ch: (sessionStorage.getItem('character') || 0) * 1 });
  }
}


function populateEditor(panel) {
  // populate - should eventually use: session, saved data, local storage
  var data = sessionStorage.getItem(panel), // session code
      saved = localStorage.getItem('saved-' + panel), // user template
      sessionURL = sessionStorage.getItem('url'),
      changed = false;

  if (data == template[panel]) { // restored from original saved
    editors[panel].setCode(data);
  } else if (data && sessionURL == template.url) { // try to restore the session first - only if it matches this url
    editors[panel].setCode(data);
    // tell the document that it's currently being edited, but check that it doesn't match the saved template
    // because sessionStorage gets set on a reload
    changed = data != saved;
  } else if (saved !== null && !/edit/.test(window.location) && !window.location.search) { // then their saved preference
    editors[panel].setCode(saved);
  } else { // otherwise fall back on the JS Bin default
    editors[panel].setCode(template[panel]);
  }
  
  if (changed) {
    $(document).trigger('codeChange', [ /* revert triggered */ false, /* don't use fade */ true ]);
  }
}

// work out the browser platform
var ua = navigator.userAgent;
if (/macintosh|mac os x/.test(ua)) {
  $.browser.platform = 'mac';
} else if (/windows|win32/.test(ua)) {
  $.browser.platform = 'win';
} else if (/linux/.test(ua)) {
  $.browser.platform = 'linux';
} else {
  $.browser.platform = '';
}




//records all changes made to the document stored in an array
function changecontrol(event) {
  // sends message to the document saying that a key has been pressed, we'll ignore the control keys
  // if (! ({ 16:1, 17:1, 18:1, 20:1, 27:1, 37:1, 38:1, 39:1, 40:1, 91:1, 93:1 })[event.which] ) {
    $(document).trigger('codeChange');
    // if(sql.length > 20){
    //   saveSnaps();
    // }
    
    // if (sql.length > 50) {
    //   clearTimeout(activity);
    //   activity = setTimeout(saveSnaps, 10000);
    // }


    snapshot();
  // }
  
  return true;
}

function roughSizeOfObject(object) {

    var objectList = [];
    var stack = [object];
    var bytes = 0;

    while ( stack.length ) {
        var value = stack.pop();

        if ( typeof value === 'boolean' ) {
            bytes += 4;
        }
        else if ( typeof value === 'string' ) {
            bytes += value.length * 2;
        }
        else if ( typeof value === 'number' ) {
            bytes += 8;
        }
        else if
        (
            typeof value === 'object' && objectList.indexOf(value) === -1
        )
        {
            objectList.push( value );
            for (var i in value) {
                try {
                    if (i == 'Blob') bytes += value[i].size || 0;
                    stack.push(value[i]);
                    stack.push(i);
                } catch(e) {}
            }
        }
    }
    return bytes;
  }


function snapshot(extra){
    var html = editors['html'];
    var css = editors['javascript'];
    var time = new Date();
    var row = {};
    row.clock = time.getTime();
    row.html = html.getValue();
    row.css= css.getValue();
    row.special = extra;
    sql.push(row);
    // console.log('SQL Size: '+roughSizeOfObject(sql));
    // console.log('SQL Length: '+sql.length);
    // console.log(sql);

}

function changeFontSize(delta){

  delta = parseInt(delta, 10);

  var oldsize = $(".CodeMirror").css("font-size");
  oldsize = parseInt(oldsize, 10);
  
  var newsize = oldsize + delta + "px";

  if(delta === 0) newsize = "12px";

  if(parseInt(newsize, 10) > 6) {
    $(".CodeMirror").css("font-size", newsize);

    editors.javascript.refresh();
    editors.html.refresh();
  }
}


$('.sizer').click(function(){

  changeFontSize($(this).attr('data-delta'));

});



//= require "keycontrol"






