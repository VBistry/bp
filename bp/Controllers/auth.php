<?php

include 'Models/auth.php';
include 'Models/user.php';

// --- LOGIN ---
if ($router['method'] == 'login') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $passwd = $_POST['passwd'] ?? '';

        if (empty($email) || empty($passwd)) {
            setFlash('error', 'Please fill in your email and password.');
            header("Location: " . $domena . "/auth/login");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Invalid email address.');
            header("Location: " . $domena . "/auth/login");
            exit;
        }

        if (strlen($passwd) < 8 || strlen($passwd) > 72) {
            setFlash('error', 'Invalid email or password (must be between 8 to 72 chars).');
            header("Location: " . $domena . "/auth/login");
            exit;
        }

        $user = findUserByEmail($pdo, $email);

        if ($user && password_verify($passwd, $user['passwd'])) {

            $_SESSION['authorized'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['surname'] = $user['surname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['verified_email'] = $user['verified_email'];

            session_regenerate_id(true);

            setFlash('success', 'Welcome back, ' . htmlspecialchars($user['first_name']) . '!');
            header("Location: " . $domena . "/dashboard");
            exit;

        } else {
            setFlash('error', 'Invalid email or password.');
            header("Location: " . $domena . "/auth/login");
            exit;
        }
    }

    include 'Views/auth/login.php';


// --- REGISTER ---
} elseif ($router['method'] == 'register') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['first_name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $passwd = $_POST['passwd'] ?? '';
        $repeatPasswd = $_POST['repeatpasswd'] ?? '';
        $birth_year = ($_POST['birth_year'] ?? 0);
        $tel = trim($_POST['tel'] ?? '');
        $description_user = trim($_POST['description_user'] ?? '');
        $gender = $_POST['gender'] ?? 'other';
        $role = 'user';

        $allowed_genders = ['male', 'female', 'other'];
        if (!in_array($gender, $allowed_genders)) {
            $gender = 'other';
        }

        // Validace
        if (empty($firstName) || empty($surname) || empty($email) || empty($passwd)) {
            setFlash('error', 'Please fill in all required fields.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if (strlen($firstName) > 50 || strlen($surname) > 50) {
            setFlash('error', 'Name is too long (max 50 characters).');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if (strlen($description_user) > 500) {
            setFlash('error', 'Description is too long (max 500 characters).');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Invalid email address.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if (strlen($passwd) < 8 || strlen($passwd) > 72) {
            setFlash('error', 'Password must be between 8 and 72 characters.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if ($passwd !== $repeatPasswd) {
            setFlash('error', 'Passwords do not match.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if ($birth_year < 1900 || $birth_year > (int) date('Y')) {
            setFlash('error', 'Invalid birth year.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if (empty($tel) || !preg_match('/^\+?[0-9\s\-()]{9,}$/', $tel)) {
            setFlash('error', 'Please enter a valid phone number.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        if (findUserByEmail($pdo, $email)) {
            setFlash('error', 'This email is already registered.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        // All valid — save user
        $passwordHash = password_hash($passwd, PASSWORD_DEFAULT);

        try {
            $success = registerUser(
                $pdo,
                $firstName,
                $surname,
                $email,
                $passwordHash,
                $birth_year,
                $tel,
                $role,
                $description_user,
                $gender
            );

            if ($success) {
                $newUserId = $pdo->lastInsertId();

                $_SESSION['authorized'] = true;
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['first_name'] = $firstName;
                $_SESSION['surname'] = $surname;
                $_SESSION['role'] = $role;
                $_SESSION['email'] = $email;
                $_SESSION['verified_email'] = false;

                session_regenerate_id(true);

                // Generate verification URL and send email
                $verificationUrl = $domena . '/' . generateVerificationUrl($newUserId, $email);
                sendVerificationEmail($email, $firstName, $verificationUrl);

                setFlash('success', 'Account was created successfully. Welcome, ' . htmlspecialchars($firstName) . '!');
                header("Location: " . $domena . "/auth/verify-email");
                exit;

            } else {
                setFlash('error', 'Registration failed. Please try again.');
                header("Location: " . $domena . "/auth/register");
                exit;
            }

        } catch (PDOException $e) {
            error_log("Registration PDOException: " . $e->getMessage());
            setFlash('error', 'Registration failed. Please try again.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }
    }

    include 'Views/auth/register.php';


// --- LOGOUT ---
} elseif ($router['method'] == 'logout') {
    session_destroy();
    $pdo = null;
    header("Location: " . $domena . "/auth/login");
    exit;

// --- VERIFY EMAIL PAGE ---
} elseif ($router['method'] == 'verify-email') {
    include 'Views/auth/verify-email.php';

// --- VERIFY EMAIL ---
} elseif ($router['method'] == 'verify') {

    $userId = $router['params'][0] ?? null;
    $hash = $router['params'][1] ?? null;
    $expires = $_GET['expires'] ?? null;
    $signature = $_GET['signature'] ?? null;

    if (!$userId || !$hash || !$expires || !$signature) {
        setFlash('error', 'Invalid verification link.');
        header("Location: " . $domena . "/auth/verify-email");
        exit;
    }

    if (!verifySignedUrl($userId, $hash, (int) $expires, $signature)) {
        setFlash('error', 'Verification link is invalid or has expired.');
        header("Location: " . $domena . "/auth/verify-email");
        exit;
    }

    // Verification OK — update DB
    $user = getUserById($pdo, $userId);
    if (!$user || sha1($user['email']) !== $hash) {
        setFlash('error', 'Invalid verification link.');
        header("Location: " . $domena . "/auth/verify-email");
        exit;
    }

    verifyUserEmail($pdo, $userId);
    $_SESSION['verified_email'] = true;

    setFlash('success', 'Email verified successfully!');
    header("Location: " . $domena . "/dashboard");
    exit;
    
} elseif ($router['method'] == 'resend-verification') {
    $userId = $_SESSION['user_id'];
    $email  = $_SESSION['email'];
    $firstName = $_SESSION['first_name'];

    $verificationUrl = $domena . '/' . generateVerificationUrl($userId, $email);
    sendVerificationEmail($email, $firstName, $verificationUrl);

    setFlash('success', 'Verification email has been resent. Please check your inbox.');
    header("Location: " . $domena . "/auth/verify-email");
    exit;
}

// --- FORGOT PASSWORD ---
elseif ($router['method'] == 'forgot-password') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            header("Location: " . $domena . "/auth/forgot-password");
            exit;
        }

        $user = findUserByEmail($pdo, $email);

        // Intentionally same message even if user does not exist — security
        if ($user) {
            $resetUrl = $domena . '/' . generatePasswordResetUrl($pdo, $user['id'], $email);
            sendPasswordResetEmail($email, $user['first_name'], $resetUrl);
        }

        setFlash('success', 'If this email is registered, you will receive a reset link shortly.');
        header("Location: " . $domena . "/auth/forgot-password");
        exit;
    }

    include 'Views/auth/forgot-password.php';

// --- RESET PASSWORD ---
} elseif ($router['method'] == 'reset-password') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token        = $_POST['token'] ?? '';
        $passwd       = $_POST['passwd'] ?? '';
        $repeatPasswd = $_POST['repeatpasswd'] ?? '';

        if (empty($token) || empty($passwd) || empty($repeatPasswd)) {
            setFlash('error', 'Please fill in all fields.');
            header("Location: " . $domena . "/auth/reset-password?token=" . urlencode($token));
            exit;
        }

        if (strlen($passwd) < 8 || strlen($passwd) > 72) {
            setFlash('error', 'Password must be between 8 and 72 characters.');
            header("Location: " . $domena . "/auth/reset-password?token=" . urlencode($token));
            exit;
        }

        if ($passwd !== $repeatPasswd) {
            setFlash('error', 'Passwords do not match.');
            header("Location: " . $domena . "/auth/reset-password?token=" . urlencode($token));
            exit;
        }

        $user = getUserByResetToken($pdo, $token);

        if (!$user) {
            setFlash('error', 'Reset link is invalid or has expired.');
            header("Location: " . $domena . "/auth/forgot-password");
            exit;
        }

        $hashedPassword = password_hash($passwd, PASSWORD_DEFAULT);
        updateUserPassword($pdo, $user['id'], $hashedPassword);

        setFlash('success', 'Password has been reset successfully. Please log in.');
        header("Location: " . $domena . "/auth/login");
        exit;
    }

    include 'Views/auth/reset-password.php';
}
?>