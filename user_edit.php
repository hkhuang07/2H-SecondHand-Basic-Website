<?php
require_once("config.php");
require_once("library.php");

$UserID = intval($_GET['id']);
$sql = "SELECT * FROM tbl_users WHERE UserID = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    ErrorMessage("User not found.");
    exit;
}

$row = $result->fetch_assoc();
?>

<div class="card-header">Edit User</div>

<div class="card-header">Edit User</div>

<form class="form needs-validation" action="index.php?do=user_edit_process" method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="UserID" value="<?php echo $row['UserID']; ?>" />

    <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" class="form-control" id="username" name="UserName" value="<?php echo htmlspecialchars($row['UserName']); ?>" required />
        <div class="invalid-feedback">Username cannot be left blank.</div>
    </div>

    <div class="mb-3">
        <label for="fullname" class="form-label">Full Name:</label>
        <input type="text" class="form-control" id="fullname" name="FullName" value="<?php echo htmlspecialchars($row['FullName']); ?>" required />
        <div class="invalid-feedback">Full name cannot be left blank.</div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" id="email" name="Email" value="<?php echo htmlspecialchars($row['Email']); ?>" />
        <div class="invalid-feedback">Please enter a valid email.</div>
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">Phone:</label>
        <input type="text" class="form-control" id="phone" name="Phone" value="<?php echo htmlspecialchars($row['Phone']); ?>" />
        <div class="invalid-feedback">Phone cannot be empty.</div>
    </div>

    <div class="mb-3">
        <label for="address" class="form-label">Address:</label>
        <textarea class="form-control" id="address" name="Address" rows="3"><?php echo htmlspecialchars($row['Address']); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="avatar" class="form-label">Avatar:</label>
        <div class="mb-2">
            <img src="uploads/avatars/<?php echo htmlspecialchars($row['Avatar']); ?>"
                alt="Current Avatar" class="img-thumbnail" style="max-width: 100px;">
        </div>
        <input type="file" class="form-control" id="avatar" name="Avatar" accept="image/*">
    </div>

    <div class="mb-3">
        <label for="role" class="form-label">Role:</label>
        <select class="form-select" id="role" name="Role" required>
            <option value="1" <?php if ($row['Role'] == 1) echo "selected"; ?>>Administrator</option>
            <option value="2" <?php if ($row['Role'] == 2) echo "selected"; ?>>Member</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="key" class="form-label">Account Status:</label>
        <select class="form-select" id="key" name="Key" required>
            <option value="1" <?php if ($row['Key'] == 1) echo "selected"; ?>>Locked</option>
            <option value="0" <?php if ($row['Key'] == 0) echo "selected"; ?>>Active</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i> Update
    </button>
</form>


<script>
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