<?php
include 'components/connect.php';
session_start(); // Start the session

// Initialize user_id variable
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Default to null if not set

// Load Composer dependencies
require 'vendor/vendor/autoload.php'; // Ensure this path is correct

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

// Handle regular login
if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']); // Password hashing
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    // Check user credentials
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
    $select_user->execute([$email, $pass]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        $_SESSION['user_id'] = $row['id'];
        header('location:home.php');
        exit;
    } else {
        $message[] = 'Incorrect username or password!';
    }
}

// Handle Facebook login
if (isset($_GET['facebook'])) {
    $helper = $fb->getRedirectLoginHelper();
    try {
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        exit('Facebook SDK returned an error: ' . $e->getMessage());
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        exit('Facebook SDK returned an error: ' . $e->getMessage());
    }

    if (isset($accessToken)) {
        $_SESSION['facebook_access_token'] = (string) $accessToken;
        $response = $fb->get('/me?fields=id,name,email', $accessToken);
        $user = $response->getGraphUser();
        
        // Check if user exists in your DB
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$user->getEmail()]);
        if ($select_user->rowCount() == 0) {
            // Insert new user into the database
            $insert_user = $conn->prepare("INSERT INTO `users` (name, email) VALUES (?, ?)");
            $insert_user->execute([$user->getName(), $user->getEmail()]);
        }
        
        $_SESSION['user_id'] = $user->getId(); // Assuming 'id' is the user ID in your DB
        header('location:home.php');
        exit;
    }
}

// Handle Google login
if (isset($_GET['code'])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    $google_client->setAccessToken($token['access_token']);
    $google_service = new Google_Service_Oauth2($google_client);
    $user_info = $google_service->userinfo->get();
    
    // Check if user exists in your DB
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$user_info->email]);
    if ($select_user->rowCount() == 0) {
        // Insert new user into the database
        $insert_user = $conn->prepare("INSERT INTO `users` (name, email) VALUES (?, ?)");
        $insert_user->execute([$user_info->name, $user_info->email]);
    }
    
    $_SESSION['user_id'] = $user_info->id; // Adjust based on your DB structure
    header('location:home.php');
    exit;
}

// Generate login URLs
$login_url_google = $google_client->createAuthUrl();
$login_url_facebook = htmlspecialchars($fb->getRedirectLoginHelper()->getLoginUrl('http://your_redirect_uri/facebook'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <link rel="shortcut icon" type="x-icon" href="Logos/LogoTab.png">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .sso-buttons {
          display: flex;
          justify-content: space-between;
          margin-top: 10px;
      }

      .sso-buttons .btn {
          display: flex;
          align-items: center;
          padding: 10px 15px;
          border: none;
          border-radius: 5px;
          text-decoration: none;
          color: #fff;
          font-weight: bold;
          flex: 1;
          margin: 0 5px;
      }

      .sso-buttons .btn img {
          margin-right: 8px;
          width: 20px;
          height: 20px;
      }

      /* Facebook Button Style */
      .sso-buttons .btn.facebook {
          background-color: #4267B2;
      }

      .sso-buttons .btn.facebook:hover {
          background-color: #365899;
      }

      /* Google Button Style */
      .sso-buttons .btn.google {
          background-color: #DB4437;
      }

      .sso-buttons .btn.google:hover {
          background-color: #C33D2E;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <?php if (isset($message)): ?>
      <div class="message"><?= implode('<br>', $message); ?></div>
   <?php endif; ?>
   <form action="" method="post">
      <h3>Login Now</h3>
      <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Login Now" class="btn" name="submit">
      <p>Don't have an account?</p>
      <a href="user_register.php" class="option-btn">Register Now</a>
      <div class="sso-buttons">
         <a href="<?= $login_url_facebook; ?>" class="btn facebook">
            <img src="Logos/facebook-logo.png" alt="Facebook Logo"> Login with Facebook
         </a>
         <a href="<?= $login_url_google; ?>" class="btn google">
            <img src="Logos/google-logo.png" alt="Google Logo"> Login with Google
         </a>
      </div>
   </form>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
