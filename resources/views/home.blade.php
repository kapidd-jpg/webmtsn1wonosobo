<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo MTsN 1 Wonosobo">
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


          <div class="nav-actions">

                @guest
                    <button type="button" class="login-btn" id="loginTrigger">
                        Login
                    </button>
                @endguest

                @auth
                    <button
                        type="button"
                        class="profile-btn"
                        id="profileTrigger"
                        data-target="{{ auth()->user()->role === 'siswa' ? 'profil' : 'guru-dashboard' }}"
                        title="Profil">
                        <span class="profile-avatar">
                            @if (auth()->user()->role === 'admin')
                                AD
                            @elseif (auth()->user()->role === 'guru_bk')
                                BK
                            @elseif (auth()->user()->role === 'guru')
                                GR
                            @else
                                AS
                            @endif
                        </span>
                    </button>
                @endauth

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

                    <img
                        src="{{ asset('images/hero/beranda.jpg') }}"
                        alt="Siswa MTsN 1 Wonosobo">

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

            <div class="page-hero" style="background-image: url('{{ asset('images/hero/konseling.jpg') }}')">

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

            <div class="page-hero" style="background-image: url('{{ asset('images/hero/ekstrakurikuler.jpg') }}')">

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


                <div class="extra-grid" id="extraGrid"></div>

            </div>

        </section>



        <!-- =================================================
             KESISWAAN
        ================================================== -->

        <section
            class="page"
            data-page="kesiswaan">

            <div class="page-hero" style="background-image: url('{{ asset('images/hero/kesiswaan.jpg') }}')">

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

                <div class="feature-grid" id="kesiswaanGrid"></div>

            </div>

        </section>



        <!-- =================================================
             AKADEMIK
        ================================================== -->

        <section
            class="page"
            data-page="akademik">

            <div class="page-hero" style="background-image: url('{{ asset('images/hero/akademik.jpg') }}')">

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

                    <div class="academic-item" data-target="jadwal">

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


                    <div class="academic-item" data-target="ujian">

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


                    <div class="academic-item" data-target="materi">

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

            <div class="page-hero" style="background-image: url('{{ asset('images/hero/pengumuman.jpg') }}')">

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

                <div class="announcement-filter" id="announcementFilter"></div>

                <div class="announcement-list" id="announcementList"></div>

            </div>

        </section>



        <!-- =================================================
             PROFIL (Dashboard Siswa — muncul setelah login)
        ================================================== -->

                <section
            class="page"
            data-page="profil">

@auth
@if (auth()->user()->role === 'siswa')

            <div class="page-hero">

                <div class="container profile-hero-row">

                    <div class="profile-hero-avatar">
                        AS
                    </div>

                    <div>
                        <span class="section-label">
                            DASHBOARD SISWA
                        </span>

                        <h1 id="profileGreeting">
                            Halo, {{ auth()->user()->name }}
                        </h1>

                        <p>
                            Kelas {{ auth()->user()->kelas ?? '-' }} · NIS {{ auth()->user()->nis_nip }}
                        </p>
                    </div>

                </div>

            </div>


            <div class="container page-content">

                <div class="profile-stats-grid">

                    <div class="profile-stat-card">
                        <span class="profile-stat-label">Kehadiran</span>
                        <strong class="profile-stat-value">{{ auth()->user()->kehadiran_persen !== null ? auth()->user()->kehadiran_persen . '%' : '-' }}</strong>
                        <span class="profile-stat-note">Bulan ini</span>
                    </div>

                    <div class="profile-stat-card">
                        <span class="profile-stat-label">Rata-rata Nilai</span>
                        <strong class="profile-stat-value">{{ auth()->user()->rata_rata_nilai ?? '-' }}</strong>
                        <span class="profile-stat-note">Semester ini</span>
                    </div>

                    <div class="profile-stat-card">
                        <span class="profile-stat-label">Ekstrakurikuler</span>
                        <strong class="profile-stat-value">{{ auth()->user()->extracurriculars->count() }}</strong>
                        <span class="profile-stat-note">Aktif diikuti</span>
                    </div>

                    <div class="profile-stat-card">
                        <span class="profile-stat-label">Poin Prestasi</span>
                        <strong class="profile-stat-value">{{ auth()->user()->poin_prestasi ?? 0 }}</strong>
                        <span class="profile-stat-note">Total terkumpul</span>
                    </div>

                </div>


                <div class="two-column">

                    <div class="content-card">

                        <span class="card-label">
                            DATA SISWA
                        </span>

                        <h2>
                            Informasi Pribadi
                        </h2>

                        <ul class="profile-detail-list">

                          <li>
                                <span>Nama Lengkap</span>
                                <strong>{{ auth()->user()->name }}</strong>
                            </li>

                            <li>
                                <span>NIS</span>
                                <strong id="profileNis">{{ auth()->user()->nis_nip }}</strong>
                            </li>

                            <li>
                                <span>Kelas</span>
                                <strong>{{ auth()->user()->kelas ?? '-' }}</strong>
                            </li>

                            <li>
                                <span>Wali Kelas</span>
                                <strong>{{ auth()->user()->wali_kelas ?? '-' }}</strong>
                            </li>

                            <li>
                                <span>Status</span>
                                <strong>Aktif</strong>
                            </li>

                        </ul>

                    </div>


                    <div class="content-card">

                        <span class="card-label">
                            EKSTRAKURIKULER
                        </span>

                        <h2>
                            Sedang Diikuti
                        </h2>

                        <ul class="check-list">

                            @forelse (auth()->user()->extracurriculars as $extra)
                                <li>
                                    <span>{{ $extra->icon }}</span>
                                    {{ $extra->judul }}
                                </li>
                            @empty
                                <li>
                                    <span>—</span>
                                    Belum mengikuti ekstrakurikuler apa pun.
                                </li>
                            @endforelse

                        </ul>

                        <form method="POST" action="{{ route('logout') }}" style="margin-top: 24px;">
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-secondary full profile-logout-btn">
                                Keluar Akun
                            </button>
                        </form>

                    </div>

                        </div>

                        </div>

                    @endif
                    @endauth

                    </section>



        <!-- =================================================
             JADWAL PELAJARAN (publik)
        ================================================== -->

        <section
            class="page"
            data-page="jadwal">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        AKADEMIK
                    </span>

                    <h1>
                        Jadwal Pelajaran
                    </h1>

                    <p>
                        Jadwal pelajaran terbaru yang dikelola
                        oleh guru dan staf sekolah.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="schedule-table-wrap">

                    <table class="schedule-table">

                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                            </tr>
                        </thead>

                        <tbody id="scheduleTableBody">
                        </tbody>

                    </table>

                </div>

            </div>

        </section>



        <!-- =================================================
             UJIAN DAN PENILAIAN (publik)
        ================================================== -->

        <section
            class="page"
            data-page="ujian">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        AKADEMIK
                    </span>

                    <h1>
                        Ujian dan Penilaian
                    </h1>

                    <p>
                        Jadwal ujian dan penilaian yang perlu
                        dipersiapkan siswa.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="exam-summary" id="examSummary"></div>

                <div class="exam-grid" id="examList"></div>

            </div>

        </section>



        <!-- =================================================
             MATERI PEMBELAJARAN (publik)
        ================================================== -->

        <section
            class="page"
            data-page="materi">

            <div class="page-hero">

                <div class="container">

                    <span class="section-label">
                        AKADEMIK
                    </span>

                    <h1>
                        Materi Pembelajaran
                    </h1>

                    <p>
                        Akses materi pembelajaran yang dibagikan
                        oleh guru.
                    </p>

                </div>

            </div>


            <div class="container page-content">

                <div class="material-grid" id="materialList"></div>

            </div>

        </section>

        <!-- =================================================
             DASHBOARD GURU (admin — kelola konten portal)
        ================================================== -->

        @auth
        @if (auth()->user()->role !== 'siswa')
        <section
            class="page"
            data-page="guru-dashboard">

            <div class="page-hero">

                <div class="container profile-hero-row" style="justify-content: space-between; align-items: center;">

                    <div style="display: flex; align-items: center; gap: 20px;">

                        <div class="profile-hero-avatar">
                            @if (auth()->user()->role === 'admin')
                                AD
                            @elseif (auth()->user()->role === 'guru_bk')
                                BK
                            @else
                                GR
                            @endif
                        </div>

                        <div>
                            <span class="section-label">
                                @if (auth()->user()->role === 'admin')
                                    DASHBOARD ADMIN
                                @elseif (auth()->user()->role === 'guru_bk')
                                    DASHBOARD GURU BK
                                @else
                                    DASHBOARD GURU
                                @endif
                            </span>

                            <h1>
                                @if (auth()->user()->role === 'admin')
                                    Panel Administrasi Sistem
                                @elseif (auth()->user()->role === 'guru_bk')
                                    Panel Layanan Konseling
                                @else
                                    Panel Pengelolaan Portal
                                @endif
                            </h1>

                            <p>
                                @if (auth()->user()->role === 'admin')
                                    Kelola seluruh konten portal dan akun pengguna.
                                @elseif (auth()->user()->role === 'guru_bk')
                                    Kelola pengajuan konseling siswa.
                                @else
                                    Kelola pengumuman, jadwal pelajaran,
                                    dan ekstrakurikuler.
                                @endif
                            </p>
                        </div>

                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="btn btn-secondary">
                            Keluar Akun
                        </button>
                    </form>

                </div>

            </div>


            <div class="container page-content">

                <div class="admin-tabs">

                    @if (auth()->user()->canManageContent())
                        <button
                            type="button"
                            class="admin-tab {{ auth()->user()->role !== 'guru_bk' ? 'active' : '' }}"
                            data-admin-tab="pengumuman">
                            Pengumuman
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="jadwal">
                            Jadwal Pelajaran
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="ekstra">
                            Ekstrakurikuler
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="kesiswaan">
                            Kesiswaan
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="ujian">
                            Ujian
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="materi">
                            Materi
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="siswa">
                            Kelola Siswa
                        </button>

                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="profil-sekolah">
                            Profil Sekolah
                        </button>
                    @endif

                    @if (auth()->user()->canManageKonseling())
                        <button
                            type="button"
                            class="admin-tab {{ auth()->user()->role === 'guru_bk' ? 'active' : '' }}"
                            data-admin-tab="konseling">
                            Konseling
                        </button>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <button
                            type="button"
                            class="admin-tab"
                            data-admin-tab="buat-akun">
                            Buat Akun
                        </button>
                    @endif

                </div>


                @if (auth()->user()->canManageContent())
                <!-- PANEL: PENGUMUMAN -->
                <div class="admin-panel {{ auth()->user()->role !== 'guru_bk' ? 'active' : '' }}" data-admin-panel="pengumuman">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">FORM PENGUMUMAN</span>
                                <h2 id="pengumumanFormTitle">Tambah Pengumuman</h2>
                            </div>

                            <form id="pengumumanForm">

                                <input type="hidden" name="id">

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" name="tanggal" placeholder="Contoh: 25" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Bulan</label>
                                        <input type="text" name="bulan" placeholder="Contoh: AGU" required maxlength="3">
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Kategori</label>
                                    <input type="text" name="kategori" placeholder="Contoh: SEKOLAH" required>
                                </div>

                                <div class="form-group">
                                    <label>Judul</label>
                                    <input type="text" name="judul" placeholder="Judul pengumuman" required>
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" rows="3" placeholder="Isi pengumuman singkat" required></textarea>
                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="pengumumanCancelEdit" hidden>Batal Edit</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR PENGUMUMAN</span>
                            <h2>Semua Pengumuman</h2>
                            <ul class="admin-list" id="pengumumanAdminList"></ul>
                        </div>

                    </div>

                </div>


                <!-- PANEL: JADWAL -->
                <div class="admin-panel" data-admin-panel="jadwal">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">FORM JADWAL</span>
                                <h2 id="jadwalFormTitle">Tambah Jadwal</h2>
                            </div>

                            <form id="jadwalForm">

                                <input type="hidden" name="id">

                                <div class="form-group">
                                    <label>Hari</label>
                                    <select name="hari" required>
                                        <option value="">Pilih hari</option>
                                        <option>Senin</option>
                                        <option>Selasa</option>
                                        <option>Rabu</option>
                                        <option>Kamis</option>
                                        <option>Jumat</option>
                                        <option>Sabtu</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Jam</label>
                                    <input type="text" name="jam" placeholder="Contoh: 07:00 - 08:30" required>
                                </div>

                                <div class="form-group">
                                    <label>Mata Pelajaran</label>
                                    <input type="text" name="mapel" placeholder="Contoh: Matematika" required>
                                </div>

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Kelas</label>
                                        <input type="text" name="kelas" placeholder="Contoh: IX A" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Guru Pengampu</label>
                                        <input type="text" name="guru" placeholder="Nama guru" required>
                                    </div>

                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="jadwalCancelEdit" hidden>Batal Edit</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR JADWAL</span>
                            <h2>Semua Jadwal</h2>
                            <ul class="admin-list" id="jadwalAdminList"></ul>
                        </div>

                    </div>

                </div>


                <!-- PANEL: EKSTRAKURIKULER -->
                <div class="admin-panel" data-admin-panel="ekstra">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">FORM EKSTRAKURIKULER</span>
                                <h2 id="ekstraFormTitle">Tambah Ekstrakurikuler</h2>
                            </div>

                            <form id="ekstraForm">

                                <input type="hidden" name="id">

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Ikon (emoji)</label>
                                        <input type="text" name="icon" placeholder="Contoh: ⚽" maxlength="4" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select name="kategori" required>
                                            <option value="olahraga">Olahraga</option>
                                            <option value="seni">Seni</option>
                                            <option value="organisasi">Organisasi</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Nama Kegiatan</label>
                                    <input type="text" name="judul" placeholder="Contoh: Futsal" required>
                                </div>

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Jadwal</label>
                                        <input type="text" name="jadwal" placeholder="Contoh: Selasa, 15.30 - 17.00" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Lokasi</label>
                                        <input type="text" name="lokasi" placeholder="Contoh: Lapangan Sekolah" required>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat kegiatan" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Banner Gambar</label>
                                    <input type="file" name="gambar" accept="image/png,image/jpeg,image/webp">
                                    <input type="hidden" name="gambar_pilihan" id="ekstraGambarPilihan">
                                    <img id="ekstraImagePreview" src="" alt="" style="display:none; margin-top:10px; max-width:100%; border-radius:8px;">
                                    <p class="form-hint" id="ekstraGambarNote" style="margin-top:8px;">Biarkan kosong untuk memakai banner bawaan.</p>
                                    <button type="button" class="btn btn-secondary" id="ekstraHapusGambar" hidden>Hapus Banner</button>
                                </div>

                                <div class="form-group">
                                    <label>Atau pilih banner yang sudah ada</label>
                                    <input type="search" id="ekstraImageSearch" class="image-picker-search" placeholder="Cari banner: voli, robotika, osis …" autocomplete="off">
                                    <div class="image-picker" id="ekstraImagePicker">
                                        <span class="image-picker-empty">Memuat daftar banner…</span>
                                    </div>
                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="ekstraCancelEdit" hidden>Batal Edit</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR EKSTRAKURIKULER</span>
                            <h2>Semua Kegiatan</h2>
                            <ul class="admin-list" id="ekstraAdminList"></ul>
                        </div>

                    </div>

                </div>


                <!-- PANEL: KESISWAAN -->
                <div class="admin-panel" data-admin-panel="kesiswaan">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">FORM KESISWAAN</span>
                                <h2 id="kesiswaanFormTitle">Tambah Konten Kesiswaan</h2>
                            </div>

                           <form id="kesiswaanForm">

                                <input type="hidden" name="id">

                                <div class="form-group">
                                    <label>Judul</label>
                                    <input type="text" name="judul" placeholder="Contoh: Organisasi Siswa" required>
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Urutan Tampil</label>
                                    <input type="number" name="urutan" placeholder="Contoh: 1" min="1">
                                </div>

                                <div class="form-group">
                                    <label>Foto (opsional)</label>
                                    <input type="file" name="gambar" accept="image/*">
                                    <img id="kesiswaanImagePreview" src="" alt="" style="display:none; margin-top:10px; max-width:160px; border-radius:8px;">
                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="kesiswaanCancelEdit" hidden>Batal Edit</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR KONTEN</span>
                            <h2>Semua Konten Kesiswaan</h2>
                            <ul class="admin-list" id="kesiswaanAdminList"></ul>
                        </div>

                    </div>

                </div>


                <!-- PANEL: UJIAN -->
                <div class="admin-panel" data-admin-panel="ujian">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">FORM UJIAN</span>
                                <h2 id="ujianFormTitle">Tambah Jadwal Ujian</h2>
                            </div>

                            <form id="ujianForm">

                                <input type="hidden" name="id">

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Tanggal</label>
                                        <input type="text" name="tanggal" placeholder="Contoh: 10 Oktober" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Jam</label>
                                        <input type="text" name="jam" placeholder="Contoh: 07:00 - 08:30" required>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Mata Pelajaran</label>
                                    <input type="text" name="mapel" placeholder="Contoh: Matematika" required>
                                </div>

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Kelas</label>
                                        <input type="text" name="kelas" placeholder="Contoh: IX A" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Jenis</label>
                                        <select name="jenis" required>
                                            <option value="">Pilih jenis</option>
                                            <option>Ulangan Harian</option>
                                            <option>UTS</option>
                                            <option>UAS</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Keterangan (opsional)</label>
                                    <textarea name="keterangan" rows="3" placeholder="Contoh: Materi Bab 1-4"></textarea>
                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="ujianCancelEdit" hidden>Batal Edit</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR UJIAN</span>
                            <h2>Semua Jadwal Ujian</h2>
                            <ul class="admin-list" id="ujianAdminList"></ul>
                        </div>

                    </div>

                </div>


                <!-- PANEL: MATERI -->
                <div class="admin-panel" data-admin-panel="materi">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">FORM MATERI</span>
                                <h2 id="materiFormTitle">Tambah Materi</h2>
                            </div>

                            <form id="materiForm">

                                <input type="hidden" name="id">

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Mata Pelajaran</label>
                                        <input type="text" name="mapel" placeholder="Contoh: Matematika" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Kelas</label>
                                        <input type="text" name="kelas" placeholder="Contoh: IX A" required>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Judul Materi</label>
                                    <input type="text" name="judul" placeholder="Contoh: Bilangan Bulat" required>
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat materi" required></textarea>
                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="materiCancelEdit" hidden>Batal Edit</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR MATERI</span>
                            <h2>Semua Materi</h2>
                            <ul class="admin-list" id="materiAdminList"></ul>
                        </div>

                    </div>

                </div>
                @endif


                @if (auth()->user()->canManageKonseling())
                <!-- PANEL: KONSELING -->
                <div class="admin-panel {{ auth()->user()->role === 'guru_bk' ? 'active' : '' }}" data-admin-panel="konseling">

                    <div class="content-card">
                        <span class="card-label">PENGAJUAN MASUK</span>
                        <h2>Daftar Pengajuan Konseling</h2>
                        <ul class="admin-list" id="konselingAdminList"></ul>
                    </div>

                </div>
                @endif


                @if (auth()->user()->canManageContent())
                <!-- PANEL: KELOLA SISWA -->
                <div class="admin-panel" data-admin-panel="siswa">

                    <div class="two-column">

                        <div class="form-card">

                            <div class="form-header">
                                <span class="section-label">DATA SISWA</span>
                                <h2 id="siswaFormTitle">Pilih Siswa untuk Diedit</h2>
                            </div>

                            <form id="siswaForm">

                                <input type="hidden" name="id">

                                <div class="form-group">
                                    <label>Kelas</label>
                                    <input type="text" name="kelas" placeholder="Contoh: IX A">
                                </div>

                                <div class="form-group">
                                    <label>Wali Kelas</label>
                                    <input type="text" name="wali_kelas" placeholder="Contoh: Ibu Siti Rahmawati, S.Pd.">
                                </div>

                                <div class="admin-form-row">

                                    <div class="form-group">
                                        <label>Kehadiran (%)</label>
                                        <input type="number" name="kehadiran_persen" min="0" max="100" placeholder="Contoh: 96">
                                    </div>

                                    <div class="form-group">
                                        <label>Rata-rata Nilai</label>
                                        <input type="number" name="rata_rata_nilai" min="0" max="100" placeholder="Contoh: 87">
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Poin Prestasi</label>
                                    <input type="number" name="poin_prestasi" min="0" placeholder="Contoh: 120">
                                </div>

                                <div class="form-group">
                                    <label>Ekstrakurikuler yang Diikuti</label>
                                    <div id="siswaEkstraCheckboxes" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;"></div>
                                </div>

                                <div class="admin-form-actions">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>

                            </form>

                        </div>


                        <div class="content-card">
                            <span class="card-label">DAFTAR SISWA</span>
                            <h2>Semua Siswa</h2>
                            <ul class="admin-list" id="siswaAdminList"></ul>
                        </div>

                    </div>

                </div>


                <!-- PANEL: PROFIL SEKOLAH -->
                <div class="admin-panel" data-admin-panel="profil-sekolah">

                    <div class="form-card">

                        <div class="form-header">
                            <span class="section-label">IDENTITAS MADRASAH</span>
                            <h2>Kelola Profil Sekolah</h2>
                        </div>

                        <form id="schoolProfileForm">

                            <div class="admin-form-row">

                                <div class="form-group">
                                    <label>Nama Sekolah</label>
                                    <input type="text" name="nama_sekolah" placeholder="Contoh: MTsN 1 Wonosobo">
                                </div>

                                <div class="form-group">
                                    <label>Kepala Sekolah</label>
                                    <input type="text" name="kepala_sekolah" placeholder="Contoh: Mochamad Abdul Malik, M.Ag.">
                                </div>

                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" name="alamat" placeholder="Contoh: Jl. Banyumas KM.04, Wonorejo, Selomerto, Wonosobo">
                            </div>

                            <div class="admin-form-row">

                                <div class="form-group">
                                    <label>Telepon</label>
                                    <input type="text" name="telepon" placeholder="Contoh: 0286-xxxxxx">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" placeholder="Contoh: info@mtsn1wonosobo.sch.id">
                                </div>

                            </div>

                            <div class="admin-form-row">

                                <div class="form-group">
                                    <label>Tahun Berdiri</label>
                                    <input type="text" name="tahun_berdiri" placeholder="Contoh: 1978">
                                </div>

                                <div class="form-group">
                                    <label>Akreditasi</label>
                                    <input type="text" name="akreditasi" placeholder="Contoh: A">
                                </div>

                            </div>

                            <div class="form-group">
                                <label>Visi</label>
                                <textarea name="visi" rows="3" placeholder="Visi sekolah"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Misi</label>
                                <textarea name="misi" rows="4" placeholder="Misi sekolah"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Sejarah Singkat</label>
                                <textarea name="sejarah_singkat" rows="4" placeholder="Sejarah singkat berdirinya sekolah"></textarea>
                            </div>

                            <div class="admin-form-actions">
                                <button type="submit" class="btn btn-primary">Simpan Profil Sekolah</button>
                            </div>

                        </form>

                    </div>

                </div>
                @endif


                @if (auth()->user()->role === 'admin')
                <!-- PANEL: BUAT AKUN (khusus admin) -->
                <div class="admin-panel" data-admin-panel="buat-akun">

                    <div class="content-card">

                        <span class="card-label">AKUN BARU</span>
                        <h2>Buat Akun Siswa / Guru / Guru BK</h2>

                        <form id="createUserForm" style="margin-top: 20px;">

                            <div class="admin-form-row">

                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name" placeholder="Contoh: Budi Santoso" required>
                                </div>

                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" id="createUserRole" required>
                                        <option value="siswa">Siswa</option>
                                        <option value="guru">Guru</option>
                                        <option value="guru_bk">Guru BK</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                            </div>

                            <div class="admin-form-row">

                                <div class="form-group">
                                    <label id="createUserNisNipLabel">NIS</label>
                                    <input type="text" name="nis_nip" placeholder="Contoh: 20240002" required>
                                </div>

                                <div class="form-group" id="createUserKelasGroup">
                                    <label>Kelas</label>
                                    <input type="text" name="kelas" placeholder="Contoh: IX B">
                                </div>

                            </div>

                            <div class="form-group">
                                <label>Password Awal</label>
                                <input type="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                            </div>

                            <div class="admin-form-actions">
                                <button type="submit" class="btn btn-primary">Buat Akun</button>
                            </div>

                        </form>

                    </div>

                </div>
                @endif

            </div>

        </section>
        
        @endif
        @endauth

    </main>




    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="site-footer">

        <div class="container footer-grid">

            <div>

                <h4>
                    Tentang
                </h4>

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
    class="modal {{ $errors->has('nis_nip') ? 'open' : '' }}"
    id="loginModal">

        <div class="modal-box">

            <button
                type="button"
                class="modal-close"
                aria-label="Tutup">
                ×
            </button>


            <div class="modal-logo">
                <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo MTsN 1 Wonosobo">
            </div>

            <span class="section-label">
                PORTAL SISWA
            </span>

            <h2>
                Login
            </h2>

            <p>
                Masuk untuk mengakses layanan siswa.
            </p>
    

                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">

                    <label>
                        NIS / NIP
                    </label>

                    <input
                        type="text"
                        name="nis_nip"
                        value="{{ old('nis_nip') }}"
                        placeholder="Masukkan NIS (siswa) atau NIP (guru)"
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

                @error('nis_nip')
                    <div class="form-note show error">{{ $message }}</div>
                @enderror

                <button
                    type="submit"
                    class="btn btn-primary full">
                    Masuk
                </button>

            </form>

        </div>

    </div>



    <!-- =====================================================
         MODAL DETAIL EKSTRAKURIKULER
    ====================================================== -->

    <div
        class="modal"
        id="extraDetailModal">

        <div class="modal-box extra-detail-box">

            <button
                type="button"
                class="modal-close"
                aria-label="Tutup">
                ×
            </button>

            <div class="extra-detail-banner">
                <img
                    id="extraDetailBanner"
                    alt="">
            </div>

            <div class="extra-detail-icon" id="extraDetailIcon"></div>

            <span class="section-label" id="extraDetailCategory">
            </span>

            <h2 id="extraDetailTitle">
            </h2>

            <p id="extraDetailDesc">
            </p>

            <div class="extra-detail-meta">

                <div class="extra-detail-meta-row">
                    <span class="extra-detail-meta-icon">🕐</span>
                    <span id="extraDetailJadwal"></span>
                </div>

                <div class="extra-detail-meta-row">
                    <span class="extra-detail-meta-icon">📍</span>
                    <span id="extraDetailLokasi"></span>
                </div>

            </div>

        </div>

    </div>



    <!-- =====================================================
         STELA AI ASSISTANT (chat widget — muncul di semua halaman)
         Catatan backend: balasan STELA diambil dari endpoint
         POST /api/stela (lihat TODO di script1.js bagian 8).
    ====================================================== -->

    <button
        type="button"
        class="stela-fab"
        id="stelaTrigger"
        aria-label="Buka STELA AI"
        aria-expanded="false">
        <span class="stela-fab-icon" aria-hidden="true">🤖</span>
    </button>

    <div class="stela-panel" id="stelaPanel" role="dialog" aria-label="STELA AI Assistant">

        <div class="stela-header">

            <div class="stela-header-info">

                <span class="stela-avatar" aria-hidden="true">
                    🤖
                </span>

                <div>
                    <strong>STELA</strong>
                    <span>Asisten AI Portal Kesiswaan</span>
                </div>

            </div>

            <button
                type="button"
                class="stela-close"
                id="stelaClose"
                aria-label="Tutup STELA">
                ×
            </button>

        </div>


        <div class="stela-messages" id="stelaMessages">
            <!-- pesan STELA dirender lewat script1.js -->
        </div>


        <form class="stela-input-row" id="stelaForm">

            <input
                type="text"
                name="message"
                id="stelaInput"
                placeholder="Tanya STELA..."
                autocomplete="off"
                required>

            <button
                type="submit"
                class="stela-send"
                aria-label="Kirim pesan ke STELA">
                ➤
            </button>

        </form>

    </div>



    <!-- JAVASCRIPT -->

    <script src="{{ asset('js/script1.js') }}"></script>

</body>
</html>