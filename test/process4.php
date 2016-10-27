<?php
dump('parent process start');

$pid = posix_getpid();
dump('current process pid: '.$pid);

// 基于 signalfd 和 eventloop 是异步 IO，不能用于同步程序中
\swoole_process::signal(SIGUSR1, function($signo) {
    dump('running signal: usr1');
    exit(0);
});

// 若将进程阻塞则会
// while (true) {
// }

// php process.php 
// kill -USR1 12863
// string(48) "[log] 2016-10-21 12:05:25 : parent process start"
// string(54) "[log] 2016-10-21 12:05:25 : current process pid: 12863"
// User defined signal 1

dump('parent process end');

function dump($msg)
{
    var_dump('[log] '.date('Y-m-d H:i:s').' : '.$msg);
}

// php process.php 
// kill -USR1 14483
// string(48) "[log] 2016-10-21 12:30:22 : parent process start"
// string(54) "[log] 2016-10-21 12:30:22 : current process pid: 14483"
// string(46) "[log] 2016-10-21 12:30:22 : parent process end"
// string(48) "[log] 2016-10-21 12:30:27 : running signal: usr1"
