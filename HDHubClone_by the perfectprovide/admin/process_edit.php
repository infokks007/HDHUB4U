<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $movie_id = intval($_POST['id']);

    // Handle poster upload if a new one is provided
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] == 0) {
        // ... (code to delete old poster and upload new one) ...
    }

    // Update main movie details
    $stmt_update = $conn->prepare("UPDATE movies SET title = ?, description = ?, release_date = ?, director = ?, movie_cast = ?, genre = ? WHERE id = ?");
    $stmt_update->bind_param("ssssssi", $_POST['title'], $_POST['description'], $_POST['release_date'], $_POST['director'], $_POST['movie_cast'], $_POST['genre'], $movie_id);
    $stmt_update->execute();

    // Update categories (Delete all, then re-insert)
    $conn->query("DELETE FROM movie_categories WHERE movie_id = " . $movie_id);
    if (!empty($_POST['category_ids'])) {
        $stmt_add_cats = $conn->prepare("INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)");
        foreach ($_POST['category_ids'] as $category_id) {
            $stmt_add_cats->bind_param("ii", $movie_id, $category_id);
            $stmt_add_cats->execute();
        }
    }

    // Update links (Delete all, then re-insert)
    $conn->query("DELETE FROM movie_links WHERE movie_id = " . $movie_id);
    if (isset($_POST['links'])) {
        $links = $_POST['links'];
        $stmt_add_links = $conn->prepare("INSERT INTO movie_links (movie_id, quality, watch_link, download_link) VALUES (?, ?, ?, ?)");
        for ($i = 0; $i < count($links['quality']); $i++) {
            if (!empty($links['quality'][$i])) {
                $stmt_add_links->bind_param("isss", $movie_id, $links['quality'][$i], $links['watch_link'][$i], $links['download_link'][$i]);
                $stmt_add_links->execute();
            }
        }
    }
    
    header("Location: dashboard.php");
    exit;
}
?>