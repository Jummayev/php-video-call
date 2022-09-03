<?php
	require "core/init.php";
	if ($_SESSION['user_id']){
		log_out();
		redirect_to(url_for("login.php"));
	}