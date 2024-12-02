<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
error_reporting(E_ALL);
ini_set('display_errors', 1);

//define variables and set to empty values
$username = $email = $password = "";
$userErr  = $emailErr = $passErr = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //Data Validation and verification of username, email and password
  if (empty($_POST['username'])) {
    $userErr = 'Name is required';
  } else {
    $username = test_input($_POST['username']);
    //check if the username contains only allowed characters
    if (!preg_match("/^[a-zA-Z0-9' ]*$/", $username)) {
      $userErr = "Username can contain letters, numbers, underscores and hyphens";
    }
  }

  //validate email
  if (empty($_POST['email'])) {
    $emailErr = 'E-mail is required';
  } else {
    $email = test_input($_POST['email']);
    //check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = 'Invalid email format';
    }
  }

  //validate password
  if (empty($_POST['password'])) {
    $passErr = 'Password is required';
  } else {
    $password = test_input($_POST['password']);
    //check if the password meets the requirement (e.g., minimun length, complexity)
    if (strlen($password) < 8 || !preg_match("/^[a-zA-Z0-9]*$/", $password)) {
      $passErr = 'Password must be at least 8 characters and contain only letters and numbers';
    }
  }

  //if no errors, proceed with registration
  if (empty($userErr) && empty($emailErr) && empty($passErr)) {
    try {
      include('dbconnect.php');

      //check if username or email already exists
      $sql = 'SELECT username, email FROM users WHERE username = ? OR email = ?';
      $stmt = $conn->prepare($sql);
      $stmt->execute([$username, $email]);

      if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['username'] == $username) {
          $userErr = 'Username already exists';
        }
        if ($row['email'] == $email) {
          $emailErr = 'E-mail already exists';
        }
      } else {
        //hash the password using a secure algorithm
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        //Insert the new user into the database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword]);

        if ($stmt->rowCount() > 0) {
          echo "<script>alert('Registration successful. Log in to continue');window.location.href='login.php';</script>";
          exit();
        } else {
          echo "<script>alert('Registration failed. Please try again.');</script>";
        }
      }
      $conn = null;
    } catch (PDOException $e) {
      //log the error or display an error message
      error_log("Error: " . $e->getMessage());
      echo "<script>alert('An error occurred. Please try again later');</script>";
    }
  } else {
    echo "<script>alert('Registration failed. Please try again.');</script>";
  }
}

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Luxury Hotel</title>
  <link rel="stylesheet" href="./styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <style>
    .error {
      color: red;
      font-size: 1em;
      display: block;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <div class="bg-img">
    <div class="content">
      <?php
      if (isset($userErr) || isset($emailErr) || isset($passErr)) {
        echo '<div class="error">' . $userErr . '</div>';
        echo '<div class="error">' . $emailErr . '</div>';
        echo '<div class="error">' . $passErr . '</div>';
      }
      ?>
      <header>Luxury Hotel</header>
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" autocomplete="off">
        <div class="field">
          <span class="fa fa-user"></span>
          <input type="text" placeholder="Username" name="username" value="<?php echo htmlspecialchars($username); ?>" />
          <!-- <span class="error"><?php echo $userErr; ?></span> -->
        </div>
        <div class="field">
          <span class="fa fa-envelope"></span>
          <input type="text" placeholder="Email" name="email" value="<?php echo htmlspecialchars($email); ?>" />
          <!-- <span class="error"><?php echo $emailErr; ?></span> -->
        </div>
        <div class="field space">
          <span class="fa fa-lock"></span>
          <input type="password" class="pass-key" placeholder="Password" name="password" />
          <span class="show">SHOW</span>
          <!-- <span class="error"><?php echo $passErr; ?></span> -->
        </div>
        <div class="pass">
          <a href="forgot-password.php">Forgot Password?</a>
        </div>
        <div class="field">
          <input type="submit" value="Sign Up" name="submit" />
        </div>
      </form>
      <div class="signup">
        Already have an account?
        <a href="../login/login.php">Login here</a>
      </div>
    </div>
  </div>

  <script>
    const pass_field = document.querySelector(".pass-key");
    const showBtn = document.querySelector(".show");
    showBtn.addEventListener("click", function() {
      if (pass_field.type === "password") {
        pass_field.type = "text";
        showBtn.textContent = "HIDE";
        showBtn.style.color = "#3498db";
      } else {
        pass_field.type = "password";
        showBtn.textContent = "SHOW";
        showBtn.style.color = "#222";
      }
    });
  </script>
</body>

</html>