<!DOCTYPE html>
<!-- PrivateAsk - Copyright Anestis Varsamidis 2014 -
http://github.com/anestv/pa - Open source: Artistic License 2.0 -->
<html>
<head>
  <meta charset="utf-8">
  <title><?php if (isset($data['title']))echo $data['title'].' - '; echo SITETITLE; //SITETITLE defined in config.php?></title>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Comfortaa:700|Damion">
  <?php
  echo '<base href="'. BASE_DIR .'">';
  if (empty($data['noGeneralCss']))
    echo '<link href="'.helpers\url::get_template_path().'css/general.css" rel="stylesheet">';
  
  if (isset($data['styles']) and is_array($data['styles']))
    foreach($data['styles'] as $style)
      if ($style[0] == '/')
        echo '<link rel="stylesheet" type="text/css" href="'.substr($style, 1).'">';
      else
        echo '<link rel="stylesheet" type="text/css" href="'.helpers\url::get_template_path()."css/$style\">";
?>
</head>
<body <?=$data['bodyData']?>>