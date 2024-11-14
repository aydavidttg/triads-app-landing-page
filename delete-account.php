<?php
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL);
if(isset($_POST['username']) && !empty($_POST['username']))
{
    if(isset($_POST['email']) && !empty($_POST['email'])){
        session_start();
        $_SESSION['userData'] = [
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'phone' => $_POST['phone'],
            'reason' => $_POST['reason'],
        ];
        setcookie("usd", json_encode($_SESSION['userData']), time() + 3600);
        header("location: request-account-delete.php");
    }
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deletion Request - Triads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-danger text-white text-center">
                <h2>Account Deletion Request</h2>
                <p class="mb-0">Submit your request to delete your Triads account and associated data</p>
            </div>
            <div class="card-body p-5">
                <form id="deletion-form" action="?submit" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Enter your Triads username">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="phone" class="form-control" id="phone" name="phone" required placeholder="Enter your phone number with country code">
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Deletion (Optional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Let us know why you're leaving..."></textarea>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <strong>Important:</strong> Deleting your account will remove:
                        <ul>
                            <li>Your profile information, including username, email, and gaming handles.</li>
                            <li>Gameplay history, friend lists, and in-app interactions.</li>
                            <li>Data collected for analytics and personalization.</li>
                        </ul>
                        <p class="mb-0">Certain information may be retained for legal or fraud-prevention purposes.</p>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="confirmation" required>
                        <label class="form-check-label" for="confirmation">
                            I understand that my account and data will be permanently deleted.
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg">Request Deletion</button>
                    </div>
                </form>
            </div>

            <div class="card-footer text-center">
                <p class="mb-1">Need help? <a href="mailto:app@triads.gg">Contact Support</a></p>
                <p class="small text-muted">This form complies with Google Play policies. Visit <a href="https://app.triadson.top/privacy-policy.html">our privacy policy</a> for more information.</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}