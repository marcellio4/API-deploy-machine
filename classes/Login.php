<?php
/**
 * Set up loging entities and set errors to true if our entities are blank.
 * This is just small validation of our login form
 * Also contain one static function for logout and destroy sessions
 * @param $uname username that is submitted
 * @param $pwd password that has been submitted
 */
class Login
{
  private $uname;
  private $pwd;

  /**
   * Boolean method Sets username otherwise
   * @return false
   */
  public function set_uname($uname){
    if($uname){
       $this->uname = $uname;
       return true;
    }
    return false;
	}

/**
 * Boolean method Sets password
 * @return false
 */
  public function set_pwd($pwd){
		if($pwd){
      $this->pwd = $pwd;
      return true;
		}
    return false;
  }

/**
 * @return username instance variable
 */
  public function getUname(){
    return $this->uname;
  }

  /**
   * @return password instance variable
   */
  public function getPwd(){
    return $this->pwd;
  }

  /**
   * If the user is not login then redirect back to login page
   * @param $user takes the name of username
   */
  public static function getAccess($user){
    if(!isset($user)){
      header("Location: ../index.php");
      exit();
    }
  }

  public static function destroy($name){
    if (isset($name)) { #delete session
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
    		$yesterday = time() - (24 * 60 * 60);
    		$params = session_get_cookie_params();
    		setcookie(session_name(), '', $yesterday,
    		$params["path"], $params["domain"],
    		$params["secure"], $params["httponly"]);
    	}
    	session_destroy();
    	header("Location: ../index.php");
    }
  }

}

?>
