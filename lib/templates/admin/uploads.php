<div id='main_div'>
  <h1 class="center_text animated fadeInDown">Uploads</h1>
  <section class="section section--menu center-content animated fadeIn" id="nav">
    <nav class="menu menu--shylock">
      <ul class="menu__list">
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/' ?>" class="menu__link">Home</a></li>
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/settings/' ?>"
                                  class="menu__link">Settings</a></li>
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/users/' ?>" class="menu__link">Users</a>
        </li>
        <li class="menu__item menu__item--current"><a href="<?php echo $GLOBALS['home'] . 'admin/uploads/' ?>"
                                                      class="menu__link">Uploads</a></li>
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/logout' ?>" class="menu__link">Log Out</a>
        </li>
      </ul>
    </nav>
  </section>

  <div id="page">

    <h2 class="animated fadeInUp">View uploads from user</h2>

    <select class="animated fadeIn" onChange="window.location.href=this.value">
      <option value="">Select user</option>
      <?php
      $users = $this->db->usersGet(0);
      foreach ($users as $user) {
        #$data = $this->db->userData($user);
        echo("<option value='" . $GLOBALS['home'] . "admin/uploads/" . $user['username'] . "'>" . $user['username'] . "</option>");
      }
      ?>
    </select>
    <br><br>

    <h2 class="animated fadeInUp">Uploads table</h2>

    <div class="center">

        <?php
        $uploads = $this->db->uploadList(0);
        //$upload_count = $this->db->userUploadCount($user);

        if (!empty($_GET['opt'])) {

          $user = $_GET['opt'];

          if ($this->userHandler->isUser(0, $user)) {

            $new_uploads = [];

//            foreach ($uploads as $key => $data) {
//
//              if ($data['uploader'] == $user) {
//                $new_uploads[$key] = $data;
//              }
//            }
//
//            $uploads = $new_uploads;
//
            echo("<h3 class='center_text'>Showing uploads for user \"<i>$user</i>\"</h3>");
            $uploads = $this->db->uploadListUser($user);

          }
        }
        else {
          $uploads = $this->db->uploadList(0);
        }
        if (!is_array($uploads)) {
          if ($uploads == 'Error') {
            echo "<h3>An Error Occured</h3>";
          } else if ($uploads == 'No Results Found') {
            echo "<h3>No Results Found</h3>";
          }
        } else {
        ?>
        <?php
          ?>
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
          foreach ($uploads as $data) {
            $link = $this->base_url . $data['file_id'];
            ?>
          <tr>
            <td><a href="<?= $link; ?>"><?= $data['file_id']; ?></a></td>
            <td><?= $data['uploader_id']; ?></td>
            <td><?= $data['upload_size']; ?></td>
            <td><?= $data['uploader_ip']; ?></td>
            <td><?= $data['access_count']; ?></td>
            <td><?= $data['file_type']; ?></td>
            <td><?= $data['file_original']; ?></td>
            <td>
              <form action="./" method="post">
                <input type="image" src="<?php echo $GLOBALS['home'] ?>/res/img/delete.png">
                <input type="hidden" name="action" value="deletefile">
                <input type="hidden" name="id" value="<?php echo $data['file_id']; ?>">
              </form>
            </td>
          </tr>
            <?php
          }
        }
      ?>
      </table>
    </div>
  </div>
</div>
