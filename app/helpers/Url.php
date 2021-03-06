<?php namespace helpers;

class Url {
  
  public static function redirect($url, $fullpath = false){
    
    if ($fullpath == false)
      $url = DIR.$url;
    
    if (headers_sent())
      echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    else
      header("Location: $url", true, 302);
    
    die;
  }
}
