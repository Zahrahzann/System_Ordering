<?php
// Pastikan session selalu dimulai di baris paling awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Customer - ME Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/system_ordering/public/assets/css/customer/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Background shapes -->
    <div class="bg-shape shape1"></div>
    <div class="bg-shape shape2"></div>
    <div class="bg-shape shape3"></div>

    <!-- Hidden form -->
    <form method="POST" action="/system_ordering/public/customer/process_login" id="customerLoginForm" style="display: none;">
        <input type="text" id="swal-name" name="name">
        <input type="text" id="swal-npk" name="npk">
        <input type="text" id="swal-phone" name="phone">
        <input type="text" id="swal-plant" name="plant_id">
        <input type="text" id="swal-dept" name="department_id">
        <input type="text" id="swal-line" name="line">
    </form>

    <div class="container">
        <div class="login-wrapper">
            <!-- Left side - Image -->
            <div class="login-image">
                <div class="image-content">
                    <div class="store-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h2>ME Store</h2>
                    <p>Manufacture Engineering<br>Digital Ordering System</p>
                </div>
            </div>

            <!-- Right side - Form -->
            <div class="login-form">
                <div class="form-header">
                    <h1>Hello, <span>Welcome!</span></h1>
                    <div class="typing-container">
                        <span class="typing-text"></span>
                        <span class="cursor"></span>
                    </div>
                </div>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <span class="feature-text">Pemesanan Praktis dan Efisien</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span class="feature-text">Keamanan dan Terpercaya</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <span class="feature-text">Self-Service Ordering</span>
                    </div>
                </div>

                <button onclick="showLoginPopup()" class="login-button">
                    Get Started
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Typing animation
        const texts = [
            "Manufaktur Part",
            "Pengajuan Work Order",
            "Pengambilan Barang Consumable"
        ];
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const typingElement = document.querySelector('.typing-text');

        function type() {
            const currentText = texts[textIndex];
            
            if (isDeleting) {
                typingElement.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
            } else {
                typingElement.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
            }

            if (!isDeleting && charIndex === currentText.length) {
                setTimeout(() => isDeleting = true, 2000);
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % texts.length;
            }

            const typingSpeed = isDeleting ? 50 : 100;
            setTimeout(type, typingSpeed);
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(type, 500);
        });

        // Login popup function
        const oldInput = {}; // Replace with PHP session data

        function showLoginPopup() {
            Swal.fire({
                title: '<strong>Customer Login</strong>',
                html: `
                    <div style="text-align: left;">
                        <input id="name" class="swal2-input" placeholder="Nama Lengkap" value="${oldInput.name || ''}" required>
                        <input id="npk" class="swal2-input" placeholder="NPK" value="${oldInput.npk || ''}" required>
                        <input id="phone" class="swal2-input" placeholder="Nomor HP" value="${oldInput.phone || ''}">
                        <select id="plant_id" class="swal2-select" required>
                            <option value="">-- Pilih Plant --</option>
                            <option value="1">Plant 1</option>
                            <option value="2">Plant 2</option>
                            <option value="3">Plant 3</option>
                            <option value="4">Plant 4</option>
                            <option value="5">Plant 5</option>
                        </select>
                        <select id="department_id" class="swal2-select" required>
                            <option value="">-- Pilih Departemen --</option>
                            <option value="1">Produksi</option>
                            <option value="2">Quality</option>
                            <option value="3">Maintenance</option>
                            <option value="4">PCL</option>
                            <option value="5">Export-Import</option>
                            <option value="6">PE</option>
                        </select>
                        <input id="line" class="swal2-input" placeholder="Line" value="${oldInput.line || ''}" required>
                    </div>
                `,
                confirmButtonText: '<i class="fas fa-sign-in-alt"></i> Masuk',
                confirmButtonColor: '#667eea',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                cancelButtonColor: '#95a5a6',
                focusConfirm: false,
                width: '550px',
                didOpen: () => {
                    if (oldInput.plant_id) document.getElementById('plant_id').value = oldInput.plant_id;
                    if (oldInput.department_id) document.getElementById('department_id').value = oldInput.department_id;
                },
                preConfirm: () => {
                    const name = document.getElementById('name').value;
                    const npk = document.getElementById('npk').value;
                    const phone = document.getElementById('phone').value;
                    const plant = document.getElementById('plant_id').value;
                    const dept = document.getElementById('department_id').value;
                    const line = document.getElementById('line').value;

                    if (!name || !npk || !plant || !dept || !line) {
                        Swal.showValidationMessage('Mohon lengkapi semua field yang wajib diisi');
                        return false;
                    }

                    document.getElementById('swal-name').value = name;
                    document.getElementById('swal-npk').value = npk;
                    document.getElementById('swal-phone').value = phone;
                    document.getElementById('swal-plant').value = plant;
                    document.getElementById('swal-dept').value = dept;
                    document.getElementById('swal-line').value = line;

                    document.getElementById('customerLoginForm').submit();
                }
            });
        }
    </script>
</body>
</html>