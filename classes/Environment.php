<?php
require_once '../include/functions.php';
spl_autoload_register('myAutoloader');
/**
 * Sets all quries ready for execution regarding to environment
 * @param $db holds set up for database
 * @param $con get connection from database class
 */
class Environment
{
    private $db;
    private $con;

    public function __construct()
    {
        $this->setDb();
        $this->setConnection();
    }

    /*
     * Sets database
     */
    public function setDb()
    {
        $this->db = new Database();
    }

    /*
     * Set connection from database
     */
    public function setConnection()
    {
        $this->con = $this->db->getConnection();
    }

    /**
     * @return array of pool environment or if is empty return null
     */
    public function getPoolIdNameType()
    {
        $query = "EXEC [dbo].[srv_GetPoolEnv]";
        $entity = $this->db->getParams($query);
        if (!empty($entity)) {
            return $entity;
        }
        return;
    }

    public function getHyperVFreeMem($hypervhost)
    {
        $query = "EXEC	[dbo].[srv_GetHypervFreeMemory] @HostHyperV = N'$hypervhost'";
        $entity = $this->db->getParams($query);
        if (!empty($entity)) {
            return $entity;
        }
        return;
    }



    /**
     * Rename pool in database
     * @param $envId takes Environment id from pool
     * @param $name takes new name that is set
     * @param $userId takes id of user
     * @return boolean
     */
    public function renamePool($envId, $name, $userId)
    {
        $query = "EXEC [dbo].[srv_RenamePool] @envid = $envId, @newname ='" . $name . "', @userid= $userId";
        $sth = $this->con->prepare($query);
        if (isset($sth)) {
            $sth->execute();
            return true;
        }
        return false;
    }
    /**
     * get table with ENV_id, ENV_Name, DeployedHost, DateCheckedout, ENVTypeId, ENVType
     * @return null if is empty
     */
    public function getAllNames()
    {
        $query = "EXEC [dbo].[srv_GetAllEnvironmentName]";
        $stmt = $this->db->getParams($query);
        if (!empty($stmt)) {
            return $stmt;
        }
        return;
    }

    /**
     * @param $id is environment id number
     * @return array of all hosts table and his pattern as well
     */
    public function getAllHosts($id)
    {
        $query = "EXEC [dbo].[srv_GetHosts] @ENV_id = $id";
        $stmt = $this->db->getParams($query);
        if (!empty($stmt)) {
            return $stmt;
        }
        return;
    }

    /**
     * Builds up the missing part of the table
     * @return array with ENV_Name, html tags for the table , EnvID
     */
    public function getAll()
    {
        $query = "EXEC	 [dbo].[srv_GetAllEnvironments_returnhtml]";
        $table = $this->db->getParams($query);
        if (!empty($table)) {
            return $table;
        }
        return;
    }
    /**
     * Insert data into database
     * @param  $database table that we insert into
     * @param  $data  has to be array of column and row
     */
    public function execInsert($database, $data)
    {
        $this->db->insert($database, $data);
    }

    /**
     * Update data in our database
     * @param  $table is table that we update
     * @param  $id is id of the environment job that we updating
     * @param  $fields array with column and row that we want to update
     * @return boolean if we update the table
     */
    public function execUpdate($table, $id, $fields)
    {
        $x = $this->db->update($table, $id, $fields);
        if ($x) {
            return true;
        }
        return false;
    }

    /**
     * Unique table with id, name, dateAdded, mustDelete, DeployedHost
     * according to environment id number
     * @param $id needs environment id Number
     * @return null if table is empty
     */
    public function setOption($id)
    {
        $query = "EXEC [dbo].[srv_GetVMfromENVID] @EnvID = $id";
        $table = $this->db->getParams($query);
        if (isset($table)) {
            return $table;
        }
        return;
    }

    /**
     * Delete environment permanently. Once is done it can not be undone
     * @param $id is an environment id number
     * @param $userId is user id number
     * @return boolean if queries were Successfully executed
     */
    public function delete($id, $userId)
    {
        $query = "EXEC [dbo].[srv_SetDeleteEnv] @ENV_id= $id, @userid= $userId";
        $sth = $this->con->prepare($query);
        if (isset($sth)) {
            $sth->execute();
            return true;
        }
        return false;
    }

    /**
     * We find the status of current machine
     * @param $vmName is the machine name example(136.UK-WS)
     * @return the status of the machine
     */
    public function getStatus($vmName)
    {
        $query = "EXEC [dbo].[srv_GetEnvironmentStatus_ByName] @VMname ='" . $vmName . "'";
        $status = $this->db->getParams($query);
        if (isset($status)) {
            return $status[0]['VMState'];
        }
        return;
    }

    /**
     * Pooling with database until the machine has start
     * @param $machine_1 takes the 1st name of the machine
     * @param $machine_2 takes the 2nd name of the machine if is exists
     * @return boolean
     */
    public function start($machine_1, $machine_2)
    {
        $status_1 = 'Off';
        $status_2 = 'Off';
        if (isset($machine_1) && isset($machine_2)) {
            while ($status_2 === 'Off') {
                $status_2 = $this->getStatus($machine_2);
                sleep(5);
            }
            return true;
        } else {
            if (isset($machine_1)) {
                while ($status_1 === 'Off') {
                    $status_1 = $this->getStatus($machine_1);
                    sleep(5);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Pooling with database until the machine has stop
     * @param $machine_1 takes the 1st name of the machine
     * @param $machine_2 takes the 2nd name of the machine if is exists
     * @return boolean
     */
    public function stop($machine_1, $machine_2)
    {
        $status_1 = 'Running';
        $status_2 = 'Running';
        if (isset($machine_1) && isset($machine_2)) {
            while ($status_2 === 'Running') {
                $status_2 = $this->getStatus($machine_2);
                sleep(5);
            }
            return true;
        } else {
            if (isset($machine_1)) {
                while ($status_1 === 'Running') {
                    $status_1 = $this->getStatus($machine_1);
                    sleep(5);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Get the whole table for our view log
     * @param $name1 holds the machine name
     * @param $name2 holds the machine name
     * @return $table the whole table or null
     */
    public function viewLog($name1, $name2)
    {
        $query = "SELECT [JobJenJobID]
                      ,[JobDate]
                      ,[JobMachine]
                      ,[JobName]
                      ,[JobHost]
                	  ,(select [JobStatusDesc] FROM [dbo].[tbl_JobStatus] where JobStatusID = JobStatus) as [Status]
                      ,[JobLog]

                  FROM [dbo].[tbl_Jobs]  where 
                           [JobMachine] like '" . $name1 . "%'  
                        or [JobMachine] like '" . $name2 . "%' 
                        or [JobMachine] like replace(replace('" . $name1 . "%','.','_'),'-','_')
                        or [JobMachine] like replace(replace('" . $name2 . "%','.','_'),'-','_')
order by [JobDate] desc";
//in ('" . $name1 . "', '" . $name2 . "',replace(replace('" . $name1 . "','.','_'),'-','_'),replace(replace('" . $name2 . "','.','_'),'-','_')) order by [JobDate] desc";
        $table = $this->db->getParams($query);
        if (isset($table)) {
            return $table;
        }
        return;
    }

    public function GetHosts_All($ENVID)
    {
        $query = "EXEC	 [dbo].[srv_GetHosts_All_2] @ENV_id = $ENVID";
        $stmt = $this->db->getParams($query);

        if (!empty($stmt)) {
            return $stmt;
        }
        return;
    }

    public function srv_GetAllEnvironmentNamefromID($ENVID)
    {
        $query = "EXEC	 [dbo].[srv_GetAllEnvironmentNamefromID] @ENV_id  = $ENVID";
        $stmt = $this->db->getParams($query);

        if (!empty($stmt)) {
            return $stmt;
        }
        return;
    }

    /**
     * get table for pie chart
     * @param $month holds int month
     * @param $year holds int year
     * @return $table whole table or null
     */
    public function pieChart($month, $year)
    {
        $query = "EXEC [dbo].[getPieUsedENVdata] @yearr = " . $year . ",@mnth = " . $month;
        $table = $this->db->getParams($query);
        if (isset($table)) {
            return $table;
        }
        return;
    }
}
