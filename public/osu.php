<?php
    // Your app's name
    $appName    = 'osu!card';
    // Your app's URL
    $appURL     = 'http://example.com';
    // Your app's version number
    $appVersion = '1.0';
    // Your e-mail (This is sent to the server)
    $appEmail   = 'example@example.com';
    // Your osu! API Key
    $apiKey     = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    // osu!APIlib path in the server
    $apiPath    = '/path/to/osucard';
    // osu!'s API URL (Don't modify this unless osu! changes URL)
    $osuPath    = 'https://osu.ppy.sh/api/';
    // Include osu!api.php
    include_once( $apiPath . '/osu!api.php' );
    // Include Card Framework
    include_once( $apiPath . '/card.php' );

    // Load modes depending og the get string.
    if (!isset($_GET['get'])||$_GET['get']==''||empty($_GET['get'])) make_error();          // Show Error
    if ($_GET['debug']=='very') $rawStats = true;                                     // Raw Stats
    if ($_GET['get']=='user')           include_once( $apiPath . '/mode/user.php' );        // User Card
    elseif ($_GET['get']=='userbar')    include_once( $apiPath . '/mode/userbar.php' );     // Userbar
    else make_error();                                                                      // Show Error

    // Show raw data.
    if ($rawStats) {

        // Escape HTML in the data array.
        // http://php.net/htmlspecialchars#45340
        function recurse_array_HTML_safe(&$arr) {
            foreach ($arr as $key => $val)
                if (is_array($val))
                    recurse_array_HTML_safe($arr[$key]);
                else
                    $arr[$key] = htmlspecialchars($val, ENT_QUOTES);
        }

        echo 'osu! Data Obtained:<br /><pre>````<br />';
        recurse_array_HTML_safe($data);
        print_r($data);
        echo '````</pre><br />Raw Data:<br /><pre>';
        echo htmlspecialchars($result, ENT_QUOTES);
        echo '</pre>';
        die();
    }


    send_image($img);
    destroy_image($img);

?>
