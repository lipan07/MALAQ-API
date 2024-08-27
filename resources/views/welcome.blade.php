<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Social Media Template</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Header */
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header .nav-items {
            display: flex;
            align-items: center;
        }

        .header .nav-items a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
        }

        .header .nav-items a:hover {
            text-decoration: underline;
        }

        /* Container */
        .container {
            display: flex;
            margin-top: 20px;
            padding: 0 20px;
        }

        /* Sidebar */
        .sidebar {
            width: 25%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-right: 20px;
        }

        .sidebar img {
            width: 100px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 15px;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 18px;
        }

        .sidebar p {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .sidebar .sidebar-links {
            margin-top: 30px;
        }

        .sidebar .sidebar-links a {
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            color: #007bff;
        }

        .sidebar .sidebar-links a:hover {
            background-color: #f9f9f9;
        }

        /* Main Content */
        .main-content {
            width: 75%;
        }

        .post {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .post .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .post .post-header img {
            width: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .post .post-header h3 {
            margin: 0;
            font-size: 16px;
        }

        .post .post-content {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .post .post-image img {
            width: 100%;
            border-radius: 8px;
        }

        .post .post-actions {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        .post .post-actions a {
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }

        /* Responsive Design */
        @media(max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar, .main-content {
                width: 100%;
                margin: 0;
            }

            .sidebar {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="logo">My Social App</div>
        <div class="nav-items">
            <a href="#"><i class="fas fa-home"></i> Home</a>
            <a href="#"><i class="fas fa-bell"></i> Notifications</a>
            <a href="#"><i class="fas fa-user-circle"></i> Profile</a>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">

        <!-- Sidebar -->
        <aside class="sidebar">
            <img src="https://e7.pngegg.com/pngimages/550/997/png-clipart-user-icon-foreigners-avatar-child-face-thumbnail.png" alt="User Profile Picture">
            <h2>John Doe</h2>
            <p>Designer, London</p>
            <div class="sidebar-links">
                <a href="#"><i class="fas fa-user-friends"></i> Friends</a>
                <a href="#"><i class="fas fa-images"></i> Photos</a>
                <a href="#"><i class="fas fa-cogs"></i> Settings</a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Post 1 -->
            <div class="post">
                <div class="post-header">
                    <img src="https://e7.pngegg.com/pngimages/550/997/png-clipart-user-icon-foreigners-avatar-child-face-thumbnail.png" alt="User Profile Picture">
                    <h3>Jane Doe</h3>
                </div>
                <div class="post-content">
                    Just completed my tour of South America. What an amazing experience!
                </div>
                <div class="post-image">
                    <img src="https://media.architecturaldigest.com/photos/63079fc7b4858efb76814bd2/16:9/w_1920%2Cc_limit/9.%2520DeLorean-Alpha-5%2520%255BDeLorean%255D.jpg" alt="Post Image">
                </div>
                <div class="post-actions">
                    <a href="#"><i class="fas fa-thumbs-up"></i> Like</a>
                    <a href="#"><i class="fas fa-comment"></i> Comment</a>
                </div>
            </div>

            <!-- Post 2 -->
            <div class="post">
                <div class="post-header">
                    <img src="https://e7.pngegg.com/pngimages/550/997/png-clipart-user-icon-foreigners-avatar-child-face-thumbnail.png" alt="User Profile Picture">
                    <h3>John Doe</h3>
                </div>
                <div class="post-content">
                    Check out this awesome view I captured!
                </div>
                <div class="post-image">
                    <img src="https://hips.hearstapps.com/pop.h-cdn.co/assets/cm/15/05/54cae423e713b_-_american-muscle-facts-06-0312-xln.jpg?crop=1xw:0.991304347826087xh;center,top&resize=980:*" alt="Post Image">
                </div>
                <div class="post-actions">
                    <a href="#"><i class="fas fa-thumbs-up"></i> Like</a>
                    <a href="#"><i class="fas fa-comment"></i> Comment</a>
                </div>
            </div>

        </div>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 My Social App. All Rights Reserved.</p>
    </footer>

</body>
</html>
