<?php

$data = json_decode(file_get_contents("php://input"));

$image = $data->image;

$image = str_replace('data:image/png;base64,', '', $image);
$image = str_replace(' ', '+', $image);

$data = base64_decode($image);

file_put_contents("chart.png",$data);

?>