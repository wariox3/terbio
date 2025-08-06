<?php

namespace App\Controller;


final class FuncionesController
{


    private static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new FuncionesController();
        }
        return $instance;
    }

    public static function consumirApi($arEmpresa, $parametros, $api, $array = false)
    {
        $url = $arEmpresa->getUrlServicio() . $api;
        $datosJson = json_encode($parametros);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosJson);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$arEmpresa->getUsuarioServicio()}:{$arEmpresa->getClaveServicio()}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'WWW-Authenticate: Basic realm="User Visible Realm", charset="UTF-8"',
                'Content-Type: application/json',
                'Content-Length: ' . strlen($datosJson))
        );
        $respuesta = curl_exec($ch);
        curl_close($ch);
        if ($array) {
            $arrRespuesta = json_decode($respuesta, true);
        } else {
            $arrRespuesta = json_decode($respuesta);
        }

        return $arrRespuesta;
    }

    public static function obtenerIp()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

}