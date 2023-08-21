<?php

declare(strict_types=1);

//shell_exec('find ./src -name "*.php" -exec md5sum {} \; > .toolbox.cache');

$files = shell_exec('md5sum -c .toolbox.cache 2>/dev/null');
var_dump(count(array_filter(explode("\n", $files), fn(string $line): bool => str_ends_with($line, ": FAILED"))));
