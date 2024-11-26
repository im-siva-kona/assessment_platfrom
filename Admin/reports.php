<?php
session_start();
if (!isset($_SESSION["uname"])){
	header("Location: ../login_Admin.php");
}

include '../config.php';
error_reporting(0);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'generate_report':
                generateReport($conn);
                break;
            case 'compare_data':
                compareData($conn);
                break;
            case 'export_data':
                exportData($conn);
                break;
        }
    }
}

function generateReport($conn) {
    $metrics = isset($_POST['metrics']) ? $_POST['metrics'] : [];
    $filters = isset($_POST['filters']) ? $_POST['filters'] : [];

    if (empty($metrics)) {
        echo "<div class='alert alert-error'>Please select at least one metric. <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
        return;
    }

    $sql = "SELECT ";
    foreach ($metrics as $metric) {
        $sql .= "$metric, ";
    }
    $sql = rtrim($sql, ", ");
    $sql .= " FROM atmpt_list";

    $params = [];
    $paramTypes = "";
    $paramValues = [];

    if (!empty($filters)) {
        $sql .= " WHERE ";
        foreach ($filters as $filter) {
            $sql .= "$filter = ? AND ";
            $params[] = &$paramValues[$filter];
            $paramTypes .= "s";
        }
        $sql = rtrim($sql, " AND ");
    }

    $stmt = $conn->prepare($sql);
    if (!empty($filters)) {
        array_unshift($params, $paramTypes);
        call_user_func_array([$stmt, 'bind_param'], $params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // echo "<div class='table-container'>";
            // echo "<table>";
            // echo "<thead><tr>";
            foreach ($metrics as $metric) {
                // echo "<th>" . ucfirst($metric) . "</th>";
            }
            echo "</tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                // echo "<tr>";
                // foreach ($metrics as $metric) {
                    // echo "<td>" . htmlspecialchars($row[$metric]) . "</td>";
                // }
                // echo "</tr>";
            }
            // echo "</tbody></table></div>";
        } else {
            echo "<div class='alert alert-info'>No results found <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
        }
    } else {
        echo "<div class='alert alert-error'>Error executing the query: " . htmlspecialchars($stmt->error) . " <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
    }

    $stmt->close();
}


function compareData($conn) {
  $metric = $_POST['metric'];
  $value1 = $_POST['value1'];
  $value2 = $_POST['value2'];

  // Use more flexible matching
  $sql = "SELECT * FROM comparison_data WHERE metric_name = ? AND (value1 = ? OR value2 = ? OR value1 = ? OR value2 = ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $metric, $value1, $value2, $value2, $value1);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($stmt === false) {
    echo "Prepare failed: " . htmlspecialchars($conn->error);
    return null;
}

$stmt->execute();

if ($stmt->errno) {
    echo "Execute failed: " . htmlspecialchars($stmt->error);
    return null;
}

  if ($result->num_rows > 0) {
      // echo "<div class='table-container'>";
      // echo "<table>";
      // echo "<thead><tr><th>Metric</th><th>Value 1</th><th>Value 2</th></tr></thead><tbody>";
      while ($row = $result->fetch_assoc()) {
          // echo "<tr>";
          // echo "<td>" . htmlspecialchars($row['metric_name']) . "</td>";
          // echo "<td>" . htmlspecialchars($row['value1']) . "</td>";
          // echo "<td>" . htmlspecialchars($row['value2']) . "</td>";
          // echo "</tr>";
      }
      echo "</tbody></table></div>";
  } else {
      echo "<div class='alert alert-info' id='no-comparison-data'>No comparison data found <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i></div>";
  }
}

function exportData($conn) {
  $format = $_POST['format'];
  $sql = "SELECT * FROM atmpt_list";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      $data = [];
      while ($row = $result->fetch_assoc()) {
          $data[] = $row;
      }

      switch ($format) {
          case 'csv':
              header('Content-Type: text/csv');
              header('Content-Disposition: attachment; filename="data.csv"');
              $output = fopen('php://output', 'w');
              fputcsv($output, array_keys($data[0]));
              foreach ($data as $row) {
                  fputcsv($output, $row);
              }
              fclose($output);
              break;
              
          case 'json':
              header('Content-Type: application/json');
              header('Content-Disposition: attachment; filename="data.json"');
              echo json_encode($data, JSON_PRETTY_PRINT);
              break;
              
          case 'json_pdf':
              generateJsonPdf($data);
              break;
              
          case 'xml':
              header('Content-Type: application/xml');
              header('Content-Disposition: attachment; filename="data.xml"');
              
              // Create XML document
              $xml = new DOMDocument('1.0', 'UTF-8');
              $xml->formatOutput = true;
              
              // Create root element
              $root = $xml->createElement('records');
              $xml->appendChild($root);
              
              // Add each record
              foreach ($data as $row) {
                  $record = $xml->createElement('record');
                  foreach ($row as $key => $value) {
                      // Sanitize element name (replace invalid characters with underscore)
                      $elementName = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
                      if (is_numeric($elementName[0])) {
                          $elementName = 'field_' . $elementName;
                      }
                      
                      $element = $xml->createElement($elementName);
                      $element->appendChild($xml->createTextNode($value));
                      $record->appendChild($element);
                  }
                  $root->appendChild($record);
              }
              
              echo $xml->saveXML();
              break;
              
          case 'xml_pdf':
              generateXmlPdf($data);
              break;
      }
      exit;
  } else {
      echo "<div class='alert alert-error'>No data available for export <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
  }
}

function generateJsonPdf($data){
  require('fpdf/fpdf.php');
  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','B',16);
  $pdf->Cell(40,10,'JSON Data');
  $pdf->Ln();

  $pdf->SetFont('Arial','',12);
  foreach ($data as $row) {
      foreach ($row as $key => $value) {
          $pdf->Cell(40,10,$key . ': ' . $value);
          $pdf->Ln();
      }
      $pdf->Ln();
  }
  $pdf->Output('D', 'data.pdf');
}

function generateXmlPdf($data) {
  require('fpdf/fpdf.php');

  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 16);
  $pdf->Cell(40, 10, 'XML Data Report');
  $pdf->Ln();

  $pdf->SetFont('Arial', '', 12);
  foreach ($data as $row) {
      foreach ($row as $key => $value) {
          $pdf->Cell(40, 10, $key . ': ' . $value);
          $pdf->Ln();
      }
      $pdf->Ln();
  }

  $pdf->Output('D', 'data.pdf');
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <title>Advanced Reporting System</title>
    <link rel="stylesheet" href="css/reports.css">
    <link rel="stylesheet" href="css/dash.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
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
          <a href="reports.php" class="active">
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
    <div class="container">
        <h1>Reporting System</h1>
        
        <div class="tabs">
            <button class="tab active" onclick="showSection('reports')">Custom Reports</button>
            <button class="tab" onclick="showSection('analytics')">Real-Time Analytics</button>
            <button class="tab" onclick="showSection('comparison')">Comparative Analytics</button>
            <button class="tab" onclick="showSection('export')">Export Data</button>
        </div>

        <div id="reports" class="section active">
    <form action="" method="post">
        <input type="hidden" name="action" value="generate_report">
        
        <div class="form-group">
            <label>Select Metrics:</label>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" name="metrics[]" value="nq" id="nq">
                    <label for="nq">Number of Questions</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" name="metrics[]" value="cnq" id="cnq">
                    <label for="cnq">Correct Questions</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" name="metrics[]" value="ptg" id="ptg">
                    <label for="ptg">Percentage</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Select Filters:</label>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" name="filters[]" value="uname" id="uname">
                    <label for="uname">Username</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" name="filters[]" value="exid" id="exid">
                    <label for="exid">Exam ID</label>
                </div>
            </div>
        </div>

        <button type="submit">Generate Report</button>
    </form>

    <!-- Add a container for displaying report results -->
    <div class="report-results">
        <?php
        // Check if the form was submitted and action is generate_report
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'generate_report') {
            $metrics = isset($_POST['metrics']) ? $_POST['metrics'] : [];
            $filters = isset($_POST['filters']) ? $_POST['filters'] : [];

            if (empty($metrics)) {
                echo "<div class='alert alert-error'>Please select at least one metric. <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
            } else {
                $sql = "SELECT ";
                foreach ($metrics as $metric) {
                    $sql .= "$metric, ";
                }
                $sql = rtrim($sql, ", ");
                $sql .= " FROM atmpt_list";

                $params = [];
                $paramTypes = "";
                $paramValues = [];

                if (!empty($filters)) {
                    $sql .= " WHERE ";
                    foreach ($filters as $filter) {
                        $sql .= "$filter = ? AND ";
                        $params[] = &$paramValues[$filter];
                        $paramTypes .= "s";
                    }
                    $sql = rtrim($sql, " AND ");
                }

                $stmt = $conn->prepare($sql);
                if (!empty($filters)) {
                    array_unshift($params, $paramTypes);
                    call_user_func_array([$stmt, 'bind_param'], $params);
                }

                if ($stmt->execute()) {
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo "<div class='table-container'>";
                        echo "<table>";
                        echo "<thead><tr>";
                        foreach ($metrics as $metric) {
                            echo "<th>" . ucfirst($metric) . "</th>";
                        }
                        echo "</tr></thead><tbody>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($metrics as $metric) {
                                echo "<td>" . htmlspecialchars($row[$metric]) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table></div>";
                    } else {
                        echo "<div class='alert alert-info'>No results found <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
                    }
                } else {
                    echo "<div class='alert alert-error'>Error executing the query: " . htmlspecialchars($stmt->error) . " <i class='bx bx-message-x' onclick='removeMessage()' style='float:right;'></i> </div>";
                }

                $stmt->close();
            }
        }
        ?>
    </div>
</div>

        <div id="analytics" class="section">
            <h1>Real-Time Analytics Dashboard</h1>
            <div class="chart-container">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>
        <div id="comparison" class="section">
            <form action="" method="post">
                <input type="hidden" name="action" value="compare_data">
                
                <div class="form-group">
                    <label for="metric">Select Metric:</label>
                    <select name="metric" id="metric" required>
                        <option value="Number of questions">Number of Questions</option>
                        <option value="Correct Answers">Correctly Answered Questions</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="value1">Value 1:</label>
                    <input type="text" name="value1" id="value1" required>
                </div>

                <div class="form-group">
                    <label for="value2">Value 2:</label>
                    <input type="text" name="value2" id="value2" required>
                </div>

                <button type="submit">Compare Data</button>
            </form>
            <div class="comparison_result">
              <br>
              <h2>Comparison Results</h2>
              <div id="comparison-data">
                <table>
                  <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Value 1</th>
                        <th>Value 2</th>
                    </tr>
                </thead>
                <tbody id="comparison-results-body">
                  <?php
                  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'compare_data') {
                    $metric = $_POST['metric'];
                    $value1 = $_POST['value1'];
                    $value2 = $_POST['value2'];

                    $sql = "SELECT * FROM comparison_data WHERE metric_name = ? AND (value1 = ? OR value2 = ? OR value1 = ? OR value2 = ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssss", $metric, $value1, $value2, $value2, $value1);
                    $stmt->execute();
                    $result = $stmt->get_result();

                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['metric_name']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['value1']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['value2']) . "</td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='3'>No comparison data found</td></tr>";
                  }
                }
                ?>
                </tbody>
               </table>
            </div>
        </div>
      </div>
    </div>

    <div id="export" class="section" style="margin:50px;margin-top:10px">
        <form action="" method="post">
            <input type="hidden" name="action" value="export_data">
                
              <div class="form-group">
                  <label for="format">Select Format:</label>
                  <select name="format" id="format" required >
                        <option value="csv">CSV</option>
                        <option value="json">JSON</option>
                        <option value="xml">XML</option>
                  </select>
              </div>

              <button type="submit">Export Data</button>
          </form>
      </div>
  </div>

  <script>

   document.addEventListener('DOMContentLoaded', function() {
    const comparisonForm = document.querySelector('form[action=""][name="action"][value="compare_data"]');
    
    if (comparisonForm) {
        comparisonForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const metric = document.getElementById('metric');
            const value1 = document.getElementById('value1');
            const value2 = document.getElementById('value2');
            const comparisonResultsBody = document.getElementById('comparison-results-body');

            // Validate form inputs
            if (!metric.value || !value1.value || !value2.value) {
                alert('Please fill in all fields for comparison');
                return;
            }

            // Prepare form data for AJAX submission
            const formData = new FormData(this);
            
            // Send AJAX request
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                // Parse the returned HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Find the comparison results table body
                const newResultBody = doc.getElementById('comparison-results-body');
                
                if (newResultBody && newResultBody.innerHTML.trim() !== '') {
                    // Update the results table
                    comparisonResultsBody.innerHTML = newResultBody.innerHTML;
                } else {
                    // Show no data message
                    comparisonResultsBody.innerHTML = `
                        <tr>
                            <td colspan="3">
                                No comparison data found for 
                                Metric: ${metric.value}, 
                                Values: ${value1.value} and ${value2.value}
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Comparison Data Fetch Error:', error);
                comparisonResultsBody.innerHTML = `
                    <tr>
                        <td colspan="3">
                            Error fetching comparison data. 
                            Please try again or contact support.
                        </td>
                    </tr>
                `;
            });
        });
    }

    // Optional: Remove message function
    window.removeMessage = function() {
        const message = document.getElementById('no-comparison-data');
        if (message) {
            message.style.display = 'none';
        }
    };
});

    function removeMessage() {
      var message = document.getElementById('no-comparison-data');
      if (message) {
        message.style.display = 'none';
      }
    }
    function showSection(sectionId) {
            // Hide all sections
      document.querySelectorAll('.section').forEach(section => {
        section.classList.remove('active');
      });
            
            // Remove active class from all tabs
      document.querySelectorAll('.tab').forEach(tab => {
          tab.classList.remove('active');
      });
            
            // Show selected section
      document.getElementById(sectionId).classList.add('active');
            
            // Add active class to clicked tab
        event.target.classList.add('active');
      }

        // Real-time analytics update
        function fetchAnalyticsData() {
            fetch('fetch_analytics.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('analytics-data').innerHTML = JSON.stringify(data, null, 2);
                })
                .catch(error => console.error('Error fetching analytics:', error));
        }

        // Fetch analytics data every 5 seconds
        setInterval(fetchAnalyticsData, 5000);

        const colors = ['#2196F3', '#4CAF50', '#FFC107', '#E91E63', '#9C27B0'];
        let currentData = [];
        let metrics = new Set();

        function fetchData() {
            fetch('fetch_analytics.php')
                .then(response => response.json())
                .then(data => {
                    currentData = data;
                    metrics = new Set();
                    data.forEach(point => {
                        Object.keys(point).forEach(key => {
                            if (key !== 'timestamp') {
                                metrics.add(key);
                            }
                        });
                    });
                    updateChart();
                    updateLegend();
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        function updateChart() {
            const container = document.getElementById('dataPoints');
            container.innerHTML = '';

            const chartRect = document.querySelector('.chart').getBoundingClientRect();
            const width = chartRect.width - 60;
            const height = chartRect.height - 60;

            // Find min/max values
            let minValue = Infinity;
            let maxValue = -Infinity;
            currentData.forEach(point => {
                metrics.forEach(metric => {
                    if (point[metric] !== undefined) {
                        minValue = Math.min(minValue, point[metric]);
                        maxValue = Math.max(maxValue, point[metric]);
                    }
                });
            });

            // Add padding to min/max
            const valuePadding = (maxValue - minValue) * 0.1;
            minValue -= valuePadding;
            maxValue += valuePadding;

            // Update Y-axis labels
            const yLabels = document.querySelector('.y-labels');
            yLabels.innerHTML = '';
            const steps = 5;
            for (let i = steps; i >= 0; i--) {
                const value = minValue + (maxValue - minValue) * (i / steps);
                const label = document.createElement('div');
                label.textContent = value.toFixed(1);
                yLabels.appendChild(label);
            }

            // Update X-axis labels
            const xLabels = document.querySelector('.x-labels');
            xLabels.innerHTML = '';
            const timePoints = 6;
            for (let i = 0; i < timePoints; i++) {
                const timestamp = currentData[Math.floor(i * (currentData.length - 1) / (timePoints - 1))].timestamp;
                const label = document.createElement('div');
                label.textContent = new Date(timestamp).toLocaleTimeString();
                xLabels.appendChild(label);
            }

            // Plot data points and lines
            Array.from(metrics).forEach((metric, metricIndex) => {
                let lastX, lastY;

                currentData.forEach((point, index) => {
                    if (point[metric] === undefined) return;

                    const x = 50 + (index / (currentData.length - 1)) * width;
                    const y = 10 + height - ((point[metric] - minValue) / (maxValue - minValue)) * height;

                    // Draw point
                    const dot = document.createElement('div');
                    dot.className = 'metric-point';
                    dot.style.backgroundColor = colors[metricIndex % colors.length];
                    dot.style.left = `${x}px`;
                    dot.style.top = `${y}px`;
                    container.appendChild(dot);

                    // Draw line to previous point
                    if (lastX !== undefined && lastY !== undefined) {
                        const line = document.createElement('div');
                        line.className = 'metric-line';
                        line.style.backgroundColor = colors[metricIndex % colors.length];
                        line.style.width = `${x - lastX}px`;
                        line.style.transform = `rotate(${Math.atan2(y - lastY, x - lastX)}rad)`;
                        line.style.left = `${lastX}px`;
                        line.style.top = `${lastY}px`;
                        line.style.transformOrigin = '0 0';
                        container.appendChild(line);
                    }

                    lastX = x;
                    lastY = y;
                });
            });
        }

        function updateLegend() {
            const legend = document.getElementById('legend');
            legend.innerHTML = '';

            Array.from(metrics).forEach((metric, index) => {
                const item = document.createElement('div');
                item.className = 'legend-item';

                const color = document.createElement('div');
                color.className = 'legend-color';
                color.style.backgroundColor = colors[index % colors.length];

                const label = document.createElement('span');
                label.textContent = metric;

                item.appendChild(color);
                item.appendChild(label);
                legend.appendChild(item);
            });
        }

        // Initial fetch and set up polling
        fetchData();
        setInterval(fetchData, 5000);
    </script>
    <script src="../js/script.js"></script>
    </section>
</body>
</html>