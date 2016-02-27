<?php
    // You need to adjust this to suit
    $filename = $_POST['fname'];

    $src     = $_POST['src'];
    $src     = substr($src, strpos($src, ",") + 1);
    $decoded = base64_decode($src);

    // delete image file if it already exists
    if(file_exists($filename)) unlink($filename);
    
    $fp = fopen($filename,'wb');
    fwrite($fp, $decoded);
    fclose($fp);

?>

