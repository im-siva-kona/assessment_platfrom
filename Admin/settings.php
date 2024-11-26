<?php
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}

include '../config.php';
error_reporting(0);

// Function to update system settings
function updateSettings($conn, $system_name, $admin_password, $admin_email) {
    $admin_password = md5($admin_password); // Encrypt password
    $query = "UPDATE admin SET uname='$system_name', pword='$admin_password', email='$admin_email' WHERE id=1";
    return mysqli_query($conn, $query);
}

// Function to update user roles and permissions
function updateUserRoles($conn, $roles) {
    foreach ($roles as $role => $permissions) {
        $permissions = implode(',', $permissions);
        $query = "UPDATE user_roles SET permissions='$permissions' WHERE role='$role'";
        mysqli_query($conn, $query);
    }
}

// Function to update API settings
function updateApiSettings($conn, $api_key, $api_secret) {
    $query = "UPDATE api_settings SET api_key='$api_key', api_secret='$api_secret' WHERE id=1";
    return mysqli_query($conn, $query);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_settings'])) {
        $system_name = $_POST['system_name'];
        $admin_password = $_POST['admin_password'];
        $admin_email = $_POST['admin_email'];
        updateSettings($conn, $system_name, $admin_password, $admin_email);
    }

    if (isset($_POST['save_roles'])) {
        $roles = $_POST['roles'];
        updateUserRoles($conn, $roles);
    }

    if (isset($_POST['save_api_settings'])) {
        $api_key = $_POST['api_key'];
        $api_secret = $_POST['api_secret'];
        updateApiSettings($conn, $api_key, $api_secret);
    }
}

// Fetch current settings
$system_settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admin WHERE id=1"));
$user_roles = [];
$result = mysqli_query($conn, "SELECT * FROM user_roles");
while ($row = mysqli_fetch_assoc($result)) {
    $user_roles[$row['role']] = explode(',', $row['permissions']);
}
$api_settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM api_settings WHERE id=1"));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dash.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/settings.css">
    <title>Settings</title>
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
          <a href="settings.php" class="active">
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
    <br><br>
    <div class="container">
      <br>
        <!-- System Settings Section -->
        <div class="section">
            <h2>System Settings</h2>
            <p>Manage your system settings here.</p>
            <form method="post">
                <input type="text" name="system_name" placeholder="System Name" value="<?php echo $system_settings['uname']; ?>" required>
                <input type="password" name="admin_password" placeholder="Admin Password" required>
                <input type="email" name="admin_email" placeholder="Admin Email" value="<?php echo $system_settings['email']; ?>" required>
                <button type="submit" name="save_settings">Save Settings</button>
            </form>
        </div>

        <!-- User Roles and Permissions Section -->
        <div class="section">
            <h2>User Roles and Permissions</h2>
            <p>Manage user roles and permissions here.</p>
            <form method="post">
                <?php foreach ($user_roles as $role => $permissions): ?>
                    <h3><?php echo ucfirst($role); ?> Role</h3>
                    <div class="permissions">
                        <label><input type="checkbox" name="roles[<?php echo $role; ?>][]" value="view_dashboard" <?php echo in_array('view_dashboard', $permissions) ? 'checked' : ''; ?>> View Dashboard</label>
                        <label><input type="checkbox" name="roles[<?php echo $role; ?>][]" value="manage_users" <?php echo in_array('manage_users', $permissions) ? 'checked' : ''; ?>> Manage Users</label>
                        <label><input type="checkbox" name="roles[<?php echo $role; ?>][]" value="manage_assessments" <?php echo in_array('manage_assessments', $permissions) ? 'checked' : ''; ?>> Manage Assessments</label>
                        <label><input type="checkbox" name="roles[<?php echo $role; ?>][]" value="view_reports" <?php echo in_array('view_reports', $permissions) ? 'checked' : ''; ?>> View Reports</label>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="save_roles">Save Roles</button>
            </form>
        </div>

        <!-- Integration and API Settings Section -->
        <div class="section">
            <h2>Integration and API Settings</h2>
            <p>Manage integrations and API configurations here.</p>
            <form method="post">
                <input type="text" name="api_key" placeholder="API Key" value="<?php echo $api_settings['api_key']; ?>" required>
                <input type="text" name="api_secret" placeholder="API Secret" value="<?php echo $api_settings['api_secret']; ?>" required>
                <button type="submit" name="save_api_settings">Save API Settings</button>
            </form>
        </div>
    </div>
</div>
</section>

<script src="../js/script.js"></script>
</body>
</html>