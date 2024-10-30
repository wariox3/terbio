<?php


namespace App\Controller\Aplicacion\Cliente\Operacion;


use App\Controller\FuncionesController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsignaController extends AbstractController
{
    #[Route("/cliente/operacion/consigna/lista", name:"cliente_operacion_consigna_lista")]
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $parametros=[
            'codigoCliente' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url="/turno/api/operacion/consigna/lista";
        $arrConsignas = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/cliente/operacion/consigna/lista.html.twig',[
            'arrConsignas' => $arrConsignas
        ]);
    }
}