<?php 

function writeHeadHtml($title, $otherHeadStuff = ''){
  global $requestAJAX;
  
  echo '<!DOCTYPE html><!-- Copyright ©2014 Anestis Varsamidis -->';
  if ($requestAJAX) return;
  echo "<html><head><title>$title - PrivateAsk</title>";
  echo '<link rel="stylesheet" type="text/css" href="css/general.css">';
  echo '<link rel="stylesheet" type="text/css" href="css/semantic.min.css">';
  echo $otherHeadStuff . '<meta charset="utf-8"></head><body>';
}

function redirect($relUrl, $code = 302){
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
      || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $absUrl = $protocol . $_SERVER['HTTP_HOST'] .'/pa/'. $relUrl;
  if (headers_sent())
    echo '<meta http-equiv="refresh" content="0;url='. $absUrl .'">';
  else
    header("Location: $absUrl", true, $code);
}

function terminate($reason = "", $exitcode = 0){
  $message = '<div class="center480 aloneInPage ui red inverted stacked segment">';
  $message .= '<div class="ui large icon error message">';
  $message .= '<i class="attention icon"></i><div class="header">Error.';
  
  if ($exitcode){
    if ($exitcode === 418) header("HTTP/1.1 418 I'm a teapot");
    else http_response_code($exitcode);
    
    $message .= " That's ".(($exitcode < 500)? 'your':'our').' fault.';
  }
  // href="?" resets all GET parameters
  $message .= "</div> $reason <br>".'<a href="?">Try again</a> &emsp;';
  $message .= '<a href="index.php">Home</a></div></div></body></html>';
  header("X-Error-Descr: $reason");
  die($message);
}

function suiMessage($type, $header, $msg = ''){
  switch ($type){
    case 'success':
      $icon = 'checkmark';
      break;
    case 'error':
      $icon = 'attention';
      break;
    default:
      $icon = $type;
  }
  
  echo "<div class=\"ui $type icon message\"><i class=\"$icon icon\"></i>";
  echo '<div class="header">'. "$header</div>$msg</div>";
}

function successMsg($header, $msg = ''){
  echo '<div class="ui success icon message"><i class="checkmark icon"></i>';
  echo '<div class="header">'. "$header</div>$msg</div>";
}
function errorMsg($header, $msg = ''){
  echo '<div class="ui error icon message"><i class="attention icon"></i>';
  echo '<div class="header">'. "$header</div>$msg</div>";
}

$requestAJAX = isset(apache_request_headers()['X-Requested-With']) and 
  apache_request_headers()['X-Requested-With'] === "XMLHttpRequest";

function connectToDB(){
  global $con, $user;
  
  if (!($db = parse_ini_file('dbConnectConfig.ini')))
    terminate('Could not read database connection configuration', 500);
  
  $con = new mysqli($db['address'], $db['username'], $db['password'], $db['database']);
  if ($con->connect_errno)
    terminate("Failed to connect to MySQL: ".$con->connect_error, 500);
  
  if (empty($_SESSION['user']))
    $user = '';
  else {
    $user = $_SESSION['user'];
    //check if account exists
    if (1 !== $con->query("SELECT username FROM users WHERE username = '$user';")->num_rows){
      session_destroy();
      $user = '';
    }
  }
}
connectToDB();
?>