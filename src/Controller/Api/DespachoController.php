<?php

namespace App\Controller\Api;

use App\Controller\FuncionesController;
use App\Entity\RespuestaElectronico;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class DespachoController extends AbstractFOSRestController
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

    /**
     * @param Request $request
     * @param int $codigoGuia
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @Rest\Post("/api/despacho/guia/adicionar", name="api_despacho_guia_adicionar")
     */
    public function adicionarDespacho(Request $request)
    {
        $arUsuario = $this->getUser();
        $parametros = json_decode($request->request->get("arrParametros"), true);
        $parametros['codigoTercero'] = $arUsuario->getCodigoTerceroErpFk();
        return FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/guia/adicionar");
    }

    /**
     * @param Request $request
     * @param int $codigoGuia
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @Rest\Post("/api/despacho/guia/adicionar/guia", name="api_despacho_guia_adicionar_guia")
     */
    public function agregarGuia(Request $request)
    {
        $arUsuario = $this->getUser();
        $parametros = json_decode($request->request->get("arrParametros"), true);
        $parametros['codigoTercero'] = $arUsuario->getCodigoTerceroErpFk();
        return FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/transporte/api/oxigeno/despacho/guia/adicionar");
    }

}
