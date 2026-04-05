<?php
$files = glob('*');
foreach($files as $file){
    if(is_file($file) && $file != 'clean.php')
        unlink($file);
}
echo "Cleaned except clean.php";
