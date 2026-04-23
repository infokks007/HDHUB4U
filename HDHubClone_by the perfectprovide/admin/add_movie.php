<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Movie</title>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
<div class="admin-container">
    <h2>Add New Movie</h2>
    <form action="save_movie.php" method="post" class="admin-form">
        <h4>Movie Details</h4>
        Title: <br> <input type="text" name="title" required><br>
        Description: <br> <textarea name="description" required rows="6"></textarea><br>
        Poster Image URL: <br> <input type="text" name="poster_url" required><br>
        Release Date: <br> <input type="date" name="release_date"><br>
        <hr>
        <h4>Download Links</h4>
        <div id="download-links-container">
            <div class="link-group">
                Link Text (e.g., 480p): <input type="text" name="link_text[]" required>
                Link URL: <input type="text" name="link_url[]" required>
            </div>
        </div>
        <button type="button" onclick="addDownloadLink()">+ Add More Link</button>
        <hr>
        <h4>Screenshots</h4>
        <div id="screenshots-container">
            <div class="screenshot-group">
                Screenshot URL: <input type="text" name="screenshot_url[]" required>
            </div>
        </div>
        <button type="button" onclick="addScreenshot()">+ Add More Screenshot</button>
        <br><br><br>
        <input type="submit" value="Save Movie">
    </form>
</div>
<script>
function addDownloadLink() {
    document.getElementById('download-links-container').insertAdjacentHTML('beforeend', `<div class="link-group">Link Text: <input type="text" name="link_text[]" required> Link URL: <input type="text" name="link_url[]" required></div>`);
}
function addScreenshot() {
    document.getElementById('screenshots-container').insertAdjacentHTML('beforeend', `<div class="screenshot-group">Screenshot URL: <input type="text" name="screenshot_url[]" required></div>`);
}
</script>
</body>
</html>