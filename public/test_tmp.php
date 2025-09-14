<?php
$tmpDir = __DIR__ . '/../tmp';
if (is_dir($tmpDir) && is_writable($tmpDir)) {
    echo "Directory $tmpDir is writable!";
} else {
    echo "Directory $tmpDir is NOT writable or does not exist.";
}
?>