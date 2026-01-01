<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

$_SESSION['buy_now'] = true;
$_SESSION['buy_now_product_id'] = (int)$data['product_id'];

$_SESSION['gift_wrap'] = $data['gift_wrap'] ?? 0;
$_SESSION['gift_card'] = $data['gift_card'] ?? 0;
$_SESSION['gift_card_msg'] = $data['gift_card_msg'] ?? "";

echo json_encode(["success" => true]);
