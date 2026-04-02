<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="<?php echo $domena; ?>/frameworks/auth-template/fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="<?php echo $domena; ?>/frameworks/auth-template/css/style.css">
    <link rel="stylesheet" href="<?php echo $domena; ?>/css/my_css.css">
</head>
<body class="form-center">

    <div class="main">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">

                    <div class="signin-image">
                        <figure><img src="<?php echo $domena; ?>/frameworks/auth-template/images/signin-image.jpg" alt="sign in image"></figure>
                        <a href="register" class="signup-image-link">Create an account</a>
                        <a href="<?php echo $domena; ?>/auth/forgot-password" class="signup-image-link">Forget password</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">Sign In</h2>

                        <form method="POST" class="register-form" id="login-form" action="<?php echo $domena; ?>/auth/login">

                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Your Email"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    />
                            </div>

                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="passwd" id="password" placeholder="Password"
                                    
                                    />
                            </div>

                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Log in" />
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