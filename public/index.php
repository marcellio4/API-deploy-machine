<?php
session_start();
session_regenerate_id(true);
require_once '../include/functions.php';
// Code to detect whether index.php has been requested without query string goes here
// If no parameter detected...
if (!isset($_GET['page'])) {
    $id = 'login'; // display login page
    pages($id);
} else {
    $id = $_GET['page']; // else requested page
    pages($id);
}
