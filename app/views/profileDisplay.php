header#profileHeader,#displayPreview span {
  background-color: <?=$data['headcolor']?>;
  color: <?=$data['backcolor']?>;
  /* must not be !important so that jQuery can
     change it at the preview (settings.php) */
}

body[data-owner],#displayPreview {
  font-family: '<?=$data['textfont']?>', Calibri, Arial, sans-serif;
  background-color: <?=$data['backcolor']?>;
  background-image: linear-gradient(to bottom,
    rgba(0, 0, 0, 0), rgba(60, 60, 60, 0.2));
}
