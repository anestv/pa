<?php
$activeU = $data['activeQA'] ? '' : 'active';
$activeQA= $data['activeQA'] ? 'active' : '';

function get($prop){
  echo 'name="'. $prop .'"';
  if (!empty($_GET[$prop]))
    echo ' value="'.$_GET[$prop].'"';
}
?>
<aside>
  <div class="ui tabular menu">
    <a class="item <?=$activeU?>" data-tab="users">Users</a>
    <a class="item <?=$activeQA?>" data-tab="qa">Questions</a>
  </div>
  <!-- name attribute for inputs is inserted by get() -->
  
  <form name="user" class="ui form <?=$activeU?> tab" autocomplete="off" data-tab="users">
    <input type="hidden" name="lookfor" value="u">
    <h4 class="ui header"><i class="users icon"></i>Search for users</h4>
    <input type="text" maxlength="20" placeholder="Username" <?=get('username')?>>
    <input type="text" maxlength="40" placeholder="Real name" <?=get('realname')?>>
    <button class="ui animated button">
      <div class="visible content">Search</div>
      <div class="hidden content"><i class="search icon"></i></div>
    </button>
  </form>
  
  <form name="qa" class="ui form <?=$activeQA?> tab" autocomplete="off" data-tab="qa">
    <input type="hidden" name="lookfor" value="qa">
    <h4 class="ui header"><i class="question icon"></i>Search for questions / answers</h4>
    <div class="ui icon input">
      <input type="text" maxlength="20" placeholder="From who" <?=get('fromuser')?>>
      <i class="info icon" title="Unless you search for questions you asked, only eponymously asked questions will show up"></i>
    </div>
    <input type="text" maxlength="20" placeholder="To who" <?=get('touser')?>>
    <input type="text" placeholder="Text to look for" <?=get('query')?>>
    <div class="field">
      <span class="small">When was the question answered?</span>
      <input type="month" placeholder="In format yyyy-mm" <?=get('timeanswered')?>>
    </div>
    <div class="field">
      <select name="sort">
        <!-- not using Semantic's dropdown, in v1.0 we will be able to do $('select').dropdown()-->
        <option value="userasc">By recipent A-Z</option>
        <option value="userdesc">By recipent Z-A</option>
        <option value="timeasc">Oldest to newest</option>
        <option value="timedesc" selected>Newest to oldest</option>
      </select>
    </div>
    <button class="ui animated button">
      <div class="visible content">Search</div>
      <div class="hidden content"><i class="search icon"></i></div>
    </button>
  </form>
</aside>

<main>
<?php

if ($GLOBALS['warnMessage'])
  echo '<div class="center480 ui warning message">'.$GLOBALS['warnMessage'].'</div>';
  
if (!$data['dosearch']){
  echo '</main>';
  return;
}

$res = $data['res'];

if ($data['activeQA']){
  
  echo '<h2>Question search</h2>';
  
  $num = count($res);
  
  if ($num === 0){
    echo '<div class="ui warning message"><i class="warning icon">';
    echo '</i>There are no questions matching your criteria</div>';
  } else {
    echo '<div class="ui info message"><i class="checkmark icon"></i>';
    echo "Found $num result". ($num === 1 ? '':'s') .'</div>';
    echo '<div id="qContainer">';
    
    foreach ($res as $q)
      $q->writeOut(true, false);
    
    echo '</div>';
  }
} else { ?>

<h2>User search</h2>

<table id="userList" class="ui collapsing padded table segment">
<thead><tr>
  <th>Username</th>
  <th>Real Name</th>
  <th>Link</th>
</tr></thead>
<tbody>
  
  <?php
  while ($row = $res->fetch_array()){
    echo '<tr><td>'.$row['username'].'</a><td>'.$row['realname'];
    echo '<td><a href="user/'.$row['username'].'"><i class="url teal link icon"></i></a>';
  }?>
  
</tbody>
<tfoot><tr>
  <?php 
  $num = $res->num_rows;
  if ($num > 0){
    echo '<th colspan="3" class="ui info message"><i class="checkmark icon"></i>';
    echo "Found $num result". ($num === 1 ? '':'s') . '</th>';
  } else {
    echo '<th colspan="3" class="ui warning message">';
    echo '<i class="warning icon"></i>Did not find any results</th>';
  }?>
</tr></tfoot>
</table>
<?php } ?>
</main>
