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
$body = '../templates/jobstatus.html';
$foot = '../templates/footer.html';
//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array('[+logout+]' => '<li><a href="?page=logout" class="">Logout</a></li>',
                                     '[+status+]' => 'active'
                                    ));
$url = "https://Dev:D3vD3v@jenkins.experienceengine.com/rssAll";
$arrContextOptions=array(
              "ssl"=>array(
                      "verify_peer"=>false,
                     "verify_peer_name"=>false
                    ),
                         );
$assertion = file_get_contents($url, false, stream_context_create($arrContextOptions));
$xml = simplexml_load_string($assertion);
$c = 1;
$strTable = '';
foreach ($xml->children() as $element) {
    if ($c < 20) {
        // Avoiding empty elements in XML file with isset() function
        if (isset($element->title)) {
            $strTable .= '<tr><td>' . $element->title . '</td>';
            // Avoiding PHP Warning: main(): Node no longer exists
            if (isset($element->link)) {
                $strTable .= '<td><a href=' . (string)$element->link->attributes()->href . 'console' . ' target=_blank>View Log</a></td>';
            }
            $strTable .= '<td>' . $element->published . '</td>';
            $strTable .= '<td>' . $element->updated . '</td></tr>';
        }
    }
    $c++;
}

$final .= parseTemplate($tpl_b, array('[+table+]' => $strTable));

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
