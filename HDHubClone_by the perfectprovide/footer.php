<!-- ======== FLOATING BUTTONS ======== -->
    <a class='buy-button' href='#'><i class="fa-solid fa-cart-shopping"></i> Buy In 500</a>
    <a class='back-to-top' href='#'><i class="fa-solid fa-arrow-up"></i></a>

    <!-- Swiper.js Javascript Link -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    <!-- All Javascript Functionality -->
    <script>
        // --- Mobile Menu ---
        const mobileMenuTrigger = document.getElementById('mobile-menu-trigger');
        const mobileNav = document.getElementById('mobile-nav');
        const closeMenuBtn = document.getElementById('close-menu-btn');
        const overlay = document.getElementById('overlay');
        const body = document.body;
        function openMenu() { body.classList.add('mobile-menu-active'); }
        function closeMenu() { body.classList.remove('mobile-menu-active'); }
        if(mobileMenuTrigger) mobileMenuTrigger.addEventListener('click', openMenu);
        if(closeMenuBtn) closeMenuBtn.addEventListener('click', closeMenu);
        if(overlay) overlay.addEventListener('click', closeMenu);
        
        // --- Search Bar ---
        const searchIconTrigger = document.getElementById('search-icon-trigger');
        const searchContainer = document.getElementById('search-container');
        if(searchIconTrigger) {
            const searchIcon = searchIconTrigger.querySelector('i');
            let isSearchVisible = false;
            searchIconTrigger.addEventListener('click', function() {
                if (!isSearchVisible) {
                    searchContainer.style.display = 'block';
                    searchIcon.classList.replace('fa-magnifying-glass', 'fa-times');
                    isSearchVisible = true;
                } else {
                    searchContainer.style.display = 'none';
                    searchIcon.classList.replace('fa-times', 'fa-magnifying-glass');
                    isSearchVisible = false;
                }
            });
        }

        // --- Auto-Play Slider Initialization ---
        const swiper = new Swiper('.auto-slider', {
            loop: true,
            spaceBetween: 15,
            slidesPerView: 'auto',
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            breakpoints: {
                320: { slidesPerView: 3 },
                480: { slidesPerView: 4 },
                768: { slidesPerView: 7 },
                1024: { slidesPerView: 9 },
            }
        });
    </script>
</body>
</html>