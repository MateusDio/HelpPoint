<?php
$params = $_GET;
$params['modo'] = 'registro';
header('Location: index.php?' . http_build_query($params));
exit();
