// to allow for download button to be introduced via beta feature
$('a.save').click(function (event) {
  event.preventDefault();
 
  saveCode('save', window.location.pathname.indexOf('/edit') !== -1);


  return false;
});

$('a.clone').click(function (event) {
  event.preventDefault();

  var $form = $('#saveform')
    .append('<input type="hidden" name="javascript" />')
    .append('<input type="hidden" name="html" />');
  
  $form.find('input[name=javascript]').val(editors.javascript.getCode());
  $form.find('input[name=html]').val(editors.html.getCode());
  $form.find('input[name=method]').val('save,new');
  $form.submit();

  snapshot("Document Copied");

  return false;
});

$('#validatehtml').click(function (event){
  event.preventDefault();
  validate("html");
  snapshot("HTML Validated");
  return false;
});

$('#validatecss').click(function (event){
  event.preventDefault();
  validate("css");
  snapshot("CSS Validated");
  return false;
});

function validate(type){
  var $form = $('#validateform')
    .append('<input type="hidden" name="code" />')
    .append('<input type="hidden" name="type" />');

  if(type == "css") { $form.find('input[name=code]').val(editors.javascript.getCode()); }
  else if (type == "html") { $form.find('input[name=code]').val(editors.html.getCode()); }
  $form.find('input[name=type]').val(type);
  $form.submit();
}

function saveSnaps(){
  snapshot("Auto Snap");

  var $form = $('#saveform')
    .append('<input type="hidden" name="replay" />');

  $form.find('input[name=replay]').val(JSON.stringify(sql));

  $.ajax({
    url: $form.attr('action')+'replay',
    data: $form.serialize(),
    dataType: 'json',
    type: 'post',
    success: function (data) {
     sql.length = 0;
     console.log("Success");
    },
      error: function () {
        console.log("Error");
      }

  });
  
}

function saveCode(method, ajax, ajaxCallback) {
  // $(this).addClass('saving');
  //record save timestamp
  snapshot("Document Saved");
  // create form and post to it
  var $form = $('#saveform')
    .append('<input type="hidden" name="javascript" />')
    .append('<input type="hidden" name="html" />')
    .append('<input type="hidden" name="replay" />');
  
  $form.find('input[name=javascript]').val(editors.javascript.getCode());
  $form.find('input[name=html]').val(editors.html.getCode());
  $form.find('input[name=replay]').val(JSON.stringify(sql));
  $form.find('input[name=method]').val(method);
  if (ajax) {
    
    $.ajax({
      url: $form.attr('action'),
      data: $form.serialize(),
      dataType: 'json',
      type: 'post',
      success: function (data) {
        $('#saveform').attr('action', data.url + '/save');
        ajaxCallback && ajaxCallback();
        sql.length = 0;

        $('#save').removeClass('unsaved');

        if (window.history && window.history.pushState) {
          window.history.pushState(null, data.edit, data.edit);

          $('#jsbinurl').attr('href', data.url).text(data.url.replace(/http:\/\//, ''));
          updateTitle(true);
        } else {
          window.location = data.edit;
        }
      },
     error: function (request, status, error) {
        console.log(request.responseText, status, error);
    }
    });
  } else {
    $form.submit();
  }
}

$document.keydown(function (event) {
  if ((event.metaKey && event.which == 83) || (event.ctrlKey && event.which == 83)) {
    if (event.shiftKey == false) {
      $('#save').click();
      event.preventDefault();
    } else if (event.shiftKey == true) {
      $('.clone').click();
      event.preventDefault();
    }
  }
});