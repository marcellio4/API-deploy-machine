<?php
/**
 * Create link for certain job to make connection with jenkins
 * @param $name name of the job
 * @param $link the url for jenkins
 * @param $token valid credential from jenkins token (each job might or might not have diffrent token)
 */

class Job
{
    private $name;
    private $link;
    private $token;

    public function __construct($name, $token = null)
    {
        $this->name = $name;
        $this->token = $token;
    }

    /**
     * @return the whole url link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Create link for jenkins job
     * @param $parameters takes all parameters that we need for url to connect with the jenkins job
     */
    public function setLinkParameter($parameters)
    {
        return  $this->link = "@jenkins/job/" . $this->name . "/buildWithParameters?token=" . $this->token . $parameters;
    }

    /**
     * @return url link for jenkin last job
     */
    public function getLastBuild()
    {
        return $lastBuild = "@jenkins/job/" . $this->name . "/lastBuild/api/json";
    }
}
