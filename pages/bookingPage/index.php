<?php
ini_set('date.timezone', 'Africa/Accra');
@include("../../php/dbconnect.php");
@include("../../php/booking.php");

session_start();
$timelimit = 10 * 60;
$now = time();

if (!isset($_SESSION['email'])) {
    echo "<script>alert('You can not access this page! Kindly login before, you can access this page');window.location='../signup/signup.php'</script>";
    exit;
}
if ($now > $_SESSION['time'] + $timelimit) {
    echo "<script>alert('Your session has expired! Login to continue your process');window.location='../login/login.php'</script>";
    exit;
}

//define variables to empty
$guestErr = $inErr = $outErr = $roomsErr = $adultsErr = "";
$guest = $checkin = $checkout = $rooms = $adults = "";

$guestObj = new Guest($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Get form data
    $guest = $_POST['guestName'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $rooms = $_POST['rooms'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];

    // Call the register method
    try {
        $guestObj->register($guest, $checkin, $checkout, $rooms, $adults, $children);
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="indexstyles.css">
</head>

<body>
    <header>

    </header>
    <div id="booking" class="section">
        <nav>
            <ol>
                <li><a href="#"><?php echo 'Hey ' . $_SESSION['email']; ?></a></li>
            </ol>
        </nav>
        <div class="section-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 col-md-push-5">
                        <div class="booking-cta">
                            <h1>Make your reservation</h1>
                            <p><strong>Experience the epitome of luxury at our exquisite hotel. Indulge in unparalleled comfort and sophistication, where every detail is meticulously crafted to ensure a memorable stay.
                                    Discover a haven of elegance and refinement, where your every need is catered to with exceptional service and care. Welcome to a world of luxury beyond compare.</strong>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 col-md-pull-7">
                        <div class="booking-form">
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                <div class="form-group">
                                    <span class="form-label">Your Destination</span>
                                    <input class="form-control" type="text" placeholder="Luxury Hotel" disabled><br />
                                    <span class="form-label">Name</span>
                                    <input class="form-control" type="text" placeholder="Enter Full Name" name="guestName" value="<?php echo $guest ?>"><span class="error"><?php echo $guestErr; ?></span>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <span class="form-label">Check In</span>
                                            <input class="form-control" type="date" name="checkin" required value="<?php echo $checkin ?>">
                                            <span class="error"><?php if (isset($_POST['checkin'])) echo $inErr; ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <span class="form-label">Check out</span>
                                            <input class="form-control" type="date" name="checkout" required value="<?php echo $checkout ?>">
                                            <span class="error"><?php if (isset($_POST['checkout'])) echo $outErr; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <span class="form-label">Rooms</span>
                                            <select class="form-control" name="rooms" required>
                                                <option disable>--select--</option>
                                                <option>102</option>
                                                <option>103</option>
                                                <option>104</option>
                                                <option>105</option>
                                                <option>106</option>
                                                <option>107</option>
                                                <option>108</option>
                                                <option>109</option>
                                                <option>110</option>
                                            </select><span class="error"><?php if (isset($_POST['rooms'])) echo $roomsErr; ?></span>
                                            <span class="select-arrow"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <span class="form-label">Adults</span>
                                            <select class="form-control" name="adults" required value="<?php echo $adults ?>">
                                                <option disable>--select--</option>
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4+</option>
                                            </select>
                                            <span class="select-arrow"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <span class="form-label">Children</span>
                                            <select class="form-control" name="children" required value="<?php echo $children ?>">
                                                <option disable>--select--</option>
                                                <option>0</option>
                                                <option>1</option>
                                                <option>2</option>
                                                <option>3</option>
                                                <option>4+</option>
                                            </select>
                                            <span class="select-arrow"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-btn">
                                    <!--<button class="submit-btn">Check availability</button>-->
                                    <input type="submit" name="submit" value="BOOK" class="submit-btn">
                                    <button class="submit-btn"><a href="../signout/signout.php">Sign Out</a></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            if (document.cookie.indexOf('logged_out=true') !== -1) {
                // Clear the cookie
                document.cookie = 'logged_out=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;';

                // Redirect to the login page
                window.location.href = "../../pages/login/login.php";
            }
        };
    </script>
</body>

</html>