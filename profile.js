var xhrb = new XMLHttpRequest();
var xhrf = new XMLHttpRequest();
var qContainer = document.getElementById('qContainer');
var showMore = document.getElementById('showMore');
var owner = document.body.getAttribute('data-owner');
var offset = 10;

xhrb.onreadystatechange = function(){
  if (xhrb.readyState === 2){ //request sent, response headers recieved
    showMore.disabled = true;
    showMore.innerHTML = 'Loading';
  } else if (xhrb.readyState === 4){ //Ready state 4 means the request is done
    if (xhrb.status === 204 || 
        xhrb.responseText.indexOf('data-last="1"') !== -1) //an den uparxei allo
      showMore.remove() // showMore.onclick = null; //mhn ksanaakouseis to koumpi
    qContainer.innerHTML += xhrb.responseText;
    offset += 10;
  }
}

if (showMore) showMore.onclick = function(){
  var page = 'loadquestions.php?user=' + owner + 
      '&offset=' + offset;
  xhrb.open('GET', page, true);
  xhrb.send();
}


xhrf.onreadystatechange = function(){
  var butSubmit = document.askForm.inpSubmit;
  if (xhrf.readyState === 2){ //request sent, response headers recieved
    butSubmit.disabled = true;
    butSubmit.value = 'Loading';
  } else if (xhrf.readyState === 4){ //Ready state 4 means the request is done
    if (xhrf.status !== 200){
      butSubmit.disabled = false;
      butSubmit.value = null; //browser default

      descr = xhrf.getResponseHeader('x-error-descr');
      alert('Your question was not submitted. ' + descr);
    } else document.askForm.outerHTML = '<div id="success">Your question has '+
        'been submitted! <a href="user/'+ owner +'">Ask another one</a></div>';
    
    
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
  
  xhrf.open('POST', 'sent.php', true);
  xhrf.setRequestHeader("Content-type",
      "application/x-www-form-urlencoded");
  xhrf.send("question=" + question + '&to=' +
      touser + '&pubAsk=' + publicasker);

  return false; //mhn upobaleis kanonika
}