<?php
include '../db_connect.php';
$movie_id = (int)$_GET['id'];
if ($movie_id <= 0) { die('Invalid Movie ID'); }

// Fetch existing data
$movie = $conn->query("SELECT * FROM movies WHERE id = $movie_id")->fetch_assoc();
$links = $conn->query("SELECT * FROM download_links WHERE movie_id = $movie_id");
$screenshots = $conn->query("SELECT * FROM screenshots WHERE movie_id = $movie_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Movie</title>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
<div class="admin-container">
    <h2>Edit Movie: <?php echo htmlspecialchars($movie['title']); ?></h2>
    <form action="save_movie.php" method="post" class="admin-form">
        <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
        <h4>Movie Details</h4>
        Title: <br> <input type="text" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required><br>
        Description: <br> <textarea name="description" required rows="6"><?php echo htmlspecialchars($movie['description']); ?></textarea><br>
        Poster Image URL: <br> <input type="text" name="poster_url" value="<?php echo htmlspecialchars($movie['poster_url']); ?>" required><br>
        Release Date: <br> <input type="date" name="release_date" value="<?php echo $movie['release_date']; ?>"><br>
        <hr>
        <h4>Download Links</h4>
        <div id="download-links-container">
            <?php while($link = $links->fetch_assoc()): ?>
            <div class="link-group">
                Link Text: <input type="text" name="link_text[]" value="<?php echo htmlspecialchars($link['link_text']); ?>" required>
                Link URL: <input type="text" name="link_url[]" value="<?php echo htmlspecialchars($link['link_url']); ?>" required>
            </div>
            <?php endwhile; ?>
        </div>
        <button type="button" onclick="addDownloadLink()">+ Add More Link</button>
        <hr>
        <h4>Screenshots</h4>
        <div id="screenshots-container">
            <?php while($ss = $screenshots->fetch_assoc()): ?>
            <div class="screenshot-group">
                Screenshot URL: <input type="text" name="screenshot_url[]" value="<?php echo htmlspecialchars($ss['image_url']); ?>" required>
            </div>
            <?php endwhile; ?>
        </div>
        <button type="button" onclick="addScreenshot()">+ Add More Screenshot</button>
        <br><br><br>
        <input type="submit" value="Update Movie">
    </form>
</div>
<script>
// Same JS functions as add_movie.php
function addDownloadLink() {
    document.getElementById('download-links-container').insertAdjacentHTML('beforeend', `<div class="link-group">Link Text: <input type="text" name="link_text[]" required> Link URL: <input type="text" name="link_url[]" required></div>`);
}
function addScreenshot() {
    document.getElementById('screenshots-container').insertAdjacentHTML('beforeend', `<div class="screenshot-group">Screenshot URL: <input type="text" name="screenshot_url[]" required></div>`);
}
</script>
</body>
</html>
<?php $conn->close(); ?>