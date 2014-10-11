$(function(){

$('.scriptOnly').removeClass('scriptOnly');
//because it is !important, .show() won't work

$('.ui.dropdown').dropdown();

$('#displaySettings').change(function(e){
  var newVal = e.target.value;
  
  switch (e.target.name){
    case 'bcolor':
      $('#displayPreview').css('background-color', newVal);
      $('#displayPreview span').css('color', newVal);
      break;
    case 'hcolor':
      $('#displayPreview span').css('background-color', newVal);
      break;
    case 'fontfamily':
      $('#displayPreview span').css('font-family', newVal);
  }
});

});
