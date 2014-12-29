<?php namespace core;

class Logger {
  
  public static function errorMessage($message = ''){
    $data = ['title' => 'Error', 'noGeneralCss' => 1, 'error' => $message];
    
    $images = glob(getcwd() . '/images/error/*.jpg'); // get files matching that pattern
    
    $img = $images[array_rand($images)];
    
    if ($img and file_exists($img)) // maybe $images is empty
      $exif = exif_read_data($img);
    
    if ($exif){
      $imgurl = BASE_DIR.'images/error/'.$exif['FileName'];
      $data['imagedata'] = "Image by {$exif['Artist']}, licensed under {$exif['Copyright']}";
    }
    
    $data['styles'] = ['error.css'];
    $data['bodyData'] = "style='background-image: url($imgurl);"; // if overlaying backgrounds are not supported
    $data['bodyData'].= "background-image: radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.4)), url($imgurl)'";
    
    view::rendertemplate('header', $data);
    view::render('error/error', $data);
    view::rendertemplate('footer', $data);
    exit;
  }
  
  public static function exception_handler($e){
    // what about the trace: $e->getTraceAsString()
    
    self::log('exception', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
    
    self::errorMessage($e->getMessage());
  }
  
  public static function error_handler($number, $message, $file, $line){
    
    if ($number === E_NOTICE) return 0;
    
    self::log('error', $message, $file, $line, $number);
    
    if ($number < 2048)
       self::errorMessage($message);
    
    return 0;
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
      copy('errorlog.template.html', $error_file); // create a copy of the template
    
    $f = file($error_file); // array of the file's lines
    
    array_splice($f, 27, 0, $entry); // insert at position 27
    
    file_put_contents($error_file, implode($f));
  }
}
