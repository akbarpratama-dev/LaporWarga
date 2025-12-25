/**
 * Vision AI Module
 * Handles AI-powered description generation from uploaded photos
 */

(function() {
    'use strict';

    // DOM Elements
    const fotoInput = document.getElementById('foto');
    const visionContainer = document.getElementById('vision-ai-container');
    const visionBtn = document.getElementById('btn-vision-ai');
    const deskripsiTextarea = document.getElementById('deskripsi');

    // State
    let isProcessing = false;
    let hasGeneratedOnce = false;

    // Initialize
    if (fotoInput && visionBtn && deskripsiTextarea) {
        initVisionAI();
    }

    function initVisionAI() {
        // Show Vision AI button when photo is selected
        fotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Validate file type
                const file = this.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipe file tidak didukung. Gunakan JPG atau PNG.');
                    this.value = '';
                    visionContainer.style.display = 'none';
                    visionBtn.disabled = true;
                    return;
                }

                // Validate file size (5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    this.value = '';
                    visionContainer.style.display = 'none';
                    visionBtn.disabled = true;
                    return;
                }

                // Show button and enable it
                visionContainer.style.display = 'block';
                visionBtn.disabled = false;
                hasGeneratedOnce = false;
            } else {
                visionContainer.style.display = 'none';
                visionBtn.disabled = true;
            }
        });

        // Handle Vision AI button click
        visionBtn.addEventListener('click', generateDescription);
    }

    async function generateDescription() {
        // Prevent multiple simultaneous requests
        if (isProcessing) {
            return;
        }

        // Check if file is selected
        if (!fotoInput.files || !fotoInput.files[0]) {
            showNotification('Silakan pilih foto terlebih dahulu.', 'error');
            return;
        }

        // Confirm if user wants to replace existing description
        if (deskripsiTextarea.value.trim() !== '' && !hasGeneratedOnce) {
            const confirm = window.confirm(
                'Deskripsi yang sudah Anda tulis akan diganti dengan hasil AI. Lanjutkan?'
            );
            if (!confirm) {
                return;
            }
        }

        // Set processing state
        isProcessing = true;
        setButtonLoading(true);

        // Prepare form data
        const formData = new FormData();
        formData.append('foto', fotoInput.files[0]);

        try {
            // Call Vision AI API
            const response = await fetch('../api/vision_deskripsi.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Insert AI-generated description
                deskripsiTextarea.value = result.description;
                hasGeneratedOnce = true;
                
                // Show success message
                showNotification(result.message || 'Deskripsi berhasil dibuat!', 'success');
                
                // Focus on textarea for user to review/edit
                deskripsiTextarea.focus();
            } else {
                // Show error message
                showNotification(result.error || 'Gagal membuat deskripsi. Silakan tulis manual.', 'error');
            }
        } catch (error) {
            console.error('Vision AI Error:', error);
            showNotification('Terjadi kesalahan koneksi. Silakan coba lagi.', 'error');
        } finally {
            // Reset processing state
            isProcessing = false;
            setButtonLoading(false);
        }
    }

    function setButtonLoading(loading) {
        if (loading) {
            visionBtn.disabled = true;
            visionBtn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses...';
            visionBtn.classList.add('loading');
        } else {
            visionBtn.disabled = false;
            visionBtn.innerHTML = '<i class="ri-magic-line"></i> Isi Deskripsi dari Foto (AI)';
            visionBtn.classList.remove('loading');
        }
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `vision-notification vision-notification-${type}`;
        notification.innerHTML = `
            <i class="ri-${type === 'success' ? 'check-circle' : 'error-warning'}-line"></i>
            <span>${message}</span>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Remove after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
})();
