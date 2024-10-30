<?php


namespace App\Controller\Aplicacion\Empleado\Programacion;


use App\Controller\FuncionesController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProgramacionReporteController extends AbstractController
{
    #[Route("/turno/programacionreporte/lista", name:"turno_programacionreporte_lista")]
    public function lista(Request $request)
    {
        $arUsuario = $this->getUser();
        $parametros = ['identificacion' => $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $form = $this->createFormBuilder()
            ->getForm();
        $arrProgramacionReportes = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/turno/api/programacionreporte/lista");
        return $this->render('aplicacion/turno/programacionreporte/lista.html.twig', [
            'arrProgramacionReportes' => $arrProgramacionReportes,
            'form' => $form->createView()
        ]);
    }

    #[Route("/turno/programacionreporte/detalle/{id}", name:"turno_programacionreporte_detalle")]
    public function detalle(Request $request, $id)
    {
        $parametros = ['id' => $id];
        $arUsuario = $this->getUser();
        $arProgramacionReporte = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/turno/api/programacionreporte/detalle");
        return $this->render('aplicacion/turno/programacionreporte/detalle.html.twig', [
            'arProgramacionReporte' => $arProgramacionReporte,
        ]);
    }
}