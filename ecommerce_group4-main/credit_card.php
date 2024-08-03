<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "e-commerce";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = trim($_POST["card_number"]);
    $card_holder_name = trim($_POST["card_holder_name"]);
    $expiry_date = trim($_POST["expiry_date"]);
    $cvv = trim($_POST["cvv"]);

    if (empty($card_number)) {
        $card_numberError = "Card number is required.";
    } elseif (!preg_match('/^\d{16}$/', $card_number)) {
        $card_numberError = "Card number must be 16 digits long.";
    }

    if (empty($card_holder_name)) {
        $cardHolderNameError = "Card holder name is required.";
    } elseif (!preg_match('/^[A-Za-z]+ [A-Za-z]+$/', $card_holder_name)) {
        $cardHolderNameError = "Card holder name must consist of at least two words separated by a space.";
    }

    if (empty($expiry_date)) {
        $expiryDateError = "Expiry date is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date)) {
        $expiryDateError = "Expiry date must be in YYYY-MM-DD format.";
    }

    if (empty($cvv)) {
        $cvvError = "CVV is required.";
    } elseif (!preg_match('/^\d{3}$/', $cvv)) {
        $cvvError = "CVV must be 3 digits long.";
    }

    if (empty($card_numberError) && empty($cardHolderNameError) && empty($expiryDateError) && empty($cvvError)) {
        $sql = "INSERT INTO credit_card (card_number, card_holder_name, expiry_date, cvv) VALUES (?, ?, ?, ?)";

        if ($stmt = $connection->prepare($sql)) {
            $stmt->bind_param("ssss", $card_number, $card_holder_name, $expiry_date, $cvv);

            if ($stmt->execute()) {
                $successMessage = "Card details added successfully.";
                header("location: index.php");
                exit;
            } else {
                $errorMessage = "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $connection->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .btn-danger {
            border-color: #007bff; 
        }
        .btn-extra-lg {
            font-size: 24px;
            padding: 15px 100px;
            border: 2px solid #007bff; 
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2>credit_card</h2>
        <br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="signupForm">

            <div class="mb-3">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" class="form-control" value="<?php echo htmlspecialchars($card_number); ?>">
                <span class="text-danger"><?php echo $card_numberError; ?></span>
            </div>

            <div class="mb-3">
                <label for="card_holder_name">Card Holder Name</label>
                <input type="text" id="card_holder_name" name="card_holder_name" class="form-control" value="<?php echo htmlspecialchars($card_holder_name); ?>">
                <span class="text-danger"><?php echo $cardHolderNameError; ?></span>
            </div>

            <div class="mb-3">
                <label for="expiry_date">Expiry Date</label>
                <input type="text" id="expiry_date" name="expiry_date" class="form-control" value="<?php echo htmlspecialchars($expiry_date); ?>">
                <span class="text-danger"><?php echo $expiryDateError; ?></span>
            </div>

            <div class="mb-3">
                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" class="form-control">
                <span class="text-danger"><?php echo $cvvError; ?></span>
            </div>

            <div class="row mb-3">
                <div class="col d-flex justify-content-center">
                    <input type="submit" class="btn btn-primary btn-extra-lg mx-2" value="Submit">
                    <a href="index.php" class="btn btn-primary btn-extra-lg mx-2" role="button">Cancel</a>
                </div>
            </div>

        </form>
    </div>

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
