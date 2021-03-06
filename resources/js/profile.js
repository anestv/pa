$(function() {

var qContainer = $('#qContainer');
var showMore = $('button#showMore');
var butSubmit = $('form.ask #askControls button');
var butFriend = $('#profileHeader a.toggle.button');
var owner = $('body').data('owner');
var offset = 10;

$('time').age();

$('.ui.message i.close.icon').click(function(){
  $(this).parent().slideUp();
});

//handle go to top button
$(window).scroll(function (){
  if ($(this).scrollTop() > 400) 
    $('#scrollTop').fadeIn();
  else 
    $('#scrollTop').fadeOut();
});

$('#scrollTop').click(function (){
  $("html, body").animate({scrollTop: 0}, 600);
  return false;
});


//add/remove friend button
function friendOK(){
  butFriend.removeClass('loading').toggleClass('active');
  
  if (! butFriend.hasClass('active')) //we are after toggleClass
    butFriend.children('span').text('Add friend');
  else
    butFriend.children('span').text('Friend');
  
}

butFriend.click(function(){
  butFriend.addClass('loading');
  var action = butFriend.hasClass('active') ? 'remove' : 'add';
 
  $.post('api/friends', {do: action, friends: owner}, friendOK);
  
  return false;
});


//handle button inside dimmer

$('form.ask .ui.dimmer .ui.button').click(function(){
  $('form.ask').dimmer('hide');
});

//handle show more button

function showMoreOK(data, status){
  if (status ==='nocontent'||data.indexOf('<div data-last')!==-1)
    showMore.remove();
  else
    showMore.prop('disabled', false).removeClass('loading');
  
  qContainer.append(data);
  offset += 10;
  $('time').age(); //calculate the new <time>s
}

showMore.click(function(){
  showMore.prop('disabled', true).addClass('loading');
  
  var page = 'api/load/' + owner + '?offset=' + offset;
  $.get(page, showMoreOK);
});


//handle form submitting

function askOK(){
  $('form.ask').trigger('reset').removeClass('loading').dimmer('show');
}

$('form.ask').submit(function(){
  if (!this.question.value.trim()){
    alert('Please enter your question');
    return false;
  }
  
  $(this).addClass('loading');
  
  $.post('api/ask', $(this).serialize(), askOK);
  return false;
});


//handle delete links

function deleteOK(data, status, xhr){
  xhr.question.slideUp();
}

if ($('.deleteq').length)
  $('#qContainer').on('click', 'a.deleteq', function(e){
    if (!confirm('Delete this question?')) return false;
    
    var qElement = $(this).parents('.question');
    
    $.post(this.href, 'del=1', deleteOK).question = qElement;
    
    return false;
  });


//error listener for all XHRs
$(document).ajaxError(function(event, xhr, settings){
  
  var errorType;
  if (settings.type === 'GET')
    errorType = 'load more questions';
  else if (xhr.question)
    errorType = 'delete this question';
  else if (settings.url === 'api/friends')
    errorType = 'change your friend list'
  else
    errorType = 'submit your question';
  
  var errorMsg = 'Unfortunately we could not ' + errorType +
    '. ' + xhr.getResponseHeader('X-Error-Descr');
  
  alert(errorMsg);
  console.warn(xhr.getResponseHeader('X-Error-Descr'));
  
  var ce = xhr.status < 500; //if it's a client error
  if (settings.type === 'GET')
    ce ? showMore.fadeOut(300) : showMore.prop('disabled', false).html('Show More');
  else if (xhr.question)
    ce ? $('#qContainer').off('click', 'a.deleteq') : null;
  else if (settings.url === 'api/friends')
    ce ? butFriend.fadeOut(300) : location.assign('friends');
  else
    ce ? butSubmit.fadeOut(300) : $('form.ask').removeClass('loading');
  
});

});
