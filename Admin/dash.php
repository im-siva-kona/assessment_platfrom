<?php 
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}
include '../config.php';
$uname=$_SESSION['uname'];

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
          <a href="#" class="active">
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
          <a href="Assessment_config.php">
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
        <!-- <li>
          <a href="message.php">
            <i class='bx bx-message' ></i>
            <span class="links_name">Messages</span>
          </a>
        </li> -->
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

    <div class="home-content">
    <div class="overview-boxes">
        <div class="box">
          <div class="right-side">
            <div class="box-topic">Exams</div>
            <div class="number"><?php  $sql="SELECT COUNT(1) FROM exm_list"; $result = mysqli_query($conn, $sql); $row=mysqli_fetch_array($result); echo $row['0'] ?></div>
            <div class="brief">
              <span class="text">Total number of exams</span>
            </div>
          </div>
          <i class='bx bx-user ico' ></i>
        </div>
        <div class="box">
          <div class="right-side">
            <div class="box-topic">Attempts</div>
            <div class="number"><?php  $sql="SELECT COUNT(1) FROM atmpt_list WHERE uname='$uname'"; $result = mysqli_query($conn, $sql); $row=mysqli_fetch_array($result); echo $row['0'] ?></div>
            <div class="brief">
              <span class="text">Total number of attempted exams</span>
            </div>
          </div>
          <i class='bx bx-book ico two' ></i>
        </div>
        <!-- <div class="box">
          <div class="right-side">
            <div class="box-topic">Results</div>
            <div class="number"><?php  $sql="SELECT COUNT(1) FROM atmpt_list"; $result = mysqli_query($conn, $sql); $row=mysqli_fetch_array($result); echo $row['0'] ?></div>
            <div class="brief">
              <span class="text">Number of available results</span>
            </div>
          </div>
          <i class='bx bx-line-chart ico three' ></i>
        </div> -->
        <div class="box">
          <div class="right-side">
            <div class="box-topic">Notifications</div>
            <div class="brief">
              <span class="text">Total number of messages recieved</span>
            </div>
          </div>
          <i class='bx bx-paper-plane ico four' ></i>
        </div>
      </div>
  </section>

<script src="../js/script.js"></script>


</body>
</html>

