<?php
require_once 'config/db.php';

// Check if a category slug is provided in the URL
if (!isset($_GET['slug'])) {
    die("No category specified.");
}
$slug = $_GET['slug'];

// Get the category details from the database using the slug
$stmt_cat = $conn->prepare("SELECT name FROM categories WHERE slug = ?");
$stmt_cat->bind_param("s", $slug);
$stmt_cat->execute();
$result_cat = $stmt_cat->get_result();
if ($result_cat->num_rows === 0) {
    die("Category not found.");
}
$category = $result_cat->fetch_assoc();
$category_name = $category['name'];

// Find all movies linked to this category ID
// This is a powerful query that joins three tables
$stmt_movies = $conn->prepare("
    SELECT m.* FROM movies m
    JOIN movie_categories mc ON m.id = mc.movie_id
    JOIN categories c ON mc.category_id = c.id
    WHERE c.slug = ?
    ORDER BY m.id DESC
");
$stmt_movies->bind_param("s", $slug);
$stmt_movies->execute();
$movies_result = $stmt_movies->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> Movies - HDHUB4U Clone</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>
<body>
    <?php // You can include your header here ?>

    <main id="content-wrapper" style="padding-top: 40px;">
        <div class="container">
            <!-- Dynamic Page Title -->
            <h3 class="section-title"><i class="fa-solid fa-film"></i> <?php echo htmlspecialchars($category_name); ?></h3>
            
            <div class="movie-grid">
                <?php if ($movies_result->num_rows > 0): ?>
                    <?php while($movie = $movies_result->fetch_assoc()): ?>
                    <div class="movie-item">
                        <a href="movie.php?id=<?php echo $movie['id']; ?>">
                            <div class="movie-poster">
                                <img src="uploads/<?php echo htmlspecialchars($movie['poster_filename']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                            </div>
                            <div class="movie-info">
                                <h4 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h4>
                                <p class="movie-quality">Multiple Qualities</p>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style='color: white; grid-column: 1 / -1; text-align:center;'>There are no movies in this category yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>