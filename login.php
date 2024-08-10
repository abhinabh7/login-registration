<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'partials/_dbconnect.php';

$showAlert = false;
$showError = false;

if (isset($_SESSION['signup_success'])) {
    $showAlert = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $_SESSION['email'] = $email;

    $stmt = $conn->prepare("SELECT sno FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $showError = "No user found with that email.";
    } else {
        $otp = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $otp_expiration = date("Y-m-d H:i:s", strtotime("+2 minutes"));

        $stmt = $conn->prepare("INSERT INTO otp (email, otp, otp_expiration) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $otp, $otp_expiration);
        $stmt->execute();

        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'duwalabhi020@gmail.com';
            $mail->Password = 'pfdz komi lsxp ehbj';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('duwalabhi020@gmail.com', 'Abhinabh Duwal');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP code';
            $mail->Body = "Your OTP code is $otp. It expires in 2 minutes.";

            if ($mail->send()) {
                $_SESSION['otp_sent'] = true;
                header("Location: verify_otp.php");
                exit();
            } else {
                $showError = "Failed to send OTP. Please try again.";
            }
        } catch (Exception $e) {
            $showError = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
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
    if ($showAlert) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> ' . $showAlert . '
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
        <h2 class="text-center">Login</h2>
        <form action="" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3 col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" name="send" class="btn btn-primary col-md-4">Request OTP</button>
        </form>
        <div class="mt-3 text-center">
            <p>Don't have an account yet? <a href="index.php" class="text-primary">Sign Up</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
