<?php 
session_start();
$login = false;
$showError = false;

include 'partials/_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];

    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);

    if ($num > 0) {
        $passwordMatched = false; // Track if any password matches

        while ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $passwordMatched = true;
                $login = true;
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];
                header("location: welcome.php");
                exit;
            }
        }

        if (!$passwordMatched) {
            $showError = "Invalid password. Please try again.";
        }
    } else {
        $showError = "No users found.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <?php require 'partials/_nav.php'; ?>

    <?php
    if (isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Your OTP has been verified. Logging in...
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        unset($_SESSION['otp_verified']);
    }

    if ($login) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success!</strong> You are logged in.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    }

    if ($showError) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error!</strong> ' . $showError . '
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    }
    ?>

    <div class="container">
        <h2 class="text-center">Password Login</h2>
        <form action="" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3 col-md-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary col-md-4">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
