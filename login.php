<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'partials/_dbconnect.php';

$login = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Validate email
    $stmt = $conn->prepare("SELECT sno FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    // $result = $stmt->get_result();
    $stmt->store_result();

    if (!$stmt->num_rows > 0) {
        echo "No user found with that email.";
    } else {
        // Generate OTP
        $otp = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)); // 6-digit alphanumeric code
        $otp_expiration = date("Y-m-d H:i:s", strtotime("+2 minutes"));

        // Update OTP in the database
        $stmt = $conn->prepare("INSERT INTO otp (email, otp, otp_expiration) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $otp, $otp_expiration);
        $stmt->execute();


        // Send OTP to user's email
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'duwalabhi020@gmail.com'; // Securely store and retrieve this
            $mail->Password = 'pfdz komi lsxp ehbj'; // Securely store and retrieve this
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('duwalabhi020@gmail.com', 'Abhinabh Duwal');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP code';
            $mail->Body = "Your OTP code is $otp. It expires in 2 minutes.";

            // Send email
            if ($mail->send()) {
                // Redirect to OTP verification page
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Login</title>
</head>

<body>
    <?php require 'partials/_nav.php'; ?>

    <?php
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
        <h2 class="text-center">Login</h2>
        <form action="" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3 col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
            </div>

            <button type="submit" name="send" class="btn btn-primary col-md-4">Request OTP</button>
        </form>
        <div class="mt-3 text-center">
            <p>Don't have an account yet? <a href="signup.php" class="text-primary">Sign Up</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
