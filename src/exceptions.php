<?php
include_once 'Include/head.php';

function inverse($x) {
    if (!$x) throw new Exception('Division by zero!');
    else return __FUNCTION__."($x) = ".(1/$x);
}

try {
    echo inverse(-5) . "<br/>\n";
    echo inverse(0) . "<br/>\n";  # Exception here!
    echo inverse(2) . "<br/>\n";
} catch (Exception $e) {  # Acolade needed!
    echo 'Exception catched: ', $e->getMessage(), "<br/>\n";
}  # Acolade needed!

// Continue execution
echo "Hello world!<br/>\n";

include_once 'Include/tail.php';
?>
