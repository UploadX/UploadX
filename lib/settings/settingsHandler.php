<?php

/*

@author: Pips

@title: Settings Handler.
@desc: Class that manages UploadX settings, such as security.

*/

class settingsHandler {
    
    protected $settings;
    
    function __construct() {
        
        $this->settings = json_decode(file_get_contents(__DIR__ . '/../files/settings.json'), true);
        
    }
    
    // return ALL the settings. 
    function getSettings() {
        
        return $this->settings;
        
    }
    
    // should do this
    function changeSetting($level, $setting, $newValue) {
            
            $this->settings[$level][$setting] = $newValue;
			
			$this->save();
        
    }
	
	function save(){
		
		file_put_contents(__DIR__ . '/../files/settings.json', json_encode($this->settings, JSON_PRETTY_PRINT));
            
        $this->settings = json_decode(file_get_contents(__DIR__ . '/../files/settings.json'), true);
		
	}
    
    function changePassword($oldpassword, $newpassword, $confirmpassword) {
        
        if (password_verify($oldpassword, $this->settings['security']['password_hash'])) {
            
            if ($newpassword === $confirmpassword) {
                
                $this->changeSetting('security', 'password_hash',password_hash($newpassword, PASSWORD_DEFAULT));
                $this->changeSetting('security', 'last_changed', date("m-d-y"));
                
                
            } else {
                
                # throw mismatch passwords
                
            }
            
        } else {
            
            //if they try to change the password but don't know it, log them out. 
            //If they knew it, they could log in and actually change it.
            $_SESSION['loggedin'] = false;
            
        }
        
    }
	
	function addExtension($ext){
		array_push($this->settings['security']['disallowed_files'], $ext);
		
		$this->save();
		
		
	}
	
	function deleteExtension($ext){
		
		$index = array_search($ext, $this->settings['security']['disallowed_files']);
		
		unset($this->settings['security']['disallowed_files'][$index]);
		
		$this->save();
		
	}
    
}

?>