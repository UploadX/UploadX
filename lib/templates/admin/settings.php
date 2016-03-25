<div id="main_div">
	<h1 class="center_text animated fadeInDown">Settings</h1>
<section class="section section--menu center-content animated fadeIn" id="nav">
				<nav class="menu menu--shylock">
					<ul class="menu__list">
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/'?>" class="menu__link">Home</a></li>
						<li class="menu__item menu__item--current"><a href="<?php echo $GLOBALS['home'] . 'admin/settings/'?>" class="menu__link">Settings</a></li>
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/users/'?>" class="menu__link">Users</a></li>
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/uploads/'?>" class="menu__link">Uploads</a></li>
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/logout'?>" class="menu__link">Log Out</a></li>
					</ul>
				</nav>
			</section>

  <div id="settings_panel">
    
    
    <div id='settings_main'>
      <form method="post" action="./">
      <h2 class="animated fadeInUp" >Viewer</h2>

		  <h3>General</h3>
        
        Show uploader: <input type="checkbox" name="show_uploader"  <?php if ($this->settingsHandler->getSettings()['viewer']['show_uploader']) echo('Checked');?>>
        <br>
        Show views: <input type="checkbox" name="show_views"  <?php if ($this->settingsHandler->getSettings()['viewer']['show_views']) echo('Checked');?>>
        <br>
        Show IP: <input type="checkbox" name="show_ip"  <?php if ($this->settingsHandler->getSettings()['viewer']['show_ip']) echo('Checked');?>>
          <br>
          <h3>Theme</h3>
          
              <?php  
              
              $files = scandir($GLOBALS['dir'] . "/res/css/themes/");
              
              unset($files[0]);
              unset($files[1]);
              
              foreach($files as $file) {
                  
                  $checked = '';
                  
                  if ($file ==  $this->settingsHandler->getSettings()['viewer']['theme'])
                      $checked = 'checked';
                  
                  echo "<input type='radio' name='theme' value='$file' $checked> $file </option><br>";
                  
              }
              
              ?>
          
		  
		  
		  
		  <br><br>
		  <h2 class="animated fadeInUp">Config</h2>
        <h3>Files</h3>
        Save location: <input name='save_location' size="10" type="text" value="<?php echo($this->settingsHandler->getSettings()['security']['storage_folder']); ?>">
		  
		
		  
        <br>
		  
		  
		 <br> 
        <h3>Uploads</h3>
        ID generator legnth: <input name='generator_legnth' size="4" type="number"  maxlength="10" value="<?php echo($this->settingsHandler->getSettings()['generator']['characters']); ?>">
        <br>
		  
		  
		  <br>
        <input type="submit" value="Save changes">
        <input type="hidden" name="action" value="changesettings">
        <br>
		  <br>
      </form>
		
<form action="./" method="post">
	<input type="submit" value="Fix files from directory move">
	<input type="hidden" name="action" value="fixfiles">
	
	</form>
		<br><br>
		  <h2 class="animated fadeInUp">Banned file types</h2> 
		  <table class="animated fadeIn" style="width:10%">
		<tr>
		<th>Extension</th>
		<th>Action</th>
		</tr>
		
		  <?php 
        foreach($this->settingsHandler->getSettings()['security']['disallowed_files'] as $value){
			?>
			
          <tr>
			  <td><?php echo $value; ?></td>
			  
			  <td>
			  	<form action="./" method="post">
					
					<input type="image" src="<?php echo $GLOBALS['home']?>/res/img/delete.png">
					<input type="hidden" name="action" value="deleteextension">
					<input type="hidden" name="extension" value="<?php echo $value; ?>">
					
				</form>
			  
			  </td>
		  </tr>
			  
			<?php
			
        }
        ?>
			  <tr>
			  <form method="post" action="./">
				  <td>		  
				  <input type="text" placeholder="extension" name="extension">
				  </td>
				  <td>
				  <input type="image" src="<?php echo $GLOBALS['home']?>/res/img/add.png">
				  </td>
			  
				  <input type="hidden" name="action" value="addextension">
			  </form>
			  </tr>
			  
			  </table>
        
      
    </div>
    
    <br><br>
  
    <div id="settings_changepassword">
      
      <h2 class="animated fadeInUp">Change password</h2>
      
      <form action="./" method="post" class="animated fadeIn">

      <input type="password" placeholder="Old password" name="old_password" required>
      <br>
      <br>
      <input type="password" placeholder="New password" name ="new_password" required>
      <br>
      <input type="password" placeholder="Confirm password" name ="confirm_password" required>
              <br>
      Last changed: <?php echo($this->settingsHandler->getSettings()['security']['last_changed']); ?>
      <br>
        <br>
      <input type="submit" value="Change password">
      <input type="hidden" name="action" value="changepassword">

        
      </form>
      
    </div>
    
  </div>
  
</div>

