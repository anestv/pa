<!DOCTYPE html>
<!-- PrivateAsk - Copyright Anestis Varsamidis 2014 -
http://github.com/anestv/pa - Open source: Artistic License 2.0 -->
<html>
<head>
  <meta charset="utf-8">
  <title><?php if (isset($data['title']))echo $data['title'].' - '; echo SITETITLE; //SITETITLE defined in config.php?></title>
  
  <?php
  echo '<base href="'. BASE_DIR .'">';
  if (empty($data['noGeneralCss']))
    echo '<link href="'.helpers\Url::get_template_path().'css/general.css" rel="stylesheet">';
  
  //TODO replace with our build
  echo '<link rel="stylesheet" href="https://pa-anestv-1.c9.io/dev-semantic/dist/semantic.min.css">';
  echo '<link rel="stylesheet" type="text/css" href="'.helpers\Url::get_template_path()."css/topBar.css\">";
  
  if (isset($data['styles']) and is_array($data['styles']))
    foreach($data['styles'] as $style)
      if ($style[0] == '/')
        echo '<link rel="stylesheet" type="text/css" href="'.substr($style, 1).'">'; // remove first slash
      else
        echo '<link rel="stylesheet" type="text/css" href="'.helpers\Url::get_template_path()."css/$style\">";
?>
</head>
<body <?=$data['bodyData']?>>

<?php require 'topbar.php'; ?>
