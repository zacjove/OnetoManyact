<?php
session_start();

require_once __DIR__ . '/core/dbConfig.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT user_id, first_name, last_name, email, password
            FROM users
            WHERE email = ?
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];

        header('Location: index.php');
        exit;

    } else {
        $error = 'Invalid email address or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<main>
    <h1>Transport Management System</h1>
    <h2>Log In</h2>

    <?php if (isset($_GET['registered'])): ?>
        <p class="notice">
            Registration successful! You can now log in.
        </p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <label>
            Email
            <input
                type="email"
                name="email"
                required
            >
        </label>

        <label>
            Password
            <input
                type="password"
                name="password"
                required
            >
        </label>

        <button type="submit">Log In</button>

    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>
</main>

</body>
</html>