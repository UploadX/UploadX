<?php

/*

@author: Pips

@title: Uploader Handler
@desc: Class that manages uploading from ShareX.

*/

class uploadHandler
{

  protected $userHandler;
  protected $errorHandler;
  protected $settingsHandler;
  protected $fileHandler;

  private $db;
  function __construct()
  {

    $this->errorHandler = new errorHandler();
    $this->userHandler = new userHandler();
    $this->settingsHandler = new settingsHandler();
    $this->fileHandler = new fileHandler();
    $this->db = new mysqlHandler();
  }

  //bulk is done here
  function process()
  {

    if (!isset($_POST['key'])) {

      $this->errorHandler->throwError('upload:nokey');

    } else {

      $key = $_POST['key'];

      if ($this->db->keyCheck($key)) {

        $uploader = $this->db->keyGetUser($key);
        $status = $uploader['enabled'];

        if ($status != '1') {
          $this->errorHandler->throwError('upload:banned');
        } else {
          $disallowed_files = $this->settingsHandler->getSettings()['security']['disallowed_files'];
          if (in_array(pathinfo($_FILES['file']['tmp_name'] . $_FILES['file']['name'], PATHINFO_EXTENSION), $disallowed_files)) {
            $this->errorHandler->throwError('upload:badextension');
          } else {
            ini_set("display_errors", 0);
            $this->fileHandler->saveFile($_FILES['file'], $uploader);
          }
        }
      } else {
        $this->errorHandler->throwError('upload:wrongkey');
      }

    }

  }

  // true/fase function to make sure that somebody is uploading, and with the right 'settings'
  function checkForUpload()
  {
    if (isset($_FILES['file'])) {
      return true;
    } else {
      return false;
    }

  }

}

?>