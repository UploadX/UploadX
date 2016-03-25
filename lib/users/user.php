<?php

class User{
    
    public $username;
    public $access_key;
    public $filesize_limit;
    public $uploads;
    public $enabled;
    
    public function __construct($username, $access_key, $filesize_limit, $uploads, $enabled){
          
        $this->username = $username;
        $this->access_key = $access_key;
        $this->filesize_limit = $filesize_limit;
        $this->enabled = $enabled;
        $this->uploads = $uploads;
        
    }
    
}

?>