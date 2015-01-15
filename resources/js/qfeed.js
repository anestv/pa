$(function(){
  
  $(window).scroll(function (){
    if ($(this).scrollTop() > 400) 
      $('#scrollTop').fadeIn();
    else 
      $('#scrollTop').fadeOut();
  });
  
  $('#scrollTop').click(function (){
    $('html, body').animate({scrollTop: 0}, 600);
    return false;
  });
  
  $('time').age();
  
});
