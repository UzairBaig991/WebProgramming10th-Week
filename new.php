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
    <meta name="theme-color" content="#f5f6fc">
    <title>Student Portal | <?php echo $loggedIn ? 'Welcome' : 'Login'; ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f5f6fc;
            color: #1d2340;
            font-family: Arial, sans-serif;
        }

        .navbar {
            display: flex;
            min-height: 76px;
            align-items: center;
            justify-content: space-between;
            padding: 0 max(6vw, 24px);
            border-bottom: 1px solid #e8e9f1;
            background: #fff;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #252943;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-decoration: none;
        }

        .brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 12px;
            background: #6558df;
            color: #fff;
            font-size: 19px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-links a {
            color: #687087;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .nav-links a:hover {
            color: #5145d3;
        }

        .nav-status {
            padding: 9px 14px;
            border: 1px solid #e6e3ff;
            border-radius: 20px;
            background: #f6f4ff;
            color: #5145d3;
            font-size: 12px;
            font-weight: 700;
        }

        .page {
            display: grid;
            width: min(1120px, calc(100% - 48px));
            min-height: calc(100vh - 76px);
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: clamp(36px, 8vw, 100px);
            margin: 0 auto;
            padding: 64px 0;
        }

        .intro {
            max-width: 520px;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 18px;
            color: #6558df;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .intro h1 {
            margin: 0;
            font-size: clamp(38px, 5vw, 58px);
            letter-spacing: -0.05em;
            line-height: 1.08;
        }

        .intro p {
            max-width: 440px;
            margin: 20px 0 0;
            color: #69718a;
            font-size: 17px;
            line-height: 1.7;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-top: 34px;
            color: #505873;
            font-size: 14px;
        }

        .feature-icon {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 12px;
            background: #e9e7ff;
            color: #5145d3;
            font-weight: 800;
        }

        .login-card {
            width: 100%;
            padding: clamp(26px, 4vw, 40px);
            border: 1px solid #e9eaf2;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 22px 65px #252a4414;
        }

        .login-card h2 {
            margin: 0 0 9px;
            font-size: 26px;
            letter-spacing: -0.03em;
        }

        .card-copy {
            margin: 0 0 27px;
            color: #788096;
            font-size: 14px;
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 18px 0 8px;
            color: #333a54;
            font-size: 13px;
            font-weight: 700;
        }

        input,
        button {
            width: 100%;
            min-height: 49px;
            padding: 12px 14px;
            border-radius: 10px;
            font: inherit;
        }

        input {
            border: 1px solid #dfe2ec;
            background: #fcfcfe;
            color: #1d2340;
        }

        input:focus {
            border-color: #6558df;
            outline: 3px solid #6558df24;
        }

        button {
            margin-top: 24px;
            border: 0;
            background: #6054dc;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            transition: background 160ms ease, transform 160ms ease;
        }

        button:hover {
            transform: translateY(-1px);
            background: #4f43ce;
        }

        button:focus-visible,
        a:focus-visible {
            outline: 3px solid #6558df70;
            outline-offset: 3px;
        }

        .error {
            padding: 11px 13px;
            border: 1px solid #f0cccc;
            border-radius: 9px;
            background: #fff5f5;
            color: #a32626;
            font-size: 14px;
            line-height: 1.5;
        }

        .success {
            margin: 22px 0;
            padding: 14px;
            border: 1px solid #cdebdc;
            border-radius: 10px;
            background: #f1fbf5;
            color: #286347;
            font-size: 14px;
            line-height: 1.5;
        }

        .footer {
            padding: 18px 24px;
            color: #8a90a3;
            font-size: 12px;
            text-align: center;
        }

        @media (max-width: 760px) {
            .navbar {
                min-height: 68px;
                padding: 0 20px;
            }

            .nav-links {
                gap: 14px;
            }

            .nav-links a {
                display: none;
            }

            .page {
                min-height: auto;
                grid-template-columns: 1fr;
                gap: 34px;
                padding: 54px 0;
            }

            .intro h1 {
                font-size: clamp(38px, 10vw, 52px);
            }
        }

        @media (max-width: 420px) {
            .brand {
                font-size: 12px;
            }

            .nav-status {
                padding: 8px 10px;
                font-size: 11px;
            }

            .page {
                width: min(100% - 32px, 440px);
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a class="brand" href="#home">
            <span class="brand-mark" aria-hidden="true">S</span>
            <span>STUDENT PORTAL</span>
        </a>
        <nav class="nav-links" aria-label="Main navigation">
            <a href="#home">Home</a>
            <a href="#portal">Sign in</a>
            <span class="nav-status">Secure student access</span>
        </nav>
    </header>

    <main class="page" id="home">
        <section class="intro" aria-labelledby="page-title">
            <span class="eyebrow">Your learning hub</span>
            <?php if ($loggedIn): ?>
                <h1 id="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>.</h1>
                <p>You are signed in and ready to continue to your student dashboard.</p>
                <div class="feature">
                    <span class="feature-icon" aria-hidden="true">✓</span>
                    <span>Your account is signed in successfully.</span>
                </div>
            <?php else: ?>
                <h1 id="page-title">Everything for your studies, in one place.</h1>
                <p>Sign in to your student account and pick up right where you left off.</p>
                <div class="feature">
                    <span class="feature-icon" aria-hidden="true">✓</span>
                    <span>A simple, secure way to access your student portal.</span>
                </div>
            <?php endif; ?>
        </section>

        <section class="login-card" id="portal" aria-labelledby="form-title">
            <?php if ($loggedIn): ?>
                <h2 id="form-title">You're all set</h2>
                <p class="card-copy">Welcome to your student portal. You can safely log out when you are finished.</p>
                <div class="success" role="status">Login successful. Welcome to the student portal!</div>
                <form method="post">
                    <button type="submit" name="logout">Log out</button>
                </form>
            <?php else: ?>
                <h2 id="form-title">Welcome back</h2>
                <p class="card-copy">Enter your account details to continue.</p>

                <?php if ($error !== ''): ?>
                    <p class="error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>

            <form method="post">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" autocomplete="username" placeholder="Enter Username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" placeholder="Enter Password" required>

                <button type="submit" name="login">Login</button>
            </form>
            <?php endif; ?>
        </section>
    </main>
    <footer class="footer">Student Portal &copy; <?php echo date('Y'); ?></footer>
</body>
</html>
