<?php
require_once '../include/functions.php';
// auto load classes
spl_autoload_register('myAutoloader');
Login::destroy($_SESSION['username']);
exit();
