# LaporWarga - Citizen Reporting Web Application

![LaporWarga](https://img.shields.io/badge/Version-2.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-Native-777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green)

Aplikasi web pelaporan warga berbasis PHP Native untuk memudahkan komunikasi antara masyarakat dan pemerintah daerah. Sistem ini memungkinkan warga melaporkan masalah infrastruktur dan pelayanan publik tanpa perlu registrasi akun, dengan visualisasi data real-time dan manajemen laporan yang komprehensif.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Design System](#-design-system)
- [API Documentation](#-api-documentation)
- [Security Features](#-security-features)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### For Citizens (Public)

- 📝 **Submit Reports Without Login** - Laporkan masalah tanpa perlu registrasi akun
- 📸 **Photo Upload to Database** - Upload foto bukti masalah (disimpan di database, max 2MB)
- 🔍 **Real-time Status Tracking** - Cek status laporan dengan kode unik dan nomor HP
- 📊 **Public Reports Dashboard** - Lihat laporan publik yang sedang diproses (Diterima/Diproses)
- ✅ **Completed Reports Gallery** - Lihat hasil penyelesaian dengan foto before/after, biaya, dan durasi
- 📢 **Community Info Carousel** - Carousel informasi kegiatan warga dengan kategori (Info/Pemberitahuan)
- ⏱️ **Status Timeline** - Timeline dengan timestamp kapan laporan diterima, diproses, dan selesai
- 📱 **Fully Responsive Design** - Tampilan optimal di mobile, tablet, dan desktop
- 🎨 **Smooth Animations** - Fade-in animations pada hero section dan scroll-triggered sections
- 🔄 **Auto-refresh Status** - Alert sukses dengan countdown timer 20 detik
- 🤖 **AI Chatbot Assistant** - Interactive FAQ chatbot powered by OpenAI untuk membantu navigasi
- 🔮 **Vision AI Description Generator** - Optional AI-powered report description from uploaded photos

### For Admin

- 🔐 **Secure Session-based Login** - Authentication dengan bcrypt password hashing
- 📊 **Advanced Data Visualization Dashboard**:
  - Weekly reports bar chart (4 minggu terakhir)
  - Category distribution donut chart
  - Monthly trend chart (6 bulan terakhir)
  - Completion rate metrics
  - Average response time analytics
- 📥 **Comprehensive Report Management** - Kelola laporan masuk dengan filter dan search
- 🔄 **Status Update System** - Update status: Diterima → Diproses → Selesai dengan timestamp tracking
- 📸 **Before/After Photo Management** - Upload dan preview foto hasil perbaikan
- 💰 **Cost & Duration Tracking** - Input biaya perbaikan dan durasi pengerjaan
- 📢 **Info Warga CRUD** - Kelola informasi untuk warga dengan kategori
- 📈 **Real-time Statistics** - Dashboard dengan metrik real-time dari database
- 🎯 **Responsive Admin Panel** - Sidebar navigation dengan active state indicator

---

## 🛠 Tech Stack

- **Backend**: PHP 7.4+ (Native, no framework)
- **Database**: MySQL 8.0 / MariaDB with PDO
- **Frontend**: HTML5, CSS3 (Vanilla, custom design system)
- **JavaScript**: ES6+ (Vanilla, no framework) - Intersection Observer API, DOM manipulation
- **Icons**: Remix Icon 3.5.0
- **Charts**: Custom CSS-based charts (no external library)
- **Server**: Apache (XAMPP/LAMP/MAMP)
- **Security**: Prepared statements, bcrypt hashing, session management

---

## 📦 Requirements

- PHP >= 7.4
- MySQL >= 5.7 atau MariaDB >= 10.2
- Apache Web Server with mod_rewrite
- PDO Extension enabled
- GD Library (for image processing)
- mbstring Extension

**Recommended:**

- XAMPP 8.0+ (Mac/Windows/Linux)
- 4GB RAM minimum
- 500MB storage (for database with images)

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

**Run Migrations (Automatic):**

- Timestamp columns akan otomatis dibuat saat pertama kali submit/update laporan
- Atau manual: `http://localhost/LaporWarga1/run_migration.php`

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

### 4. Create Admin User

```bash
# Akses script untuk membuat/reset admin
http://localhost/LaporWarga1/create_admin.php
```

Atau manual via SQL:

```sql
INSERT INTO admin (username, password, nama)
VALUES ('admin', '$2y$10$...bcrypt_hash...', 'Administrator');
```

### 5. Configure AI Features (Optional)

**AI Chatbot Setup:**

```bash
# Copy chatbot configuration template
cp config/chatbot.example.php config/chatbot.php

# Edit and add your OpenAI API key
nano config/chatbot.php
```

**Vision AI Setup:**

```bash
# Copy Vision AI configuration template
cp config/vision.example.php config/vision.php

# Edit and add your OpenAI API key
nano config/vision.php
```

Update API keys in both files:

```php
'openai_api_key' => 'sk-proj-YOUR_OPENAI_API_KEY_HERE',
```

> 📖 See full documentation:
> - [Chatbot Documentation](docs/CHATBOT.md)
> - [Vision AI Documentation](docs/VISION_AI.md)

### 6. Start Server

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

   - Scroll to "Laporkan Masalah" section atau klik tombol "Laporkan Masalah" di hero
   - Fill in: Nama, Nomor HP, Kategori, Lokasi, Deskripsi
   - Upload foto (max 2MB, JPG/PNG) - disimpan langsung di database
   - Click "Kirim Laporan"
   - **PENTING: Salin kode laporan Anda!** (ditampilkan selama 20 detik)

2. **Check Status**

   - Click "Cek Status" di navbar atau hero section
   - Enter Kode Laporan dan Nomor HP
   - Lihat timeline status dengan timestamp:
     - Kapan laporan diterima
     - Kapan mulai diproses
     - Kapan selesai dikerjakan

3. **View Public Reports**

   - Scroll ke section "Laporan Publik"
   - Lihat semua laporan ongoing (Diterima/Diproses)
   - Tabel dengan kategori, lokasi, tanggal, dan status badge

4. **View Completed Reports**

   - Scroll ke "Laporan Terselesaikan"
   - Grid card dengan:
     - Foto before/after
     - Hasil pekerjaan
     - Biaya perbaikan (Rp)
     - Durasi pengerjaan
     - Link "Lihat Detail" untuk info lengkap

5. **Read Community Info**
   - Carousel "Informasi Kegiatan Warga"
   - Badge kategori: Info (biru) / Pemberitahuan (merah)
   - Auto-slide setiap 6 detik
   - Click "Lihat Selengkapnya" untuk detail

### For Admin

1. **Login**

   - Go to `/admin/login.php`
   - Enter username dan password
   - Session aktif selama browser terbuka

2. **Dashboard Overview**

   - **Metric Cards**:
     - Rata-rata waktu penyelesaian
     - Tercepat/terlama
     - Tingkat penyelesaian
   - **Bar Chart**: Laporan per minggu (4 minggu)
   - **Donut Chart**: Distribusi per kategori
   - **Trend Chart**: Tren 6 bulan terakhir
   - **Progress Bars**: Tingkat respon dan penyelesaian

3. **Manage Incoming Reports**

   - Dashboard → "Laporan Masuk"
   - Lihat semua laporan dengan status Diterima/Diproses
   - Click "Update" untuk mengubah status
   - Form update:
     - Pilih status baru
     - Tambah keterangan (catatan admin)
     - Jika "Selesai": upload foto after, input biaya & durasi
   - Timestamp otomatis tercatat saat status berubah

4. **Manage Community Info**

   - Dashboard → "Info Warga"
   - **Create**: Tambah info baru dengan kategori (Info/Pemberitahuan)
   - **Read**: Lihat daftar semua info dalam tabel
   - **Update**: Edit info yang sudah ada
   - **Delete**: Hapus info (dengan konfirmasi)

5. **View Statistics & Analytics**
   - Dashboard menampilkan:
     - Total laporan
     - Breakdown per status
     - Kategori paling banyak dilaporkan
     - Tren bulanan/mingguan
     - Rata-rata waktu penyelesaian

---

## 📁 Project Structure

```
LaporWarga1/
│
├── config/
│   ├── database.php              # Database connection (PDO) - EXCLUDED FROM GIT
│   └── database.example.php      # Database template untuk clone
│
├── public/
│   ├── index.php                 # Homepage (hero, flow, form, carousel, reports)
│   ├── cek_status.php            # Check report status dengan timeline
│   ├── detail_laporan.php        # Report detail dengan timestamp timeline
│   ├── detail_info.php           # Info warga detail page
│   ├── image.php                 # Serve images from database BLOB
│   └── assets/
│       ├── css/
│       │   └── style.css         # Main stylesheet (2500+ lines, design system)
│       ├── js/
│       │   └── script.js         # Vanilla JS (carousel, animations, scroll spy)
│       └── images/
│           └── asset1.png        # Hero section image
│
├── admin/
│   ├── .htaccess                 # Redirect to login, disable directory listing
│   ├── login.php                 # Admin login page
│   ├── logout.php                # Session destroy handler
│   ├── dashboard.php             # Dashboard dengan visualisasi data (charts)
│   ├── laporan_masuk.php         # Incoming reports table
│   ├── laporan_selesai.php       # Completed reports table
│   ├── update_laporan.php        # Update report form (status, foto, biaya, durasi)
│   └── info_warga.php            # CRUD Info Warga dengan kategori
│
├── process/
│   ├── submit_laporan.php        # Handle report submission + auto-migration
│   ├── cek_status_process.php    # Handle status check (deprecated, inline now)
│   ├── admin_login_process.php   # Handle admin authentication
│   ├── update_status_process.php # Handle report updates + timestamp tracking
│   └── upload_info_process.php   # Handle info creation + kategori
│
├── database/
│   ├── laporwarga.sql            # Full database schema + sample data
│   ├── install.sql               # Quick installation script
│   └── migration_add_status_timestamps.sql  # Migration untuk timestamp columns
│
├── uploads/                      # Folder tidak digunakan (foto di database)
│
├── .htaccess                     # Apache rewrite rules
├── .gitignore                    # Exclude database.php, uploads, IDE files
├── README.md                     # This comprehensive documentation
├── create_admin.php              # Script to create/reset admin user
└── run_migration.php             # Manual migration runner
```

---

## 🗄 Database Schema

### Table: `admin`

| Column   | Type         | Description               |
| -------- | ------------ | ------------------------- |
| id       | INT (PK)     | Admin ID (auto increment) |
| username | VARCHAR(50)  | Login username (unique)   |
| password | VARCHAR(255) | Bcrypt hashed password    |
| nama     | VARCHAR(100) | Admin display name        |

### Table: `laporan`

| Column          | Type          | Description                               |
| --------------- | ------------- | ----------------------------------------- |
| id              | INT (PK)      | Report ID (auto increment)                |
| kode            | VARCHAR(20)   | Unique report code (LAP-YYYYMMDD####)     |
| nama_pelapor    | VARCHAR(100)  | Reporter name                             |
| no_hp           | VARCHAR(15)   | Phone number (for verification)           |
| kategori        | VARCHAR(50)   | Category (Infrastruktur, Kebersihan, dll) |
| deskripsi       | TEXT          | Problem description                       |
| lokasi          | VARCHAR(255)  | Location                                  |
| foto_blob       | MEDIUMBLOB    | Photo (before) binary data                |
| foto_mime       | VARCHAR(50)   | Photo MIME type (image/jpeg, image/png)   |
| status          | ENUM          | Diterima, Diproses, Selesai               |
| tanggal_lapor   | DATETIME      | Report submission date                    |
| diterima_at     | DATETIME      | **NEW:** Timestamp saat status Diterima   |
| diproses_at     | DATETIME      | **NEW:** Timestamp saat status Diproses   |
| tanggal_selesai | DATETIME      | Completion date                           |
| selesai_at      | DATETIME      | **NEW:** Timestamp saat status Selesai    |
| catatan_admin   | TEXT          | Admin completion notes                    |
| biaya           | DECIMAL(15,2) | Repair cost (Rupiah)                      |
| durasi          | VARCHAR(50)   | Duration (e.g., "3 hari", "1 minggu")     |
| foto_after_blob | MEDIUMBLOB    | Photo (after) binary data                 |
| foto_after_mime | VARCHAR(50)   | After photo MIME type                     |

### Table: `info_warga`

| Column          | Type         | Description                   |
| --------------- | ------------ | ----------------------------- |
| id              | INT (PK)     | Info ID (auto increment)      |
| judul           | VARCHAR(200) | Title                         |
| deskripsi       | TEXT         | Full description              |
| lokasi          | VARCHAR(255) | Location                      |
| kategori        | ENUM         | **NEW:** Info / Pemberitahuan |
| tanggal_mulai   | DATE         | Start date                    |
| tanggal_selesai | DATE         | End date (nullable)           |
| created_at      | TIMESTAMP    | Created timestamp (auto)      |

**Indexes:**

- `laporan.kode` - UNIQUE index untuk fast lookup
- `laporan.status` - INDEX untuk filtering
- `laporan.tanggal_lapor` - INDEX untuk sorting
- `info_warga.created_at` - INDEX untuk ordering

---

## 🎨 Design System

### Color Palette

```css
/* Primary - Trust & Stability */
--primary-color: rgb(26, 42, 74); /* Dark Navy Blue */
--primary-light: rgba(26, 42, 74, 0.1);

/* Secondary - Action & Urgency */
--secondary-color: rgb(211, 47, 47); /* Deep Red */
--secondary-light: rgba(211, 47, 47, 0.1);

/* Accent */
--accent-color: #ef5350; /* Light Red */

/* Backgrounds */
--background: #ffffff;
--background-alt: #f9fafb; /* Light Gray */
--background-dark: #1f2937;

/* Text */
--text-dark: #1f2937;
--text-muted: #6b7280;
--text-light: #9ca3af;

/* Status Colors */
--status-diterima: rgb(26, 42, 74); /* Blue */
--status-diproses: rgb(211, 47, 47); /* Red */
--status-selesai: #22c55e; /* Green */
```

### Typography

```css
/* Font Family */
font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;

/* Font Weights */
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;

/* Font Sizes */
--text-xs: 0.75rem; /* 12px */
--text-sm: 0.875rem; /* 14px */
--text-base: 1rem; /* 16px */
--text-lg: 1.125rem; /* 18px */
--text-xl: 1.25rem; /* 20px */
--text-2xl: 1.5rem; /* 24px */
--text-3xl: 1.875rem; /* 30px */
--text-4xl: 2.25rem; /* 36px */

/* Line Heights */
--leading-tight: 1.25;
--leading-normal: 1.5;
--leading-relaxed: 1.625;
--leading-loose: 2;
```

### Spacing Scale

```css
--spacing-xs: 0.25rem; /* 4px */
--spacing-sm: 0.5rem; /* 8px */
--spacing-md: 1rem; /* 16px */
--spacing-lg: 1.5rem; /* 24px */
--spacing-xl: 2rem; /* 32px */
--spacing-2xl: 3rem; /* 48px */
--spacing-3xl: 4rem; /* 64px */
```

### Shadows

```css
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
```

### Border Radius

```css
--radius-sm: 0.375rem; /* 6px */
--radius-md: 0.5rem; /* 8px */
--radius-lg: 0.75rem; /* 12px */
--radius-xl: 1rem; /* 16px */
--radius-full: 9999px; /* Fully rounded */
```

### Component Styles

**Buttons:**

```css
.btn {
  padding: 0.75rem 1.5rem;
  border-radius: var(--radius-lg);
  font-weight: var(--font-semibold);
  transition: all 0.2s ease;
  box-shadow: var(--shadow-md);
}

.btn-primary {
  background: var(--primary-color);
  color: white;
}

.btn-secondary {
  background: var(--secondary-color);
  color: white;
}

.btn-outline {
  border: 2px solid var(--primary-color);
  color: var(--primary-color);
  background: transparent;
}

.btn:hover {
  opacity: 0.9;
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}
```

**Cards:**

```css
.card {
  background: white;
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  transition: all 0.3s ease;
}

.card:hover {
  box-shadow: var(--shadow-xl);
  transform: translateY(-4px);
}

.card-header {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
}

.card-body {
  padding: 1.25rem;
}
```

**Badges:**

```css
.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  font-size: var(--text-sm);
  font-weight: var(--font-semibold);
  border-radius: var(--radius-full);
}

.badge-diterima {
  background: var(--status-diterima);
  color: white;
}

.badge-diproses {
  background: var(--status-diproses);
  color: white;
}

.badge-selesai {
  background: var(--status-selesai);
  color: white;
}
```

### Animations

**Fade In (Hero Section):**

```css
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-title {
  animation: fadeInUp 0.8s ease-out;
}

.hero-description {
  animation: fadeInUp 0.8s ease-out 0.2s backwards;
}

.hero-actions {
  animation: fadeInUp 0.8s ease-out 0.4s backwards;
}
```

**Scroll Triggered Animations:**

```css
.fade-in-section {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.fade-in-section.visible {
  opacity: 1;
  transform: translateY(0);
}
```

**Process Flow Sequential:**

```css
.process-item {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 0.6s ease-out forwards;
}

.process-item:nth-child(1) {
  animation-delay: 0s;
}
.process-item:nth-child(2) {
  animation-delay: 0.1s;
}
.process-item:nth-child(3) {
  animation-delay: 0.2s;
}
.process-item:nth-child(4) {
  animation-delay: 0.3s;
}
.process-item:nth-child(5) {
  animation-delay: 0.4s;
}
```

---

## 🔌 API Documentation

### Image Serving Endpoint

**GET** `/public/image.php`

Retrieve images stored in database as BLOB.

**Parameters:**

- `id` (required): Report ID
- `type` (optional): `foto` (default) or `foto_after`

**Example:**

```html
<img src="image.php?id=123&type=foto" alt="Report Photo" /> <img src="image.php?id=123&type=foto_after" alt="After Photo" />
```

**Response:**

- **Success (200)**: Binary image data dengan proper MIME type headers
  ```
  Content-Type: image/jpeg
  Content-Length: 123456
  Cache-Control: public, max-age=86400
  ```
- **Not Found (404)**: Gambar tidak ditemukan
- **Error (500)**: Database error

**Caching:**

- Browser cache: 1 hari (86400 detik)
- Server-side: Query dari database setiap request (bisa di-optimize dengan Redis/Memcached)

---

## 🔒 Security Features

### Authentication & Authorization

- **Session-based Authentication**: PHP native sessions untuk admin
- **Password Hashing**: bcrypt (`PASSWORD_DEFAULT`) untuk password admin
- **Session Timeout**: Logout otomatis saat browser ditutup
- **Protected Routes**: Semua halaman admin cek `$_SESSION['admin_id']`
- **CSRF Protection**: Method check (POST only) untuk form submission

### AI Chatbot Security

- **API Key Protection**: OpenAI API key disimpan di `config/chatbot.php` (not in public folder)
- **Rate Limiting**: 10 pesan per 5 menit per session untuk mencegah spam
- **Input Validation**: Max 500 karakter, sanitasi HTML entities
- **Scope Restriction**: System prompt membatasi chatbot hanya menjawab FAQ LaporWarga
- **No Data Access**: Chatbot tidak bisa akses/ubah data database
- **Error Handling**: Tidak expose internal error messages ke user

### Vision AI Security

- **API Key Protection**: OpenAI API key disimpan di `config/vision.php` (gitignored)
- **Manual Trigger Only**: Vision AI hanya aktif saat user klik button (no auto-submit)
- **File Validation**: Strict file type (JPG, PNG) dan size (max 5MB) validation
- **Output Sanitization**: HTML encoding dengan `htmlspecialchars()` untuk mencegah XSS
- **Cost Control**: One request per photo, low max tokens (100), manual trigger
- **Scope Limitation**: System prompt membatasi AI hanya deskripsi visual netral
- **No Auto-submit**: AI result masuk textarea, user wajib review dan submit manual

### Input Validation & Sanitization

```php
// Prepared Statements (PDO)
$stmt = $conn->prepare("SELECT * FROM laporan WHERE kode = :kode AND no_hp = :hp");
$stmt->execute([':kode' => $kode, ':hp' => $no_hp]);

// File Upload Validation
$allowed_types = ['image/jpeg', 'image/png'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!in_array($_FILES['foto']['type'], $allowed_types)) {
    die('Invalid file type');
}

if ($_FILES['foto']['size'] > $max_size) {
    die('File too large');
}

// HTML Output Escaping
echo htmlspecialchars($laporan['nama_pelapor'], ENT_QUOTES, 'UTF-8');
```

### Database Security

- **PDO Prepared Statements**: Semua query menggunakan prepared statements
- **No Direct User Input**: Semua input di-bind dengan parameter
- **Database User Privileges**: Gunakan user dengan minimal privileges (SELECT, INSERT, UPDATE only)
- **Connection Encryption**: Gunakan SSL untuk production

### File Upload Security

- **MIME Type Validation**: Cek `$_FILES['foto']['type']`
- **File Size Limit**: Max 2MB
- **Binary Storage**: Foto disimpan sebagai BLOB di database (tidak di filesystem)
- **No Direct File Access**: Gambar hanya bisa diakses via [`image.php`](image.php) dengan validasi ID

### XSS Prevention

```php
// Escape output
<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?>

// Safe innerHTML (if needed)
DOMPurify.sanitize(userInput);
```

### SQL Injection Prevention

```php
// ❌ BAD (vulnerable)
$query = "SELECT * FROM laporan WHERE kode = '$kode'";

// ✅ GOOD (safe)
$stmt = $conn->prepare("SELECT * FROM laporan WHERE kode = :kode");
$stmt->execute([':kode' => $kode]);
```

### Best Practices Implemented

- ✅ `.htaccess` untuk admin directory protection
- ✅ `config/database.php` excluded dari Git
- ✅ Error logging (bukan display di production)
- ✅ HTTPS recommended untuk production
- ✅ Regular security updates

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

### How to Contribute

1. **Fork the repository**

   ```bash
   git fork https://github.com/username/LaporWarga.git
   ```

2. **Create a feature branch**

   ```bash
   git checkout -b feature/AmazingFeature
   ```

3. **Make your changes**

   - Write clean, readable code
   - Follow existing code style
   - Add comments for complex logic

4. **Test thoroughly**

   - Test di berbagai browser (Chrome, Firefox, Safari)
   - Test responsive di mobile/tablet
   - Test semua fitur (submit, update, delete)

5. **Commit with clear messages**

   ```bash
   git commit -m "Add feature: Email notification for status updates"
   ```

6. **Push to your fork**

   ```bash
   git push origin feature/AmazingFeature
   ```

7. **Open a Pull Request**
   - Describe your changes clearly
   - Reference any related issues
   - Include screenshots if UI changes

### Coding Standards

**PHP:**

- Follow PSR-12 coding style
- Use meaningful variable names (`$reportData` not `$rd`)
- Add PHPDoc comments for functions
- Use type hints when possible

**JavaScript:**

- Use ES6+ syntax
- Avoid `var`, use `const`/`let`
- Add JSDoc comments
- Use semicolons consistently

**CSS:**

- Use BEM naming convention where applicable
- Group related properties
- Use CSS variables for colors/spacing
- Mobile-first responsive design

**Commit Messages:**

- Use present tense ("Add feature" not "Added feature")
- Capitalize first letter
- Keep first line under 50 characters
- Add detailed description if needed

**Example:**

```
Add email notification feature

- Implement PHPMailer integration
- Add email template for status updates
- Update admin dashboard with email settings
- Add database field for notification preferences

Closes #42
```

---

## 📝 License

This project is licensed under the MIT License.

```
MIT License

Copyright (c) 2024 LaporWarga Contributors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 👨‍💻 Author & Contributors

**Main Developer:**

- **Your Name**
  - GitHub: [@yourusername](https://github.com/yourusername)
  - Email: your.email@example.com
  - LinkedIn: [Your Name](https://linkedin.com/in/yourprofile)

**Contributors:**

- View all contributors: [Contributors List](https://github.com/username/LaporWarga/graphs/contributors)

---

## 🙏 Acknowledgments

- **Remix Icon** - Beautiful open-source icon library
- **Google Fonts (Poppins)** - Clean, modern typeface
- **PHP Community** - Excellent documentation and support
- **Stack Overflow** - Problem-solving and learning resource
- **GitHub Community** - Version control and collaboration platform
- **XAMPP Team** - Easy local development environment

---

## 📞 Support & Contact

### Get Help

**Found a bug?**

1. Check [existing issues](https://github.com/username/LaporWarga/issues)
2. Create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots if applicable
   - Environment details (PHP version, MySQL version, OS)

**Have a question?**

- Open a [Discussion](https://github.com/username/LaporWarga/discussions)
- Email: support@laporwarga.com

**Security vulnerability?**

- **DO NOT** open a public issue
- Email: security@laporwarga.com
- We'll respond within 48 hours

---

## 🗺 Roadmap

### Version 2.1 (Q1 2025)

- [ ] Email notification untuk update status
- [ ] Export laporan ke PDF
- [ ] Dashboard analytics yang lebih advanced
- [ ] Multi-user admin dengan roles
- [ ] Activity log untuk audit trail

### Version 2.2 (Q2 2025)

- [ ] SMS notification via Twilio/Vonage
- [ ] Push notifications (PWA)
- [ ] Real-time chat admin-warga
- [ ] Voting system untuk laporan publik
- [ ] Geolocation integration dengan maps

### Version 3.0 (Q3 2025)

- [ ] Multi-language support (ID/EN)
- [ ] Mobile app (Flutter/React Native)
- [ ] RESTful API untuk third-party integration
- [ ] GraphQL endpoint
- [ ] WebSocket untuk real-time updates

### Future Considerations

- [ ] AI-powered category detection
- [ ] Sentiment analysis untuk prioritas laporan
- [ ] Blockchain untuk transparency
- [ ] IoT sensor integration
- [ ] AR untuk visualisasi masalah infrastruktur

---

## 📊 Project Statistics

![GitHub repo size](https://img.shields.io/github/repo-size/username/LaporWarga)
![GitHub last commit](https://img.shields.io/github/last-commit/username/LaporWarga)
![GitHub issues](https://img.shields.io/github/issues/username/LaporWarga)
![GitHub pull requests](https://img.shields.io/github/issues-pr/username/LaporWarga)
![GitHub stars](https://img.shields.io/github/stars/username/LaporWarga)
![GitHub forks](https://img.shields.io/github/forks/username/LaporWarga)
![GitHub watchers](https://img.shields.io/github/watchers/username/LaporWarga)

**Code Statistics:**

- Total Lines: ~15,000+
- PHP: ~8,000 lines
- CSS: ~2,500 lines
- JavaScript: ~1,500 lines
- SQL: ~500 lines
- Documentation: ~3,000 lines

**Database:**

- Tables: 3 (admin, laporan, info_warga)
- Average Report Size: ~150KB (dengan foto)
- Estimated Capacity: 10,000+ reports per database

---

## 🎓 Academic Note

Project ini dibuat sebagai tugas akhir semester 3 Informatika dengan fokus pada:

- **Database Design**: Normalisasi, indexing, relationship
- **Web Development**: Full-stack PHP Native development
- **UI/UX Design**: User-centered design, responsive layout
- **Security**: Input validation, SQL injection prevention, authentication
- **Data Visualization**: Charts dan analytics untuk decision making

**Learning Outcomes:**

- ✅ Memahami konsep MVC sederhana
- ✅ Implementasi CRUD operations
- ✅ Keamanan aplikasi web
- ✅ Responsive web design
- ✅ Data visualization
- ✅ Version control dengan Git

---

<div align="center">

## 💙 Made with Love for Better Citizen Engagement

**LaporWarga** - Connecting Citizens with Government

[⬆ Back to Top](#laporwarga---citizen-reporting-web-application)

---

**Star ⭐ this repository if you find it helpful!**

</div>
