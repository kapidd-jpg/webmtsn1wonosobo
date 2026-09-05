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

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    async function apiRequest(url, options = {}) {
        const headers = Object.assign(
            { 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
                    '<span class="announcement-date">' + escapeHtml(item.tanggal) + ' ' + escapeHtml(item.bulan) + '</span>' +
                    '<span class="announcement-category">' + escapeHtml(item.kategori) + '</span>' +
                    '<h3>' + escapeHtml(item.judul) + '</h3>' +
                    '<p>' + escapeHtml(item.deskripsi) + '</p>' +
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
                scheduleData.push(Object.assign({ id: getNextId(scheduleData) }, payload));
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
                extracurricularsData.push(Object.assign({ id: getNextId(extracurricularsData) }, payload));
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

    function stelaAddMessage(text, sender) {
        if (!stelaMessages) return;
        const bubble = document.createElement('div');
        bubble.className = 'stela-message ' + sender;
        bubble.textContent = text;
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
                    body: JSON.stringify({ message: message })
                });

                stelaHideTyping();
                stelaAddMessage(
                    result && result.reply ? result.reply : 'Maaf, aku belum punya jawaban untuk itu.',
                    'bot'
                );
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

});