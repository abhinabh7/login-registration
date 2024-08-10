<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$showError = false;
$showAlert = false;
include 'partials/_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resend'])) {
        $email = $_SESSION['email'];

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
            $mail->Body = "Your new OTP code is $otp. It expires in 2 minutes.";

            if ($mail->send()) {
                $showAlert = "A new OTP has been sent to your email.";
            } else {
                $showError = "Failed to resend OTP. Please try again.";
            }
        } catch (Exception $e) {
            $showError = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $entered_otp = $_POST['otp'];

        if (!isset($_SESSION['email'])) {
            $showError = "Session expired or email not set.";
        } else {
            $email = $_SESSION['email'];

            $stmt = $conn->prepare("SELECT * FROM otp WHERE email = ? AND otp = ?");
            $stmt->bind_param("ss", $email, $entered_otp);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $current_time = new DateTime();
                $expiration_time = new DateTime($row['otp_expiration']);

                if ($current_time <= $expiration_time) {
                    $_SESSION['otp_verified'] = true;
                    header('Location: password.php');
                    exit();
                } else {
                    $showError = "OTP has expired.";
                }
            } else {
                $showError = "Invalid OTP.";
            }
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
    <title>Verify OTP</title>
</head>
<body>
    <?php require 'partials/_nav.php'; ?>

        <?php
        if (isset($_SESSION['otp_sent']) && $_SESSION['otp_sent']) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> We have send you an OTP. Please check you mail.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            unset($_SESSION['otp_sent']);
        }


        // Display error alert if it exists
        if ($showError) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Error!</strong> ' . $showError . '
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        }
        ?>

        <h2 class="text-center">Verify OTP</h2>
        <form id="otpForm" action="" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3 col-md-4">
                <input type="text" class="form-control" id="otp" name="otp" placeholder="OTP" maxlength="6" >
            </div>
            <button type="submit" class="btn btn-primary col-md-4">Verify OTP</button>
            <button type="submit" name="resend" class="btn btn-link">Resend OTP</button>
        </form>
    


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
