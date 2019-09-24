<?php
require_once '../include/functions.php';
// auto load classes
spl_autoload_register('myAutoloader');
// Give access only if user is login
Login::getAccess($_SESSION['username']);
/*
-------------Import templates --------------
*/
// set which templates to user_error
$head = '../templates/header.html';
$body = '../templates/chart.html';
$foot = '../templates/footer.html';
//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array('[+logout+]' => '<li><a href="?page=logout" class="">Logout</a></li>',
                                      '[+chart+]' => 'active'
                                    ));
$env = new Environment();
// drawing pie chart
if (isset($_POST['month']) && isset($_POST['year'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $chart = $env->pieChart($month, $year);
    echo json_encode($chart);
    exit();
}
// Continue building our template
$final .= $tpl_b;
try {
    Server::openSocket('ping');
    $status = '<span class="on">online</span>';
} catch (Exception $e) {
    $status = '<span class=off>offline</span>';
}
$final .= parseTemplate($tpl_c, array('[+name+]' => $_SESSION['username'],
                                      '[+IP+]' => $_SERVER['REMOTE_ADDR'],
                                      '[+log_status+]' => $status
                                    ));
//diplays our template file with all placeholders
$content = $final;
echo $content;
