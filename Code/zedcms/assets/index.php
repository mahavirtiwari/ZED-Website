
<?php $base = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $base .= '://'.$_SERVER['HTTP_HOST']; 
header("Location: $base");

?>