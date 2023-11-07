<?php


namespace App\Controller\Aplicacion\Empleado\Reclamo;


use App\Controller\FuncionesController;
use App\Servicios\Correo;
use App\Utilidades\Mensajes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReclamoController extends AbstractController
{
    /**
     * @Route("/empleado/reclamo/lista", name="empleado_reclamo_lista")
     */
    public function lista(Request $request)
    {
        $arUsuario = $this->getUser();
        $parametros = ['identificacion' => $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $form = $this->createFormBuilder()
            ->getForm();
        $arrReclamos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/reclamo/lista");
        return $this->render('aplicacion/empleado/reclamo/lista.html.twig', [
            'arrReclamos' => $arrReclamos,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/empleado/reclamo/nuevo/{id}", name="empleado_reclamo_nuevo")
     */
    public function nuevo(Request $request, $id)
    {
        $arUsuario = $this->getUser();
        $parametros = [];
        $arReclamo = [];
        if ($id != 0) {
            $parametros = ['id' => $id];
            $arReclamo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/reclamo/detalle");
        }
        $form = $this->createFormBuilder()
            ->add('descripcion', TextareaType::class, ['required' => true, 'data' => $arReclamo->descripcion ?? null, 'attr' => ['rows' => '20', 'style' => 'height:200px']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametros = [
                    'codigoReclamo' => $id,
                    'identificacion' => $arUsuario->getCodigoIdentificacionFk(),
                    'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion(),
                    'descripcion' => $form->get('descripcion')->getData(),
                    'reclamoConcepto' => $request->get('reclamoConcepto'),
                ];
                $arReclamo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/reclamo/nuevo");
                if ($arReclamo->error == 0) {
                    $correo = new Correo();
                    $asunto = "Se ha registrado con éxito el reclamo";
                    $respuestaCorreo = $correo->enviarCorreo($arUsuario->getCorreo(), $asunto,
                        $this->renderView(
                            'aplicacion/seguridad/correoNotificacionReclamo.html.twig',
                            array(
                                'nombreEmpresa' => $arUsuario->getEmpresaRel()->getNombre(),
                                'id' => $arReclamo->id
                            )
                        ));
                    if ($respuestaCorreo->error === false) {
                        return $this->redirect($this->generateUrl('empleado_reclamo_detalle', ['id' => $arReclamo->id]));
                    } else {
                        Mensajes::error("Error al enviar correo de confirmación de registro de la solicitud{$respuestaCorreo->mensajeError}");
                    }
                } else {
                    Mensajes::error($arReclamo->errorMensaje);
                    return $this->redirect($this->generateUrl('empleado_reclamo_lista'));
                }
            }
        }
        $arrReclamoConceptos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/reclamoconcepto/lista");
        return $this->render('aplicacion/empleado/reclamo/nuevo.html.twig', [
            'arReclamo' => $arReclamo,
            'arrReclamoConceptos' => $arrReclamoConceptos,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/empleado/reclamo/detalle/{id}", name="empleado_reclamo_detalle")
     */
    public function detalle(Request $request, $id)
    {
        $parametros = ['id' => $id];
        $arUsuario = $this->getUser();
        $arReclamo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/reclamo/detalle");
        return $this->render('aplicacion/empleado/reclamo/detalle.html.twig', [
            'arReclamo' => $arReclamo,
        ]);
    }
}