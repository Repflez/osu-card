<?php
    // Get the username data. If it's unspecified, use peppy
    $getData    = $_GET['u'] ? urlencode($_GET['u']) : 'peppy';

    // Lower case the string
    $getData    = strtolower($getData);

    // Get the current game mode. Assume 0 if unspecified
    $mode       = (int) $_GET['m'] ? (int) $_GET['m'] : 0;

    // Call osu! for the user data and cache the results for 3 hours
    $result     = osu_get_user($getData, $mode, 3);

    // Decode the JSON response on an Array
    $data       = json_decode($result, true);

    if (!$rawStats) {
        // This makes it easy to call the data
        $user = $data['0'];

        // Setup the actual image
        setup_image(352,24);
        setup_basic_card_data($getData);
        setup_font_colors();

        // Is the data empty? Do an error
        if ($data == null || ($user['ranked_score'] == 0 || $user['pp_rank'] == 0)) {

            // Define the box
            box(  0,  0, 44,  3);

            // Write game mode
            twrite($fontR,  3,  1,  0, 'Mode not played or user does not exist.');

        // No, let's continue and make the card
        } else {
            // Define game mode names
            if ($mode == 0) $gameMode = 'osu!';
            if ($mode == 1) $gameMode = 'Taiko';
            if ($mode == 2) $gameMode = 'CtB';
            if ($mode == 3) $gameMode = 'Mania';

            // Define the box
            box(  0,  0, 44,  3);

            // Write game mode
            twrite($fontW,  1,  1,  5, $gameMode);

            // Write Score
            twrite($fontW,  7,  1,  0, 'S:');
            twrite($fontB,  9,  1,  0, nice_number($user['ranked_score']));

            // Write Accuracy
            twrite($fontW,  17,  1,  0, 'A:');
            twrite($fontB,  19,  1,  0, number_format($user['accuracy'], 2) . '%');

            // Write Level
            twrite($fontW,  27,  1,  0, 'L:');
            twrite($fontB,  29,  1,  0, unround_number($user['level']));

            // Write Rank
            twrite($fontW,  33,  1,  0, 'R:');
            twrite($fontB,  35,  1,  0, '#' . $user['pp_rank']);
        }
    }


    function nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),2).'T';
        else if($n>1000000000) return round(($n/1000000000),2).'B';
        else if($n>1000000) return round(($n/1000000),2).'M';
        else if($n>1000) return round(($n/1000),2).'K';

        return number_format($n);
    }
