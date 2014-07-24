var friends = JSON.parse(document.af.friends.value);
var user = document.body.getAttribute('data-user')
var ul = document.af.friendList.firstChild;

for(var i=0; i<ul.childElementCount; i++)
    ul.children[i].onclick = removeFriend;

document.af.friendInput.onkeydown = function(e){
    if (e.keyCode === 13) {
        e.preventDefault();
        addFriend();
        return false;
    }
};
document.af.onkeypress = function(e){return e.keyCode !== 13;};

document.af.addFr.onclick = addFriend;

document.af.onsubmit = function(){
    document.af.friends.value = JSON.stringify(friends);
};

function addFriend(){
    var friendName = document.af.friendInput.value.trim();
    document.af.friendInput.value = "";
    
    if (friendName == '') return alert("Enter a friend name");
    if (friends.indexOf(friendName) !== -1)
        return alert("You have already entered this friend");
    if (friendName === user)
        return alert('We know you are friends with yourself, but please, be more social');

    friends.push(friendName);
    
    var curr = document.createElement('li');
    curr.innerHTML = friendName;
    curr.onclick = removeFriend;
    ul.appendChild(curr);
    
}

function removeFriend(){
    var index = friends.indexOf(this.innerHTML);
    if (index !== -1) 
        friends.splice(index, 1);
    
    this.remove();
}
