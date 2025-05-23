<div class="card-header">Change Password</div>

<form class="form" action="index.php?do=changepass_process" method="post">
	<input type="hidden" name="UserID" value="<?php echo $_SESSION['UserID']; ?>" />

	<div class="mb-3">
		<label for="OldPass" class="form-label">Old Password:</label>
		<input type="password" class="form-control" name="OldPass" id="OldPass" required />
	</div>

	<div class="mb-3">
		<label for="NewPass" class="form-label">New Password:</label>
		<input type="password" class="form-control" name="NewPass" id="NewPass" required />
	</div>

	<div class="mb-3">
		<label for="ConfirmPass" class="form-label">Confirm New Password:</label>
		<input type="password" class="form-control" name="ConfirmPass" id="ConfirmPass" required />
	</div>

	<button type="submit" class="btn btn-primary">Update Password</button>
</form>
