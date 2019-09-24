<?php
// get page id and display the correct page
function pages($id_page){
		switch ($id_page) {
			case 'home'     :
				return include '../views/home.php';
				break;
			case 'tools'    :
				return include '../views/tools.php';
				break;
			case 'chart' :
				return include '../views/chart.php';
				break;
			case 'login'    :
				return include '../views/login.php';
				break;
			case 'logout'    :
					return include '../views/logout.php';
					break;
			case 'jobstatus'  :
					return include '../views/jobstatus.php';
					break;
			case 'deploy'  :
					return include '../views/deploy.php';
					break;
			default         :
				return include '../views/404.php';
		}
}

//define the autoload function
function myAutoloader($class){
	//constract path to the class file
	include '../classes/' . $class . '.php';
}
/* replace our placeholders for the original value from our templates */
function parseTemplate($tpl, $placeholders) {
    $pass = $tpl;
    $content = '';
    foreach ($placeholders as $key => $val) {
        $pass = str_replace($key, $val, $pass);
    }
    // Remove any missed/misspelled tags
    $pass = preg_replace('/[*]/','', $pass, 1);
    $content .= $pass;
    return $content;
}

/**
 * @return clean entity
 */
function clean_str($str){
	$clean = '';
	$trimmed = trim($str);
	return $clean = htmlentities($trimmed,ENT_QUOTES,'UTF-8');
}
function encodeUrl($entity){
	$encode = '';
	$trimmed = trim($entity);
	return $clean = urlencode($trimmed);
}
 ?>
