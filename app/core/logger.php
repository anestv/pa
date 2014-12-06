<?php namespace core;

class Logger {
  
  public static function customErrorMsg() {
    echo "<p>An error occured. The error has been reported to the development team and will be addressed asap.</p>";  
    exit;
  }
  
  public static function exception_handler($e){
    self::newMessage($e);
    self::customErrorMsg();
  }
  
  public static function error_handler($number, $message, $file, $line){
    
    self::log('error', $message, $file, $line, $number);
    
    if ($number !== E_NOTICE && $number < 2048)
       self::customErrorMsg();
    
    return 0;
  }
  
  public static function newMessage(\Exception $e){
    
    // what about the trace $e->getTraceAsString()
    
    self::log('exception', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
  }
  
  private static function log($type, $text, $file, $line, $code, $error_file = 'errorlog.html'){
    
    $time = date('G:i d/M/y');
    $datetime = date('c');
    
    $file = str_replace(getcwd(), '', $file);
    
    $entry = "<tr class='$type'>
      <td><time datetime='$datetime'>$time</time></td>
      <td>$text</td>
      <td>$file</td>
      <td>$line</td>
      <td>$code</td>
    </tr>";
    
    if (!file_exists($error_file))
      file_put_contents($error_file, '');
    //TODO ok, we create it, but how do we add the html head stuff etc
    
    $f = file($error_file); // array of the file's lines
    
    array_splice($f, 27, 0, $entry); // insert at position 27
    
    file_put_contents($error_file, implode($f));
  }
}
