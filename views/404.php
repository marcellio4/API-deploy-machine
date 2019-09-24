<?php
require_once '../include/functions.php';
// auto load classes
spl_autoload_register('myAutoloader');
/*
-------------Import templates --------------
*/
// set which templates to user_error
$head = '../templates/header.html';
$body = '../templates/404.html';
$foot = '../templates/footer.html';
//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
//build up our header HTML with function text_body
if (isset($_SESSION['username'])) {
    $final = parseTemplate($tpl_a, array('[+jenkins+]' => '<li><a href="#" target=_blank class="">Jenkins↪</a></li>',
                                        '[+logout+]' => '<li><a href="?page=logout" class="">Logout</a></li>'
                                      ));
} else {
    $final = parseTemplate($tpl_a, array('[+jenkins+]' => '',
                                        '[+logout+]' => ''
                                      ));
}


$final .= $tpl_b;

if (isset($_SESSION['username'])) {
    $final .= parseTemplate($tpl_c, array('[+name+]' => $_SESSION['username'],
                                        '[+log_status+]' => '<span class="on">Online</span>'
                                      ));
} else {
    $final .= parseTemplate($tpl_c, array('[+name+]' => '',
                                        '[+log_status+]' => '<span class="off">Offline</span>'
                                      ));
}

//diplays our template file with all placeholders
$content = $final;
echo $content;
