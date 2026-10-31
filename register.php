<?php
session_start();

require_once __DIR__ . '/core/dbConfig.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (
        $first_name === '' ||
        $last_name === '' ||
        $email === '' ||
        $password === ''
    ) {
        $error = 'Please fill in all required fields.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';

    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';

    } else {
        try {
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO users
                    (
                        first_name,
                        last_name,
                        email,
                        password,
                        address,
                        date_of_birth
                    )
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                $password_hash,
                $address !== '' ? $address : null,
                $date_of_birth !== '' ? $date_of_birth : null
            ]);

            header('Location: login.php?registered=1');
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                $error = 'This email address is already registered.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<main>
    <h1>Create an Account</h1>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="register.php">

        <label>
            First Name
            <input
                type="text"
                name="first_name"
                maxlength="100"
                required
            >
        </label>

        <label>
            Last Name
            <input
                type="text"
                name="last_name"
                maxlength="100"
                required
            >
        </label>

        <label>
            Email
            <input
                type="email"
                name="email"
                maxlength="150"
                required
            >
        </label>

        <label>
            Address
            <input
                type="text"
                name="address"
                maxlength="255"
            >
        </label>

        <label>
            Date of Birth
            <input
                type="date"
                name="date_of_birth"
            >
        </label>

        <label>
            Password
            <input
                type="password"
                name="password"
                minlength="8"
                required
            >
        </label>

        <label>
            Confirm Password
            <input
                type="password"
                name="confirm_password"
                minlength="8"
                required
            >
        </label>

        <button type="submit">Register</button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Log in</a>
    </p>
</main>

</body>
</html>