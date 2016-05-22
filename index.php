<?php


// debug. Will break the viewer.
$debug = false;

// include everything in the main file so the sub-classes can access it.
include_once __DIR__.'/lib/errors/errorHandler.php';
include_once __DIR__.'/lib/errors/applicationError.php';
include_once __DIR__.'/lib/errors/sanityChecker.php';

include_once __DIR__.'/lib/users/userHandler.php';
include_once __DIR__.'/lib/users/user.php';

include_once __DIR__.'/lib/settings/settingsHandler.php';

include_once __DIR__.'/lib/uploadHandler.php';

include_once __DIR__.'/lib/fileHandler.php';

include_once __DIR__.'/lib/web/webCore.php';


// logic to detect http/https connection
$connection = 'http';
if(!empty($_SERVER['HTTPS']))
  $connection = 'https';

// global vairiable to tell the script exactly what the home url is, including http/https, sub folders, etc.
$GLOBALS['home'] =  $connection . '://' . str_replace("index.php", "", $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']);
$GLOBALS['dir'] = __DIR__ ;


// create the handlers
$errorHandler = new errorHandler();
$userHandler = new userHandler();
$settingsHandler = new settingsHandler();
$uploadHandler = new uploadHandler();
$fileHandler = new fileHandler();
$webCore = new webCore();
$sanityChecker = new SanityChecker();

$sanityChecker->checkFiles();
$sanityChecker->checkSettings();

// session manager
session_start();
if(!isset ($_SESSION['loggedin'])){
    $_SESSION['loggedin'] = false;
}

if ($debug){
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  
echo '$_SESSION<br>';
var_dump($_SESSION);
  echo '$_POST<br>';
var_dump($_POST);
    echo '$_GET<br>';
var_dump($_GET);
    echo '$_FILES<br>';
var_dump($_FILES);
  
}

/* Testing Area */
if($debug){


	
}
/* ============ */



// somebody is uploading, so we send it to the upload handler
if (!empty($_FILES)){
    
    if($uploadHandler->checkForUpload()){
        $uploadHandler->process();
        
    }else{
      $errorHandler->throwError("upload:wrongfile");
    }
    
}
// they're accessing a file, so we go to the file Handler.

else if (!empty($_GET)){

  // viewing file
  if (!empty($_GET['id'])){
    
    // valid file
    if ($fileHandler->isValidId($_GET['id'])){
      if(empty($_GET['action'])){
        $webCore->buildPreview();
      }else{
        $fileHandler->showFile();
      }
    }else{
       $errorHandler->throwError("action:nofile");
    }
    
  }else if(isset($_GET['page'])){
    
    if (!empty($_POST)){
      $webCore->process();
    }
    
    
    else if(empty($_GET['page']) ){
      
      $webCore->buildPage('home', '');
      
    }else{
      
      $webCore->buildPage($_GET['page'], $_GET['opt']);
      
    }
    
  }else{
    echo "404";
  }  
}

else {
$theme       = $settingsHandler->getSettings()['viewer']['theme'];
include_once "./lib/templates/admin/default_header.php";
include_once "./lib/templates/admin/homepage.php";
  
}


?>
