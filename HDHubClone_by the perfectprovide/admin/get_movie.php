<?php
require_once 'auth.php';
header('Content-Type: application/json');

$response = [
    'movie' => null,
    'links' => [],
    'categories' => []
];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get main movie data
    $stmt_movie = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt_movie->bind_param("i", $id);
    $stmt_movie->execute();
    $response['movie'] = $stmt_movie->get_result()->fetch_assoc();

    // Get associated links
    $stmt_links = $conn->prepare("SELECT * FROM movie_links WHERE movie_id = ?");
    $stmt_links->bind_param("i", $id);
    $stmt_links->execute();
    $result_links = $stmt_links->get_result();
    while ($row = $result_links->fetch_assoc()) {
        $response['links'][] = $row;
    }

    // Get associated category IDs
    $stmt_cats = $conn->prepare("SELECT category_id FROM movie_categories WHERE movie_id = ?");
    $stmt_cats->bind_param("i", $id);
    $stmt_cats->execute();
    $result_cats = $stmt_cats->get_result();
    while ($row = $result_cats->fetch_assoc()) {
        $response['categories'][] = $row['category_id'];
    }
}
echo json_encode($response);
?>