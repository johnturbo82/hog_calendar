<!DOCTYPE HTML>
<html>

<head>
    <title><?php echo (isset($this->_['title'])) ? $this->_['title'] : "InChap Events" ?></title>
    <link rel="stylesheet" href="<?php echo SITE_ADDRESS ?>css/styles.css?v=<?php echo CURRENT_VERSION ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo SITE_ADDRESS ?>images/icons/favicon.ico">
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_ADDRESS ?>images/icons/favicon.ico">
    <link rel="icon" type="image/gif" href="<?php echo SITE_ADDRESS ?>images/icons/favicon.gif">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-57x57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-60x60.png" sizes="60x60">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-72x72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-76x76.png" sizes="76x76">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-114x114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-120x120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-128x128.png" sizes="128x128">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-144x144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-152x152.png" sizes="152x152">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-180x180.png" sizes="180x180">
    <link rel="apple-touch-icon" href="<?php echo SITE_ADDRESS ?>images/icons/apple-touch-icon-precomposed.png">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS ?>images/icons/favicon-196x196.png" sizes="196x196">
    <meta name="msapplication-TileImage" content="images/icons/win8-tile-144x144.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-navbutton-color" content="#ffffff">
    <meta name="msapplication-square70x70logo" content="images/icons/win8-tile-70x70.png">
    <meta name="msapplication-square144x144logo" content="images/icons/win8-tile-144x144.png">
    <meta name="msapplication-square150x150logo" content="images/icons/win8-tile-150x150.png">
    <meta name="msapplication-wide310x150logo" content="images/icons/win8-tile-310x150.png">
    <meta name="msapplication-square310x310logo" content="images/icons/win8-tile-310x310.png">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0a1014">
    <script>
        function copyToClipboard(string, el) {
            el.innerHTML = "Kopiert!";
            let textarea;
            let result;

            try {
                textarea = document.createElement('textarea');
                textarea.setAttribute('readonly', true);
                textarea.setAttribute('contenteditable', true);
                textarea.style.position = 'fixed'; // prevent scroll from jumping to the bottom when focus is set.
                textarea.value = string;

                document.body.appendChild(textarea);

                textarea.focus();
                textarea.select();

                const range = document.createRange();
                range.selectNodeContents(textarea);

                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);

                textarea.setSelectionRange(0, textarea.value.length);
                result = document.execCommand('copy');
            } catch (err) {
                console.error(err);
                result = null;
            } finally {
                document.body.removeChild(textarea);
            }

            // manual copy fallback using prompt
            if (!result) {
                const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                const copyHotkey = isMac ? '⌘C' : 'CTRL+C';
                result = prompt(`Press ${copyHotkey}`, string); // eslint-disable-line no-alert
                if (!result) {
                    return false;
                }
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="content">
            <img src="<?php echo SITE_ADDRESS ?>images/Ingolstadt-Chapter.png" alt="H.O.G. Ingolstadt Chapter" />
            <h1>H.O.G. Ingolstadt Chapter Events</h1>
            <?php
            if ($this->_['menu']) {
            ?>
                <div class="menu">
                    <a class="button" href="<?php echo SITE_ADDRESS ?>?view=events">Events</a>
                    <?php
                    if (MODULE_POLLS) {
                    ?>
                        <a class="button" href="<?php echo SITE_ADDRESS ?>?view=polls">Abstimmungen</a>
                    <?php
                    }
                    if (MODULE_SHOP) {
                    ?>
                        <a class="button" href="<?php echo SITE_ADDRESS ?>?view=shop">Shop</a>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
            <?php echo $this->_['content'] ?>
        </div>
        <footer>
            <a href="mailto:oliver@schoettner.rocks">Bei Fragen und Anregungen: oliver@schoettner.rocks</a>
            <br />
            <span>Version <?php echo CURRENT_VERSION ?></span>
        </footer>
    </div>
</body>

</html>