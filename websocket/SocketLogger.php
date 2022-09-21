<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/01/20 10:44
 */
declare(strict_types=1);

namespace Websocket;

class SocketLogger
{
    public const DEBUG = 0;
    public const INFO = 2;
    public const WARN = 6;
    public const ERROR = 8;
    public const LOG = 99;
    protected static array $words = [
        0 => 'DEBUG',
        2 => 'INFO',
        6 => 'WARN',
        8 => 'ERROR',
        99 => 'LOG',
    ];
    private static int $logLevel = self::DEBUG;
    public static string $dateFormat = 'd.M H:i:s.';

    public static function initByEnv($env): void
    {
        $isDebug = $env['APP_DEBUG'] ?? false;
        if (!$isDebug) {
            $isDebug = $env['DEBUG'] ?? false;
        }
        if (!$isDebug) {
            $isDebug = $env['APP_ENV'] === 'dev';
        }
        if ($isDebug) {
            self::setLogLevel(self::DEBUG);
        } else {
            self::setLogLevel(self::INFO);
        }
    }

    public static function setLogLevel(int $logLevel): void
    {
        self::$logLevel = $logLevel;
    }

    /**
     * Always log
     * @param string $message
     * @param int $severity
     * @return void
     */
    public static function log(string $message, int $severity = self::LOG): void
    {
        //This one is much faster than using \DateTime->format()
        $t = microtime();
        printf('[%s][%s] %s' . PHP_EOL, (date(self::$dateFormat, (int)substr($t, -10))) . substr($t, 2, 5), str_pad(self::$words[$severity], 4), $message);
    }

    public static function warn(string $message): void
    {
        if (self::$logLevel > self::WARN) {
            return;
        }
        self::log($message, self::WARN);
    }

    public static function info(string $message): void
    {
        if (self::$logLevel > self::INFO) {
            return;
        }
        self::log($message, self::INFO);
    }

    public static function debug(string $message): void
    {
        if (self::$logLevel > self::DEBUG) {
            return;
        }
        self::log($message, self::DEBUG);
    }

    public static function error(string $message): void
    {
        if (self::$logLevel > self::ERROR) {
            return;
        }
        self::log($message, self::ERROR);
    }
}
