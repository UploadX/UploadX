<div id='main_div'>
	<h1 class="center_text animated fadeInDown">Uploads</h1>
<section class="section section--menu center-content animated fadeIn" id="nav">
				<nav class="menu menu--shylock">
					<ul class="menu__list">
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/'?>" class="menu__link">Home</a></li>
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/settings/'?>" class="menu__link">Settings</a></li>
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/users/'?>" class="menu__link">Users</a></li>
						<li class="menu__item menu__item--current"><a href="<?php echo $GLOBALS['home'] . 'admin/uploads/'?>" class="menu__link">Uploads</a></li>
						<li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/logout'?>" class="menu__link">Log Out</a></li>
					</ul>
				</nav>
			</section>

    <div id="page">
  
  <h2 class="animated fadeInUp">View uploads from user</h2>
  <select class="animated fadeIn" onChange="window.location.href=this.value">
    <option value="">Select user</option>

    
    <?php
    
$users = $this->userHandler->getUsers();
    
foreach ($users as $user){
  
 echo ("<option value='".$GLOBALS['home']."admin/uploads/". $user->username ."'>". $user->username ."</option>");
  
}

    ?>
    
       
    
</select>
        <br><br>
  
      <h2 class="animated fadeInUp">Uploads table</h2>
      
   <div class="center">
  
  <table class="flatTable animated fadeIn">
  <tr class="headingTr">
    <th>ID</th>
    <th>Uploader</th>
    <th>Filesize</th>
    <th>Uploader IP</th>
    <th>Views</th>
    <th>File MIME type</th>
    <th>Original filename</th>
    <th>Delete</th>
  </tr>
  
  <?php
$uploads = $this->fileHandler->getJsonData();
$upload_count = count($uploads);


if (!empty($_GET['opt'])){
  
  $user = $_GET['opt'];
  
  if($this->userHandler->isUser($user)){
    
    $new_uploads = [];
    
    foreach ($uploads as $key => $data){
      
      if( $data['uploader'] == $user){
        $new_uploads[$key] = $data;
      }
    }
    
    $uploads = $new_uploads;
    
    echo("<h3 class='center_text'>Showing uploads for user \"<u>$user</u>\"</h3>");
    
  }
  
}

foreach ($uploads as $key => $data){
  echo("<tr>");
  
  echo ("<td><a href='". $GLOBALS['home'].$key ."' target='_blank'>$key</td>");
  echo ("<td>". $data['uploader'] ."</td>");
  echo ("<td>". $data['filesize'] ."</td>");
  echo ("<td>". $data['uploader_ip'] ."</td>");
  echo ("<td>". $data['access_count'] ."</td>");
  echo ("<td>". $data['type'] ."</td>");
  echo ("<td>". $data['old_name'] ."</td>");
  echo ("<td>");
  

  ?>
    <form action="./" method="post">
    
      <input type="image" src="<?php echo $GLOBALS['home']?>/res/img/delete.png" >
      <input type="hidden" name="action" value="deletefile">
      <input type="hidden" name="id" value="<?php echo $key?>">
    
    </form>
    <?php
  echo("</td>");
  
  echo("</tr>");
}
  ?>
     </table>
  </div>
  </div>
</div>