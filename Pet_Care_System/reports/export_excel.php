<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Get parameters
$report_type = $_GET['type'] ?? 'health';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$vet_id = $_GET['vet_id'] ?? '';

// Fetch report data
$report_data = [];
$report_title = ucfirst($report_type) . ' Report';

if ($start_date && $end_date) {
    if ($report_type == 'vaccination') {
        $query = "SELECT v.*, p.name AS pet_name, p.species 
                  FROM vaccinations v 
                  JOIN pets p ON v.pet_id = p.id 
                  WHERE v.date BETWEEN '$start_date' AND '$end_date'";
        if ($vet_id) {
            $query .= " AND v.pet_id IN (SELECT pet_id FROM health_records WHERE vet_id = '$vet_id')";
        }
        $query .= " ORDER BY v.date DESC";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [
                'id' => $row['id'],
                'name' => $row['pet_name'],
                'details' => $row['vaccine_name'] . ' - ' . $row['status'],
                'date' => $row['date']
            ];
        }
    } elseif ($report_type == 'health') {
        $query = "SELECT hr.*, p.name AS pet_name, p.species 
                  FROM health_records hr 
                  JOIN pets p ON hr.pet_id = p.id 
                  WHERE hr.date BETWEEN '$start_date' AND '$end_date'";
        if ($vet_id) {
            $query .= " AND hr.vet_id = '$vet_id'";
        }
        $query .= " ORDER BY hr.date DESC";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [
                'id' => $row['id'],
                'name' => $row['pet_name'],
                'details' => substr($row['diagnosis'], 0, 50) . '...',
                'date' => $row['date']
            ];
        }
    } elseif ($report_type == 'visits') {
        $query = "SELECT cv.*, p.name AS pet_name, p.species 
                  FROM clinic_visits cv 
                  JOIN pets p ON cv.pet_id = p.id 
                  WHERE cv.visit_date BETWEEN '$start_date' AND '$end_date'";
        if ($vet_id) {
            $query .= " AND cv.vet_id = '$vet_id'";
        }
        $query .= " ORDER BY cv.visit_date DESC";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [
                'id' => $row['id'],
                'name' => $row['pet_name'],
                'details' => $row['reason'] . ' - ' . $row['status'],
                'date' => $row['visit_date']
            ];
        }
    }
}

// Set headers for Excel download
$filename = $report_type . '_report_' . date('Y-m-d') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Output Excel content
echo "PetCare Management System - " . $report_title . "\n";
echo "Report Period: " . $start_date . " to " . $end_date . "\n";
echo "Generated: " . date('Y-m-d H:i:s') . "\n\n";

echo "ID\tName\tDetails\tDate\n";

if (count($report_data) > 0) {
    foreach ($report_data as $row) {
        echo $row['id'] . "\t";
        echo $row['name'] . "\t";
        echo $row['details'] . "\t";
        echo $row['date'] . "\n";
    }
} else {
    echo "No data found for the selected period.\n";
}
?>
