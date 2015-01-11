<?php
$activeU = $data['activeQA'] ? '' : 'active';
$activeQA= $data['activeQA'] ? 'active' : '';

function get($prop){
  echo 'name="'. $prop .'"';
  if (!empty($_GET[$prop]))
    echo ' value="'.$_GET[$prop].'"';
}
?>

<div class="ui padded stackable grid">
<main class="twelve wide computer ten wide tablet column">

<?php

if ($GLOBALS['warnMessage'])
  echo '<div class="center480 ui warning message">'.$GLOBALS['warnMessage'].'</div>';
  
if ($data['dosearch'] and ($res = $data['res'])){

if ($data['activeQA']):
  
  $num = count($res);
  
  if ($num === 0){
    echo '<div class="ui warning message"><i class="warning icon">';
    echo '</i>There are no questions matching your criteria</div>';
  } else {
    echo '<div class="ui info message"><i class="checkmark icon"></i>';
    echo "Found $num result". ($num === 1 ? '':'s') .'</div>';
    echo '<div id="qContainer">';
    
    foreach ($res as $q)
      $q->writeOut(['extended']);
    
    echo '</div>';
  }
else: ?>

<table id="userList" class="ui striped padded table segment">
<thead><tr>
  <th class="six wide">Username</th>
  <th class="eight wide">Real Name</th>
  <th class="two wide">Link</th>
</tr></thead>
<tbody>
  
  <?php
  while ($row = $res->fetch_array()){
    echo '<tr><td>'.$row['username'].'</a><td>'.$row['realname'];
    echo '<td><a href="user/'.$row['username'].'"><i class="linkify blue link icon"></i></a>';
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
<?php endif; 
} ?>

</main>

<aside class="four wide computer six wide tablet column">
<div class="ui segment">
  <div class="ui tabular menu">
    <!-- hrefs at links in case of no js -->
    <a class="item <?=$activeU?>" data-tab="users" href="search?lookfor=u">Users</a>
    <a class="item <?=$activeQA?>" data-tab="qa" href="search?lookfor=qa">Questions</a>
  </div>
  <!-- name attribute for inputs is inserted by get() -->
  
  <form name="user" class="ui form <?=$activeU?> tab" autocomplete="off" data-tab="users">
    <input type="hidden" name="lookfor" value="u">
    <h4 class="ui center aligned header"><i class="users icon"></i>Search for users</h4>
    
    <div class="field">
      <input type="text" maxlength="20" placeholder="Username" <?=get('username')?>>
    </div>
    <div class="field">
      <input type="text" maxlength="40" placeholder="Real name" <?=get('realname')?>>
    </div>
    
    <button class="centered ui positive animated button">
      <div class="visible content">Search</div>
      <div class="hidden content"><i class="search icon"></i></div>
    </button>
  </form>
  
  <form name="qa" class="ui form <?=$activeQA?> tab" autocomplete="off" data-tab="qa">
    <input type="hidden" name="lookfor" value="qa">
    <h4 class="ui center aligned header"><i class="question icon"></i>Search for questions</h4>
    
    <div class="field ui right icon input">
      <input type="text" maxlength="20" placeholder="From who" <?=get('fromuser')?>>
      <i class="inverted circular blue info icon"></i>
    </div>
    <div class="field">
      <input type="text" maxlength="20" placeholder="To who" <?=get('touser')?>>
    </div>
    <div class="field">
      <input type="text" placeholder="Text to look for" <?=get('query')?>>
    </div>
    <div class="field">
      <div class="ui pointing below blue label">When was the question answered?</div>
      <input type="month" placeholder="In format yyyy-mm" <?=get('timeanswered')?>>
    </div>
    <div class="field">
      <select name="sort">
        <option value="userasc">By recipent A-Z</option>
        <option value="userdesc">By recipent Z-A</option>
        <option value="timeasc">Oldest to newest</option>
        <option value="timedesc" selected>Newest to oldest</option>
      </select>
    </div>
    
    <button class="centered ui positive animated button">
      <div class="visible content">Search</div>
      <div class="hidden content"><i class="search icon"></i></div>
    </button>
  </form>
</div>
</aside>

</div>
