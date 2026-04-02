<?php
include 'Views/includes/head.php';
include 'Views/includes/navbar.php';
// tady vyjimecne includuju ve view
?>

<h1>Verification</h1>
<div class="main-content">
    <p>Dear <?php echo htmlspecialchars($_SESSION['first_name']); ?>, to access the dashboard and all features, please
        check your email inbox for a verification link. If you haven't received the email, please check your spam folder
        or click the button below to resend the verification email.</p>
    <form action="<?php echo $domena; ?>/auth/resend-verification" method="POST">
        <button type="submit" class="btn-group__item btn-group__item--primary">
            Resend Verification Email
        </button>
    </form>
</div>
<?php
include 'Views/includes/footer.php';
?>