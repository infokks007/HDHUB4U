<?php
// This line should be at the top of every page that uses the header.
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- The title will be set on each page individually -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
</head>
<body>

    <!-- ======== HEADER SECTION ======== -->
    <header id="header-wrap">
        <div class="header-top-bar">
            <div class="header-icon" id="mobile-menu-trigger"><i class="fa-solid fa-bars"></i></div>
            <div class="header-logo">
                <a href="/"><img src="https://blogger.googleusercontent.com/img/a/AVvXsEhQEB-rYCJGjEvEL3eQt_XUhQacZPNhcgiAIVTYPE_1cFjyn8F6HyzUscs_R_rl27E2ZWYoERppCD9W9I0edmZXqGTMwY79k_WJS4e0umHgapZ1rFvOy8MvIjqSdugOKzGGkZR20_SEbPU7X28p1c8bw3iIfrf1tTnCC9wenrjnBosqmMM-AzsrsJCStSz5=s260" alt="Logo"></a>
            </div>
            <div class="header-icon" id="search-icon-trigger"><i class="fa-solid fa-magnifying-glass"></i></div>
        </div>
        <div id="search-container">
            <!-- UPDATED: The form now points to search.php -->
            <form action="search.php" method="get" class="search-form">
                <input type="text" name="q" class="search-input" placeholder="Search movies..." required>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
    </header>

    <!-- ======== HIDDEN MOBILE MENU (NOW DYNAMIC) ======== -->
    <div id="overlay"></div>
    <nav id="mobile-nav">
        <div id="close-menu-btn">&times;</div>
        <?php
        // Fetch categories from the database specifically for the mobile menu
        $mobile_menu_categories = $conn->query("SELECT * FROM categories ORDER BY sort_order ASC");
        if ($mobile_menu_categories && $mobile_menu_categories->num_rows > 0) {
            while($category = $mobile_menu_categories->fetch_assoc()) {
                // Create a link for each category
                echo '<a href="category.php?slug=' . htmlspecialchars($category['slug']) . '">' . htmlspecialchars($category['name']) . '</a>';
            }
        }
        ?>
    </nav>

    <!-- ======== CATEGORY AND TELEGRAM BAR ======== -->
    <section class="category-bar">
        <div class="container">
            <div class="category-buttons">
                <?php
                $categories_result_header = $conn->query("SELECT * FROM categories ORDER BY sort_order ASC");
                if ($categories_result_header->num_rows > 0) {
                    while($category = $categories_result_header->fetch_assoc()) {
                        echo '<a href="category.php?slug=' . htmlspecialchars($category['slug']) . '" class="btn-grad ' . htmlspecialchars($category['style_class']) . '">' . htmlspecialchars($category['name']) . '</a>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- ======== AUTO-PLAY MOVIE SLIDER ======== -->
    <section class="slider-section">
        <div class="swiper auto-slider">
            <div class="swiper-wrapper">
                <?php
                $sliders_result_header = $conn->query("SELECT * FROM sliders ORDER BY sort_order ASC");
                if ($sliders_result_header->num_rows > 0) {
                    while($slide = $sliders_result_header->fetch_assoc()) {
                        echo '<div class="swiper-slide">';
                        if (!empty($slide['target_url'])) { echo '<a href="' . htmlspecialchars($slide['target_url']) . '">'; }
                        echo '<img src="' . htmlspecialchars($slide['image_url']) . '" alt="Slider Movie">';
                        if (!empty($slide['target_url'])) { echo '</a>'; }
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>