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
$body = '../templates/home.html';
$foot = '../templates/footer.html';
//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array( '[+logout+]' => '<li><a href="?page=logout" class="">Logout</a></li>',
                                      '[+home+]' => 'active'
                                    ));
$env = new Environment();
$table ="";
$tbl = $env->getAll();
if (!empty($tbl)) {
    // Display the rest of the table
    foreach ($tbl as $element) {
        $table .= $element['Freetxt'];
    }
} else {
    $table = 'Something went wrong with database';
}

// Setting option menu for the pool environment in HTML file
$opt = "";
$createEnv = $env->getPoolIdNameType();
if (!empty($createEnv)) {
    foreach ($createEnv as $options) {
        $opt .= '<option value= ' . $options['ENV_id'] . '>' . $options['ENV_Name'] . '-' . $options['EnvType'] . '</option>';
    }
}

$DEV1 = "";
$gethyperfreememDEV1 = $env->getHyperVFreeMem('DEV1');
if (!empty($gethyperfreememDEV1)) {
    foreach ($gethyperfreememDEV1 as $options) {
        $DEV1 .= $options['HostHyperVFreeMemory'];
    }
}

$DEV2 = "";
$gethyperfreememDEV2 = $env->getHyperVFreeMem('DEV2');
if (!empty($gethyperfreememDEV2)) {
    foreach ($gethyperfreememDEV2 as $options) {
        $DEV2 .= $options['HostHyperVFreeMemory'];
    }
}

// Create view Log table
if (isset($_POST['comp1'])) {
    $name1 = clean_str($_POST['comp1']);
    $name2 = '';
    if (isset($_POST['comp2'])) {
        $name2 = clean_str($_POST['comp2']);
    }
    $log = $env->viewLog($name1, $name2);
    $viewLog = '<div class="modal-table-view-log">
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Date</th>
                      <th>Machine</th>
                      <th>Jobname</th>
                      <th>Host</th>
                      <th>Status</th>
                      <th>Log</th>
                    </tr>
                  </thead>
                  <tbody>';
    foreach ($log as $logs) {
        if ($logs['Status'] == 'Error') {
            $viewLog .= '<tr class="error"><td>' . $logs['JobJenJobID'] . '</td>
                        <td>' . date('j F y - H:i', strtotime($logs['JobDate'])) . '</td>
                        <td>' . $logs['JobMachine'] . '</td>
                        <td>' . $logs['JobName'] . '</td>
                        <td>' . $logs['JobHost'] . '</td>
                        <td>' . $logs['Status'] . '</td>
                        <td>' . $logs['JobLog'] . '</td></tr>';
        } else {
            $viewLog .= '<tr><td>' . $logs['JobJenJobID'] . '</td>
                        <td>' . date('j F y - H:i', strtotime($logs['JobDate'])) . '</td>
                        <td>' . $logs['JobMachine'] . '</td>
                        <td>' . $logs['JobName'] . '</td>
                        <td>' . $logs['JobHost'] . '</td>
                        <td>' . $logs['Status'] . '</td>
                        <td>' . $logs['JobLog'] . '</td></tr>';
        }
    }
    $viewLog .= '</tbody>
              </table>
              </div>';
    echo $viewLog;
    exit();
}

// Change Memory
if (isset($_POST['EnvName']) && isset($_POST['sizeGB'])) {
    $name = $_POST['EnvName'];
    $gb = $_POST['sizeGB'];
    if (is_numeric($gb)) {
        $start_60 = round(($gb*0.6));
        if ($start_60 < 1) {
            $start_60 =1;
        }
        /*
        $min_40 = round(($gb*0.4));
        if ($min_40 < 1) {
            $min_40 =1;
        }*/
        $min_40 =1;

        $gb = $gb*1024*1024*1024;
        $start_60 = $start_60*1024*1024*1024;
        $min_40 = $min_40*1024*1024*1024;
        $job = new Job("zz-ReconfigureVMMemory", $token);
        $parameter = "&EnvironmentName=" . $name . "&MemoryStartupBytes=" . $start_60 . "&MemoryMinimumBytes=" . $min_40 . "&MemoryMaximumBytes=" . $gb;
        $start_url = $job->setLinkParameter($parameter);
        Jenkins::executeUrl($start_url);
        $url = $job->getLastBuild();
        $rst = Jenkins::jsonFile($url); //get json file from jenkins on last build ($rst -> result)
        // set data for insert into database
        $data = array('[JobJenJobID]' => $rst->number + 1,
                    '[JobStatus]' => 1,
                    '[JobMachine]' => $rst->actions[0]->parameters[0]->value,
                    '[JobName]' => 'zz-ReconfigureVMMemory',
                    '[JobHost]' => $rst->builtOn);
        $env->execInsert('[dbo].[tbl_Jobs]', $data);
        echo 1;
    } else {
        echo 0;
    }
    exit();
}

// Change description name
if (isset($_POST['text'])) {
    $id = trim($_POST['id']);
    $text = clean_str($_POST['text']);
    $field = array('[EnvDesc]' => $text);
    $x = $env->execUpdate('[dbo].[tbl_Env]', $id, $field);
    if ($x) {
        echo $text;
    }
    exit();
}
// Deploy job
if (isset($_POST['selected']) && !empty($_POST['selected'])) {
    $job = array();
    $job[] = trim($_POST['machine-1']);
    $job[] = trim($_POST['machine-2']);
    $selected = trim($_POST['selected']);
    $gitbranch = trim($_POST['Env_deployBR']);
    foreach ($job as $value) {
        // cust
        if (strpos($value, '-Ubuntu-WS') !== false) {
            $linux = $value;
        }

        // uk
        if (strpos($value, '.UK-WS') !== false) {
            $ms = $value;
            $ms = str_replace(".", "_", $ms);
            $ms = str_replace("-", "_", $ms);
        }
        if (strpos($value, '-UK-logging') !== false) {
            $linux = $value;
        }

        // us
        if (strpos($value, '.US.MSQL') !== false) {
            $ms = $value;
            $ms = str_replace(".", "_", $ms);
            $ms = str_replace("-", "_", $ms);
        }
        if (strpos($value, '.US.FE') !== false) {
            $linux = $value;
        }
    }

    $user = "All";
    if ($_SESSION['userid'] == 5) {
        $user = "Dan";
    }
    $result = Jenkins::deploy_machine($selected, $linux, $ms, $user, $gitbranch);
    if (isset($result)) {
        echo 1;
    } else {
        echo 0;
    }
    exit();
}

// Buttons functionality to stop, start, Checkpoint, Revert, delete jenkins environment
if (isset($_POST['ids']) && isset($_POST['NAME'])) {
    $name = clean_str($_POST['NAME']);
    $id = trim($_POST['ids']);
    $user_id = $_SESSION['userid'];
    if (isset($_POST['comp-1'])) {
        $machine_1 = clean_str($_POST['comp-1']);
        $machine_2 = clean_str($_POST['comp-2']);
    }
    $table = $env->setOption($id);
    $result = Jenkins::options($table, $name);
    if (isset($result)) {
        switch ($result) {
        case 'Stop':
          sleep(5);
          $success = $env->start($machine_1, $machine_2);
          $option = 'Start';
          break;
        case 'Start':
          sleep(5);
          $success = $env->stop($machine_1, $machine_2);
          $option = 'Stop';
          break;
        case 'check':
          sleep(5);
          $job = new Job("zz-CheckpointEnv");
          $url = $job->getLastBuild();
          $pool = Jenkins::pooling($url);
          break;
        case 'revert':
          sleep(5);
          $job = new Job("zz-CheckpointApplyEnv");
          $url = $job->getLastBuild();
          $pool = Jenkins::pooling($url);
          break;
        default:
          break;
      }
        if (isset($option)) {
            if ($success) {
                echo $option;
            } else {
                echo 'Fail';
            }
        }
        if (isset($pool)) {
            if ($pool) {
                echo $result;
            } else {
                echo 'Fail';
            }
        }
    }
    if ($name === 'delete') {
        $delete = $env->delete($id, $user_id);
        if ($delete) {
            $gone = Jenkins::delete($user_id);
            if ($gone) {
                echo 'Success';
            } else {
                echo 'Fail';
            }
        } else {
            echo 'database';
        }
    }
    exit();
}

// deploy host file button
if (isset($_POST['deployid'])) {
    $enname = $env->srv_GetAllEnvironmentNamefromID($_POST['deployid']);
    $hh = $env->GetHosts_All($_POST['deployid']);
    $hh = array_shift($hh[0]);
    $hh = str_replace('&#x0D;', "", $hh);
    $hostFile = clean_str($hh);
    $hostFile = str_replace('', "", $hostFile);
    $hostFile = "#hosts ". $enname[0]["ENV_Name"] ."\r\n".$hostFile . "\r\n" . "#endhosts" . "\r\n";
    try {
        Server::openSocket($hostFile);
    } catch (Exception $e) {
        echo $e;
    }
    exit();
}

// Continue building our template
$final .= parseTemplate($tpl_b, array('[+option+]' => $opt,
                                      '[+table+]' => $table,
                                      '[+DEV1+]' => $DEV1,
                                      '[+DEV2+]' => $DEV2));



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
// close connection to database
Config::db_close();
