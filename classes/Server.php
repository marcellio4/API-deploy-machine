<?php
/**
 *
 */
class Server
{
  /**
   * Open connection to server
   * @param $text text file that is send to server
   * @return true if connection has been made
   */
  public static function openSocket($text){
    if (empty($text)) {
      throw new Exception("text is empty");
      return false;
    }
    $timeout = 30;
    if ($text === 'ping') {
      $timeout = 1;
    }
    $socket = fsockopen($_SERVER['REMOTE_ADDR'], '11666', $errno, $errstr,$timeout);
    if ($socket) {
      fputs($socket, $text);
      fclose($socket);
      $socket = "";
      return true;
    }
      throw new Exception("Connection failed! ");
      return false;
  }
}

 ?>
