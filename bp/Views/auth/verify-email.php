<?php
include 'Views/includes/head.php';
include 'Views/includes/navbar.php';

?>

<h1>Please verify your email ASAP, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>
<p>To access the dashboard and all features, please check your email inbox for a verification link. If you haven't received the email, please check your spam folder or click the button below to resend the verification email.</p>
<?php
include 'Views/includes/footer.php';
?>