<base href="/pa/">
<title>Error - PrivateAsk</title>
<link rel="stylesheet" type="text/css" href="css/general.css">
<link rel="stylesheet" type="text/css" href="css/semantic.min.css">
</head>
<body>
<?php

// even if $e->getCode === 0 it's no problem, because see terminate's declaration
terminate($e->getMessage(), $e->getCode());
// no need for closing tags, terminate() adds them
?>
