<?php
define("SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");

try {
    $conn = new PDO("mysql:host=" . SERVER . ";dbname=luxuryhotel", DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected Successfully";
} catch (PDOException $e) {
    echo "Connection failed:" . $e->getMessage();
}
