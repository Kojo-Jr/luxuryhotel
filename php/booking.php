<?php
class Guest
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function register($guest, $checkin, $checkout, $rooms, $adults, $children)
    {

        //define variables to empty
        $guestErr = $inErr = $outErr = $roomsErr = $adultsErr = "";

        //Sanitize input
        $guest = $this->sanitizeInput($guest);
        $checkin = $this->sanitizeInput($checkin);
        $checkout = $this->sanitizeInput($checkout);
        $rooms = (int)$rooms;
        $adults = (int)$adults;
        $children = (int)$children;

        //validation
        if (empty($guest) || !preg_match("/^[a-zA-Z- ]*$/", $guest)) {
            //throw new Exception("Name can contain letters and hyphens");
            $guestErr = "Name can contain letters and hyphens";
        }

        if (empty($checkin)) {
            //throw new Exception("You need to set a checkin date");
            $inErr = "You need to set a check-in date";
        }

        if (empty($checkout)) {
            //throw new Exception("You need to set a checkout date");
            $outErr = "You need to set a check-out date";
        }

        if (empty($rooms)) {
            //throw new Exception("Select a room");
            $roomsErr = "Select a room";
        }

        if (empty($adults)) {
            //throw new Exception('You need to select the number of adults booking for a room');
            $adultsErr = "You need to select the number of adults booking for a room";
        }

        if (!empty($guestErr) || !empty($inErr) || !empty($outErr) || !empty($roomsErr) || !empty($adultsErr)) {
            echo "<script>alert('Name, check-in, check-out, adult should be completed');</script>";
            return [$guestErr, $inErr, $outErr, $roomsErr, $adultsErr];
        }
        try {
            //Query to check availabilty
            $sql = "SELECT * FROM bookings WHERE rooms = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$rooms]);

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['rooms'] == $rooms) {
                    $roomsErr = "Room not available";
                    echo "<script>alert('$roomsErr');</script>";
                }
            } else {
                //Insert to new guest into the database

                $sql = "INSERT INTO bookings(guestName, checkin_date, checkout_date, rooms, adults, children) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$guest, $checkin, $checkout, $rooms, $adults, $children]);

                if ($stmt->rowCount() > 0) {
                    //Generate BookingID
                    $bookingID = rand(100, 10000);
                    $sql = "UPDATE bookings SET bookingId = ? WHERE rooms =?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([$bookingID, $rooms]);

                    //Generate and display receipt
                    $receipt = "Your booking has been confirmed. Booking ID is $bookingID. Thank you.";
                    echo "<script>alert('$receipt');</script>";
                } else {
                    echo "<script>alert('Rooms are not available for the selected dates.')</script>;";
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        //return any error messages if necessary
        return [$guestErr, $inErr, $outErr, $roomsErr, $adultsErr];
    }

    private function sanitizeInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}


//Instantiate the Guest class

//$guestObj = new Guest($conn);
//$conn is the database connection
//$guestObj->register($guest, $checkin, $checkout, $rooms, $adults, $children);
/*
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])){
	//Get form data
	$guest = $_POST['guestName'];
	$checkin = $_POST['checkin'];
	$checkout = $_POST['checkout'];
	$rooms = $_POST['rooms'];
	$adults = $_POST['adults'];
	$childern = $_POST['children'];

	//call the register method
	try{
		$guestObj->register($guest, $checkin, $checkout, $rooms, $adults, $children);
	} catch(Exception $e){
		echo "<script>alert('Error: ".$e->getMessage()."');</script>";
	} 
}*/
