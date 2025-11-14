<?php
include("./includes/koneksi.php");
include("./includes/config.php");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Partnership Team</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.3/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./css/style-index.css" />
    <style>
        body {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            flex-direction: column;
            padding: 50px 10px;
        }

        /* Tombol Back Elegan di Pojok Kiri Atas */
        .back-button {
            position: fixed;
            top: 25px;
            left: 25px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            color: #e0f2fe;
            padding: 12px 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: all 0.35s ease;
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.15);
            z-index: 1000;
        }

        .back-button i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .back-button:hover {
            background: rgba(14, 165, 233, 0.15);
            border-color: rgba(14, 165, 233, 0.5);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.25);
            transform: translateY(-2px);
            color: #38bdf8;
        }

        .back-button:hover i {
            transform: translateX(-4px);
            color: #7dd3fc;
        }

        .team-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 250px;
            text-align: center;
        }

        .team-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 12px 30px rgba(4, 90, 250, 0.4);
        }

        .team-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .team-card:hover img {
            transform: scale(1.1);
        }

        .team-info {
            padding: 20px;
        }

        .team-info h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .team-info p {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-links a {
            color: #94a3b8;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: #38bdf8;
            transform: scale(1.3);
        }

        .team-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            margin-top: 100px;
        }

        .row {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-slide {
            opacity: 0;
        }

        .fade-slide.visible {
            animation: fadeSlideUp 0.8s ease forwards;
        }
    </style>
</head>

<body>

    <!-- Tombol Back Elegan -->
    <a href="index.php" class="back-button">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali ke Halaman Utama</span>
    </a>

    <h1 class="text-4xl font-bold mb-10 text-center text-cyan-400 mt-16">Our Partnership Team</h1>

    <div class="team-container">

        <!-- 1 Card -->
        <div class="row">
            <div class="team-card">
                <img src="assets/team/man.jfif" alt="Partner 1">
                <div class="team-info">
                    <h3>M. Lutfi Nur Fauzi</h3>
                    <p>Founder & CEO</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fa-solid fa-link"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Cards -->
        <div class="row">
            <div class="team-card"><img src="assets/team/women.jfif" alt="">
                <div class="team-info">
                    <h3>Zasta Maulidia</h3>
                    <p>Ilustrator</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
            <div class="team-card"><img src="assets/team/women.jfif" alt="">
                <div class="team-info">
                    <h3>Nora Resita</h3>
                    <p>Ilustrator</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
            <div class="team-card"><img src="assets/team/women.jfif" alt="">
                <div class="team-info">
                    <h3>Salma Faizatul Anwar</h3>
                    <p>Ilustrator</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
            <div class="team-card"><img src="assets/team/women.jfif" alt="">
                <div class="team-info">
                    <h3>Nadien</h3>
                    <p>Ilustrator</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
        </div>

        <!-- 3 Cards -->
        <div class="row">
            <div class="team-card"><img src="assets/team/man.jfif" alt="">
                <div class="team-info">
                    <h3>Reno</h3>
                    <p>Game Developer</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
            <div class="team-card"><img src="assets/team/man.jfif" alt="">
                <div class="team-info">
                    <h3>Alfauzan Adhim</h3>
                    <p>FrontEnd, BackEnd, & UI/UX Designer</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
            <div class="team-card"><img src="assets/team/man.jfif" alt="">
                <div class="team-info">
                    <h3>Julio</h3>
                    <p>FrontEnd & UI Designer</p>
                    <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fa-solid fa-link"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Efek animasi saat muncul (scroll atau load)
        document.addEventListener("DOMContentLoaded", () => {
            const cards = document.querySelectorAll(".team-card");

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add("visible");
                        }
                    });
                }, {
                    threshold: 0.2
                }
            );

            cards.forEach((card, index) => {
                card.classList.add("fade-slide");
                observer.observe(card);
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>

</body>

</html>
