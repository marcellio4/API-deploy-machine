<?php
require_once '../include/functions.php';
// auto load classes
spl_autoload_register('myAutoloader');

/**
 * All sql queries that are related to user
 * Connect to database that we can work with
 * @param $db holds database connection
 */
class User
{
  private $db;

  function __construct(){
    $this->setDb();
  }
  /*
   * Sets database
   */
    public function setDb(){
    $this->db = new Database();
  }
  /**
   * Checking if user exist in database
   * @param $username takes the name of user
   * @param $pwd takes password of the user
   * @return array of user id - example array([UserID] => 4)
   */
    public function getId($username, $pwd){
      $query = "EXEC [dbo].[srv_GetAuth] @Uname = $username,@PWord = $pwd";
      $entity = $this->db->getParams($query);
      if(!empty($entity)){
        return $entity;
      }
      return;
    }
}

 ?>
