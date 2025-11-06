<?php
/**
 * Installation Script
 * Helps set up the School Management System
 * 
 * WARNING: Delete this file after installation for security
 */

// Start session for installation process
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent running if already installed (check for connection.php)
if (file_exists(__DIR__ . '/database/connection.php')) {
    $connTest = @include __DIR__ . '/database/connection.php';
    if ($connTest) {
        $testConn = @getDBConnection();
        if ($testConn) {
            die('System appears to be already installed. If you need to reinstall, please delete database/connection.php first.');
        }
    }
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 1) {
        // Database configuration
        $host = trim($_POST['db_host'] ?? 'localhost');
        $user = trim($_POST['db_user'] ?? 'root');
        $pass = trim($_POST['db_pass'] ?? '');
        $name = trim($_POST['db_name'] ?? 'school_management');
        
        // Test database connection
        try {
            $testConn = @new mysqli($host, $user, $pass);
            if ($testConn->connect_error) {
                $error = 'Database connection failed: ' . $testConn->connect_error;
            } else {
                // Create database if it doesn't exist
                $testConn->query("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $testConn->close();
                
                // Save configuration
                $configContent = "<?php\n";
                $configContent .= "define('DB_HOST', '" . addslashes($host) . "');\n";
                $configContent .= "define('DB_USER', '" . addslashes($user) . "');\n";
                $configContent .= "define('DB_PASS', '" . addslashes($pass) . "');\n";
                $configContent .= "define('DB_NAME', '" . addslashes($name) . "');\n\n";
                $configContent .= "function getDBConnection() {\n";
                $configContent .= "    \$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);\n";
                $configContent .= "    if (\$conn->connect_error) {\n";
                $configContent .= "        die(\"Connection failed: \" . \$conn->connect_error);\n";
                $configContent .= "    }\n";
                $configContent .= "    \$conn->set_charset(\"utf8mb4\");\n";
                $configContent .= "    return \$conn;\n";
                $configContent .= "}\n\n";
                $configContent .= "function closeDBConnection(\$conn) {\n";
                $configContent .= "    if (\$conn) {\n";
                $configContent .= "        \$conn->close();\n";
                $configContent .= "    }\n";
                $configContent .= "}\n";
                $configContent .= "?>\n";
                
                file_put_contents(__DIR__ . '/database/connection.php', $configContent);
                
                // Store for next step
                $_SESSION['db_config'] = ['host' => $host, 'user' => $user, 'pass' => $pass, 'name' => $name];
                header('Location: install.php?step=2');
                exit();
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Import database schema
        if (!isset($_SESSION['db_config'])) {
            $error = 'Database configuration not found. Please start over.';
        } else {
            $config = $_SESSION['db_config'];
            require_once __DIR__ . '/database/connection.php';
            
            $schemaFile = __DIR__ . '/database/schema.sql';
            if (!file_exists($schemaFile)) {
                $error = 'Schema file not found: ' . $schemaFile;
            } else {
                $sql = file_get_contents($schemaFile);
                $conn = getDBConnection();
                
                // Execute SQL statements
                if ($conn->multi_query($sql)) {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->next_result());
                    
                    $success = 'Database schema imported successfully!';
                    header('Location: install.php?step=3');
                    exit();
                } else {
                    $error = 'Error importing schema: ' . $conn->error;
                }
                closeDBConnection($conn);
            }
        }
    } elseif ($step == 3) {
        // Create admin user
        $username = trim($_POST['admin_username'] ?? 'admin');
        $password = trim($_POST['admin_password'] ?? '');
        $confirmPassword = trim($_POST['admin_password_confirm'] ?? '');
        
        if (empty($username) || empty($password)) {
            $error = 'Please fill in all fields';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long';
        } else {
            require_once __DIR__ . '/database/connection.php';
            $conn = getDBConnection();
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
            $stmt->bind_param("ss", $username, $hashedPassword);
            
            if ($stmt->execute()) {
                $success = 'Admin user created successfully!';
                // Import sample data if requested
                if (isset($_POST['import_sample_data']) && $_POST['import_sample_data'] == '1') {
                    $sampleFile = __DIR__ . '/database/sample_data.sql';
                    if (file_exists($sampleFile)) {
                        $sampleSql = file_get_contents($sampleFile);
                        $conn->multi_query($sampleSql);
                        do {
                            if ($result = $conn->store_result()) {
                                $result->free();
                            }
                        } while ($conn->next_result());
                    }
                }
                $stmt->close();
                closeDBConnection($conn);
                
                // Delete install.php for security
                if (isset($_POST['delete_install']) && $_POST['delete_install'] == '1') {
                    @unlink(__FILE__);
                }
                
                header('Location: install.php?step=4');
                exit();
            } else {
                $error = 'Error creating admin user: ' . $stmt->error;
            }
            $stmt->close();
            closeDBConnection($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - School Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .install-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .install-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        .install-step {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            position: relative;
        }
        .install-step.active {
            font-weight: 600;
            color: var(--primary-color);
        }
        .install-step.completed {
            color: var(--success-color);
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="card">
            <h1 class="card-title" style="text-align: center; margin-bottom: 2rem;">Installation Wizard</h1>
            
            <div class="install-progress">
                <div class="install-step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                    1. Database
                </div>
                <div class="install-step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                    2. Schema
                </div>
                <div class="install-step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                    3. Admin User
                </div>
                <div class="install-step <?php echo $step >= 4 ? 'active' : ''; ?>">
                    4. Complete
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($step == 1): ?>
                <form method="POST" action="">
                    <h2>Step 1: Database Configuration</h2>
                    <p>Enter your MySQL database credentials:</p>
                    
                    <div class="form-group">
                        <label class="form-label">Database Host *</label>
                        <input type="text" name="db_host" class="form-control" value="localhost" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database User *</label>
                        <input type="text" name="db_user" class="form-control" value="root" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database Password</label>
                        <input type="password" name="db_pass" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database Name *</label>
                        <input type="text" name="db_name" class="form-control" value="school_management" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Test Connection & Continue</button>
                    </div>
                </form>
            <?php elseif ($step == 2): ?>
                <form method="POST" action="">
                    <h2>Step 2: Import Database Schema</h2>
                    <p>Click the button below to import the database schema:</p>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Import Schema</button>
                    </div>
                </form>
            <?php elseif ($step == 3): ?>
                <form method="POST" action="">
                    <h2>Step 3: Create Admin User</h2>
                    <p>Create an administrator account:</p>
                    
                    <div class="form-group">
                        <label class="form-label">Admin Username *</label>
                        <input type="text" name="admin_username" class="form-control" value="admin" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Admin Password *</label>
                        <input type="password" name="admin_password" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="admin_password_confirm" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="import_sample_data" value="1" style="margin-right: 0.5rem;" checked>
                            Import sample data for testing
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="delete_install" value="1" style="margin-right: 0.5rem;" checked>
                            Delete install.php after completion (recommended for security)
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Create Admin & Complete Installation</button>
                    </div>
                </form>
            <?php elseif ($step == 4): ?>
                <div style="text-align: center; padding: 2rem;">
                    <h2>Installation Complete!</h2>
                    <p style="margin: 2rem 0;">Your School Management System has been successfully installed.</p>
                    <div>
                        <a href="auth/login.php" class="btn btn-primary">Go to Login Page</a>
                    </div>
                    <p style="margin-top: 2rem; color: var(--text-light); font-size: 0.875rem;">
                        <strong>Important:</strong> For security, please delete or rename the install.php file.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
