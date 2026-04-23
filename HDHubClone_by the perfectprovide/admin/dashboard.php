<?php
require_once 'auth.php'; // Security check

// --- SEARCH LOGIC ---
$search_term = '';
$sql_condition = '';
$params = [];
if (!empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $sql_condition = " WHERE title LIKE ?";
    $params[] = '%' . $search_term . '%';
}

// Fetch data for all sections
$movies_sql = "SELECT id, title, poster_filename FROM movies" . $sql_condition . " ORDER BY id DESC";
$stmt_movies = $conn->prepare($movies_sql);
if (!empty($params)) {
    $stmt_movies->bind_param("s", $params[0]);
}
$stmt_movies->execute();
$movies_result = $stmt_movies->get_result();

$sliders_result = $conn->query("SELECT * FROM sliders ORDER BY sort_order ASC");
$categories_result = $conn->query("SELECT * FROM categories ORDER BY sort_order ASC");
$all_categories_for_form = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        /* Basic Setup */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #000; color: #fff; margin: 0; }
        .dashboard-container { max-width: 1200px; margin: 20px auto; padding: 0 15px; }
        h1, h2 { margin-top: 0; border-bottom: 2px solid #f57f26; padding-bottom: 10px; }
        h1 { text-align: center; }
        .form-section { background: #1a1a1a; padding: 25px; border-radius: 8px; border: 1px solid #333; }
        .logout-link { display: block; text-align: right; margin-bottom: 20px; color: #ff4b2b; font-weight: bold; }

        /* Tabbed Navigation */
        .admin-nav { display: flex; background-color: #1a1a1a; border-radius: 8px; margin-bottom: 20px; overflow-x: auto; }
        .nav-tab { padding: 15px 20px; cursor: pointer; color: #ccc; font-weight: bold; border-bottom: 3px solid transparent; transition: all 0.3s; white-space: nowrap; }
        .nav-tab.active, .nav-tab:hover { color: #fff; border-bottom-color: #f57f26; }
        .tab-content { display: none; animation: fadeIn 0.5s; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Form & Table Styles */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .full-width { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #ccc; }
        input, textarea, select { width: 100%; padding: 12px; border-radius: 5px; border: 1px solid #555; background: #333; color: #fff; box-sizing: border-box; font-size: 1em; }
        select[multiple] { height: 150px; }
        button, .btn-action { padding: 12px 30px; border: none; border-radius: 5px; color: white; font-weight: bold; cursor: pointer; font-size: 1em; background-color: #0072ff; }
        .btn-cancel { background-color: #555; }
        .list-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .list-table th, .list-table td { padding: 12px 15px; border: 1px solid #333; text-align: left; vertical-align: middle; }
        .list-table img { width: 70px; height: auto; border-radius: 4px; }
        .delete-link { color: #ff4b2b; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex-grow: 1; }
        
        /* Specific layouts for category and slider forms for desktop */
        .category-form-grid { grid-template-columns: 2fr 1fr 1fr auto; align-items: flex-end; }
        .slider-form-grid { grid-template-columns: 2fr 2fr 1fr auto; align-items: flex-end; }
        .list-table img.slider-preview { width: 200px; }

        /* --- MOBILE FRIENDLY FIX --- */
        @media (max-width: 768px) {
            h1 { font-size: 1.5em; }
            .form-grid { grid-template-columns: 1fr; } /* General rule for all grids on mobile */
            
            /* Specific fix for category and slider forms to stack vertically */
            .category-form-grid, .slider-form-grid {
                grid-template-columns: 1fr;
                align-items: stretch; /* Make items full width */
            }
            .nav-tab { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <a href="logout.php" class="logout-link">Logout</a>
    <h1>Admin Panel</h1>

    <!-- TAB NAVIGATION -->
    <nav class="admin-nav">
        <div class="nav-tab active" data-tab="movies"><i class="fa fa-film"></i> Movies</div>
        <div class="nav-tab" data-tab="add_movie"><i class="fa fa-plus-circle"></i> Add/Edit Movie</div>
        <div class="nav-tab" data-tab="categories"><i class="fa fa-tags"></i> Categories</div>
        <div class="nav-tab" data-tab="slider"><i class="fa fa-images"></i> Slider</div>
    </nav>

    <!-- TAB CONTENT: MOVIE LIBRARY -->
    <div id="movies" class="tab-content active">
        <div class="form-section">
            <h2>Movie Library</h2>
            <form class="search-bar" method="GET" action="dashboard.php">
                <input type="hidden" name="tab" value="movies"> <!-- Keep the tab active after search -->
                <input type="text" name="search" placeholder="Search for a movie title..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
            <table class="list-table">
                <thead><tr><th>Poster</th><th>Title</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while ($movie = $movies_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../uploads/<?php echo htmlspecialchars($movie['poster_filename']); ?>"></td>
                        <td><?php echo htmlspecialchars($movie['title']); ?></td>
                        <td>
                            <a class="edit-btn" data-id="<?php echo $movie['id']; ?>" style="color:#00c6ff; cursor:pointer;">Edit</a>
                            <a href="delete.php?id=<?php echo $movie['id']; ?>" class="delete-link" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB CONTENT: ADD/EDIT MOVIE FORM -->
    <div id="add_movie" class="tab-content">
        <div class="form-section">
            <h2 id="form-title">Add New Movie</h2>
            <form id="movie-form" action="process_add.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="movie-id" name="id">
                <div class="form-grid">
                    <div class="form-group"><label>Movie Title</label><input type="text" id="title" name="title" required></div>
                    <div class="form-group"><label>Movie Poster</label><input type="file" id="poster_file" name="poster_file" accept="image/*"></div>
                    <div class="full-width form-group"><label>Description</label><textarea id="description" name="description"></textarea></div>
                    <div class="form-group"><label>Release Date</label><input type="date" id="release_date" name="release_date"></div>
                    <div class="form-group"><label>Director</label><input type="text" id="director" name="director"></div>
                    <div class="form-group"><label>Cast</label><input type="text" id="movie_cast" name="movie_cast"></div>
                    <div class="form-group"><label>Genre</label><input type="text" id="genre" name="genre"></div>
                    <div class="full-width form-group">
                        <label>Categories (Hold Ctrl/Cmd to select multiple)</label>
                        <select id="category_ids" name="category_ids[]" multiple required>
                            <?php mysqli_data_seek($all_categories_for_form, 0); ?>
                            <?php while ($cat = $all_categories_for_form->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <hr style="border-color: #333; margin: 20px 0;">
                <label>Watch & Download Links</label>
                <div id="links-container"></div>
                <button type="button" class="btn-action" id="add-link-btn" style="background-color: #00aaff; margin-top: 10px;">Add Link</button>
                <hr style="border-color: #333; margin: 20px 0;">
                <button type="submit">Save Movie</button>
                <button type="button" class="btn-cancel" id="cancel-edit" style="display:none;">Cancel Edit</button>
            </form>
        </div>
    </div>

    <!-- TAB CONTENT: CATEGORIES -->
    <div id="categories" class="tab-content">
        <div class="form-section">
            <h2>Manage Categories</h2>
            <form action="process_add_category.php" method="POST">
                <!-- ADDED a specific class 'category-form-grid' for the mobile fix -->
                <div class="form-grid category-form-grid">
                    <div class="form-group"><label>Button Name</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Style Class</label><input type="text" name="style_class" value="style-1" required></div>
                    <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    <div class="form-group"><button type="submit" style="width:100%;">Add</button></div>
                </div>
            </form>
            <table class="list-table">
                 <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
                 <tbody>
                    <?php mysqli_data_seek($categories_result, 0); ?>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <tr>
                        <td><a class="btn-grad <?php echo htmlspecialchars($category['style_class']); ?>"><?php echo htmlspecialchars($category['name']); ?></a></td>
                        <td>/category.php?slug=<?php echo htmlspecialchars($category['slug']); ?></td>
                        <td><a href="delete_category.php?id=<?php echo $category['id']; ?>" class="delete-link" onclick="return confirm('Are you sure?');">Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                 </tbody>
            </table>
        </div>
    </div>

    <!-- TAB CONTENT: SLIDER -->
    <div id="slider" class="tab-content">
        <div class="form-section">
            <h2>Manage Homepage Slider</h2>
            <form action="process_add_slider.php" method="POST">
                 <!-- ADDED a specific class 'slider-form-grid' for the mobile fix -->
                <div class="form-grid slider-form-grid">
                    <div class="form-group"><label>Image URL</label><input type="url" name="image_url" required></div>
                    <div class="form-group"><label>Target URL (Optional)</label><input type="url" name="target_url"></div>
                    <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="0"></div>
                    <div class="form-group"><button type="submit">Add Slide</button></div>
                </div>
            </form>
            <table class="list-table">
                <thead><tr><th>Preview</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php mysqli_data_seek($sliders_result, 0); ?>
                    <?php while ($slide = $sliders_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($slide['image_url']); ?>" class="slider-preview"></td>
                        <td><a href="delete_slider.php?id=<?php echo $slide['id']; ?>" class="delete-link" onclick="return confirm('Are you sure?');">Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    // --- TAB SWITCHING LOGIC ---
    function switchTab(tabId) {
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        const activeTab = document.querySelector(`.nav-tab[data-tab="${tabId}"]`);
        const activeContent = document.getElementById(tabId);
        if (activeTab) activeTab.classList.add('active');
        if (activeContent) activeContent.classList.add('active');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Update URL hash for better UX (optional)
            window.location.hash = tab.dataset.tab;
            switchTab(tab.dataset.tab);
        });
    });

    // Check URL hash on page load to open the correct tab
    const currentHash = window.location.hash.substring(1);
    if (currentHash) {
        switchTab(currentHash);
    } else if ("<?php echo !empty($search_term); ?>") {
        switchTab('movies'); // Stay on movies tab after a search
    }

    // --- DYNAMIC LINKS LOGIC ---
    const linksContainer = document.getElementById('links-container');
    document.getElementById('add-link-btn').addEventListener('click', () => addLinkGroup());
    linksContainer.addEventListener('click', e => {
        if (e.target.classList.contains('btn-remove-link')) e.target.closest('.link-group').remove();
    });
    function addLinkGroup(q = '', w = '', d = '') {
        const div = document.createElement('div');
        div.className = 'link-group';
        div.innerHTML = `<div class="form-grid" style="grid-template-columns: 1fr 2fr 2fr auto; align-items:center; gap:10px; margin-bottom:10px;"><input type="text" name="links[quality][]" placeholder="Quality" value="${q}" required><input type="url" name="links[watch_link][]" placeholder="Watch Link" value="${w}"><input type="url" name="links[download_link][]" placeholder="Download Link" value="${d}"><button type="button" class="btn-remove-link" style="background-color:#ff416c; padding:12px;">X</button></div>`;
        linksContainer.appendChild(div);
    }

    // --- EDIT MOVIE LOGIC (FIXED) ---
    const form = document.getElementById('movie-form');
    const formTitle = document.getElementById('form-title');
    const cancelBtn = document.getElementById('cancel-edit');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.dataset.id;
            
            fetch(`get_movie.php?id=${movieId}`)
                .then(response => response.json())
                .then(data => {
                    switchTab('add_movie');
                    form.action = 'process_edit.php';
                    formTitle.textContent = 'Edit Movie: ' + data.movie.title;
                    document.getElementById('movie-id').value = data.movie.id;
                    document.getElementById('title').value = data.movie.title;
                    document.getElementById('description').value = data.movie.description;
                    document.getElementById('release_date').value = data.movie.release_date;
                    document.getElementById('director').value = data.movie.director;
                    document.getElementById('movie_cast').value = data.movie.movie_cast;
                    document.getElementById('genre').value = data.movie.genre;

                    const categorySelect = document.getElementById('category_ids');
                    Array.from(categorySelect.options).forEach(option => {
                        option.selected = data.categories.includes(parseInt(option.value));
                    });

                    linksContainer.innerHTML = '';
                    if (data.links.length > 0) {
                        data.links.forEach(link => addLinkGroup(link.quality, link.watch_link, link.download_link));
                    } else {
                        addLinkGroup();
                    }

                    cancelBtn.style.display = 'inline-block';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
        });
    });

    cancelBtn.addEventListener('click', function() {
        form.reset();
        form.action = 'process_add.php';
        formTitle.textContent = 'Add New Movie';
        linksContainer.innerHTML = '';
        this.style.display = 'none';
        switchTab('movies');
    });
});
</script>
</body>
</html>