<?php

namespace App\Utilidades;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
class BackBlaze
{
    //https://docs.digitalocean.com/products/spaces/how-to/use-aws-sdks/
    private $cliente;
    public function __construct()
    {
        $dominio = "backblazeb2.com";
        if(isset($_ENV['B2_DOMINIO'])) {
            $dominio = $_ENV['B2_DOMINIO'];
        }
        $this->cliente = new S3Client([
            'version' => 'latest',
            'region'  => $_ENV['B2_REGION'],
            'endpoint' => "https://s3.{$_ENV['B2_REGION']}.{$dominio}",
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key'    => $_ENV['B2_ACCESS_KEY_ID'],
                'secret' => $_ENV['B2_SECRET_ACCESS_KEY'],
            ],
        ]);
    }

    public function subirB64($rutaDestino, $data, $contentType) {
        try {
            $datos = [
                'Bucket' => $_ENV['B2_BUCKET_NAME'],
                'Key' => $rutaDestino,
                'Body' => $data,
                'ContentType' => $contentType,
            ];
            $result = $this->cliente->putObject($datos);
            $resultArray = $result->toArray();
            $detalle = json_encode($resultArray, JSON_PRETTY_PRINT);
            if (isset($result['@metadata']['statusCode'])) {
                $statusCode = $result['@metadata']['statusCode'];
                if($statusCode === 200) {
                    return [
                        'error' => false,
                        'detalle' => $detalle
                    ];
                } else {
                    return [
                        'error' => true,
                        'errorMensaje' => "El servicio de backblaze retorna status code {$statusCode}",
                        'detalle' => $detalle,
                    ];
                }
            } else {
                return [
                    'error' => true,
                    'errorMensaje' => 'El servicio de backblaze no retorna statusCode',
                    'detalle' => $detalle,
                ];
            }
        } catch (AwsException $e) {
            return [
                'error' => true,
                'errorMensaje' => "Error no controlado {$e->getMessage()}",
                'detalle' => $e->getMessage(),
            ];
        }
    }

    public function descargar($rutaDestino) {
        try {
            $result = $this->cliente->getObject([
                'Bucket' => $_ENV['B2_BUCKET_NAME'],
                'Key' => $rutaDestino,
            ]);
            $b64 = base64_encode($result['Body']);
            return [
                "error" => false,
                "b64" => $b64,
                "contenido" => $result['Body']
            ];
        } catch (AwsException $e) {
            return [
                "error" => true,
                "errorMensaje" => $e->getMessage()
            ];
        }
    }

    public function descargarV2($rutaBase, $tipo, $nombre) {
        try {
            if($rutaBase) {
                switch ($tipo) {
                    case "A":
                        $directorio = "archivo";
                        break;
                    case "G":
                        $directorio = "fichero";
                        break;
                    case "F":
                        $directorio = "firma";
                        break;
                    case "I":
                        $directorio = "imagen";
                        break;
                    default:
                        $directorio = "fichero";
                }
                $ruta = "$rutaBase/$directorio/$nombre";
                $result = $this->cliente->getObject([
                    'Bucket' => $_ENV['B2_BUCKET_NAME'],
                    'Key' => $ruta,
                ]);
                $b64 = base64_encode($result['Body']);
                return [
                    "error" => false,
                    "b64" => $b64,
                    "contenido" => $result['Body']
                ];
            } else {
                return [
                    "error" => true,
                    "errorMensaje" => "No esta definida la ruta base"
                ];
            }
        } catch (AwsException $e) {
            return [
                "error" => true,
                "errorMensaje" => $e->getMessage()
            ];
        }
    }
    public function eliminar($rutaDestino) {
        try {
            $result = $this->cliente->deleteObject([
                'Bucket' => $_ENV['B2_BUCKET_NAME'],
                'Key' => $rutaDestino,
            ]);
            return [
                "error" => false
            ];
        } catch (AwsException $e) {
            return [
                "error" => true,
                "errorMensaje" => $e->getMessage()
            ];
        }
    }
}