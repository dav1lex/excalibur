<?php
// BCrypt pw generate
// run before inserting db
// generate
function generateBcryptHash($password, $cost = 10) { //cost = lenght
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
}

$password = "admin123";  // change this
// $2y$10$554AKK1E3opnO0gBa70c1u6I4t4ysNLdpC4JQJ7GIMsqGSH/9xuPe = admin123
echo "Password: " . $password . "\n";
echo "Generated Hash: " . generateBcryptHash($password) . "\n";

?>