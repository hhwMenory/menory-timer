<?php
// echo posix_getpid(), "\n";

// $isDaemon = \swoole_process::daemon(true, true);

// var_dump($isDaemon);
// echo getcwd(), posix_getpid(), "\n";
// exit();

// file_put_contents('./data2._log', "ssssssssss\n", FILE_APPEND);

// \swoole_process::signal(SIGTERM, function ($signo) {
//     file_put_contents('./data2._log', "zzzzzzzzzz{$signo}\n", FILE_APPEND);
//     exit();
// });

// \swoole_process::signal(SIGCHLD, function ($signo) {
//     while ($res = \swoole_process::wait()) {
//         file_put_contents('./data2._log', "ssssssssss{$signo}\n", FILE_APPEND);
//     }
// });

$process = new \swoole_process('test1');
$process->start();

// while ($res = \swoole_process::wait()) {
//     var_dump($res);
// }

// var_dump($process);
// exit();

_log('parent process start');

// while ($result = $process->read()) {
//     var_dump($result);
// }
$result = $process->read();
_log($result);

_log('parent process end');

// \swoole_process::daemon();

// function test0()
// {
//     var_dump('xxxxxxxxxxxxxx');
// }

function test1($process)
{
    // swoole_event_add($process->pipe, function ($pipe) use ($process) {
    //     var_dump($process->read());
    // });
    _log('child process start');
    sleep(3);
    $process->write('test write');
    _log('child process end');
    $process->exit(0);

    // $i = 0;
    // while ($i < 10) {
    //     $process->write('[_log] '.$i);
    //     sleep(1);
    //     $i++;
    // }
    // while (true) {
    //     // var_dump(date('Y-m-d H:i:s'));
    //     file_put_contents('./data._log', date('Y-m-d H:i:s')."\n", FILE_APPEND);
    //     sleep(1);
    // }
}


function _log($msg)
{
    var_dump('[log] '.date('Y-m-d H:i:s').' : '.$msg);
}
