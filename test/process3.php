<?php
dump('parent process start');

$process = new \swoole_process('test');
$process->start();

$result = \swoole_process::wait();
dump(sprintf('PID: %d code: %d signal: %d', 
    $result['pid'], 
    $result['code'], 
    $result['signal']
));

// \swoole_process::signal(SIGCHLD, function($signal) {
//     while($result =  swoole_process::wait(false)) {
//         dump(sprintf('PID: %d code: %d signal: %d', 
//             $result['pid'],
//             $result['code'],
//             $result['signal']
//         ));
//     }
// });

dump('parent process end');

// \swoole_event_add($process->pipe, function ($pipe) use($process) {
//     dump(strlen($process->read()));
//     // swoole_event_del($pipe);
// });




// 执行结果
// php process.php 
// string(48) "[log] 2016-10-20 18:00:04 : parent process start"
// string(47) "[log] 2016-10-20 18:00:04 : child process start"
// string(45) "[log] 2016-10-20 18:00:07 : child process end"
// string(38) "[log] 2016-10-20 18:00:07 : test write"
// string(46) "[log] 2016-10-20 18:00:07 : parent process end"

function test($process)
{
    // \swoole_event_add($process->pipe, function () {

    // }, function ($pipe) use($process) {
    //     dump('ssss');
    //     // swoole_event_del($pipe);
    // });
    dump('child process start');
    sleep(3);
    // for ($i = 0; $i < 3; $i++) {
        // $process->write(str_repeat('#', 100000));
    //     usleep(100);
    // }
    // \swoole_event_write($process->pipe, str_repeat('#', 100000));
    dump('child process end');
    // $process->exit(0);
}

function dump($msg)
{
    var_dump('[log] '.date('Y-m-d H:i:s').' : '.$msg);
}
