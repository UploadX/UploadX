<?php 

class error{
    
    public $code;
    public $message;
    public $level; // error severity
    
    public function __construct($array){
        
        $this->code = $array['error_code'];
        $this->message = $array['error_message'];
        $this->level = $array['error_level'];
        
    }
    
}

?>
