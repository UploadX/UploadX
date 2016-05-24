<li><hr/></li>
<li><a class="icon icon-params" href="<?= $admin_url; ?>">Go to Admin Panel</a></li>
<li>
<?php
  if ($this->settingsHandler->getSettings()['uploads']['viewer_delete'] == true) {
?>
<form action="<?= $admin_url; ?>" method="post" id='delete_form'>
  <a class="icon icon-trash" onclick="document.getElementById('delete_form').submit();" href="">Delete File</a>
  <input type="hidden" name="action" value="deletefile">
  <input type="hidden" name="id" value="<?= $id; ?>">
</form>
<?php
  }
?>
 <li><hr/></li>
