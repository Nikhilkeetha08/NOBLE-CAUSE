<?php
// Start session
session_start();

// Initialize variables
$servername = "localhost";
$username = "root";
$password = "";
$database_name = "s";

$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save'])) {
    // Establish connection
    $conn = mysqli_connect($servername, $username, $password, $database_name);
    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }

    // Retrieve user inputs
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($password)) {
        $message = "Both username and password are required.";
    } else {
        // Escape user inputs to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        // Prepare SQL query
        $sql_query = "SELECT * FROM signupform WHERE username = '$username'";

        // Execute SQL query
        $result = mysqli_query($conn, $sql_query);

        if ($result && mysqli_num_rows($result) > 0) {
            // Fetch user details
            $row = mysqli_fetch_assoc($result);
            $hashed_password = $row['password'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, create session
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $row['id']; // Store user_id in session

                // Insert into loginform table
                $login_insert_query = "INSERT INTO loginform (username, password) VALUES ('$username', '$hashed_password')";
                if (mysqli_query($conn, $login_insert_query)) {
                    header("Location: http://localhost/Noble%20cause/indexf.html");
                    exit(); // Ensure no further code is executed after redirection
                } else {
                    $message = "Error inserting into loginform table: " . mysqli_error($conn);
                }
            } else {
                // Incorrect password
                $message = "Incorrect username or password";
            }
        } else {
            // User does not exist
            $message = "User does not exist.";
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #4CAF50; /* Leafy green */
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50; /* Leafy green */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049; /* Darker shade of leafy green */
        }
        .error-message {
            color: #D8000C; /* Dark red */
            background-color: #FFD2D2; /* Light red */
            border: 1px solid #D8000C;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Form</h2>
        <?php if (!empty($message)) : ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" name="save" value="Login"><br><br>
        </form>
    </div>
</body>
</html>
