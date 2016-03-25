<head>
    <title>
        <?php if(isset($title)){ echo $title;} ?>
    </title>
    
    <?php if ( isset($type) && strpos($type, 'text') !== false){ ?>
    <script src="<?php echo $GLOBALS['home'] . "res/js/highlight.js"?>"></script>
    <script src="<?php echo $GLOBALS['home'] . "res/js/highlightloader.js"?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/highlight/monokai-sublime.css"; ?>">
    <?php }; ?>
    
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/default.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/component.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/normalize.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/demo.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/animate.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/themes/$theme"; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/sidebar/component.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/sidebar/demo.css "; ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['home'] . "res/css/sidebar/icons.css "; ?>">
    
    <link rel='shortcut icon' type='image/x-icon' href='<?php echo $GLOBALS['home'] .'res/img/favicon.ico ';?>'>
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    
    <?php
    if(isset($id)){
    ?>
        
    <meta property="og:site_name" content="UploadX">
    <meta property="og:url" content="<?php echo $GLOBALS['home'] . $id . '/'; ?>"/>
    <meta property="og:title" content="<?php echo "$id ($file_size)"; ?>">
    <meta property="og:image:type" content="<?php echo $type; ?>" />
    <meta property="og:image" content="<?php echo $src; ?>" />
    
    <?php
    }
    ?>
    
</head>
