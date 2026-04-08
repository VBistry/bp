<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet"
        href="<?php echo $domena; ?>/frameworks/auth-template/fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="<?php echo $domena; ?>/frameworks/auth-template/css/style.css">
    <link rel="stylesheet" href="<?php echo $domena; ?>/css/my_css.css">
</head>

<body class="form-center">
    <div class="main">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-form">
                        <h1 class="form-title">Reset Password</h1>

                        <form method="POST" action="<?php echo $domena; ?>/auth/reset-password">
                            <input type="hidden" name="token"
                                value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                            <div class="form-group">
                                <label for="passwd"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="passwd" id="passwd" placeholder="New Password"  />
                            </div>
                            <div class="form-group">
                                <label for="repeatpasswd"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="repeatpasswd" id="repeatpasswd"
                                    placeholder="Repeat New Password"  />
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" class="form-submit form-submit--full" value="Reset Password" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include 'Views/includes/toast.php'; ?>
    <script src="<?php echo $domena; ?>/frameworks/auth-template/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $domena; ?>/frameworks/auth-template/js/main.js"></script>
    <script src="<?php echo $domena; ?>/js/my_js.js"></script>
</body>

</html>