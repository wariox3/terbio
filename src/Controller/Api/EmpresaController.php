<?php

namespace App\Controller\Api;

use App\Entity\RespuestaElectronico;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class EmpresaController extends AbstractFOSRestController
{

    /**
     * @return array
     * @Rest\Post("/api/empresa/respuestafe")
     */
    public function respuestaFacturaElectronica(Request $request,  EntityManagerInterface $em)
    {
        try {
            $raw = json_decode($request->getContent(), true);
            $codigoEmpresa = $raw['codigoEmpresa'] ?? null;
            $modelo = $raw['modelo'] ?? null;
            $codigoFactura = $raw['codigoFactura'] ?? null;
            if ($codigoEmpresa && $modelo && $codigoFactura) {
                return $em->getRepository(RespuestaElectronico::class)->apiConsultaEmpresa($codigoEmpresa, $modelo, $codigoFactura);
            } else{
                return [
                    'error' => true,
                    'errorMensaje' => 'Faltan parametros para el consumo de la api'];
            }

        } catch (\Exception $e) {
            return [
                'error' => "Ocurrio un error en la api ",
            ];
        }
    }



}
