/* =========================================================
   MTsN 1 Wonosobo — Portal Kesiswaan
   script1.js — semua interaksi frontend portal.
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    /* -----------------------------------------------------
       1. MOBILE MENU
       (didefinisikan lebih dulu karena dipanggil oleh
       goToPage() di bagian navigasi di bawah)
    ----------------------------------------------------- */

    const mobileMenuBtn = document.querySelector('.mobile-menu');
    const mainNav = document.querySelector('.main-nav');

    function closeMobileMenu() {
        if (!mainNav) return;
        mainNav.classList.remove('open');
        if (mobileMenuBtn) mobileMenuBtn.setAttribute('aria-expanded', 'false');
    }

    if (mobileMenuBtn && mainNav) {
        mobileMenuBtn.addEventListener('click', function () {
            const isOpen = mainNav.classList.toggle('open');
            mobileMenuBtn.setAttribute('aria-expanded', String(isOpen));
        });
    }

    /* -----------------------------------------------------
       2. NAVIGASI ANTAR HALAMAN (SPA-style, data-target)
       Semua elemen dengan [data-target] — nav, hero button,
       quick-card, footer — berbagi logika yang sama.
    ----------------------------------------------------- */

    const navLinks = document.querySelectorAll('.nav-link');
    const targetTriggers = document.querySelectorAll('[data-target]');

    function goToPage(pageName) {
        if (!pageName) return;

        const nextPage = document.querySelector('.page[data-page="' + pageName + '"]');
        if (!nextPage) return;

        const currentPage = document.querySelector('.page.active');

        // halaman lama: fade-out dulu, baru disembunyikan setelah animasinya kelar
        if (currentPage && currentPage !== nextPage) {
            currentPage.classList.remove('active');
            currentPage.classList.add('leaving');

            currentPage.addEventListener('animationend', function onLeaveEnd() {
                currentPage.classList.remove('leaving');
                currentPage.removeEventListener('animationend', onLeaveEnd);
            });
        }

        // halaman baru: fade-in (animasi jalan otomatis lewat CSS .page.active)
        nextPage.classList.add('active');

        navLinks.forEach(function (link) {
            link.classList.toggle('active', link.dataset.target === pageName);
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });

        // simpan posisi terakhir di URL biar bisa di-refresh/share
        history.replaceState(null, '', '#' + pageName);

        closeMobileMenu();
    }

    targetTriggers.forEach(function (el) {
        el.addEventListener('click', function () {
            goToPage(el.dataset.target);
        });
    });

    // buka halaman sesuai hash URL saat pertama load (misal portal.com#akademik)
    const initialHash = window.location.hash.replace('#', '');
    if (initialHash && document.querySelector('.page[data-page="' + initialHash + '"]')) {
        goToPage(initialHash);
    }

    /* -----------------------------------------------------
       3. MODAL (generik — dipakai login & detail ekstrakurikuler)
       Semua elemen .modal dibuka/ditutup lewat fungsi yang sama,
       supaya nambah modal baru gak perlu duplikat logic.
    ----------------------------------------------------- */

    function openModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.add('open');
        document.body.style.overflow = 'hidden';
        const firstInput = modalEl.querySelector('input');
        if (firstInput) firstInput.focus();
    }

    function closeModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.remove('open');
        document.body.style.overflow = '';
    }

    function closeAllModals() {
        document.querySelectorAll('.modal.open').forEach(closeModal);
    }

    // tombol × di semua modal
    document.querySelectorAll('.modal-close').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeModal(btn.closest('.modal'));
        });
    });

    // klik di luar modal-box menutup modal yang bersangkutan
    document.querySelectorAll('.modal').forEach(function (modalEl) {
        modalEl.addEventListener('click', function (e) {
            if (e.target === modalEl) closeModal(modalEl);
        });
    });

    // Escape menutup modal manapun yang lagi kebuka
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAllModals();
    });

    /* -----------------------------------------------------
       3a. MODAL LOGIN
    ----------------------------------------------------- */

    const loginBtn = document.getElementById('loginTrigger');
    const profileBtn = document.getElementById('profileTrigger');
    const loginModal = document.getElementById('loginModal');

    if (loginBtn) {
        loginBtn.addEventListener('click', function () {
            openModal(loginModal);
        });
    }

    /* -----------------------------------------------------
       3b. MODAL DETAIL EKSTRAKURIKULER
    ----------------------------------------------------- */

    const extraDetailModal = document.getElementById('extraDetailModal');
    const extraDetailIcon = document.getElementById('extraDetailIcon');
    const extraDetailCategory = document.getElementById('extraDetailCategory');
    const extraDetailTitle = document.getElementById('extraDetailTitle');
    const extraDetailDesc = document.getElementById('extraDetailDesc');
    const extraDetailJadwal = document.getElementById('extraDetailJadwal');
    const extraDetailLokasi = document.getElementById('extraDetailLokasi');

    function openExtraDetail(item) {
        if (!extraDetailModal || !item) return;

        if (extraDetailIcon) extraDetailIcon.textContent = item.icon;
        if (extraDetailCategory) extraDetailCategory.textContent = item.kategori.toUpperCase();
        if (extraDetailTitle) extraDetailTitle.textContent = item.judul;
        if (extraDetailDesc) extraDetailDesc.textContent = item.deskripsi;
        if (extraDetailJadwal) extraDetailJadwal.textContent = item.jadwal || 'Jadwal belum diatur';
        if (extraDetailLokasi) extraDetailLokasi.textContent = item.lokasi || 'Lokasi belum diatur';

        openModal(extraDetailModal);
    }

    /* -----------------------------------------------------
       4. FORM SUBMIT — Konseling & Login
       Catatan: submit sungguhan (ke controller Laravel) bisa
       ditambahkan dengan mengganti bagian fetch() di bawah
       dengan endpoint route yang sesuai, atau hapus
       e.preventDefault() kalau mau submit form biasa.
    ----------------------------------------------------- */

    function showFormNote(form, message, type) {
        let note = form.querySelector('.form-note');
        if (!note) {
            note = document.createElement('div');
            note.className = 'form-note';
            form.appendChild(note);
        }
        note.textContent = message;
        note.className = 'form-note show ' + type;
    }

    const counselingForm = document.getElementById('counselingForm');
    if (counselingForm) {
        counselingForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const nama = counselingForm.nama.value.trim();
            const kelas = counselingForm.kelas.value.trim();
            const masalah = counselingForm.masalah.value.trim();

            if (!nama || !kelas || !masalah) {
                showFormNote(counselingForm, 'Mohon lengkapi semua kolom terlebih dahulu.', 'error');
                return;
            }

            // TODO: ganti dengan request ke route Laravel, contoh:
            // fetch('/konseling', { method: 'POST', body: new FormData(counselingForm), headers: { 'X-CSRF-TOKEN': token } })

            showFormNote(counselingForm, 'Pengajuan konsultasi berhasil dikirim. Guru BK akan menghubungi kamu.', 'success');
            counselingForm.reset();
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const nis = loginForm.nis.value.trim();
            const password = loginForm.password.value.trim();

            if (!nis || !password) {
                showFormNote(loginForm, 'NIS/NIP dan password wajib diisi.', 'error');
                return;
            }

            // TODO: ganti dengan request ke route Laravel, contoh:
            // fetch('/login', { method: 'POST', body: new FormData(loginForm), headers: { 'X-CSRF-TOKEN': token } })
            // Role (siswa/guru) dan data profil di dashboard saat ini masih dummy —
            // sambungkan ke response backend supaya menampilkan data & role asli.
            //
            // Aturan role sementara (dummy): NIP guru format aslinya 18 digit,
            // sedangkan NIS siswa jauh lebih pendek. Ganti logic ini dengan
            // status role yang dikirim backend saat sudah terhubung.
            const digitsOnly = nis.replace(/\D/g, '');
            const role = digitsOnly.length >= 15 ? 'guru' : 'siswa';

            showFormNote(loginForm, 'Login berhasil. Mengalihkan ke dashboard...', 'success');
            setUserSignedIn(role, nis);

            setTimeout(function () {
                closeModal(loginModal);
                loginForm.reset();
                goToPage(role === 'guru' ? 'guru-dashboard' : 'profil');
            }, 700);
        });
    }

    function setUserSignedIn(role, id) {
        if (loginBtn) loginBtn.hidden = true;

        if (profileBtn) {
            profileBtn.hidden = false;
            profileBtn.dataset.target = role === 'guru' ? 'guru-dashboard' : 'profil';

            const avatarEl = profileBtn.querySelector('.profile-avatar');
            if (avatarEl) avatarEl.textContent = role === 'guru' ? 'GR' : 'AS';
        }

        if (role === 'siswa') {
            const profileNis = document.getElementById('profileNis');
            if (profileNis && id) profileNis.textContent = id;
        }
    }

    function setUserSignedOut() {
        if (loginBtn) loginBtn.hidden = false;
        if (profileBtn) profileBtn.hidden = true;
        goToPage('home');
    }

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            // TODO: ganti dengan request logout ke route Laravel, contoh:
            // fetch('/logout', { method: 'POST', headers: { 'X-CSRF-TOKEN': token } })
            setUserSignedOut();
        });
    }

    /* -----------------------------------------------------
       5. EKSTRAKURIKULER — filter kategori + pencarian + detail
    ----------------------------------------------------- */

    const filterButtons = document.querySelectorAll('.filter');
    const searchInput = document.querySelector('.filter-row input[type="search"]');
    const extraGrid = document.getElementById('extraGrid');

    let activeFilter = 'all';

    function ensureEmptyState() {
        let emptyState = document.querySelector('.empty-state');
        if (!emptyState && extraGrid) {
            emptyState = document.createElement('p');
            emptyState.className = 'empty-state';
            emptyState.textContent = 'Tidak ada ekstrakurikuler yang cocok dengan pencarianmu.';
            extraGrid.insertAdjacentElement('afterend', emptyState);
        }
        return emptyState;
    }

    function applyExtraFilters() {
        const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const extraCards = extraGrid ? extraGrid.querySelectorAll('.extra-card') : [];
        let visibleCount = 0;

        extraCards.forEach(function (card) {
            const matchesCategory = activeFilter === 'all' || card.dataset.category === activeFilter;
            const title = card.querySelector('h3');
            const matchesKeyword = !keyword || (title && title.textContent.toLowerCase().includes(keyword));
            const visible = matchesCategory && matchesKeyword;

            card.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        const emptyState = ensureEmptyState();
        if (emptyState) emptyState.classList.toggle('show', visibleCount === 0);
    }

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            activeFilter = btn.dataset.filter;
            applyExtraFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyExtraFilters);
    }

    // klik "Lihat kegiatan →" (atau kartunya) buka modal detail
    if (extraGrid) {
        extraGrid.addEventListener('click', function (e) {
            const card = e.target.closest('.extra-card');
            if (!card) return;

            const id = card.dataset.id;
            const item = extracurricularsData.find(function (x) { return String(x.id) === id; });
            if (item) openExtraDetail(item);
        });
    }

    /* -----------------------------------------------------
       6. DATA PORTAL — pengumuman, jadwal, ekstrakurikuler
       Catatan: array di bawah ini adalah data dummy yang
       hidup di memori browser (hilang saat refresh). Untuk
       produksi, ganti setiap render*()/simpan di bawah dengan
       fetch() ke endpoint Laravel (GET untuk render, POST/PUT/
       DELETE untuk aksi guru), supaya data tersimpan permanen
       di database dan sinkron ke semua pengguna.
    ----------------------------------------------------- */

    let nextAnnouncementId = 4;
    let announcementsData = [
        { id: 1, tanggal: '20', bulan: 'AGU', kategori: 'SEKOLAH', judul: 'Informasi kegiatan siswa', deskripsi: 'Pantau informasi kegiatan sekolah melalui portal kesiswaan.' },
        { id: 2, tanggal: '18', bulan: 'AGU', kategori: 'AKADEMIK', judul: 'Informasi pembelajaran', deskripsi: 'Siswa diharapkan memperhatikan informasi akademik terbaru.' },
        { id: 3, tanggal: '15', bulan: 'AGU', kategori: 'KESISWAAN', judul: 'Pengumuman kegiatan ekstrakurikuler', deskripsi: 'Informasi mengenai kegiatan ekstrakurikuler tersedia di portal.' }
    ];

    let nextExtraId = 7;
    let extracurricularsData = [
        { id: 1, icon: '⚽', kategori: 'olahraga', judul: 'Futsal', jadwal: 'Selasa, 15.30 - 17.00', lokasi: 'Lapangan Futsal Sekolah', deskripsi: 'Melatih kemampuan bermain, kerja sama, dan sportivitas.' },
        { id: 2, icon: '🏀', kategori: 'olahraga', judul: 'Basket', jadwal: 'Kamis, 15.30 - 17.00', lokasi: 'Lapangan Basket Sekolah', deskripsi: 'Mengembangkan kemampuan teknik dan kerja sama tim.' },
        { id: 3, icon: '🎨', kategori: 'seni', judul: 'Seni', jadwal: 'Rabu, 14.00 - 15.30', lokasi: 'Ruang Kesenian', deskripsi: 'Wadah untuk mengekspresikan kreativitas dan bakat.' },
        { id: 4, icon: '★', kategori: 'organisasi', judul: 'OSIM', jadwal: 'Jumat, 14.00 - 15.30', lokasi: 'Aula Sekolah', deskripsi: 'Belajar kepemimpinan, organisasi, dan tanggung jawab.' },
        { id: 5, icon: '🎵', kategori: 'seni', judul: 'Musik', jadwal: 'Senin, 15.30 - 17.00', lokasi: 'Ruang Musik', deskripsi: 'Mengembangkan kemampuan musik dan kreativitas siswa.' },
        { id: 6, icon: '📚', kategori: 'organisasi', judul: 'PMR', jadwal: 'Sabtu, 08.00 - 10.00', lokasi: 'Ruang UKS', deskripsi: 'Belajar kepedulian, kesehatan, dan kegiatan sosial.' }
    ];

    let nextScheduleId = 5;
    let scheduleData = [
        { id: 1, hari: 'Senin', jam: '07:00 - 08:30', mapel: 'Matematika', kelas: 'IX A', guru: 'Bpk. Arif Rahman' },
        { id: 2, hari: 'Senin', jam: '08:30 - 10:00', mapel: 'Bahasa Indonesia', kelas: 'IX A', guru: 'Ibu Siti Rahmawati' },
        { id: 3, hari: 'Selasa', jam: '07:00 - 08:30', mapel: 'IPA', kelas: 'IX A', guru: 'Bpk. Dedi Kurniawan' },
        { id: 4, hari: 'Rabu', jam: '09:00 - 10:30', mapel: 'Bahasa Inggris', kelas: 'IX A', guru: 'Ibu Nurul Hidayah' }
    ];

    const dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    function sortSchedule(list) {
        return list.slice().sort(function (a, b) {
            const dayDiff = dayOrder.indexOf(a.hari) - dayOrder.indexOf(b.hari);
            if (dayDiff !== 0) return dayDiff;
            return a.jam.localeCompare(b.jam);
        });
    }

    /* ---------- RENDER: halaman publik ---------- */

    function renderAnnouncements() {
        const list = document.getElementById('announcementList');
        if (!list) return;

        if (announcementsData.length === 0) {
            list.innerHTML = '<p class="empty-state show">Belum ada pengumuman.</p>';
            return;
        }

        list.innerHTML = announcementsData.map(function (item) {
            return (
                '<article class="announcement-card">' +
                    '<div class="announcement-date">' +
                        '<strong>' + escapeHtml(item.tanggal) + '</strong>' +
                        '<span>' + escapeHtml(item.bulan) + '</span>' +
                    '</div>' +
                    '<div>' +
                        '<span class="announcement-label">' + escapeHtml(item.kategori) + '</span>' +
                        '<h3>' + escapeHtml(item.judul) + '</h3>' +
                        '<p>' + escapeHtml(item.deskripsi) + '</p>' +
                    '</div>' +
                '</article>'
            );
        }).join('');
    }

    function renderExtracurriculars() {
        if (!extraGrid) return;

        extraGrid.innerHTML = extracurricularsData.map(function (item) {
            return (
                '<article class="extra-card" data-category="' + escapeHtml(item.kategori) + '" data-id="' + item.id + '">' +
                    '<div class="extra-icon">' + escapeHtml(item.icon) + '</div>' +
                    '<span class="extra-category">' + escapeHtml(item.kategori.toUpperCase()) + '</span>' +
                    '<h3>' + escapeHtml(item.judul) + '</h3>' +
                    '<p>' + escapeHtml(item.deskripsi) + '</p>' +
                    '<span class="extra-link">Lihat kegiatan →</span>' +
                '</article>'
            );
        }).join('');

        applyExtraFilters();
    }

    function renderSchedule() {
        const body = document.getElementById('scheduleTableBody');
        if (!body) return;

        if (scheduleData.length === 0) {
            body.innerHTML = '<tr class="schedule-empty"><td colspan="5">Jadwal pelajaran belum tersedia.</td></tr>';
            return;
        }

        body.innerHTML = sortSchedule(scheduleData).map(function (item) {
            return (
                '<tr>' +
                    '<td>' + escapeHtml(item.hari) + '</td>' +
                    '<td>' + escapeHtml(item.jam) + '</td>' +
                    '<td>' + escapeHtml(item.mapel) + '</td>' +
                    '<td>' + escapeHtml(item.kelas) + '</td>' +
                    '<td>' + escapeHtml(item.guru) + '</td>' +
                '</tr>'
            );
        }).join('');
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value == null ? '' : value);
        return div.innerHTML;
    }

    /* -----------------------------------------------------
       7. DASHBOARD GURU — kelola pengumuman, jadwal, ekstra
    ----------------------------------------------------- */

    // --- tab switching ---

    const adminTabs = document.querySelectorAll('.admin-tab');
    const adminPanels = document.querySelectorAll('.admin-panel');

    adminTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            adminTabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            const target = tab.dataset.adminTab;
            adminPanels.forEach(function (panel) {
                panel.classList.toggle('active', panel.dataset.adminPanel === target);
            });
        });
    });

    // --- PENGUMUMAN admin ---

    const pengumumanForm = document.getElementById('pengumumanForm');
    const pengumumanAdminList = document.getElementById('pengumumanAdminList');
    const pengumumanFormTitle = document.getElementById('pengumumanFormTitle');
    const pengumumanCancelEdit = document.getElementById('pengumumanCancelEdit');

    function renderPengumumanAdmin() {
        if (!pengumumanAdminList) return;

        if (announcementsData.length === 0) {
            pengumumanAdminList.innerHTML = '<li class="admin-empty">Belum ada pengumuman.</li>';
            return;
        }

        pengumumanAdminList.innerHTML = announcementsData.map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.judul) + '</strong>' +
                        '<span>' + escapeHtml(item.tanggal) + ' ' + escapeHtml(item.bulan) + ' · ' + escapeHtml(item.kategori) + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                        '<button type="button" class="delete-btn" data-action="delete">Hapus</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (pengumumanForm) {
        pengumumanForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = pengumumanForm.id.value;
            const payload = {
                tanggal: pengumumanForm.tanggal.value.trim(),
                bulan: pengumumanForm.bulan.value.trim().toUpperCase(),
                kategori: pengumumanForm.kategori.value.trim().toUpperCase(),
                judul: pengumumanForm.judul.value.trim(),
                deskripsi: pengumumanForm.deskripsi.value.trim()
            };

            if (!payload.tanggal || !payload.bulan || !payload.kategori || !payload.judul || !payload.deskripsi) {
                showFormNote(pengumumanForm, 'Semua kolom wajib diisi.', 'error');
                return;
            }

            // TODO: ganti dengan fetch() POST/PUT ke route Laravel (mis. /admin/pengumuman)

            if (id) {
                announcementsData = announcementsData.map(function (item) {
                    return String(item.id) === id ? Object.assign({ id: item.id }, payload) : item;
                });
                showFormNote(pengumumanForm, 'Pengumuman berhasil diperbarui.', 'success');
            } else {
                announcementsData.push(Object.assign({ id: nextAnnouncementId++ }, payload));
                showFormNote(pengumumanForm, 'Pengumuman berhasil ditambahkan.', 'success');
            }

            resetPengumumanForm();
            renderPengumumanAdmin();
            renderAnnouncements();
        });
    }

    function resetPengumumanForm() {
        if (!pengumumanForm) return;
        pengumumanForm.reset();
        pengumumanForm.id.value = '';
        if (pengumumanFormTitle) pengumumanFormTitle.textContent = 'Tambah Pengumuman';
        if (pengumumanCancelEdit) pengumumanCancelEdit.hidden = true;
    }

    if (pengumumanCancelEdit) {
        pengumumanCancelEdit.addEventListener('click', resetPengumumanForm);
    }

    if (pengumumanAdminList) {
        pengumumanAdminList.addEventListener('click', function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                // TODO: ganti dengan fetch() DELETE ke route Laravel
                announcementsData = announcementsData.filter(function (item) { return String(item.id) !== id; });
                renderPengumumanAdmin();
                renderAnnouncements();
                return;
            }

            if (btn.dataset.action === 'edit') {
                const item = announcementsData.find(function (a) { return String(a.id) === id; });
                if (!item || !pengumumanForm) return;

                pengumumanForm.id.value = item.id;
                pengumumanForm.tanggal.value = item.tanggal;
                pengumumanForm.bulan.value = item.bulan;
                pengumumanForm.kategori.value = item.kategori;
                pengumumanForm.judul.value = item.judul;
                pengumumanForm.deskripsi.value = item.deskripsi;

                if (pengumumanFormTitle) pengumumanFormTitle.textContent = 'Edit Pengumuman';
                if (pengumumanCancelEdit) pengumumanCancelEdit.hidden = false;
                pengumumanForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- JADWAL admin ---

    const jadwalForm = document.getElementById('jadwalForm');
    const jadwalAdminList = document.getElementById('jadwalAdminList');
    const jadwalFormTitle = document.getElementById('jadwalFormTitle');
    const jadwalCancelEdit = document.getElementById('jadwalCancelEdit');

    function renderJadwalAdmin() {
        if (!jadwalAdminList) return;

        if (scheduleData.length === 0) {
            jadwalAdminList.innerHTML = '<li class="admin-empty">Belum ada jadwal.</li>';
            return;
        }

        jadwalAdminList.innerHTML = sortSchedule(scheduleData).map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.mapel) + ' · ' + escapeHtml(item.kelas) + '</strong>' +
                        '<span>' + escapeHtml(item.hari) + ', ' + escapeHtml(item.jam) + ' · ' + escapeHtml(item.guru) + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                        '<button type="button" class="delete-btn" data-action="delete">Hapus</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (jadwalForm) {
        jadwalForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = jadwalForm.id.value;
            const payload = {
                hari: jadwalForm.hari.value,
                jam: jadwalForm.jam.value.trim(),
                mapel: jadwalForm.mapel.value.trim(),
                kelas: jadwalForm.kelas.value.trim(),
                guru: jadwalForm.guru.value.trim()
            };

            if (!payload.hari || !payload.jam || !payload.mapel || !payload.kelas || !payload.guru) {
                showFormNote(jadwalForm, 'Semua kolom wajib diisi.', 'error');
                return;
            }

            // TODO: ganti dengan fetch() POST/PUT ke route Laravel (mis. /admin/jadwal)

            if (id) {
                scheduleData = scheduleData.map(function (item) {
                    return String(item.id) === id ? Object.assign({ id: item.id }, payload) : item;
                });
                showFormNote(jadwalForm, 'Jadwal berhasil diperbarui.', 'success');
            } else {
                scheduleData.push(Object.assign({ id: nextScheduleId++ }, payload));
                showFormNote(jadwalForm, 'Jadwal berhasil ditambahkan.', 'success');
            }

            resetJadwalForm();
            renderJadwalAdmin();
            renderSchedule();
        });
    }

    function resetJadwalForm() {
        if (!jadwalForm) return;
        jadwalForm.reset();
        jadwalForm.id.value = '';
        if (jadwalFormTitle) jadwalFormTitle.textContent = 'Tambah Jadwal';
        if (jadwalCancelEdit) jadwalCancelEdit.hidden = true;
    }

    if (jadwalCancelEdit) {
        jadwalCancelEdit.addEventListener('click', resetJadwalForm);
    }

    if (jadwalAdminList) {
        jadwalAdminList.addEventListener('click', function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                // TODO: ganti dengan fetch() DELETE ke route Laravel
                scheduleData = scheduleData.filter(function (item) { return String(item.id) !== id; });
                renderJadwalAdmin();
                renderSchedule();
                return;
            }

            if (btn.dataset.action === 'edit') {
                const item = scheduleData.find(function (s) { return String(s.id) === id; });
                if (!item || !jadwalForm) return;

                jadwalForm.id.value = item.id;
                jadwalForm.hari.value = item.hari;
                jadwalForm.jam.value = item.jam;
                jadwalForm.mapel.value = item.mapel;
                jadwalForm.kelas.value = item.kelas;
                jadwalForm.guru.value = item.guru;

                if (jadwalFormTitle) jadwalFormTitle.textContent = 'Edit Jadwal';
                if (jadwalCancelEdit) jadwalCancelEdit.hidden = false;
                jadwalForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- EKSTRAKURIKULER admin ---

    const ekstraForm = document.getElementById('ekstraForm');
    const ekstraAdminList = document.getElementById('ekstraAdminList');
    const ekstraFormTitle = document.getElementById('ekstraFormTitle');
    const ekstraCancelEdit = document.getElementById('ekstraCancelEdit');

    function renderEkstraAdmin() {
        if (!ekstraAdminList) return;

        if (extracurricularsData.length === 0) {
            ekstraAdminList.innerHTML = '<li class="admin-empty">Belum ada ekstrakurikuler.</li>';
            return;
        }

        ekstraAdminList.innerHTML = extracurricularsData.map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.icon) + ' ' + escapeHtml(item.judul) + '</strong>' +
                        '<span>' + escapeHtml(item.kategori) + ' · ' + escapeHtml(item.jadwal || '-') + ' · ' + escapeHtml(item.lokasi || '-') + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                        '<button type="button" class="delete-btn" data-action="delete">Hapus</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (ekstraForm) {
        ekstraForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = ekstraForm.id.value;
            const payload = {
                icon: ekstraForm.icon.value.trim(),
                kategori: ekstraForm.kategori.value,
                judul: ekstraForm.judul.value.trim(),
                jadwal: ekstraForm.jadwal.value.trim(),
                lokasi: ekstraForm.lokasi.value.trim(),
                deskripsi: ekstraForm.deskripsi.value.trim()
            };

            if (!payload.icon || !payload.kategori || !payload.judul || !payload.jadwal || !payload.lokasi || !payload.deskripsi) {
                showFormNote(ekstraForm, 'Semua kolom wajib diisi.', 'error');
                return;
            }

            // TODO: ganti dengan fetch() POST/PUT ke route Laravel (mis. /admin/ekstrakurikuler)

            if (id) {
                extracurricularsData = extracurricularsData.map(function (item) {
                    return String(item.id) === id ? Object.assign({ id: item.id }, payload) : item;
                });
                showFormNote(ekstraForm, 'Ekstrakurikuler berhasil diperbarui.', 'success');
            } else {
                extracurricularsData.push(Object.assign({ id: nextExtraId++ }, payload));
                showFormNote(ekstraForm, 'Ekstrakurikuler berhasil ditambahkan.', 'success');
            }

            resetEkstraForm();
            renderEkstraAdmin();
            renderExtracurriculars();
        });
    }

    function resetEkstraForm() {
        if (!ekstraForm) return;
        ekstraForm.reset();
        ekstraForm.id.value = '';
        if (ekstraFormTitle) ekstraFormTitle.textContent = 'Tambah Ekstrakurikuler';
        if (ekstraCancelEdit) ekstraCancelEdit.hidden = true;
    }

    if (ekstraCancelEdit) {
        ekstraCancelEdit.addEventListener('click', resetEkstraForm);
    }

    if (ekstraAdminList) {
        ekstraAdminList.addEventListener('click', function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                // TODO: ganti dengan fetch() DELETE ke route Laravel
                extracurricularsData = extracurricularsData.filter(function (item) { return String(item.id) !== id; });
                renderEkstraAdmin();
                renderExtracurriculars();
                return;
            }

            if (btn.dataset.action === 'edit') {
                const item = extracurricularsData.find(function (x) { return String(x.id) === id; });
                if (!item || !ekstraForm) return;

                ekstraForm.id.value = item.id;
                ekstraForm.icon.value = item.icon;
                ekstraForm.kategori.value = item.kategori;
                ekstraForm.judul.value = item.judul;
                ekstraForm.jadwal.value = item.jadwal || '';
                ekstraForm.lokasi.value = item.lokasi || '';
                ekstraForm.deskripsi.value = item.deskripsi;

                if (ekstraFormTitle) ekstraFormTitle.textContent = 'Edit Ekstrakurikuler';
                if (ekstraCancelEdit) ekstraCancelEdit.hidden = false;
                ekstraForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- render awal saat halaman dimuat ---

    renderAnnouncements();
    renderExtracurriculars();
    renderSchedule();
    renderPengumumanAdmin();
    renderJadwalAdmin();
    renderEkstraAdmin();

});