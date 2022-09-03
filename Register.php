<?php

    namespace MyApp;

    require "core/init.php";

    if (isset($_SESSION['user_id'])){
        redirect_to(url_for('index.php'));
    }
    if (is_request_post()){
        if (isset($_POST['submitButton'])){
            $first_name = FormSanitizer::sanitizeFromString($_POST['first_name']);
            $last_name  = FormSanitizer::sanitizeFromString($_POST['last_name']);
            $username   = FormSanitizer::sanitizeFromUsername($_POST['username']);
            $email      = FormSanitizer::sanitizeFromEmail($_POST['email']);
            $password   = FormSanitizer::sanitizeFromPassword($_POST['password']);
            $wasSuccessFull = $account->register($first_name, $last_name, $username,$email, $password);
            if ($wasSuccessFull){
               session_regenerate_id();
               $_SESSION['user_id'] = $wasSuccessFull;
               redirect_to(url_for('index.php'));
            }
        }
    }
?>
<?php  require "shared/header.php"; ?>
    <div class="sigInContainer">
        <div class="column">
            <section class="header">
                <img src="asset/images/webrtclockup.png" alt="Site Logo">
                <h3>Register</h3>
                <span>to continue to webRTC</span>
            </section>
            <form action="Register.php" method="POST">
                <?= $account->getError(Constant::FirstNameCharacters); ?>
                <input type="text" id="first_name" name="first_name" placeholder="first_name" value="<?php getInputValue("first_name") ?>" required>
				<?= $account->getError(Constant::LastNameCharacters); ?>
                <input type="text" id="last_name" name="last_name" placeholder="last_name" value="<?php getInputValue("last_name") ?>" required>
				<?= $account->getError(Constant::UsernameCharacters); ?>
				<?= $account->getError(Constant::UsernameTake); ?>
                <input type="text" id="username" name="username" placeholder="username" value="<?php getInputValue("username") ?>" required>
				<?= $account->getError(Constant::EmailCharacters); ?>
				<?= $account->getError(Constant::EmailTake); ?>
				<?= $account->getError(Constant::EmailInvalid); ?>
                <input type="email" id="email" name="email" placeholder="email" value="<?php getInputValue("email") ?>" required>
				<?= $account->getError(Constant::PasswordCharacters); ?>
				<?= $account->getError(Constant::PasswordNotAlphaNumeric); ?>
                <input type="password" id="password" name="password" required>
                <input type="submit" name="submitButton" value="Register">
            </form>
            <a href="login.php" class="logInMessage">Already have an account? LogIn Here</a>
        </div>
    </div>
</body>
</html>