<base href="/pa/">
<title>Error - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.19.3/css/semantic.min.css">
</head>
<body>
<?php

// even if $e->getCode === 0 it's no problem, because see terminate's declaration
terminate($e->getMessage(), $e->getCode());
// no need for closing tags, terminate() adds them
?>
