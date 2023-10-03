<?php


namespace App\Controller\Aplicacion\Cliente\Transporte;

use App\Controller\FuncionesController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class ApiGuiaController  extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/cliente/transporte/api/guia/condicion", name="cliente_transporte_guia_condicion")
     */
    public function condicionEspecial(Request $request) {
        $arUsuario = $this->getUser();
        $raw = json_decode($request->request->get("arrParametros"));
        $arrDatos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $raw,"/transporte/api/oxigeno/condicion", true);
        return $arrDatos;
    }

}