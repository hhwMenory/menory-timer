<?php
/**
 * @todo
 * @author menory <hhw_menory@yeah.net>
 * @date 2016-10-23 12:38:50
 */
namespace Menory\Timer;

class TimerManager
{
    protected static $pid;

    protected static $configFilePath = '/home/nginx/package/timer/conf/test.yaml';

    protected static $timerManagerPidPath = '/tmp/timer-manager.pid';

    protected static $configs = [];

    protected static $timerProcessPidPools = [];

    protected static function init()
    {
        date_default_timezone_set('PRC');

        if (!get_extension_funcs('yaml')) {
            Stdio::out('timer package need yaml extension！', Stdio::TYPE['warning']);
            exit(0);
        }

        if (!get_extension_funcs('swoole')) {
            Stdio::out('timer package need swoole extension！', Stdio::TYPE['warning']);
            exit(0);
        }

        self::$configs = yaml_parse_file(self::$configFilePath);

        Stdio::out("\n### system info\n", Stdio::TYPE['success']);
        Stdio::out("  - php version：".PHP_VERSION, Stdio::TYPE['success']);
        Stdio::out("  - swoole version：".SWOOLE_VERSION, Stdio::TYPE['success']);
        Stdio::out("  - timezone：".date_default_timezone_get(), Stdio::TYPE['success']);
        Stdio::out("  - current time：".date("Y-m-d H:i:s")."\n", Stdio::TYPE['success']);
        Stdio::out("  - timer-service pid file：".self::$timerManagerPidPath, Stdio::TYPE['success']);
        Stdio::out("  - timer-service config file：".self::$configFilePath."\n", Stdio::TYPE['success']);
    }

    public static function start()
    {
        \swoole_process::daemon(true, true);
        self::init();

        \swoole_process::signal(SIGUSR2, function($signal) {
            Stdio::out("timer service start stop", Stdio::TYPE['success']);
            // 通知子进程退出
            foreach (self::$timerProcessPidPools as $timerProcessPid => $timer) {
                \swoole_process::kill($timerProcessPid);
            }
            // 等待所有子进程退出
            \swoole_timer_tick(100, function() {
                if (count(self::$timerProcessPidPools) <= 0) {
                    Stdio::out("timer service has stopped", Stdio::TYPE['success']);
                    @unlink(self::$timerManagerPidPath);
                    exit(0);
                }
                usleep(100);
            });
        });

        \swoole_process::signal(SIGCHLD, function($signal) {
            while($result =  \swoole_process::wait(false)) {
                $timer = self::$timerProcessPidPools[$result['pid']];
                Stdio::out(
                    "timer process [{$timer}] has stopped",
                    Stdio::TYPE['warning']
                );

                // 移出进程池
                unset(self::$timerProcessPidPools[$result['pid']]);
            }
        });

        foreach (self::$configs as $timer => $config) {
            Stdio::out("### ".$timer."\n", Stdio::TYPE['success']);
            Stdio::out("  - time：".$config['time'], Stdio::TYPE['success']);
            Stdio::out("  - enabled：".($config['enabled'] ? 'true' : 'false')."\n", Stdio::TYPE['success']);

            if (!$config['enabled']) {
                continue;
            }

            $time = trim($config['time']);
            $reg  = '/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+'.
                    '((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+'.
                    '((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+'.
                    '((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+'.
                    '((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+'.
                    '((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i';
            if (!preg_match($reg, trim($time))) {
                Stdio::out('[warning] invalid time: '.$timer.'::$time = '.$time, Stdio::TYPE['warning']);
                continue;
                // exit(0);
            }

            $timerProcess = new \swoole_process(function ($process) use ($timer, $time) {
                $times   = preg_split("/[\s]+/i", trim($time));
                $second  = parseOneTime($times[0], 0, 59);
                $minutes = parseOneTime($times[1], 0, 59);
                $hours   = parseOneTime($times[2], 0, 23);
                $day     = parseOneTime($times[3], 1, 31);
                $month   = parseOneTime($times[4], 1, 12);
                $week    = parseOneTime($times[5], 0, 6);

                \swoole_timer_tick(1000, function ($timerId) use ($timer, $second, $minutes, $hours, $day, $month, $week) {
                    $currTime = time();
                    if (
                        in_array((int) date('s', $currTime), $second)  &&
                        in_array((int) date('i', $currTime), $minutes) &&
                        in_array((int) date('G', $currTime), $hours)   &&
                        in_array((int) date('j', $currTime), $day)     &&
                        in_array((int) date('w', $currTime), $week)    &&
                        in_array((int) date('n', $currTime), $month)
                    ) {
                        $timer::run();
                    }
                });

            });
            $timerProcessPid = $timerProcess->start();
            if ($timerProcessPid !== false) {
                self::$timerProcessPidPools[$timerProcessPid] = $timer;
            } else {
                Stdio::out("timer process [{$timer}] startup fail".count(self::$timerProcessPidPools), Stdio::TYPE['warning']);
            }
        }

        self::writePidFile();
        Stdio::out("### timed service startup succes，timer manager process pid is ".self::getPid()."\n", Stdio::TYPE['success']);
        Stdio::out("  - timer process num：".count(self::$timerProcessPidPools), Stdio::TYPE['success']);

    }

    public static function stop()
    {
    }

    public static function daemon()
    {
        \swoole_process::daemon(true, true);
    }

    public static function getPid()
    {
        self::$pid = posix_getpid();
        return self::$pid;
    }

    public static function writePidFile()
    {
        file_put_contents(self::$timerManagerPidPath, self::getPid());
    }

}


/*

### format
    \n\033[xx;xxm%s\033[0m

    0：黑
    1：深红
    2：绿
    3：黄色
    4：蓝色
    5：紫色
    6：深绿
    7：白色

    30-37：前景色
    40-37：背景色

    \33[4m：下划线
    \33[0m：关闭所有属性

 */

class Stdio
{
    // enum
    const TYPE = [
        'success' => 33,
        'warning' => 31,
        'info'    => 36
    ];

    const FORMAT = "\033[%dm%s\033[0m\n";

    public static function out($msg, $type) {
        if (!in_array($type, self::TYPE)) {
            fwrite(STDOUT, sprintf(self::FORMAT, self::TYPE['warning'], 'Stdio out type not\'t support'));
        }

        fwrite(STDOUT, sprintf(self::FORMAT, $type, $msg));
    }
}

function parseOneTime($val, $min, $max)
{
    $result = [];
    $v1     = explode(",", $val);
    foreach ($v1 as $v2) {
        $v3   = explode("/", $v2);
        $step = empty($v3[1]) ? 1 : $v3[1];
        $v4   = explode("-", $v3[0]);

        $rangeMin = count($v4) == 2 ? $v4[0] : ($v3[0] == "*" ? $min : $v3[0]);
        $ranegMax = count($v4) == 2 ? $v4[1] : ($v3[0] == "*" ? $max : $v3[0]);

        for ($i = $rangeMin; $i <= $ranegMax; $i += $step) {
            $result[] = (int) $i;
        }
    }
    // ksort($result);
    return $result;
}
