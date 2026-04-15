<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Get parameters from URL
$report_type = $_GET['type'] ?? 'health';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$vet_id = $_GET['vet_id'] ?? '';

// Initialize report data
$report_data = [];
$report_title = ucfirst($report_type) . ' Report';

// Fetch data based on report type
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <style>
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">📊 View Report: <?php echo ucfirst($report_type); ?></h1>
            <p style="color: #64748b; font-size: 1rem;">View and export system reports.</p>
        </div>
        
        <?php if (empty($start_date) || empty($end_date)): ?>
            <div class="card" style="padding: 20px; text-align: center; color: #64748b;">
                <p>Please select date range to generate report.</p>
                <a href="../veterinarian/generate_report.php" class="btn btn-primary">Go to Generate Report</a>
            </div>
        <?php else: ?>
        <div class="card">
            <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
                <strong>Report Period:</strong> <?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Details</th><th>Date</th></tr>
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
                            <td colspan="4" style="text-align: center; padding: 20px; color: #64748b;">
                                No data found for the selected period.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="actions">
            <a href="export_pdf.php?type=<?= urlencode($report_type) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&vet_id=<?= urlencode($vet_id) ?>" class="btn btn-primary">Export PDF</a>
            <a href="export_excel.php?type=<?= urlencode($report_type) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>&vet_id=<?= urlencode($vet_id) ?>" class="btn btn-secondary">Export Excel</a>
        </div>
        <?php endif; ?>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
