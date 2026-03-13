<?php

$data = $_POST['chart'];

$data = str_replace('data:image/png;base64,','',$data);
$data = base64_decode($data);

file_put_contents("chart.png",$data);

?>
