<?php
require_once('./database_connection.php');
header("Access-Control-Allow-Origin: localhost");

$imid = isset($_GET['imid']) ? intval($_GET['imid']) : 0;


// Prepare and execute SQL statement to count number of reps
$stmt = $database_connection->prepare("SELECT body, extension FROM items WHERE id = ? and date_deleted = '0000-00-00 00:00:00'");
$stmt->bind_param("i", $imid);
$stmt->execute();
$stmt->bind_result($imgbody, $imgextension);
$stmt->fetch();
$stmt->close();

// decode the base64 data


if (!is_null($imgbody))
{
    $image_data = base64_decode($imgbody);
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_buffer($finfo, $image_data);

    header('Content-Type: ' . $mime_type);
    echo $image_data;
}
else
{
    // create a new 250x150 image
    $image = imagecreatetruecolor(250, 150);

    // allocate white color
    $white = imagecolorallocate($image, 255, 255, 255);

    // fill the background with white color
    imagefill($image, 0, 0, $white);

    $font_file = __DIR__ . '/sequesters/dejavu.ttf';

    // allocate black color
    $black = imagecolorallocate($image, 0, 0, 0);

    // draw a black rectangle with a 2-pixel border
    imagerectangle($image, 1, 1, 248, 148, $black);
    imagerectangle($image, 2, 2, 247, 147, $black);

    // set font size and calculate the text box size
    $font_size = 20;
    $text_box = imagettfbbox($font_size, 0, $font_file, 'Image Deleted');

    // calculate the position to center the text
    $text_width = $text_box[2] - $text_box[0];
    $text_height = $text_box[1] - $text_box[7];
    $x = (250 - $text_width) / 2;
    $y = (150 - $text_height) / 2 + $font_size;

    // write the text centered in the rectangle
    imagettftext($image, $font_size, 0, $x, $y, $black, $font_file, 'Image Deleted');

    // set the content type header to output as image
    header('Content-Type: image/png');

    // output the image to the browser
    imagepng($image);

    // free up memory
    imagedestroy($image);

    
}


    
?>
