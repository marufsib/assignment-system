# Supervisor & Assessor Assignment System

A comprehensive web-based system for automatically assigning supervisors and assessors using a lottery-based method. Perfect for educational institutions, training centers, and assessment organizations.

## 🎯 Features

### Core Functionality
- **Lottery-Based Assignment**: Random, fair assignment of supervisors and assessors
- **Admin Configuration**: Customize number of supervisors and assessors per assignment batch
- **Assignment Parameters**:
  - Occupation Type
  - Level (Beginner, Intermediate, Advanced, etc.)
  - Assessment Date (dd-mm-yyyy format)
  - Assessment Center
- **Candidate Management**: Add, edit, delete candidates for assignment
- **View & Filter**: Search and filter assignments by various criteria
- **Report Generation**: Export assignments to Excel format
- **Assignment History**: Track all past assignments

### Admin Features
- **Dashboard**: Overview of all assignments and statistics
- **User Management**: Create and manage admin accounts
- **Assignment Settings**: Configure assignment rules and parameters
- **Report Analytics**: View detailed reports and insights

### User Interface
- **Fully Responsive Design**: Mobile, tablet, and desktop compatible
- **Navigation Bar**: Easy access to all features
- **Clean Dashboard**: Visual representation of data
- **Intuitive Forms**: Simple configuration interface

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Additional Libraries**:
  - PHPExcel/PhpSpreadsheet (Report generation)
  - Chart.js (Dashboard charts)
  - DataTables (Advanced table features)

## 📋 Prerequisites

- Web Server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (for PHP dependencies)

## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/marufsib/assignment-system.git
cd assignment-system
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Database Setup
1. Create a new MySQL database:
   ```sql
   CREATE DATABASE assignment_system;
   ```

2. Import the database schema:
   ```bash
   mysql -u root -p assignment_system < database/schema.sql
   ```

### Step 4: Configure Database Connection
1. Copy `config/config.example.php` to `config/config.php`
2. Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'assignment_system');
   ```

### Step 5: Set Permissions
```bash
chmod -R 755 .
chmod -R 777 uploads/
chmod -R 777 reports/
```

### Step 6: Access the Application
- Local Development: `http://localhost/assignment-system`
- Production: Configure your domain accordingly

## 📖 Usage Guide

### For Admins

#### 1. Login
- Navigate to the login page
- Enter your admin credentials
- Click "Login"

#### 2. Create an Assignment Batch
1. Go to **Dashboard** → **New Assignment**
2. Fill in the form:
   - **Occupation Type**: Select or add new occupation
   - **Level**: Choose assessment level
   - **Assessment Date**: Select date (dd-mm-yyyy)
   - **Assessment Center**: Choose location
3. Specify number of supervisors and assessors needed
4. Click **"Create Assignment"**

#### 3. Add Candidates
1. Go to **Candidates** section
2. Click **"Add Candidate"**
3. Fill in candidate information
4. Click **"Save"**

#### 4. Run Lottery Assignment
1. Navigate to **Assignments** → **Active Assignments**
2. Select an assignment batch
3. Click **"Run Lottery"**
4. Confirm the action
5. System automatically assigns supervisors and assessors

#### 5. View Assignments
1. Go to **View Assignments**
2. Use filters to find specific assignments:
   - By Occupation Type
   - By Level
   - By Assessment Date
   - By Assessment Center
   - By Status
3. Click on any assignment to see details

#### 6. Generate Reports
1. Navigate to **Reports**
2. Select report type:
   - **Assignment Summary**: Overview of all assignments
   - **Supervisor Report**: Assignments by supervisor
   - **Assessor Report**: Assignments by assessor
   - **Center Report**: Assignments by assessment center
3. Choose date range
4. Click **"Generate Report"**
5. Select format (Excel/PDF)
6. Click **"Download"**

#### 7. Manage Users
1. Go to **Settings** → **User Management**
2. View, edit, or delete admin accounts
3. Add new admin users with appropriate permissions

## 📊 Database Schema

### Main Tables
- **users**: Admin user accounts
- **occupations**: Occupation types
- **levels**: Assessment levels
- **assessment_centers**: Assessment center locations
- **candidates**: Candidate information
- **assignments**: Assignment batches
- **assignment_details**: Individual supervisor/assessor assignments
- **reports**: Generated reports log

## 🔐 Security Features

- **Password Hashing**: bcrypt for secure password storage
- **Session Management**: Secure session handling
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization and output encoding
- **CSRF Protection**: Token-based protection
- **Role-Based Access Control**: Admin-only features

## 📱 Responsive Design

The system is fully responsive with breakpoints for:
- **Mobile**: 320px - 767px
- **Tablet**: 768px - 1024px
- **Desktop**: 1025px+

## 📝 API Documentation

### Authentication
- **POST** `/api/auth/login` - User login
- **POST** `/api/auth/logout` - User logout
- **POST** `/api/auth/register` - Register new admin

### Assignments
- **GET** `/api/assignments` - List all assignments
- **POST** `/api/assignments` - Create new assignment
- **GET** `/api/assignments/{id}` - Get assignment details
- **PUT** `/api/assignments/{id}` - Update assignment
- **DELETE** `/api/assignments/{id}` - Delete assignment
- **POST** `/api/assignments/{id}/lottery` - Run lottery

### Candidates
- **GET** `/api/candidates` - List all candidates
- **POST** `/api/candidates` - Add new candidate
- **PUT** `/api/candidates/{id}` - Update candidate
- **DELETE** `/api/candidates/{id}` - Delete candidate

### Reports
- **GET** `/api/reports` - List all reports
- **POST** `/api/reports/generate` - Generate new report
- **GET** `/api/reports/{id}/download` - Download report

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config/config.php`
- Ensure database exists

### Permission Denied Error
- Run: `chmod -R 777 uploads/ reports/`
- Ensure web server has write permissions

### Excel Export Not Working
- Verify PhpSpreadsheet is installed: `composer install`
- Check `reports/` directory permissions

### Lottery Not Running
- Ensure candidates are added
- Check if enough candidates for assignment
- Verify assignment parameters are complete

## 📋 File Structure

```
assignment-system/
├── config/
│   ├── config.php
│   └── database.php
├── database/
│   ├── schema.sql
│   └── seeders/
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── images/
├── src/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── helpers/
├── uploads/
├── reports/
├── vendor/
├── composer.json
└── README.md
```

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 💬 Support

For issues, questions, or suggestions:
- Open an issue on GitHub
- Contact: support@assignmentsystem.com

## 🙏 Acknowledgments

- Built with PHP and MySQL
- Inspired by fair lottery-based assignment systems
- Thanks to all contributors

## 📞 Contact

**Developer**: Maruf Sib
**GitHub**: [@marufsib](https://github.com/marufsib)
**Email**: marufsib@example.com

---

**Last Updated**: May 2026
**Version**: 1.0.0
