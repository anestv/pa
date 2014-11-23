$(function(){

$('.scriptOnly').removeClass('scriptOnly');
//because it is !important, .show() won't work

$('.ui.dropdown').dropdown();

$('#displaySettings').change(function(e){
  var newVal = e.target.value;
  
  switch (e.target.name){
    case 'backcolor':
      $('#displayPreview').css('background-color', newVal);
      $('#displayPreview span').css('color', newVal);
      break;
    case 'headcolor':
      $('#displayPreview span').css('background-color', newVal);
      break;
    case 'textfont':
      $('#displayPreview span').css('font-family', newVal);
  }
});

});
