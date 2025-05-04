<?php
session_start();
include 'db_connection.php';  // Include your database connection

function uploadToDropbox($localFilePath, $dropboxPath, $accessToken) {
    $fp = fopen($localFilePath, 'rb');
    $size = filesize($localFilePath);

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/octet-stream",
        "Dropbox-API-Arg: " . json_encode([
            "path" => $dropboxPath,
            "mode" => "add",
            "autorename" => true,
            "mute" => false
        ])
    ];

    $ch = curl_init("https://content.dropboxapi.com/2/files/upload");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_INFILE, $fp);
    curl_setopt($ch, CURLOPT_INFILESIZE, $size);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    fclose($fp);

    if ($response === false) {
        return ['error' => $error];
    }

    return json_decode($response, true);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the form and check if it's set
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $visibility = isset($_POST['visibility']) ? $_POST['visibility'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $start_period = isset($_POST['start_period']) ? $_POST['start_period'] : '';
    $end_period = isset($_POST['end_period']) ? $_POST['end_period'] : '';
    $source = isset($_POST['source']) ? $_POST['source'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $link = isset($_POST['link']) ? $_POST['link'] : '';
    
    $file = isset($_FILES['fileToUpload']) ? $_FILES['fileToUpload'] : null;
    $uploaded_file_name = isset($_POST['uploaded_file']) ? $_POST['uploaded_file'] : '';
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to upload a dataset.";
        exit;
    }
    $user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

    // Validate the data
    if (empty($title) || empty($description) || empty($visibility) || empty($category) || empty($start_period) || empty($end_period) || empty($source) || empty($location) || empty($link)) {
        echo "All fields are required!";
    } else {
        // Handle file upload if a file was uploaded
        if ($file) {
            $target_dir = "uploads/";
            $target_file = $target_dir . $uploaded_file_name;
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Define allowed file types
            $allowedTypes = ["csv", "xls", "xlsx", "json"];

            // Check if file is too large
            if ($file["size"] > 5000000) {  // Limit file size to 5MB
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow only certain file formats (CSV, XLS, XLSX, JSON)
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error_message'] = "Sorry, only CSV, Excel (XLS, XLSX), and JSON files are allowed.";
                $uploadOk = 0;
            }
            // Ensure they are in the correct format (YYYY-MM)
            if (preg_match("/^\d{4}-\d{2}$/", $start_period) && preg_match("/^\d{4}-\d{2}$/", $end_period)) {
                // If the format is valid, store them as is
                $start_date = $start_period; // Store as YYYY-MM
                $end_date = $end_period;     // Store as YYYY-MM
            } else {
                echo "Invalid date format. Please use the format YYYY-MM.";
                exit;
            }
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo "File upload error.";
                exit;
            }
            

            // Try to upload the file if no errors
            if ($uploadOk == 1) {
                $accessToken = getenv('DROPBOX_ACCESS_TOKEN');
                $dropboxPath = "/mangdx/" . basename($file["name"]);
                
                $response = uploadToDropbox($file["tmp_name"], $dropboxPath, $accessToken);
                
                if (isset($response['path_display'])) {
                    $_SESSION['uploaded_file'] = $response['path_display'];
                    $target_file = $response['path_display']; // save in DB later
                    echo "Dropbox upload success: " . $response['path_display'];
                } else {
                    echo "Dropbox upload failed: " . ($response['error_summary'] ?? json_encode($response));
                    exit;
                }
            }
        }

        // Prepare and execute the SQL statement using PDO for PostgreSQL
        $sql = "INSERT INTO datasets (title, description, visibility, category, start_period, end_period, source, location, link, file_path, user_id)
                VALUES (:title, :description, :visibility, :category, :start_period, :end_period, :source, :location, :link, :file_path, :user_id)";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':visibility', $visibility);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':start_period', $start_period);
        $stmt->bindParam(':end_period', $end_period);
        $stmt->bindParam(':source', $source);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':file_path', $target_file);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            // Redirect to a success page or a confirmation message
            header("Location: dataset_success.php");
            exit();
        } else {
            echo "Error: Could not execute the query.";
        }
    }
}
?>
