<?php
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}

include '../config.php';
error_reporting(0);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dash.css">
    <link rel="stylesheet" href="css/help.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <title>Help</title>
</head>
<body>
    <div class="container">
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
          <a href="help.php" class="active">
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
        <div class="section">
            <ul>
                <li><a href="#" onclick="showSection('user-guide')">User Guide</a></li>
                <li><a href="#" onclick="showSection('faq')">FAQ</a></li>
                <li><a href="#" onclick="showSection('contact-support')">Contact Support</a></li>
            </ul>
        </div>

        <!-- User Guide Section -->
        <div id="user-guide" class="content-section">
            <h2>User Guide</h2>
            <p>Welcome to the user guide. Here you can find detailed instructions on how to use the platform.</p>
            <ul>
                <li><strong>Getting Started</strong>
                    <p>To get started, log in with your credentials. If you are a new user, you may need to register first.</p>
                </li>
                <li><strong>Managing Users</strong>
                    <p>Navigate to the User Management section to add, edit, or delete users. Ensure you have the necessary permissions to perform these actions.</p>
                </li>
                <li><strong>Scheduling Assessments</strong>
                    <p>In the Assessment Configuration section, you can create new assessments, set their parameters, and schedule them for specific times.</p>
                </li>
                <li><strong>Monitoring System Performance</strong>
                    <p>Use the System Monitoring dashboard to keep track of system performance, user activity, and other key metrics.</p>
                </li>
            </ul>
        </div>

        <!-- FAQ Section -->
        <div id="faq" class="content-section">
            <h2>Frequently Asked Questions</h2>
            <p>Find answers to common questions here.</p>
            <ul>
                <li><strong>How to create a new user?</strong>
                    <p>Go to the User Management section, click on "Add New User," fill in the required details, and save the new user profile.</p>
                </li>
                <li><strong>How to schedule an assessment?</strong>
                    <p>Navigate to the Assessment Configuration section, create a new assessment, set the start and end times, and save the schedule.</p>
                </li>
                <li><strong>How to monitor system performance?</strong>
                    <p>Access the System Monitoring dashboard to view real-time performance metrics, user activity, and other relevant data.</p>
                </li>
            </ul>
        </div>

        <!-- Contact Support Section -->
        <div id="contact-support" class="content-section">
            <h2>Contact Support</h2>
            <p>If you need further assistance, please contact our support team.</p>
            <ul>
                <li><strong>Email:</strong> <a href="mailto:support@example.com">support@example.com</a></li>
                <li><strong>Phone:</strong> +1-800-123-4567</li>
            </ul>
            <p>Our support team is available 24/7 to assist you with any issues or questions you may have.</p>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });

            // Show the selected section
            document.getElementById(sectionId).classList.add('active');
        }

        // Show the User Guide section by default
        document.addEventListener('DOMContentLoaded', function() {
            showSection('user-guide');
        });
    </script>
    <script src="../js/script.js"></script>
    </section>
</body>
</html>