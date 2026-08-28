<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal Kesiswaan — MTsN 1 Wonosobo</title>

    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>

<body>

    <!-- =====================================================
         NAVBAR
    ====================================================== -->

    <header class="site-header">

        <div class="navbar">

            <!-- LOGO -->
            <a href="#" class="brand" data-target="home">

                <div class="brand-logo">
                    M1
                </div>

                <div class="brand-text">
                    <strong>MTsN 1 Wonosobo</strong>
                    <span>Portal Kesiswaan</span>
                </div>

            </a>


            <!-- MOBILE BUTTON -->
            <button
                class="mobile-menu"
                type="button"
                aria-label="Buka menu"
                aria-expanded="false">
                ☰
            </button>


            <!-- NAVIGATION -->
            <nav class="main-nav">

                <button
                    type="button"
                    class="nav-link active"
                    data-target="home">
                    Beranda
                </button>

                <button
                    type="button"
                    class="nav-link"
                    data-target="konseling">
                    Konseling
                </button>

                <button
                    type="button"
                    class="nav-link"
                    data-target="ekstra">
                    Ekstra
                </button>

                <button
                    type="button"
                    class="nav-link"
                    data-target="kesiswaan">
                    Kesiswaan
                </button>

                <button
                    type="button"
                    class="nav-link"
                    data-target="akademik">
                    Akademik
                </button>

                <button
                    type="button"
                    class="nav-link"
                    data-target="pengumuman">
                    Pengumuman
                </button>

            </nav>


            <!-- RIGHT ACTION -->
            <div class="nav-actions">

                <button
                    type="button"
                    class="theme-button"
                    title="Mode tampilan">
                    ◐
                </button>

                <button
                    type="button"
                    class="login-btn">
                    Login Siswa
                </button>

            </div>

        </div>

    </header>


    <!-- =====================================================
         MAIN
    ====================================================== -->

    <main>


        <!-- =================================================
             HOME
        ================================================== -->

        <section
            class="page active"
            data-page="home">

            <div class="hero">

                <div class="hero-content">

                    <span class="eyebrow">
                        PORTAL RESMI KESISWAAN
                    </span>

                    <h1>
                        Ruang informasi dan
                        <span>aktivitas siswa.</span>
                    </h1>

                    <p>
                        Temukan informasi akademik, kegiatan
                        kesiswaan, ekstrakurikuler, konseling,
                        dan pengumuman sekolah dalam satu portal.
                    </p>

                    <div class="hero-buttons">

                        <button
                            type="button"
                            class="btn btn-primary"
                            data-target="kesiswaan">
                            Lihat Kesiswaan
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-target="pengumuman">
                            Lihat Pengumuman
                        </button>

                    </div>

                </div>


                <div class="hero-card">

                    <div class="hero-card-top">
                        <span>PORTAL SISWA</span>
                        <span class="status-dot"></span>
                    </div>

                    <div class="hero-card-icon">
                        M1
                    </div>

                    <h3>
                        MTsN 1 Wonosobo
                    </h3>

                    <p>
                        Belajar, berkembang,
                        dan berprestasi bersama.
                    </p>

                    <div class="hero-card-stats">

                        <div>
                            <strong>24/7</strong>
                            <span>Akses</span>
                        </div>

                        <div>
                            <strong>6</strong>
                            <span>Layanan</span>
                        </div>

                        <div>
                            <strong>1</strong>
                            <span>Portal</span>
                        </div>

                    </div>

                </div>

            </div>


            <!-- QUICK ACCESS -->

            <div class="container">

                <div class="section-heading">

                    <div>
                        <span class="section-label">
                            AKSES CEPAT
                        </span>

                        <h2>
                            Yang kamu butuhkan.
                        </h2>
                    </div>

                    <p>
                        Akses layanan siswa dengan lebih mudah
                        melalui portal kesiswaan.
                    </p>

                </div>


                <div class="quick-grid">

                    <button
                        class="quick-card"
                        data-target="konseling">

                        <div class="quick-icon green">
                            ♡
                        </div>

                        <div>
                            <h3>Konseling</h3>
                            <p>
                                Konsultasi bersama guru BK
                                secara nyaman.
                            </p>
                        </div>

                        <span class="arrow">→</span>

                    </button>


                    <button
                        class="quick-card"
                        data-target="ekstra">

                        <div class="quick-icon blue">
                            ★
                        </div>

                        <div>
                            <h3>Ekstrakurikuler</h3>
                            <p>
                                Temukan kegiatan dan
                                organisasi yang kamu sukai.
                            </p>
                        </div>

                        <span class="arrow">→</span>

                    </button>


                    <button
                        class="quick-card"
                        data-target="akademik">

                        <div class="quick-icon orange">
                            ▣
                        </div>

                        <div>
                            <h3>Akademik</h3>
                            <p>
                                Informasi pembelajaran,
                                jadwal, dan akademik.
                            </p>
                        </div>

                        <span class="arrow">→</span>

                    </button>


                    <button
                        class="quick-card"
                        data-target="pengumuman">

                        <div class="quick-icon purple">
                            !
                        </div>

                        <div>
                            <h3>Pengumuman</h3>
                            <p>
                                Informasi terbaru untuk
                                seluruh siswa.
                            </p>
                        </div>

                        <span class="arrow">→</span>

                    </button>

                </div>

            </div>


            <!-- INFO -->

            <div class="container">

                <div class="info-banner">

                    <div class="info-banner-icon">
                        i
                    </div>

                    <div>
                        <strong>
                            Tetap ikuti informasi terbaru.
                        </strong>

                        <p>
                            Jangan lewatkan pengumuman,
                            kegiatan sekolah, dan informasi
                            penting lainnya.
                        </p>
                    </div>

                </div>

            </div>

        </section>



        <!-- =================================================
             KONSELING
        ================================================== -->

        <section
            class="page"
            data-page="konseling">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        LAYANAN SISWA
                    </span>

                    <h1>
                        Konseling
                    </h1>

                    <p>
                        Ruang aman untuk menyampaikan cerita,
                        masalah, dan mendapatkan pendampingan.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="two-column">

                    <div class="content-card">

                        <span class="card-label">
                            KENAPA KONSELING?
                        </span>

                        <h2>
                            Kamu tidak harus menghadapi
                            semuanya sendirian.
                        </h2>

                        <p>
                            Konseling membantu siswa memahami
                            masalah, mengelola emosi, serta
                            mencari solusi yang tepat bersama
                            guru BK.
                        </p>

                        <ul class="check-list">

                            <li>
                                <span>✓</span>
                                Masalah belajar
                            </li>

                            <li>
                                <span>✓</span>
                                Masalah pertemanan
                            </li>

                            <li>
                                <span>✓</span>
                                Pengembangan diri
                            </li>

                            <li>
                                <span>✓</span>
                                Perencanaan masa depan
                            </li>

                        </ul>

                    </div>


                    <div class="form-card">

                        <div class="form-header">

                            <span class="section-label">
                                FORM KONSELING
                            </span>

                            <h2>
                                Ajukan Konsultasi
                            </h2>

                        </div>

                        <form id="counselingForm">

                            <div class="form-group">

                                <label>
                                    Nama
                                </label>

                                <input
                                    type="text"
                                    name="nama"
                                    placeholder="Masukkan nama kamu"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>
                                    Kelas
                                </label>

                                <input
                                    type="text"
                                    name="kelas"
                                    placeholder="Contoh: IX A"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>
                                    Hal yang ingin dikonsultasikan
                                </label>

                                <textarea
                                    name="masalah"
                                    rows="5"
                                    placeholder="Ceritakan secara singkat..."
                                    required></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary full">
                                Kirim Pengajuan
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </section>



        <!-- =================================================
             EKSTRA
        ================================================== -->

        <section
            class="page"
            data-page="ekstra">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        KEGIATAN SISWA
                    </span>

                    <h1>
                        Ekstrakurikuler
                    </h1>

                    <p>
                        Kembangkan minat, bakat, kreativitas,
                        dan kemampuanmu di luar pembelajaran.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="filter-row">

                    <input
                        type="search"
                        placeholder="Cari ekstrakurikuler...">

                    <div class="filter-buttons">

                        <button
                            class="filter active"
                            data-filter="all">
                            Semua
                        </button>

                        <button
                            class="filter"
                            data-filter="olahraga">
                            Olahraga
                        </button>

                        <button
                            class="filter"
                            data-filter="seni">
                            Seni
                        </button>

                        <button
                            class="filter"
                            data-filter="organisasi">
                            Organisasi
                        </button>

                    </div>

                </div>


                <div class="extra-grid">


                    <article
                        class="extra-card"
                        data-category="olahraga">

                        <div class="extra-icon">
                            ⚽
                        </div>

                        <span class="extra-category">
                            OLAHRAGA
                        </span>

                        <h3>
                            Futsal
                        </h3>

                        <p>
                            Melatih kemampuan bermain,
                            kerja sama, dan sportivitas.
                        </p>

                        <span class="extra-link">
                            Lihat kegiatan →
                        </span>

                    </article>


                    <article
                        class="extra-card"
                        data-category="olahraga">

                        <div class="extra-icon">
                            🏀
                        </div>

                        <span class="extra-category">
                            OLAHRAGA
                        </span>

                        <h3>
                            Basket
                        </h3>

                        <p>
                            Mengembangkan kemampuan teknik
                            dan kerja sama tim.
                        </p>

                        <span class="extra-link">
                            Lihat kegiatan →
                        </span>

                    </article>


                    <article
                        class="extra-card"
                        data-category="seni">

                        <div class="extra-icon">
                            🎨
                        </div>

                        <span class="extra-category">
                            SENI
                        </span>

                        <h3>
                            Seni
                        </h3>

                        <p>
                            Wadah untuk mengekspresikan
                            kreativitas dan bakat.
                        </p>

                        <span class="extra-link">
                            Lihat kegiatan →
                        </span>

                    </article>


                    <article
                        class="extra-card"
                        data-category="organisasi">

                        <div class="extra-icon">
                            ★
                        </div>

                        <span class="extra-category">
                            ORGANISASI
                        </span>

                        <h3>
                            OSIM
                        </h3>

                        <p>
                            Belajar kepemimpinan, organisasi,
                            dan tanggung jawab.
                        </p>

                        <span class="extra-link">
                            Lihat kegiatan →
                        </span>

                    </article>


                    <article
                        class="extra-card"
                        data-category="seni">

                        <div class="extra-icon">
                            🎵
                        </div>

                        <span class="extra-category">
                            SENI
                        </span>

                        <h3>
                            Musik
                        </h3>

                        <p>
                            Mengembangkan kemampuan musik
                            dan kreativitas siswa.
                        </p>

                        <span class="extra-link">
                            Lihat kegiatan →
                        </span>

                    </article>


                    <article
                        class="extra-card"
                        data-category="organisasi">

                        <div class="extra-icon">
                            📚
                        </div>

                        <span class="extra-category">
                            ORGANISASI
                        </span>

                        <h3>
                            PMR
                        </h3>

                        <p>
                            Belajar kepedulian, kesehatan,
                            dan kegiatan sosial.
                        </p>

                        <span class="extra-link">
                            Lihat kegiatan →
                        </span>

                    </article>

                </div>

            </div>

        </section>



        <!-- =================================================
             KESISWAAN
        ================================================== -->

        <section
            class="page"
            data-page="kesiswaan">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        INFORMASI SISWA
                    </span>

                    <h1>
                        Kesiswaan
                    </h1>

                    <p>
                        Informasi kegiatan, organisasi,
                        dan layanan siswa.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="feature-grid">

                    <article class="feature-card">

                        <div class="feature-number">
                            01
                        </div>

                        <h3>
                            Organisasi Siswa
                        </h3>

                        <p>
                            Wadah bagi siswa untuk belajar
                            berorganisasi dan memimpin.
                        </p>

                    </article>


                    <article class="feature-card">

                        <div class="feature-number">
                            02
                        </div>

                        <h3>
                            Kegiatan Sekolah
                        </h3>

                        <p>
                            Berbagai kegiatan yang mendukung
                            kreativitas dan pengalaman siswa.
                        </p>

                    </article>


                    <article class="feature-card">

                        <div class="feature-number">
                            03
                        </div>

                        <h3>
                            Prestasi
                        </h3>

                        <p>
                            Informasi dan apresiasi terhadap
                            prestasi siswa.
                        </p>

                    </article>


                    <article class="feature-card">

                        <div class="feature-number">
                            04
                        </div>

                        <h3>
                            Tata Tertib
                        </h3>

                        <p>
                            Informasi mengenai aturan dan
                            kedisiplinan siswa.
                        </p>

                    </article>

                </div>

            </div>

        </section>



        <!-- =================================================
             AKADEMIK
        ================================================== -->

        <section
            class="page"
            data-page="akademik">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        PEMBELAJARAN
                    </span>

                    <h1>
                        Akademik
                    </h1>

                    <p>
                        Informasi yang membantu perjalanan
                        belajar siswa.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="academic-list">

                    <div class="academic-item">

                        <div class="academic-icon">
                            📅
                        </div>

                        <div>
                            <span>
                                INFORMASI
                            </span>

                            <h3>
                                Jadwal Pelajaran
                            </h3>

                            <p>
                                Informasi jadwal pembelajaran
                                siswa.
                            </p>
                        </div>

                        <span class="arrow">
                            →
                        </span>

                    </div>


                    <div class="academic-item">

                        <div class="academic-icon">
                            📝
                        </div>

                        <div>
                            <span>
                                PENILAIAN
                            </span>

                            <h3>
                                Ujian dan Penilaian
                            </h3>

                            <p>
                                Informasi mengenai jadwal
                                ujian dan penilaian.
                            </p>
                        </div>

                        <span class="arrow">
                            →
                        </span>

                    </div>


                    <div class="academic-item">

                        <div class="academic-icon">
                            📖
                        </div>

                        <div>
                            <span>
                                PEMBELAJARAN
                            </span>

                            <h3>
                                Materi Pembelajaran
                            </h3>

                            <p>
                                Akses informasi dan materi
                                pembelajaran.
                            </p>
                        </div>

                        <span class="arrow">
                            →
                        </span>

                    </div>

                </div>

            </div>

        </section>



        <!-- =================================================
             PENGUMUMAN
        ================================================== -->

        <section
            class="page"
            data-page="pengumuman">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        INFORMASI TERBARU
                    </span>

                    <h1>
                        Pengumuman
                    </h1>

                    <p>
                        Informasi terbaru yang perlu diketahui
                        oleh seluruh siswa.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="announcement-list">

                    <article class="announcement-card">

                        <div class="announcement-date">

                            <strong>
                                20
                            </strong>

                            <span>
                                AGU
                            </span>

                        </div>

                        <div>

                            <span class="announcement-label">
                                SEKOLAH
                            </span>

                            <h3>
                                Informasi kegiatan siswa
                            </h3>

                            <p>
                                Pantau informasi kegiatan sekolah
                                melalui portal kesiswaan.
                            </p>

                        </div>

                    </article>


                    <article class="announcement-card">

                        <div class="announcement-date">

                            <strong>
                                18
                            </strong>

                            <span>
                                AGU
                            </span>

                        </div>

                        <div>

                            <span class="announcement-label">
                                AKADEMIK
                            </span>

                            <h3>
                                Informasi pembelajaran
                            </h3>

                            <p>
                                Siswa diharapkan memperhatikan
                                informasi akademik terbaru.
                            </p>

                        </div>

                    </article>


                    <article class="announcement-card">

                        <div class="announcement-date">

                            <strong>
                                15
                            </strong>

                            <span>
                                AGU
                            </span>

                        </div>

                        <div>

                            <span class="announcement-label">
                                KESISWAAN
                            </span>

                            <h3>
                                Pengumuman kegiatan ekstrakurikuler
                            </h3>

                            <p>
                                Informasi mengenai kegiatan
                                ekstrakurikuler tersedia di portal.
                            </p>

                        </div>

                    </article>

                </div>

            </div>

        </section>

    </main>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="site-footer">

        <div class="container footer-grid">

            <div>

                <div class="footer-brand">
                    MTsN 1 Wonosobo
                </div>

                <p>
                    Portal Kesiswaan — layanan informasi
                    dan aktivitas siswa.
                </p>

            </div>


            <div>

                <h4>
                    Navigasi
                </h4>

                <button data-target="home">
                    Beranda
                </button>

                <button data-target="konseling">
                    Konseling
                </button>

                <button data-target="ekstra">
                    Ekstra
                </button>

            </div>


            <div>

                <h4>
                    Informasi
                </h4>

                <button data-target="kesiswaan">
                    Kesiswaan
                </button>

                <button data-target="akademik">
                    Akademik
                </button>

                <button data-target="pengumuman">
                    Pengumuman
                </button>

            </div>

        </div>


        <div class="container footer-bottom">

            <span>
                © {{ date('Y') }} MTsN 1 Wonosobo
            </span>

            <span>
                Portal Kesiswaan
            </span>

        </div>

    </footer>



    <!-- =====================================================
         LOGIN MODAL
    ====================================================== -->

    <div
        class="modal"
        id="loginModal">

        <div class="modal-box">

            <button
                type="button"
                class="modal-close"
                aria-label="Tutup">
                ×
            </button>


            <div class="modal-logo">
                M1
            </div>

            <span class="section-label">
                PORTAL SISWA
            </span>

            <h2>
                Login Siswa
            </h2>

            <p>
                Masuk untuk mengakses layanan siswa.
            </p>


            <form id="loginForm">

                <div class="form-group">

                    <label>
                        NIS
                    </label>

                    <input
                        type="text"
                        name="nis"
                        placeholder="Masukkan NIS"
                        required>

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary full">
                    Masuk
                </button>

            </form>

        </div>

    </div>



    <!-- JAVASCRIPT -->

    <script src="{{ asset('js/script1.js') }}"></script>

</body>
</html>