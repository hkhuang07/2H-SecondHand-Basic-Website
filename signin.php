<div class="card-header">Sign in</div>
<form class="form" action="index.php?do=signin_process" method="post">
		<div class="mb-3">
			<label for="UserName" class="form-label">User Name: </label>
			<input type="text" class="form-control" name="UserName" id="UserName" required />
		</div>

		<div class="mb-3">
			<label for="Password" class="form-label">Password:</label>
			<input type="Password" class="form-control" name="Password" id="Password" required />
		</div>

		<button type="submit" class="btn btn-primary">Sign In</button>
</form>




<!--form action="index.php?do=signin_process" method="post">
	<table class="Form">
		<tr>
			<td>UserName:</td>
			<td><input type="text" name="UserName" /></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="Password" /></td>
		</tr>
	</table>
	<input type="submit" value="Sign In" />
</form-->