<?php


namespace App\Controller\Aplicacion\Cliente\General;


use App\Controller\FuncionesController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InformacionController extends AbstractController
{
    /**
     * @Route("/cliente/general/informacion", name="cliente_general_informacion")
     */
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $parametros=[
            'codigoCliente' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url="/general/api/tercero/detalle";
        $arrTercero = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/cliente/general/informacion.html.twig',[
            'arrTercero' => $arrTercero
        ]);
    }
}