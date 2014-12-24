<?php

function setup_image($width, $height) {
    global $img;
    header('Content-type:image/png');
    $img = ImageCreateTrueColor($width, $height);
}

function setup_basic_card_data($user = '') {
    global $img, $c;

        $c['bg']        = ImageColorAllocate($img, 40, 40, 90);
        $c['bxb0']      = ImageColorAllocate($img, 22, 34, 74);
        $c['bxb1']      = ImageColorAllocate($img, 39, 56,115);
        $c['bxb2']      = ImageColorAllocate($img, 59, 76,135);
        $c['bxb3']      = ImageColorAllocate($img, 49, 66,125);

        for($i=0;$i<100;$i++) {
            $c[$i]      = ImageColorAllocate($img, 103-$i/2, 47-$i/3, 139-$i/1.6);
        }

        $c['barE1']     = ImageColorAllocate($img,120,150,180);
        $c['barE2']     = ImageColorAllocate($img, 30, 60, 90);
        $c['bar1']['1'] = ImageColorAllocate($img,215, 91,129);
        $c['bar2']['1'] = ImageColorAllocate($img, 90, 22, 43);
        $c['bar1']['2'] = ImageColorAllocate($img,255,136,154);
        $c['bar2']['2'] = ImageColorAllocate($img,151,  0, 38);
        $c['bar1']['3'] = ImageColorAllocate($img,255,139, 89);
        $c['bar2']['3'] = ImageColorAllocate($img,125, 37,  0);
        $c['bar1']['4'] = ImageColorAllocate($img,255,251, 89);
        $c['bar2']['4'] = ImageColorAllocate($img, 83, 81,  0);
        $c['bar1']['5'] = ImageColorAllocate($img, 89,255,139);
        $c['bar2']['5'] = ImageColorAllocate($img,  0,100, 30);
        $c['bar1']['6'] = ImageColorAllocate($img, 89,213,255);
        $c['bar2']['6'] = ImageColorAllocate($img,  0, 66, 93);
        $c['bar1']['7'] = ImageColorAllocate($img,196, 33, 33);
        $c['bar2']['7'] = ImageColorAllocate($img, 70, 12, 12);

    ImageColorTransparent($img,0);
}

function setup_font_colors() {
    global $fontY, $fontR, $fontG, $fontB, $fontW;
    $fontY = fontc(255,250,240,255,240, 80,  1,  1,  1);
    $fontR = fontc(255,230,220,240,160,150,  1,  1,  1);
    $fontG = fontc(190,255,190, 60,220, 60,  1,  1,  1);
    $fontB = fontc(160,240,255,120,190,240,  1,  1,  1);
    $fontW = fontc(255,255,255,210,210,210,  1,  1,  1);
}

function make_error($generated = false) {
    global $img, $fontW;
    if (!$generated) {
        setup_image(304,96);
        setup_basic_card_data();
        setup_font_colors();
    }
    box( 2, 3,34.5,6);
    twrite($fontW, 8.5, 4.5, 0, 'Derpy Hooves was here.');
    twrite($fontW, 3.5, 6.5, 0, 'She didn\'t knew what went wrong!');
}

function send_image($img) {
    global $img;
    ImagePNG($img);
}

function destroy_image($img) {
    global $img, $fontY, $fontR, $fontG, $fontB, $fontW;
    ImageDestroy($img);

    ImageDestroy($fontY);
    ImageDestroy($fontR);
    ImageDestroy($fontG);
    ImageDestroy($fontB);
    ImageDestroy($fontW);
}

function get_osu_avatar($userId) {
    global $apiPath;

    if ($userId == 'doge')
        return $apiPath . '/joke/doge';

    // Prepare the avatar URL
    $fileName = $apiPath . '/cache/user/avatar/'. $userId;

    // Download the avatar and cache it for 24 hours
    $i = get_content($fileName, 'https://a.ppy.sh/' . $userId, 24);

    // Free the variable containing the avatar (To free memory!)
    unset($i);

    // Return the location of the avatar
    return $fileName;
}

function resize_osu_avatar($filename) {
    global $img;

    // Set a maximum height and width
    $width = $height = 86;

    // Get new dimensions
    list($width_orig, $height_orig) = getimagesize($filename);

    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig)
        $width = $height*$ratio_orig;
    else
        $height = $width/$ratio_orig;

    // Detect filetype
    $type = exif_imagetype($filename);

    // Resample
    if ($type == IMAGETYPE_PNG) $image = ImageCreateFromPNG($filename); // It's a PNG
    elseif ($type == IMAGETYPE_JPEG) $image = ImageCreateFromJPEG($filename); // It's a JPG/JPEG
    return ImageCopyResampled($img, $image, 5, 5, 0, 0, $width, $height, $width_orig, $height_orig);
}

function twrite($font, $x, $y, $l, $text) {
    global $img;
    $x *= 8;
    $y *= 8;
    $text .= '';
    if(strlen($text)<$l) $x+=($l-strlen($text))*8;
    for($i=0;$i<strlen($text);$i++)
        ImageCopyMerge($img, $font, $i * 8 + $x, $y, (ord($text[$i])%16) *8 ,floor(ord($text[$i])/16) * 8, 8, 8, 100);
}

function fontc($r1, $g1, $b1, $r2, $g2, $b2, $r3, $g3, $b3) {
    global $apiPath;
    $font = ImageCreateFromPNG($apiPath . '/img/font.png');
    ImageColorTransparent($font, 1);
    ImageColorSet($font, 6, $r1, $g1, $b1);
    ImageColorSet($font, 5, ($r1*2+$r2)/3, ($g1*2+$g2)/3, ($b1*2+$b2)/3);
    ImageColorSet($font, 4, ($r1+$r2*2)/3, ($g1+$g2*2)/3, ($b1+$b2*2)/3);
    ImageColorSet($font, 3, $r2, $g2, $b2);
    ImageColorSet($font, 0, $r3, $g3, $b3);
    return $font;
}

function box($x, $y, $w, $h) {
    global $img, $c;
    $x *= 8;
    $y *= 8;
    $w *= 8;
    $h *= 8;
    ImageRectangle($img, $x+0, $y+0, $x+$w-1, $y+$h-1, $c['bxb0']);
    ImageRectangle($img, $x+1, $y+1, $x+$w-2, $y+$h-2, $c['bxb3']);
    ImageRectangle($img, $x+2, $y+2, $x+$w-3, $y+$h-3, $c['bxb1']);
    ImageRectangle($img, $x+3, $y+3, $x+$w-4, $y+$h-4, $c['bxb2']);
    ImageRectangle($img, $x+4, $y+4, $x+$w-5, $y+$h-5, $c['bxb0']);
    for($i=5;$i<$h-5;$i++) {
        $n = (1-$i/$h) * 100;
        ImageLine($img, $x+5, $y+$i, $x+$w-6, $y+$i, $c[$n]);
    }
}

function bars($x, $y, $width, $color = 6) {
    global $img, $c;
    $y2 = $y+6;
    ImageFilledRectangle($img, $x, $y, $width, $y2, $c['bar2'][$color]);
    ImageFilledRectangle($img, $x, $y-1, $width, $y2-1, $c['bar1'][$color]);
}

function unround_number($number, $decimalsonly = false) {
  $broken_number = explode('.', $number);
  if ($decimalsonly) return $broken_number['1'];
  return $broken_number['0'];
}
