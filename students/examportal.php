<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
if (!isset($_SESSION["uname"])) {
  header("Location: ../login_student.php");
}

include '../config.php';
error_reporting(0);
$exid = $_POST['exid'];

if (!isset($_POST["edit_btn"])) {
  header("Location: exams.php");
}

if (isset($_POST["edit_btn"])) {
  $sql = "SELECT * FROM exm_list WHERE exid='$exid'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $ogtime = $row['extime'];
  $subt = $row['subt'];
  $cmtime = date("Y-m-d H:i:s");

  $letters = array('-', ' ', ':');
  $ogtime = str_replace($letters, '', $ogtime);
  $cmtime = str_replace($letters, '', $cmtime);
  if ($ogtime > $cmtime) {
    header("Location: exams.php");
  }
  if ($cmtime > $subt) {
    echo "<script>st();</script>";
  }
}

$sql = "SELECT qid, qstn, qstn_o1, qstn_o2, qstn_o3, qstn_o4, qstn_type, question, Testcases, TIME_COMPLEXITY, EXPECTED_OUTPUT FROM qstn_list WHERE exid='$exid'";
$result = mysqli_query($conn, $sql);

$details = "SELECT * FROM exm_list WHERE exid='$exid'";
$res = mysqli_query($conn, $details);
while ($rowd = mysqli_fetch_array($res)) {
  $nq = $rowd['nq'];
  $exname = $rowd['exname'];
  $desp = $rowd['desp'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Exams</title>
  <link rel="stylesheet" href="css/dash.css">
  <link rel="stylesheet" href="css/examportal_styles.css">
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
  $td = $subt;
  ?>
  <script type="text/javascript">
    function st() {
      document.getElementById("form1").submit();
    }
    //set the date we are counting down to 
    var count_id = "<?php echo $td; ?>";
    var countDownDate = new Date(count_id).getTime();
    //Update the count down every 1 second 
    var x = setInterval(function() {
      //Get today's date and time 
      var now = new Date().getTime();
      //Find the distance between now and the count down date 
      var distance = countDownDate - now;
      //Time calculations for days, hours, minutes and seconds 
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);
      document.getElementById("time").innerHTML = "Timer: " + hours + "h " + minutes + "m " + seconds + "s";
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("form1").submit();
      }
    }, 1000);
  </script>
</head>

<body>
  <div class="sidebar active">
    <div class="logo-details">
      <i class='bx bx-diamond'></i>
      <span class="logo_name">Welcome</span>
    </div>
    <ul class="nav-links">
      <li>
        <a>
          <i class='bx bx-grid-alt'></i>
          <span class="links_name">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="exams.php" class="active">
          <i class='bx bx-book-content'></i>
          <span class="links_name">Exams</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bxs-bar-chart-alt-2'></i>
          <span class="links_name">Results</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bx-message'></i>
          <span class="links_name">Messages</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bx-cog'></i>
          <span class="links_name">Settings</span>
        </a>
      </li>
      <li>
        <a>
          <i class='bx bx-help-circle'></i>
          <span class="links_name">Help</span>
        </a>
      </li>
      <li class="log_out">
        <a>
          <i class='bx bx-log-out-circle'></i>
          <span class="links_name">Log out</span>
        </a>
      </li>
    </ul>
  </div>
  <section class="home-section">
    <nav>
      <div class="sidebar-button">
        <i class='bx bx-menu-alt-right sidebarBtn'></i>
        <span class="dashboard">Student Dashboard</span>
      </div>
    </nav>

    <div class="home-content">
      <div class="stat-b
      oxes">
        <div class="recent-stat box" style="width: 100%;">
          <div>
            <h3>Exam name: <?php echo $exname ?><?php echo '
          <p id="time"style="float:right"></p>'; ?></h3>
          </div>
          <span style="font-size: 17px;">Description: <?php echo $desp ?></span>
          <br><br><br>
          <form action="submit.php" id="form1" method="post">
            <div class="radio-container">
              <?php
              if (mysqli_num_rows($result) > 0) {
                $i = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                  echo '
                  <div class="question-container" id="question' . $i . '">
                    <input type="hidden" name="qid' . $i . '" value="' . $row['qid'] . '">
                    <div class="question-header">
                      <span class="question-number">Q' . $i . '.</span>
                      <span class="question-text">' . $row['qstn'] . '</span>
                    </div>';
                   
if ($row['qstn_type'] == 'coding') {
    echo '
    <div class="grid">
        <div class="left-column">
            <div class="problem-section">
                <h3>Problem Name</h3>
                <p>' . htmlspecialchars($row['qstn']) . '</p>
            </div>
            <div class="problem-section">
                <h4>Problem Statement</h4>
                <div class="prose">' . htmlspecialchars($row['question']) . '</div>
            </div>
            <div class="test-cases-section">
                <h4>Sample Test Cases</h4>
                <pre>' . htmlspecialchars($row['Testcases']) . '</pre>
            </div>
            <div class="complexity-section">
                <h4>Time Complexity</h4>
                <div class="prose">' . htmlspecialchars($row['TIME_COMPLEXITY']) . '</div>
            </div>
            <div class="output-section">
                <h4>Expected Output</h4>
                <pre>' . htmlspecialchars($row['EXPECTED_OUTPUT']) . '</pre>
            </div>
        </div>
        <div class="right-column">
            <textarea 
                id="codeEditor' . $i . '" 
                name="code' . $i . '" 
                class="code-editor"
                data-question-number="' . $i . '"
                rows="10" 
                cols="50"
            ></textarea>
            <button type="button" class="test-code-btn" data-editor="codeEditor' . $i . '">Test Code</button>
            <pre id="output' . $i . '" class="code-output"></pre>
            <input type="hidden" name="code_language' . $i . '" value="python3">
        </div>
    </div>';
}
 else if($row['qstn_type'] == 'single_choice') {
                    echo '<div class="options-container single-choice">';
                    $options = array($row['qstn_o1'], $row['qstn_o2'], $row['qstn_o3'], $row['qstn_o4']);
                    foreach ($options as $index => $option) {
                      echo '
                      <div class="option-item">
                        <input type="radio" id="o' . ($index + 1) . $i . '" name="o' . $i . '" value="' . $option . '" />
                        <label class="option-label" for="o' . ($index + 1) . $i . '">
                          <span class="option-text">' . $option . '</span>
                        </label>
                      </div>';
                    }
                    echo '</div>';
                  } 
                  else if ($row['qstn_type'] == 'multiple_choice') {
                    echo '<div class="options-container multiple-choice">';
                    $options = array($row['qstn_o1'], $row['qstn_o2'], $row['qstn_o3'], $row['qstn_o4']);
                    foreach ($options as $index => $option) {
                      echo '
                      <div class="option-item">
                        <input type="checkbox" id="o' . ($index + 1) . $i . '" name="o' . $i . '[]" value="' . $option . '" />
                        <label class="option-label" for="o' . ($index + 1) . $i . '">
                          <span class="option-text">' . $option . '</span>
                        </label>
                      </div>';
                    }
                    echo '</div>';
                  }
                  echo '</div>';
                  $i++;
                }
              }
              ?>
            </div>
            <div class="button-container">
              <input type="hidden" name="exid" value="<?php echo $exid; ?>" />
              <input type="hidden" name="nq" value="<?php echo $nq; ?>" />
              <button type="reset" class="rbtn">Reset all</button>
              <button type="button" class="prev-btn" onclick="prevQuestion()">Previous</button>
              <button type="button" class="next-btn" onclick="nextQuestion()">Next</button>
              <input type="submit" name="ans_sub" value="Submit" class="btn" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <div class="full-screen-modal" id="fullScreenModal">
    <div class="modal-content">
        <div class="warning-icon">⚠️</div>
        <h2>Exam Guidelines</h2>
        <p>Please read the following instructions carefully before starting the exam:</p>
        <ul>
            <li>You must remain in full-screen mode throughout the exam</li>
            <li>Switching tabs or windows is not permitted</li>
            <li>The exam will auto-submit if you exit full-screen mode</li>
            <li>Right-clicking and keyboard shortcuts are disabled</li>
            <li>Ensure you have a stable internet connection</li>
        </ul>
        <button id="startFullScreenBtn">Start Exam in Full Screen</button>
    </div>
  </div>
  <script>
    var inputs = document.querySelectorAll("input[type=radio]:checked"),
      x = inputs.length;
    document.querySelector("button[type=reset]").addEventListener("click", function(event) {
      const inputs = document.querySelectorAll("input[type=radio]:checked, input[type=checkbox]:checked");
    inputs.forEach(input => input.checked = false);
    });

    var currentQuestion = 1;
    var totalQuestions = <?php echo $nq; ?>;

    function showQuestion(questionNumber) {
      document.querySelectorAll('.question-container').forEach(function(question) {
        question.style.display = 'none';
      });
      document.getElementById('question' + questionNumber).style.display = 'block';
    }

    function nextQuestion() {
      if (currentQuestion < totalQuestions) {
        currentQuestion++;
        showQuestion(currentQuestion);
      }
    }

    function prevQuestion() {
      if (currentQuestion > 1) {
        currentQuestion--;
        showQuestion(currentQuestion);
      }
    }

    // Initialize by showing the first question
    showQuestion(currentQuestion);

    function showModal(){
      document.getElementById('fullScreenModal').style.display = 'flex';
    }

    function hideModal(){
      document.getElementById('fullScreenModal').style.display = 'none';
    }

    function enterFullScreen(){
      const elem = document.documentElement;
      if(elem.requestFullscreen){
        elem.requestFullscreen();
      } else if(elem.mozRequestFullScreen){
        elem.mozRequestFullScreen();
      } else if(elem.webkitRequestFullscreen){
        elem.webkitRequestFullscreen();
      } else if(elem.msRequestFullscreen){
        elem.msRequestFullscreen();
      }
    }
    // Show the modal when the page loads
    document.addEventListener('DOMContentLoaded', function() {
      showModal();
      const startFullScreenBtn = document.getElementById('startFullScreenBtn');
      let tabSwitchCount = 0;
      startFullScreenBtn.addEventListener('click', function() {

        enterFullScreen();
        hideModal();
        showQuestion(1);
        startFullScreenBtn.textContent = "Start Exam in full screen";
      });

      function handleFullScreenChange(){
        if(!document.fullscreenElement && 
          !document.mozFullScreenElement && 
          !document.webkitFullscreenElement && 
          !document.msFullscreenElement){
          alert("Warning: Exiting full-screen mode during exam is not allowed!");
          showModal();
          startFullScreenBtn.textContent = "Please Enter into Full screen";
        }
      }

      // Add full-screen change event listeners
      document.addEventListener('fullscreenchange', handleFullScreenChange);
      document.addEventListener('mozfullscreenchange', handleFullScreenChange);
      document.addEventListener('webkitfullscreenchange', handleFullScreenChange);
      document.addEventListener('MSFullscreenChange', handleFullScreenChange);

      // Prevent tab switching
      document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'hidden') {
          showModal();
          if (tabSwitchCount > 2) {
          alert("You have switched tabs more than 2 times. Submitting the exam automatically");
          document.getElementById('form1').submit();
        }
        else{
          showModal();
          startFullScreenBtn.textContent = "Please Enter into Full screen";
        }
          tabSwitchCount++;
        }
      });

      // Disable right-click
      document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
      });

      // Disable keyboard shortcuts
      document.addEventListener('keydown', function(e) {
        if ((e.altKey && e.key === 'Tab') ||
          (e.key === 'Meta') ||
          (e.altKey && e.key === 'F4') ||
          (e.ctrlKey && e.key === 'w')) {
          e.preventDefault();
          showModal();
          startFullScreenBtn.textContent = "Please Enter into Full screen";
        }
      });
    });
    document.addEventListener('DOMContentLoaded', function() {
    const codeEditor = document.getElementById('codeEditor');
    const executeBtn = document.getElementById('executeCodeBtn');
    const outputArea = document.getElementById('output');

    executeBtn.addEventListener('click', function() {
        // Show loading state
        executeBtn.disabled = true;
        executeBtn.textContent = 'Executing...';
        outputArea.textContent = 'Running code...';

        const script = codeEditor.value;
        const language = 'python3'; // You can make this dynamic based on question requirements

        fetch('jdoodle_execute.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                script: script,
                language: language
            })
        })
        .then(response => response.json())
        .then(data => {
            executeBtn.disabled = false;
            executeBtn.textContent = 'Execute Code';
            
            if (data.error) {
                outputArea.textContent = `Error: ${data.error}`;
                outputArea.style.color = '#d32f2f';
            } else {
                outputArea.textContent = data.output || data.result;
                outputArea.style.color = '#000';
            }
        })
        .catch((error) => {
            executeBtn.disabled = false;
            executeBtn.textContent = 'Execute Code';
            outputArea.textContent = `Error: ${error.message}`;
            outputArea.style.color = '#d32f2f';
            console.error('Error:', error);
        });
    });

    // Add keyboard shortcut (Ctrl/Cmd + Enter) to execute code
    codeEditor.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            executeBtn.click();
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle all test code buttons
    document.querySelectorAll('.test-code-btn').forEach(button => {
        button.addEventListener('click', function() {
            const editorId = this.dataset.editor;
            const editor = document.getElementById(editorId);
            const questionNumber = editor.dataset.questionNumber;
            const outputElement = document.getElementById('output' + questionNumber);
            
            // Show loading state
            this.disabled = true;
            this.textContent = 'Running...';
            outputElement.textContent = 'Executing code...';

            fetch('jdoodle_execute.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    script: editor.value,
                    language: 'python3'
                })
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                this.textContent = 'Test Code';
                
                if (data.error) {
                    outputElement.textContent = `Error: ${data.error}`;
                    outputElement.classList.add('error');
                } else {
                    outputElement.textContent = data.output;
                    outputElement.classList.remove('error');
                }
            })
            .catch((error) => {
                this.disabled = false;
                this.textContent = 'Test Code';
                outputElement.textContent = `Error: ${error.message}`;
                outputElement.classList.add('error');
                console.error('Error:', error);
            });
        });
    });

    // Add form submission handler
    document.getElementById('form1').addEventListener('submit', function(e) {
        // Save all code editor contents to their respective hidden fields
        document.querySelectorAll('.code-editor').forEach(editor => {
            const questionNumber = editor.dataset.questionNumber;
            if (!editor.value.trim()) {
                editor.value = '# No code submitted';
            }
        });
    });
});
  </script>
</body>

</html>