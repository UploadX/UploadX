<?php

class mysqlHandler
{

  protected $settingsHandler;
  protected $errorHandler;
  protected $userHandler;

  protected $files;

  protected $configArray;

  private $sqlHost;
  private $sqlUser;
  private $sqlPass;
  private $sqlDB;

  private $connected = false;
  private $sqlLink;

  function __construct(){

    $this->settingsHandler = new settingsHandler();
    $this->errorHandler = new errorHandler();
    $this->userHandler = new userHandler();
    $this->files = json_decode(file_get_contents(__DIR__.'/files/files.json'), true);

    $this->sqlHost = dbConfig['host'];
    $this->sqlUser = dbConfig['user'];
    $this->sqlPass = dbConfig['pass'];
    $this->sqlDB = dbConfig['database'];
  }

  private function connect(){
    $db = new mysqli($this->sqlHost, $this->sqlUser, $this->sqlPass, $this->sqlDB);
    if($db->connect_errno > 0){
//      TODO: Add MySQL Connection Error Handler
//      $this->errorHandler->throwError('mysql:connection');
      $db->close();
      /* Temp and Dirty Error Handling */
      die('Unable to connect to database [' . $db->connect_error . ']');
    }else{
      return $db;
    }

  }
  
  public function checkID($id){
    $conn = $this->connect();
    $id = $conn->real_escape_string($id);
    $query = "SELECT file_id FROM uploadx_files WHERE `file_id`='$id'";
    $result = $conn->query($query);
    if(!$result){
      die($conn->error);
    }
    if(!$result->num_rows == 1){
      $conn->close();
      return false;
    }else{
      $conn->close();
      return true;
    }
  }
  public function getFileData($file_id) {
    $conn = $this->connect();
    $id = $conn->real_escape_string($file_id);

    $query = "SELECT * FROM uploadx_files WHERE `file_id`='$id'";
    if(!$result = $conn->query($query)){
      die($conn->error);
    }
    if(!$assoc = $result->fetch_assoc()){
      die($conn->error);
    }else{
      $conn->close();
     return $assoc;
    }
  }
  /**
   * @param $new_file_name
   * @param $file_id
   * @param $old_file_name
   * @param $file_type
   * @param $uploader_id
   * @param $uploader_ip
   * @param $time
   * @param $file_size
   * @param $delete
   * @return bool
   */
  public function insert($new_file_name, $file_id, $old_file_name, $file_type, $uploader_id, $uploader_ip, $time, $file_size, $delete){
    $conn = $this->connect();

    $new = $conn->real_escape_string($new_file_name);
    $file_id = $conn->real_escape_string($file_id);
    $old = $conn->real_escape_string($old_file_name);
    $type = $conn->real_escape_string($file_type);
    $uploader_id = $conn->real_escape_string($uploader_id);
    $ip = $conn->real_escape_string($uploader_ip);
    $time = $conn->real_escape_string($time);
    $size = $conn->real_escape_string($file_size);
    $delete = $conn->real_escape_string($delete);

    $query = "INSERT INTO uploadx_files (file_name, file_id, file_original, file_type, uploader_id, uploader_ip, upload_time, upload_size, delete_after) VALUES ('$new', '$file_id', '$old', '$type', '$uploader_id', '$ip', '$time', '$size', '$delete')";

    $result = $conn->query($query);
    if(!$result){
      die($conn->error);
    }else{
      $conn->close();
      return true;
    }
  }

  /**
   * Update the views for $file_id
   *
   * @param $file_id
   */
  public function updateViews($file_id){
    $conn = $this->connect();
    $id = $conn->real_escape_string($file_id);
    $query = "SELECT * FROM uploadx_files WHERE file_id = '$id'";
    if(!$result = $conn->query($query)){
      die($conn->error);
    }
    if($result->num_rows == 1){
      $assoc = $result->fetch_assoc();
      $count = $assoc['access_count'];
      $count++;
      $query = "UPDATE uploadx_files SET access_count = $count WHERE file_id = '$id'";
      if(!$result = $conn->query($query)){
        die($conn->error);
      }
    }else{
      die($result->num_rows);
    }
  }

  /**
   * Execute an (UNSAFE!) MySQL Query
   *
   * @param $query
   * @return bool
   */
  public function query($query){
    $conn = $this->connect();
    $result = $conn->query($query);
    if(!$result){
      die($conn->error);
    }
    return true;
  }
}