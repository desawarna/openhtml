// to allow for download button to be introduced via beta feature
$('a.save').click(function (event) {
  event.preventDefault();
  saveCode('save', window.location.pathname.indexOf('/edit') !== -1);
  
  return false;
});

$('a.clone').click(function (event) {
  event.preventDefault();

  var $form = $('saveform')
    .append('<input type="hidden" name="javascript" />')
    .append('<input type="hidden" name="html" />');
  
  $form.find('input[name=javascript]').val(editors.javascript.getCode());
  $form.find('input[name=html]').val(editors.html.getCode());
  $form.find('input[name=method]').val('save,new');
  $form.submit();

  return false;
});

$('#validate').click(function (event){
  event.preventDefault();
  validate();
  return false;
});

function validate(){
  var $form = $('#validateform')
    .append('<input type="hidden" name="javascript" />')
    .append('<input type="hidden" name="html_code" />');

  $form.find('input[name=javascript]').val(editors.javascript.getCode());
  $form.find('input[name=html_code]').val(editors.html.getCode());
  $form.submit();

  snapshot("Valitator Activated");
}

function saveCode(method, ajax, ajaxCallback) {

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

        if (window.history && window.history.pushState) {
          window.history.pushState(null, data.edit, data.edit);

          $('#jsbinurl').attr('href', data.url).text(data.url.replace(/http:\/\//, ''));
          updateTitle(true)
        } else {
          window.location = data.edit;
        }
      },
      error: function () {

      }
    });
  } else {
    $form.submit();
  }
}

$document.keydown(function (event) {
  if (event.metaKey && event.which == 83) {
    if (event.shiftKey == false) {
      $('#save').click();
      event.preventDefault();
    } else if (event.shiftKey == true) {
      $('.clone').click();
      event.preventDefault();
    }
  }
});