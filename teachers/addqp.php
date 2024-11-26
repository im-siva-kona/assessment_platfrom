<?php 
error_reporting(0);
session_start();
if (!isset($_SESSION["fname"])){
	header("Location: ../login_teacher.php");
}
include('../config.php');
$exid = $_POST['exid'];
$nq = $_POST['nq'];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>Edit question paper</title>
    <link rel="stylesheet" href="css/dash.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
<body>
  <div class="sidebar">
    <div class="logo-details">
      <i class='bx bx-diamond'></i>
      <span class="logo_name">Welcome</span>
    </div>
      <ul class="nav-links">
        <li>
          <a href="dash.php">
            <i class='bx bx-grid-alt'></i>
            <span class="links_name">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="exams.php" class="active">
            <i class='bx bx-book-content' ></i>
            <span class="links_name">Exams</span>
          </a>
        </li>
        <li>
          <a href="results.php">
          <i class='bx bxs-bar-chart-alt-2'></i>
            <span class="links_name">Results</span>
          </a>
        </li>
        <li>
          <a href="records.php">
           <i class='bx bxs-user-circle'></i>
            <span class="links_name">Records</span>
          </a>
        </li>
        <li>
          <a href="messages.php">
            <i class='bx bx-message' ></i>
            <span class="links_name">Messages</span>
          </a>
        </li>
        <li>
         <a href="settings.php" >
            <i class='bx bx-cog' ></i>
            <span class="links_name">Settings</span>
          </a>
        </li>
        <li>
          <a href="help.php">
            <i class='bx bx-help-circle' ></i>
            <span class="links_name">Help</span>
          </a>
        </li>
        <li class="log_out">
           <a href="../logout.php">
            <i class='bx bx-log-out-circle' ></i>
            <span class="links_name">Log out</span>
          </a>
        </li>
      </ul>
  </div>
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class='bx bx-menu sidebarBtn'></i>
        <span class="dashboard">Teacher's Dashboard</span>
      </div>
    </nav>

    <div class="home-content">

      <div class="stat-boxes">
        <div class="recent-stat box" style="width:70%;">

          <div class="title">Add questions</div>
          <br><br>
          <form action="addexam.php" method="post">
            <?php 
            for($i = 1; $i <= $nq; $i++){
            echo'
            <input type="hidden" name="nq" value="' . $nq . '">
            <input type="hidden" name="exid" value="' . $exid . '">
            <label for="q' . $i . '"><b>Question number ' . $i . '</b></label><br><br>
            <label for="q' . $i . '">Enter the question:</label><br>
            <input class="inputbox" type="text" id="q' . $i . '" name="q' . $i . '" placeholder="Enter the question here" minlength ="4" maxlength="200" required /></br>
            <label for="qstn_type' . $i . '">Question Type:</label>
            <select id="qstn_type' . $i . '" name="qstn_type' . $i . '" onchange="updateQuestionFields(' . $i . ')">
              <option value="single_choice">Single Choice</option>
              <option value="multiple_choice">Multiple Choice</option>
               <option value="coding">Coding Question</option>
            </select><br><br>
            <div id="questionFields' . $i . '">
              <label for="o1' . $i . '">Option A:</label><br>
              <input class="inputbox" type="text" id="o1' . $i . '" name="o1' . $i . '" placeholder="Enter option A" minlength ="2" maxlength="100" required /></br>
              <label for="o2' . $i . '">Option B:</label><br>
              <input class="inputbox" type="text" id="o2' . $i . '" name="o2' . $i . '" placeholder="Enter option B" minlength ="2" maxlength="100" required /></br>
              <label for="o3' . $i . '">Option C:</label><br>
              <input class="inputbox" type="text" id="o3' . $i . '" name="o3' . $i . '" placeholder="Enter option C" minlength ="2" maxlength="100" required /></br>
              <label for="o4' . $i . '">Option D:</label><br>
              <input class="inputbox" type="text" id="o4' . $i . '" name="o4' . $i . '" placeholder="Enter option D" minlength ="2" maxlength="100" required /></br>
              <label for="a' . $i . '">Correct option:</label><br>
              <input class="inputbox" type="text" id="a' . $i . '" name="a' . $i . '" placeholder="Paste the correct answer here" minlength ="2" maxlength="100" required /></br>
            </div>
            <br><br> ';
            }
            ?>          
            <button type="submit" name="addqp" class="btn">Update</button>    
          </form>
        </div>
      </div>
    </div>
  </section>

<script src="../js/script.js"></script>
<script>
function updateQuestionFields(questionNumber) {
    const questionType = document.getElementById('qstn_type' + questionNumber).value;
    const questionFields = document.getElementById('questionFields' + questionNumber);

    if (questionType === 'coding') {
        questionFields.innerHTML = `
            <label for="problem_desc${questionNumber}">Problem Description:</label><br>
            <textarea class="inputbox" id="question${questionNumber}" name="question${questionNumber}" 
                placeholder="Enter detailed problem description" rows="4" maxlength="1000" required></textarea><br>

            <label for="testcases${questionNumber}">Test Cases:</label><br>
            <textarea class="inputbox" id="testcases${questionNumber}" name="testcases${questionNumber}" 
                placeholder="Enter test cases (one per line)" rows="4" maxlength="500" required></textarea><br>

            <label for="expected_output${questionNumber}">Expected Output:</label><br>
            <textarea class="inputbox" id="expected_output${questionNumber}" name="expected_output${questionNumber}" 
                placeholder="Enter expected output for each test case (one per line)" rows="4" maxlength="500" required></textarea><br>

            <label for="time_complexity${questionNumber}">Time Complexity:</label><br>
            <input class="inputbox" type="text" id="time_complexity${questionNumber}" 
                name="time_complexity${questionNumber}" placeholder="Enter expected time complexity (e.g., O(n))" 
                maxlength="50" required /><br>
        `;
    }
    else if (questionType === 'multiple_choice') {
        questionFields.innerHTML = `
            <label for="o1${questionNumber}">Option A:</label><br>
            <input class="inputbox" type="text" id="o1${questionNumber}" name="o1${questionNumber}" 
                placeholder="Enter option A" minlength="2" maxlength="100" required /></br>
            <label for="o2${questionNumber}">Option B:</label><br>
            <input class="inputbox" type="text" id="o2${questionNumber}" name="o2${questionNumber}" 
                placeholder="Enter option B" minlength="2" maxlength="100" required /></br>
            <label for="o3${questionNumber}">Option C:</label><br>
            <input class="inputbox" type="text" id="o3${questionNumber}" name="o3${questionNumber}" 
                placeholder="Enter option C" minlength="2" maxlength="100" required /></br>
            <label for="o4${questionNumber}">Option D:</label><br>
            <input class="inputbox" type="text" id="o4${questionNumber}" name="o4${questionNumber}" 
                placeholder="Enter option D" minlength="2" maxlength="100" required /></br>
            <label for="a${questionNumber}">Correct options:</label><br>
            <input class="inputbox" type="text" id="a${questionNumber}" name="a${questionNumber}" 
                placeholder="Enter correct answers separated by comma" minlength="2" maxlength="100" required /></br>
        `;
    }
    else if (questionType === 'single_choice') {
        questionFields.innerHTML = `
            <label for="o1${questionNumber}">Option A:</label><br>
            <input class="inputbox" type="text" id="o1${questionNumber}" name="o1${questionNumber}" 
                placeholder="Enter option A" minlength="2" maxlength="100" required /></br>
            <label for="o2${questionNumber}">Option B:</label><br>
            <input class="inputbox" type="text" id="o2${questionNumber}" name="o2${questionNumber}" 
                placeholder="Enter option B" minlength="2" maxlength="100" required /></br>
            <label for="o3${questionNumber}">Option C:</label><br>
            <input class="inputbox" type="text" id="o3${questionNumber}" name="o3${questionNumber}" 
                placeholder="Enter option C" minlength="2" maxlength="100" required /></br>
            <label for="o4${questionNumber}">Option D:</label><br>
            <input class="inputbox" type="text" id="o4${questionNumber}" name="o4${questionNumber}" 
                placeholder="Enter option D" minlength="2" maxlength="100" required /></br>
            <label for="a${questionNumber}">Correct option:</label><br>
            <input class="inputbox" type="text" id="a${questionNumber}" name="a${questionNumber}" 
                placeholder="Enter the correct option" minlength="2" maxlength="100" required /></br>
        `;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    for(let i = 1; i <= <?php echo $nq; ?>; i++) {
        updateQuestionFields(i);
    }
});
</script>


</body>
</html>

<?php
// In addexam.php
if (isset($_POST["addqp"])) {
    include('../config.php');
    $nq = mysqli_real_escape_string($conn, $_POST["nq"]);
    $exid = mysqli_real_escape_string($conn, $_POST["exid"]);
    
    for ($i = 1; $i <= $nq; $i++) {
        $q = mysqli_real_escape_string($conn, $_POST['q' . $i]);
        $qstn_type = isset($_POST['qstn_type' . $i]) ? mysqli_real_escape_string($conn, $_POST['qstn_type' . $i]) : '';
        
        if ($qstn_type === 'coding') {
            // Handle coding question with VARCHAR field lengths
            $problem_desc = isset($_POST['question' . $i]) ? 
                substr(mysqli_real_escape_string($conn, $_POST['question' . $i]), 0, 1000) : '';
            $testcases = isset($_POST['testcases' . $i]) ? 
                substr(mysqli_real_escape_string($conn, $_POST['testcases' . $i]), 0, 500) : '';
            $expected_output = isset($_POST['expected_output' . $i]) ? 
                substr(mysqli_real_escape_string($conn, $_POST['expected_output' . $i]), 0, 500) : '';
            $time_complexity = isset($_POST['time_complexity' . $i]) ? 
                substr(mysqli_real_escape_string($conn, $_POST['time_complexity' . $i]), 0, 50) : '';
            
            // Debug information
            error_log("Question $i Data:");
            error_log("Problem Description: " . $question);
            error_log("Test Cases: " . $testcases);
            error_log("Expected Output: " . $expected_output);
            error_log("Time Complexity: " . $time_complexity);
            
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
                
            // Debug SQL query
            error_log("SQL Query: " . $sql);
        } else {
            // Handle multiple/single choice questions
            $a = mysqli_real_escape_string($conn, $_POST['a' . $i]);
            $o1 = isset($_POST['o1' . $i]) ? mysqli_real_escape_string($conn, $_POST['o1' . $i]) : null;
            $o2 = isset($_POST['o2' . $i]) ? mysqli_real_escape_string($conn, $_POST['o2' . $i]) : null;
            $o3 = isset($_POST['o3' . $i]) ? mysqli_real_escape_string($conn, $_POST['o3' . $i]) : null;
            $o4 = isset($_POST['o4' . $i]) ? mysqli_real_escape_string($conn, $_POST['o4' . $i]) : null;

            if ($qstn_type === 'multiple_choice') {
                $a = implode(',', array_map('trim', explode(',', $a)));
            }

            $sql = "INSERT INTO qstn_list (exid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4, qstn_ans, sno, qstn_type) 
                    VALUES ('$exid', '$q', '$o1', '$o2', '$o3', '$o4', '$a', '$i', '$qstn_type')";
        }
        
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            error_log("MySQL Error: " . mysqli_error($conn));
            echo "<script>alert('Updating question $i failed: " . mysqli_error($conn) . "');</script>";
            exit();
        }
    }
    
    if ($result) {
        header("Location: exams.php");
        exit();
    } else {
        echo "<script>alert('Updating questions failed.');</script>";
        header("Location: exams.php");
        exit();
    }
}
?>