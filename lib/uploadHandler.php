<?php

/*

@author: Pips

@title: Uploader Handler
@desc: Class that manages uploading from ShareX.

*/

class uploadHandler{
    
    protected $userHandler;
    protected $errorHandler;
    protected $settingsHandler;
    protected $fileHandler;
    
    function __construct(){
        
        $this->errorHandler = new errorHandler();
        $this->userHandler = new userHandler();
        $this->settingsHandler = new settingsHandler();
        $this->fileHandler = new fileHandler();
        
    }
    
  //bulk is done here
    function process(){
        
      if(!isset($_POST['key'])){
        
        $this->errorHandler->throwError('upload:nokey');
        
      }else{
        
        $key = $_POST['key'];
        
        if($this->userHandler->isValidKey($key)){

          $uploader = $this->userHandler->getUserByKey($key);
          //var_dump($uploader);
          if(!$uploader->enabled){

            $this->errorHandler->throwError('upload:banned');

          }else{

             if(in_array(pathinfo($_FILES['file']['tmp_name'] . $_FILES['file']['name'], PATHINFO_EXTENSION), $this->settingsHandler->getSettings()['security']['disallowed_files'])){

                $this->errorHandler->throwError('upload:badextension');

          }else{
               
               $this->fileHandler->saveFile($_FILES['file'], $uploader);
               
             }
          }
          }else{
            $this->errorHandler->throwError('upload:wrongkey');
          }
        
      }     
        
    }
    
    // true/fase function to make sure that somebody is uploading, and with the right 'settings'
    function checkForUpload(){
        
        if(isset($_FILES['file'])){
            return true;
        }else{
            return false;
        }
        
    }

}

?>