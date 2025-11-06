<?php
/**
 * Error Page Handler
 * Displays user-friendly error pages
 */

$errorCode = isset($_GET['code']) ? intval($_GET['code']) : 500;
$errorMessages = [
    400 => ['title' => 'Bad Request', 'message' => 'The request was invalid or cannot be served.'],
    401 => ['title' => 'Unauthorized', 'message' => 'You are not authorized to access this resource.'],
    403 => ['title' => 'Forbidden', 'message' => 'Access to this resource is forbidden.'],
    404 => ['title' => 'Page Not Found', 'message' => 'The page you are looking for does not exist.'],
    500 => ['title' => 'Internal Server Error', 'message' => 'An internal server error occurred. Please try again later.'],
    503 => ['title' => 'Service Unavailable', 'message' => 'The service is temporarily unavailable.'],
];

$error = $errorMessages[$errorCode] ?? $errorMessages[500];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($error['title']); ?> - School Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .error-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }
        .error-card {
            text-align: center;
            max-width: 500px;
            padding: 3rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-color);
        }
        .error-message {
            color: var(--text-light);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-code"><?php echo $errorCode; ?></div>
            <h1 class="error-title"><?php echo htmlspecialchars($error['title']); ?></h1>
            <p class="error-message"><?php echo htmlspecialchars($error['message']); ?></p>
            <div>
                <a href="index.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="auth/login.php" class="btn btn-secondary">Go to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
