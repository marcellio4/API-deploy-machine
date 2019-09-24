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
$body = '../templates/tools.html';
$foot = '../templates/footer.html';
//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array('[+logout+]' => '<li><a href="?page=logout" class="">Logout</a></li>',
                                     '[+tools+]' => 'active'
                                    ));

$env = new Environment();

// after click on create pool we create pool and send succesfull msg
if (isset($_POST['envvalue']) && isset($_POST['newname'])) {
    $val = clean_str($_POST['envvalue']);
    $name = clean_str($_POST['newname']);
    $id = clean_str($_SESSION['userid']);
    $pool = $env->renamePool($val, $name, $id);
    if ($pool) {
        $url = "@jenkins.experienceengine.com/job/zz-PoolVMs/buildWithParameters?token=";
        Jenkins::executeUrl($url);
        echo 1;
    } else {
        echo 0;
    }
    exit();
}

// Display all Environment names for our template
$str ="";
$environment = $env->getAllNames();
if (!empty($environment)) {
    foreach ($environment as $environments) {
        $str .= '<option value= ' . $environments['ENV_id'] . ':' . $environments['ENV_Name'] . '>' . $environments['ENV_Name'] . '</option>';
    }
}


// Running second form for deploy machine after we slect host
$msgSoc = "";
if (isset($_POST['Selecthosts'])) {
    $hostFile = "#hosts\n\r#endhosts";
    try {
        Server::openSocket($hostFile);
        $msgSoc = nl2br($hostFile) . '</br>';
    } catch (Exception $e) {
        $msgSoc = '<span class=off>Connection failed!</span>';
    }
}


$final .= parseTemplate($tpl_b, array('[+socket+]' => $msgSoc));
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
