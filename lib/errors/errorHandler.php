<?php

class errorHandler{
    
    protected $error;
    
    public function __construct(){

    }

    // we get the errors dynamically instead once per load
    public function getError($error_code){
      $error_codes = json_decode(file_get_contents(__DIR__.'/../files/errors.json'), true); // get the errors

      if(isset($error_codes[$error_code])){

        $error = new applicationError($error_codes[$error_code]);
        return $error;

      }else{
        // error is invalid
        return false;
      }

    }
    
    public function throwError($error_code){
      
      if($error = $this->getError($error_code)){
  $message = "";
      include(__DIR__.'/../templates/display/error_message.php');
      }else{
        // error is invalid, so we throw an error that says the error had an error. Yeah, I know.
        $this->throwError('error:error');
      }
    }
    public function throwMySQLError($error_func, $errorMsg, $errorQuery, $errorLevel){
      include(__DIR__.'/../templates/display/error_message_mysql.php');
    }
	
}

?>