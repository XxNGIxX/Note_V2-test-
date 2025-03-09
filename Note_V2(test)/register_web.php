<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name =  $_POST["username"];
    $email =  $_POST["email"];
    $pwd =  $_POST["password"];
    $con_pwd =  $_POST["con_password"];
    $conn = mysqli_connect("localhost", "root", "", "suspicious_web_db");
    
    
    
    $user_sql = "SELECT email FROM user WHERE email='" . $email . "'";
    $rs_user = mysqli_query($conn, $user_sql);
    $user_num_rows = mysqli_num_rows($rs_user);
    if ($user_num_rows  > 0) {
        echo "<script>
                alert('อีเมลนี้ถูกใช้ไปแล้ว กรุณาใช้อีเมลอื่น!');
                window.location = 'register_web.php';
              </script>";
        die();
    } 

    $user_sql = "SELECT email FROM user WHERE username='" . $user_name . "'";
    $rs_user = mysqli_query($conn, $user_sql);
    $user_num_rows = mysqli_num_rows($rs_user);
    if ($user_num_rows  > 0) {
        echo "<script>
                alert('ชื่อ Username นี้ถูกใช้ไปเเล้ว กรุณาใช้ชื่อ Usernameอื่น!');
                window.location = 'register_web.php';
              </script>";
        die();
    } 
    
    


    $insert_sql = "INSERT INTO user(username, email, password) VALUES('$user_name', '$email', '$pwd')";
    $result = mysqli_query($conn, $insert_sql);
    header('location:login_web.php');

    mysqli_close($conn);
}
    
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <link rel="stylesheet" href="styles_web.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h2>Create an Account</h2>
            <form id="register-form" action="" method="POST">
                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="con_password">Confirm Password</label>
                    <input type="password" id="con_password" name="con_password" required>
                </div>
                <div class="input-group">
                    <button type="submit">Create Account</button>
                </div>
                <div class="options">
                    <a href="login_web.php">Already have an account? Log in</a>
                </div>
                <!-- Element สำหรับแสดงข้อความข้อผิดพลาด -->
                <p id="error-message" class="error-message"></p>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('register-form');
           
            form.addEventListener('submit', (event) => {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('con_password').value;

        
                if (password !== confirmPassword){
                event.preventDefault();
                alert("รหัสไม่ตรงกัน");
                    }
               

            });
        });
    </script>
</body>
</html>

