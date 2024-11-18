<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit();
}

if (isset($_POST['submit'])) {
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $pass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING);
    $cpass = filter_var(trim($_POST['cpass']), FILTER_SANITIZE_STRING);
    $role_id = filter_var($_POST['role_id'], FILTER_SANITIZE_NUMBER_INT);

    if (empty($name)) {
        $message[] = 'Username cannot be empty!';
    } elseif (strlen($name) > 20) {
        $message[] = 'Username cannot exceed 20 characters!';
    } elseif ($pass !== $cpass) {
        $message[] = 'Confirm password does not match!';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $pass)) {
        $message[] = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
        $select_admin->execute([$name]);

        if ($select_admin->rowCount() > 0) {
            $message[] = 'Username already exists!';
        } else {
            $insert_admin = $conn->prepare("INSERT INTO `admins` (name, password, role_id) VALUES (?, ?, ?)");
            if ($insert_admin->execute([$name, $hashed_pass, $role_id])) {
                // Redirect to success page or show success message
                header('location: success.php?message=Admin registered successfully');
                exit();
            } else {
                $message[] = 'Registration failed, please try again!';
            }
        }
    }
}

$select_roles = $conn->prepare("SELECT * FROM roles");
$select_roles->execute();
$roles = $select_roles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container .btn {
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container .btn:hover {
            background: #2980b9;
        }
        .message {
            margin: 10px 0;
            color: red;
            text-align: center;
        }
        .password-strength {
            margin: 10px 0;
            font-size: 12px;
            color: #888;
        }
        .strength-weak { color: red; }
        .strength-medium { color: orange; }
        .strength-strong { color: green; }
    </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">
    <form action="" method="post" onsubmit="return validateForm()">
        <h3>Register Now</h3>
        
        <!-- Display messages to the user -->
        <?php if (isset($message)): ?>
            <div class="message">
                <?php foreach ($message as $msg): ?>
                    <p><?= htmlspecialchars($msg); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" id="password" oninput="checkPasswordStrength()">
        <div class="password-strength" id="password-strength"></div>
        <input type="password" name="cpass" required placeholder="Confirm your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">

        <label for="role_id">Select Role:</label>
        <select name="role_id" id="role_id" required class="box">
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id']; ?>"><?= htmlspecialchars($role['role_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Register Now" class="btn" name="submit">
    </form>
</section>

<script src="../js/admin_script.js"></script>
<script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const cpass = document.querySelector('input[name="cpass"]').value;

        if (password !== cpass) {
            alert('Confirm password does not match!');
            return false;
        }
        return true;
    }

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthText = document.getElementById('password-strength');
        
        let strength = 'Weak';
        let colorClass = 'strength-weak';

        if (password.length >= 8) {
            strength = 'Medium';
            colorClass = 'strength-medium';
            if (/(?=.*[A-Z])(?=.*\d)/.test(password)) {
                strength = 'Strong';
                colorClass = 'strength-strong';
            }
        }

        strengthText.innerHTML = `Password Strength: <strong class="${colorClass}">${strength}</strong>`;
    }
</script>
</body>
</html>