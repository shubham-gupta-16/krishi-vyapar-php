<?php
header("Content-Type: application/json");
require_once dirname(__FILE__) . '/config.php';

class API
{
    const MISSING_PARAMS_MSG = 'Some Parameters are missing';
    const UNKNOWN_ERROR_MSG = 'Unknown Error Occured';
    const UNKNOWN_ERROR_CODE = 45;
    const MISSING_PARAMS_CODE = 12;
    const DB_CONFIG_ERROR_CODE = 29;
    const STATUS_OK_CODE = 200;


    static $startTime = 0;
    static $db = null;


    public static function initResponseTime(): void
    {
        if (self::$startTime ==  0) {
            self::$startTime = microtime(true) * 1000;
        }
    }
    public static function getResponseTime(): float
    {
        $time = microtime(true) * 1000;
        return $time - self::$startTime;
    }

    public static function getAuthToken()
    {

        $headers = apache_request_headers();
        if (isset($headers['Auth'])) {
            return $headers['Auth'];
        }
        throw new Exception("auth token is missing " .  json_encode($headers), self::MISSING_PARAMS_CODE);
        return null;
    }

    public static function db(): mysqli
    {
        if (self::$db == null) {
            $db = new mysqli(dbservername, dbuser, dbpassword, dbname);
            $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE);
            self::$db = $db;
        }
        return self::$db;
    }

    public static function receivePOST(string $key, bool $important = false): ?string
    {
        // todo remove get => 
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if ($important)
            throw new Exception("$key parameter is missing", self::MISSING_PARAMS_CODE);
        return null;
    }

    public static function receiveGET(string $key, bool $important = false): string
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        if ($important)
            throw new Exception("$key parameter is missing", self::MISSING_PARAMS_CODE);
        return null;
    }

    /* public static function receivePOST(string $key)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return null;
    } */

    public static function getCurrentTimeForMySQL(): string
    {
        return date('Y-m-d H:i:s', time());
    }

    public static function printError(Exception $e, string $message = null, int $code = -1): void
    {
        echo json_encode([
            'status' => $code != -1 ? $e->getCode() : $code,
            'message' => $message != null ? $e->getMessage() : $message,
        ]);
    }

    public static function printSuccess(array $array = []): void
    {
        echo json_encode(array_merge([
            'status' => self::STATUS_OK_CODE,
        ], $array));
    }
}
