<?php if($show){ ?>
<html prefix="og: http://ogp.me/ns#">

    <head>
        <title>
            UploadX - a ShareX proxy
        </title>
        <meta property="og:image" content="<?php echo $src ; ?>" />
    </head>

    <body <?php if (strpos($type, 'text') !== false)echo "onload='loadDoc();'"; ?>>

                <div id="st-container" class="st-container">

                    <nav class="st-menu st-effect-4" id="menu-4">

                        <h2>UploadX | <?php echo $id;?></h2>
                        <?php include $GLOBALS['dir'].'/lib/templates/display/info_panel.php' ?>

                    </nav>

                <div class="st-pusher">

                    <div id="st-trigger-effects" class="animated fadeInDown">

                        <button data-effect="st-effect-4">
                            <img id="menu_button" src="./res/img/menu.png" height="40px" onclick='return false;'>
                        </button>

                    </div>

                    <div  class="animated fadeIn">

                <?php } else { ?>
                    </div>

                </div>
            </div>

            <script src="./res/js/classie.js"></script>
            <script src="./res/js/sidebarEffects.js"></script>
    </body>

</html>

<?php } ?>