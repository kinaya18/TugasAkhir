<?php
// Konfigurasi database
$servername = "sql308.infinityfree.com";
$username = "if0_42290825";
$password = "AcZRMfp9olOdaje";
$dbname = "if0_42290825_asma_db";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data dari GET request
if(isset($_GET["temperature"]) && isset($_GET["humidity"])) {
    $temperature = $_GET["temperature"];
    $humidity = $_GET["humidity"];
    $pm1_0 = isset($_GET["pm1_0"]) ? $_GET["pm1_0"] : 0;
    $pm2_5 = isset($_GET["pm2_5"]) ? $_GET["pm2_5"] : 0;
    $pm10 = isset($_GET["pm10"]) ? $_GET["pm10"] : 0;
    $co2 = isset($_GET["co2_ppm"]) ? $_GET["co2_ppm"] : 0;
    $ozone = isset($_GET["ozone_ppm"]) ? $_GET["ozone_ppm"] : 0;
    $no2 = isset($_GET["no2_ppm"]) ? $_GET["no2_ppm"] : 0;
    
    $sql = "INSERT INTO data_udara (temperature, humidity, pm1_0, pm2_5, pm10, co2_ppm, ozone_ppm, no2_ppm) 
            VALUES ('$temperature', '$humidity', '$pm1_0', '$pm2_5', '$pm10', '$co2', '$ozone', '$no2')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "No data received";
}

$conn->close();
?>