<?php

use MyApp\Constant;
use MyApp\FormSanitizer;

require "core/init.php";

    if (isset($_SESSION['user_id'])){
        redirect_to(url_for('index.php'));
    }
    if (is_request_post()){
        if (isset($_POST['LogInButton'])){
            $username   = FormSanitizer::sanitizeFromUsername($_POST['username']);
            $password   = FormSanitizer::sanitizeFromPassword($_POST['password']);
            $wasSuccessFull = $account->login($username, $password);
            if ($wasSuccessFull){
                session_regenerate_id();
                $_SESSION['user_id'] = $wasSuccessFull;
                redirect_to(url_for('index.php'));
            }
        }
    }
	$pageTitle = "Login Page";
	include "shared/header.php"
?>
<div class="sigInContainer">
	<div class="column">
		<section class="header">
			<img src="asset/images/webrtclockup.png" alt="Site Logo">
			<h3>Login</h3>
			<span>to continue to webRTC</span>
		</section>
		<form action="<?php echo h($_SERVER['PHP_SELF']); ?>" method="post">
			<?= $account->getError(Constant::LoginFailed); ?>
			<input type="text" id="" name="username" placeholder="username or Email" value="<?php getInputValue("username") ?>" required>
            <input type="password" id="" name="password" required>
			<input type="submit" name="LogInButton" value="LogIn">
		</form>
		<a href="Register.php" class="logInMessage">Need an account? Register Here</a>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>