$(document).ready(function() {

var qContainer = $('#qContainer');
var showMore = $('#showMore');
var butSubmit = $('form.ask input[type="submit"]');
var owner = document.body.getAttribute('data-owner');
var offset = 10;


//handle show more button

function showMoreOK(data, status){
  if (status ==='nocontent'||data.indexOf('<div data-last')!==-1)
    showMore.remove();
  else
    showMore.prop('disabled', false).html('Show More');
  
  qContainer.append(data);
  offset += 10;
}

showMore.click(function(){
  showMore.prop('disabled', true).html('Loading...');
  
  var page = 'loadquestions.php?user=' +
      owner + '&offset=' + offset;
  $.get(page, showMoreOK);
});


//handle form submitting

function askOK(){
  butSubmit.prop('disabled', false).html('Submit');
  
  document.askForm.reset();
  
  document.askForm.outerHTML = '<div id="success">Your question has '+
  'been submitted! <a href="user/'+ owner +'">Ask another one</a></div>';
  //TODO maybe overlay and then hide the top layer to avoid reloading
}

if (document.askForm) document.askForm.onsubmit = function(){
  if (!this.question.value.trim()){
    alert('Please enter your question');
    return false;
  }
  
  butSubmit.prop('disabled', true).html('Submitting...');
  
  $.post('sent.php', $('form.ask').serialize(), askOK);
  return false;
}


//handle delete links

function deleteOK(data, status, xhr){
  
  if (data.indexOf('<div id="success">') !== -1){
    //alert('The question was deleted successfully!'); 
    //is it obvious enough to omit the alert?
    xhr.question.slideUp();
  } else
    alert("Something went wrong, the question was not deleted. That's all we know");
}

if ($('.deleteq').length)
  $('#qContainer').on('click', 'a.deleteq', function(e){
    if (!confirm('Delete this question?')) return false;
    
    var qElement = $(this).parents('.question');
    var page = this.href + '&del=1';
    
    $.get(page, deleteOK).question = qElement;
    
    return false;
  });


//error listener for all XHRs
$(document).ajaxError(function(event, xhr, settings){
  
  var errorType;
  if (settings.type === 'POST')
    errorType = 'submit your question';
  else if (settings.url.indexOf('?user=') !==-1)
    errorType = 'load more questions';
  else
    errorType = 'delete this question';
  
  var errorMsg = 'Unfortynately we could not ' + errorType +
    '. ' + xhr.getResponseHeader('X-Error-Descr');
  
  alert(errorMsg);
  console.warn(xhr.getResponseHeader('X-Error-Descr'));
});


});
