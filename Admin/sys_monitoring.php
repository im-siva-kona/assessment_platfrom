<?php 
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}
include '../config.php';
$uname=$_SESSION['uname'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Monitoring</title>
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
                <a href="Assessment_config.php">
                    <i class='bx bx-book-content' ></i>
                    <span class="links_name">Configuration</span>
                </a>
            </li>
            <li>
                <a href="sys_monitoring.php" class="active">
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
        <br>
        <h1>System Monitoring</h1>
        <br>
        <p>Yet to be created</p>
    </section>
</body>
</html>