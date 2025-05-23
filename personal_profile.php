<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('config.php');
include_once('library.php');

if (!isset($_SESSION['UserID'])) {
    die("Access denied. Please login first.");
}

$UserID = intval($_SESSION['UserID']);
$sql = "SELECT * FROM tbl_users WHERE UserID = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("User not found.");
}
$row = $result->fetch_assoc();
?>
<div class="card mt-4">
    <div class="card-header">Personal Profile</div>
    <div class="card-body">
        <form class="form w-100"
            action="index.php?do=personal_profile_process"
            method="post"
            enctype="multipart/form-data"
            novalidate>

            <input type="hidden" name="UserID" value="<?= htmlspecialchars($row['UserID']) ?>" />

            <div class="row mb-3">
                <div class="col-md-3 text-center">
                    <img src="uploads/avatars/<?= htmlspecialchars($row['Avatar']) ?>"
                        alt="Avatar"
                        class="img-thumbnail mb-2"
                        style="width: 100%; max-width: 200px; height: 200px; object-fit: contain;">
                    <input type="file"
                        class="form-control"
                        name="Avatar"
                        id="Avatar"
                        accept="image/*" />
                    <div class="invalid-feedback">Please select a valid image file.</div>
                </div>
                <div class="col-md-9">
                    <div class="mb-3">
                        <label for="FullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="FullName"
                            id="FullName" value="<?= htmlspecialchars($row['FullName']) ?>" required />
                        <div class="invalid-feedback">Full name cannot be blank.</div>
                    </div>

                    <div class="mb-3">
                        <label for="Email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="Email"
                            id="Email" value="<?= htmlspecialchars($row['Email']) ?>" required />
                        <div class="invalid-feedback">Valid email is required.</div>
                    </div>

                    <div class="mb-3">
                        <label for="Phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" name="Phone"
                            id="Phone" value="<?= htmlspecialchars($row['Phone']) ?>" />
                    </div>

                    <div class="mb-3">
                        <label for="Address" class="form-label">Address</label>
                        <textarea class="form-control" name="Address" id="Address" rows="3"><?= htmlspecialchars($row['Address']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="UserName" class="form-label">User Name</label>
                        <input type="text" class="form-control" id="UserName"
                            value="<?= htmlspecialchars($row['UserName']) ?>" disabled />
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

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



<!--?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('config.php');
include_once("library.php");

if (!isset($_SESSION['UserID'])) {
    die("Access denied. Please login first.");
}

$UserID = intval($_SESSION['UserID']);
$sql = "SELECT * FROM tbl_users WHERE UserID = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("User not found.");
}

$row = $result->fetch_assoc();
?>

<div class="card mt-4">
    <div class="card-header">Personal Profile</div>
    <div class="card-body">
        <form class="form" action="index.php?do=personal_profile_process" method="post">
            <input type="hidden" name="UserID" value="<?= htmlspecialchars($row['UserID']) ?>" />

            <div class="mb-3">
                <label for="FullName" class="form-label">Full Name:</label>
                <input type="text" class="form-control" name="FullName" id="FullName"
                       value="<?= htmlspecialchars($row['FullName']) ?>" required />
            </div>

            <div class="mb-3">
                <label for="UserName" class="form-label">User Name:</label>
                <input type="text" class="form-control" id="UserName"
                       value="<?= htmlspecialchars($row['UserName']) ?>" disabled />
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div-->