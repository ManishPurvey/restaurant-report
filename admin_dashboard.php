<?php
session_start();
require_once("../config.php");

// Only allow admin (user_type = 'admin')
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Filters
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");
$selected_manager = isset($_GET['manager']) ? $_GET['manager'] : "";

// Fetch managers
$managers = $conn->query("SELECT id, username FROM users WHERE user_type = 'manager'");

// Fetch reports
$sql = "SELECT r.*, u.username 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.report_date = '$selected_date'";

if (!empty($selected_manager)) {
    $sql .= " AND r.user_id = '$selected_manager'";
}

$result = $conn->query($sql);
// CSV Download
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reports.csv');
    $output = fopen('php://output', 'w');

    // CSV Header
    fputcsv($output, ['Manager', 'Date', 'Sale', 'Purchase', 'Zomato', 'Swiggy', 'Cash', 'Card', 'Expenses']);

    // CSV Rows
    $csv_result = $conn->query($sql);
    while ($row = $csv_result->fetch_assoc()) {
        fputcsv($output, [
            $row['username'],
            $row['report_date'],
            $row['sale'],
            $row['purchase'],
            $row['zomato_sale'],
            $row['swiggy_sale'],
            $row['cash_sale'],
            $row['card_sale'],
            $row['expenses']
        ]);
    }
    fclose($output);
    exit();
}

// Initialize totals
$total_sale = $total_purchase = $total_zomato = $total_swiggy = 0;
$total_cash = $total_card = $total_expenses = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        th { background: #f4f4f4; }
        .filter-form { margin-bottom: 20px; }
        select, input { padding: 5px; }
        .summary-row { background: #e9f7ef; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Admin Dashboard - Reports</h2>

    <form method="GET" class="filter-form">
        <label for="date">Select Date:</label>
        <input type="date" name="date" value="<?php echo $selected_date; ?>">

        <label for="manager">Select Manager:</label>
        <select name="manager">
            <option value="">All Managers</option>
            <?php while ($m = $managers->fetch_assoc()) { ?>
                <option value="<?php echo $m['id']; ?>" 
                    <?php if ($selected_manager == $m['id']) echo "selected"; ?>>
                    <?php echo $m['username']; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>Manager</th>
            <th>Date</th>
            <th>Sale</th>
            <th>Purchase</th>
            <th>Zomato</th>
            <th>Swiggy</th>
            <th>Cash</th>
            <th>Card</th>
            <th>Expenses</th>
        </tr>

        <?php if ($result->num_rows > 0) { 
            while ($row = $result->fetch_assoc()) { 
                // Add to totals
                $total_sale     += $row['sale'];
                $total_purchase += $row['purchase'];
                $total_zomato   += $row['zomato_sale'];
                $total_swiggy   += $row['swiggy_sale'];
                $total_cash     += $row['cash_sale'];
                $total_card     += $row['card_sale'];
                $total_expenses += $row['expenses'];
                ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['report_date']; ?></td>
                    <td><?php echo $row['sale']; ?></td>
                    <td><?php echo $row['purchase']; ?></td>
                    <td><?php echo $row['zomato_sale']; ?></td>
                    <td><?php echo $row['swiggy_sale']; ?></td>
                    <td><?php echo $row['cash_sale']; ?></td>
                    <td><?php echo $row['card_sale']; ?></td>
                    <td><?php echo $row['expenses']; ?></td>
                </tr>
        <?php } ?>
            <!-- Totals Row -->
            <tr class="summary-row">
                <td colspan="2">TOTAL</td>
                <td><?php echo $total_sale; ?></td>
                <td><?php echo $total_purchase; ?></td>
                <td><?php echo $total_zomato; ?></td>
                <td><?php echo $total_swiggy; ?></td>
                <td><?php echo $total_cash; ?></td>
                <td><?php echo $total_card; ?></td>
                <td><?php echo $total_expenses; ?></td>
            </tr>
        <?php } else { ?>
            <tr><td colspan="9">No reports found for this filter</td></tr>
        <?php } ?>
    </table>
</body>
</html>
<form method="GET" class="filter-form">
    <label for="date">Select Date:</label>
    <input type="date" name="date" value="<?php echo $selected_date; ?>">

    <label for="manager">Select Manager:</label>
    <select name="manager">
        <option value="">All Managers</option>
        <?php 
        // Reset pointer (since we already looped managers above)
        $managers->data_seek(0); 
        while ($m = $managers->fetch_assoc()) { ?>
            <option value="<?php echo $m['id']; ?>" 
                <?php if ($selected_manager == $m['id']) echo "selected"; ?>>
                <?php echo $m['username']; ?>
            </option>
        <?php } ?>
    </select>

    <button type="submit">Filter</button>
    <button type="submit" name="download" value="csv">Download CSV</button>
</form>
