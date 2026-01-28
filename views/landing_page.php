<?php
$basePath = '/system_ordering/public';
if (!isset($reviews)) {
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOME - System Ordering Manufacture Engineering</title>
    <link href="<?= $basePath ?>/assets/css/landing_page.css?v=<?= time() ?>" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <div class="nav-logo-box"> <img src="<?= $basePath ?>/assets/img/ME_LOGO.jpg" alt="Logo" style="height:40px;">
                </div>
                <h2>SOME</h2>
            </div>
            <ul class="nav-menu">
                <li><a onclick="scrollToSection('home')">HOME</a></li>
                <li><a onclick="scrollToSection('about')">VISI & MISI</a></li>
                <li><a onclick="scrollToSection('services')">KEUNGGULAN</a></li>
                <li><a onclick="scrollToSection('team')">TIM KAMI</a></li>
                <li><a onclick="scrollToSection('reviews')">ULASAN</a></li>
                <li><a onclick="scrollToSection('contact')">CONTACT</a></li>
            </ul>
            <!-- LOGIN ROLE -->
            <div class="nav-login">
                <button class="login-btn" id="loginBtn">Login ▾</button>
                <div class="login-dropdown" id="loginDropdown">
                    <a href="<?= $basePath ?>/admin/login">Admin</a>
                    <a href="<?= $basePath ?>/spv/login">Supervisor</a>
                    <a href="<?= $basePath ?>/customer/login">Customer</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <p class="hero-subtitle">Welcome to SOME</p>
            <h1>
                <span class="highlight">
                    <span class="yellow">S</span>YSTEM
                    <span class="yellow">O</span>RDERING
                </span><br>
                <span class="yellow">M</span>ANUFACTURE
                <span class="yellow">E</span>NGINEERING</span>
            </h1>

            <button class="hero-btn" onclick="scrollToSection('services')">Pelajari Lebih Lanjut</button>
        </div>
    </section>

    <!-- Service Cards -->
    <div class="services-cards">
        <div class="service-card">
            <div class="service-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="#2746d0">
                    <path
                        d="M19.14,12.94a7.07,7.07,0,0,0,.05-.94,7.07,7.07,0,0,0-.05-.94l2.11-1.65a.5.5,0,0,0,.12-.64l-2-3.46a.5.5,0,0,0-.61-.22l-2.49,1a7.28,7.28,0,0,0-1.63-.94l-.38-2.65A.5.5,0,0,0,13.21,3H10.79a.5.5,0,0,0-.49.42L9.92,6.07a7.28,7.28,0,0,0-1.63.94l-2.49-1a.5.5,0,0,0-.61.22l-2,3.46a.5.5,0,0,0,.12.64L5.42,11.06a7.07,7.07,0,0,0-.05.94,7.07,7.07,0,0,0,.05.94L3.31,14.59a.5.5,0,0,0-.12.64l2,3.46a.5.5,0,0,0,.61.22l2.49-1a7.28,7.28,0,0,0,1.63.94l.38,2.65a.5.5,0,0,0,.49.42h2.42a.5.5,0,0,0,.49-.42l.38-2.65a7.28,7.28,0,0,0,1.63-.94l2.49,1a.5.5,0,0,0,.61-.22l2-3.46a.5.5,0,0,0-.12-.64ZM12,15.5A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                </svg>
            </div>
            <h3>ORDER MANAGEMENT</h3>
            <p>Pencatatan dan pengelolaan pesanan manufaktur secara terstruktur, cepat, dan akurat</p>
        </div>

        <div class="service-card featured">
            <div class="service-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                    <path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" />
                </svg>
            </div>
            <h3>APPROVAL WORKFLOW</h3>
            <p>Alur persetujuan otomatis sesuai role (Admin, SPV, Customer) untuk menjaga integritas proses</p>
        </div>

        <div class="service-card">
            <div class="service-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="#2746d0">
                    <path
                        d="M12 2C7.58 2 4 3.79 4 6v12c0 2.21 3.58 4 8 4s8-1.79 8-4V6c0-2.21-3.58-4-8-4zm0 2c3.31 0 6 .9 6 2s-2.69 2-6 2-6-.9-6-2 2.69-2 6-2zm0 14c-3.31 0-6-.9-6-2v-2c1.35.84 3.53 1.34 6 1.34s4.65-.5 6-1.34v2c0 1.1-2.69 2-6 2z" />
                </svg>
            </div>
            <h3>REPORTING & COST TRACKING</h3>
            <p>Visualisasi biaya dan laporan produksi bulanan untuk mendukung keputusan manajemen</p>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="section-dark">
        <div class="container">
            <div class="section-header">
                <p class="section-subtitle">Tentang Kami</p>
                <h2 class="section-title">Visi & Misi ADM</h2>
            </div>
            <div class="vm-grid">
                <div class="vm-box">
                    <h3>Visi</h3>
                    <p>Perusahaan global terbaik yang membuat hidup orang lebih baik melalui mobilitas dan konektivitas.
                    </p>
                </div>
                <div class="vm-box">
                    <h3>Misi</h3>
                    <ul>
                        <li>Mengutamakan kebahagiaan, keselamatan, dan kualitas melalui budaya perusahaan yang kuat</li>
                        <li>Menginspirasi orang untuk meningkatkan kehidupan dan melampaui kemampuannya</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Services/Benefits Section -->
    <section id="services" class="section-light">
        <div class="container">
            <div class="section-header">
                <p class="section-subtitle">Layanan Kami</p>
                <h2 class="section-title">Keunggulan Manufacture Engineering</h2>
            </div>
            <div class="benefits-grid">
                <div class="benefit-box">
                    <div class="benefit-icon">1</div>
                    <h3>Otomasi Proses</h3>
                    <p>Mengurangi kesalahan manual dan mempercepat proses ordering dengan sistem otomatis yang
                        terintegrasi.</p>
                </div>
                <div class="benefit-box">
                    <div class="benefit-icon">2</div>
                    <h3>Real-Time Monitoring</h3>
                    <p>Pantau status order secara real-time dan dapatkan insight untuk pengambilan keputusan yang lebih
                        baik.</p>
                </div>
                <div class="benefit-box">
                    <div class="benefit-icon">3</div>
                    <h3>Efisiensi Biaya</h3>
                    <p>Optimalkan penggunaan resources dan kurangi waste untuk meningkatkan profitabilitas perusahaan.
                    </p>
                </div>
                <div class="benefit-box">
                    <div class="benefit-icon">4</div>
                    <h3>Traceability</h3>
                    <p>Lacak setiap tahap produksi dengan mudah untuk memastikan kualitas dan compliance standar
                        industri.</p>
                </div>
                <div class="benefit-box">
                    <div class="benefit-icon">5</div>
                    <h3>Skalabilitas</h3>
                    <p>Sistem yang dapat berkembang seiring pertumbuhan bisnis Anda tanpa mengorbankan performa.</p>
                </div>
                <div class="benefit-box">
                    <div class="benefit-icon">6</div>
                    <h3>Kolaborasi Tim</h3>
                    <p>Tingkatkan koordinasi antar departemen dengan platform terpusat yang mudah diakses.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="section-dark">
        <div class="container">
            <div class="section-header">
                <p class="section-subtitle">Tim Profesional</p>
                <h2 class="section-title">Kenali Tim Kami</h2>
            </div>
            <div class="team-carousel">
                <div class="team-scroll" id="teamScroll">
                    <!-- Team members will be inserted here by JavaScript -->
                </div>
                <div class="carousel-nav">
                    <button class="carousel-btn" onclick="scrollTeam('left')">‹</button>
                    <button class="carousel-btn" onclick="scrollTeam('right')">›</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="section-light">
        <div class="container">
            <div class="section-header text-center">
                <p class="section-subtitle">Review Customer Kami</p>
                <h2 class="section-title">Apa Kata Customer Kami?</h2>
                <?php
                // ambil rata-rata rating bulan ini
                $avgRating = \App\Models\ReviewModel::getAverageRatingByMonth(date('Y-m'));
                ?>
                <?php if ($avgRating): ?>
                    <p class="section-subtitle">Rata-rata bulan ini: <?= $avgRating ?>/5</p>
                <?php endif; ?>
            </div>

            <div class="reviews-carousel">
                <div class="reviews-scroll" id="reviewsScroll">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $r): ?>
                            <div class="review-card">
                                <h5><?= htmlspecialchars($r['customer_name'] ?? 'Anonim') ?></h5>
                                <p class="stars">
                                    <?= str_repeat("★", (int)$r['rating']) ?>
                                    <?= str_repeat("☆", 5 - (int)$r['rating']) ?>
                                </p>
                                <p class="review-text"><?= htmlspecialchars($r['review']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-reviews">Belum ada review dari customer.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($reviews)): ?>
                    <div class="carousel-nav text-center mt-3">
                        <button class="carousel-btn" onclick="scrollReviews('left')" aria-label="Scroll left">‹</button>
                        <button class="carousel-btn" onclick="scrollReviews('right')" aria-label="Scroll right">›</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        function scrollReviews(direction) {
            const container = document.getElementById('reviewsScroll');
            const card = container.querySelector('.review-card');
            const scrollAmount = card ? card.offsetWidth + 16 : 300; // card width + gap
            container.scrollBy({
                left: direction === 'left' ? -scrollAmount : scrollAmount,
                behavior: 'smooth'
            });
        }
    </script>

    <!-- Map Section -->
    <section class="map-section" id="contact">
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3965.180343695398!2d107.2738995!3d-6.3707026!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e699e08d07fe99f%3A0xa54208b12ee3f8ff!2sADM%20Engine%20Plant%2C%20PT.%20Astra%20Daihatsu%20Motor%20-%20Engine%20Plant!5e0!3m2!1sid!2sid!4v1767596150716!5m2!1sid!2sid"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div class="footer-about">
                <h3>SOME</h3>
                <p>System Ordering for Manufacture Engineering - Transformasi digital terintegrasi untuk mendorong
                    efisiensi dan produktivitas manufaktur di era industri modern.</p>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <div>
                        <p>Kawasan Industri KIIC<br>Jl. Maligi VI Lot M No. 6, Margakaya, Kec. Telukjambe
                            Barat.<br>Karawang, Jawa Barat 41361</p>
                    </div>
                </div>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a onclick="scrollToSection('home')">Home</a></li>
                    <li><a onclick="scrollToSection('about')">Visi & Misi</a></li>
                    <li><a onclick="scrollToSection('services')">Keunggulan</a></li>
                    <li><a onclick="scrollToSection('team')">Tim Kami</a></li>
                    <li><a onclick="scrollToSection('reviews')">Ulasan</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Contact Info</h4>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    <p>+62 815-7492-4732 - Dwi Haryanto</p> <br>
                    <p>+62 812-8797-7243 - Andri Rahmat</p>
                </div>
                <div class="contact-item">
                    <svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <p>dwi.haryantop2@daihatsu.astra.co.id</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2026 SOME - System Ordering for Manufacturing Engineering. All rights reserved.</p>
        </div>
    </footer>


    <?php $basePath = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/system_ordering/public'; ?>
    <script>
        const basePath = "<?= $basePath ?>";

        // Login Role Dropdown
        const loginBtn = document.getElementById('loginBtn');
        const loginDropdown = document.getElementById('loginDropdown');

        loginBtn.addEventListener('click', () => {
            loginDropdown.style.display =
                loginDropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Tutup dropdown kalau klik di luar
        window.addEventListener('click', (e) => {
            if (!loginBtn.contains(e.target) && !loginDropdown.contains(e.target)) {
                loginDropdown.style.display = 'none';
            }
        });


        // Team Members Data
        const teamMembers = [{
                name: "DETALANA NUGRAHA",
                position: "Manager Maintenance",
                photo: `${basePath}/uploads/member/detalana.jpg`,
                quote: "Semangat tim tumbuh dari keberanian, dedikasi, dan tekad untuk mencapai yang terbaik"
            },
            {
                name: "MARWAN TRIANA",
                position: "Supervisor",
                photo: `${basePath}/uploads/member/marwan.jpg`,
                quote: "Sinergi, komitmen, dan solusi adalah kunci untuk mewujudkan tujuan bersama"
            },
            {
                name: "DWI HARYANTO",
                position: "Foreman",
                photo: `${basePath}/uploads/member/dwi.jpg`,
                quote: "Kepemimpinan adalah tentang memberdayakan tim untuk mencapai yang terbaik"
            },
            {
                name: "ANDRI RAHMAT",
                position: "Leader",
                photo: `${basePath}/uploads/member/andri.jpg`,
                quote: "Kesuksesan tim adalah kesuksesan kita bersama"
            },
            {
                name: "IWAN HERDIANA",
                position: "Leader",
                photo: `${basePath}/uploads/member/iwan.jpg`,
                quote: "Inovasi dimulai dari keberanian untuk mencoba"
            },
            {
                name: "IRVAN WAHYU",
                position: "Member",
                photo: `${basePath}/uploads/member/irvan.jpg`,
                quote: "Setiap detail kecil berkontribusi pada kesempurnaan"
            },
            {
                name: "KHANIN FALIK",
                position: "Member",
                photo: `${basePath}/uploads/member/falik.jpg`,
                quote: "Kualitas adalah prioritas utama dalam setiap pekerjaan"
            },
            {
                name: "RIO ANDRIKO",
                position: "Member",
                photo: `${basePath}/uploads/member/rio.jpg`,
                quote: "Kolaborasi adalah kunci menuju inovasi"
            },
            {
                name: "IMAM PUJIANTO",
                position: "Member",
                photo: `${basePath}/uploads/member/imam.jpg`,
                quote: "Dedikasi dan kerja keras tidak pernah mengkhianati hasil"
            }
        ];

        // Render Team Members
        function renderTeam() {
            const container = document.getElementById('teamScroll');
            container.innerHTML = teamMembers.map(member => `
                <div class="team-member">
                    <div class="member-photo">
                        <img src="${member.photo}" alt="${member.name}">
                    </div>
                    <h3 class="member-name">${member.name}</h3>
                    <p class="member-position">${member.position}</p>
                    <p class="member-quote">"${member.quote}"</p>
                </div>
            `).join('');
        }


        // Scroll Functions
        function scrollTeam(direction) {
            const container = document.getElementById('teamScroll');
            const scrollAmount = 320;
            container.scrollBy({
                left: direction === 'left' ? -scrollAmount : scrollAmount,
                behavior: 'smooth'
            });
        }

        function scrollReviews(direction) {
            const container = document.getElementById('reviewsScroll');
            const scrollAmount = 380;
            container.scrollBy({
                left: direction === 'left' ? -scrollAmount : scrollAmount,
                behavior: 'smooth'
            });
        }

        // Smooth Scroll to Section
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            const offset = 75;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }

        // Initialize
        renderTeam();

        // Navbar background on scroll
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(14, 47, 89, 0.98)';
            } else {
                navbar.style.background = 'rgba(14, 47, 89, 0.95)';
            }
        });
    </script>

</body>

</html>