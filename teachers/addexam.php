<?php 
include('../config.php');

//Below code to add exam details
if (isset($_POST["addexm"])) {
    $exname = mysqli_real_escape_string($conn, $_POST["exname"]);
    $nq = mysqli_real_escape_string($conn, $_POST["nq"]);
    $desp = mysqli_real_escape_string($conn, $_POST["desp"]);
    $subt = mysqli_real_escape_string($conn, $_POST["subt"]);
    $extime = mysqli_real_escape_string($conn, $_POST["extime"]);
    $subject = mysqli_real_escape_string($conn, $_POST["subject"]);

    $sql = "INSERT INTO exm_list (exname, nq, desp, subt, extime, subject) 
            VALUES ('$exname', '$nq', '$desp', '$subt', '$extime', '$subject')";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        header("Location: exams.php");
        exit();
    } else {
        echo "<script>alert('Adding exam failed.');</script>";
        header("Location: exams.php");
        exit();
    }
}

//Below code to add questions to database
if (isset($_POST["addqp"])) {
    $nq = mysqli_real_escape_string($conn, $_POST["nq"]);
    $exid = mysqli_real_escape_string($conn, $_POST["exid"]);
    
    for ($i = 1; $i <= $nq; $i++) {
        $q = mysqli_real_escape_string($conn, $_POST['q' . $i]);
        $qstn_type = isset($_POST['qstn_type' . $i]) ? mysqli_real_escape_string($conn, $_POST['qstn_type' . $i]) : null;
        
        if ($qstn_type === 'coding') {
            // Handle coding question type
            $problem_desc = isset($_POST['question' . $i]) ? 
                mysqli_real_escape_string($conn, $_POST['question' . $i]) : null;
            $testcases = isset($_POST['testcases' . $i]) ? 
                mysqli_real_escape_string($conn, $_POST['testcases' . $i]) : null;
            $expected_output = isset($_POST['expected_output' . $i]) ? 
                mysqli_real_escape_string($conn, $_POST['expected_output' . $i]) : null;
            $time_complexity = isset($_POST['time_complexity' . $i]) ? 
                mysqli_real_escape_string($conn, $_POST['time_complexity' . $i]) : null;

            $sql = "INSERT INTO qstn_list (
                    exid, 
                    qstn, 
                    qstn_type, 
                    sno, 
                    question,
                    testcases, 
                    expected_output, 
                    time_complexity
                ) VALUES (
                    '$exid',
                    '$q',
                    '$qstn_type',
                    '$i',
                    '$question',
                    '$testcases',
                    '$expected_output',
                    '$time_complexity'
                )";
        } else {
            // Handle multiple choice and single choice questions
            $a = isset($_POST['a' . $i]) ? mysqli_real_escape_string($conn, $_POST['a' . $i]) : null;
            
            // Initialize options to null
            $o1 = $o2 = $o3 = $o4 = null;

            // Check if options are set and assign them
            if (isset($_POST['o1' . $i])) {
                $o1 = mysqli_real_escape_string($conn, $_POST['o1' . $i]);
            }
            if (isset($_POST['o2' . $i])) {
                $o2 = mysqli_real_escape_string($conn, $_POST['o2' . $i]);
            }
            if (isset($_POST['o3' . $i])) {
                $o3 = mysqli_real_escape_string($conn, $_POST['o3' . $i]);
            }
            if (isset($_POST['o4' . $i])) {
                $o4 = mysqli_real_escape_string($conn, $_POST['o4' . $i]);
            }

            // Handle multiple-choice questions
            if ($qstn_type === 'multiple_choice' && $a !== null) {
                $a = implode(',', array_map('trim', explode(',', $a)));
            }

            $sql = "INSERT INTO qstn_list (
                    exid, 
                    qstn, 
                    qstn_o1, 
                    qstn_o2, 
                    qstn_o3, 
                    qstn_o4, 
                    qstn_ans, 
                    sno, 
                    qstn_type
                ) VALUES (
                    '$exid', 
                    '$q', 
                    '$o1', 
                    '$o2', 
                    '$o3', 
                    '$o4', 
                    '$a', 
                    '$i', 
                    '$qstn_type'
                )";
        }

        // Execute the query and handle any errors
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            error_log("MySQL Error for question $i: " . mysqli_error($conn));
            echo "<script>alert('Updating question $i failed: " . mysqli_error($conn) . "');</script>";
            header("Location: exams.php");
            exit();
        }
    }

    // All questions were added successfully
    header("Location: exams.php");
    exit();
}
?>