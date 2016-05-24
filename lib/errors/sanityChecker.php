<?php

class sanityChecker
{

  protected $errorHandler;
  protected $settingsHandler;
  protected $fileList;

  public function __construct() {

    $this->errorHandler = new errorHandler();
    $this->settingsHandler = new settingsHandler();

    //load up the files to be checked
    $fileList['/errors/'] = [
      "error.php",
      "errorHandler.php",
      "sanityChecker.php"
    ];

    $fileList['/settings/'] = [
      "settingsHandler.php"
    ];

    $fileList['/users/'] =
      ["user.php",
        "userHandler.php"
      ];

    $fileList['/web/'] =
      ["webCore.php"
      ];

    $fileList['/'] = [
      "fileHandler.php",
      "uploadHandler.php"
    ];

    $this->fileList = $fileList;

  }

  public function checkSettings() {
    $fatal = false;
    if ($this->settingsHandler->getSettings()['security']['password_hash'] === "") {

      $this->settingsHandler->changeSetting('security', 'password_hash', password_hash('password', PASSWORD_DEFAULT));
      echo "password changed to 'password'<br>";

    }
    if (!isset($this->settingsHandler->getSettings()['viewer']['theme'])) {

      $this->settingsHandler->changeSetting('viewer', 'theme', 'red.css');
      echo "Theme was unset, set to default 'red'. <br>";

    }

    $location = $this->settingsHandler->getSettings()['uploads']['location'];
    if ((!isset($location) || ($location == '/full/path/to/your/upload-base-directory/'))) {
      echo "Upload directory not correctly configured in config.php.<br/>";
      $fatal = true;
    }

    if (!isset($this->settingsHandler->getSettings()['mysql'])) {
      echo "MySQL section missing in config.php<br/>";
      $fatal = true;
    } else {
      $mysql = $this->settingsHandler->getSettings()['mysql'];
      if (!isset($mysql['host'])) {
        echo "Missing MySQL host!<br/>";
        $fatal = true;
      }
      if (!isset($mysql['user'])) {
        echo "Missing MySQL username!<br/>";
        $fatal = true;
      }
      if (!isset($mysql['pass'])) {
        echo "Missing MySQL pass!<br/>";
        $fatal = true;
      }
      if (!isset($mysql['database'])) {
        echo "Missing MySQL database!<br/>";
        $fatal = true;
      }
    }

    if ($fatal == true) {
      die("Required configuration missing.");
    }
	}

  public function checkFiles() {
    // false = quiet
    // true = verbose

    $okay = true;
    $missing = [];

    foreach ($this->fileList as $dir => $files) {

      foreach ($files as $file) {

        $location = $GLOBALS['dir'] . '/lib' . $dir . $file;

        if (!file_exists($location)) {

          $okay = false;
          array_push($missing, $location);

        }
      }
    }
  }
}


?>