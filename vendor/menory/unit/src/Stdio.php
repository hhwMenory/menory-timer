<?php
/**
 * @todo 标准输入输出类
 * @author menory <hhw.menory@gmail.com | hhw_menory@yeah.net>
 * @date 2016-10-29 01:12:40
 */
namespace Menory\Unit;

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

    public static function out($msg, $type = self::TYPE['success']) {
        if (!in_array($type, self::TYPE)) {
            fwrite(STDOUT, sprintf(self::FORMAT, self::TYPE['warning'], 'Stdio out type not\'t support'));
        }

        fwrite(STDOUT, sprintf(self::FORMAT, $type, $msg));
    }
}