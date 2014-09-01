var friends = JSON.parse($('input[name="friends"]').val());
var user = document.body.getAttribute('data-user');

//fix for when form is submitted in form.keydown
//return false, preventDefault, or stopProgagation did not work
var submit = true;

$('ul#friendList').on('click', 'li', removeFriend);

$('#addFriend').click(addFriend);

$('form').keydown(function(e){
  if (e.which === 13) {
	submit = false;
    addFriend();
    return false;
  }
});


$('form').submit(function(){
  $('input[name="friends"]').val(JSON.stringify(friends));
  if (submit) return true;
  submit = true;
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
  
  var curr = document.createElement('li');
  curr.innerHTML = friendName;
  $('ul#friendList').append(curr);
  return 'OK';
}

function removeFriend(){
  var index = friends.indexOf(this.innerHTML);
  if (index !== -1) friends.splice(index, 1);
  console.log(index+this.innerHTML)
  this.remove();
}
