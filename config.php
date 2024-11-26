<?php
date_default_timezone_set('Asia/Kolkata');

$hostname = "localhost";
$username = "root";
$password = "";
$database = "db_eval";

define('GEMINI_API_KEY','AIzaSyB7LWOkzYNRuxo0BorEOxEC-CqOqgYrz30');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent');

if(!$conn = mysqli_connect($hostname, $username, $password, $database)){

 die("Database connection failed");
}
$sql = "CREATE TABLE IF NOT EXISTS proctoring_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id VARCHAR(50),
    user_id VARCHAR(50),
    violation_type VARCHAR(50),
    details TEXT,
    timestamp DATETIME,
    severity VARCHAR(20),
    INDEX (exam_id),
    INDEX (user_id)
)";
$conn->query($sql);
$time = date("H");
    /* Set the $timezone variable to become the current timezone */
$timezone = date("e");
    /* If the time is less than 1200 hours, show good morning */
if ($time < "12") {
     $greet= "Good Morning";
     $img="img/mng.jpg";
} else
    /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
 if ($time >= "12" && $time < "17") {
    $greet= "Good Afternoon";
    $img="img/aftn.jpg";
  } else
    /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
 if ($time >= "17" && $time < "19") {
    $greet= "Good Evening";
    $img="img/evng.jpg";
} else
    /* Finally, show good Evening if the time is greater than or equal to 1900 hours */
 if ($time >= "19") {
    $greet= "Good Evening";
    $img="img/evng.jpg";
}
class ExamProctor {
    private $conn;
    private $examId;
    private $userId;
    
    public function __construct($conn, $examId, $userId) {
        $this->conn = $conn;
        $this->examId = $examId;
        $this->userId = $userId;
    }
    
    public function logViolation($type, $details) {
        $timestamp = date('Y-m-d H:i:s');
        
        // Analyze severity using Gemini
        $severity = $this->analyzeSeverity($type, $details);
        
        $sql = "INSERT INTO proctoring_logs (exam_id, user_id, violation_type, details, timestamp, severity) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssss', $this->examId, $this->userId, $type, $details, $timestamp, $severity);
        $stmt->execute();
        
        return $this->shouldTerminateExam($type, $severity);
    }
    
    private function analyzeSeverity($type, $details) {
        $data = [
            'contents' => [
                'parts' => [
                    [
                        'text' => "Analyze this exam violation and classify severity as 'low', 'medium', or 'high':\nType: $type\nDetails: $details"
                    ]
                ]
            ]
        ];

        $ch = curl_init(GEMINI_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . GEMINI_API_KEY
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'medium';
    }
    
    private function shouldTerminateExam($type, $severity) {
        // Check violation count in last 5 minutes
        $sql = "SELECT COUNT(*) as count FROM proctoring_logs 
                WHERE exam_id = ? AND user_id = ? 
                AND timestamp >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                AND severity = 'high'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $this->examId, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return ($row['count'] >= 3 || $severity === 'high');
    }
}

?>