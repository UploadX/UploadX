<?php 

$show_uploader = $this->settingsHandler->getSettings()['viewer']['show_uploader'];
$show_views = $this->settingsHandler->getSettings()['viewer']['show_views'];
$show_ip = $this->settingsHandler->getSettings()['viewer']['show_ip']; // this should probably always be false. when enabled, it will show the uploaders IP to ANYONE.

$loggedin = $_SESSION['loggedin'];
?>

<ul>

    <li><a class="icon icon-stack" href="<?php echo $src; ?>" download>Download</a></li>
    <li><a class="icon icon-data" href="<?php echo $src; ?>">Raw file</a></li>
    <?php if ($loggedin){ ?>
<!--
    <li>
        <form action="./admin/" method="post" id='delete_form'>
            <a class="icon icon-trash" onclick="document.getElementById('delete_form').submit();" href="">delete</a>
            
            <input type="hidden" name="action" value="deletefile">
            <input type="hidden" name="id" value="<?php //echo $id; ?>">
        </form>
    </li>
-->
    <?php }?>
    
    <li><a class="icon icon-params" href="<?php echo $GLOBALS['home'] . 'admin/';?>" target="_blank">admin panel</a></li>
    <li><a></a></li>
    <?php if($loggedin or $show_uploader){ ?>
    <li><a class="icon icon-user"><?php echo $uploader; ?></a></li>
    <?php }?>
    <?php if($loggedin or $show_views) { ?>
    <li><a class="icon icon-eye"><?php echo $views; ?> views</a></li>
    <?php } ?>
    <li><a class="icon icon-calendar"><?php echo $upload_time; ?></a></li>
    <li><a class="icon icon-tag"><?php echo $file_size; ?></a></li>
    <?php if($loggedin or $show_ip){ ?>
    <li><a class="icon icon-world"><?php echo $uploader_ip ?></a></li>
    <?php } ?>
    <li><a class="icon icon-clip"><?php echo $type; ?></a></li>
    <li><a><img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=<?php echo $GLOBALS['home']. $id;?>&choe=UTF-8w&chld=L|2"></a></li>
    
</ul>