<?php namespace core;

class View {
  
  public static function render($path,$data = false, $error = false){
    require "app/views/$path.php";
  }
  
  public static function rendertemplate($path,$data = false){
    require "app/templates/$path.php";
  }
}
