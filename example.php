<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/sagittarius.php');

// Initialize Rabbit with an *insecure* key (OPTIONAL)
$rabbit = new Rabbit(str_repeat('A', 16));

// Initialize the obfuscater with GZ-Compression level-9
//  Default Compression Level: 5
$obfuc = new Obfuscate(9);

// Read PHP code file contents into $code and trim PHP tags.
$code = trim(file_get_contents(__ROOT__.'/code_sample.php'), '<?php?>');

// Encrypt the code and provide a self-contained obfuscated script
$encrypted = $obfuc->encode_contained($code, $rabbit);

// Only provide a self-contained obfuscated script
$obfuscated = $obfuc->encode_contained($code);

echo "=== Encrypted ===\n", $encrypted, "=== Encrypted ===\n\n";
echo "=== Obfuscated ===\n", $obfuscated, "=== Obfuscated ===\n";
?>
