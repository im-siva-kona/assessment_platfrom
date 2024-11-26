<?php
session_start();
if (!isset($_POST["exid"])) {
    header("Location: dash.php");
    exit();
}

include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST["exid"])) {
    $nq = mysqli_real_escape_string($conn, $_POST["nq"]);
    $exid = mysqli_real_escape_string($conn, $_POST["exid"]);
    $uname = mysqli_real_escape_string($conn, $_SESSION["uname"]);
    $j = 0; // Counter for correct answers
    $total_questions = $nq; // Total number of questions for percentage calculation
    $current_time = date('Y-m-d H:i:s');

    // Process each question
    for ($i = 1; $i <= $nq; $i++) {
        $qid = mysqli_real_escape_string($conn, $_POST['qid' . $i]);
        
        // Get question details
        $sql = "SELECT * FROM qstn_list WHERE exid='$exid' AND qid='$qid'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $qstn_type = $row['qstn_type'];
            
            if ($qstn_type == 'coding') {
                // Handle coding questions
                $submitted_code = isset($_POST['code' . $i]) ? mysqli_real_escape_string($conn, $_POST['code' . $i]) : '# No code submitted';
                $code_language = mysqli_real_escape_string($conn, $_POST['code_language' . $i]);
                
                // Store code submission
                $sql_submission = "INSERT INTO code_submissions (
                    qid, 
                    exid, 
                    uname, 
                    submitted_code, 
                    submission_time
                ) VALUES (
                    '$qid',
                    '$exid',
                    '$uname',
                    '$submitted_code',
                    '$current_time'
                )";
                
                if (!mysqli_query($conn, $sql_submission)) {
                    error_log("Error storing code submission: " . mysqli_error($conn));
                }
                
                // Don't increment j here since coding evaluation will be done separately
                $total_questions--; // Decrease total questions as coding won't be part of immediate scoring
                
            } else {
                // Handle choice questions (single and multiple)
                $correct_answer = $row['qstn_ans'];
                
                if ($qstn_type == 'multiple_choice') {
                    // Handle multiple choice questions
                    $submitted_answer = isset($_POST['o' . $i]) ? $_POST['o' . $i] : array();
                    
                    if (is_array($submitted_answer)) {
                        // Sort both arrays for consistent comparison
                        $submitted_answers = array_map('trim', $submitted_answer);
                        sort($submitted_answers);
                        
                        $correct_answers = array_map('trim', explode(',', $correct_answer));
                        sort($correct_answers);
                        
                        // Compare as strings
                        if (implode(',', $submitted_answers) === implode(',', $correct_answers)) {
                            $j++;
                        }
                    }
                    
                } else if ($qstn_type == 'single_choice') {
                    // Handle single choice questions
                    $submitted_answer = isset($_POST['o' . $i]) ? $_POST['o' . $i] : '';
                    
                    if ($submitted_answer == $correct_answer) {
                        $j++;
                    }
                }
            }
        }
    }
    
    // Calculate percentage based on choice questions only
    $percentage = $total_questions > 0 ? ($j / $total_questions) * 100 : 0;
    
    // Insert attempt record
    $status = 1; // Completed
    $sql_attempt = "INSERT INTO atmpt_list (
        exid, 
        uname, 
        nq, 
        cnq, 
        ptg, 
        status
    ) VALUES (
        '$exid',
        '$uname',
        '$total_questions',
        '$j',
        '$percentage',
        '$status'
    )";
    
    if (mysqli_query($conn, $sql_attempt)) {
        header("Location: results.php");
        exit();
    } else {
        error_log("Error inserting attempt: " . mysqli_error($conn));
        echo "An error occurred while submitting your exam. Please contact support.";
    }
} else {
    header("Location: dash.php");
    exit();
}
?>