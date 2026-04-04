<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet"
        href="<?php echo $domena; ?>/frameworks/auth-template/fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="<?php echo $domena; ?>/frameworks/auth-template/css/style.css">
    <link rel="stylesheet" href="<?php echo $domena; ?>/css/my_css.css">
</head>

<body class="form-center">

    <div class="main">
        <section class="signup">
            <div class="container">
                <div class="signup-content">

                    <div class="signup-form">
                        <h2 class="form-title">Register</h2>

                        <form method="POST" class="register-form" id="register-form"
                            action="<?php echo $domena; ?>/auth/register">

                            <div class="form-group">
                                <label for="first_name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="first_name" id="first_name" placeholder="First Name"
                                    value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" />
                            </div>

                            <div class="form-group">
                                <label for="surname"><i class="zmdi zmdi-account"></i></label>
                                <input type="text" name="surname" id="surname" placeholder="Surname"
                                    value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>" />
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Email Address"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                            </div>

                            <div class="form-group">
                                <label for="passwd"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="passwd" id="passwd" placeholder="Password" />
                            </div>

                            <div class="form-group">
                                <label for="repeatpasswd"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="repeatpasswd" id="repeatpasswd"
                                    placeholder="Repeat Password" />
                            </div>

                            <div class="form-group">
                                <label for="birth_year"><i class="zmdi zmdi-calendar"></i></label>
                                <input type="number" name="birth_year" id="birth_year" placeholder="Birth Year"
                                    value="<?php echo htmlspecialchars($_POST['birth_year'] ?? ''); ?>" />
                            </div>

                            <div class="form-group">
                                <label for="description"><i class="zmdi zmdi-comment-text"></i></label>
                                <input type="text" name="description_user" id="description" placeholder="Description"
                                    value="<?php echo htmlspecialchars($_POST['description_user'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="tel"><i class="zmdi zmdi-phone"></i></label>
                                <input type="tel" name="tel" id="tel" placeholder="Telephone (+420 754 584 652)"
                                    value="<?php echo htmlspecialchars($_POST['tel'] ?? ''); ?>" />
                            </div>
                            
                            <div class="form-group">
                                <label for="gender"><i class="zmdi zmdi-male-female"></i></label>
                                <select name="gender" id="gender" class="auth-select">
                                    <option value="" disabled <?php echo empty($_POST['gender']) ? 'selected' : ''; ?>>
                                        Select Gender</option>
                                    <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Register" />
                            </div>

                        </form>
                    </div>

                    <div class="signup-image">
                        <figure><img src="<?php echo $domena; ?>/frameworks/auth-template/images/signup-image.jpg"
                                alt="sign up image"></figure>
                        <a href="<?php echo $domena; ?>/auth/login" class="signup-image-link">I am already member</a>
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