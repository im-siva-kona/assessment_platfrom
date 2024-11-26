<?php
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}
include '../config.php';
$uname=$_SESSION['uname'];

// Function to fetch all assessments
function getAllAssessments($conn) {
    $sql = "SELECT * FROM exm_list";
    $result = $conn->query($sql);
    $assessments = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $assessments[] = $row;
        }
    }
    return $assessments;
}

// Function to add a new assessment
function addAssessment($conn, $exname, $nq, $desp, $subt, $extime, $subject, $accessibility, $proctoring) {
    $sql = "INSERT INTO exm_list (exname, nq, desp, subt, extime, subject, accessibility, proctoring) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $exname, $nq, $desp, $subt, $extime, $subject, $accessibility, $proctoring);
    return $stmt->execute();
}

// Function to update an assessment
function updateAssessment($conn, $exid, $exname, $nq, $desp, $subt, $extime, $subject, $accessibility, $proctoring) {
    $sql = "UPDATE exm_list SET exname=?, nq=?, desp=?, subt=?, extime=?, subject=?, accessibility=?, proctoring=? WHERE exid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssssi", $exname, $nq, $desp, $subt, $extime, $subject, $accessibility, $proctoring, $exid);
    return $stmt->execute();
}

// Function to delete an assessment
function deleteAssessment($conn, $exid) {
    $sql = "DELETE FROM exm_list WHERE exid=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exid);
    return $stmt->execute();
}

// Function to handle bulk actions
function bulkAction($conn, $action, $exids) {
    foreach ($exids as $exid) {
        if ($action == 'archive') {
            $sql = "UPDATE exm_list SET status='archived' WHERE exid=?";
        } elseif ($action == 'delete') {
            $sql = "DELETE FROM exm_list WHERE exid=?";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $exid);
        $stmt->execute();
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_assessment'])) {
        $exname = $_POST['exname'];
        $nq = $_POST['nq'];
        $desp = $_POST['desp'];
        $subt = $_POST['subt'];
        $extime = $_POST['extime'];
        $subject = $_POST['subject'];
        $accessibility = $_POST['accessibility'];
        $proctoring = $_POST['proctoring'];
        addAssessment($conn, $exname, $nq, $desp, $subt, $extime, $subject, $accessibility, $proctoring);
    } elseif (isset($_POST['update_assessment'])) {
        $exid = $_POST['exid'];
        $exname = $_POST['exname'];
        $nq = $_POST['nq'];
        $desp = $_POST['desp'];
        $subt = $_POST['subt'];
        $extime = $_POST['extime'];
        $subject = $_POST['subject'];
        $accessibility = $_POST['accessibility'];
        $proctoring = $_POST['proctoring'];
        updateAssessment($conn, $exid, $exname, $nq, $desp, $subt, $extime, $subject, $accessibility, $proctoring);
    } elseif (isset($_POST['delete_assessment'])) {
        $exid = $_POST['exid'];
        deleteAssessment($conn, $exid);
    } elseif (isset($_POST['bulk_action'])) {
        $action = $_POST['bulk_action'];
        $exids = $_POST['exids'] ?? [];
        if (!empty($exids)) {
            bulkAction($conn, $action, $exids);
        }
    }
}

$assessments = getAllAssessments($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Configuration</title>
    <link rel="stylesheet" href="css/dash.css">
    <link rel="stylesheet" href="css/config.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
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
                    <i class="bx bx-grid-alt"></i>
                    <span class="links_name">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="userManagement.php">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">User Management</span>
                </a>
            </li>
            <li>
                <a href="Assessment_config.php" class="active">
                    <i class='bx bx-book-content' ></i>
                    <span class="links_name">Configuration</span>
                </a>
            </li>
            <li>
                <a href="sys_monitoring.php">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="links_name">System Monitoring</span>
                </a>
            </li>
            <li>
                <a href="reports.php">
                    <i class='bx bxs-report' ></i>
                    <span class="links_name">Reports</span>
                </a>
            </li>
            <li>
                <a href="settings.php">
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
                <a href="../logout_admin.php">
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
                <span class="dashboard">Admin Dashboard</span>
            </div>
            <div class="profile-details">
                <img src="<?php echo $_SESSION['img'];?>" alt="pro">
                <span class="admin_name"><?php echo $_SESSION['fname'];?></span>
            </div>
        </nav>
        <br><br><br>
        <div class="form-container">
            <h2>Add New Assessment</h2>
            <form method="post" action="">
                <input type="hidden" name="exid" id="exid">
                <input type="text" name="exname" id="exname" placeholder="Assessment Name" required>
                <input type="number" name="nq" id="nq" placeholder="Number of Questions" required>
                <textarea name="desp" id="desp" placeholder="Description" required></textarea>
                <input type="datetime-local" name="subt" id="subt" placeholder="Start Time" required>
                <input type="datetime-local" name="extime" id="extime" placeholder="End Time" required>
                <input type="text" name="subject" id="subject" placeholder="Subject" required>
                <textarea name="accessibility" id="accessibility" placeholder="Accessibility Settings"></textarea>
                <textarea name="proctoring" id="proctoring" placeholder="Proctoring Settings"></textarea>
                <button type="submit" name="add_assessment">Add Assessment</button>
                <button type="submit" name="update_assessment">Update Assessment</button>
            </form>
        </div>
        <h2>Existing Assessments</h2>
        <form method="post" action="">
            <select name="bulk_action">
                <option value="archive">Archive</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="bulk">Apply Bulk Action</button>
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Questions</th>
                        <th>Description</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Subject</th>
                        <th>Accessibility</th>
                        <th>Proctoring</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assessments as $assessment): ?>
                        <tr>
                            <td><input type="checkbox" name="exids[]" value="<?php echo $assessment['exid']; ?>"></td>
                            <td><?php echo $assessment['exid']; ?></td>
                            <td><?php echo $assessment['exname']; ?></td>
                            <td><?php echo $assessment['nq']; ?></td>
                            <td><?php echo $assessment['desp']; ?></td>
                            <td><?php echo $assessment['subt']; ?></td>
                            <td><?php echo $assessment['extime']; ?></td>
                            <td><?php echo $assessment['subject']; ?></td>
                            <td><?php echo $assessment['accessibility']; ?></td>
                            <td><?php echo $assessment['proctoring']; ?></td>
                            <td>
                                <button type="button" onclick="populateForm(<?php echo $assessment['exid']; ?>, '<?php echo $assessment['exname']; ?>', <?php echo $assessment['nq']; ?>, '<?php echo $assessment['desp']; ?>', '<?php echo $assessment['subt']; ?>', '<?php echo $assessment['extime']; ?>', '<?php echo $assessment['subject']; ?>', '<?php echo $assessment['accessibility']; ?>', '<?php echo $assessment['proctoring']; ?>')">Update</button>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="exid" value="<?php echo $assessment['exid']; ?>">
                                    <button type="submit" name="delete_assessment">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </section>

    <script>
        function populateForm(exid, exname, nq, desp, subt, extime, subject, accessibility, proctoring) {
            document.getElementById('exid').value = exid;
            document.getElementById('exname').value = exname;
            document.getElementById('nq').value = nq;
            document.getElementById('desp').value = desp;
            document.getElementById('subt').value = subt;
            document.getElementById('extime').value = extime;
            document.getElementById('subject').value = subject;
            document.getElementById('accessibility').value = accessibility;
            document.getElementById('proctoring').value = proctoring;
        }

        document.getElementById('select_all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('input[name="exids[]"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = this.checked;
            }, this);
        });
    </script>
    <script src="../js/script.js"></script>
</body>
</html>