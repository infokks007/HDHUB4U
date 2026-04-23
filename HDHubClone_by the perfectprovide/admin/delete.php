<?php
require_once 'auth.php';

if (isset($_GET['id'])) {
    $movie_id = intval($_GET['id']);

    // 1. Get the poster filename to delete the file
    $stmt_file = $conn->prepare("SELECT poster_filename FROM movies WHERE id = ?");
    $stmt_file->bind_param("i", $movie_id);
    $stmt_file->execute();
    $result = $stmt_file->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['poster_filename']) && file_exists('../uploads/' . $row['poster_filename'])) {
            unlink('../uploads/' . $row['poster_filename']);
        }
    }

    // 2. Delete all links associated with the movie
    $stmt_links = $conn->prepare("DELETE FROM movie_links WHERE movie_id = ?");
    $stmt_links->bind_param("i", $movie_id);
    $stmt_links->execute();

    // 3. Delete the movie record itself
    $stmt_movie = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt_movie->bind_param("i", $movie_id);
    $stmt_movie->execute();
    
    header("Location: dashboard.php");
    exit;
}
?>