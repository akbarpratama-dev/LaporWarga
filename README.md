# LaporWarga - Citizen Reporting Web Application

![LaporWarga](https://img.shields.io/badge/Version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-Native-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green)

Aplikasi web pelaporan warga berbasis PHP Native untuk memudahkan komunikasi antara masyarakat dan pemerintah daerah. Sistem ini memungkinkan warga melaporkan masalah infrastruktur dan pelayanan publik tanpa perlu registrasi akun.

![LaporWarga Screenshot](public/assets/images/screenshot.png)

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Screenshots](#-screenshots)
- [API Documentation](#-api-documentation)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

---

## ✨ Features

### For Citizens (Public)

- 📝 **Submit Reports** - Laporkan masalah tanpa perlu login
- 📸 **Photo Upload** - Upload foto bukti masalah
- 🔍 **Track Status** - Cek status laporan dengan kode unik
- 📊 **Public Reports** - Lihat laporan publik yang sedang diproses
- ✅ **Completed Reports** - Lihat hasil penyelesaian dengan foto before/after
- 📢 **Community Info** - Carousel informasi kegiatan warga
- 📱 **Responsive Design** - Tampilan optimal di semua perangkat

### For Admin

- 🔐 **Secure Login** - Session-based authentication
- 📈 **Dashboard** - Statistik real-time laporan
- 📥 **Manage Reports** - Kelola laporan masuk
- 🔄 **Update Status** - Update status: Diterima → Diproses → Selesai
- 📸 **Before/After Photos** - Upload foto hasil perbaikan
- 💰 **Cost & Duration** - Input biaya dan durasi pengerjaan
- 📢 **Info Management** - Kelola informasi untuk warga

---

## 🛠 Tech Stack

- **Backend**: PHP 7.4+ (Native, no framework)
- **Database**: MySQL 8.0 / MariaDB
- **Frontend**: HTML5, CSS3 (Vanilla)
- **JavaScript**: ES6+ (Vanilla, no framework)
- **Icons**: Remix Icon
- **Server**: Apache (XAMPP/LAMP/MAMP)

---

## 📦 Requirements

- PHP >= 7.4
- MySQL >= 5.7 atau MariaDB >= 10.2
- Apache Web Server
- PDO Extension enabled
- GD Library (for image processing)
- mod_rewrite enabled

**Recommended:**

- XAMPP 8.0+ (Mac/Windows/Linux)
- 2GB RAM minimum
- 100MB storage

---

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/username-anda/LaporWarga.git
cd LaporWarga
```

### 2. Configure Database

**Create Database:**

```sql
CREATE DATABASE LaporWarga2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Import Schema:**

```bash
# Via phpMyAdmin
# - Open http://localhost/phpmyadmin
# - Select database LaporWarga2
# - Import file: database/laporwarga.sql

# Or via command line
mysql -u root -p LaporWarga2 < database/laporwarga.sql
```

### 3. Configure Database Connection

```bash
# Copy template
cp config/database.example.php config/database.php

# Edit config/database.php
nano config/database.php
```

Update credentials:

```php
private $host = 'localhost';
private $db_name = 'LaporWarga2';
private $username = 'root';
private $password = 'YOUR_PASSWORD_HERE'; // Ganti dengan password Anda
```

### 4. Set Permissions (Linux/Mac)

```bash
# Create uploads directory
mkdir -p uploads
chmod 775 uploads

# Set ownership (adjust to your web server user)
sudo chown -R www-data:www-data uploads  # Linux
# or
sudo chown -R _www:_www uploads          # Mac
```

### 5. Start Server

**XAMPP:**

```bash
# Start Apache and MySQL
sudo /Applications/XAMPP/xamppfiles/xampp start

# Or use XAMPP Control Panel
```

**Built-in PHP Server (Development only):**

```bash
cd public
php -S localhost:8000
```

### 6. Access Application

- **Public Site**: http://localhost/LaporWarga1/public/
- **Admin Panel**: http://localhost/LaporWarga1/admin/login.php

**Default Admin Credentials:**

- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change default password after first login!

---

## 💡 Usage

### For Citizens

1. **Submit Report**

   - Scroll to "Laporkan Masalah" section
   - Fill in: Name, Phone, Category, Location, Description
   - Upload photo (max 2MB, JPG/PNG)
   - Click "Kirim Laporan"
   - **Save your Report Code!** (displayed for 20 seconds)

2. **Check Status**

   - Click "Cek Status" in navbar
   - Enter Report Code and Phone Number
   - View detailed status and timeline

3. **View Public Reports**

   - Scroll to "Laporan Publik"
   - See all ongoing reports (Diterima/Diproses)

4. **View Completed Reports**
   - Scroll to "Laporan Terselesaikan"
   - See results with before/after photos, cost, and duration

### For Admin

1. **Login**

   - Go to `/admin/login.php`
   - Enter credentials
   - Access dashboard

2. **Manage Incoming Reports**

   - Dashboard → "Laporan Masuk"
   - Click "Update" on a report
   - Change status to "Diproses" or "Selesai"
   - For "Selesai": Add completion notes, cost, duration, and after photo
   - Click "Update Laporan"

3. **Manage Community Info**

   - Dashboard → "Info Warga"
   - Add new info with category (Info/Pemberitahuan)
   - Fill in title, location, description, dates
   - Info will appear in public carousel

4. **View Statistics**
   - Dashboard shows:
     - Total reports
     - Reports by status
     - Recent activity

---

## 📁 Project Structure

```
LaporWarga1/
│
├── config/
│   ├── database.php              # Database connection (PDO)
│   └── database.example.php      # Database template
│
├── public/
│   ├── index.php                 # Homepage (hero, form, reports)
│   ├── cek_status.php            # Check report status
│   ├── detail_laporan.php        # Report detail with timeline
│   ├── detail_info.php           # Info detail page
│   ├── image.php                 # Serve images from database
│   └── assets/
│       ├── css/
│       │   └── style.css         # Main stylesheet (2000+ lines)
│       ├── js/
│       │   └── script.js         # JavaScript functions
│       └── images/
│           └── asset1.png        # Hero image
│
├── admin/
│   ├── login.php                 # Admin login page
│   ├── logout.php                # Logout handler
│   ├── dashboard.php             # Admin dashboard
│   ├── laporan_masuk.php         # Incoming reports
│   ├── laporan_selesai.php       # Completed reports
│   ├── update_laporan.php        # Update report form
│   └── info_warga.php            # Manage info warga (CRUD)
│
├── process/
│   ├── submit_laporan.php        # Handle report submission
│   ├── cek_status_process.php    # Handle status check
│   ├── admin_login_process.php   # Handle admin login
│   ├── update_status_process.php # Handle report updates
│   └── upload_info_process.php   # Handle info creation
│
├── database/
│   ├── laporwarga.sql            # Full database schema + sample data
│   ├── install.sql               # Quick installation script
│   └── migration_*.sql           # Database migrations
│
├── uploads/                      # (Not used - images stored in DB)
│
├── .htaccess                     # Apache configuration
├── .gitignore                    # Git ignore rules
├── README.md                     # This file
└── create_admin.php              # Script to create/reset admin user
```

---

## 🗄 Database Schema

### Table: `admin`

| Column   | Type         | Description     |
| -------- | ------------ | --------------- |
| id       | INT (PK)     | Admin ID        |
| username | VARCHAR(50)  | Login username  |
| password | VARCHAR(255) | Hashed password |
| nama     | VARCHAR(100) | Admin name      |

### Table: `laporan`

| Column          | Type          | Description                  |
| --------------- | ------------- | ---------------------------- |
| id              | INT (PK)      | Report ID                    |
| kode            | VARCHAR(20)   | Unique report code           |
| nama_pelapor    | VARCHAR(100)  | Reporter name                |
| no_hp           | VARCHAR(15)   | Phone number                 |
| kategori        | VARCHAR(50)   | Category                     |
| deskripsi       | TEXT          | Description                  |
| lokasi          | VARCHAR(255)  | Location                     |
| foto_blob       | MEDIUMBLOB    | Photo (before) binary data   |
| foto_mime       | VARCHAR(50)   | Photo MIME type              |
| status          | ENUM          | Diterima/Diproses/Selesai    |
| tanggal_lapor   | DATETIME      | Report date                  |
| diterima_at     | DATETIME      | Accepted timestamp           |
| diproses_at     | DATETIME      | Processing started timestamp |
| tanggal_selesai | DATETIME      | Completion date              |
| selesai_at      | DATETIME      | Completion timestamp         |
| catatan_admin   | TEXT          | Admin notes                  |
| biaya           | DECIMAL(15,2) | Cost (Rupiah)                |
| durasi          | VARCHAR(50)   | Duration                     |
| foto_after_blob | MEDIUMBLOB    | Photo (after) binary data    |
| foto_after_mime | VARCHAR(50)   | After photo MIME type        |

### Table: `info_warga`

| Column          | Type         | Description         |
| --------------- | ------------ | ------------------- |
| id              | INT (PK)     | Info ID             |
| judul           | VARCHAR(200) | Title               |
| deskripsi       | TEXT         | Description         |
| lokasi          | VARCHAR(255) | Location            |
| kategori        | ENUM         | Info/Pemberitahuan  |
| tanggal_mulai   | DATE         | Start date          |
| tanggal_selesai | DATE         | End date (nullable) |
| created_at      | TIMESTAMP    | Created timestamp   |

---

## 📸 Screenshots

### Public Pages

**Homepage - Hero Section**

```
[Hero with title, description, and CTA buttons]
```

**Process Flow**

```
[5-step process visualization with icons]
```

**Community Info Carousel**

```
[Minimalist white cards with Info/Pemberitahuan badges]
```

**Submit Report Form**

```
[Clean form with photo upload]
```

**Completed Reports Gallery**

```
[Grid of cards showing before/after, cost, duration]
```

### Admin Panel

**Dashboard**

```
[Statistics cards + recent reports table]
```

**Report Management**

```
[Update form with status selector, photo upload, cost/duration inputs]
```

**Info Management**

```
[CRUD interface with category selector]
```

---

## 🔌 API Documentation

### Image Serving Endpoint

**GET** `/public/image.php`

Retrieve images stored in database.

**Parameters:**

- `id` (required): Report ID
- `type` (optional): `foto` (default) or `foto_after`

**Example:**

```html
<img src="image.php?id=123&type=foto" alt="Report Photo" /> <img src="image.php?id=123&type=foto_after" alt="After Photo" />
```

**Response:**

- Success: Binary image data with proper MIME headers
- Error 404: Image not found

---

## 🎨 Design System

### Colors

```css
--primary-color: rgb(26, 42, 74); /* Dark Blue - Trust & Stability */
--secondary-color: rgb(211, 47, 47); /* Deep Red - Action & Urgency */
--background: #ffffff;
--background-alt: #f9fafb;
--text-dark: #1f2937;
--text-muted: #6b7280;
```

### Typography

- Font: Poppins (Google Fonts)
- Headings: 600-700 weight
- Body: 400 weight
- Line height: 1.6-1.8

### Components

- **Buttons**: Rounded 8-12px, shadow-md, hover states
- **Cards**: Rounded-xl, shadow-sm to shadow-lg
- **Forms**: Focus ring blue, rounded inputs
- **Badges**: Rounded-full, color-coded by status

### Animations

- Hero section: Fade-in from bottom (staggered)
- Sections: Scroll-triggered fade-in
- Process flow: Sequential fade-in
- Hover: Smooth transitions (0.2s ease)

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. **Commit your changes**
   ```bash
   git commit -m 'Add some AmazingFeature'
   ```
4. **Push to the branch**
   ```bash
   git push origin feature/AmazingFeature
   ```
5. **Open a Pull Request**

### Coding Standards

- Follow PSR-12 coding style (PHP)
- Use meaningful variable names
- Add comments for complex logic
- Write commit messages in English
- Test before submitting PR

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2024 LaporWarga

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software...
```

---

## 👨‍💻 Author

**Your Name**

- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com
- LinkedIn: [Your Name](https://linkedin.com/in/yourprofile)

---

## 🙏 Acknowledgments

- **Remix Icon** for beautiful icons
- **Google Fonts** for Poppins typeface
- **PHP Community** for excellent documentation
- **Stack Overflow** for problem-solving support

---

## 📞 Support

Jika Anda mengalami masalah atau memiliki pertanyaan:

1. **Check existing issues**: [GitHub Issues](https://github.com/username/LaporWarga/issues)
2. **Create new issue**: Describe your problem with:
   - PHP/MySQL version
   - Error message
   - Steps to reproduce
3. **Email**: support@laporwarga.com

---

## 🗺 Roadmap

- [ ] Email notification for status updates
- [ ] SMS notification via Twilio
- [ ] Multi-language support (ID/EN)
- [ ] Mobile app (Flutter)
- [ ] Export reports to PDF/Excel
- [ ] Real-time chat with admin
- [ ] Voting system for public reports
- [ ] Geolocation integration
- [ ] Push notifications
- [ ] API for third-party integration

---

## 📊 Project Stats

![GitHub repo size](https://img.shields.io/github/repo-size/username/LaporWarga)
![GitHub last commit](https://img.shields.io/github/last-commit/username/LaporWarga)
![GitHub issues](https://img.shields.io/github/issues/username/LaporWarga)
![GitHub pull requests](https://img.shields.io/github/issues-pr/username/LaporWarga)

---

<div align="center">

**Made with ❤️ for better citizen engagement**

[⬆ back to top](#laporwarga---citizen-reporting-web-application)

</div>
