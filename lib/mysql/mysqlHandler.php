<?php

class mysqlHandler
{

  protected $errorHandler;

  protected $files;

  protected $configArray;

  private $config;
  private $sqlHost;
  private $sqlUser;
  private $sqlPass;
  private $sqlDB;

  private $conn;
  private $root_dir;

  private $className;
  function __construct(){

    $this->errorHandler = new errorHandler();

    $this->config = config;
    $this->sqlHost = dbConfig['host'];
    $this->sqlUser = dbConfig['user'];
    $this->sqlPass = dbConfig['pass'];
    $this->sqlDB = dbConfig['database'];

    $this->root_dir = ROOTPATH;
    $this->className = "mysqlHandler";
  }
  
  private function connect(){
    $db = new mysqli($this->sqlHost, $this->sqlUser, $this->sqlPass, $this->sqlDB);
    if($db->connect_errno > 0){
      /* Temp and Dirty Error Handling */
      echo 'Unable to connect to database [' . $db->connect_error . ']';
      $db->close();
      exit();
    } else {
      $this->conn = $db;
      return $db;
    }

  }

  /**
   * Get the current number of users
   *
   * @return int|string
   */
  public function countUsers() {
    $this->checkConn();
    $query = "SELECT user_id FROM uploadx_users";
    if (!$result = $this->checkQuery($query)) {
      $this->logAdd('WARNING', 'MySQL', 'Couldn\'t count the number of users');
      return "Error.";
    } else {
      return $result->num_rows;
    }
  }
  /**
   * * Get the current number of uploaded files
   *
   * @return int|string
   */
  public function countUploads() {
    $this->checkConn();
    $query = "SELECT file_id FROM uploadx_files";
    if (!$result = $this->checkQuery($query)) {
      $this->logAdd('WARNING', 'MySQL', 'Couldn\'t count the number of uploads');
      return "Error.";
    } else {
      return $result->num_rows;
    }
  }

  /**
   * Create a FATAL MySQL error.
   * This should be used to forcefully stop processing
   *
   * @param $this->conn
   * @param $last_query
   */
  private function errorFatal($caller, $last_query) {
    $this->errorHandler->throwMySQLError("$this->className::$caller", $this->conn, $last_query, 'FATAL');
    exit();
  }

  /**
   * Get stored data on an update
   *
   * @param $file_id
   * @return mixed
   */
  public function uploadGetData($file_id) {
    $this->checkConn();
    $id = $this->cleanVar($file_id);

    $query = "SELECT * FROM uploadx_files WHERE `file_id`='$id'";
    if(!$result = $this->checkQuery($query)){
      $this->errorFatal("uploadGetData", $query);
    }
    if(!$assoc = $result->fetch_assoc()){
      return false;
    }else{
      return $assoc;
    }
    return false;
  }

  /**
   * Check if $key is valid
   *
   * @param $key
   * @return bool
   */
  public function keyCheck($key) {
    $this->checkConn();
    $key = $this->cleanVar($key);
    $query = "SELECT access_key FROM uploadx_users WHERE access_key = '$key'";
    if (!$result = $this->checkQuery($query)) {
      $this->errorFatal("keyCheck", $query);
    }
    if ($result->num_rows == 1) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Get the user the key belongs to
   *
   * @param $key
   * @return string
   */
  public function keyGetUser($key) {
    $this->checkConn();
    $key = $this->cleanVar($key);
    $query = "SELECT * FROM uploadx_users WHERE access_key = '$key'";
    if (!$result = $this->checkQuery($query)) {
      $this->errorFatal('keyGetUser', $query);
    }
    if ($result->num_rows == 1) {
      $data = $result->fetch_assoc();
      return $data;
    }
    $this->errorFatal("keyGetUser", "Couldn't find user");
  }
  /**
   * Add a new line to the log table
   *
   * @param $log_level
   * @param $log_type
   * @param $log_message
   */
  public function logAdd($log_level, $log_type, $log_message){
    $this->checkConn();
    $log_level = $this->cleanVar($log_level);
    $log_type = $this->cleanVar($log_type);
    $log_message = $this->cleanVar($log_message);
    $log_time = date("Y-m-d h:ia");

    $query = "INSERT INTO uploadx_logs (`log_level`, `log_type`, `log_message`) VALUES ('$log_level', '$log_type', '$log_message', '$log_time')";
    if (!$result = $this->checkQuery($query)){
      $this->errorFatal("logAdd", $query);
    }

  }
  /**
   * Add an upload
   *
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
  public function uploadAdd($new_file_name, $file_id, $old_file_name, $file_type, $uploader_id, $uploader_ip, $time, $file_size, $delete){
    $this->checkConn();

    $new = $this->cleanVar($new_file_name);
    $file_id = $this->cleanVar($file_id);
    $old = $this->cleanVar($old_file_name);
    $type = $this->cleanVar($file_type);
    $uploader_id = $this->cleanVar($uploader_id);
    $ip = $this->cleanVar($uploader_ip);
    $time = $this->cleanVar($time);
    $size = $this->cleanVar($file_size);
    $delete = $this->cleanVar($delete);

    $query = "INSERT INTO uploadx_files (file_name, file_id, file_original, file_type, uploader_id, uploader_ip, upload_time, upload_size, delete_after) VALUES ('$new', '$file_id', '$old', '$type', '$uploader_id', '$ip', '$time', '$size', '$delete')";

    $result = $this->checkQuery($query);
    if(!$result){
      $this->errorFatal("uploadAdd", $query);
    }else{
      return true;
    }
  }

  /**
   * Remove an upload record from the database
   *
   * @param $id
   * @return bool
   */
  public function uploadDelete($id) {
    $this->checkConn();
    $id = $this->cleanVar($id);
    if ($this->uploadCheckID($id)) {
      $query = "DELETE FROM uploadx_files WHERE file_id = '$id'";

      if (!$result = $this->checkQuery($query)) {
        $this->errorFatal("uploadDelete", $query);
      } else {
        return true;
      }
    }
    return false;
  }
  /**
   * Check if the upload ID exists
   *
   * @param $id
   * @return bool
   */
  public function uploadCheckID($id){
    $this->checkConn();
    $id = $this->cleanVar($id);
    $query = "SELECT file_id FROM uploadx_files WHERE `file_id`= '$id'";
    if (!$result = $this->checkQuery($query)) {
      $this->errorFatal("uploadCheckID", $this->conn, $query);
    }
    if (!$result->num_rows == 1) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Get a list of uploads starting from $start_num
   * Returns up to 100 rows
   *
   * @param $start_row
   * @return array|string
   */
  public function uploadList($start_row) {
    $this->checkConn();
    $start_row = $this->cleanVar($start_row);
    $query = "SELECT * FROM uploadx_files ORDER BY rowid DESC LIMIT $start_row,100";

    if (!$result = $this->checkQuery($query)) {
      return 'Error!';
    }
    if ($result->num_rows > 0) {
      $uploads = array();
      while ($row = $result->fetch_assoc()) {
        array_push($uploads, $row);
      }
      return $uploads;
    }
    return 'No Results Found';
  }

  /**
   * Same as uploadList but returns for a user
   *
   * @param $start_row
   * @param $username
   * @return array|string
   */
  public function uploadListUser($start_row, $username) {
    $this->checkConn();
    $start_row = $this->cleanVar($start_row);
    $username = $this->cleanVar($username);
    $query = "SELECT * FROM uploadx_files WHERE uploader_id = '$username' ORDER BY rowid DESC LIMIT $start_row,100";

    if (!$result = $this->checkQuery($query)) {
      return 'Error!';
    }
    if ($result->num_rows > 0) {
      $uploads = array();
      while ($row = $result->fetch_assoc()) {
        array_push($uploads, $row);
      }
      return $uploads;
    }
    return 'No Results Found';
  }
  /**
   * Update the views for $file_id
   *
   * @param $file_id
   */
  public function updateViews($file_id){
    $this->checkConn();
    $id = $this->cleanVar($file_id);
    $query = "SELECT * FROM uploadx_files WHERE file_id = '$id'";
    if(!$result = $this->checkQuery($query)){
      $this->errorFatal("updateViews", $query);
    }
    if($result->num_rows == 1){
      $assoc = $result->fetch_assoc();
      $count = $assoc['access_count'];
      $count++;
      $query = "UPDATE uploadx_files SET access_count = $count WHERE file_id = '$id'";
      if(!$result = $this->checkQuery($query)){
        $this->errorFatal("updateViews", $query);
      }
    }else{

    }
  }
  /**
   * Create a new user
   *
   * @param $user_id
   * @param $access_key
   * @param $filesize_limit
   *
   * @return bool
   */
  public function userCreate($user_id, $access_key, $filesize_limit){
    $this->checkConn();
    $id = $this->cleanVar($user_id);
    $key = $this->cleanVar($access_key);
    $limit = $this->cleanVar($filesize_limit);

    $query = "SELECT user_id FROM uploadx_users WHERE user_id = '$id'";
    if(!$result = $this->checkQuery($query)){
      $this->errorFatal('userCreate', $this->conn, $query);
    }

    if($result->num_rows == 0) {
      $query = "INSERT INTO uploadx_users (`user_id`, `access_key`, `filesize_limit`) VALUES ('$user_id', '$key', '$limit')";
      if (!$result = $this->checkQuery($query)) {
        return false;
      } else {
        return true;
      }
    }
    return false;
  }
  /**
   * Check if the user is valid
   *
   * @param $user_id
   * @return bool
   */
  public function userCheck($user_id) {
    $this->checkConn();
    $user_id = $this->cleanVar($user_id);
    $query = "SELECT user_id FROM uploadx_users WHERE user_id = '$user_id'";
    if (!$result = $this->checkQuery($query)) {
      return false;
    } else {
      if ($result->num_rows == 1) {
        return true;
      }
    }
    return false;
  }

  /**
   * Returns the user's data
   * @param $user_id
   * @return array|string
   */
  public function userData($user_id) {
    $this->checkConn();
    $user_id = $this->cleanVar($user_id);
    $query = "SELECT * FROM uploadx_users WHERE user_id = '$user_id'";
    if (!$result = $this->checkQuery($query)) {
     return "Error";
    }
    if ($result->num_rows == 1) {
      $data = $result->fetch_assoc();
      return $data;
    }
    return "Error";
  }

  /**
   * Deletes a user
   *
   * @param $user_id
   * @return bool
   */
  public function userDelete($user_id) {
    $this->checkConn();
    $user_id = $this->cleanVar($user_id);
    $query = "DELETE FROM uploadx_users WHERE user_id = '$user_id'";
    if (!$this->checkQuery($query)) {
      return false;
    }
    return true;
  }
  /**
   *
   * Get users starting from $start_row
   *
   * @param $start_row
   * @return array|string
   */
  public function usersGet($start_row) {
    $this->checkConn();
    $start_row = $this->cleanVar($start_row);
    $query = "SELECT * FROM uploadx_users ORDER BY rowid DESC LIMIT $start_row,100";
    if (!$result = $this->checkQuery($query)) {
      $this->logAdd('WARNING', 'MySQL' , 'Failed to get users');
    } else {
      $users = array();
      while ($data = $result->fetch_assoc()) {
        $username = $data['user_id'];
        $access_key = $data['access_key'];
        $enabled = $data['enabled'];
        $user = array(
          'username' => $username,
          'access_key' => $access_key,
          'enabled' => $enabled,
        );
        array_push($users, $user);
      }
      return $users;
    }
  }
  public function usersMigrate() {
    $json_file = $this->root_dir.'/lib/files/users.json';
    $users = json_decode(file_get_contents($json_file), true);

    foreach ($users as $user => $data) {
      $this->userMigrate($user, $data['access_key'], $data['filesize_limit'], $data['enabled']);
    }
    if (!rename($json_file, $json_file.".bk")){
      die("Failed to rename the users file");
    }
  }
  /**
   * Check if $username is a valid user
   *
   * @param $username
   * @return bool
   */
  public function userExists($username) {
    $this->checkConn();
    $username = $this->cleanVar($username);
    $query = "SELECT rowid FROM uploadx_users WHERE user_id = '$username'";
    if (!$result = $this->checkQuery($query)) {

    } else if ($result->num_rows == 1) {
        return true;
    }
    return false;
  }
  /**
   *
   * @param $user_id
   * @param $access_key
   * @param $filesize_limit
   * @param $enabled
   *
   * @return bool
   */
  private function userMigrate($user_id, $access_key, $filesize_limit, $enabled){
    $this->checkConn();
    $id = $this->cleanVar($user_id);
    $key = $this->cleanVar($access_key);
    $limit = $this->cleanVar($filesize_limit);
    $enabled = $this->cleanVar($enabled);

    $query = "SELECT user_id FROM uploadx_users WHERE user_id = '$id'";
    if(!$result = $this->checkQuery($query)){
      $this->errorFatal('userMigrate', $this->conn, $query);
    }

    if($result->num_rows == 0){ //No user (yet!)
      /* Let's find out how many uploads might be tied to this user on uploadx_files */
      $upload_query = "SELECT uploader_id FROM uploadx_files";
      if(!$upload_result = $this->checkQuery($upload_query)) {
        $this->errorFatal('userMigrate', $this->conn, $upload_query);
      }
      $upload_count = $upload_result->num_rows;

      $create_query = "INSERT INTO uploadx_users (`user_id`, `access_key`, `filesize_limit`, `uploads`, `enabled`) VALUES ('$id', '$key', '$limit', '$upload_count', '$enabled')";
      if (!$create_result = $this->checkQuery($create_query)){
        $this->errorFatal('userMigrate', $this->conn, $create_query);
      }else{
        return true;
      }
    }else {
      return false;
    }
  }
  public function userSetKey($user_id, $new_key) {
    $user_id = $this->cleanVar($user_id);
    $new_key = $this->cleanVar($new_key);
    $query = "UPDATE uploadx_users SET access_key = '$new_key' WHERE user_id = '$user_id'";
    if (!$this->checkQuery($query)) {

    }
  }
  /**
   * The amount of uploads for the user
   *
   * @param $user
   * @return int
   */
  public function userUploadCount($user) {
    $this->checkConn();
    $query = "SELECT rowid FROM uploadx_files WHERE uploader_id = '$user'";
    if(!$result = $this->checkQuery($query)) {
      return 0;
    } else {
      return $result->num_rows;
    }
  }

  /**
   * Check to see if there's an active MySQL connection
   *
   * @return bool
   */
  private function checkConn() {
    if (!$this->conn) {
      $this->connect();
      if(!$this->conn) {
        return false;
      } else {
        return true;
      }
    } else {
      return true;
    }
  }

  /**
   * Check if the query worked
   *
   * @param $query
   * @return mixed
   */
  private function checkQuery($query) {
    if (!$result = $this->conn->query($query)) {
      return false;
    } else {
      return $result;
    }
  }

  /**
   * Cleans a variable by escaping it
   * @param $dirty
   * @return mixed
   */
  private function cleanVar($dirty) {
    return $this->conn->real_escape_string($dirty);
  }
}