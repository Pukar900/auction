<?php
include '../db_connection.php';
session_start();

// Flash message for successful login
$loginSuccess = '';
if (isset($_SESSION['login_success'])) {
    $loginSuccess = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

// Get today's date in 'Y-m-d' format for validation
$today = date('Y-m-d');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $starting_price = $_POST['starting_price'];
    $auction_start_date = $_POST['auction_start_date'];
    $auction_end_date = $_POST['auction_end_date'];
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']); // Get image data
    } else {
        $imageData = null; // If no image uploaded, set as NULL
    }

    // Validate form inputs
    if (empty($title) || empty($starting_price) || empty($auction_start_date) || empty($auction_end_date)) {
        $error = "All fields are required!";
    } elseif ($auction_start_date < $today || $auction_end_date < $today) {
        $error = "Auction dates cannot be in the past.";
    } elseif ($auction_end_date < $auction_start_date) {
        $error = "End date cannot be earlier than start date.";
    } else {
        // Generate lot number based on today's date and the number of auctions created today
        // Query to get the number of auctions created today
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM auctions WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Create lot number as 'LOT-YYYYMMDD-001'
        $nextId = $row['total'] + 1;
        $lotNumber = "LOT-" . $today . "-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Prepare SQL statement with placeholders
        $sql = "INSERT INTO auctions (title, description, starting_price, auction_start_date, auction_end_date, user_id, image, lot_number)
                VALUES (:title, :description, :starting_price, :auction_start_date, :auction_end_date, :user_id, :image, :lot_number)";

        try {
            // Prepare the statement
            $stmt = $pdo->prepare($sql);

            // Bind values to placeholders
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':starting_price', $starting_price);
            $stmt->bindParam(':auction_start_date', $auction_start_date);
            $stmt->bindParam(':auction_end_date', $auction_end_date);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
            $stmt->bindParam(':lot_number', $lotNumber);

            // Execute the statement
            $stmt->execute();

            $_SESSION['login_success'] = "Auction item added successfully!";
            header("Location: ../index.php"); // Redirect to home page
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Antique Art Auction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Modal overlay */
        .modal {
            display: block;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Modal content */
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .modal-content h3 {
            margin-bottom: 15px;
            color: #4a4e69;
        }

        .modal-content select {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
        }

        .modal-content button {
            background-color: #4a4e69;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }


        header {
            background-color: #4a4e69;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: -75px;
            flex-wrap: wrap;
        }

        .logo img {
            height: 60px;
            border-radius: 10px;
        }

        .search-container {
            display: flex;
            align-items: center;
        }

        .search-container form {
            display: flex;
            align-items: center;
        }

        .search-container input[type="text"] {
            padding: 8px 12px;
            border-radius: 5px 0 0 5px;
            border: none;
            outline: none;
            font-size: 1rem;
            width: 200px;
        }

        .search-container button {
            padding: 8px 12px;
            border: none;
            background-color: #9a8c98;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }

        .search-container button:hover {
            background-color: #c9ada7;
        }


        nav {
            background-color: #22223b;
            text-align: center;
            padding: 10px 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .auction-form-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }

        .auction-form-container h2 {
            text-align: center;
            color: #4a4e69;
            margin-bottom: 20px;
        }

        .auction-form-container label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .auction-form-container input[type="text"],
        .auction-form-container input[type="number"],
        .auction-form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        .auction-form-container textarea {
            resize: vertical;
            min-height: 100px;
        }

        .auction-form-container input[type="file"] {
            margin-top: 10px;
        }

        .auction-form-container button {
            display: block;
            width: 100%;
            background-color: rgb(56, 61, 94);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .auction-form-container button:hover {
            background-color: rgb(39, 40, 99);
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }

        footer {
            background-color: #4a4e69;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <header>
        <div class="header-top">
            <div class="logo">
                <img src="../images/logo.jpg" alt="Logo">
            </div>
            <div class="search-container">
                <form action="search.php" method="GET">
                    <input type="text" name="query" placeholder="Search artworks..." required>
                    <button type="submit">🔍</button>
                </form>
            </div>
        </div>
        <h1>Antique Art Auction</h1>
        <p>Bid on timeless masterpieces</p>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="#">Current Auctions</a>
        <a href="#">How It Works</a>
        <a href="contact.php">Contact</a>
    </nav>
    <!-- Category Selection Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <h3>Select a Category</h3>
            <select id="categorySelect" required>
                <option value="">-- Choose Category of the item--</option>
                <option value="Drawings">Drawings</option>
                <option value="Paintings">Paintings</option>
                <option value="Photographic Images">Photographic Images</option>
                <option value="Sculptures">Sculptures</option>
                <option value="Carvings">Carvings</option>
            </select>
            <button onclick="submitCategory()">Continue</button>
        </div>
    </div>

    <div class="auction-form-container">
        <h2>Add New Auction Item</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="addAuction.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category" id="selectedCategory">

            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required><br><br>

            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea><br><br>

            <label for="starting_price">Starting Price:</label>
            <input type="number" step="0.01" name="starting_price" id="starting_price" required><br><br>

            <label for="auction_start_date">Auction Start Date:</label>
            <input type="date" name="auction_start_date" id="auction_start_date" required min="<?php echo $today; ?>">

            <label for="auction_end_date">Auction End Date:</label>
            <input type="date" name="auction_end_date" id="auction_end_date" required min="<?php echo $today; ?>">


            <label for="image">Upload Image:</label>
            
            <input type="file" name="image" id="image" accept="image/*"><br><br>

            <button type="submit">Add Auction Item</button>
        </form>
    </div>

    <footer>
        &copy; 2025 Antique Art Auction. All rights reserved.
    </footer>
    <script>
        function submitCategory() {
            const category = document.getElementById("categorySelect").value;
            if (category === "") {
                alert("Please select a category.");
                return;
            }

            // Set hidden input in the form
            document.getElementById("selectedCategory").value = category;

            // Hide modal and show the form
            document.getElementById("categoryModal").style.display = "none";
            document.querySelector(".auction-form-container").style.display = "block";

            // Adjust form fields based on selected category
            adjustFormBasedOnCategory(category);
        }

        function adjustFormBasedOnCategory(category) {
            const titleInput = document.getElementById("title");
            const descriptionInput = document.getElementById("description");
            const startingPriceInput = document.getElementById("starting_price");
            const auctionStartDateInput = document.getElementById("auction_start_date");
            const auctionEndDateInput = document.getElementById("auction_end_date");

            // Reset form to default values
            titleInput.value = '';
            descriptionInput.value = '';
            startingPriceInput.value = '';
            auctionStartDateInput.value = '';
            auctionEndDateInput.value = '';

            // Update the form based on the selected category
            switch (category) {
                case "Drawings":
                    descriptionInput.placeholder = "Provide details about the drawing, materials used, etc.";
                    startingPriceInput.placeholder = "Enter starting price for the drawing";
                    break;
                case "Paintings":
                    descriptionInput.placeholder = "Describe the painting's style, size, and artist.";
                    startingPriceInput.placeholder = "Enter starting price for the painting";
                    break;
                case "Photographic Images":
                    descriptionInput.placeholder = "Provide details about the photograph and its background.";
                    startingPriceInput.placeholder = "Enter starting price for the photograph";
                    break;
                case "Sculptures":
                    descriptionInput.placeholder = "Describe the sculpture's materials, artist, and history.";
                    startingPriceInput.placeholder = "Enter starting price for the sculpture";
                    break;
                case "Carvings":
                    descriptionInput.placeholder = "Describe the carving's materials and origin.";
                    startingPriceInput.placeholder = "Enter starting price for the carving";
                    break;
                default:
                    break;
            }
        }

        // Hide the form initially
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector(".auction-form-container").style.display = "none";
        });
    </script>


</body>

</html>