<?php

namespace App\Utilidades;

use SpacesAPI\Spaces;

class SpaceDO
{

    public function __construct()
    {

    }

    public function subir($rutaLocal, $rutaDestino, $codigoArchivoTipo, $mimeType) {
        //https://github.com/SociallyDev/Spaces-API
        try {
            $rutaDestino = "oxigeno/{$codigoArchivoTipo}/{$rutaDestino}";
            $spaces = new Spaces($_ENV['DO_CLAVE_ACCESO'], $_ENV['DO_CLAVE_SECRETA'], $_ENV['DO_REGION']);
            $my_space = $spaces->space($_ENV['DO_BUCKET']);
            $my_space->uploadFile($rutaLocal, $rutaDestino, $mimeType);
            return [
                'error' => false
            ];
        } catch (\Exception $e) {
            return  [
                'error' => true,
                'errorMensaje' => "Ocurrio un error con el Space"
            ];
        }
    }

    public function eliminar($rutaDestino, $codigoModelo) {
        try {
            $rutaDestino = "rubidio/{$codigoModelo}/{$rutaDestino}";
            $spaces = new Spaces($_ENV['DO_CLAVE_ACCESO'], $_ENV['DO_CLAVE_SECRETA'], $_ENV['DO_REGION']);
            $my_space = $spaces->space("semantica");
            $my_space->file($rutaDestino)->delete();
            return [
                'error' => false
            ];
        } catch (\Exception $e) {
            return  [
                'error' => true,
                'errorMensaje' => "Ocurrio un error con el Space"
            ];
        }
    }

    public function contenido($rutaDestino, $codigoArchivoTipo) {
        try {
            $rutaDestino = "rubidio/{$codigoArchivoTipo}/{$rutaDestino}";
            $spaces = new Spaces($_ENV['DO_CLAVE_ACCESO'], $_ENV['DO_CLAVE_SECRETA'], $_ENV['DO_REGION']);
            $my_space = $spaces->space("semantica");
            $contenido = $my_space->file($rutaDestino)->getContents();
            return [
                'error' => false,
                'contenido' => $contenido
            ];
        } catch (\Exception $e) {
            return  [
                'error' => true,
                'errorMensaje' => "Ocurrio un error con el Space"
            ];
        }
    }
}