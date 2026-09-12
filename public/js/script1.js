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
        counselingForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const nama = counselingForm.nama.value.trim();
            const kelas = counselingForm.kelas.value.trim();
            const masalah = counselingForm.masalah.value.trim();

            if (!nama || !kelas || !masalah) {
                showFormNote(counselingForm, 'Mohon lengkapi semua kolom terlebih dahulu.', 'error');
                return;
            }

            try {
                await apiRequest('/api/konseling', {
                    method: 'POST',
                    body: JSON.stringify({ nama: nama, kelas: kelas, masalah: masalah })
                });

                showFormNote(counselingForm, 'Pengajuan konsultasi berhasil dikirim. Guru BK akan menghubungi kamu.', 'success');
                counselingForm.reset();
            } catch (err) {
                showFormNote(counselingForm, err.message, 'error');
            }
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
       Catatan: pengumuman & ekstrakurikuler & jadwal diambil
       lewat apiRequest() dari endpoint Laravel. Untuk
       jadwal/ekstrakurikuler, aksi tambah/edit/hapus di
       dashboard guru masih memakai array lokal (lihat TODO
       di bagian 7) — ganti dengan apiRequest() begitu route
       Laravel-nya tersedia, seperti pola pengumuman.
    ----------------------------------------------------- */

    let announcementsData = [];
    let extracurricularsData = [];
    let scheduleData = [];
    let kesiswaanData = [];
    let examData = [];
    let materialData = [];
    let konselingData = [];
    let siswaData = [];

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    async function apiRequest(url, options = {}) {
        const isFormData = options.body instanceof FormData;

        const headers = Object.assign(
            isFormData ? { 'Accept': 'application/json' } : { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            options.headers || {}
        );

        if (csrfToken && options.method && options.method !== 'GET') {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(url, Object.assign({}, options, { headers }));

        if (!response.ok) {
            const errorBody = await response.json().catch(function () { return {}; });
            throw new Error(errorBody.message || 'Terjadi kesalahan pada server.');
        }

        if (response.status === 204) return null;
        return response.json();
    }

    // Menghasilkan id baru yang aman dipakai untuk data lokal
    // (jadwal & ekstrakurikuler) tanpa bentrok dengan id yang
    // sudah datang dari API.
    function getNextId(list) {
        return list.reduce(function (max, item) {
            const n = Number(item.id);
            return Number.isFinite(n) && n > max ? n : max;
        }, 0) + 1;
    }

    const dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    function sortSchedule(list) {
        return list.slice().sort(function (a, b) {
            const dayDiff = dayOrder.indexOf(a.hari) - dayOrder.indexOf(b.hari);
            if (dayDiff !== 0) return dayDiff;
            return a.jam.localeCompare(b.jam);
        });
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = String(value == null ? '' : value);
        return div.innerHTML;
    }

    /* ---------- RENDER: halaman publik ---------- */

    async function loadAnnouncements() {
        try {
            announcementsData = await apiRequest('/api/pengumuman');
        } catch (err) {
            announcementsData = [];
        }
        renderAnnouncements();
        renderPengumumanAdmin();
    }

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

    async function loadExtracurriculars() {
        try {
            extracurricularsData = await apiRequest('/api/ekstrakurikuler');
        } catch (err) {
            extracurricularsData = [];
        }
        renderExtracurriculars();
        renderEkstraAdmin();
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

    async function loadSchedule() {
        try {
            scheduleData = await apiRequest('/api/jadwal');
        } catch (err) {
            scheduleData = [];
        }
        renderSchedule();
        renderJadwalAdmin();
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

        async function loadKesiswaan() {
        try {
            kesiswaanData = await apiRequest('/api/kesiswaan');
        } catch (err) {
            kesiswaanData = [];
        }
        renderKesiswaan();
        renderKesiswaanAdmin();
    }

    function renderKesiswaan() {
        const grid = document.getElementById('kesiswaanGrid');
        if (!grid) return;

        if (kesiswaanData.length === 0) {
            grid.innerHTML = '<p class="empty-state show">Belum ada konten kesiswaan.</p>';
            return;
        }

        grid.innerHTML = kesiswaanData.map(function (item, index) {
            const nomor = String(index + 1).padStart(2, '0');
            const gambarHtml = item.gambar_url
                ? '<img src="' + item.gambar_url + '" alt="' + escapeHtml(item.judul) + '" style="width:100%; border-radius:10px; margin-bottom:16px; aspect-ratio:16/9; object-fit:cover;">'
                : '';

            return (
                '<article class="feature-card">' +
                    gambarHtml +
                    '<div class="feature-number">' + nomor + '</div>' +
                    '<h3>' + escapeHtml(item.judul) + '</h3>' +
                    '<p>' + escapeHtml(item.deskripsi) + '</p>' +
                '</article>'
            );
        }).join('');
    }

        async function loadExams() {
        try {
            examData = await apiRequest('/api/ujian');
        } catch (err) {
            examData = [];
        }
        renderExams();
        renderUjianAdmin();
    }

    function renderExams() {
        const body = document.getElementById('examTableBody');
        if (!body) return;

        if (examData.length === 0) {
            body.innerHTML = '<tr class="schedule-empty"><td colspan="6">Jadwal ujian belum tersedia.</td></tr>';
            return;
        }

        body.innerHTML = examData.map(function (item) {
            return (
                '<tr>' +
                    '<td>' + escapeHtml(item.tanggal) + '</td>' +
                    '<td>' + escapeHtml(item.jam) + '</td>' +
                    '<td>' + escapeHtml(item.mapel) + '</td>' +
                    '<td>' + escapeHtml(item.kelas) + '</td>' +
                    '<td>' + escapeHtml(item.jenis) + '</td>' +
                    '<td>' + escapeHtml(item.keterangan || '-') + '</td>' +
                '</tr>'
            );
        }).join('');
    }

    async function loadMaterials() {
        try {
            materialData = await apiRequest('/api/materi');
        } catch (err) {
            materialData = [];
        }
        renderMaterials();
        renderMateriAdmin();
    }

    function renderMaterials() {
        const list = document.getElementById('materialList');
        if (!list) return;

        if (materialData.length === 0) {
            list.innerHTML = '<p class="empty-state show">Belum ada materi pembelajaran.</p>';
            return;
        }

        list.innerHTML = materialData.map(function (item) {
            return (
                '<article class="announcement-card">' +
                    '<div class="announcement-date">' +
                        '<strong>' + escapeHtml(item.kelas) + '</strong>' +
                    '</div>' +
                    '<div>' +
                        '<span class="announcement-label">' + escapeHtml(item.mapel) + '</span>' +
                        '<h3>' + escapeHtml(item.judul) + '</h3>' +
                        '<p>' + escapeHtml(item.deskripsi) + '</p>' +
                    '</div>' +
                '</article>'
            );
        }).join('');
    }

        async function loadKonseling() {
        try {
            konselingData = await apiRequest('/api/konseling');
        } catch (err) {
            konselingData = [];
        }
        renderKonselingAdmin();
    }

        async function loadSiswa() {
        try {
            siswaData = await apiRequest('/api/siswa');
        } catch (err) {
            siswaData = [];
        }
        renderSiswaAdmin();
    }

        // --- BUAT AKUN BARU (siswa/guru) ---

    const createUserForm = document.getElementById('createUserForm');
    const createUserRole = document.getElementById('createUserRole');
    const createUserNisNipLabel = document.getElementById('createUserNisNipLabel');
    const createUserKelasGroup = document.getElementById('createUserKelasGroup');

    function updateCreateUserFormByRole() {
        if (!createUserRole) return;

        const isGuru = createUserRole.value === 'guru';

        if (createUserNisNipLabel) {
            createUserNisNipLabel.textContent = isGuru ? 'NIP' : 'NIS';
        }

        if (createUserKelasGroup) {
            createUserKelasGroup.style.display = isGuru ? 'none' : 'block';
        }
    }

    if (createUserRole) {
        createUserRole.addEventListener('change', updateCreateUserFormByRole);
        updateCreateUserFormByRole();
    }

    if (createUserForm) {
        createUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const payload = {
                name: createUserForm.name.value.trim(),
                role: createUserForm.role.value,
                nis_nip: createUserForm.nis_nip.value.trim(),
                kelas: createUserForm.kelas.value.trim(),
                password: createUserForm.password.value
            };

            if (!payload.name || !payload.nis_nip || !payload.password) {
                showFormNote(createUserForm, 'Nama, NIS/NIP, dan password wajib diisi.', 'error');
                return;
            }

            if (payload.password.length < 6) {
                showFormNote(createUserForm, 'Password minimal 6 karakter.', 'error');
                return;
            }

            try {
                await apiRequest('/api/siswa', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                showFormNote(createUserForm, 'Akun berhasil dibuat.', 'success');
                createUserForm.reset();
                updateCreateUserFormByRole();

                if (payload.role === 'siswa') {
                    await loadSiswa();
                }
            } catch (err) {
                showFormNote(createUserForm, err.message, 'error');
            }
        });
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
        pengumumanForm.addEventListener('submit', async function (e) {
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

            try {
                if (id) {
                    await apiRequest('/api/pengumuman/' + id, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(pengumumanForm, 'Pengumuman berhasil diperbarui.', 'success');
                } else {
                    await apiRequest('/api/pengumuman', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(pengumumanForm, 'Pengumuman berhasil ditambahkan.', 'success');
                }

                resetPengumumanForm();
                await loadAnnouncements();
            } catch (err) {
                showFormNote(pengumumanForm, err.message, 'error');
            }
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
        pengumumanAdminList.addEventListener('click', async function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                if (!confirm('Hapus pengumuman ini?')) return;
                try {
                    await apiRequest('/api/pengumuman/' + id, { method: 'DELETE' });
                    await loadAnnouncements();
                } catch (err) {
                    alert(err.message);
                }
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
        jadwalForm.addEventListener('submit', async function (e) {
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

            try {
                if (id) {
                    await apiRequest('/api/jadwal/' + id, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(jadwalForm, 'Jadwal berhasil diperbarui.', 'success');
                } else {
                    await apiRequest('/api/jadwal', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(jadwalForm, 'Jadwal berhasil ditambahkan.', 'success');
                }

                resetJadwalForm();
                await loadSchedule();
            } catch (err) {
                showFormNote(jadwalForm, err.message, 'error');
            }
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
        jadwalAdminList.addEventListener('click', async function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                if (!confirm('Hapus jadwal ini?')) return;
                try {
                    await apiRequest('/api/jadwal/' + id, { method: 'DELETE' });
                    await loadSchedule();
                } catch (err) {
                    alert(err.message);
                }
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
        ekstraForm.addEventListener('submit', async function (e) {
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

            try {
                if (id) {
                    await apiRequest('/api/ekstrakurikuler/' + id, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(ekstraForm, 'Ekstrakurikuler berhasil diperbarui.', 'success');
                } else {
                    await apiRequest('/api/ekstrakurikuler', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(ekstraForm, 'Ekstrakurikuler berhasil ditambahkan.', 'success');
                }

                resetEkstraForm();
                await loadExtracurriculars();
            } catch (err) {
                showFormNote(ekstraForm, err.message, 'error');
            }
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
        ekstraAdminList.addEventListener('click', async function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                if (!confirm('Hapus ekstrakurikuler ini?')) return;
                try {
                    await apiRequest('/api/ekstrakurikuler/' + id, { method: 'DELETE' });
                    await loadExtracurriculars();
                } catch (err) {
                    alert(err.message);
                }
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

        // --- KESISWAAN admin ---

    const kesiswaanForm = document.getElementById('kesiswaanForm');
    const kesiswaanAdminList = document.getElementById('kesiswaanAdminList');
    const kesiswaanFormTitle = document.getElementById('kesiswaanFormTitle');
    const kesiswaanCancelEdit = document.getElementById('kesiswaanCancelEdit');

    function renderKesiswaanAdmin() {
        if (!kesiswaanAdminList) return;

        if (kesiswaanData.length === 0) {
            kesiswaanAdminList.innerHTML = '<li class="admin-empty">Belum ada konten kesiswaan.</li>';
            return;
        }

        kesiswaanAdminList.innerHTML = kesiswaanData.map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.judul) + '</strong>' +
                        '<span>Urutan: ' + escapeHtml(item.urutan) + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                        '<button type="button" class="delete-btn" data-action="delete">Hapus</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (kesiswaanForm) {
        kesiswaanForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = kesiswaanForm.id.value;
            const judul = kesiswaanForm.judul.value.trim();
            const deskripsi = kesiswaanForm.deskripsi.value.trim();

            if (!judul || !deskripsi) {
                showFormNote(kesiswaanForm, 'Judul dan deskripsi wajib diisi.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('judul', judul);
            formData.append('deskripsi', deskripsi);
            if (kesiswaanForm.urutan.value) {
                formData.append('urutan', kesiswaanForm.urutan.value);
            }
            if (kesiswaanForm.gambar.files[0]) {
                formData.append('gambar', kesiswaanForm.gambar.files[0]);
            }

            try {
                if (id) {
                    // PHP tidak mem-parsing file pada method PUT asli,
                    // jadi tetap kirim via POST dengan _method=PUT (method spoofing Laravel).
                    formData.append('_method', 'PUT');
                    await apiRequest('/api/kesiswaan/' + id, {
                        method: 'POST',
                        body: formData
                    });
                    showFormNote(kesiswaanForm, 'Konten berhasil diperbarui.', 'success');
                } else {
                    await apiRequest('/api/kesiswaan', {
                        method: 'POST',
                        body: formData
                    });
                    showFormNote(kesiswaanForm, 'Konten berhasil ditambahkan.', 'success');
                }

                resetKesiswaanForm();
                await loadKesiswaan();
            } catch (err) {
                showFormNote(kesiswaanForm, err.message, 'error');
            }
        });
    }

    function resetKesiswaanForm() {
        if (!kesiswaanForm) return;
        kesiswaanForm.reset();
        kesiswaanForm.id.value = '';
        if (kesiswaanFormTitle) kesiswaanFormTitle.textContent = 'Tambah Konten Kesiswaan';
        if (kesiswaanCancelEdit) kesiswaanCancelEdit.hidden = true;

        const preview = document.getElementById('kesiswaanImagePreview');
        if (preview) {
            preview.style.display = 'none';
            preview.src = '';
        }
    }

    if (kesiswaanCancelEdit) {
        kesiswaanCancelEdit.addEventListener('click', resetKesiswaanForm);
    }

    if (kesiswaanAdminList) {
        kesiswaanAdminList.addEventListener('click', async function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                if (!confirm('Hapus konten kesiswaan ini?')) return;
                try {
                    await apiRequest('/api/kesiswaan/' + id, { method: 'DELETE' });
                    await loadKesiswaan();
                } catch (err) {
                    alert(err.message);
                }
                return;
            }

                       if (btn.dataset.action === 'edit') {
                const item = kesiswaanData.find(function (k) { return String(k.id) === id; });
                if (!item || !kesiswaanForm) return;

                kesiswaanForm.id.value = item.id;
                kesiswaanForm.judul.value = item.judul;
                kesiswaanForm.deskripsi.value = item.deskripsi;
                kesiswaanForm.urutan.value = item.urutan;

                const preview = document.getElementById('kesiswaanImagePreview');
                if (preview) {
                    if (item.gambar_url) {
                        preview.src = item.gambar_url;
                        preview.style.display = 'block';
                    } else {
                        preview.style.display = 'none';
                        preview.src = '';
                    }
                }

                if (kesiswaanFormTitle) kesiswaanFormTitle.textContent = 'Edit Konten Kesiswaan';
                if (kesiswaanCancelEdit) kesiswaanCancelEdit.hidden = false;
                kesiswaanForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- UJIAN admin ---

    const ujianForm = document.getElementById('ujianForm');
    const ujianAdminList = document.getElementById('ujianAdminList');
    const ujianFormTitle = document.getElementById('ujianFormTitle');
    const ujianCancelEdit = document.getElementById('ujianCancelEdit');

    function renderUjianAdmin() {
        if (!ujianAdminList) return;

        if (examData.length === 0) {
            ujianAdminList.innerHTML = '<li class="admin-empty">Belum ada jadwal ujian.</li>';
            return;
        }

        ujianAdminList.innerHTML = examData.map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.mapel) + ' · ' + escapeHtml(item.kelas) + '</strong>' +
                        '<span>' + escapeHtml(item.tanggal) + ', ' + escapeHtml(item.jam) + ' · ' + escapeHtml(item.jenis) + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                        '<button type="button" class="delete-btn" data-action="delete">Hapus</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (ujianForm) {
        ujianForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = ujianForm.id.value;
            const payload = {
                tanggal: ujianForm.tanggal.value.trim(),
                jam: ujianForm.jam.value.trim(),
                mapel: ujianForm.mapel.value.trim(),
                kelas: ujianForm.kelas.value.trim(),
                jenis: ujianForm.jenis.value,
                keterangan: ujianForm.keterangan.value.trim()
            };

            if (!payload.tanggal || !payload.jam || !payload.mapel || !payload.kelas || !payload.jenis) {
                showFormNote(ujianForm, 'Semua kolom wajib (kecuali keterangan) harus diisi.', 'error');
                return;
            }

            try {
                if (id) {
                    await apiRequest('/api/ujian/' + id, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(ujianForm, 'Jadwal ujian berhasil diperbarui.', 'success');
                } else {
                    await apiRequest('/api/ujian', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(ujianForm, 'Jadwal ujian berhasil ditambahkan.', 'success');
                }

                resetUjianForm();
                await loadExams();
            } catch (err) {
                showFormNote(ujianForm, err.message, 'error');
            }
        });
    }

    function resetUjianForm() {
        if (!ujianForm) return;
        ujianForm.reset();
        ujianForm.id.value = '';
        if (ujianFormTitle) ujianFormTitle.textContent = 'Tambah Jadwal Ujian';
        if (ujianCancelEdit) ujianCancelEdit.hidden = true;
    }

    if (ujianCancelEdit) {
        ujianCancelEdit.addEventListener('click', resetUjianForm);
    }

    if (ujianAdminList) {
        ujianAdminList.addEventListener('click', async function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                if (!confirm('Hapus jadwal ujian ini?')) return;
                try {
                    await apiRequest('/api/ujian/' + id, { method: 'DELETE' });
                    await loadExams();
                } catch (err) {
                    alert(err.message);
                }
                return;
            }

            if (btn.dataset.action === 'edit') {
                const item = examData.find(function (x) { return String(x.id) === id; });
                if (!item || !ujianForm) return;

                ujianForm.id.value = item.id;
                ujianForm.tanggal.value = item.tanggal;
                ujianForm.jam.value = item.jam;
                ujianForm.mapel.value = item.mapel;
                ujianForm.kelas.value = item.kelas;
                ujianForm.jenis.value = item.jenis;
                ujianForm.keterangan.value = item.keterangan || '';

                if (ujianFormTitle) ujianFormTitle.textContent = 'Edit Jadwal Ujian';
                if (ujianCancelEdit) ujianCancelEdit.hidden = false;
                ujianForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- MATERI admin ---

    const materiForm = document.getElementById('materiForm');
    const materiAdminList = document.getElementById('materiAdminList');
    const materiFormTitle = document.getElementById('materiFormTitle');
    const materiCancelEdit = document.getElementById('materiCancelEdit');

    function renderMateriAdmin() {
        if (!materiAdminList) return;

        if (materialData.length === 0) {
            materiAdminList.innerHTML = '<li class="admin-empty">Belum ada materi.</li>';
            return;
        }

        materiAdminList.innerHTML = materialData.map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.judul) + '</strong>' +
                        '<span>' + escapeHtml(item.mapel) + ' · ' + escapeHtml(item.kelas) + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                        '<button type="button" class="delete-btn" data-action="delete">Hapus</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (materiForm) {
        materiForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = materiForm.id.value;
            const payload = {
                mapel: materiForm.mapel.value.trim(),
                kelas: materiForm.kelas.value.trim(),
                judul: materiForm.judul.value.trim(),
                deskripsi: materiForm.deskripsi.value.trim()
            };

            if (!payload.mapel || !payload.kelas || !payload.judul || !payload.deskripsi) {
                showFormNote(materiForm, 'Semua kolom wajib diisi.', 'error');
                return;
            }

            try {
                if (id) {
                    await apiRequest('/api/materi/' + id, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(materiForm, 'Materi berhasil diperbarui.', 'success');
                } else {
                    await apiRequest('/api/materi', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showFormNote(materiForm, 'Materi berhasil ditambahkan.', 'success');
                }

                resetMateriForm();
                await loadMaterials();
            } catch (err) {
                showFormNote(materiForm, err.message, 'error');
            }
        });
    }

    function resetMateriForm() {
        if (!materiForm) return;
        materiForm.reset();
        materiForm.id.value = '';
        if (materiFormTitle) materiFormTitle.textContent = 'Tambah Materi';
        if (materiCancelEdit) materiCancelEdit.hidden = true;
    }

    if (materiCancelEdit) {
        materiCancelEdit.addEventListener('click', resetMateriForm);
    }

    if (materiAdminList) {
        materiAdminList.addEventListener('click', async function (e) {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            if (btn.dataset.action === 'delete') {
                if (!confirm('Hapus materi ini?')) return;
                try {
                    await apiRequest('/api/materi/' + id, { method: 'DELETE' });
                    await loadMaterials();
                } catch (err) {
                    alert(err.message);
                }
                return;
            }

            if (btn.dataset.action === 'edit') {
                const item = materialData.find(function (m) { return String(m.id) === id; });
                if (!item || !materiForm) return;

                materiForm.id.value = item.id;
                materiForm.mapel.value = item.mapel;
                materiForm.kelas.value = item.kelas;
                materiForm.judul.value = item.judul;
                materiForm.deskripsi.value = item.deskripsi;

                if (materiFormTitle) materiFormTitle.textContent = 'Edit Materi';
                if (materiCancelEdit) materiCancelEdit.hidden = false;
                materiForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // --- KONSELING admin (read-only + ubah status) ---

    const konselingAdminList = document.getElementById('konselingAdminList');

    const statusLabel = {
        baru: 'Baru',
        diproses: 'Diproses',
        selesai: 'Selesai'
    };

    function renderKonselingAdmin() {
        if (!konselingAdminList) return;

        if (konselingData.length === 0) {
            konselingAdminList.innerHTML = '<li class="admin-empty">Belum ada pengajuan konseling.</li>';
            return;
        }

        konselingAdminList.innerHTML = konselingData.map(function (item) {
            const tanggal = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            }) : '-';

            return (
                '<li class="admin-list-item" data-id="' + item.id + '" style="flex-direction: column; align-items: stretch;">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.nama) + ' · ' + escapeHtml(item.kelas) + '</strong>' +
                        '<span>Diajukan: ' + escapeHtml(tanggal) + '</span>' +
                        '<p class="konseling-item-masalah">' + escapeHtml(item.masalah) + '</p>' +
                    '</div>' +
                    '<div class="admin-list-item-actions" style="margin-top: 10px; align-self: flex-start;">' +
                        '<select class="status-select" data-action="status">' +
                            '<option value="baru"' + (item.status === 'baru' ? ' selected' : '') + '>' + statusLabel.baru + '</option>' +
                            '<option value="diproses"' + (item.status === 'diproses' ? ' selected' : '') + '>' + statusLabel.diproses + '</option>' +
                            '<option value="selesai"' + (item.status === 'selesai' ? ' selected' : '') + '>' + statusLabel.selesai + '</option>' +
                        '</select>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    if (konselingAdminList) {
        konselingAdminList.addEventListener('change', async function (e) {
            const select = e.target.closest('select[data-action="status"]');
            if (!select) return;

            const li = select.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            try {
                await apiRequest('/api/konseling/' + id, {
                    method: 'PUT',
                    body: JSON.stringify({ status: select.value })
                });
                await loadKonseling();
            } catch (err) {
                alert(err.message);
            }
        });
    }

    // --- KELOLA SISWA admin ---

    const siswaForm = document.getElementById('siswaForm');
    const siswaAdminList = document.getElementById('siswaAdminList');
    const siswaFormTitle = document.getElementById('siswaFormTitle');
    const siswaEkstraCheckboxes = document.getElementById('siswaEkstraCheckboxes');

    function renderSiswaAdmin() {
        if (!siswaAdminList) return;

        if (siswaData.length === 0) {
            siswaAdminList.innerHTML = '<li class="admin-empty">Belum ada data siswa.</li>';
            return;
        }

        siswaAdminList.innerHTML = siswaData.map(function (item) {
            return (
                '<li class="admin-list-item" data-id="' + item.id + '">' +
                    '<div class="admin-list-item-info">' +
                        '<strong>' + escapeHtml(item.name) + '</strong>' +
                        '<span>NIS ' + escapeHtml(item.nis_nip) + ' · ' + escapeHtml(item.kelas || 'Kelas belum diatur') + '</span>' +
                    '</div>' +
                    '<div class="admin-list-item-actions">' +
                        '<button type="button" class="edit-btn" data-action="edit">Edit</button>' +
                    '</div>' +
                '</li>'
            );
        }).join('');
    }

    function renderSiswaEkstraCheckboxes(selectedIds) {
        if (!siswaEkstraCheckboxes) return;

        if (extracurricularsData.length === 0) {
            siswaEkstraCheckboxes.innerHTML = '<span style="font-size: 13px; color: var(--ink-faint);">Belum ada data ekstrakurikuler.</span>';
            return;
        }

        siswaEkstraCheckboxes.innerHTML = extracurricularsData.map(function (item) {
            const checked = selectedIds.includes(item.id) ? 'checked' : '';
            return (
                '<label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 400; cursor: pointer;">' +
                    '<input type="checkbox" name="extracurricular_ids" value="' + item.id + '" ' + checked + '>' +
                    escapeHtml(item.icon) + ' ' + escapeHtml(item.judul) +
                '</label>'
            );
        }).join('');
    }

    if (siswaForm) {
        siswaForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = siswaForm.id.value;
            if (!id) {
                showFormNote(siswaForm, 'Pilih siswa dari daftar di sebelah kanan terlebih dahulu.', 'error');
                return;
            }

            const checkedBoxes = siswaForm.querySelectorAll('input[name="extracurricular_ids"]:checked');
            const extracurricularIds = Array.from(checkedBoxes).map(function (cb) { return Number(cb.value); });

            const payload = {
                kelas: siswaForm.kelas.value.trim(),
                wali_kelas: siswaForm.wali_kelas.value.trim(),
                kehadiran_persen: siswaForm.kehadiran_persen.value ? Number(siswaForm.kehadiran_persen.value) : null,
                rata_rata_nilai: siswaForm.rata_rata_nilai.value ? Number(siswaForm.rata_rata_nilai.value) : null,
                poin_prestasi: siswaForm.poin_prestasi.value ? Number(siswaForm.poin_prestasi.value) : 0,
                extracurricular_ids: extracurricularIds
            };

            try {
                await apiRequest('/api/siswa/' + id, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                });
                showFormNote(siswaForm, 'Data siswa berhasil diperbarui.', 'success');
                await loadSiswa();
            } catch (err) {
                showFormNote(siswaForm, err.message, 'error');
            }
        });
    }

    if (siswaAdminList) {
        siswaAdminList.addEventListener('click', function (e) {
            const btn = e.target.closest('button[data-action="edit"]');
            if (!btn) return;

            const li = btn.closest('.admin-list-item');
            const id = li ? li.dataset.id : null;
            if (!id) return;

            const item = siswaData.find(function (s) { return String(s.id) === id; });
            if (!item || !siswaForm) return;

            siswaForm.id.value = item.id;
            siswaForm.kelas.value = item.kelas || '';
            siswaForm.wali_kelas.value = item.wali_kelas || '';
            siswaForm.kehadiran_persen.value = item.kehadiran_persen ?? '';
            siswaForm.rata_rata_nilai.value = item.rata_rata_nilai ?? '';
            siswaForm.poin_prestasi.value = item.poin_prestasi ?? 0;

            const selectedIds = (item.extracurriculars || []).map(function (x) { return x.id; });
            renderSiswaEkstraCheckboxes(selectedIds);

            if (siswaFormTitle) siswaFormTitle.textContent = 'Edit Data: ' + item.name;
            siswaForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    /* -----------------------------------------------------
       8. STELA AI ASSISTANT (chat widget)
       Catatan: balasan STELA diambil dari endpoint Laravel
       POST /api/stela dengan body { message } yang membalas
       JSON { reply: "..." }, contoh route:

           Route::post('/api/stela', [StelaController::class, 'reply']);

       Selama endpoint belum tersedia, apiRequest() akan gagal
       (ke-catch) dan STELA menampilkan pesan fallback di bawah.
    ----------------------------------------------------- */

    const stelaTrigger = document.getElementById('stelaTrigger');
    const stelaPanel = document.getElementById('stelaPanel');
    const stelaClose = document.getElementById('stelaClose');
    const stelaMessages = document.getElementById('stelaMessages');
    const stelaForm = document.getElementById('stelaForm');
    const stelaInput = document.getElementById('stelaInput');
    const stelaSendBtn = stelaForm ? stelaForm.querySelector('.stela-send') : null;

    let stelaGreeted = false;
    let stelaBusy = false;
    let stelaHistory = [];

    // Konversi Markdown sederhana (bold, italic, list, paragraf) ke HTML.
    // Teks mentah di-escape dulu supaya aman, baru tag Markdown diproses,
    // jadi tidak ada risiko HTML asing ikut ter-render.
    function renderMarkdown(rawText) {
        const escaped = escapeHtml(rawText);
        const lines = escaped.split('\n');

        let html = '';
        let listBuffer = [];
        let listType = null; // 'ul' atau 'ol'

        function flushList() {
            if (listBuffer.length === 0) return;
            const tag = listType === 'ol' ? 'ol' : 'ul';
            html += '<' + tag + '>' + listBuffer.join('') + '</' + tag + '>';
            listBuffer = [];
            listType = null;
        }

        function inlineFormat(str) {
            return str
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/(^|[^*])\*(?!\*)(.+?)\*(?!\*)/g, '$1<em>$2</em>');
        }

        lines.forEach(function (line) {
            const trimmed = line.trim();
            const bulletMatch = trimmed.match(/^[-*]\s+(.*)$/);
            const numberedMatch = trimmed.match(/^\d+\.\s+(.*)$/);

            if (bulletMatch) {
                if (listType !== 'ul') flushList();
                listType = 'ul';
                listBuffer.push('<li>' + inlineFormat(bulletMatch[1]) + '</li>');
                return;
            }

            if (numberedMatch) {
                if (listType !== 'ol') flushList();
                listType = 'ol';
                listBuffer.push('<li>' + inlineFormat(numberedMatch[1]) + '</li>');
                return;
            }

            flushList();

            if (trimmed === '') {
                html += '<br>';
            } else {
                html += '<p>' + inlineFormat(trimmed) + '</p>';
            }
        });

        flushList();
        return html;
    }

    function stelaAddMessage(text, sender) {
        if (!stelaMessages) return;
        const bubble = document.createElement('div');
        bubble.className = 'stela-message ' + sender;

        if (sender === 'bot') {
            bubble.innerHTML = renderMarkdown(text);
        } else {
            bubble.textContent = text;
        }

        stelaMessages.appendChild(bubble);
        stelaMessages.scrollTop = stelaMessages.scrollHeight;
    }

    function stelaShowTyping() {
        if (!stelaMessages) return;
        const typing = document.createElement('div');
        typing.className = 'stela-typing';
        typing.id = 'stelaTypingIndicator';
        typing.innerHTML = '<span></span><span></span><span></span>';
        stelaMessages.appendChild(typing);
        stelaMessages.scrollTop = stelaMessages.scrollHeight;
    }

    function stelaHideTyping() {
        const typing = document.getElementById('stelaTypingIndicator');
        if (typing) typing.remove();
    }

    function openStela() {
        if (!stelaPanel) return;
        stelaPanel.classList.add('open');

        if (stelaTrigger) {
            stelaTrigger.classList.add('is-open');
            stelaTrigger.setAttribute('aria-expanded', 'true');
        }

        if (!stelaGreeted) {
            stelaGreeted = true;
            stelaAddMessage(
                'Halo! Aku STELA, asisten AI Portal Kesiswaan MTsN 1 Wonosobo. ' +
                'Ada yang bisa aku bantu — jadwal pelajaran, ekstrakurikuler, konseling, atau pengumuman?',
                'bot'
            );
        }

        if (stelaInput) stelaInput.focus();
    }

    function closeStela() {
        if (!stelaPanel) return;
        stelaPanel.classList.remove('open');

        if (stelaTrigger) {
            stelaTrigger.classList.remove('is-open');
            stelaTrigger.setAttribute('aria-expanded', 'false');
        }
    }

    if (stelaTrigger) {
        stelaTrigger.addEventListener('click', function () {
            const isOpen = stelaPanel && stelaPanel.classList.contains('open');
            isOpen ? closeStela() : openStela();
        });
    }

    if (stelaClose) {
        stelaClose.addEventListener('click', closeStela);
    }

    // Escape juga menutup panel STELA (selaras dengan modal lain di bagian 3)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && stelaPanel && stelaPanel.classList.contains('open')) {
            closeStela();
        }
    });

    if (stelaForm) {
        stelaForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (stelaBusy || !stelaInput) return;

            const message = stelaInput.value.trim();
            if (!message) return;

            stelaAddMessage(message, 'user');
            stelaInput.value = '';
            stelaBusy = true;
            if (stelaSendBtn) stelaSendBtn.disabled = true;
            stelaShowTyping();

            try {
                const result = await apiRequest('/api/stela', {
                    method: 'POST',
                    body: JSON.stringify({ message: message, history: stelaHistory })
                });

                stelaHideTyping();

                const replyText = result && result.reply ? result.reply : 'Maaf, aku belum punya jawaban untuk itu.';
                stelaAddMessage(replyText, 'bot');

                // simpan ke riwayat supaya STELA ingat konteks percakapan berikutnya
                stelaHistory.push({ role: 'user', text: message });
                stelaHistory.push({ role: 'model', text: replyText });

                // batasi riwayat maksimal 20 entri biar tidak kebesaran
                if (stelaHistory.length > 20) {
                    stelaHistory = stelaHistory.slice(-20);
                }
            } catch (err) {
                stelaHideTyping();
                stelaAddMessage('Maaf, STELA sedang tidak dapat diakses. Coba lagi sebentar lagi, ya.', 'bot');
            } finally {
                stelaBusy = false;
                if (stelaSendBtn) stelaSendBtn.disabled = false;
                stelaInput.focus();
            }
        });
    }

    // --- ambil & render data awal saat halaman dimuat ---

    loadAnnouncements();
    loadExtracurriculars();
    loadSchedule();
    loadKesiswaan();
    loadExams();
    loadMaterials();

    if (konselingAdminList) {
        loadKonseling();
    }

    if (siswaAdminList) {
        loadSiswa();
    }

});