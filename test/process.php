<?php
dump('parent process start');

$process = new \swoole_process('test1');
$process->start();

$i = 0;
\swoole_event_add($process->pipe, null, function ($pipe) use ($process, &$i) {
    dump('write callback running');

    $process->write(str_repeat('#', 10));
    dump('write num:'.++$i);
    usleep(10000);

    // swoole_event_del($pipe);
}, SWOOLE_EVENT_WRITE);


\swoole_process::signal(SIGCHLD, function($signal) {
    $result = \swoole_process::wait();
    dump('child process pid '.$result['pid'].' exit');
    // exit(); // 子进程退出，父进程也退出
});

dump('parent process end');

function test1($process)
{
    dump('child process start');

    $i = 0;
    \swoole_event_add($process->pipe, function($pipe) use ($process, &$i) {
        if ($i < 300) {
            dump('read: '.($process->read()).' '.$i++);
            usleep(500000);            
        } else if ($i < 400) {
            dump('read: wait '.$i++);
            usleep(500000);            
        } else if ($i < 450) {
            dump('read: '.($process->read()).' '.$i++);
            usleep(500000);                 
        } else {
            swoole_event_del($pipe);
        }
    });

    dump('child process end');
}

function dump($msg)
{
    var_dump('[log] '.date('Y-m-d H:i:s').' : '.$msg);
}
