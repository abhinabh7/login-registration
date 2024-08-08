<?php
$showAlert = false;
$showError = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  include 'partials/_dbconnect.php'; // Ensure _dbconnect.php establishes a $conn variable

  $username = $_POST["username"];
  $password = $_POST["password"];
  $cpassword = $_POST["cpassword"];
  $email = $_POST["email"];

  // Check if email already exists
  $stmt = $conn->prepare("SELECT * FROM `users` WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $numExistRows = $result->num_rows;
  if ($numExistRows > 0) {
    $showError = "Email Already Exists";
  } else {
    if ($password == $cpassword) { // Simplified condition
      $hash = password_hash($password, PASSWORD_DEFAULT);

      // Insert new user
      $stmt = $conn->prepare("INSERT INTO `users` (`username`, `email`, `password`, `dt`) VALUES (?, ?, ?, current_timestamp())");
      $stmt->bind_param("sss", $username, $email, $hash); // Removed the unnecessary fourth parameter
      $stmt->execute();

      // Check if the user was inserted successfully
      if ($stmt->affected_rows > 0) {
        $showAlert = true;
      }
    } else {
      $showError = "Passwords do not match";
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
  <title>SignUp</title>
</head>

<body>
  <?php require 'partials/_nav.php' ?>
  <?php
  if ($showAlert) {
    echo '<script>
        alert("Your account is now created and you can login.");
        window.location.href = "login.php";
    </script>';
    exit();
  }
  if ($showError) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>' . $showError . ' 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> ';
  }
  ?>
  <div class="container">
    <form action="/test/signup.php" method="post" style="display: flex; align-items: center; flex-direction: column;">
      <h2 class="text-center mb-4"> SignUp</h2>

      <div class="mb-3 col-md-4">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" aria-describedby="usernameHelp" required>
      </div>
      <div class="mb-3 col-md-4">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
      </div>
      <div class="mb-3 col-md-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <div class="mb-3 col-md-4">
        <label for="cpassword" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="cpassword" name="cpassword" required>
        <div id="passwordHelp" class="form-text">Make sure to type the same password</div>
      </div>
      <button type="submit" class="btn btn-primary col-md-4">SignUp</button>
    </form>
    <div class="mt-3 text-center">
      <p>Already have an account? <a href="login.php" class="text-primary">Sign in</a></p>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>
