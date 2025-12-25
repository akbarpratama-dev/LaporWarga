<?php
/**
 * Vision AI Configuration
 * Copy this file to vision.php and add your OpenAI API key
 */

return [
    // OpenAI Vision API Configuration
    'openai_api_key' => 'YOUR_OPENAI_API_KEY_HERE',
    'openai_model' => 'gpt-4o-mini',  // Vision-capable model
    'temperature' => 0.2,              // Low temperature for consistent, factual responses
    'max_tokens' => 100,               // Short descriptions only
    
    // Image Validation
    'allowed_types' => ['image/jpeg', 'image/png', 'image/jpg'],
    'max_file_size' => 5242880,  // 5MB in bytes
    
    // Security & Cost Control
    'max_description_length' => 300,
    
    // System Prompt for Vision AI
    'system_prompt' => 'Anda adalah asisten yang membantu warga membuat deskripsi laporan berdasarkan foto kondisi di lapangan.

ATURAN KETAT:
- Gunakan bahasa Indonesia
- Tulis 1-2 kalimat saja
- Bersikap netral dan faktual
- Deskripsikan HANYA apa yang terlihat pada gambar
- JANGAN menebak lokasi spesifik
- JANGAN menyalahkan pihak tertentu
- JANGAN gunakan bahasa emosional
- JANGAN membuat asumsi di luar gambar
- Fokus pada kondisi fisik yang terlihat

Contoh format yang baik:
"Terlihat jalan berlubang dengan kedalaman sekitar 20cm di tengah jalan beraspal."
"Terdapat tumpukan sampah di sisi jalan dengan volume cukup besar."
"Lampu jalan dalam kondisi mati, tiang masih berdiri kokoh."

Tugas Anda: Deskripsikan kondisi yang terlihat pada gambar secara singkat, netral, dan formal untuk laporan warga.'
];
?>
