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

// Generate PDF using HTML that can be printed/saved as PDF
$filename = $report_type . '_report_' . date('Y-m-d') . '.html';

// Set headers to force download
header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($report_title) ?></title>
    <style>
        @media print {
            @page { margin: 1cm; }
            body { margin: 0; }
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 12px;
        }
        h1 { 
            color: #333; 
            border-bottom: 2px solid #333; 
            padding-bottom: 10px; 
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #333; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #4a5568; 
            color: white; 
            font-weight: bold;
        }
        tr:nth-child(even) { 
            background-color: #f2f2f2; 
        }
        .header-info { 
            margin-bottom: 20px; 
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($report_title) ?></h1>
    <div class="header-info">
        <p><strong>Report Period:</strong> <?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?></p>
        <p><strong>Generated:</strong> <?= date('Y-m-d H:i:s') ?></p>
        <?php if ($vet_id): ?>
            <p><strong>Veterinarian ID:</strong> <?= htmlspecialchars($vet_id) ?></p>
        <?php endif; ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Details</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($report_data) > 0): ?>
                <?php foreach ($report_data as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['details']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-data">No data found for the selected period.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 10px;">
        <p>PetCare Management System - Generated on <?= date('Y-m-d H:i:s') ?></p>
    </div>
</body>
</html>
