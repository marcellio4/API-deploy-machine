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
    private static $username = 'dev';
    private static $password = '20acca50c21064f2a68a7a82caceb6cd';

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
     * example(@jenkins.experienceengine.com:8080/job/CTO/lastBuild/api/json) only like this
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
     * @return boolean
     */
    public static function deploy_machine($value, $linux, $ms, $user, $branch)
    {
        switch ($value) {
          case 'CUST-CTO':
            if (!empty($linux)) {
                $job = new Job("CTO", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
                $parameter = "&Linux%20WS=" . $linux . "&isDev=yes&Branch_Specifier=$branch";
            }
            break;
          case 'US1-depXF':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("US-1-Deploy_XenonFeed", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
                $parameter = "&EnvironmentNameMS=" . $ms . "&US=" . $linux . "&isDev=yes&Branch_Specifier=$branch";
            }
            break;
            //removed 14 june 2019 willem.
         // case 'US1-depCentriumres':
          //  if (!empty($linux) && !empty($ms)) {
           //     $job = new Job("US-1-Deploy_Centriumres", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
            //    $parameter = "&EnvironmentNameMS=" . $ms . "&US-linux=" . $linux . "&isDev=yes&Branch_Specifier=$branch&Username=" . $user;
           // }
           // break;
            case 'US1-depCentriumresV2':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("US-1-Deploy_CentriumresV2", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
                $parameter = "&EnvironmentNameMS=" . $ms . "&US-linux=" . $linux . "&isDev=yes&Branch_Specifier=$branch&Username=" . $user;
            }
            break;
          case 'UK1-depEIR':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("UK-1-deploy%20eliteislandholidays.com", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
                $parameter = "&MS%20uk%20webserver=" . $ms . "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
            }
            break;
          case 'UK1-depCentriumUKRes':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("UK-deploy%20centriumres.com", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
                $parameter = "&MS%20uk%20webserver=" . $ms . "&UK-EEDEV=" . $linux . "&Branch_Specifier=$branch";
            }
            break;
          case 'UK1-depResportBeds':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("UK-1-deploy_resortbedsdirect.com", "eefc40a3-cfc4-49c1-aa2b-169245cf0dep");
                $parameter = "&EnvironmentNameMicrosoft=" . $ms . "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
            }
            break;
          case 'UK-1-deploy_rmidirectnew':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("UK-1-deploy_rmidirectnew", "eefc40a3-cfc4-49c1-aa2b-169245cf0rmi");
                $parameter = "&EnvironmentNameMicrosoft=" . $ms . "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
            }
            break;
          case 'UK-1-deploy_interlinebyelite':
            if (!empty($linux) && !empty($ms)) {
                $job = new Job("UK-1-deploy%20interlinebyelite", "eefc40a3-cfc4-49c1-aa2b-169245cf0ibe");
                $parameter = "&EnvironmentNameMicrosoft=" . $ms . "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
            }
            break;

          case 'php-Booking':
              if (!empty($linux) && !empty($ms)) {
                  $job = new Job("php-Booking", "eefc40a3-cfc4-49c1-aa2b-1692php-Booking");
                  $parameter = "&EnvironmentNameMicrosoft=" . $ms . "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
              }
              break;
              
          case 'CUST-Newsletters':
              if (!empty($linux)) {
                  $job = new Job("CUST-Newsletters", "eefc40a3-cfc4-49c1-aa2b-Newsletters");
                  $parameter = "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
              }
              break;
              
          case 'UK-1-deploy_XML_Linux':
              if (!empty($linux)) {
                  $job = new Job("UK-1-deploy_XML_Linux", "eefc40a3-cfc4-49c1-aa2b-xml");
                  $parameter = "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
              }
              break;
              
          case 'UK-1-deploy-www.resort-marketing.co.uk':
              if (!empty($linux)) {
                  $job = new Job("UK-1-deploy-www.resort-marketing.co.uk", "eefc40a3-cfc4-49c1-aa2b-169245cf0rmi");
                  $parameter = "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
              }
              break;
              
          case 'UK-1-deploy_SB-Logger-Reciever':
              if (!empty($linux)) {
                  $job = new Job("UK-1-deploy_SB-Logger-Reciever", "eefc40a3-cfc4-49c1-aa2b-sblogger");
                  $parameter = "&EnvironmentNameLinux=" . $linux. "&Branch_Specifier=$branch";
              }
              break;
         
          case 'US-1-StagingDBtoDEV':
              if (!empty($ms)) {
                  $job = new Job("US-1-StagingDBtoDEV", "eefc40a3-cfc4-49c1-aa2b-169245cf0dbsy");
                  $parameter = "&EnvironmentNameMicrosoft=" . $ms;
              }
              break;
              
          case 'US-5-Tests-USCentrium':
              if (!empty($linux)) {
                  $job = new Job("US-5-Tests-USCentrium", "eefc40a3-cfc4-49c1-aa2b-169245ustest");
                  $parameter = "&EnvironmentNameLinux=" . $linux;
              }
              break;
              
              

          default:
            break;
    }
        if (isset($parameter)) {
            $job->setLinkParameter($parameter);
            $url = $job->getLink();
       
            self::executeUrl($url);
            return $job->getLastBuild();
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
            $job = new Job("zz-StartEnv", "eefc40a3-cfc4-49c1-aa2b-169245cfstat");
            $option = 'Stop';
            break;
          case 'Stop':
              $job = new Job("zz-StopEnv", "eefc40a3-cfc4-49c1-aa2b-169245cfstat");
              $option = 'Start';
            break;
          case 'check':
            $job = new Job("zz-CheckpointEnv", "eefc40a3-cfc4-49c1-aa2b-169245cfstat");
            $option = 'check';
            break;
          case 'revert':
            $job = new Job("zz-CheckpointApplyEnv", "eefc40a3-cfc4-49c1-aa2b-169245cfstat");
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
            $job = new Job("zz-DeleteVMs", "deletec40a3-cfc4-49c1-aa2b-169245cf0923");
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
     * @param $url need url of the lastBuild from jenkins (http://jenkins.experienceengine.com:8080/job/JOB-NAME/lastBuild/api/json)
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
