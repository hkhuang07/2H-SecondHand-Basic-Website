<div class="card-header">Sign up</div>

<form class="form needs-validation" action="index.php?do=signup_process" method="post" novalidate>
	<div class="mb-3">
		<label for="fullname" class="form-label">Full Name:</label>
		<input type="text" class="form-control" id="fullname" name="FullName" required />
		<div class="invalid-feedback">Full name cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" class="form-control" id="email" name="Email" required />
		<div class="invalid-feedback">Please provide a valid email address.</div>
	</div>

	<div class="mb-3">
		<label for="phone" class="form-label">Phone:</label>
		<input type="text" class="form-control" id="phone" name="Phone" required pattern="^[0-9+\s().-]{7,20}$" />
		<div class="invalid-feedback">Phone number is required and must be valid.</div>
	</div>

	<div class="mb-3">
		<label for="address" class="form-label">Address:</label>
		<textarea class="form-control" id="address" name="Address" rows="3" required></textarea>
		<div class="invalid-feedback">Address cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="username" class="form-label">Username:</label>
		<input type="text" class="form-control" id="username" name="UserName" required />
		<div class="invalid-feedback">Username cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="password" class="form-label">Password:</label>
		<input type="password" class="form-control" id="password" name="Password" required autocomplete="new-password" />
		<div class="invalid-feedback">Password cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="repassword" class="form-label">Confirm Password:</label>
		<input type="password" class="form-control" id="repassword" name="ConfirmPassword" required autocomplete="new-password" />
		<div class="invalid-feedback">Confirm password cannot be left blank.</div>
	</div>

	<button type="submit" class="btn btn-primary">
		<i class="bi bi-person-add"></i> Sign up
	</button>
</form>

<script>
// Bootstrap 5 validation
(() => {
	'use strict';
	const forms = document.querySelectorAll('.needs-validation');
	Array.from(forms).forEach(form => {
		form.addEventListener('submit', event => {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);
	});
})();
</script>


<!--div class="card-header">Sign up</div>

<form class="form" action="index.php?do=signup_process" method="post" class="needs-validation" novalidate>
	<div class="mb-3">
		<label for="fullname" class="form-label">Full Name:</label>
		<input type="text" class="form-control" id="fullname" name="FullName" required />
		<div class="invalid-feedback">Full name cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="username" class="form-label">Username:</label>
		<input type="text" class="form-control" id="username" name="UserName" required />
		<div class="invalid-feedback">Username cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="password" class="form-label">Password:</label>
		<input type="password" class="form-control" id="password" name="Password" required autocomplete="new-password" />
		<div class="invalid-feedback">Password cannot be left blank.</div>
	</div>

	<div class="mb-3">
		<label for="repassword" class="form-label">Confirm Password:</label>
		<input type="password" class="form-control" id="repassword" name="ConfirmPassword" required autocomplete="new-password" />
		<div class="invalid-feedback">Confirm password cannot be left blank.</div>
	</div>

	<button type="submit" class="btn btn-primary">
		<i class="bi bi-person-add"></i> Sign up
	</button>
</form>

<script>
// Bootstrap 5 validation
(() => {
	'use strict';
	const forms = document.querySelectorAll('.needs-validation');
	Array.from(forms).forEach(form => {
		form.addEventListener('submit', event => {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		}, false);
	});
})();
</script-->