<?php
// Step 1: Connect to DB and get the Movie ID from the URL
require_once 'config/db.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No movie specified.");
}
$id = intval($_GET['id']);

// Step 2: Find the first available "Watch Link" for this movie and get the title
$stmt = $conn->prepare("
    SELECT m.title, l.watch_link 
    FROM movies m
    JOIN movie_links l ON m.id = l.movie_id
    WHERE m.id = ? AND l.watch_link IS NOT NULL AND l.watch_link != ''
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("No watchable link found for this movie.");
}
$data = $result->fetch_assoc();
$movie_title = $data['title'];
$watch_link = $data['watch_link'];

// Step 3: Include the standard site header
include 'header.php';
?>

<!-- Set the unique title for this watch page -->
<title>Watching: <?php echo htmlspecialchars($movie_title); ?></title>
<style>
    /* CSS for the watch page */
    .watch-page-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
    }
    .watch-page-container h1 {
        font-size: 1.8em;
        margin-bottom: 20px;
        color: #fff;
    }
    .video-player-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        height: 0;
        background-color: #000;
        border-radius: 8px;
        overflow: hidden;
    }
    .video-player-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>

<main class="watch-page-container">
    <h1><i class="fa-solid fa-play-circle"></i> Now Playing: <?php echo htmlspecialchars($movie_title); ?></h1>
    
    <!-- Video Player Section -->
    <div class="video-player-wrapper">
        <iframe src="<?php echo htmlspecialchars($watch_link); ?>" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
        </iframe>
    </div>
</main>

<?php
// Include the standard site footer
include 'footer.php';
?>