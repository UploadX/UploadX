<?php

// worry about viewer later
//include __DIR__.'/templates/viewer.php';


class fileHandler
{

  protected $settingsHandler;
  protected $errorHandler;
  protected $userHandler;

  protected $db;

  private $base_dir;
  function __construct() {

    $this->settingsHandler = new settingsHandler();
    $this->errorHandler = new errorHandler();
    $this->userHandler = new userHandler();

    $this->db = new mysqlHandler();

    $this->base_dir = $this->settingsHandler->getSettings()['uploads']['location'];
  }

// Strip Image depending on MIME Type using the GD PHP extension
function imageStripExif($type,$filepath) { 
    $allowedTypes = array( 
        'image/gif',
        'image/jpeg', 
        'image/png', 
        'image/bmp'
    ); 
    if (!in_array($type, $allowedTypes)) { 
        return false; 
    }
    switch ($type) {
        case 'image/gif' :
            imagegif(imageCreateFromGif($filepath),$filepath);
        break;
        case 'image/jpeg' :
            imagejpeg(imageCreateFromJpeg($filepath),$filepath,100);
        break;
        case 'image/png' :
            imagepng(imageCreateFromPng($filepath),$filepath,0);
        break;
        case 'image/bmp' :
            imagebmp(imageCreateFromBmp($filepath),$filepath,false);
        break;
    }
} 

  function saveFile($file, $uploader) {

    // set all the interesting data
    $username = $uploader['user_id'];
    $file_name = $file['name'];
    $file_temp = $file['tmp_name'];
    if (in_array(mime_content_type($file_temp), $this->settingsHandler->getSettings()['security']['disallowed_mime_types'])) {
      unlink($file_temp);
      http_response_code(403);
    }
    $ext = pathinfo($file_temp . $file_name, PATHINFO_EXTENSION);
    $file_id = $this->generateFileName();
    $new_file_name = $file_id . '.' . $ext;
    $new_file_location = $this->base_dir . $username . '/' . $new_file_name;
    $old_name = $file_name;
    $time = date("Y-m-d h:ia");
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // create the upload directory if it doesn't exist
    if (!file_exists($this->base_dir)) {
      mkdir($this->base_dir);
    }
    if (!file_exists($this->base_dir . $username)) {
      mkdir($this->base_dir . $username);
    }

    // attempt to move the file
    if (move_uploaded_file($file_temp, $new_file_location)) {
      if (isset($_POST['delete']) and ($_POST['delete'] == 'true')) {
        $link_data[$file_id]['delete_after'] = true;
        $delete = true;
      } else {
        $link_data[$file_id]['delete_after'] = false;
        $delete = false;
      }

      $file_type = $this->getMIME($new_file_location);

      // attempt to strip exif data if the correct extensions are loaded
      if ($file_type == 'image/jpeg' || $file_type == 'image/png' || $file_type == 'image/gif' ||  $file_type == 'image/bmp') {
        if (extension_loaded('imagick')) {
	  // Strip with Imagick
          $strip_img = new Imagick($new_file_location);
          $strip_img->stripImage();
          $strip_img->writeImage($new_file_location);
        }
        else if (extension_loaded('gd')) {
	  // Strip with GD
          $this->imageStripExif($file_type,$new_file_location);
        }
        else {
	  // Fail to strip, log failure, continue uploading image
          error_log("GD or Imagick Extensions are not install. Cannot strip EXIF data.", 0);
        }
      }
      $file_size = $this->filesizeConvert(filesize($new_file_location));

      if ($this->db->uploadAdd($new_file_name, $file_id, $old_name, $file_type, $username, $user_ip, $time, $file_size, $delete)) {
        // Respond with JSON upon sucessful upload.
	header("Location: ./$file_id");
        header("Content-Type: application/json");
        echo json_encode(array(
	    'id' => $file_id,
            'datetime' => $time,
            'width' => '',
            'height' => '',
            'size' => $file_size,
            'type' => $file_type,
            'deletehash' => '',
            'link' => $GLOBALS['home'] . $file_id,
        ));
      } else {
        $this->errorHandler->throwError('upload:error');
      }
    } else {
      $this->errorHandler->throwError('upload:error');
    }
  }

  /**
   * Deletes an uploaded file
   *
   * @param $id
   */
  function deleteFile($id) {

    if ($this->isValidId($id)) {
      $id_data = $this->db->uploadGetData($id);

      $user_id = $id_data['uploader_id'];
      $file = $id_data['file_name'];
      $full_path = $this->base_dir . "$user_id/$file";

      if (file_exists($full_path) && is_file($full_path)) {
        if ($this->db->uploadDelete($id)) {
          unlink($full_path);
        }
      }
    }
  }

  function deleteUserDir($username) {
    $userDir = $this->base_dir . "$username/";
    if (is_dir($userDir)) {
      unlink($userDir);
    }
  }

  function showFile() {

    $id = $_GET['id'];
    $id_data = $this->db->uploadGetData($id);
    $filename = $id_data['file_name'];
    $username = $id_data['uploader_id'];
    $base_dir = $this->settingsHandler->getSettings()['uploads']['location'];
    $location = $base_dir . $username . '/' . $filename;

    $raw_size = filesize($location);
//    $size = $id_data['upload_size'];

    $type = $id_data['file_type'];

    $admin_session = $this->settingsHandler->getSettings()['security']['session'];
    if (!isset($_SESSION[$admin_session])) {
      $this->db->updateViews($id);
    }

    header("Content-type: $type");
    header("Content-length: $raw_size");
    header("Connection: Keep-Alive");
    header("Cache-control: public");
    header("Pragma: public");
    header("Expires: Mon, 27 Mar 2038 13:33:37 GMT");
    header('Content-Disposition: inline; filename="' . basename($filename) . '"');

    // read the file out
    readfile($location);

  }

  public function isValidId($id) {
    if ($this->db->uploadCheckID($id)) {
      return true;
    } else {
      return false;
    }
  }

  function getFileData($id) {
    if ($this->db->uploadCheckID($id)) {
      $this->db->uploadGetData($id);
    } else {
      return null;
    }

  }

  function generateFileName()
  {

    $generator_settings = $this->settingsHandler->getSettings()['generator'];

    $file_name_mode = $generator_settings['mode'];
    $number_of_chars = $generator_settings['characters'];

    $upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lower_case = 'abcdefghijklmnopqrstuvwxyz';
    $numeric = '0123456789';

    if ($file_name_mode == 1)
      $set = $upper_case . $lower_case . $numeric;
    else if ($file_name_mode == 2)
      $set = $upper_case;
    else if ($file_name_mode == 3)
      $set = $lower_case;
    else if ($file_name_mode == 4)
      $set = $numeric;
    else {
      $set = $upper_case . $lower_case . $numeric;
    }

    $id = substr(str_shuffle($set), 0, $number_of_chars);

    if ($this->isValidId($id)) {

      return $this->generateFileName();

    } else {

      return $id;

    }


  }

  function getMIMELegacy($filename) {

    $mime_types = array(

      'txt' => 'text/plain',
      'htm' => 'text/plain',
      'html' => 'text/plain',
      'php' => 'text/plain',
      'css' => 'text/plain',
      'js' => 'text/plain',
      'json' => 'text/plain',
      'xml' => 'text/plain',
      'swf' => 'application/x-shockwave-flash',
      'flv' => 'video/x-flv',

      // images
      'png' => 'image/png',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg' => 'image/jpeg',
      'gif' => 'image/gif',
      'bmp' => 'image/bmp',
      'ico' => 'image/vnd.microsoft.icon',
      'tiff' => 'image/tiff',
      'tif' => 'image/tiff',
      'svg' => 'image/svg+xml',
      'svgz' => 'image/svg+xml',

      // archives
      'zip' => 'application/zip',
      'rar' => 'application/x-rar-compressed',
      'exe' => 'application/x-msdownload',
      'msi' => 'application/x-msdownload',
      'cab' => 'application/vnd.ms-cab-compressed',

      // audio/video
      //'mp3' => 'video/mpeg',
      'qt' => 'video/quicktime',
      'mov' => 'video/quicktime',

      // adobe
      'pdf' => 'application/pdf',
      'psd' => 'image/vnd.adobe.photoshop',
      'ai' => 'application/postscript',
      'eps' => 'application/postscript',
      'ps' => 'application/postscript',

      // ms office
      'doc' => 'application/msword',
      'rtf' => 'application/rtf',
      'xls' => 'application/vnd.ms-excel',
      'ppt' => 'application/vnd.ms-powerpoint',

      // open office
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $ext = strtolower(array_pop(explode('.', $filename)));

    if (array_key_exists($ext, $mime_types)) {
      return $mime_types[$ext];
    } else if (function_exists('finfo_open')) {
      $finfo = finfo_open(FILEINFO_MIME);
      $mimetype = finfo_file($finfo, $filename);
      finfo_close($finfo);
      return $mimetype;
    } else {
      return 'application/octet-stream';
    }

  }

  /**
   * Get the MIME Type (returns text/plain for certain files or the correct mime type
   *
   * @param $filename
   * @return string
   */
  function getMIME($filename) {
    $info = pathinfo($filename);
    $ext = $info['extension'];

    switch ($ext) {
      case "txt":
      case 'text':
      case 'htm':
      case 'html':
      case 'php':
      case 'css':
      case 'js':
      case 'json':
      case 'xml':
        $mime_type = 'text/plain';
        return $mime_type;
        break;
      default:
        return mime_content_type($filename);
    }
  }

  function filesizeConvert($bytes)
  {
    $bytes = floatval($bytes);
    $arBytes = array(
      0 => array(
        "UNIT" => "TB",
        "VALUE" => pow(1024, 4)
      ),
      1 => array(
        "UNIT" => "GB",
        "VALUE" => pow(1024, 3)
      ),
      2 => array(
        "UNIT" => "MB",
        "VALUE" => pow(1024, 2)
      ),
      3 => array(
        "UNIT" => "KB",
        "VALUE" => 1024
      ),
      4 => array(
        "UNIT" => "B",
        "VALUE" => 1
      ),
    );
    $result = null;
    foreach ($arBytes as $arItem) {

      if ($bytes >= $arItem["VALUE"]) {
        $result = $bytes / $arItem["VALUE"];
        $result = str_replace(".", ".", strval(round($result, 2))) . " " . $arItem["UNIT"];
        break;
      }
    }
    return $result;
  }

//  private function save() {
//
////    file_put_contents(__DIR__ . '/files/files.json', json_encode($this->files, JSON_PRETTY_PRINT));
////    $this->files = json_decode(file_get_contents(__DIR__ . '/files/files.json'), true);
//
//  }

//  function getJsonData() {
//
//    return $this->files;
//
//  }

}
