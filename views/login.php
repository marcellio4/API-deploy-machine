<?php
require_once '../include/functions.php';
// auto load classes
spl_autoload_register('myAutoloader');
if (isset($_SESSION['username'])) {
    header("Location: ../index.php?page=home");
}
/*
-------------Import templates --------------
*/
// set which templates to user_error
$body = '../templates/login.html';
//load the content of templates
$tpl_b = file_get_contents($body);
//build up our header HTML
$login = new Login();
$user = new User();
$error = '';
/* clean entity and set username with password
 * Do some validation
 */
if (isset($_POST['login'])) {
    $name = clean_str($_POST['username']);
    $pwd  = clean_str($_POST['password']);
    if ($login->set_uname($name) &&   $login->set_pwd($pwd)) {
        //redirect user to home page
        if ($user->getId($name, $pwd)) {
            $_SESSION['username'] = $name;
            $_SESSION['pwd'] = $pwd;
            $result = $user->getId($name, $pwd);
            $_SESSION['userid'] = $result[0]['UserID'];
            header("Location: index.php?page=home");
            exit();
        } else {
            $error = '<span class="off">Username or password is invalid</span>';
        }
    }
}
//continue to build up our body for the admin_page.
$final = parseTemplate($tpl_b, array('[+name+]' => $login->getUname(),
                                      '[+pwd+]' => $login->getPwd(),
                                      '[+error+]' => $error
                                      ));
//diplays our template file with all placeholders
$content = $final;
echo $content;
// close connection to database
Config::db_close();
