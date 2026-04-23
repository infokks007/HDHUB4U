<?php
// Step 1: Connect to DB and fetch all data for the page BEFORE loading the header.
require_once 'config/db.php';

// Check for movie ID in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No movie specified.");
}
$id = intval($_GET['id']);

// Fetch main movie details
$stmt_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt_movie->bind_param("i", $id);
$stmt_movie->execute();
$movie_result = $stmt_movie->get_result();
if ($movie_result->num_rows === 0) { die("Movie not found."); }
$movie = $movie_result->fetch_assoc();

// Fetch all links and find the primary watch link for the button
$stmt_links = $conn->prepare("SELECT * FROM movie_links WHERE movie_id = ? ORDER BY quality DESC");
$stmt_links->bind_param("i", $id);
$stmt_links->execute();
$links_result = $stmt_links->get_result();
$all_links = [];
$primary_watch_link = ''; // This will hold the link for our new button
while($link = $links_result->fetch_assoc()){ 
    $all_links[] = $link;
    if (empty($primary_watch_link) && !empty($link['watch_link'])) {
        $primary_watch_link = $link['watch_link'];
    }
}

// Fetch all categories for this movie
$stmt_cats = $conn->prepare("SELECT c.id, c.name, c.slug FROM categories c JOIN movie_categories mc ON c.id = mc.category_id WHERE mc.movie_id = ?");
$stmt_cats->bind_param("i", $id);
$stmt_cats->execute();
$cats_result = $stmt_cats->get_result();

// Get the first category's ID to find related movies
$first_category_id = null;
if ($cats_result->num_rows > 0) {
    $first_cat = $cats_result->fetch_assoc();
    $first_category_id = $first_cat['id'];
    mysqli_data_seek($cats_result, 0); // Reset pointer to loop again later for display
}

// Step 2: Now that we have the movie title, include the site header.
include 'header.php';
?>

<!-- Set the unique title for this specific movie page -->
<title><?php echo htmlspecialchars($movie['title']); ?> - Details and Download</title>
<style>
    /* This CSS is specific to the movie page design */
    .movie-page-container { max-width: 1000px; margin: 30px auto; padding: 0 15px; }
    
    /* --- THIS IS THE BACKGROUND FIX --- */
    .details-grid, .download-section, .related-movies-section {
        background-color: #1a1a1a; /* Dark charcoal background for the block */
        padding: 25px;
        border-radius: 8px;
        border: 1px solid #222;
        margin-top: 30px;
    }
    /* --- END OF BACKGROUND FIX --- */

    .details-grid { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }
    .details-poster img { width: 100%; height: auto; border-radius: 8px; }
    .details-info h1 { margin-top: 0; margin-bottom: 10px; font-size: 2.2em; line-height: 1.3; }
    .details-info h1 i { margin-right: 12px; color: #ccc; }
    .details-info .quality-string { font-size: 1.2em; font-weight: bold; color: #ddd; margin: 0 0 20px 0; }
    .movie-meta-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 25px; }
    .meta-tag { color: #fff; font-size: 14px; font-weight: bold; padding: 8px 15px; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4); }
    .meta-tag.tag-date { background: linear-gradient(45deg, #e40056, #ff416c); }
    .meta-tag.tag-quality { background: linear-gradient(45deg, #7a00ff, #e100ff); }
    
    /* --- WATCH BUTTON STYLE --- */
    .action-buttons { margin-bottom: 25px; }
    .btn-watch-now {
        display: inline-block;
        width: 100%;
        text-align: center;
        padding: 15px;
        font-size: 1.4em;
        font-weight: bold;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 114, 255, 0.4);
    }
    .btn-watch-now:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 114, 255, 0.6);
    }
    .btn-watch-now i { margin-right: 10px; }
    /* --- END WATCH BUTTON STYLE --- */

    .description { color: #ccc; line-height: 1.7; }
    .download-section h2, .related-movies-section h2 { margin-top: 0; font-size: 1.8em; border-bottom: 2px solid #f57f26; padding-bottom: 10px; }
    .download-links-list { list-style: none; padding: 0; margin: 20px 0 0 0; }
    .download-links-list li { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #333; }
    .download-links-list li:last-child { border-bottom: none; }
    .download-links-list .quality-label { font-weight: bold; font-size: 1.1em; }
    .download-links-list .btn-download { color: white; padding: 10px 25px; border-radius: 5px; text-decoration: none; background-image: linear-gradient(to right, #00c6ff, #0072ff); transition: transform 0.2s; }
    .download-links-list .btn-download:hover { transform: scale(1.05); }
    @media (max-width: 768px) { .details-grid { grid-template-columns: 1fr; } .details-poster { max-width: 300px; margin: 0 auto; } .details-info h1 { text-align: center; } }
</style>

<main class="movie-page-container">
    <div class="details-grid">
        <div class="details-poster">
            <img src="uploads/<?php echo htmlspecialchars($movie['poster_filename']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        </div>
        <div class="details-info">
            <h1><i class="fa-solid fa-film"></i> <?php echo htmlspecialchars($movie['title']); ?></h1>
            <p class="quality-string"><?php echo htmlspecialchars($movie['quality_string']); ?></p>
            <div class="movie-meta-tags">
                <span class="meta-tag tag-date"><i class="fa-solid fa-calendar-days"></i> <?php echo date("F j, Y", strtotime($movie['release_date'])); ?></span>
                <span class="meta-tag tag-quality"><i class="fa-solid fa-video"></i> <?php echo htmlspecialchars($movie['primary_quality_tag']); ?></span>
            </div>

            <!-- THE WATCH NOW BUTTON IS HERE -->
            <?php if (!empty($primary_watch_link)): ?>
                <div class="action-buttons">
                    <a href="watch.php?id=<?php echo $movie['id']; ?>" class="btn-watch-now">
                        <i class="fa-solid fa-play"></i> Watch Now
                    </a>
                </div>
            <?php endif; ?>

            <p class="description"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
        </div>
    </div>

    <div class="download-section">
        <h2><i class="fa-solid fa-download"></i> Download Links</h2>
        <ul class="download-links-list">
            <?php foreach($all_links as $link): if (!empty($link['download_link'])): ?>
                <li>
                    <span class="quality-label"><?php echo htmlspecialchars($link['quality']); ?></span>
                    <a href="<?php echo htmlspecialchars($link['download_link']); ?>" target="_blank" class="btn-download"><i class="fa-solid fa-download"></i> Download</a>
                </li>
            <?php endif; endforeach; ?>
        </ul>
    </div>

    <!-- YOU MAY ALSO LIKE SECTION -->
    <div class="related-movies-section">
        <h2>You May Also Like</h2>
        <div class="movie-grid">
            <?php
            if ($first_category_id !== null) {
                $stmt_related = $conn->prepare("SELECT m.* FROM movies m JOIN movie_categories mc ON m.id = mc.movie_id WHERE mc.category_id = ? AND m.id != ? ORDER BY RAND() LIMIT 6");
                $stmt_related->bind_param("ii", $first_category_id, $id);
                $stmt_related->execute();
                $related_movies_result = $stmt_related->get_result();
                if ($related_movies_result && $related_movies_result->num_rows > 0) {
                    while($related_movie = $related_movies_result->fetch_assoc()) {
            ?>
            <div class="movie-item">
                <a href="movie.php?id=<?php echo $related_movie['id']; ?>">
                    <div class="movie-poster">
                         <img src="uploads/<?php echo htmlspecialchars($related_movie['poster_filename']); ?>" alt="<?php echo htmlspecialchars($related_movie['title']); ?>">
                    </div>
                    <div class="movie-info">
                        <h4 class="movie-title"><i class="fa-solid fa-film"></i> <?php echo htmlspecialchars($related_movie['title']); ?></h4>
                        <p class="movie-quality"><?php echo htmlspecialchars($related_movie['quality_string'] ?? 'HD Quality'); ?></p>
                        <div class="movie-meta-tags">
                            <span class="meta-tag tag-date"><i class="fa-solid fa-calendar-days"></i> <?php echo date("F j, Y", strtotime($related_movie['release_date'])); ?></span>
                            <span class="meta-tag tag-quality"><i class="fa-solid fa-video"></i> <?php echo htmlspecialchars($related_movie['primary_quality_tag']); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php
                    }
                } else { echo "<p style='color:#777; grid-column: 1/-1;'>No related movies found.</p>"; }
            } else { echo "<p style='color:#777; grid-column: 1/-1;'>No categories assigned to find related movies.</p>"; }
            ?>
        </div>
    </div>
</main>

<?php
include 'footer.php';
?>