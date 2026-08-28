document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       NAVIGATION
    ===================================================== */

    const pages = document.querySelectorAll(".page");
    const navButtons = document.querySelectorAll("[data-target]");
    const mobileMenu = document.querySelector(".mobile-menu");
    const mainNav = document.querySelector(".main-nav");


    function showPage(pageName) {

        if (!pageName) return;


        /* Sembunyikan semua page */

        pages.forEach(function (page) {
            page.classList.remove("active");
        });


        /* Hapus active navbar */

        document
            .querySelectorAll(".nav-link")
            .forEach(function (button) {

                button.classList.remove("active");

            });


        /* Cari page */

        const targetPage =
            document.querySelector(
                '.page[data-page="' + pageName + '"]'
            );


        if (!targetPage) {

            console.error(
                "Page tidak ditemukan:",
                pageName
            );

            return;
        }


        /* Tampilkan page */

        targetPage.classList.add("active");


        /* Aktifkan navbar */

        document
            .querySelectorAll(".nav-link")
            .forEach(function (button) {

                if (
                    button.getAttribute("data-target")
                    === pageName
                ) {

                    button.classList.add("active");

                }

            });


        /* Tutup menu mobile */

        if (mainNav) {
            mainNav.classList.remove("show");
        }

        if (mobileMenu) {

            mobileMenu.innerHTML = "☰";

            mobileMenu.setAttribute(
                "aria-expanded",
                "false"
            );

        }


        /* Scroll atas */

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

    }



    /* =====================================================
       SEMUA BUTTON DATA-TARGET
    ===================================================== */

    navButtons.forEach(function (button) {

        button.addEventListener("click", function (event) {

            event.preventDefault();

            const target =
                button.getAttribute("data-target");

            showPage(target);

        });

    });



    /* =====================================================
       MOBILE MENU
    ===================================================== */

    if (mobileMenu && mainNav) {

        mobileMenu.addEventListener(
            "click",
            function (event) {

                event.stopPropagation();

                const isOpen =
                    mainNav.classList.toggle("show");


                if (isOpen) {

                    mobileMenu.innerHTML = "✕";

                    mobileMenu.setAttribute(
                        "aria-expanded",
                        "true"
                    );

                } else {

                    mobileMenu.innerHTML = "☰";

                    mobileMenu.setAttribute(
                        "aria-expanded",
                        "false"
                    );

                }

            }
        );

    }



    /* =====================================================
       CLOSE MOBILE MENU
    ===================================================== */

    document.addEventListener(
        "click",
        function (event) {

            if (!mainNav || !mobileMenu) {
                return;
            }

            if (
                !mainNav.contains(event.target) &&
                !mobileMenu.contains(event.target)
            ) {

                mainNav.classList.remove("show");

                mobileMenu.innerHTML = "☰";

                mobileMenu.setAttribute(
                    "aria-expanded",
                    "false"
                );

            }

        }
    );



    /* =====================================================
       LOGIN MODAL
    ===================================================== */

    const modal =
        document.getElementById("loginModal");

    const loginButtons =
        document.querySelectorAll(".login-btn");

    const modalClose =
        document.querySelector(".modal-close");


    function openModal() {

        if (!modal) return;

        modal.classList.add("show");

        document.body.classList.add(
            "modal-open"
        );

    }


    function closeModal() {

        if (!modal) return;

        modal.classList.remove("show");

        document.body.classList.remove(
            "modal-open"
        );

    }


    loginButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                openModal();

            }
        );

    });


    if (modalClose) {

        modalClose.addEventListener(
            "click",
            function () {

                closeModal();

            }
        );

    }


    if (modal) {

        modal.addEventListener(
            "click",
            function (event) {

                if (
                    event.target === modal
                ) {

                    closeModal();

                }

            }
        );

    }



    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape"
            ) {

                closeModal();

            }

        }
    );



    /* =====================================================
       LOGIN FORM
    ===================================================== */

    const loginForm =
        document.getElementById("loginForm");


    if (loginForm) {

        loginForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();

                alert(
                    "Fitur login akan terhubung dengan sistem akun siswa."
                );

            }
        );

    }



    /* =====================================================
       COUNSELING FORM
    ===================================================== */

    const counselingForm =
        document.getElementById(
            "counselingForm"
        );


    if (counselingForm) {

        counselingForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                const nama =
                    counselingForm
                        .querySelector(
                            '[name="nama"]'
                        );

                const kelas =
                    counselingForm
                        .querySelector(
                            '[name="kelas"]'
                        );

                const masalah =
                    counselingForm
                        .querySelector(
                            '[name="masalah"]'
                        );


                if (
                    !nama.value.trim()
                ) {

                    alert(
                        "Silakan isi nama terlebih dahulu."
                    );

                    nama.focus();

                    return;

                }


                if (
                    !kelas.value.trim()
                ) {

                    alert(
                        "Silakan isi kelas terlebih dahulu."
                    );

                    kelas.focus();

                    return;

                }


                if (
                    !masalah.value.trim()
                ) {

                    alert(
                        "Silakan ceritakan hal yang ingin dikonsultasikan."
                    );

                    masalah.focus();

                    return;

                }


                alert(
                    "Pengajuan konseling berhasil dikirim."
                );


                counselingForm.reset();

            }
        );

    }



    /* =====================================================
       FILTER EKSTRA
    ===================================================== */

    const filters =
        document.querySelectorAll(".filter");

    const extraCards =
        document.querySelectorAll(".extra-card");


    filters.forEach(function (filter) {

        filter.addEventListener(
            "click",
            function () {


                filters.forEach(
                    function (item) {

                        item.classList.remove(
                            "active"
                        );

                    }
                );


                filter.classList.add(
                    "active"
                );


                const category =
                    filter.getAttribute(
                        "data-filter"
                    );


                extraCards.forEach(
                    function (card) {

                        const cardCategory =
                            card.getAttribute(
                                "data-category"
                            );


                        if (
                            category === "all" ||
                            cardCategory === category
                        ) {

                            card.style.display =
                                "";

                        } else {

                            card.style.display =
                                "none";

                        }

                    }
                );

            }
        );

    });



    /* =====================================================
       SEARCH EKSTRA
    ===================================================== */

    const searchInput =
        document.querySelector(
            ".filter-row input"
        );


    if (searchInput) {

        searchInput.addEventListener(
            "input",
            function () {

                const keyword =
                    searchInput.value
                        .toLowerCase()
                        .trim();


                extraCards.forEach(
                    function (card) {

                        const text =
                            card.textContent
                                .toLowerCase();


                        if (
                            text.includes(keyword)
                        ) {

                            card.style.display =
                                "";

                        } else {

                            card.style.display =
                                "none";

                        }

                    }
                );

            }
        );

    }



    /* =====================================================
       THEME BUTTON
    ===================================================== */

    const themeButton =
        document.querySelector(
            ".theme-button"
        );


    if (themeButton) {

        themeButton.addEventListener(
            "click",
            function () {

                document.body.classList.toggle(
                    "dark-preview"
                );

            }
        );

    }



    /* =====================================================
       INITIAL
    ===================================================== */

    showPage("home");


    console.log(
        "Portal Kesiswaan MTsN 1 Wonosobo berhasil dimuat."
    );

});