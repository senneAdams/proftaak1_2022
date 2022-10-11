<?php

require 'backend/users.php';
session_start();


?>
<!DOCTYPE html>
<html>
<body>

<h2>login</h2>

<form method="post">
    <label for="email">email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password"> password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" name="submit" value="Submit">
</form>
</body>
<?php
if (isset($_POST['submit'])) {
    $users = new Users($_POST['email'], $_POST['password']);
    try {
        $login = $users->login();
    } catch (Exception $e){
        echo $e->getMessage();
    }
}
?>
</html>

