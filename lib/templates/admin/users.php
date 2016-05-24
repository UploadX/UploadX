<div id="main_div">
  <h1 class="center_text animated fadeInDown">Users</h1>
  <section class="section section--menu center-content animated fadeIn" id="nav">
    <nav class="menu menu--shylock">
      <ul class="menu__list">
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/' ?>" class="menu__link">Home</a></li>
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/settings/' ?>"
                                  class="menu__link">Settings</a></li>
        <li class="menu__item menu__item--current"><a href="<?php echo $GLOBALS['home'] . 'admin/users/' ?>"
                                                      class="menu__link">Users</a></li>
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/uploads/' ?>"
                                  class="menu__link">Uploads</a></li>
        <li class="menu__item"><a href="<?php echo $GLOBALS['home'] . 'admin/logout' ?>" class="menu__link">Log Out</a>
        </li>
      </ul>
    </nav>
  </section> <!-- menu__item--current -->

  <h2 class="animated fadeInUp">Create a user</h2>
  <form action="./" method="post" class="animated fadeIn">


    <input name="username" type="text" placeholder="new username" required class="css-input">
    &nbsp;
    <input type="image" src="<?php echo $GLOBALS['home'] ?>/res/img/add.png">
    <input type="hidden" name="action" value="createuser">

  </form>

  <h2 class="animated fadeInUp">View and edit users</h2>

  <table class="animated fadeIn" style="width:100%;">
    <tr>
      <th>Username</th>
      <th>Access key</th>
      <th>Uploads</th>
      <th>Enabled</th>
      <th>Delete</th>
      <th>Custom uploader JSON</th>
    </tr>
    <?php
    $db = new mysqlHandler();
    $users = $db->usersGet(0);
    foreach ($users as $user) {
      $uploads = $db->userUploadCount($user['username']);
      ?>
      <tr>
      </tr>
      <tr>

        <td><?php echo "<a href='" . $GLOBALS['home'] . 'admin/uploads/' . $user['username'] . "' alt='View Uploads from user'>" . $user['username'] . '</a>'; ?></td>
        <td>
          <form action="./" method="post">
            <input type="text" name="key" value="<?= $user['access_key']; ?>">
            <input type="hidden" name="action" value="changekey">
            <input type="hidden" name="username" value="<?= $user['username']; ?>">
          </form>

          <form action="./" method="post">
            <input type="image" src="<?php echo $GLOBALS['home'] ?>/res/img/refresh.png">
            <input type="hidden" name="action" value="newkey">
            <input type="hidden" name="username" value="<?= $user['username']; ?>">
          </form>

        </td>
        <td><?php echo $uploads; ?></td>
        <td>
          <form action="./" method="post">

            <input onChange="this.form.submit()" type="checkbox"
                   name="enabled" <?php if ($user['enabled']) echo 'checked' ?>> Enable account
            <input type="hidden" name="action" value="enable">
            <input type="hidden" name="username" value="<?= $user['username']; ?>">


          </form>
        </td>
        <td>
          <form action="./" method="post">

            <input type="submit" value="Delete">
            <input type="hidden" name="action" value="deleteuser">
            <input type="hidden" name="username" value="<?= $user['username'] ?>">

            &nbsp;&nbsp;&nbsp;
          </form>
          <!--
          <form action="./" method="post">
            <input type="submit" value="Delete Uploads">
            <input type="hidden" name="action" value="deleteuploads">
            <input type="hidden" name="username" value="<?= $user['username']; ?>">
          </form>
          -->

        </td>
        <td>
          <form action="./" method="post">

            <input type="image" src="<?php echo $GLOBALS['home'] ?>/res/img/code.png">
            <input type="hidden" name="action" value="generatejson">
            <input type="hidden" name="username" value="<?= $user['username']; ?>">
          </form>
        </td>
      </tr>
      <?php
    }
    ?>
  </table>

</div>