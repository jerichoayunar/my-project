<?php
require_once __DIR__ . '/../../vendor/autoload.php';
include '../../includes/db.php';

use Mpdf\Mpdf;

// Get filters
$date = $_GET['date'] ?? '';
$name = $_GET['name'] ?? '';
$status = $_GET['status'] ?? '';

$conditions = [];

if (!empty($date)) {
    $conditions[] = "DATE(bookings.booking_date) = '" . $conn->real_escape_string($date) . "'";
}
if (!empty($name)) {
    $conditions[] = "clients.name LIKE '%" . $conn->real_escape_string($name) . "%'";
}
if (!empty($status)) {
    $conditions[] = "bookings.status = '" . $conn->real_escape_string($status) . "'";
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

// SQL query with title and price
$sql = "SELECT 
            clients.name AS client_name,
            bookings.booking_date,
            bookings.status,
            GROUP_CONCAT(packages.title SEPARATOR '||') AS package_titles,
            GROUP_CONCAT(packages.price SEPARATOR '||') AS package_prices
        FROM bookings 
        LEFT JOIN clients ON bookings.client_id = clients.id 
        LEFT JOIN packages ON bookings.package_id = packages.id 
        $whereClause
        GROUP BY bookings.client_id, bookings.booking_date, bookings.status
        ORDER BY bookings.booking_date DESC";

$result = $conn->query($sql);

// Start HTML with styles
$html = '
<style>
    body { font-family: sans-serif; }
    h2 { text-align: center; color: #2C3E50; margin-bottom: 20px; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 auto;
        font-size: 12px;
    }
    th {
        background-color: #3498DB;
        color: white;
        padding: 8px;
        text-align: left;
    }
    td {
        border: 1px solid #ddd;
        padding: 8px;
        vertical-align: top;
    }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style>
<h2>Booking Report</h2>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Client Name</th>
            <th>Packages</th>
            <th>Booking Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';

if ($result && $result->num_rows > 0) {
    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $titles = explode('||', $row['package_titles']);
        $prices = explode('||', $row['package_prices']);
        $packageOutput = [];

        for ($i = 0; $i < count($titles); $i++) {
            $title = htmlspecialchars($titles[$i]);
            $price = isset($prices[$i]) ? number_format(floatval($prices[$i]), 2) : '0.00';
            $packageOutput[] = "$title (₱$price)";
        }

        $packagesFormatted = implode('<br>', $packageOutput);

        $html .= "<tr>
                    <td>{$counter}</td>
                    <td>" . htmlspecialchars($row['client_name']) . "</td>
                    <td>$packagesFormatted</td>
                    <td>" . htmlspecialchars($row['booking_date']) . "</td>
                    <td>" . htmlspecialchars($row['status']) . "</td>
                  </tr>";
        $counter++;
    }
} else {
    $html .= '<tr><td colspan="5">No bookings found.</td></tr>';
}

$html .= '</tbody></table>';

// Generate PDF
$mpdf = new Mpdf(['default_font' => 'Arial']);
$mpdf->WriteHTML($html);
$mpdf->Output('bookings_report.pdf', 'I'); // Display inline
