# Vision AI - Deskripsi Laporan Otomatis

## 📋 Deskripsi

Fitur Vision AI membantu warga menghasilkan deskripsi laporan secara otomatis berdasarkan foto yang diunggah menggunakan OpenAI Vision API.

**PENTING**: Vision AI adalah fitur **opsional** yang membantu menyusun deskripsi. Warga tetap dapat mengedit atau menulis deskripsi manual.

## 🎯 Tujuan Fitur

- Memudahkan warga yang kesulitan menulis deskripsi
- Menghasilkan deskripsi netral dan faktual dari foto
- Mempercepat proses pelaporan
- Meningkatkan kualitas deskripsi laporan

## 🔒 Prinsip Keamanan

### AI TIDAK Boleh:

- ❌ Otomatis mengirim laporan
- ❌ Mengunci atau menimpa input user
- ❌ Membuat keputusan
- ❌ Memvalidasi kebenaran laporan
- ❌ Menebak lokasi spesifik
- ❌ Menyalahkan pihak tertentu

### AI Boleh:

- ✅ Menghasilkan saran deskripsi
- ✅ Mendeskripsikan kondisi visual
- ✅ Memberikan deskripsi netral dan faktual

## 📦 Instalasi

### 1. Copy Configuration File

```bash
cp config/vision.example.php config/vision.php
```

### 2. Edit Configuration

Buka `config/vision.php` dan masukkan API key OpenAI Anda:

```php
'openai_api_key' => 'sk-proj-xxxxxxxxxxxx',
```

### 3. Pastikan File Permissions

```bash
chmod 644 config/vision.php
```

### 4. Verifikasi .gitignore

Pastikan `config/vision.php` sudah ada di `.gitignore`:

```
config/vision.php
```

## 🔧 Konfigurasi

### Default Settings

| Setting       | Value         | Keterangan                        |
| ------------- | ------------- | --------------------------------- |
| Model         | `gpt-4o-mini` | Model Vision API yang digunakan   |
| Temperature   | `0.2`         | Low temperature untuk konsistensi |
| Max Tokens    | `100`         | Deskripsi singkat 1-2 kalimat     |
| Max File Size | `5MB`         | Batas ukuran file gambar          |
| Allowed Types | JPG, PNG      | Tipe file yang didukung           |

### Customizing System Prompt

Edit `config/vision.php` bagian `system_prompt` untuk menyesuaikan gaya deskripsi:

```php
'system_prompt' => 'Anda adalah asisten yang membantu warga...'
```

## 🎨 Alur Penggunaan

```
1. User upload foto
   ↓
2. Tombol "Isi Deskripsi dari Foto (AI)" muncul
   ↓
3. User klik tombol (opsional)
   ↓
4. Sistem kirim foto ke OpenAI Vision API
   ↓
5. AI generate deskripsi netral
   ↓
6. Deskripsi masuk ke textarea
   ↓
7. User dapat edit deskripsi
   ↓
8. User submit laporan manual
```

## 💻 Struktur File

```
LaporWarga/
├── config/
│   ├── vision.example.php      # Template konfigurasi
│   └── vision.php              # Konfigurasi aktual (gitignored)
├── api/
│   └── vision_deskripsi.php    # Backend endpoint
├── public/
│   ├── index.php               # Form dengan Vision AI button
│   └── assets/
│       ├── css/
│       │   └── style.css       # Vision AI styles
│       └── js/
│           └── vision.js       # Vision AI logic
└── docs/
    └── VISION_AI.md            # Dokumentasi ini
```

## 🔌 API Endpoint

### POST `/api/vision_deskripsi.php`

**Request:**

- Method: `POST`
- Content-Type: `multipart/form-data`
- Body: `foto` (file)

**Success Response:**

```json
{
  "success": true,
  "description": "Terlihat jalan berlubang dengan kedalaman sekitar 20cm di tengah jalan beraspal.",
  "message": "Deskripsi berhasil dibuat. Anda dapat mengedit sebelum mengirim laporan."
}
```

**Error Response:**

```json
{
  "success": false,
  "error": "Tipe file tidak didukung. Gunakan JPG atau PNG."
}
```

## 🛡️ Validasi Input

### File Validation

- ✅ Tipe file: JPG, PNG
- ✅ Ukuran maksimal: 5MB
- ✅ File harus valid image
- ✅ Sanitasi MIME type

### Output Sanitization

- ✅ HTML encoding dengan `htmlspecialchars()`
- ✅ Batasan panjang deskripsi (300 karakter)
- ✅ Trim whitespace

## ⚡ Error Handling

| Error Type        | User Message                              | Action         |
| ----------------- | ----------------------------------------- | -------------- |
| No config         | "Konfigurasi Vision AI belum diatur"      | Contact admin  |
| Invalid API key   | "API key OpenAI belum dikonfigurasi"      | Check config   |
| Invalid file type | "Tipe file tidak didukung"                | Use JPG/PNG    |
| File too large    | "Ukuran file terlalu besar. Maksimal 5MB" | Compress image |
| API error         | "Layanan AI tidak dapat memproses gambar" | Write manually |
| Network error     | "Terjadi kesalahan koneksi"               | Try again      |

## 💰 Kontrol Biaya

### Strategi Hemat Biaya:

1. **Trigger Manual**: Vision AI hanya jalan saat tombol diklik
2. **One Request per Photo**: Satu gambar = satu request
3. **Low Max Tokens**: Hanya 100 tokens per request
4. **Low Temperature**: Konsistensi tinggi, retry rendah
5. **Small Model**: Gunakan `gpt-4o-mini` (lebih murah)

### Estimasi Biaya:

- Model: `gpt-4o-mini`
- Cost: ~$0.01 per request (estimate)
- 100 laporan/hari = ~$1/hari

## 📱 UI Components

### Vision AI Button

```html
<button type="button" id="btn-vision-ai" class="btn btn-secondary" disabled><i class="ri-magic-line"></i> Isi Deskripsi dari Foto (AI)</button>
```

### States:

- **Disabled**: Saat foto belum dipilih
- **Enabled**: Saat foto sudah dipilih
- **Loading**: Saat memproses request

## 🧪 Testing

### Manual Testing Steps:

1. **Test Upload Valid Image**

   - Upload JPG/PNG < 5MB
   - Button harus muncul dan enabled

2. **Test Invalid File Type**

   - Upload PDF/DOCX
   - Alert: "Tipe file tidak didukung"

3. **Test File Too Large**

   - Upload > 5MB
   - Alert: "Ukuran file terlalu besar"

4. **Test AI Generation**

   - Klik "Isi Deskripsi dari Foto (AI)"
   - Loading state muncul
   - Deskripsi masuk ke textarea

5. **Test Manual Edit**

   - Edit deskripsi hasil AI
   - Pastikan bisa diedit bebas

6. **Test Replace Warning**
   - Tulis deskripsi manual dulu
   - Klik Vision AI button
   - Confirm dialog muncul

## 🔍 Troubleshooting

### Button Tidak Muncul

- ✅ Cek foto sudah dipilih
- ✅ Cek `vision.js` ter-load
- ✅ Cek console browser untuk error

### API Error

- ✅ Cek `config/vision.php` exists
- ✅ Cek API key valid
- ✅ Cek quota OpenAI
- ✅ Cek error log PHP

### Deskripsi Tidak Muncul

- ✅ Cek network tab browser
- ✅ Cek response API
- ✅ Cek file size < 5MB

## 📚 Referensi

- [OpenAI Vision API Documentation](https://platform.openai.com/docs/guides/vision)
- [OpenAI API Pricing](https://openai.com/api/pricing/)
- [PHP cURL Documentation](https://www.php.net/manual/en/book.curl.php)

## 🔐 Security Checklist

- [x] API key tidak di-commit ke Git
- [x] Input file divalidasi (type, size)
- [x] Output di-sanitize (htmlspecialchars)
- [x] Error message tidak expose sensitive info
- [x] Rate limiting per request (manual trigger)
- [x] System prompt membatasi scope AI
- [x] Tidak auto-submit laporan

## 📝 Changelog

### Version 1.0.0 (2025-12-25)

- ✨ Initial release
- ✨ OpenAI Vision API integration
- ✨ Optional AI description generator
- ✨ Manual trigger with button
- ✨ User-editable results
- ✨ Comprehensive error handling
- ✨ Mobile responsive design

## 📄 License

Copyright © 2025 LaporWarga. All rights reserved.

---

**Catatan**: Fitur ini untuk keperluan akademis. Pastikan OpenAI API key Anda aman dan tidak dibagikan.
