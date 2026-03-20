<?php
$docRoot = __DIR__ . '/server';
$apiPath = 'deliverers';
$phpFile = $docRoot . '/api/' . $apiPath . '.php';
echo "Path: " . $phpFile . PHP_EOL;
echo "Exists: " . (file_exists($phpFile) ? 'YES' : 'NO') . PHP_EOL;

// Also test __DIR__ inside index.php context
$indexDir = $docRoot;
$phpFile2 = $indexDir . '/api/' . $apiPath . '.php';
echo "Path2: " . $phpFile2 . PHP_EOL;
echo "Exists2: " . (file_exists($phpFile2) ? 'YES' : 'NO') . PHP_EOL;
