<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 11:27 PM
 */

namespace restApi\core;


class ResponseCollector
{
    public static function buildJSON($data){
        header("Content-Type: application/json;charset=utf-8");
        if (isset($data['fail_status'])){
            http_response_code($data['fail_status']);
        }
        $json = json_encode($data);

        if ($json === false) {
            // Avoid echo of empty string (which is invalid JSON), and
            // JSONify the error message instead:
            $json = json_encode(array("jsonError", json_last_error_msg()));
            if ($json === false) {
                $json = '{"jsonError": "unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        }

        echo $json;
    }

    /**
     * @param $e \Exception
     */
    public static function buildFailure(\Exception $e){
        self::buildJSON([
            'success' => false,
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],

        ]);
    }
}