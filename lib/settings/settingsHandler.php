<?php

/*

@author: Pips

@title: Settings Handler.
@desc: Class that manages UploadX settings, such as security.

*/

class settingsHandler {

  protected $settings;
  protected $rootDir;

  private $dbgGlobal = false;
  private $dbgClass = false;

  function __construct() {
    $this->settings = config;
    $this->rootDir = ROOTPATH;
    $this->dbgGlobal = devel['all'];
    $this->dbgClass = devel['settings'];

  }

  // return ALL the settings.
  function getSettings() {
      return $this->settings;
  }

  // should do this
  function changeSetting($level, $setting, $newValue) {
    $this->settings[$level][$setting] = $newValue;
    $this->configSave();
  }
	
	function configSave() {
    $configFile = $this->rootDir.'/lib/config.php';
    $backup = $configFile.'.save';
    if (file_exists($backup)) {
      unlink($backup);
    }
    copy($configFile, $backup);

    $compiledConfig = $this->configCompile();
    $output = '<?php';
    file_put_contents($configFile, $output ."\r\n");
    file_put_contents($configFile, $compiledConfig, FILE_APPEND);

	}

  /**
   * Check if debug is enabled for this class
   * @return bool
   */
  private function debugCheck() {
    return ($this->dbgGlobal || $this->dbgClass);
  }
  /**
   * Compile lb/config.php from current settings
   * 
   * @return string
   */
  private function configCompile() {
    $outConfig = $this->configFormatItem('$config = array(', 2);

    $outConfig .= $this->configFormatCommentSingle("MySQL Configuration - End User NEEDS to configure this", 3);
    $outConfig .= $this->configBuildSection('mysql');

//    $outConfig .= $this->configFormatCommentSingle("Upload Related Settings", 3);
    $outConfig .= $this->configBuildSection('uploads');

//    $outConfig .= $this->configFormatCommentSingle("Configure the file and upload limits", 3);
    $outConfig .= $this->configBuildSection('limits');

    $comment_array = array('Security-related (MOSTLY Auto Generated!)', 'You can modify the session value, but do not modify any others!');
    $outConfig .= $this->configFormatCommentMulti($comment_array, 3);
    $outConfig .= $this->configBuildSection('security');

    $outConfig .= $this->configFormatCommentSingle('Viewer Config. Do NOT Touch', 3);
    $outConfig .= $this->configBuildSection('viewer');

    $outConfig .= $this->configFormatCommentSingle('Generator Settings - Do NOT Touch', 3);
    $outConfig .= $this->configBuildSection('generator');

    $outConfig .= $this->configFormatCommentSingle('This following section is intended for developers only!', 3);
    $outConfig .= $this->configBuildSection('developer');

    $outConfig .= $this->configFormatItem(');', 1);
    if ($this->debugCheck()) {
      $pretty = preg_replace('/\\r\\n/', '<br/>', $outConfig);
      $pretty = preg_replace('/\\t/', '&nbsp;&nbsp;&nbsp;&nbsp;', $pretty);
      echo $pretty;
    }
    return $outConfig;
  }
  /**
   * Builds the given config section
   *
   * @param $config_section
   * @return string
   */
  private function configBuildSection($config_section) {

    $output = $this->configFormatItem("'$config_section' => array(", 3);
    foreach ($this->getSettings()[$config_section] as $config_item => $config_value) {

      if ($config_section == 'viewer' || $config_section == 'developer' || $config_section == 'uploads') {
        if (is_bool($config_value)) {
          if ($config_value == true) {
            $item = "'$config_item' => true,";
          } else {
            $item = "'$config_item' => false,";
          }
        } else {
          $item = "'$config_item' => '$config_value',";
        }

      } else if (($config_section == 'limits') && (is_int($config_item))) {
        $item = "'$config_item' => $config_value,";

      } else if (($config_section == 'security') && (($config_item == 'disallowed_files') || ($config_item == 'disallowed_mime_types'))) {
        $current = $this->getSettings()['security'][$config_item];
        $disallowed = '';

        foreach ($current as $disallowed_item) {
          if (sizeof($disallowed) == 0) {
            $disallowed = "'$disallowed_item'";
          } else {
            $disallowed = "'$disallowed_item', $disallowed";
          }
        }
        $item = "'$config_item' => array($disallowed),";
      } else {
        $item = "'$config_item' => '$config_value',";
      }
      $item .= $this->configItemComments($config_section, $config_item);
      $output .= $this->configFormatItem($item, 4);

    }
    $output .= $this->configFormatItem("),", 3);
    $output .= "\r\n";

    return $output;
  }

  /**
   * Allows creation of inline comments
   *
   * @param $config_section
   * @param $config_item
   * @return string
   */
  private function configItemComments($config_section, $config_item) {
    if (($config_section == 'uploads') && ($config_item == 'location')) {
      return $this->configFormatCommentInline('Change this line to reflect your full path to your upload directory');
    }
    return '';
  }

  /**
   * Creates an inline comment
   *
   * @param $comment_string
   * @return string
   */
  private function configFormatCommentInline($comment_string) {
    return "\t//$comment_string";
  }
  /**
   * Creates a multi line comment
   *
   * @param $comment_array
   * @param int $identLevel
   * @return string
   */
  private function configFormatCommentMulti($comment_array, $identLevel) {
    $formatted = $this->configFormatItem("/*", $identLevel);
    foreach ($comment_array as $comment_line) {
      $formatted .= $this->configFormatItem(" * $comment_line", $identLevel);
    }
    $formatted .= $this->configFormatItem("*/", $identLevel);
    return $formatted;
  }

  /**
   * Create a formatted single line comment in the config
   *
   * @param $comment_string
   * @param int $identLevel
   * @return string
   */
  private function configFormatCommentSingle($comment_string, $identLevel) {
    $formatted = $this->configFormatItem("/* $comment_string */", $identLevel);
    return $formatted;
  }

  /**
   * Formats the config item with the given number of preceding tabs and adds a new line after
   *
   * @param $comment_string
   * @param int $identLevel
   * @return string
   */
  private function configFormatItem($comment_string, $identLevel) {
    $i = 0;
    $formatted = '';
    while ($identLevel >= $i) {
      $formatted .= "\t";
      $i++;
    }
    $formatted .= "$comment_string";
    return "$formatted\r\n";
  }

  /**
   * Change the admin-area password
   *
   * @param $oldpassword
   * @param $newpassword
   * @param $confirmpassword
   *
   * @return bool
   */
  function changePassword($oldpassword, $newpassword, $confirmpassword) {

    if (password_verify($oldpassword, $this->settings['security']['password_hash'])) {
      if ($newpassword === $confirmpassword) {
        $this->changeSetting('security', 'password_hash',password_hash($newpassword, PASSWORD_DEFAULT));
        $this->changeSetting('security', 'last_changed', date("m-d-y"));
        return true;
      } else {
        return false;
        # throw mismatch passwords
      }
    } else {
      $_SESSION[$this->settings['security']['admin_session']] = false;
      return true;
    }
  }

  /**
   * Add an extension to the disallowed array
   *
   * @param $ext
   */
	public function addExtension($ext){
		array_push($this->settings['security']['disallowed_files'], $ext);
    var_dump($this->getSettings()['security']['disallowed_files']);
		$this->configSave();
	}

  /**
   * Remove an extension from the disallowed array
   *
   * @param $ext
   */
	public function deleteExtension($ext){
		
		$index = array_search($ext, $this->settings['security']['disallowed_files']);
    unset($this->settings['security']['disallowed_files'][$index]);
    $this->configSave();
		
	}

  /**
   * Adds a MIME type to the blacklist
   * @param $mime
   */
  public function addMIME($mime) {
    array_push($this->settings['security']['disallowed_mime_types'], $mime);
    var_dump($this->getSettings()['security']['disallowed_files']);
    $this->configSave();
  }

  /**
   * Removes a blacklisted MIME type
   * @param $mime
   */
  public function deleteMIME($mime) {
    $index = array_search($mime, $this->settings['security']['disallowed_mime_types']);
    unset($this->settings['security']['disallowed_files'][$index]);
    $this->configSave();
  }

  /**
   * Creates a new config section
   *
   * @param $setting
   * @param string $setting_parent
   */
  private function createNewSetting($setting, $setting_parent = '') {
    if ($setting_parent != '') {
      if (($setting_parent == 'security') && ($setting == 'disallowed_mime_types')) {
        $this->settings[$setting_parent][$setting] = array();
      }
    }
    $this->configSave();
  }


  /**
   * @param $setting
   * @param string $setting_parent
   * @return mixed
   */
  public function getSetting($setting, $setting_parent = '') {
    if ($setting_parent != '') {
      if(!array_key_exists($setting_parent, $this->settings)) {
        // Return false for now, maybe also throw an error?
        die("Need to make a better error!");
      }
      else if (!array_key_exists($setting, $this->settings[$setting_parent])) {
        $this->createNewSetting($setting, $setting_parent);
      }

      return $this->settings[$setting_parent][$setting];
    } else {
      if (!array_key_exists($setting_parent[$setting], $this->settings)) {
        $this->createNewSetting($setting);
      }

      return $this->settings[$setting];
    }
  }
}
