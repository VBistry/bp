<?php

include 'Models/user.php';

// =========================================================================
// ZPRACOVÁNÍ POST POŽADAVKŮ
// =========================================================================

// --- SMAZÁNÍ ÚČTU ---
if (isset($_POST['submit_delete_profile'])) {
    deleteUserById($pdo, $_SESSION['user_id']);
    session_destroy();
    header("Location: " . $domena . "/auth/login");
    exit;
}

// --- AKTUALIZACE PROFILU ---
if (isset($_POST['submit_update_profile'])) {

    // Zpracování avataru pokud byl nahrán
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['avatar']['tmp_name']);

        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowed_mimes)) {
            setFlash('error', 'Invalid file type. Only JPG, PNG and WebP are allowed.');
            header("Location: " . $domena . "/profile/edit");
            exit;
        }

        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            setFlash('error', 'File is too large. Maximum size is 2MB.');
            header("Location: " . $domena . "/profile/edit");
            exit;
        }

        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };

        $filename   = $_SESSION['user_id'] . '.' . $ext;
        $uploadPath = 'uploads/avatars/' . $filename;

        foreach (['jpg', 'png', 'webp'] as $oldExt) {
            $oldFile = 'uploads/avatars/' . $_SESSION['user_id'] . '.' . $oldExt;
            if ($oldExt !== $ext && file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
            updateUserAvatar($pdo, $_SESSION['user_id'], $uploadPath);
        } else {
            setFlash('error', 'Could not save the profile picture. Check folder permissions.');
            header("Location: " . $domena . "/profile/edit");
            exit;
        }
    }

    // Načtení a ošetření vstupů
    $first_name = trim($_POST['first_name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $birth_year = (int) ($_POST['birth_year'] ?? 0);
    $description = trim($_POST['description_user'] ?? '');
    $gender = $_POST['gender'] ?? 'other';
    $passwd = $_POST['passwd'] ?? '';

    // Whitelist pro gender
    $allowed_genders = ['male', 'female', 'other'];
    if (!in_array($gender, $allowed_genders)) {
        $gender = 'other';
    }

    // Validace
    if (empty($first_name) || empty($surname) || empty($email)) {
        setFlash('error', 'First name, last name and email are required.');
        header("Location: " . $domena . "/profile/edit");
        exit;
    }

    if (strlen($first_name) > 50 || strlen($surname) > 50) {
        setFlash('error', 'Name is too long (max 50 characters).');
        header("Location: " . $domena . "/profile/edit");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Invalid email address.');
        header("Location: " . $domena . "/profile/edit");
        exit;
    }

    if ($birth_year < 1900 || $birth_year > (int) date('Y')) {
        setFlash('error', 'Invalid birth year.');
        header("Location: " . $domena . "/profile/edit");
        exit;
    }

    if (strlen($description) > 500) {
        setFlash('error', 'Description is too long (max 500 characters).');
        header("Location: " . $domena . "/profile/edit");
        exit;
    }

    // Heslo je nepovinné - mění se jen pokud ho uživatel vyplnil
    $passwordHash = null;
    if (!empty($passwd)) {
        if (strlen($passwd) < 8 || strlen($passwd) > 72) {
            setFlash('error', 'Password must be between 8 and 72 characters.');
            header("Location: " . $domena . "/profile/edit");
            exit;
        }
        $passwordHash = password_hash($passwd, PASSWORD_DEFAULT);
    }

    $clean_data = [
        'first_name' => $first_name,
        'surname' => $surname,
        'email' => $email,
        'tel' => $tel,
        'birth_year' => $birth_year,
        'description_user' => $description,
        'gender' => $gender,
        'passwd' => $passwordHash, // null = heslo se nemění
    ];

    $isUpdated = updateUser($pdo, $_SESSION['user_id'], $clean_data);

    if ($isUpdated) {
        // Aktualizujeme i session, aby navbar zobrazoval nové jméno
        $_SESSION['first_name'] = $first_name;
        $_SESSION['surname'] = $surname;
        $_SESSION['email'] = $email;

        setFlash('success', 'Profile updated successfully.');
    } else {
        setFlash('error', 'Profile could not be updated.');
    }

    header("Location: " . $domena . "/profile/show");
    exit;
}

// =========================================================================
// VYKRESLOVÁNÍ STRÁNKY
// =========================================================================

include 'Views/includes/head.php';
include 'Views/includes/navbar.php';

$user_data = null;
if (isset($_SESSION['user_id'])) {
    $user_data = getUserById($pdo, $_SESSION['user_id']);
}

if ($router['method'] === 'edit') {
    if ($user_data) {
        include 'Views/profile/edit.php';
    } else {
        setFlash('error', 'User not found.');
        header("Location: " . $domena . "/profile/show");
        exit;
    }
} else {
    if ($user_data) {
        include 'Views/profile/show.php';
    } else {
        setFlash('error', 'Please log in to view your profile.');
        header("Location: " . $domena . "/profile/show");
        exit;
    }
}

include 'Views/includes/footer.php';
?>