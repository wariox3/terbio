<?php
namespace App\Servicios;

class Correo
{
    public function enviarCorreo($correo = null, $asunto = null, $mensaje = null, $operador = null)
    {
        if($correo) {
            $datosJson = json_encode([
                "correo" => $correo,
                "asunto" => $asunto,
                "contenido" => $mensaje,
                "operador" => $operador
            ]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://zinc.semantica.com.co/api/correo/html');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datosJson);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($datosJson))
            );
            $respuesta = curl_exec($ch);
            curl_close($ch);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } else {
            return false;
        }
    }
}
