<?php
require_once '../include/functions.php';
spl_autoload_register('myAutoloader');
/**
 * Anything that require connection with jenkins website
 * sets the url for jenkins and execute connection with Jenkins website
 * Get json files from jenkins on last build
 * @param $username user name for jenkins login
 * @param $password is token for jenkins
 */

class Jenkins
{
    private static $username = '';
    private static $password = '';

    /**
     * Execute jenkins link
     * Do not pass the whole url
     * pass without credential and http
     * @param $url Holds part of the url without credential
     * @param $set_url create valid url
     */
    public static function executeUrl($url)
    {
        $set_url = "https://" . self::$username . ":" . self::$password . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $set_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // grab URL and pass it to the browser
        curl_exec($ch);
        // close cURL resource, and free up system resources
        curl_close($ch);
    }
    /**
     * Is json file that we get from jenkins last build on
     * Do not pass the whole url
     * example(@jenkins:8080/job/CTO/lastBuild/api/json) only like this
     * @param $url Holds part of the url without credential
     * @param $set_url create valid url
     * @return $json is json file
     */
    public static function jsonFile($url)
    {
        $set_url = "https://" . self::$username . ":" . self::$password . $url;
        $curl = curl_init($set_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $ret = curl_exec($curl);
        $json = json_decode($ret);
        return $json;
    }

    /**
     * Deploy machine into particular job that we sending from
     * @param  $value is job name that we need to check on
     * @param  $linux is for linux machine
     * @param  $ms is for microsoft machine
     * @param  $user is extra future of error logs for example, for the specific user
     * @param  $branch git branch that we pool for our job
     * @return boolean
     */
    public static function deploy_machine($value, $linux, $ms, $user, $branch)
    {
        $db = new Database();
        $query = "SELECT DisplayJenkinsJobName, JenkinsToken FROM tbl_JenkinsJobs where JenkinsJobName = '{$value}'";
        $data = $db->getParams($query);
        $job = new Job($data[0]['DisplayJenkinsJobName'], $data[0]['JenkinsToken']);
        $parameter = "&EnvironmentNameMS=$ms&EnvironmentNameLinux=$linux&isDev=yes&isLive=no&Branch_Specifier=$branch&Username=$user";

        if (isset($job)) {
            $job->setLinkParameter($parameter);
            $url = $job->getLink();

            self::executeUrl($url);
            return $data[0]['DisplayJenkinsJobName'];
        }
        return;
    }

    /**
     * Start the job either stop, start, Checkpoint or revert
     * @param $table must have table with VMname and DeployedHost column
     * @param $name check which options has been choose (start, stop, Checkpoint or revert)
     * @return string of the choosen option or null
     */
    public static function options($table, $name)
    {
        switch ($name) {
          case 'Start':
            $job = new Job("zz-StartEnv", $token);
            $option = 'Stop';
            break;
          case 'Stop':
              $job = new Job("zz-StopEnv", $token);
              $option = 'Start';
            break;
          case 'check':
            $job = new Job("zz-CheckpointEnv", $token);
            $option = 'check';
            break;
          case 'revert':
            $job = new Job("zz-CheckpointApplyEnv", $token);
            $option = 'revert';
            break;
          default:
            break;
    }
        if (isset($option)) {
            foreach ($table as $row) {
                $parameter = "&hyper-v%20machine=" . strtoupper($row['DeployedHost']) . "&VMname=" . $row['VM_name'];
                $job->setLinkParameter($parameter);
                $url = $job->getLink();
                self::executeUrl($url);
            }
            return $option;
        }
        return;
    }

    /**
     * Delete permanently environment
     * @param $id holds user id
     * @return boolean
     */
    public static function delete($id)
    {
        if (isset($id)) {
            $job = new Job("zz-DeleteVMs", $token);
            $parameter = "&userid=" . $id;
            $job->setLinkParameter($parameter);
            $url = $job->getLink();
            self::executeUrl($url);
            return true;
        }
        return false;
    }

    /**
     * pooling with script until jenkins is finish the job
     * @param $url need url of the lastBuild from jenkins (http://jenkins.:8080/job/JOB-NAME/lastBuild/api/json)
     * @return boolean
     */
    public static function pooling($url)
    {
        sleep(5);
        $sleep = 0;
        $c = 0;
        $min = (10*60)/5;
        while ($sleep < 1) {
            $json = self::jsonFile($url);
            $sleep = $json->duration;
            $c++;
            if ($c > $min) {
                return false;
                break;
            }
            sleep(5);
        }
        if ($json->result === "SUCCESS") {
            return true;
        }
        return false;
    }
}
