<?php
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}

include '../config.php';
error_reporting(0);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the last 100 data points
    $sql = "SELECT metric_name, value, timestamp 
            FROM analytics_data 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY timestamp DESC 
            LIMIT 100";
    
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organize data by timestamp
    $organizedData = [];
    foreach ($rows as $row) {
        $timestamp = strtotime($row['timestamp']) * 1000; // Convert to milliseconds for JS
        if (!isset($organizedData[$timestamp])) {
            $organizedData[$timestamp] = ['timestamp' => $timestamp];
        }
        $organizedData[$timestamp][$row['metric_name']] = floatval($row['value']);
    }

    // Sort by timestamp and convert to array
    ksort($organizedData);
    $finalData = array_values($organizedData);

    echo json_encode($finalData);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>