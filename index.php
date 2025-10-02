<?php 
session_start();

require 'service.php';
require 'view.php';


$message = '';
$messageType = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    $result = processUserAction($action);


    $_SESSION['message'] = $result['message'];
    $_SESSION['messageType'] = $result['type'];

    header ('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_SESSION['message'])){
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}

$data = getPageData();
showPage($data['users'], $data['colors'], $data['userColors'], $message, $messageType);