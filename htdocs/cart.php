<?php
// Start session
session_start();

// Initialize variables
$servername = "localhost";
$username = "root";
$password = "";
$database_name = "s";

// Establish database connection
$conn = mysqli_connect($servername, $username, $password, $database_name);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Check if the form was submitted
if (isset($_POST['add_to_cart'])) {
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Escape user inputs to prevent SQL injection
        $product_id = mysqli_real_escape_string($conn, $product_id);
        $quantity = mysqli_real_escape_string($conn, $quantity);

        // Insert a new record into the cart_items table using the user_id
        $sql_query = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        if (mysqli_query($conn, $sql_query)) {
            $_SESSION['message'] = "Item added to cart!";
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['message'] = "Error: User not logged in.";
    }

    mysqli_close($conn);

    // Redirect to display_messages.php
    header("Location: display_messages.php");
    exit();
}
?>
