<?php
ini_set('display_errors', 0);
$show_uploader = $this->settingsHandler->getSettings()['viewer']['show_uploader'];
$show_views = $this->settingsHandler->getSettings()['viewer']['show_views'];
$show_ip = $this->settingsHandler->getSettings()['viewer']['show_ip']; // this should probably always be false. when enabled, it will show the uploaders IP to ANYONE.
$admin_session = $this->settingsHandler->getSettings()['security']['session'];
if (isset($_SESSION[$admin_session])) {
  $is_admin = true;
} else {
  $is_admin = false;
}
?>

<ul>

  <li><a class="icon icon-stack" href="<?= $src; ?>" download>Download</a></li>
  <li><a class="icon icon-data" href="<?= $src; ?>">Raw file</a></li>
  <?php
    if ($is_admin) {
      $admin_url = $this->base_url . 'admin/';
      require_once('info_admin.php');
    }
  ?>
  <?php if ($is_admin or $show_uploader) { ?>
  <li><a class="icon icon-user"><?= $uploader; ?></a></li>
  <?php
  }
  if ($is_admin or $show_views) {
    ?>
  <li><a class="icon icon-eye"><?= $views; ?> views</a></li>
  <?php
  }
  ?>
  <li><a class="icon icon-calendar"><?= $upload_time; ?></a></li>
  <li><a class="icon icon-tag"><?= $file_size; ?></a></li>
  <?php if ($is_admin or $show_ip) { ?>
    <li><a class="icon icon-world"><?= $uploader_ip; ?></a></li>
  <?php } ?>
  <li><a class="icon icon-clip"><?= $type; ?></a></li>
  <li><a><img
        src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=<?php echo $GLOBALS['home'] . $id; ?>&choe=UTF-8w&chld=L|2"></a>
  </li>

</ul>