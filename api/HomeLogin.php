<?php
session_start();
include 'db_connection.php'; // Include your database connection file
// Query to count the number of datasets in the database
$stmt = $pdo->query("SELECT COUNT(*) AS dataset_count FROM datasets");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$dataset_count = $row['dataset_count'];

// Query to count the number of unique users (distinct user_id) in the datasets table
$stmt_sources = $pdo->query("SELECT COUNT(DISTINCT user_id) AS unique_sources FROM datasets");
$row_sources = $stmt_sources->fetch(PDO::FETCH_ASSOC);
$sources_count = $row_sources['unique_sources'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDX</title>
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        text-align: center;
    }
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 5%; /* Adjusted padding for a more compact navbar */
        padding-left: 30px;
        background-color: #0099ff; /* Transparent background */
        color: #cfd9ff;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        margin: 10px 0;
        backdrop-filter: blur(10px);
        max-width: 1200px; /* Limit the maximum width */
        width: 100%; /* Ensure it takes up the full width but doesn't exceed 1200px */
        margin-top:30px;
        margin-left: auto; /* Center align the navbar */
        margin-right: auto; /* Center align the navbar */
        font-weight: bold;
    }

    .logo {
        display: flex;
        align-items: center;
    }
    .logo img {
        height: auto;
        width: 80px; /* Adjust logo size */
        max-width: 100%;
    }
    .search-bar {
        flex-grow: 1;
        display: flex;
        align-items: center;
        position: relative;
        margin-left: -190px; /* Adjust space for a smaller navbar */
    }
    .search-bar input {
        padding: 8px;
        width: 400px;
        border-radius: 5px;
        border: none;
    }
    .search-bar button {
        background: none;
        border: none;
        cursor: pointer;
    }
    .search-bar img {
        width: 20px;
        height: 20px;
        margin-left: -50px;
    }
    .search-dropdown {
        position: absolute;
        top: 45px;
        left: 0;
        width: 100%;
        max-width: 300px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: none;
        z-index: 10;
    }
    .search-dropdown ul li, .trending-title {
        padding: 10px;
        cursor: pointer;
        transition: background 0.3s;
        color: black;
    }
    .search-dropdown .trending-title {
        font-weight: bold;
        padding: 8px 10px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }
    .search-dropdown ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .search-dropdown ul li {
        padding: 10px;
        cursor: pointer;
        transition: background 0.3s;
        text-align: left;
    }
    .search-dropdown ul li:hover {
        background: #cfd9ff;
    }
    .nav-links a {
        color: white;
        margin-left: 20px;
        text-decoration: none;
        font-size: 18px;
        transition: transform 0.3s ease; /* Smooth transition for scaling */
    }

    .nav-links a:hover {
        transform: scale(1.2); /* Scale up the link by 20% */
    }

    .wrapper {
        padding: 50px 5%;
        margin-top: 50px;
        position: relative;
        z-index: 1; 
    }
    #background-video {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1; /* stays behind everything */
    }
    h1 {
        font-size: 90px;
        font-weight: 600;
        margin-bottom: 20px;
        color: rgba(0, 153, 255, 0.8);
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease; /* Smooth transition for all properties */
    }

    h1:hover {
        color: rgba(0, 172, 255, 1); /* Change color on hover */
        font-size: 95px; /* Slightly increase font size */
        text-shadow: 2px 2px 5px rgba(233, 230, 230, 0.3); /* Enhance shadow */
    }

    #tagline {
        color: rgba(0, 153, 255, 0.8);
        text-align: center;
        font-size: 1.2rem;
        margin-top: 10px;
        text-shadow: 1px 1px 5px rgba(255, 253, 253, 0.67);
        transition: all 0.3s ease; /* Smooth transition for hover effects */
    }

    #tagline:hover {
        color: rgba(0, 172, 255, 1); /* Change color on hover (brighter blue) */
        font-size: 1.3rem; /* Slightly increase font size */
        text-shadow: 2px 2px 8px rgb(255, 253, 253); /* Enhance shadow */
    }

    .stats-box {
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 20px 0px 0px 0px;
        width: 30%;
        margin: 0 auto;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-size: 30px;
        font-weight: bold;
        color: #ffffff;
    }
    .stat {
        flex: 1;
        text-align: center;
        color:rgba(28, 132, 227, 0.8);
    }
    .divider {
        width: 3px;
        background-color: black;
        height: 90px;
        margin-top: -20px
    }
    .upload-section {
        position: fixed;
        bottom: 20px;
        right: 20px;
        color:rgba(0, 153, 255, 0.8);
    }
    .upload-btn {
        display: inline-block;
        padding: 20px;
        background-color:rgba(0, 153, 255, 0.8);
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
        transition: all 0.3s ease;
    }

    .upload-btn i {
        font-size: 40px; /* Larger icon size */
        color: #ffffff; /* White color for the icon */
    }

    .upload-btn:hover {
        background-color: #a0b6f3; /* Darker blue when hovered */
        transform: scale(1.1); /* Slightly increase size on hover */
    }
    }
    .upload-btn p {
        font-size: 16px;
        color: black;
    }
    .profile-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: white; 
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 70px;
    }
    .profile-icon img {
        width: 150%;
        height: auto;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
    }
    .profile-icon img:hover {
        transform: scale(1.2); /* Slightly enlarge the image on hover */
    }
    @media (max-width: 768px) {
        .stats-box {
            flex-direction: column;
            width: 80%;
            font-size: 24px;
        }
        .divider {
            width: 100%;
            height: 2px;
            margin: 20px 0;
        }
        h1 {
            font-size: 40px;
        }
    }
    .nav-links {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    /* Cursor Trail Styles */
    .cursor-trail {
        position: absolute;
        width: 10px; /* Size of the trail dot */
        height: 10px; /* Size of the trail dot */
        border-radius: 50%; /* Round shape */
        pointer-events: none; /* So it doesn't interfere with other elements */
        animation: trail-animation 0.5s forwards; /* Smooth fade effect */
    }

    .cursor-trail.blue {
        background-color: rgba(0, 153, 255, 0.8); /* Blue color for the trail */
    }

    .cursor-trail.white {
        background-color: rgba(255, 255, 255, 0.8); /* White color for the trail */
    }

    @keyframes trail-animation {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        100% {
            transform: scale(0);
            opacity: 0;
        }
    }
            
    </style>
</head>
<body>
    <video autoplay muted loop id="background-video">
        <source src="https://www.dropbox.com/scl/fi/3d897nz65telo6yvpn4sh/background4.mp4?rlkey=9t0ayksrok8b9cj4a3am68qzc&st=4uicfr3j&raw=1" type="video/mp4">
    </video>

    <div id="wrapper">
        
        <header class="navbar">
            <div class="logo">
                <img src="https://www.dropbox.com/scl/fi/oo9zuxjgx2dzws72biodk/mdx_logo.png?rlkey=37lcn6yg9aoqjynlelncfsl6e&st=1rxlcyms&raw=1" alt="Mangasay Data Exchange Logo">
            </div>
            <form id="searchForm" action="search_results.php" method="GET">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search datasets" onfocus="showDropdown()" onblur="hideDropdown()">
                <button>
                    <img src="https://www.dropbox.com/scl/fi/inemp7yqoz90spu069qwe/search_icon.png?rlkey=e3vgdi11rrhsctviypkig4bou&st=6tpoojzq&raw=1" alt="Search">
                </button>
                
            </div>
            </form>
            <nav class="nav-links">
                <a href="HomeLogin.php">HOME</a>
                <a href="datasets.php">DATASETS</a>
                <a onclick="showModal()" style="cursor: pointer;">CATEGORY</a>
                <div class="profile-icon">
                <img src="https://www.dropbox.com/scl/fi/qluw1qll9bauz91379zhl/avatarIconunknown.jpg?rlkey=jhrxtz3pl41wsfayiwce6gqtl&st=72avrw3j&raw=1" alt="Profile">
            </div>
            </nav>
        </header>
    
    <script>
        function showModal() {
            document.getElementById("categoryModal").style.display = "flex";
        }
        function hideModal() {
            document.getElementById("categoryModal").style.display = "none";
        }
        document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("categoryModal").style.display = "none";
    });
    </script>
        <main class="wrapper">
            <h1>Mangasay <br> Data Exchange </h1>
            <p id="tagline">Discover, Share, and Transform Data Seamlessly.</p>
            <div class="stats-box">
                <div class="stat">
                <span class="stat-number"><?= number_format($dataset_count) ?></span>
                    <p>Datasets</p>
                </div>
                <div class="divider"></div>
                <div class="stat">
                <span class="stat-number"><?= number_format($sources_count) ?></span> <!-- Dynamic Sources Count -->
                <p>Sources</p>
                </div>
            </div>

        </main>

        <div class="upload-section">
            <a href="uploadselection.php" class="upload-btn">
            <i class="fa-solid fa-upload"></i>
            </a>
            <p>Upload Data</p>
        </div>
    </div>
    <script>
        function showDropdown() {
            document.getElementById("searchDropdown").style.display = "block";
        }
        function hideDropdown() {
            setTimeout(() => {
                document.getElementById("searchDropdown").style.display = "none";
            }, 200);
        }
    </script>
    <script src="https://kit.fontawesome.com/2c68a433da.js" crossorigin="anonymous">  
    </script>
    <?php include 'sidebar.php'; ?>
    <?php include 'category_modal.php'; // Include the modal?>
    <script>
        document.addEventListener("mousemove", function (e) {
            // Create a new element for the trail dot
            const trailDot = document.createElement("div");
            trailDot.classList.add("cursor-trail");

            // Check if the mouse is over the header
            if (e.target.closest("header")) {
                trailDot.classList.add("white"); // White trail if over header
            } else {
                trailDot.classList.add("blue"); // Blue trail otherwise
            }

            // Set its position to the cursor's position
            trailDot.style.left = `${e.pageX - 5}px`; // Position adjusted to center the trail
            trailDot.style.top = `${e.pageY - 5}px`; // Position adjusted to center the trail

            // Append the trail dot to the body
            document.body.appendChild(trailDot);

            // Remove the trail dot after animation ends
            setTimeout(() => {
                trailDot.remove();
            }, 500); // Matches the animation duration
        });
    </script>
    <script src="search.js"></script>


</body>
</html>
