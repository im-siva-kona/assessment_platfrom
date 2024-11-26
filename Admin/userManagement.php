<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header("Location: ../login_Admin.php");
}

// Debug CSRF token handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

include '../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Function to fetch all users
function fetchUsers($conn) {
  $sql = "SELECT 
  id, 
  uname, 
  fname, 
  dob, 
  gender, 
  email, 
  subject,
  user_type
FROM (
  SELECT id, uname, fname, dob, gender, email, NULL as subject, 'student' as user_type 
  FROM student
  UNION ALL
  SELECT id, uname, fname, dob, gender, email, subject, 'teacher' as user_type 
  FROM teacher
  UNION ALL
  SELECT id, uname, fname, dob, gender, email, NULL as subject, 'admin' as user_type 
  FROM ADMIN
) users";
  
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
      error_log("Prepare failed: " . $conn->error);
      return [];
  }
  
  $stmt->execute();
  $result = $stmt->get_result();
  $users = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $users;
}

function fetchUser($conn, $id, $user_type) {
  $valid_tables = ['student' => 'student', 'teacher' => 'teacher', 'ADMIN' => 'ADMIN'];
  if (!isset($valid_tables[$user_type])) {
    throw new Exception("Invalid user type");
  }

  $table = $valid_tables[$user_type];
  $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
  if (!$stmt) {
      throw new Exception("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $stmt->close();

  if ($user) {
      $user['user_type'] = $user_type;
  }
  return $user;
}

// Function to add a new user with prepared statements
function addUser($conn, $table, $data) {
  $valid_tables = ['student', 'teacher', 'ADMIN'];
  if (!in_array($table, $valid_tables)) {
      throw new Exception("Invalid table name");
  }

  $columns = ['uname', 'pword', 'fname', 'dob', 'gender', 'email'];
  $values = ['?', '?', '?', '?', '?', '?'];
  $types = 'ssssss';
  $params = [
      $data['uname'],
      password_hash($data['pword'], PASSWORD_DEFAULT), // Use proper password hashing
      $data['fname'],
      $data['dob'],
      $data['gender'],
      $data['email']
  ];

  if ($table === 'teacher' && isset($data['subject'])) {
      $columns[] = 'subject';
      $values[] = '?';
      $types .= 's';
      $params[] = $data['subject'];
  }

  $sql = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ")";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
      throw new Exception("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param($types, ...$params);
  $success = $stmt->execute();
  $stmt->close();

  if (!$success) {
      throw new Exception("Error adding user: " . $conn->error);
  }
  return true;
}

// Function to update an existing user
function updateUser($conn, $table, $id, $data) {
  $valid_tables = ['student', 'teacher', 'ADMIN'];
  if (!in_array($table, $valid_tables)) {
      throw new Exception("Invalid table name");
  }

  $updates = [
      'uname = ?',
      'fname = ?',
      'dob = ?',
      'gender = ?',
      'email = ?'
  ];
  
  $params = [
      $data['uname'],
      $data['fname'],
      $data['dob'],
      $data['gender'],
      $data['email']
  ];
  $types = 'sssss';

  // Add password to update only if new password is provided
  if (!empty($data['pword'])) {
      $updates[] = 'pword = ?';
      $params[] = password_hash($data['pword'], PASSWORD_DEFAULT);
      $types .= 's';
  }

  // Add subject for teachers
  if ($table === 'teacher' && isset($data['subject'])) {
      $updates[] = 'subject = ?';
      $params[] = $data['subject'];
      $types .= 's';
  }

  // Add ID to params
  $params[] = $id;
  $types .= 'i';

  $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE id = ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
      throw new Exception("Prepare failed: " . $conn->error);
  }

  $stmt->bind_param($types, ...$params);
  $success = $stmt->execute();
  $stmt->close();

  if (!$success) {
      throw new Exception("Error updating user: " . $conn->error);
  }
  return true;
}

// Function to delete a user
function deleteUser($conn, $user_type, $id) {
  $valid_tables = ['student' => 'student', 'teacher' => 'teacher', 'admin' => 'ADMIN'];
    if (!isset($valid_tables[$user_type])) {
        throw new Exception("Invalid user type");
    }

    $table = $valid_tables[$user_type];
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

  $stmt->bind_param('i', $id);
  $success = $stmt->execute();
  $affected_rows = $stmt->affected_rows;  // Check if any rows were affected
  $stmt->close();

if (!$success) {
    throw new Exception("Error deleting user: " . $conn->error);
}
if ($affected_rows === 0) {
    throw new Exception("No user found with the specified ID");
}
return true;
}

// Debug POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  // Validate CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      die("CSRF token validation failed");
  }

  try {
      if (isset($_POST['delete_user'])) {
        if (!isset($_POST['id']) || !isset($_POST['user_type'])) {
            throw new Exception("Missing user ID or type for deletion");
        }
        
        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($id === false) {
            throw new Exception("Invalid user ID");
        }
        
        if (deleteUser($conn, $_POST['user_type'], $id)) {
            $success_message = "User deleted successfully";
            // Redirect to refresh the page and prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF'] . "?message=deleted");
            exit();
        }
    }
    if (isset($_POST['add_user'])) {
      // Validate input
      $required_fields = ['user_type', 'uname', 'pword', 'fname', 'dob', 'gender', 'email'];
      foreach ($required_fields as $field) {
          if (!isset($_POST[$field]) || ($_POST[$field] === '' && $field !== 'pword')) {
              throw new Exception("Missing required field: $field");
          }
      }

      $data = [
          'uname' => $_POST['uname'],
          'pword' => $_POST['pword'],
          'fname' => $_POST['fname'],
          'dob' => $_POST['dob'],
          'gender' => $_POST['gender'],
          'email' => $_POST['email'],
          'subject' => $_POST['subject'] ?? null
      ];

      if (isset($_POST['id'])) {
          updateUser($conn, $_POST['user_type'], $_POST['id'], $data);
      } else {
          addUser($conn, $_POST['user_type'], $data);
      }
      $success_message = "User operation completed successfully";
  }
    
  } catch (Exception $e) {
      $error_message = $e->getMessage();
      error_log($error_message);
  }
}

// Get user data for editing if ID is provided in GET request
$edit_user = null;
if (isset($_GET['edit']) && isset($_GET['type'])) {
    try {
        $edit_user = fetchUser($conn, $_GET['edit'], $_GET['type']);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        error_log($error_message);
    }
}

if (isset($_GET['message']) && $_GET['message'] === 'deleted') {
  $success_message = "User deleted successfully";
}

$users = fetchUsers($conn);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dash.css">
    <link rel="stylesheet" href="css/user.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <title>User Management</title>
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
          <a href="userManagement.php" class="active">
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
    <br><br>
    <div class="content">
            <h2>User Management</h2>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="user-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <?php if ($edit_user): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_user['id']); ?>">
            <?php endif; ?>
                <div class="form-group">
                    <label for="user_type">User Type:</label>
                    <select name="user_type" id="user_type" class="form-control" required>
                        <?php echo $edit_user ? 'disabled' : ''; ?>>
                        <option value="student" <?php echo ($edit_user && $edit_user['user_type'] == 'student') ? 'selected' : ''; ?>>Student</option>
                        <option value="teacher" <?php echo ($edit_user && $edit_user['user_type'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                        <option value="admin" <?php echo ($edit_user && $edit_user['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($edit_user['user_type']); ?>">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="uname">Username:</label>
                    <input type="text" name="uname" id="uname" class="form-control" required
                    value="<?php echo htmlspecialchars($edit_user['uname'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="pword">Password:</label>
                    <input type="password" name="pword" id="pword" class="form-control"
                    <?php echo $edit_user ? '' : 'required'; ?>>
                <?php if ($edit_user): ?>
                    <small class="form-text text-muted">Leave blank to keep current password</small>
                <?php endif; ?>
                </div>

                <div class="form-group">
                <label for="fname">Full Name:</label>
                <input type="text" name="fname" id="fname" class="form-control" required
                    value="<?php echo htmlspecialchars($edit_user['fname'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" class="form-control" required
                    value="<?php echo htmlspecialchars($edit_user['dob'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="M" <?php echo ($edit_user && $edit_user['gender'] == 'M') ? 'selected' : ''; ?>>Male</option>
                    <option value="F" <?php echo ($edit_user && $edit_user['gender'] == 'F') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required
                    value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>">
            </div>

            <div class="form-group" id="subject-field">
                <label for="subject">Subject (for Teachers):</label>
                <input type="text" name="subject" id="subject" class="form-control"
                    value="<?php echo htmlspecialchars($edit_user['subject'] ?? ''); ?>">
            </div>

            <button type="submit" name="add_user" class="btn btn-primary">
                <?php echo $edit_user ? 'Update User' : 'Add User'; ?>
            </button>
            
            <?php if ($edit_user): ?>
                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
            </form>

            <h3>User List</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                            <td><?php echo htmlspecialchars($user['uname']); ?></td>
                            <td><?php echo htmlspecialchars($user['fname']); ?></td>
                            <td><?php echo htmlspecialchars($user['dob']); ?></td>
                            <td><?php echo htmlspecialchars($user['gender']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['subject'] ?? ''); ?></td>
                            <td>
                            <a href="?edit=<?php echo htmlspecialchars($user['id']); ?>&type=<?php echo htmlspecialchars($user['user_type']); ?>" 
                               class="btn btn-primary">Edit</a>
                            
                            <!-- Updated delete form -->
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user['user_type']); ?>">
                                <button type="submit" 
                                        name="delete_user" 
                                        class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script>
        // Show/hide subject field based on user type
        document.getElementById('user_type').addEventListener('change', function() {
            const subjectField = document.getElementById('subject-field');
            subjectField.style.display = this.value === 'teacher' ? 'block' : 'none';
        });

        // Initialize subject field visibility
        document.getElementById('subject-field').style.display = 
            document.getElementById('user_type').value === 'teacher' ? 'block' : 'none';
    </script>
</body>
</html>