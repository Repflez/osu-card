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

    // Doge mode
    $dogeMode   = isset($_GET['doge']) ? $_GET['doge'] == 'wow' : false;

    if (!$rawStats) {
        // This makes it easy to call the data
        $user = $data['0'];

        // Setup the actual image
        setup_image(408, 96);
        setup_basic_card_data($getData);
        setup_font_colors();

        // Is the data empty? Do an error
        if ($data == null) {
            box( 2, 3, 46.5, 6);
            twrite($fontR, 4, 4.5,0,'We had a problem fetching the data for this');
            twrite($fontR, 6, 6.5,0,'account.');
            twrite($fontY,14, 6.5,0,' We don\'t know what went wrong!');

        // No, let's continue and make the card
        } else {

            // Create username hax
            // To cancel box mode creation, use $mode = 4
            if ($dogeMode) {
                $nameData = array(
                    'name' => 'doge',
                    'length' => 4,
                );
                twrite($fontW, 25,  1,  0, 'wow');
                twrite($fontR, 18,  3,  0, 'so pro');
                twrite($fontB, 20,  8,  0, 'many hits');
                twrite($fontY, 35,  8,  0, 'such rank');
                twrite($fontB, 38,  3,  0, 'best mode');
            } else {
                $nameData = array(
                    'name' => $user['username'],
                    'length' => strlen($user['username']),
                );
            }

            // Define game mode names
            if ($mode == 0) $gameMode = 'osu!standard';
            if ($mode == 1) $gameMode = 'osu!taiko';
            if ($mode == 2) $gameMode = 'Catch the Beat';
            if ($mode == 3) $gameMode = 'osu!mania';

            // Define the game modes boxes
            if ($mode == 0) box(37, 0,2 + strlen($gameMode), 3);
            if ($mode == 1) box(40, 0,2 + strlen($gameMode), 3);
            if ($mode == 2) box(35, 0,2 + strlen($gameMode), 3);
            if ($mode == 3) box(40, 0,2 + strlen($gameMode), 3);

            // Define generic boxes
            box( 0,  0, 12, 12);
            box(13,  0, 2 + $nameData['length'], 3);
            box(13,  4, 38,  4);
            box(13,  9, 38,  3);

            // Write username
            twrite($fontW, 14, 1, 0, $nameData['name']);

            // Get user's avatar
            $avatar = get_osu_avatar($dogeMode ? 'doge' : $user['user_id']);

            //Resize osu! avatar
            resize_osu_avatar($avatar);

            // Write game mode name
            if ($mode == 0) twrite($fontW, 38,  1,  0, $gameMode);
            if ($mode == 1) twrite($fontW, 41,  1,  0, $gameMode);
            if ($mode == 2) twrite($fontW, 36,  1,  0, $gameMode);
            if ($mode == 3) twrite($fontW, 41,  1,  0, $gameMode);

            // Write the level data
            twrite($fontB,  14,  5,  0, 'Lv:');
            twrite($fontR,  18,  5,  3, unround_number($user['level']));
            twrite($fontR,  46,  5,  4, unround_number($user['level']) + 1);

            // Write the accuracy and PP data
            if ($user['accuracy'] == 100) {
                // We have 100% Accuracy. No need to round 100.
                twrite($fontY, 19,  6,  6, '100% Accuracy');

                // At this stage the user should have PP.
                twrite($fontB, 36,  6,  6, number_format($user['pp_raw'], 0) . ' PP');
            } elseif ($user['accuracy'] == 0) {
                // We don't have accuracy. How you can round 0?
                twrite($fontY, 19,  6,  6, 'No Accuracy');

                if ($user['pp_raw'] == 0) twrite($fontB, 36,  6,  6, 'No PP');
                else twrite($fontB, 35,  6,  6, number_format($user['pp_raw'], 0) . ' PP');
            } else {
                // I can round to potato!
                twrite($fontY, 19,  6,  6, number_format($user['accuracy'], 2) . '% Accuracy');

                if ($user['pp_raw'] == 0) twrite($fontB, 39,  6,  6, 'No PP');
                else twrite($fontB, 38,  6,  6, number_format($user['pp_raw'], 0) . ' PP');
            }

            // Write the Total Hits data
            twrite($fontB, 14, 10,  0, 'Total Hits:');
            twrite($fontY, 26, 10,  0, $user['count50'] + $user['count100'] + $user['count300']);

            // Write the PP Rank data
            if ($user['pp_rank'] == 0) twrite($fontR, 39,  10,  0, 'Unranked'); // RIP Saturos ;_;
            else {
                twrite($fontB,  37,  10,  0, 'Rank:');
                twrite($fontY,  42,  10,  0, '#' . $user['pp_rank']);
            }

            // Calculate the level bar width
            $width = number_format($user['level'], 3);          // Round up to 3 decimals
            $width = unround_number($width, true);              // Get only the decimals
            $width *= 100;                                      // Multiply by 100
            $width /= 1000;                                     // Make it go to the thousandth
            $width *= 2.64;                                     // Calculate the final width
            if ($width < 72) $width += 71;                      // Fix for overlapping bars
            if ($width > 264) $width -= 71;                     // Fix for overlapping bars: Take 2
            $width += 104;                                      // Add padding to the final bar
            bars(71+104, 41, 368, 7);                           // Draw the back bar (red)
            bars(71+104, 41, $width);                           // Draw the actual bar (blue)
        }
    }
