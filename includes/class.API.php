<?php
header("Content-Type: application/json");

require_once dirname(__FILE__) . '/class.Config.php';

class API extends Config
{
    const MISSING_PARAMS_MSG = 'Some Parameters are missing';
    const UNKNOWN_ERROR_MSG = 'Unknown Error Occured';
    const UNKNOWN_ERROR_CODE = 45;
    const MISSING_PARAMS_CODE = 12;
    const STATUS_OK_CODE = 200;

    public static function getAuthToken()
    {
        $headers = apache_request_headers();
        if (isset($headers['Auth'])) {
            return $headers['Auth'];
        }
        throw new Exception("auth token is missing " .  json_encode($headers), self::MISSING_PARAMS_CODE);
        return null;
    }

    public static function receivePOST(string $key, bool $important = false) : string
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

    public static function printError(Exception $e): void
    {
        echo json_encode([
            'status' => $e->getCode(),
            'message' => $e->getMessage(),
        ]);
    }

    public static function printSuccess(array $array = []): void
    {
        echo json_encode(array_merge($array, [
            'status' => self::STATUS_OK_CODE,
        ]));
    }
}
