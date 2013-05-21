<?php
if(!$_GET['callback'])die(':(');
$version="1.5";
exit($_GET['callback'].'('.$version.');');
?>