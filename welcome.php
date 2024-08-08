<?php
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']!=true){
  header("location: login.php");
  exit;
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>welcome </title>
  </head>
  <body>
    <?php require 'partials/_nav.php' ?>
    
    <div class="container my-5">
    <div class="alert alert-success" role="alert">
  <h4 class="alert-heading">Hey! Welcome </h4>
  <p>The assignment of Infromation Assurance Security has been completed. And you have successfully logged in.</p>
  <hr>
  <p class="mb-0">Whenever you need to, be sure to to logout <a href="/test/logout.php">using this link.</a></p>
</div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>