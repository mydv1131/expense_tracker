<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" href="assets\wallet.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    height: 100dvh;
    width: 100dvw;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, rgb(0, 0, 0),black);
}

#h2 {
    color: whitesmoke;
    position: relative;
}

form {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    padding: 2em;
    border-radius: 1em;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.99);
    display: flex;
    flex-direction: column;
    width: 100%;
    align-items: start;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 0.5em;
    margin-top: 0.5em;
    border-radius: 5px;
    border: 1px solid #ccc;
}

#main {
    width: 35%;
    margin: auto;
    padding: 2rem;
    border-radius: 2rem;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    align-items: center;
    justify-content: center;
    text-align: center;
    display: flex;
    flex-direction: column;
}

/* Responsive styles */
@media (max-width: 1024px) {
    #main {
        width: 60%;
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    #main {
        width: 80%;
        padding: 1rem;
    }

    form {
        padding: 1.5em;
    }
}

@media (max-width: 480px) {
    #main {
        width: 95%;
        padding: 1rem;
    }

    form {
        padding: 1em;
    }
}

</style>
<body>

<div id="main">
<h2 id="h2">Let's Login <br> Your Money's Story: Start Here.</h2>
    <form method="post">
        <label for="username" class="my-2 text-light">Username:</label>
        <input type="text" name="username" id="username" required><br>
        <label for="password" class="my-2 text-light">Password:</label>
        <input type="password" name="password" id="password" required><br>
        <button type="submit" class="btn btn-success my-2 ">Login</button>
    </form>
    <h6 class="text-light my-2">New here! <a href="register.php">Register</a></h6>
</div>
</body>
</html>
