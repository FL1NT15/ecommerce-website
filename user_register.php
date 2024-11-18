<?php
include 'components/connect.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Load Composer dependencies
require 'vendor/vendor/autoload.php';

// Initialize Facebook SDK
$fb = new \Facebook\Facebook([
    'app_id' => '{your-app-id}', 
    'app_secret' => '{your-app-secret}',
    'default_graph_version' => 'v9.0',
]);

// Initialize Google Client
$google_client = new Google_Client();
$google_client->setClientId('{your-client-id}');
$google_client->setClientSecret('{your-client-secret}');
$google_client->setRedirectUri('http://your_redirect_uri');
$google_client->addScope("email");
$google_client->addScope("profile");

// Handle registration
if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$email]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    // Password validation
    $password_error = '';
    if (strlen($pass) < 8) {
        $password_error .= 'Password must be at least 8 characters long.<br>';
    }
    if (!preg_match('/[A-Z]/', $pass)) {
        $password_error .= 'Password must contain at least one uppercase letter.<br>';
    }
    if (!preg_match('/[a-z]/', $pass)) {
        $password_error .= 'Password must contain at least one lowercase letter.<br>';
    }
    if (!preg_match('/[0-9]/', $pass)) {
        $password_error .= 'Password must contain at least one number.<br>';
    }
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};":\\|,.<>\/?]+/', $pass)) {
        $password_error .= 'Password must contain at least one special character.<br>';
    }

    if ($select_user->rowCount() > 0) {
        $message[] = 'Email already exists!';
    } else {
        if ($password_error) {
            $message[] = $password_error; // Display error messages to the user
        } else {
            if ($pass != $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                $hashed_pass = sha1($pass); // Hashing the password
                $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
                $insert_user->execute([$name, $email, $hashed_pass]);
                $message[] = 'Registered successfully! Please log in now.';
            }
        }
    }
}

// Generate login URLs
$login_url_google = $google_client->createAuthUrl();
$login_url_facebook = htmlspecialchars($fb->getRedirectLoginHelper()->getLoginUrl('http://your_redirect_uri/facebook'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   
   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .sso-buttons {
          display: flex;
          justify-content: space-between;
          margin-top: 15px;
      }

      .sso-buttons .btn {
          display: flex;
          align-items: center;
          padding: 10px 15px;
          border: none;
          border-radius: 5px;
          text-decoration: none;
          color: white;
          flex: 1;
          margin: 0 5px;
          font-size: 16px;
          font-weight: 500;
          transition: background-color 0.3s ease;
      }

      /* Facebook Button */
      .sso-buttons .btn.facebook {
          background-color: #3b5998;
      }

      .sso-buttons .btn.facebook:hover {
          background-color: #2d4373;
      }

      /* Google Button */
      .sso-buttons .btn.google {
          background-color: #db4437;
      }

      .sso-buttons .btn.google:hover {
          background-color: #c23321;
      }

      .sso-buttons .btn img {
          margin-right: 8px;
          width: 20px;
          height: 20px;
      }

      /* Password feedback styles */
      .password-requirements {
          color: red;
          margin-top: 5px;
          font-size: 12px;
      }
      .password-requirements.success {
          color: green;
      }
   </style>

   <script>
      function validatePassword() {
          const pass = document.getElementById('password').value;
          const cpass = document.getElementById('confirm_password').value;
          const requirements = document.getElementById('password-requirements');
          let feedback = [];
          let allRequirementsMet = true;

          // Reset feedback messages
          requirements.innerHTML = '';

          // Check each requirement
          if (pass.length < 8) {
              feedback.push('• Password must be at least 8 characters long.');
              allRequirementsMet = false;
          }
          if (!/[A-Z]/.test(pass)) {
              feedback.push('• Password must contain at least one uppercase letter.');
              allRequirementsMet = false;
          }
          if (!/[a-z]/.test(pass)) {
              feedback.push('• Password must contain at least one lowercase letter.');
              allRequirementsMet = false;
          }
          if (!/[0-9]/.test(pass)) {
              feedback.push('• Password must contain at least one number.');
              allRequirementsMet = false;
          }
          if (!/[!@#$%^&*()_+\-=\[\]{};":\\|,.<>\/?]+/.test(pass)) {
              feedback.push('• Password must contain at least one special character.');
              allRequirementsMet = false;
          }
          if (pass !== cpass) {
              feedback.push('• Confirm password does not match.');
              allRequirementsMet = false;
          }

          // Display feedback
          if (feedback.length > 0) {
              requirements.innerHTML = feedback.join('<br>');
              requirements.classList.remove('success');
          } else {
              requirements.innerHTML = 'Password is strong!';
              requirements.classList.add('success');
          }
      }
   </script>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <?php if (isset($message)): ?>
      <div class="message"><?= implode('<br>', $message); ?></div>
   <?php endif; ?>

   <form action="" method="post">
      <h3>Register Now</h3>
      <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box">
      <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" id="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" oninput="validatePassword()">
      <input type="password" id="confirm_password" name="cpass" required placeholder="Confirm your password" maxlength="20" class="box" oninput="validatePassword()">
      <div class="password-requirements" id="password-requirements"></div>
      <input type="submit" value="Register Now" class="btn" name="submit">
      <p>Already have an account?</p>
      <a href="user_login.php" class="option-btn">Login Now</a>

      <div class="sso-buttons">
         <a href="<?= $login_url_facebook; ?>" class="btn facebook">
            <img src="Logos/facebook-logo.png" alt="Facebook Logo"> Register with Facebook
         </a>
         <a href="<?= $login_url_google; ?>" class="btn google">
            <img src="Logos/google-logo.png" alt="Google Logo"> Register with Google
         </a>
      </div>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
