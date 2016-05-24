<div id="main_div">

  <h1 class="center_text animated fadeInDown">UploadX Administrator Panel</h1>
  <div id="nav_bar">
    <section class="section section--menu center_content animated fadeIn" id="nav">
      <nav class="menu menu--shylock">
        <ul class="menu__list">
          <li class="menu__item menu__item--current"><a href="<?php echo $GLOBALS['home'] . 'admin/' ?>"
                                                        class="menu__link">Home</a></li>
          <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/settings/' ?>" class="menu__link">Settings</a>
          </li>
          <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/users/' ?>" class="menu__link">Users</a>
          </li>
          <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/uploads/' ?>"
                                    class="menu__link">Uploads</a></li>
          <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/logout' ?>" class="menu__link">Log
              Out</a></li>
        </ul>
      </nav>
    </section>
  </div>
  <h2>Stats</h2>
  <table>

    <tbody>

    <tr>

      <th>Uploads</th>
      <th>Users</th>
      <th>Total size</th>

    </tr>
    <tr>

      <?php
        $db = new mysqlHandler();
        $user_count = $db->countUsers();
        $upload_count = $db->countUploads();
      ?>

      <td><?= $upload_count; ?> files</td>
      <td><?= $user_count; ?> users</td>
      <td><?php
        $bytes= disk_total_space(__UPLOAD__);
        function foldersize($path) {
          $total_size = 0;
          $files = scandir($path);

          foreach($files as $t) {
            if (is_dir(rtrim($path, '/') . '/' . $t)) {
              if ($t<>"." && $t<>"..") {
                $size = foldersize(rtrim($path, '/') . '/' . $t);

                $total_size += $size;
              }
            } else {
              $size = filesize(rtrim($path, '/') . '/' . $t);
              $total_size += $size;
            }
          }
          return $total_size;
        }

        echo $this->fileHandler->fileSizeConvert(foldersize(__UPLOAD__));
//        foreach ($this->fileHandler->getJsonData() as $key => $value) {
//
//          $bytes = $bytes + filesize($value['location']);
//        }
//        echo $this->fileHandler->filesizeConvert($bytes);


        ?></td>

    </tr>

    </tbody>

  </table>

</div>