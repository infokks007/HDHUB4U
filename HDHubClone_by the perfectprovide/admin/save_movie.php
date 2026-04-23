<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $poster_url = $_POST['poster_url'];
    $release_date = $_POST['release_date'];
    
    // Check if we are updating or inserting
    if (isset($_POST['movie_id']) && !empty($_POST['movie_id'])) {
        // UPDATE LOGIC
        $movie_id = (int)$_POST['movie_id'];
        
        $sql = "UPDATE movies SET title=?, description=?, poster_url=?, release_date=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $description, $poster_url, $release_date, $movie_id);
        $stmt->execute();
        
        // Delete old links and screenshots to replace them with new ones
        $conn->query("DELETE FROM download_links WHERE movie_id = $movie_id");
        $conn->query("DELETE FROM screenshots WHERE movie_id = $movie_id");
        
        $last_movie_id = $movie_id; // Use existing ID

    } else {
        // INSERT LOGIC
        $sql = "INSERT INTO movies (title, description, poster_url, release_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $description, $poster_url, $release_date);
        $stmt->execute();
        $last_movie_id = $conn->insert_id;
    }
    
    // Save Links
    $link_texts = $_POST['link_text'];
    $link_urls = $_POST['link_url'];
    $sql_link = "INSERT INTO download_links (movie_id, link_text, link_url) VALUES (?, ?, ?)";
    $stmt_link = $conn->prepare($sql_link);
    for ($i = 0; $i < count($link_texts); $i++) {
        if (!empty($link_texts[$i]) && !empty($link_urls[$i])) {
            $stmt_link->bind_param("iss", $last_movie_id, $link_texts[$i], $link_urls[$i]);
            $stmt_link->execute();
        }
    }

    // Save Screenshots
    $screenshot_urls = $_POST['screenshot_url'];
    $sql_ss = "INSERT INTO screenshots (movie_id, image_url) VALUES (?, ?)";
    $stmt_ss = $conn->prepare($sql_ss);
    foreach ($screenshot_urls as $ss_url) {
        if (!empty($ss_url)) {
            $stmt_ss->bind_param("is", $last_movie_id, $ss_url);
            $stmt_ss->execute();
        }
    }

    $conn->close();
    // Redirect back to admin dashboard
    header("Location: index.php");
    exit();
}
?>