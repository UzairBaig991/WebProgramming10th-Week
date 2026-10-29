<?php
session_start();

$error = '';

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: new.php');
    exit;
}

if (isset($_POST['login'])) {
    unset($_SESSION['name']);

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Enter both your username and password.';
    } else {
        $connection = mysqli_connect('localhost', 'root', '', 'studentdashboard');

        if (!$connection) {
            $error = 'Could not connect to the database.';
        } else {
            $query = mysqli_prepare(
                $connection,
                'SELECT name FROM sign WHERE BINARY uname = ? AND BINARY pass = ? LIMIT 1'
            );
            if (!$query) {
                $error = 'Could not check your login. Please try again.';
            } else {
                mysqli_stmt_bind_param($query, 'ss', $username, $password);
                mysqli_stmt_execute($query);
                mysqli_stmt_bind_result($query, $name);

                if (mysqli_stmt_fetch($query)) {
                    session_regenerate_id(true);
                    $_SESSION['name'] = $name;
                } else {
                    $error = 'Incorrect username or password.';
                }
                mysqli_stmt_close($query);
            }
            mysqli_close($connection);
        }
    }
}

$loggedIn = isset($_SESSION['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard Login</title>
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #f1f3fa;
            color: #20243a;
            font-family: Arial, sans-serif;
        }
        main {
            width: min(100%, 390px);
            padding: 36px;
            border-radius: 18px;
            background: white;
            box-shadow: 0 16px 45px #252a441c;
        }
        h1 { margin: 0 0 10px; }
        p { color: #687087; line-height: 1.5; }
        label { display: block; margin: 18px 0 7px; font-weight: bold; }
        input, button {
            width: 100%;
            min-height: 46px;
            padding: 10px 12px;
            border-radius: 9px;
            font: inherit;
        }
        input { border: 1px solid #d5d9e5; }
        input:focus { outline: 2px solid #685be8; border-color: transparent; }
        button {
            margin-top: 22px;
            border: 0;
            background: #6054dc;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .error { color: #a32626; }
    </style>
</head>
<body>
    <main>
        <?php if ($loggedIn): ?>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
            <p>You have logged in successfully.</p>
            <form method="post">
                <button type="submit" name="logout">Log out</button>
            </form>
        <?php else: ?>
            <h1>Welcome back</h1>
            <p>Enter your account details to continue.</p>

            <?php if ($error !== ''): ?>
                <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <form method="post">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" autocomplete="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>

                <button type="submit" name="login">Login</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
