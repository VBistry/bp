<?php

include 'Models/auth.php';

/* ------------------------------------------------------------------
   1. LOGIKA PRO PŘIHLÁŠENÍ (LOGIN)
   ------------------------------------------------------------------ */
if ($router['method'] == 'login') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email  = trim($_POST['email'] ?? '');
        $passwd = $_POST['passwd'] ?? '';

        // Validace
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

            $_SESSION['autorized']  = true;
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['surname']    = $user['surname'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['email']      = $user['email'];

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


/* ------------------------------------------------------------------
   2. LOGIKA PRO REGISTRACI (REGISTER)
   ------------------------------------------------------------------ */
} elseif ($router['method'] == 'register') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName        = trim($_POST['first_name'] ?? '');
        $surname          = trim($_POST['surname'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $passwd           = $_POST['passwd'] ?? '';
        $repeatPasswd     = $_POST['repeatpasswd'] ?? '';
        $birth_year       = (int) ($_POST['birth_year'] ?? 0);
        $tel              = trim($_POST['tel'] ?? '');
        $description_user = trim($_POST['description_user'] ?? '');
        $gender           = $_POST['gender'] ?? 'other';
        $role             = 'user';

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

        if (findUserByEmail($pdo, $email)) {
            setFlash('error', 'This email is already registered.');
            header("Location: " . $domena . "/auth/register");
            exit;
        }

        // Vše OK — ukládáme
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

                $_SESSION['autorized']  = true;
                $_SESSION['user_id']    = $newUserId;
                $_SESSION['first_name'] = $firstName;
                $_SESSION['surname']    = $surname;
                $_SESSION['role']       = $role;
                $_SESSION['email']      = $email;

                session_regenerate_id(true);

                setFlash('success', 'Account was created successfully. Welcome, ' . htmlspecialchars($firstName) . '!');
                header("Location: " . $domena . "/dashboard");
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


/* ------------------------------------------------------------------
   3. ODHLÁŠENÍ
   ------------------------------------------------------------------ */
} elseif ($router['method'] == 'logout') {
    session_destroy();
    $pdo = null;
    header("Location: " . $domena . "/auth/login");
    exit;
}
?>