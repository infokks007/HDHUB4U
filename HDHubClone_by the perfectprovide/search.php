<?php
// This includes the header, which also connects to the database.
include 'header.php';

// Check if a search query 'q' was submitted in the URL.
$search_query = '';
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search_query = trim($_GET['q']);
    
    // --- SECURE DATABASE QUERY ---
    // Prepare the search term by adding wildcards for a partial match.
    $search_term = '%' . $search_query . '%';

    // Use a prepared statement to prevent SQL injection attacks.
    $stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY id DESC");
    $stmt->bind_param("s", $search_term); // 's' means the parameter is a string.
    $stmt->execute();
    $movies_result = $stmt->get_result();
} else {
    $movies_result = null; // No search was performed.
}
?>

<!-- Set the unique title for the search page -->
<title>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</title>

<!-- This is the main content area for the search results grid -->
<main id="content-wrapper">
    <div class="container">
        
        <?php if (!empty($search_query)): ?>
            <!-- If a search was performed, show this title -->
            <h3 class="section-title"><i class="fa-solid fa-search"></i> Search Results for: "<?php echo htmlspecialchars($search_query); ?>"</h3>
        <?php else: ?>
            <!-- If the user landed on the page directly, show this title -->
            <h3 class="section-title"><i class="fa-solid fa-search"></i> Please enter a search term</h3>
        <?php endif; ?>

        <div class="movie-grid">
            <?php
            // Check if a search was performed and if there are results.
            if ($movies_result && $movies_result->num_rows > 0) {
                // Loop through each movie that matched the search.
                while($movie = $movies_result->fetch_assoc()) {
            ?>

            <!-- Movie Item START: This HTML is the same as your index.php -->
            <div class="movie-item">
                <a href="movie.php?id=<?php echo $movie['id']; ?>">
                    <div class="movie-poster">
                         <img src="uploads/<?php echo htmlspecialchars($movie['poster_filename']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    </div>
                    <div class="movie-info">
                        <h4 class="movie-title"><i class="fa-solid fa-film"></i> <?php echo htmlspecialchars($movie['title']); ?></h4>
                        <p class="movie-quality"><?php echo htmlspecialchars($movie['quality_string'] ?? 'HD Quality'); ?></p>
                        <div class="movie-meta-tags">
                            <?php if (!empty($movie['release_date'])): ?>
                                <span class="meta-tag tag-date"><i class="fa-solid fa-calendar-days"></i> <?php echo date("F j, Y", strtotime($movie['release_date'])); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($movie['primary_quality_tag'])): ?>
                                <span class="meta-tag tag-quality"><i class="fa-solid fa-video"></i> <?php echo htmlspecialchars($movie['primary_quality_tag']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Movie Item END -->

            <?php
                } // End of the while loop.
            } else if (!empty($search_query)) {
                // If a search was performed but no movies were found.
                echo "<p style='color: white; grid-column: 1 / -1; text-align:center;'>No movies found matching your search term.</p>";
            }
            ?>
        </div>
    </div>
</main>

<?php
// This includes the floating buttons and all necessary JavaScript.
include 'footer.php';
?>