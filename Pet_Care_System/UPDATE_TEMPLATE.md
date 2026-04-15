# File Update Template

All PHP pages that use header/sidebar/footer should follow this structure:

```php
<?php
session_start();
// Add any role checks here
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'required_role') {
    header("Location: ../auth/login.php");
    exit;
}

// Add your PHP logic here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title | PetCare</title>
    <?php include('../includes/head.php'); ?>
    <!-- Add page-specific styles here if needed -->
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/sidebar.php'); ?>
    
    <div class="main-content">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Page Title</h1>
            <p style="color: #64748b; font-size: 1rem;">Page description.</p>
        </div>
        
        <!-- Your page content here -->
        <div class="card">
            <!-- Use card, table, form-container classes from style.css -->
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
```

## Important Notes:
- ❌ DO NOT include `navbar.php` - it doesn't exist
- ✅ DO include `head.php` in the `<head>` section
- ✅ DO include `header.php` and `sidebar.php` in the `<body>` section
- ✅ Use classes from `style.css` (card, card-grid, btn, form-group, etc.)
- ✅ All styles are in `assets/css/style.css` - no inline styles in includes

## Files Updated:
- ✅ index.php
- ✅ auth/login.php
- ✅ auth/register.php
- ✅ includes/header.php
- ✅ includes/sidebar.php
- ✅ includes/footer.php
- ✅ includes/head.php
- ✅ assets/css/style.css
- ✅ admin/dashboard.php
- ✅ admin/manage_users.php
- ✅ owner/dashboard.php
- ✅ owner/add_pet.php
- ✅ owner/view_pet_profile.php
- ✅ owner/view_health_records.php
- ✅ veterinarian/dashboard.php
- ✅ health/add_record.php
- ✅ visits/view_visit.php
- ✅ visits/schedule_visit.php
- ✅ reports/view_report.php
- ✅ reports/generate_report.php
- ✅ remainders/list_remainders.php

## Files Still Need Updating (remove navbar.php, add proper structure):
- admin/activity_logs.php
- admin/view_reports.php
- admin/manage_notifications.php
- admin/manage_records.php
- admin/manage_veterinarians.php
- admin/manage_pets.php
- owner/update_pet_details.php
- owner/reminders.php
- owner/view_care_instructions.php
- owner/view_clinic_visits.php
- owner/view_vaccination_schedule.php
- veterinarian/manage_clinic_visit.php
- veterinarian/generate_report.php
- veterinarian/view_pet_history.php
- veterinarian/add_care_instruction.php
- veterinarian/update_health_record.php
- veterinarian/add_health_record.php
- veterinarian/update_vaccination.php
- health/delete_record.php
- health/view_record.php
- health/update_record.php
- vaccination/vaccination_history.php
- vaccination/view_schedule.php
- vaccination/update_vaccine.php
- vaccination/add_vaccine.php
- visits/visit_history.php
- visits/update_visit.php
- remainders/notification_log.php
- remainders/send_remainder.php
- remainders/create_remainder.php


