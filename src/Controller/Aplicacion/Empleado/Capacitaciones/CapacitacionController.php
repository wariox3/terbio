<?php


namespace App\Controller\Aplicacion\Empleado\Capacitaciones;


use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CapacitacionController extends AbstractController
{
    /**
     * @Route("/empleado/capacitaciones/pendientes/lista", name="empleado_capacitaciones_pendites_lista")
     */
    public function pendiente(Request $request,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $parametros = ['identificacion'=> $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion'=> $arUsuario->getNumeroIdentificacion()];

        if($form->isSubmitted()){
            if ($request->request->get('btnConfirmarAsistencia')) {
                $codigoCapacitacion = $request->request->get('btnConfirmarAsistencia');
                $parametros['codigoCapacitacionDetalle'] = $codigoCapacitacion;
                $url = '/recursohumano/api/capacitaciones/empleado/confirmar/asistencia';
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
                if ($respuesta){
                    Mensajes::info("Gracias por confirmar su asistencia a la capacitación");
                } else{
                    Mensajes::error("error");
                }
            }
        }
        $url="/recursohumano/api/capacitaciones/empleado/pendiente";
        $arrCapatitaciones = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/empleado/capacitaciones/pendientes/lista.html.twig', [
            'arrCapatitaciones' => $arrCapatitaciones->capacitaciones,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/empleado/capacitaciones/pendientes/confirmarAsistencia/{codigoCapacitacionDetelle}", name="empleado_capacitaciones_pendites_confirmarasistencia")
     */
    public function confirmarAsistencia(Request $request, $codigoCapacitacionDetelle)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoCapacitacionDetalle' => $codigoCapacitacionDetelle,
            'ip' => FuncionesController::obtenerIp()
        ];
        $url = '/recursohumano/api/capacitaciones/empleado/confirmar/asistencia';
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if ($respuesta){
            Mensajes::info("Gracias por confirmar su asistencia a la capacitación");
        } else{
            Mensajes::error("error");
        }
        return $this->redirect($this->generateUrl('inicio'));
    }


}