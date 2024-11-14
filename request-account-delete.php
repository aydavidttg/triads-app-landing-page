<?php

session_start();

if(isset($_SESSION['userData']) && !isset($_POST['confirmCheckbox']))
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triads - Data Deletion Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="material-icons mr-2">delete_forever</i> Triads Data Deletion Request</h5>
                </div>
                <div class="card-body">
                    <p>To request deletion of your Triads account and associated data, please confirm your understanding of the following:</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>Account Deletion:</b> This action will permanently delete your Triads account and you will no longer be able to access its features.</li>
                        <li class="list-group-item"><b>Data Deletion:</b>  This will erase all data associated with your account, including profile information, game progress, in-app purchases, esports data, tournament data, and also remove all your app tokens and app coins.</li>
                        <li class="list-group-item"><b>Data Retention:</b>We may retain some anonymized data for analytical purposes.</li>
                    </ul>
                    <form action="?submit" method="post">
                        <div class="form-group form-check mt-3">
                            <input type="checkbox" class="form-check-input" id="confirmCheckbox" name="confirmCheckbox" required>
                            <label class="form-check-label" for="confirmCheckbox">I understand the information above and wish to proceed with account and data deletion.</label>
                        </div>
                        <button type="submit" class="btn btn-danger" disabled id="deleteButton">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple JavaScript to enable the submit button only when the checkbox is checked
    const checkbox = document.getElementById('confirmCheckbox');
    const deleteButton = document.getElementById('deleteButton');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            deleteButton.disabled = false;
        } else {
            deleteButton.disabled = true;
        }
    });
</script>

</body>
</html>
<?php 
}
else {
    $userData = json_decode($_COOKIE['usd']);
    $userData->reason = $userData->reason ?? "N/A";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and retrieve user inputs
        $username = htmlspecialchars(trim($userData->username));
        $email = filter_var(trim($userData->email), FILTER_SANITIZE_EMAIL);
        $reason = htmlspecialchars(trim($userData->reason));
        $phone = htmlspecialchars(trim($userData->phone));
    
        // Validate required fields
        if (empty($username) || empty($email)) {
            echo "Username and email are required.";
            exit();
        }
    
        // Prepare the email message
        $subject = "Triads App: Account Deletion Request";
        $message = "
        New account deletion request received:
    
        Username: $username
        Email: $email
        Reason: $reason
        Phone: $phone
    
        Please respond within 24-96 hours.";
    
        $headers = [
            "From: TriadsApp <app@triadson.top>",
            "Reply-To: $email",
            "Content-Type: text/plain; charset=UTF-8"
        ];
    
        // Send the email
        $emailSent = file_put_contents("../../delete-requests", [PHP_EOL, PHP_EOL, $subject, $message . PHP_EOL, implode("\r\n", $headers), PHP_EOL], FILE_APPEND);
        
        // Prepare user data for the POST request
        $data = [
            'username' => $userData->username,
            'email' => $userData->email,
            'phone' => $userData->phone,
            'request_message' => "ACCOUNT_DELETION_REQUEST. Reason: " . ($userData->reason ?? 'No reason provided'),
        ];

        // Initialize cURL
        $ch = curl_init('https://triadson.top/api/web/v1/support-requests/create');

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        // Execute the request and capture the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Request Error: ' . curl_error($ch);
        } else {
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpStatus == 200 || 1) {
                ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triads - Data Deletion Request Received</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="material-icons mr-2">check_circle</i> Request Received</h5>
                </div>
                <div class="card-body">
                    <p>Thank you for submitting your data deletion request. We have received your request and will review it shortly.</p>
                    <p>If your data is deemed accurate, we will contact you at the email address associated with your Triads account (<b><?php echo $userData->email ?></b>) to confirm your identity and finalize the deletion process.</p>
                    <p>Please allow up to 24-96 hours for us to process your request.</p>
                    <p class="mb-0">If you have any questions, please contact us at app@triads.gg.</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php

            } else {

                $responseData = json_decode($response, true);
                $errors = $responseData['data']['errors'] ?? ['Unknown error occurred.'];
                // var_dump($errors, $data);
                // Format and display the errors
                echo "Failed to create support request.";
            }
        }

        // Close the cURL session
        curl_close($ch);
    }
}
?>

