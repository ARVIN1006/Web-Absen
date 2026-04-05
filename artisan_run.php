<?php
set_time_limit(300);
$php = 'E:\\laragon\\bin\\php\\php-8.3.16-Win32-vs16-x64\\php.exe';
$artisan = __DIR__ . '\\artisan';
$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : 'list';
$fullCmd = escapeshellcmd($php) . ' ' . escapeshellarg($artisan) . ' ' . $cmd . ' --no-interaction 2>&1';
echo "<pre style='background:#111;color:#0f0;padding:20px;font-family:monospace;font-size:13px;'>";
echo "$ php artisan {$cmd}\n\n";
echo shell_exec($fullCmd);
echo "</pre>";
