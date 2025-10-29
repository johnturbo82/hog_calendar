<!DOCTYPE HTML>
<html>

<head>
    <title><?php echo (isset($this->_['title'])) ? $this->_['title'] : SHORT_NAME . " Events" ?><?php echo ($this->_['admin'] == PSEUDO_ADMIM_PASSWORD) ? " ADMIN" : "" ?></title>
    <link rel="stylesheet" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>css/vars.css?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>">
    <link rel="stylesheet" href="<?php echo SITE_ADDRESS ?>css/styles.css?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.ico">
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.ico">
    <link rel="icon" type="image/gif" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.gif">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-57x57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-60x60.png" sizes="60x60">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-72x72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-76x76.png" sizes="76x76">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-114x114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-120x120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-128x128.png" sizes="128x128">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-144x144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-152x152.png" sizes="152x152">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-180x180.png" sizes="180x180">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/apple-touch-icon-precomposed.png">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon-196x196.png" sizes="196x196">
    <meta name="msapplication-TileImage" content="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/win8-tile-144x144.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-navbutton-color" content="#ffffff">
    <meta name="msapplication-square70x70logo" content="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/win8-tile-70x70.png">
    <meta name="msapplication-square144x144logo" content="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/win8-tile-144x144.png">
    <meta name="msapplication-square150x150logo" content="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/win8-tile-150x150.png">
    <meta name="msapplication-wide310x150logo" content="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/win8-tile-310x150.png">
    <meta name="msapplication-square310x310logo" content="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/win8-tile-310x310.png">
    <meta name="theme-color" content="<?php echo ($this->_['admin']) ? "#501014" : "#0a1014" ?>">
    <!-- <meta name="mobile-web-app-capable" content="yes"> -->
    <!-- <meta name="apple-mobile-web-app-status-bar-style" content="default"> -->
    <!-- <link rel="manifest" href="manifest.json"> -->
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/jquery-3.6.1.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/copy_to_clipboard.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/datatables.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/site.js?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>"></script>
</head>

<body <?php echo ($this->_['admin']) ? "class='admin'" : "" ?>>
    <div class="container">
        <div class="content">
            <div class="nav <?php echo ($this->_['admin']) ? "admin" : "" ?>">
                <nav>
                    <a class="<?php echo ($_GET['view'] == "events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=events<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/events<?php echo ($_GET['view'] == "events") ? "_active" : "" ?>.svg" alt="Events">
                        <span>Events</span>
                    </a>
                    <a class="<?php echo ($_GET['view'] == "my_events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=my_events">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/my_events<?php echo ($_GET['view'] == "my_events") ? "_active" : "" ?>.svg" alt="Meine Events">
                        <span>Meine<br />Events</span>
                    </a>
                    <a class="<?php echo ($_GET['view'] == "past_events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=past_events<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/past_events<?php echo ($_GET['view'] == "past_events") ? "_active" : "" ?>.svg" alt="Vergangene Events">
                        <span>Vergangene<br />Events</span>
                    </a>
                    <?php
                    if ($this->_['admin'] == PSEUDO_ADMIM_PASSWORD) {
                    ?>
                        <a class="<?php echo ($_GET['view'] == "polls") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=polls<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/survey<?php echo ($_GET['view'] == "polls") ? "_active" : "" ?>.svg" alt="Abstimmungen">
                            <span>Umfragen</span>
                        </a>
                        <a class="<?php echo ($_GET['view'] == "tutorials") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=tutorials<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/help<?php echo ($_GET['view'] == "tutorials") ? "_active" : "" ?>.svg" alt="Hilfe">
                            <span>Hilfe</span>
                        </a>
                    <?php
                    } else {
                    ?>
                        <a class="<?php echo ($_GET['view'] == "help") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=help">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/help<?php echo ($_GET['view'] == "help") ? "_active" : "" ?>.svg" alt="Hilfe">
                            <span>Hilfe</span>
                        </a>
                    <?php
                    }
                    ?>
                </nav>
            </div>
            <img class="logo" src="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/logo.png" alt="<?php echo LEGAL_ENTITY_NAME ?>" />
            <h1 class="app-name"><?php echo APP_NAME ?></h1>
            <?php echo $this->_['content'] ?>
        </div>
        <footer>
            &copy; Oliver Schöttner 2023
            <?php 
            if (defined('SUPPORT_EMAIL')) {
                echo "- Bei Fragen und Anregungen: <a href='mailto:" . SUPPORT_EMAIL . "?subject=" . SITE_ADDRESS . " - Version " . CURRENT_VERSION ."'>" . SUPPORT_EMAIL ."</a>";
            }
            ?>
            <br />
            <span>Version <?php echo CURRENT_VERSION ?> | <a href="<?php echo SITE_ADDRESS ?>?view=support">Support und Versionshinweise</a></span>
        </footer>
    </div>
</body>

</html>