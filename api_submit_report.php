<?php
session_start();
require_once("../config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$date = date("Y-m-d");

$sale        = $_POST['sale'];
$purchase    = $_POST['purchase'];
$zomato_sale = $_POST['zomato_sale'];
$swiggy_sale = $_POST['swiggy_sale'];
$cash_sale   = $_POST['cash_sale'];
$card_sale   = $_POST['card_sale'];
$expenses    = $_POST['expenses'];

$sql = "INSERT INTO reports (user_id, report_date, sale, purchase, zomato_sale, swiggy_sale, cash_sale, card_sale, expenses) 
        VALUES ('$user_id', '$date', '$sale', '$purchase', '$zomato_sale', '$swiggy_sale', '$cash_sale', '$card_sale', '$expenses')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Report submitted successfully'); window.location.href='../views/submit_report.php';</script>";
} else {
    echo "Error: " . $conn->error;
}
?>
