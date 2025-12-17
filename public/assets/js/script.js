/**
 * LaporWarga - Citizen Reporting System
 * JavaScript Functions
 * Created: December 2025
 */

// ============================================
// NAVBAR MOBILE TOGGLE
// ============================================
document.addEventListener("DOMContentLoaded", function () {
  const navToggle = document.getElementById("navToggle");
  const navMenu = document.getElementById("navMenu");

  if (navToggle) {
    navToggle.addEventListener("click", function () {
      navMenu.classList.toggle("active");
    });
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href !== "#" && href.length > 1) {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
          // Close mobile menu if open
          if (navMenu) {
            navMenu.classList.remove("active");
          }
        }
      }
    });
  });
});

// ============================================
// CAROUSEL FUNCTIONALITY
// ============================================
(function () {
  const carousel = document.getElementById("infoCarousel");
  if (!carousel) return;

  const prevBtn = document.getElementById("carouselPrev");
  const nextBtn = document.getElementById("carouselNext");
  const cards = carousel.querySelectorAll(".carousel-card");

  let currentIndex = 0;
  let cardsToShow = 3;
  let autoSlideInterval;

  // Responsive cards to show
  function updateCardsToShow() {
    if (window.innerWidth <= 768) {
      cardsToShow = 1;
    } else if (window.innerWidth <= 1024) {
      cardsToShow = 2;
    } else {
      cardsToShow = 3;
    }
  }

  function updateCarousel() {
    const cardWidth = cards[0].offsetWidth;
    const gap = 20;
    const offset = -(currentIndex * (cardWidth + gap));
    carousel.style.transform = `translateX(${offset}px)`;
  }

  function nextSlide() {
    const maxIndex = Math.max(0, cards.length - cardsToShow);
    currentIndex = currentIndex + 1 > maxIndex ? 0 : currentIndex + 1;
    updateCarousel();
  }

  function prevSlide() {
    const maxIndex = Math.max(0, cards.length - cardsToShow);
    currentIndex = currentIndex - 1 < 0 ? maxIndex : currentIndex - 1;
    updateCarousel();
  }

  // Event listeners
  if (nextBtn) {
    nextBtn.addEventListener("click", function () {
      nextSlide();
      resetAutoSlide();
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", function () {
      prevSlide();
      resetAutoSlide();
    });
  }

  // Auto slide every 6 seconds
  function startAutoSlide() {
    autoSlideInterval = setInterval(nextSlide, 6000);
  }

  function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
  }

  // Initialize
  updateCardsToShow();
  updateCarousel();
  startAutoSlide();

  // Update on resize
  window.addEventListener("resize", function () {
    updateCardsToShow();
    currentIndex = 0;
    updateCarousel();
  });
})();

// ============================================
// FORM VALIDATION
// ============================================
(function () {
  const formLaporan = document.getElementById("formLaporan");
  if (!formLaporan) return;

  formLaporan.addEventListener("submit", function (e) {
    const nama = document.getElementById("nama_pelapor").value.trim();
    const noHp = document.getElementById("no_hp").value.trim();
    const kategori = document.getElementById("kategori").value;
    const lokasi = document.getElementById("lokasi").value.trim();
    const deskripsi = document.getElementById("deskripsi").value.trim();

    // Basic validation
    if (!nama || nama.length < 3) {
      e.preventDefault();
      alert("Nama lengkap minimal 3 karakter");
      return false;
    }

    if (!noHp || !/^[0-9]{10,13}$/.test(noHp)) {
      e.preventDefault();
      alert("Nomor HP harus 10-13 digit angka");
      return false;
    }

    if (!kategori) {
      e.preventDefault();
      alert("Silakan pilih kategori masalah");
      return false;
    }

    if (!lokasi || lokasi.length < 5) {
      e.preventDefault();
      alert("Lokasi minimal 5 karakter");
      return false;
    }

    if (!deskripsi || deskripsi.length < 20) {
      e.preventDefault();
      alert("Deskripsi minimal 20 karakter");
      return false;
    }

    // File validation
    const fotoInput = document.getElementById("foto");
    if (fotoInput && fotoInput.files.length > 0) {
      const file = fotoInput.files[0];
      const maxSize = 2 * 1024 * 1024; // 2MB
      const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];

      if (!allowedTypes.includes(file.type)) {
        e.preventDefault();
        alert("Format foto harus JPG, JPEG, atau PNG");
        return false;
      }

      if (file.size > maxSize) {
        e.preventDefault();
        alert("Ukuran foto maksimal 2MB");
        return false;
      }
    }

    // Show loading state
    const submitBtn = formLaporan.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Mengirim...";
    }
  });

  // Real-time phone number validation
  const noHpInput = document.getElementById("no_hp");
  if (noHpInput) {
    noHpInput.addEventListener("input", function (e) {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  }
})();

// ============================================
// CEK STATUS FORM VALIDATION
// ============================================
(function () {
  const formCekStatus = document.getElementById("formCekStatus");
  if (!formCekStatus) return;

  formCekStatus.addEventListener("submit", function (e) {
    const kodeLaporan = document.getElementById("kode_laporan").value.trim();
    const noHp = document.getElementById("no_hp").value.trim();

    if (!kodeLaporan) {
      e.preventDefault();
      alert("Kode laporan harus diisi");
      return false;
    }

    if (!/^LPR-\d{8}-\d{3}$/.test(kodeLaporan)) {
      e.preventDefault();
      alert("Format kode laporan tidak valid (contoh: LPR-20251216-001)");
      return false;
    }

    if (!noHp || !/^[0-9]{10,13}$/.test(noHp)) {
      e.preventDefault();
      alert("Nomor HP harus 10-13 digit angka");
      return false;
    }

    // Show loading state
    const submitBtn = formCekStatus.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Mencari...";
    }
  });

  // Auto format kode laporan
  const kodeLaporanInput = document.getElementById("kode_laporan");
  if (kodeLaporanInput) {
    kodeLaporanInput.addEventListener("input", function (e) {
      this.value = this.value.toUpperCase();
    });
  }

  // Real-time phone number validation
  const noHpInput = document.getElementById("no_hp");
  if (noHpInput) {
    noHpInput.addEventListener("input", function (e) {
      this.value = this.value.replace(/[^0-9]/g, "");
    });
  }
})();

// ============================================
// SHOW/HIDE COMPLETED FIELDS IN ADMIN
// ============================================
(function () {
  const statusSelect = document.getElementById("status");
  if (!statusSelect) return;

  const selesaiFields = document.getElementById("selesaiFields");
  if (!selesaiFields) return;

  statusSelect.addEventListener("change", function () {
    if (this.value === "Selesai") {
      selesaiFields.style.display = "block";
    } else {
      selesaiFields.style.display = "none";
    }
  });
})();

// ============================================
// PHOTO PREVIEW
// ============================================
function previewImage(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      const preview = document.getElementById(previewId);
      if (preview) {
        preview.src = e.target.result;
        preview.style.display = "block";
      } else {
        // Create preview element if not exists
        const img = document.createElement("img");
        img.id = previewId;
        img.src = e.target.result;
        img.style.width = "100%";
        img.style.marginTop = "10px";
        img.style.borderRadius = "8px";
        input.parentElement.appendChild(img);
      }
    };

    reader.readAsDataURL(input.files[0]);
  }
}

// Attach preview to file inputs
document.addEventListener("DOMContentLoaded", function () {
  const fotoInput = document.getElementById("foto");
  if (fotoInput) {
    fotoInput.addEventListener("change", function () {
      previewImage(this, "fotoPreview");
    });
  }

  const fotoAfterInput = document.getElementById("foto_after");
  if (fotoAfterInput) {
    fotoAfterInput.addEventListener("change", function () {
      previewImage(this, "fotoAfterPreview");
    });
  }
});

// ============================================
// TABLE SEARCH (OPTIONAL ENHANCEMENT)
// ============================================
function searchTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);

  if (!input || !table) return;

  input.addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      const cells = row.getElementsByTagName("td");
      let found = false;

      for (let j = 0; j < cells.length; j++) {
        const cell = cells[j];
        if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
          found = true;
          break;
        }
      }

      row.style.display = found ? "" : "none";
    }
  });
}

// ============================================
// CONFIRM DELETE
// ============================================
document.addEventListener("DOMContentLoaded", function () {
  const deleteLinks = document.querySelectorAll('a[href*="delete"]');

  deleteLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        e.preventDefault();
      }
    });
  });
});

// ============================================
// AUTO DISMISS ALERTS
// ============================================
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");

  alerts.forEach((alert) => {
    setTimeout(function () {
      alert.style.transition = "opacity 0.5s";
      alert.style.opacity = "0";
      setTimeout(function () {
        alert.remove();
      }, 500);
    }, 5000);
  });
});

// ============================================
// UTILITY FUNCTIONS
// ============================================
function formatRupiah(angka) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(angka);
}

function formatTanggal(dateString) {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString("id-ID", options);
}

// Prevent multiple form submissions
document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    form.addEventListener("submit", function () {
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        setTimeout(function () {
          submitBtn.disabled = true;
        }, 100);
      }
    });
  });
});

console.log("LaporWarga JavaScript loaded successfully");

// ============================================
// SCROLL SPY (ACTIVE LINK ON SCROLL)
// ============================================
document.addEventListener("DOMContentLoaded", function () {
  const sections = document.querySelectorAll("section");
  const navLinks = document.querySelectorAll(".nav-link");

  function changeLinkState() {
    let index = sections.length;

    while (--index && window.scrollY + 100 < sections[index].offsetTop) {}

    navLinks.forEach((link) => link.classList.remove("active"));

    // Find the link that corresponds to the current section
    if (index >= 0 && sections[index]) {
      const id = sections[index].id;
      const activeLink = document.querySelector(`.nav-link[href="#${id}"]`);
      if (activeLink) {
        activeLink.classList.add("active");
      }
    }
  }

  changeLinkState();
  window.addEventListener("scroll", changeLinkState);
});

// ============================================
// SCROLL ANIMATION TRIGGER
// ============================================
document.addEventListener("DOMContentLoaded", function () {
  const scrollElements = document.querySelectorAll(".scroll-animate");

  const elementInView = (el, offset = 100) => {
    const elementTop = el.getBoundingClientRect().top;
    return elementTop <= (window.innerHeight || document.documentElement.clientHeight) - offset;
  };

  const displayScrollElement = (element) => {
    element.classList.add("animate-in");
  };

  const handleScrollAnimation = () => {
    scrollElements.forEach((el) => {
      if (elementInView(el, 100)) {
        displayScrollElement(el);
      }
    });
  };

  window.addEventListener("scroll", handleScrollAnimation);
  handleScrollAnimation(); // Initial check on page load
});
