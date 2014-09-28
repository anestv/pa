$(function (){ //on document ready

var promptUnsaved = true;
var orig = JSON.parse($('input[name="friends"]').val());
if (!Array.isArray(orig))
  console.error("'Friends' is not a valid JSON array");
var friends = orig.sort(); //sorts both arrays

var user = $('body').data('user');

$('.ui.link.list').on('click', '.ui.red.icon.button', removeFriend);

$('#addFriend').click(addFriend);

$('form').keydown(function(e){
  if (e.which === 13) {
    addFriend();
    return false;
  }
});


$('form').submit(function(){
  $('input[name="friends"]').val(JSON.stringify(friends));
  promptUnsaved = false;
  return false;
});

function addFriend(){
  var friendName = $('#friendInput').val();
  $('#friendInput').val('');
  
  if (friendName.trim() == '') {
    return alert("Enter a friend name");
  }
  if (friends.indexOf(friendName) !== -1)
    return alert("You have already entered this friend");
  if (friendName === user)
    return alert('We know you are friends with yourself, but please, be more social');
  
  friends.push(friendName);
  
  var curr = '<div class="item"><div class="ui right floated circular red icon button">';
  curr += '<i class="remove icon"></i></div><a class="header" href="user/';
  curr += friendName + '">' + friendName + '</a></div>';
  
  $('.ui.link.list').append(curr);
  return 'OK';
}

function removeFriend(){
  var index = friends.indexOf($(this).siblings('.header').text());
  if (index !== -1) friends.splice(index, 1);
  
  this.parentElement.remove();
}

$('.ui.message > i.close.icon').click(function(){
  $(this).parent().slideUp();
});

//Prevent user from leaving with unsaved changes

window.onbeforeunload = function (e) {
  var origS = JSON.stringify(orig); //is already sorted
  var currS = JSON.stringify(friends.sort());
  
  if (promptUnsaved && origS !== currS) return 'You have not saved your friends';
};

});
