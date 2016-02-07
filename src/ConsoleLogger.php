<?php

namespace PhpCluster;


/**
 * Class ConsoleLogger
 */
class ConsoleLogger
{
    const TYPE_INFO     = 'info';
    const TYPE_ERROR    = 'error';
    const TYPE_DEBUG    = 'debug';
    const TYPE_WARNING  = 'warning';

    /**
     * Output info message.
     *
     * @param $msg
     */
    public function info($msg)
    {
        $this->log($msg, self::TYPE_INFO);
    }

    /**
     * Output debug message.
     *
     * @param $msg
     */
    public function debug($msg)
    {
        $this->log($msg, self::TYPE_DEBUG);
    }

    /**
     * Output error message.
     *
     * @param $msg
     */
    public function error($msg)
    {
        $this->log($msg, self::TYPE_ERROR);
    }

    /**
     * Output warning message.
     *
     * @param $msg
     */
    public function warning($msg)
    {
        $this->log($msg, self::TYPE_WARNING);
    }

    /**
     * Output message.
     *
     * @param $msg
     * @param $type
     */
    public function log($msg, $type)
    {
        $labels = [
            self::TYPE_INFO     => 'INFO....',
            self::TYPE_WARNING  => 'WARNING',
            self::TYPE_DEBUG    => 'DEBUG...',
            self::TYPE_ERROR    => 'ERROR...'
        ];

        echo sprintf('%s [%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), $labels[$type], $msg);
    }
}