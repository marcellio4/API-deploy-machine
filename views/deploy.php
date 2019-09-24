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
$body = '../templates/deploy.html';
$foot = '../templates/footer.html';

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array( '[+logout+]' => '<li><a href="?page=logout" class="">Logout</a></li>'));
// Connecting to jenkins to execute Deploy
$uk = '';
$us = '';
$cto = '';
if (isset($_POST['uksubmit'])) {
    $name = encodeUrl($_POST['ukenvname']);
    $hyperv = clean_str($_POST['hypervlabel']);
    $job = new Job("UK-0-Deploy-UK-Machines", "eefc40a3-cfc4-49c1-aa2b-169245cf0923");
    $parameter = "&Environmentname=" . $name . "&Hyper-v=" . $hyperv . "&userid=" . $_SESSION['userid'];
    $url = $job->setLinkParameter($parameter);
    Jenkins::executeUrl($url);
    $uk = '<span class=on>Deploying ENV ' . $name . '</span>';
}
if (isset($_POST['ussubmit'])) {
    $name = encodeUrl($_POST['usenvname']);
    $hyperv = clean_str($_POST['hypervlabel']);
    $job = new Job("US-0-Deploy-US-Machine", "usfc40a3-cfc4-49c1-aa2b-169245cf0923");
    $parameter = "&Environmentname=" . $name . "&Hyper-v=" . $hyperv . "&userid=" . $_SESSION['userid'];
    $url = $job->setLinkParameter($parameter);
    Jenkins::executeUrl($url);
    $us = '<span class=on>Deploying ENV ' . $name . '</span>';
}
if (isset($_POST['ctosubmit'])) {
    $name = encodeUrl($_POST['ctoenvname']);
    $hyperv = clean_str($_POST['hypervlabel']);
    $job = new Job("aCust-0-Deploy-Customer-Machines", "custfc40a3-cfc4-49c1-aa2b-169245cf0923");
    $parameter = "&Environmentname=" . $name . "&Hyper-v=" . $hyperv . "&userid=" . $_SESSION['userid'];
    $url = $job->setLinkParameter($parameter);
    Jenkins::executeUrl($url);
    $cto = '<span class=on>Deploying ENV ' . $name . '</span>';
}
// Continue building our template
$final .= parseTemplate($tpl_b, array('[+uk+]' => $uk,
                                      '[+us+]' => $us,
                                      '[+cto+]' => $cto
                                    ));
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
