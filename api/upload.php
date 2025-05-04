<?php
session_start(); // Start the session at the top

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // File validation (size and type checks)
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

    // Check file size (5MB max)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        $_SESSION['error_message'] = "Sorry, your file is too large.";
        $uploadOk = 0;
        header("Location: uploadselection.php");
        exit();
    }

    // Allowed file types (CSV, XLS, XLSX, and JSON)
    $allowedTypes = ["csv", "xls", "xlsx", "json"];
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error_message'] = "Sorry, only CSV, Excel (XLS, XLSX), and JSON files are allowed.";
        $uploadOk = 0;
        header("Location: uploadselection.php");
        exit();
    }

    // If everything is ok, try to upload the file to Dropbox
    if ($uploadOk == 1) {
        // Dropbox access token and path setup
        $accessToken = getenv('DROPBOX_ACCESS_TOKEN');  // Get Dropbox access token
        $dropboxPath = "/mangdx/" . basename($_FILES["fileToUpload"]["name"]);  // Path in Dropbox

        // Function to upload to Dropbox
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

        // Perform the Dropbox upload
        $uploadResponse = uploadToDropbox($_FILES["fileToUpload"]["tmp_name"], $dropboxPath, $accessToken);

        if (isset($uploadResponse['path_display'])) {
            $_SESSION['success_message'] = "The file has been uploaded to Dropbox: " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));
            $_SESSION['uploaded_file'] = $uploadResponse['path_display'];  // Save Dropbox file path
            header("Location: upload_fill.php");  // Redirect to the next page (upload fill)
            exit();
        } else {
            $_SESSION['error_message'] = "Sorry, there was an error uploading your file to Dropbox.";
            header("Location: uploadselection.php");
            exit();
        }
    } else {
        // If validation fails, redirect back to the upload page
        header("Location: uploadselection.php");
        exit();
    }
}
?>
