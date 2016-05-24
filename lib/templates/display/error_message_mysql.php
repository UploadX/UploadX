<div>
  <h3>MySQL Error Occurred</h3>
  <p>
    <strong>Error Message:</strong> A MySQL error occurred in function <?= $error_func; ?><b/><br/>
  </p>
    <?php
      if(isset($_SESSION['loggedin'])){
        echo "<hr/>";
        echo "<h3>Admin Debug Info</h3>";
        echo "<hr/>";
        echo "<strong>Bad Querry:</strong> $error_querry<br/>";
        echo "<strong>MySQL Error:</strong> $error_msg<br/>";
      }
    ?>
 </div>
