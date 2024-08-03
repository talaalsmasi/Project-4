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
$card_number = "";
$card_holder_name = "";
$expiry_date = "";
$cvv = "";
$errorMessage = "";
$successMessage = "";
$card_numberError = "";
$cardHolderNameError = "";
$expiryDateError = "";
$cvvError = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["user_id"]) || empty($_GET["user_id"])) {
        header("Location: index.php");
        exit;
    }

    $user_id = intval($_GET["user_id"]);  // Sanitize user_id
    $sql = "SELECT * FROM credit_card WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        header("Location: index.php");
        exit;
    }

    $card_number = $row["card_number"];
    $card_holder_name = $row["card_holder_name"];
    $expiry_date = $row["expiry_date"];
    $cvv = $row["cvv"];

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = intval($_POST["user_id"]);  
    $card_number = $conn->real_escape_string($_POST["card_number"]);
    $card_holder_name = $conn->real_escape_string($_POST["card_holder_name"]);
    $expiry_date = $conn->real_escape_string($_POST["expiry_date"]);
    $cvv = $conn->real_escape_string($_POST["cvv"]);

    if (empty($card_number) || empty($card_holder_name) || empty($expiry_date) || empty($cvv)) {
        $errorMessage = "All fields are required";
    } 

    // Assuming CVV is a 3-digit number and expiry date is in YYYY-MM format
    if (!preg_match('/^\d{16}$/', $card_number)) {
        $card_numberError = "Card number must be 16 digits long.";
    }

    if (!preg_match('/^[A-Za-z]+ [A-Za-z]+$/', $card_holder_name)) {
        $cardHolderNameError = "Card holder name must consist of at least two words separated by a space.";
    }
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
        $expiryDateError = "Expiry date must be in YYYY-MM-DD format.";
    }
    
    if (!preg_match('/^\d{3}$/', $cvv)) {
        $cvvError = "CVV must be 3 digits long.";
    }

    if (empty($card_numberError) && empty($cardHolderNameError) && empty($expiryDateError) && empty($cvvError)) {
        $sql = "UPDATE credit_card SET card_number = ?, card_holder_name = ?, expiry_date = ?, cvv = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $card_number, $card_holder_name, $expiry_date, $cvv, $user_id);

        if ($stmt->execute()) {
            $successMessage = "Card information updated correctly";
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
            background-color: #f0f8ff; /* Light AliceBlue background color */
            color: #333;
        }
        .container {
            max-width: 600px; /* Set a max-width for the container */
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px; /* Adjust top margin for spacing */
        }
        .container h2 {
            color: #0056b3; /* Primary color for heading */
        }
        .text-danger {
            color: #dc3545;
        }
    </style>
    <title>Edit Credit Card</title>
</head>
<body>
<div class="container my-5">
    <h2 class="text-center mb-4">Edit Credit Card</h2>

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

    <form method="post">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
        <div class="mb-3">
            <label class="form-label">Card Number</label>
            <input type="text" class="form-control" name="card_number" value="<?php echo htmlspecialchars($card_number); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($card_numberError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Card Holder Name</label>
            <input type="text" class="form-control" name="card_holder_name" value="<?php echo htmlspecialchars($card_holder_name); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($cardHolderNameError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Expiry Date</label>
            <input type="text" class="form-control" name="expiry_date" value="<?php echo htmlspecialchars($expiry_date); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($expiryDateError); ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">CVV</label>
            <input type="text" class="form-control" name="cvv" value="<?php echo htmlspecialchars($cvv); ?>">
            <div class="text-danger mt-2"><?php echo htmlspecialchars($cvvError); ?></div>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-outline-primary" href="index.php" role="button">Cancel</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.querySelector("form").addEventListener("submit", function (event) {
        let errorMessage = [];

        let cardNumber = document.querySelector('input[name="card_number"]').value;
        let cardHolderName = document.querySelector('input[name="card_holder_name"]').value;
        let expiryDate = document.querySelector('input[name="expiry_date"]').value;
        let cvv = document.querySelector('input[name="cvv"]').value;

        let cardNumberPattern = /^\d{16}$/;
        let cardHolderNamePattern = /^[A-Za-z]+ [A-Za-z]+$/;
        let expiryDatePattern = /^\d{4}-\d{2}-\d{2}$/;
        let cvvPattern = /^\d{3}$/;

        if (cardNumber.length === 0) {
            errorMessage.push("Card number is required.");
        } else if (!cardNumberPattern.test(cardNumber)) {
            errorMessage.push("Card number must be 16 digits long.");
        }

        if (cardHolderName.length === 0) {
            errorMessage.push("Card holder name is required.");
        } else if (!cardHolderNamePattern.test(cardHolderName)) {
            errorMessage.push("Card holder name must consist of at least two words separated by a space.");
        }

        if (expiryDate.length === 0) {
            errorMessage.push("Expiry date is required.");
        } else if (!expiryDatePattern.test(expiryDate)) {
            errorMessage.push("Expiry date must be in YYYY-MM-DD format.");
        }

        if (cvv.length === 0) {
            errorMessage.push("CVV is required.");
        } else if (!cvvPattern.test(cvv)) {
            errorMessage.push("CVV must be 3 digits long.");
        }

        if (errorMessage.length > 0) {
            event.preventDefault();
            alert(errorMessage.join("\n"));
        }
    });
</script>
</body>
</html>

