<?php
// Quick test script to verify dvdoug/boxpacker is installed and autoloadable.
// Run: php .\tools\boxpacker_test.php  (after running composer install)

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "vendor/autoload.php not found. Run 'composer install' first.\n";
    exit(1);
}

require $autoload;

if (class_exists('DVDoug\\BoxPacker\\Packer')) {
    echo "BoxPacker is installed and autoloaded.\n";
    echo "Packer class available: DVDoug\\BoxPacker\\Packer\n";
    exit(0);
}

echo "BoxPacker classes not found after autoload. Did composer install succeed?\n";
exit(2);
