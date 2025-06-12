<?php
// BCrypt pw generate
// run before inserting db
// generate
function generateBcryptHash($password, $cost = 10) { //cost = lenght
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
}

$password = "test123";  // change this

echo "Password: " . $password . "\n";
echo "Generated Hash: " . generateBcryptHash($password) . "\n";

?>