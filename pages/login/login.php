<?php
// start session
session_start();

// csrf token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// include database connection file
include("../../php/dbconnect.php");

// set the variables to empty values
$email = $password = "";
$emailErr = $passErr = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate email
  if (empty($_POST['email'])) {
    $emailErr = 'Email is required';
  } else {
    $email = test_input($_POST['email']);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = 'Invalid email format';
    }
  }

  // Validate password
  if (empty($_POST['password'])) {
    $passErr = 'Password is required';
  } else {
    $password = test_input($_POST['password']);
  }

  // Check if email and password are not empty
  if (empty($emailErr) && empty($passErr)) {
    // Check if email and password match a record in the database
    try {
      $sql = "SELECT * FROM users WHERE email = :email";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($result && password_verify($password, $result['password'])) {
        // Password is correct
        $_SESSION['email'] = $email;
        $_SESSION['time'] = time();
        echo "<script>alert('Login successful. Redirecting...');window.location.href='../bookingPage/index.php';</script>";
        exit();
      } else {
        // Password is incorrect
        $passErr = 'Incorrect email or password';
        echo "<script>alert('" . htmlspecialchars($passErr) . "');</script>";
      }
    } catch (PDOException $e) {
      error_log($e->getMessage());
      echo "<script>alert('An error occurred. Please try again later');</script>";
    }
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
  <title>Login Form</title>
  <link rel="stylesheet" href="styles.css" />
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
      if (isset($emailErr) || isset($passErr)) {
        echo '<div class="error">' . $emailErr . '</div>';
        echo '<div class="error">' . $passErr . '</div>';
      }
      ?>
      <header>Luxury Hotel</header>
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="field">
          <span class="fa fa-user"></span>
          <input type="text" name="email" required placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" />
          <!-- <span class="error"><?php echo $emailErr; ?></span> -->
        </div>
        <div class="field space">
          <span class="fa fa-lock"></span>
          <input type="password" name="password" class="pass-key" required placeholder="Password" />
          <span class="show">SHOW</span><br>
          <!-- <span class="error"><?php echo $passErr; ?></span> -->
        </div>
        <div class="pass">
          <a href="#">Forgot Password?</a>
        </div>
        <div class="field">
          <input type="submit" name="submit" value="LOGIN" />
        </div>
      </form>
      <div class="login">Or login with</div>
      <div class="links">
        <div class="facebook">
          <i class="fab fa-facebook-f"><span>Facebook</span></i>
        </div>
        <div class="google">
          <i class="fab fa-google"><span>Google</span></i>
        </div>
      </div>
      <div class="signup">
        Don't have account?
        <a href="../signup/signup.php">Signup Now</a>
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