<?php

class sanityChecker{
	
	protected $errorHandler;
    protected $settingsHandler;
	protected $fileList;
	
	public function __construct(){
		
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
    
    public function checkSettings(){
        
        if($this->settingsHandler->getSettings()['security']['password_hash'] === ""){
            
            $this->settingsHandler->changeSetting('security', 'password_hash', password_hash('password', PASSWORD_DEFAULT));
            echo "password changed to 'password'<br>";
            
        } 
        
        if(!isset($this->settingsHandler->getSettings()['viewer']['theme'])){
      
        $this->settingsHandler->changeSetting('viewer', 'theme', 'red.css');
        echo "Theme was unset, set to default 'red'. <br>";
      
      }
        
        
    }
	
	public function checkFiles(){
		// false = quiet
		// true = verbose
		
		$okay = true;
		$missing = [];
		
		foreach ($this->fileList as $dir => $files){
			
			foreach ($files as $file){
				
				$location = $GLOBALS['dir']. '/lib' . $dir . $file;
				
				if (!file_exists($location)){
					
					$okay = false;
					array_push($missing, $location);
                    
				}					
			}
		}		
	}
}


?>