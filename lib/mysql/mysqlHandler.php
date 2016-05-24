<?php

class mysqlHandler
{

  protected $errorHandler;

  protected $files;

  protected $configArray;

  private $sqlHost;
  private $sqlUser;
  private $sqlPass;
  private $sqlDB;

  private $root_dir;

  function __construct(){

    $this->errorHandler = new errorHandler();

    $this->sqlHost = dbConfig['host'];
    $this->sqlUser = dbConfig['user'];
    $this->sqlPass = dbConfig['pass'];
    $this->sqlDB = dbConfig['database'];

    $this->root_dir = ROOTPATH;
  }
  private function connect(){
    $db = new mysqli($this->sqlHost, $this->sqlUser, $this->sqlPass, $this->sqlDB);
    if($db->connect_errno > 0){
      /* Temp and Dirty Error Handling */
      echo 'Unable to connect to database [' . $db->connect_error . ']';
      $db->close();
      exit();
    }else{
      return $db;
    }

  }
  public function checkUploadID($id){
    $conn = $this->connect();
    $id = $conn->real_escape_string($id);
    $query = "SELECT file_id FROM uploadx_files WHERE `file_id`='$id'";
    $result = $conn->query($query);
    if (!$result) {
      die($conn->error);
    }
    if (!$result->num_rows == 1) {
      $conn->close();
      return false;
    } else {
      $conn->close();
      return true;
    }
  }
  /**
   * Get the current number of users
   *
   * @return int|string
   */
  public function countUsers() {
    $conn = $this->connect();
    $query = "SELECT user_id FROM uploadx_users";
    if (!$result = $conn->query($query)) {
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
    $conn = $this->connect();
    $query = "SELECT file_id FROM uploadx_files";
    if (!$result = $conn->query($query)) {
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
   * @param $conn
   * @param $last_query
   */
  private function errorFatal($caller, $conn, $last_query){
    $this->errorHandler->throwMySQLError($caller, $conn->error, $last_query, 'FATAL');
    exit();
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
   * Check if $key is valid
   *
   * @param $key
   * @return bool
   */
  public function keyCheck($key) {
    $conn = $this->connect();
    $key = $conn->real_escape_string($key);
    $query = "SELECT access_key FROM uploadx_users WHERE access_key = '$key'";
    if (!$result = $conn->query($query)) {
      $this->errorFatal("myslHandler::keyCheck", $conn, $query);
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
    $conn = $this->connect();
    $key = $conn->real_escape_string($key);
    $query = "SELECT user_id FROM uploadx_users WHERE access_key = '$key'";
    if (!$result = $conn->query($query)) {
      die("No MySQL query issue!");
    }
    if ($result->num_rows == 1) {
      $data = $result->fetch_assoc();
      return $data['user_id'];
    }
    die("Couldn't find user!");
  }
  /**
   * Add a new line to the log table
   *
   * @param $log_level
   * @param $log_type
   * @param $log_message
   */
  public function logAdd($log_level, $log_type, $log_message){
    $conn = $this->connect();
    $log_level = $conn->real_escape_string($log_level);
    $log_type = $conn->real_escape_string($log_type);
    $log_message = $conn->real_escape_string($log_message);
    $log_time = date("Y-m-d h:ia");

    $query = "INSERT INTO uploadx_logs (`log_level`, `log_type`, `log_message`) VALUES ('$log_level', '$log_type', '$log_message', '$log_time')";
    if (!$result = $conn->query($query)){
      $this->errorFatal("mysqlHandler::logAdd", $conn, $query);
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
  public function uploadList($start_row) {
    $conn = $this->connect();
    $start_row = $conn->real_escape_string($start_row);
    $query = "SELECT * FROM uploadx_files ORDER BY rowid DESC LIMIT $start_row,100";

    if (!$result = $conn->query($query)) {
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
  public function uploadListUser($start_row, $username) {
    $conn = $this->connect();
    $start_row = $conn->real_escape_string($start_row);
    $username = $conn->real_escape_string($username);
    $query = "SELECT * FROM uploadx_files WHERE uploader_id = '$username' ORDER BY rowid DESC LIMIT $start_row,100";

    if (!$result = $conn->query($query)) {
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
   * Create a new user
   *
   * @param $user_id
   * @param $access_key
   * @param $filesize_limit
   */
  public function userCreate($user_id, $access_key, $filesize_limit){
    $conn = $this->connect();
    $id = $conn->real_escape_string($user_id);
    $key = $conn->real_escape_string($access_key);
    $limit = $conn->real_escape_string($filesize_limit);

    $query = "SELECT user_id FROM uploadx_users WHERE user_id = '$id'";
    if(!$result = $conn->query($query)){
      $this->errorFatal('mysqlHandler::userMigrate', $conn, $query);
    }

    if($result->num_rows == 0){//No user (yet!)
    }

  }
  /**
   * Check if the user is valid
   *
   * @param $user_id
   * @return bool
   */
  public function userCheck($user_id) {
    $conn = $this->connect();
    $user_id = $conn->real_escape_string($user_id);
    $query = "SELECT user_id FROM uploadx_users WHERE user_id = '$user_id'";
    if (!$result = $conn->query($query)) {
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
    $conn = $this->connect();
    $user_id = $conn->real_escape_string($user_id);
    $query = "SELECT * FROM uploadx_users WHERE user_id = '$user_id'";
    if (!$result = $conn->query($query)) {
     return "Error";
    }
    if ($result->num_rows == 1) {
      $data = $result->fetch_assoc();
      return $data;
    }
    return "Error";
  }
  /**
   *
   * Get users starting from $start_row
   *
   * @param $start_row
   * @return array|string
   */
  public function usersGet($start_row) {
    $conn = $this->connect();
    $start_row = $conn->real_escape_string($start_row);
    $query = "SELECT * FROM uploadx_users ORDER BY rowid DESC LIMIT $start_row,100";
    if (!$result = $conn->query($query)) {
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
    $conn = $this->connect();
    $username = $conn->real_escape_string($username);
    $query = "SELECT rowid FROM uploadx_users WHERE user_id = '$username'";
    if (!$result = $conn->query($query)) {

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
    $conn = $this->connect();
    $id = $conn->real_escape_string($user_id);
    $key = $conn->real_escape_string($access_key);
    $limit = $conn->real_escape_string($filesize_limit);
    $enabled = $conn->real_escape_string($enabled);

    $query = "SELECT user_id FROM uploadx_users WHERE user_id = '$id'";
    if(!$result = $conn->query($query)){
      $this->errorFatal('mysqlHandler::userMigrate', $conn, $query);
    }

    if($result->num_rows == 0){ //No user (yet!)
      /* Let's find out how many uploads might be tied to this user on uploadx_files */
      $upload_query = "SELECT uploader_id FROM uploadx_files";
      if(!$upload_result = $conn->query($upload_query)) {
        $this->errorFatal('mysqlHandler::userMigrate', $conn, $upload_query);
      }
      $upload_count = $upload_result->num_rows;

      $create_query = "INSERT INTO uploadx_users (`user_id`, `access_key`, `filesize_limit`, `uploads`, `enabled`) VALUES ('$id', '$key', '$limit', '$upload_count', '$enabled')";
      if (!$create_result = $conn->query($create_query)){
        $this->errorFatal('mysqlHandler::userMigrate', $conn, $create_query);
      }else{
        return true;
      }
    }else {
      return false;
    }
  }
  /**
   * The amount of uploads for the user
   *
   * @param $user
   * @return int
   */
  public function userUploadCount($user) {
    $conn = $this->connect();
    $query = "SELECT rowid FROM uploadx_files WHERE uploader_id = '$user'";
    if(!$result = $conn->query($query)) {
      return 0;
    } else {
      return $result->num_rows;
    }
  }
  /**
   * Execute a (UNSAFE!) MySQL Query
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