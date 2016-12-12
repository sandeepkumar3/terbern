<?php
require_once '../config/database.php';
$dblistobj = new DATABASE_CONFIG();
$SERVERS_ARR = get_class_vars(get_class($dblistobj));

?>