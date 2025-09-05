<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Daily Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 10px; }
        input, select { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px; width: 100%; background: #28a745; color: #fff; border: none; border-radius: 5px; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <h2>Daily Report Form</h2>
    <form method="POST" action="../api/submit_report.php">
        <label>Sale Amount</label>
        <input type="number" name="sale" required>

        <label>Purchase Amount</label>
        <input type="number" name="purchase" required>

        <label>Zomato Sale</label>
        <input type="number" name="zomato_sale" required>

        <label>Swiggy Sale</label>
        <input type="number" name="swiggy_sale" required>

        <label>Cash Sale</label>
        <input type="number" name="cash_sale" required>

        <label>Card Sale</label>
        <input type="number" name="card_sale" required>

        <label>Expenses</label>
        <input type="number" name="expenses" required>

        <button type="submit">Submit Report</button>
    </form>
</body>
</html>
