# Database Setup Instructions

## 📋 How to Upload the Database to phpMyAdmin

### Step 1: Open phpMyAdmin
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Login with your MySQL credentials (usually username: `root`, password: blank)

### Step 2: Create/Select Database
- The SQL file will automatically create the database `petcare_system_db`
- If the database already exists, it will use it

### Step 3: Import the SQL File
1. Click on **"Import"** tab at the top
2. Click **"Choose File"** button
3. Select the file: `database/petcare_system_db_complete.sql`
4. Scroll down and click **"Go"** button
5. Wait for the import to complete

### Step 4: Verify the Import
1. Check that all tables are created:
   - ✅ users
   - ✅ pets
   - ✅ health_records
   - ✅ vaccinations
   - ✅ clinic_visits
   - ✅ reminders
   - ✅ care_instructions
   - ✅ reports
   - ✅ notifications
   - ✅ activity_logs

### Step 5: Update Database Configuration
Make sure your `config/db.php` file has the correct database name:
```php
$dbname = "petcare_system_db";
```

## 🔑 Default Sample Users (if you uncomment them in the SQL)

The SQL file includes commented sample user data. To use it:
1. Edit `database/petcare_system_db_complete.sql`
2. Uncomment the INSERT statements at the bottom
3. Re-import or manually run those INSERT queries

Default password for all sample users: `password`

## ✅ Troubleshooting

- **Error: Database already exists**
  - Delete the existing database first, or the SQL will skip creating it

- **Error: Foreign key constraint fails**
  - Make sure you're importing the complete file
  - All tables should be created in the correct order

- **Tables not showing**
  - Refresh phpMyAdmin
  - Check if you selected the correct database


