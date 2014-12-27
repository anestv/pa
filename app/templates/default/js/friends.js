$(function (){ //on document ready

var promptUnsaved = true;
var orig = JSON.parse($('input[name="friends"]').val());
if (!Array.isArray(orig))
  console.error("'Friends' is not a valid JSON array");
var friends = orig.sort().slice(0);
//sorts both arrays and makes them different references (equal values only)

var user = $('body').data('user');

$('form .ui.list').on('click', '.item>i.remove.icon', removeFriend);

$('#addFriend').click(addFriend);

$('form').keydown(function(e){
  if (e.which === 13) {
    
    // setTimeout() for #32: Chrome submits the form if addFriend alerts
    setTimeout(function(){
      try {
        addFriend();
      } catch (e) {
        alert(e);
      }
    }, 5);
    
    return false;
  }
});

$('form').submit(function(){
  $('input[name="friends"]').val(JSON.stringify(friends));

  promptUnsaved = false;
  return true;
});

function addFriend(){
  var friendName = $('#friendInput').val();
  $('#friendInput').val('');
  
  if (friendName.trim() == '')
    throw "Enter a friend name";
  if (friends.indexOf(friendName) !== -1)
    throw "You have already entered this friend";
  if (friendName === user)
    throw 'We know you are friends with yourself, but please, be more social';
  
  friends.push(friendName);
  
  var curr = '<div class="item"><i class="right floated remove red link icon"></i>';
  curr +=  '<a class="header" href="user/'+ friendName +'">'+ friendName +'</a></div>';
  
  $('form .ui.list').append(curr);
}

function removeFriend(){
  var index = friends.indexOf($(this).siblings('.header').text());
  if (index !== -1) friends.splice(index, 1);
  
  this.parentElement.remove();
}


//Prevent user from leaving with unsaved changes

window.onbeforeunload = function (e) {
  var origS = JSON.stringify(orig); //is already sorted
  var currS = JSON.stringify(friends.sort());
  
  // do not compare the actual arrays as they are objects and have different references
  if (promptUnsaved && origS !== currS)
    return e.returnValue = 'You have not saved your friends';
};

});
