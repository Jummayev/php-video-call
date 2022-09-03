<?php
	require "../init.php";
	if (isset($_POST['user_id']) && !empty($_POST['user_id'])){
		$user_id = h($_POST['user_id']);
		$other_id = h($_POST['otherid']);
		if ($user_id ==$loadFromUser->user_id){
			$users=$loadFromUser->getConnectedPeers();
			foreach ($users as $user){
				$activeClass = ( (!empty($other_id) && $other_id == $user->id) ? "activeClass" : "");
				echo '<a href="'.url_for($user->username.'/videochat').'" class="user-connection '.$activeClass.'" data-profileid="'.$user->id.'">
                <div class="u-connected-wrapper">
                    <img width="40px" height="40px" style="border-radius: 50%"
                         src="'.  url_for("$user->avatar") .'" alt="'.$user->first_name.'">
                    <span class="u-connection-name">
                        '.$user->first_name.'
                    </span>
                    <div class="u-icons">
                        <svg class="cam-icon-connection video-call" xmlns="http://www.w3.org/2000/svg" focusable="false"
                             width="24" height="24" viewBox="0 0 24 24">
                            <path d="M18 10.48V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4.48l4 3.98v-11l-4 3.98zm-2-.79V18H4V6h12v3.69z"/>
                        </svg>
                        <i class="fa fa-phone audio-icon audio-call"></i>
                    </div>
                </div>
            </a>';
			}
		}
	}