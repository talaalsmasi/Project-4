<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "e-commerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = "";
$username = "";
$email = "";
$address = "";
$password = "";
$confirm_password = "";
$phone = "";
$created_at = "";
$errorMessage = "";
$successMessage = "";
$usernameError = "";
$emailError = "";
$addressError = "";
$passwordError = "";
$phoneError = "";
$created_atError = "";
$confirm_passwordError = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["user_id"]) || empty($_GET["user_id"])) {
        header("Location: index.php");
        exit;
    }

    $user_id = intval($_GET["user_id"]);  // Sanitize user_id
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        header("Location: index.php");
        exit;
    }

    $username = $row["username"];
    $email = $row["email"];
    $address = $row["address"];
    $phone = $row["phone"];
    $created_at = $row["created_at"];

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = intval($_POST["user_id"]);  
    $username = $conn->real_escape_string($_POST["username"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $address = $conn->real_escape_string($_POST["address"]);
    $phone = $conn->real_escape_string($_POST["phone"]);
    $created_at = $conn->real_escape_string($_POST["created_at"]);
    $password = $conn->real_escape_string($_POST["password"]);
    $confirm_password = $conn->real_escape_string($_POST["confirm_password"]);

    // Validation
    if (empty($username) || empty($email) || empty($address) || empty($phone) || empty($created_at)) {
        $errorMessage = "All fields are required";
    }

    if (!preg_match('/^[\w]+(?: [\w]+){3}$/', $username)) {
        $usernameError = 'Username must be in 4 sections separated by spaces.';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $passwordError = 'Password must be at least 8 characters long and include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.';
    }
    
    if ($password !== $confirm_password) {
        $confirm_passwordError = 'Passwords do not match.';
    }
    
    if (!preg_match('/^07\d{8}$/', $phone)) {
        $phoneError = 'Phone number must start with 07 and be 10 digits long.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Email address is invalid.';
    }

    if (empty($usernameError) && empty($emailError) && empty($addressError) && empty($passwordError) && empty($phoneError) && empty($created_atError)   && empty($confirm_passwordError)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET username = ?, email = ?, address = ?, password = ?, phone = ?, created_at = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssi', $username, $email, $address, $hashed_password, $phone, $created_at, $user_id);

        if ($stmt->execute()) {
            $successMessage = "User information updated correctly";
            header("Location: index.php");
            exit;
        } else {
            $errorMessage = "Invalid query: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f8ff;
            color: #333;
        }
        .container {
            max-width: 600px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .container h2 {
            color: #0056b3;
        }
        .text-danger {
            color: #dc3545;
        }
    </style>
    <title>Edit User</title>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Edit User</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
            <strong><?php echo htmlspecialchars($errorMessage); ?></strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong><?php echo htmlspecialchars($successMessage); ?></strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    <?php endif; ?>

    <form id="editForm" method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($usernameError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($emailError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($address); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($addressError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($passwordError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($confirm_passwordError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($phoneError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Created At</label>
            <input type="text" class="form-control" name="created_at" value="<?php echo htmlspecialchars($created_at); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($created_atError); ?></div>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-outline-primary" href="index.php" role="button">Cancel</a>
        </div>
    </form>

    <script>
        document.getElementById('editForm').addEventListener('submit', function(event) {
            let valid = true;
            const username = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            
            const usernameError = document.querySelector('div.text-danger.mt-2');
            const phoneError = document.querySelector('div.text-danger.mt-2');

            // Validate username
            if (!/^[\w]+(?: [\w]+){3}$/.test(username)) {
                usernameError.textContent = 'Username must be in 4 sections separated by spaces.';
                valid = false;
            } else {
                usernameError.textContent = '';
            }

            // Validate phone number
            if (!/^07\d{8}$/.test(phone)) {
                phoneError.textContent = 'Phone number must start with 07 and be 10 digits long.';
                valid = false;
            } else {
                phoneError.textContent = '';
            }

            // Validate password
            if (password !== confirmPassword) {
                passwordError.textContent = 'Passwords do not match.';
                valid = false;
            } else {
                passwordError.textContent = '';
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-1A2PZmcZhB8XiyTciRb+qeTm4W28zQ8p4t5qT02dHP1dP2e+Zo3y7e4I5mJwwP1" crossorigin="anonymous"></script>
</body>
</html>
