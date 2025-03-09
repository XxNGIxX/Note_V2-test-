<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
session_start();

$conn = mysqli_connect("localhost", "root", "", "suspicious_web_db");

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $user_sh = "SELECT * FROM user WHERE username='" . $username . "'";
    $rs_sh = mysqli_query($conn, $user_sh);
    $num_rows = mysqli_num_rows($rs_sh);
    
    if($num_rows > 0){
        $pull_userinfo = mysqli_fetch_assoc($rs_sh);
        $pwd_ch = $pull_userinfo['password'];
            if($pwd_ch == $password){
                $id = $pull_userinfo['user_id'];
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
            die(header('location:test.php'));
            }
            else{
            echo "<script>
                    alert('รหัสผิด!');
                    window.location = 'login_web.php';
                </script>";

            }
    }
    else{
        echo "<script>
                    alert('ชื่อผู้ใช้ผิด!');
                    window.location = 'login_web.php';
                </script>";
    }
    
   
}
mysqli_close($conn);

}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles_web.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <button type="submit">Login</button>
                </div>
                <div class="options">
                    <a href="#">Forgot password?</a>
                    <a href="register_web.php">Create an account</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
