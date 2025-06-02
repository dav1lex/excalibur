<?php
// BCrypt Password Hash Generator

// Function to generate bcrypt hash
function generateBcryptHash($password, $cost = 10) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
}

// Function to verify hash
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Example usage
$password = "admin123";  // Change this to your desired password

echo "Password: " . $password . "\n";
echo "Generated Hash: " . generateBcryptHash($password) . "\n";

// Generate multiple examples
echo "\n--- Multiple Examples ---\n";
$passwords = ["admin123", "user123", "test123"];

foreach ($passwords as $pwd) {
    $hash = generateBcryptHash($pwd);
    echo "Password: $pwd\n";
    echo "Hash: $hash\n";
    echo "Verified: " . (verifyPassword($pwd, $hash) ? "✓" : "✗") . "\n";
    echo "---\n";
}

// Quick test with your example format
echo "\n--- Quick Generator ---\n";
echo "Enter your password and run this script to get bcrypt hash!\n";

// Uncomment below lines to generate hash for specific password
$myPassword = "admin123";
echo "Your Hash: " . generateBcryptHash($myPassword) . "\n";
?>