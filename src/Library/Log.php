<?php
function logHandler($data)
{
    file_put_contents(
        '/tmp/lw/lw.log',
        sprintf('[%s][%s]%s%s',posix_getpid(),
            microtime(true),
            var_export($data, true),
            PHP_EOL
        ),
        FILE_APPEND);
}