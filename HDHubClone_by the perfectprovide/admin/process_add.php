<?php
// Step 1: Ensure the user is a logged-in admin.
// This is a critical security measure.
require_once 'auth.php';

// Step 2: Only execute if the form was submitted using the POST method.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // --- Part A: Handle the Poster File Upload ---

    $poster_filename = null; // Initialize as null. If no file is uploaded, this will be saved in the database.

    // Check if a file was uploaded ('poster_file') and if there were no upload errors.
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] == 0) {
        
        $upload_dir = '../uploads/'; // The folder where posters are stored (outside the admin folder).
        
        // Create a unique filename to prevent overwriting existing files.
        // It combines a prefix 'poster_', a unique ID based on the time, and the original file extension.
        $file_extension = pathinfo($_FILES['poster_file']['name'], PATHINFO_EXTENSION);
        $poster_filename = 'poster_' . uniqid('', true) . '.' . $file_extension;
        
        // Move the uploaded file from the temporary server location to your permanent 'uploads' folder.
        move_uploaded_file($_FILES['poster_file']['tmp_name'], $upload_dir . $poster_filename);
    }

    // --- Part B: Save the Main Movie Details into the 'movies' table ---

    // Prepare the SQL INSERT statement. Using prepared statements prevents SQL injection attacks.
    $stmt = $conn->prepare("
        INSERT INTO movies (title, description, poster_filename, release_date, director, movie_cast, genre) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // Bind the data from the form to the SQL statement's placeholders (the question marks).
    $stmt->bind_param("sssssss", 
        $_POST['title'], 
        $_POST['description'], 
        $poster_filename, 
        $_POST['release_date'], 
        $_POST['director'], 
        $_POST['movie_cast'], 
        $_POST['genre']
    );

    // Execute the prepared statement to insert the movie data.
    $stmt->execute();

    // IMPORTANT: Get the ID of the movie we just created.
    // We need this ID to link the categories and download links to it.
    $movie_id = $conn->insert_id;

    // --- Part C: Link the Selected Categories in the 'movie_categories' table ---

    // Check if the 'category_ids' array was submitted from the multi-select box.
    if (!empty($_POST['category_ids'])) {
        
        // Prepare a new statement to insert into our linking table.
        $stmt_link = $conn->prepare("INSERT INTO movie_categories (movie_id, category_id) VALUES (?, ?)");
        
        // Loop through each category ID that was selected in the form.
        foreach ($_POST['category_ids'] as $category_id) {
            // For each category, bind the new movie's ID and the category's ID.
            $stmt_link->bind_param("ii", $movie_id, $category_id);
            // Execute the statement to create the link.
            $stmt_link->execute();
        }
    }
    
    // --- Part D: Save the Dynamic Watch & Download Links (Optional Bonus) ---
    // This part handles the multiple quality links if you have them in your form.
    if (isset($_POST['links'])) {
        $links = $_POST['links'];
        $stmt_links = $conn->prepare("INSERT INTO movie_links (movie_id, quality, watch_link, download_link) VALUES (?, ?, ?, ?)");
        
        // Loop through each link group submitted from the form.
        for ($i = 0; $i < count($links['quality']); $i++) {
            // Only save if the 'quality' field for that link group is not empty.
            if (!empty($links['quality'][$i])) {
                $stmt_links->bind_param("isss", 
                    $movie_id, 
                    $links['quality'][$i],
                    $links['watch_link'][$i],
                    $links['download_link'][$i]
                );
                $stmt_links->execute();
            }
        }
    }


    // Step 3: Redirect the admin back to the dashboard after everything is saved.
    // The user will see the newly added movie in the list.
    header("Location: dashboard.php");
    exit; // Always call exit() after a header redirect.
}
?>