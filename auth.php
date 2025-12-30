<?php
session_start();
$conn = new mysqli('localhost','root','','cjsa_files');

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE username='$username'");
    if($res->num_rows > 0){
        $user = $res->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
<style>
    body {
        background-color: #000; /* Black background */
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .login-box {
        background-color: #111; /* Slightly lighter black for the box */
        color: #fff; /* White text */
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(255,255,255,0.1);
        width: 350px;
        text-align: center;
    }

    input[type="text"], input[type="password"] {
        padding: 10px;
        margin: 10px 0;
        width: 100%;
        border-radius: 5px;
        border: none;
    }

    button {
        background-color: #22c55e;
        color: #fff;
        padding: 10px 20px;
        margin-top: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }

    button:hover {
        background-color: #16a34a;
    }

    .error {
        color: #f87171; /* Red error message */
        margin-top: 10px;
    }

    h2 {
        margin-bottom: 20px;
    }
</style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
    </div>
</body>
</html>
