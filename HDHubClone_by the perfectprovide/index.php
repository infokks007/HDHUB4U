<?php
// This single line includes the database connection, doctype, <head> section,
// your logo, search bar, category buttons, and the homepage slider.
include 'header.php';
?>

<!-- Set the unique page title for your homepage -->
<title>HDHUB4U Clone - Watch & Download Latest Movies</title>

<!-- This is the main content area for the movie grid -->
<main id="content-wrapper">
    <div class="container">
        <!-- The section title -->
        <h3 class="section-title"><i class="fa-solid fa-film"></i> LATEST MOVIES</h3>
        
        <!-- The grid that will be filled with movies from the database -->
        <div class="movie-grid">
            <?php
            // Fetch all movies from the database, ordering by the newest first (highest ID).
            // We select '*' to get all columns, including the new ones for the tags.
            $movies_result = $conn->query("SELECT * FROM movies ORDER BY id DESC");

            // Check if the query was successful and if there are any movies to show.
            if ($movies_result && $movies_result->num_rows > 0) {
                // Loop through each movie row from the database result.
                while($movie = $movies_result->fetch_assoc()) {
            ?>

            <!-- Movie Item START: This HTML block is repeated for every movie in the loop -->
            <div class="movie-item">
                <a href="movie.php?id=<?php echo $movie['id']; ?>">
                    <div class="movie-poster">
                         <img src="uploads/<?php echo htmlspecialchars($movie['poster_filename']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    </div>
                    <div class="movie-info">
                        <h4 class="movie-title"><i class="fa-solid fa-film"></i> <?php echo htmlspecialchars($movie['title']); ?></h4>
                        
                        <?php // Display the quality string text if it exists ?>
                        <?php if (!empty($movie['quality_string'])): ?>
                            <p class="movie-quality"><?php echo htmlspecialchars($movie['quality_string']); ?></p>
                        <?php endif; ?>

                        <!-- Container for the red and purple tags -->
                        <div class="movie-meta-tags">
                            <?php // Display the red date tag if a release date exists ?>
                            <?php if (!empty($movie['release_date'])): ?>
                                <span class="meta-tag tag-date">
                                    <i class="fa-solid fa-calendar-days"></i> <?php echo date("F j, Y", strtotime($movie['release_date'])); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php // Display the purple quality tag if it exists ?>
                            <?php if (!empty($movie['primary_quality_tag'])): ?>
                                <span class="meta-tag tag-quality">
                                    <i class="fa-solid fa-video"></i> <?php echo htmlspecialchars($movie['primary_quality_tag']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Movie Item END -->

            <?php
                } // This ends the while loop.
            } else {
                // If there are no movies in the database, show this message.
                echo "<p style='color: white; grid-column: 1 / -1; text-align:center;'>No movies have been added yet.</p>";
            }
            ?>
        </div>
    </div>
</main>

<?php
// This single line includes the floating buttons ("Buy In 500", "Back to Top")
// and all the necessary JavaScript for the slider, menu, and search bar.
include 'footer.php';
?>