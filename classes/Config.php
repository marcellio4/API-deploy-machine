<?php
/**
* Sets connection with the database and close it
* Setting connection is done by using PDO
* @param $dbuser set username
* @param $dbname set database name
* @param $dbpwd set database password
* @param $dbhost set database host
* @param $conn set connection to database
*/

class Config {
  private $dbuser;
  private $dbname;
  private $dbpwd;
  private $dbhost;
  protected $conn;

// Open connection to database
  public function __construct(){
    $this->db_connect();
  }

  public function db_connect(){
    $this->dbuser = 'Dev';
    $this->dbname = 'Dev_Env';
    $this->dbpwd = 'D3v';
    $this->dbhost = '192.168.10.13';
    //Create connection
    $this->conn = new PDO("sqlsrv:server=$this->dbhost;Database=$this->dbname",  $this->dbuser, $this->dbpwd);
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
  }

  public static function db_close() {
    if (isset(self::$conn)) {
      self::$conn = null;
      unset(self::$conn);
    }
  }

}
 ?>
