$(document).ready(function() {


var xhrb = new XMLHttpRequest();
var xhrf = new XMLHttpRequest();
var xhrd = new XMLHttpRequest();
var qContainer = $('#qContainer');//document.getElementById('qContainer');
var showMore = $('#showMore');// document.getElementById('showMore');
var owner = document.body.getAttribute('data-owner');
var offset = 10;


//handle show more button

xhrb.onreadystatechange = function(){
  if (xhrb.readyState === 4){ //Ready state 4 means the request is done
    if (xhrb.status === 204 || 
        xhrb.responseText.indexOf('data-last="1"') !== -1) //an den uparxei allo
      showMore.remove();
    else {
      showMore.prop({disabled: false});
      showMore.html('Show More');
    }
    qContainer.append(xhrb.responseText);
    offset += 10;
  }
}

if (showMore) showMore.click(function(){
  showMore.prop({disabled: true});
  showMore.html('Loading');
  
  var page = 'loadquestions.php?user=' + owner + 
      '&offset=' + offset;
  xhrb.open('GET', page, true);
  xhrb.send();
});


//handle form submitting

xhrf.onreadystatechange = function(){
  var butSubmit = document.askForm.inpSubmit;
  if (xhrf.readyState === 4){ //Ready state 4 means the request is done
    butSubmit.disabled = false;
    butSubmit.value = null; //browser default
    
    if (xhrf.status !== 200 || xhrf.getResponseHeader('x-error-descr')){
      descr = xhrf.getResponseHeader('x-error-descr');
      alert('Your question was not submitted. ' + descr);
    } else 
      document.askForm.outerHTML = '<div id="success">Your question has '+
      'been submitted! <a href="user/'+ owner +'">Ask another one</a></div>';
      //TODO maybe overlay and then hide the top layer to avoid reloading
  }
}

if (document.askForm) document.askForm.onsubmit = function(){
  if (!this.question.value.trim()){
    alert('Please enter your question');
    return false;
  }
  var question = encodeURIComponent(this.question.value);
  var publicasker =(this.pubAsk && this.pubAsk.checked)? 1:'';
  var touser = encodeURIComponent(owner);
  
  
  butSubmit.disabled = true;
  butSubmit.value = 'Loading';
  
  xhrf.open('POST', 'sent.php', true);
  xhrf.setRequestHeader("Content-type",
      "application/x-www-form-urlencoded");
  xhrf.send("question=" + question + '&to=' +
      touser + '&pubAsk=' + publicasker);

  return false; //mhn upobaleis kanonika
}


//handle delete links

xhrd.onreadystatechange = function(){
  if (xhrd.readyState === 4){ //Ready state 4 means the request is done
    
    if (xhrd.status !== 200 || xhrd.getResponseHeader('x-error-descr')){
      descr = xhrd.getResponseHeader('x-error-descr');
      alert('The question was not deleted. ' + descr);
    } else if(xhrd.responseText.indexOf('<div id="success">') !== -1)
      alert('The question was deleted successfully!');
  }
}


if ($('.deleteq').length)
  $('#qContainer').on('click', 'a.deleteq', function(e){
    if (!confirm('Delete this question?')) return false;
    
    var page = this.href + '&del=1';
    xhrd.open('GET', page, true);
    xhrd.send();
    
    return false;
});


});