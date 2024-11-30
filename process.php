<?php
session_start();

$database_file = 'database.txt';

function load_users($database_file) {
    $users = array();
    if (file_exists($database_file)) {
        $file = fopen($database_file, 'r');
        while (($line = fgets($file)) !== false) {
            list($username, $hashed_password) = explode(',', trim($line));
            $users[$username] = $hashed_password;
        }
        fclose($file);
    }
    return $users;
}

function save_user($database_file, $username, $hashed_password) {
    $file = fopen($database_file, 'a');
    fwrite($file, "$username,$hashed_password\n");
    fclose($file);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $users = load_users($database_file);

    switch ($action) {
        case 'login':
            $username = $_POST['username'];
            $password = $_POST['password'];
            if (isset($users[$username]) && password_verify($password, $users[$username])) {
                $_SESSION['loggedin'] = true;
                header('Location: templates/main.html');
                exit();
            } else {
                echo "Invalid credentials. Please try again.";
            }
            break;
        
        case 'register':
            $username = $_POST['username'];
            $password = $_POST['password'];
            if (isset($users[$username])) {
                echo "User already exists. Please log in.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                save_user($database_file, $username, $hashed_password);
                echo "Registration successful! Please <a href='templates/login.html'>log in</a>.";
            }
            break;
        
        case 'forget_password':
            $username = $_POST['username'];
            if (isset($users[$username])) {
                echo "Password retrieval is not possible because passwords are securely hashed.";
            } else {
                echo "User not found. Please register.";
            }
            break;
    }
}
?>
