<?php
	function ErrorMessage($msg = "")
	{
		echo "<div class='alert alert-danger'>$msg</div>";
		exit();
		//echo "<h3>Error</h3><p class='ErrorMessage'>$Message</p>";
	}
	
	function Message($msg = "")
	{
		echo "<div class='alert alert-success'>$msg</div>";
		//exit();
		//echo "<h3>Success</h3><p class='Message'>$Message</p>";
	}

	function Redirect($url) {
		sleep(5); // Đợi 2 giây trước khi chuyển hướng
		echo "<script>window.location.href = '$url';</script>";
		exit;
	}

?>