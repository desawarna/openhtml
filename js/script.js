$("#menu a[href^='#']").on('click', function(e) {

   e.preventDefault();

   var hash = this.hash;

   $('html, body').animate({
       scrollTop: $(this.hash).offset().top
     }, 300, function(){

       window.location.hash = hash;
     });

});

$('#menu').affix({
  offset: {
    top: function () {
      return $('#splash').outerHeight(true);
    }
  }
});
