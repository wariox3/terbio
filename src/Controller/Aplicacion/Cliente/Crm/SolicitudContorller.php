<?php


namespace App\Controller\Aplicacion\Cliente\Crm;


use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SolicitudContorller  extends AbstractController
{
    #[Route("/cliente/crm/solicitud/lista", name:"cliente_crm_solicitud_lista")]
    public function lista(Request $request, PaginatorInterface $paginator)
    {
        $arUsuario = $this->getUser();
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $form = $this->createFormBuilder()
            ->getForm();
        if ($form->isSubmitted() && $form->isValid()) {
        }
        $arrSolicitudes = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/crm/api/gestion/solicitud/lista");
        return $this->render('aplicacion/cliente/crm/solicitud/lista.html.twig', [
            'arrSolicitudes' => $arrSolicitudes,
            'form' => $form->createView()
        ]);
    }

    #[Route("/cliente/crm/solicitud/nuevo/{codigoSolicitud}", name:"cliente_crm_solicitud_nuevo")]
    public function nuevo(Request $request, PaginatorInterface $paginator, $codigoSolicitud)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('nombreContacto', TextType::class, ['label' => 'Nombre:', 'required' => false])
            ->add('numeroIdentificacionContacto', TextType::class, ['label' => 'Número identificación:', 'required' => false])
            ->add('telefonoContacto', TextType::class, ['label' => 'Teléfono:', 'required' => false])
            ->add('correoContacto', TextType::class, ['label' => 'Correo:', 'required' => false])
            ->add('cargoContacto', TextType::class, ['label' => 'Cargo contacto:', 'required' => false])
            ->add('descripcion',TextareaType::class,['required' => false, 'attr'=>['rows'=>20, 'style'=>'height: 200px;']])
            ->add('btnGuardar', SubmitType::class, ['label' => 'Guardar', 'attr' => ['class' => 'btn btn-sm btn-primary']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $parametrosSolicitudNuevo = [
                    'codigoTercero' => $arUsuario->getCodigoTerceroErpFk(),
                    'nombreContacto' => $form['nombreContacto']->getData(),
                    'numeroIdentificacionContacto' => $form['numeroIdentificacionContacto']->getData(),
                    'telefonoContacto' => $form['telefonoContacto']->getData(),
                    'correoContacto' => $form['correoContacto']->getData(),
                    'cargoContacto' => $form['cargoContacto']->getData(),
                    'descripcion' => $form['descripcion']->getData(),
                    'solicitudTipo' => $request->request->get('solicitudTipo'),
                    'solicitudCanal' => "POR",
                ];
                $urlSolicitudNuevo = "/crm/api/gestion/solicitud/nuevo";
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosSolicitudNuevo, $urlSolicitudNuevo);
                if ($respuesta->error == 0) {
                    return $this->redirect($this->generateUrl('cliente_crm_solicitud_lista'));
                } else {
                    Mensajes::error($respuesta['mensaje']);
                }            }
        }
        return $this->render('aplicacion/cliente/crm/solicitud/nuevo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/cliente/crm/solicitud/detalle/{id}", name:"cliente_crm_solicitud_detalle")]
    public function detalle(Request $request, $id)
    {
        $parametros = ['id' => $id];
        $arUsuario = $this->getUser();
        $arSolicitud = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/crm/api/gestion/solicitud/detalle");
        return $this->render('aplicacion/cliente/crm/solicitud/detalle.html.twig', [
            'arSolicitud' => $arSolicitud,
        ]);
    }

    #[Route("/cliente/crm/solicitud/archivo/{tipo}/{codigo}", name:"cliente_crm_solicitud_archivo")]
    public function consultarArchivos(Request $request, $tipo, $codigo,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('btnDescargar')) {
                $codigoArchivo = $request->request->get('btnDescargar');
                $parametros=[
                    "codigo"=> $codigoArchivo
                ];
                $urlDescarga = "/api/documental/archivo/descarga";
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $urlDescarga);
                if($respuesta->error == 0) {
                    $ruta = '/var/www/html/temporal/oxigeno_' . $respuesta->nombre;
                    $file = fopen($ruta, "wb");
                    fwrite($file, base64_decode($respuesta->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="' . $respuesta->nombre . '";');
                    //$response->headers->set('Content-length', $arArchivo->getTamano());
                    $response->sendHeaders();
                    $response->setContent(readfile($ruta));
                    return $response;
                    unlink($ruta);
                }

            }
        }

        $arArchivos = [];
        $parametros=[
            'tipo' => $tipo,
            "codigo"=> $codigo
        ];
        $url="/crm/api/solicitud/archivos";
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if($respuesta->error == 0) {
            $arArchivos = $respuesta->arrArchivos;
        }
        return $this->render('aplicacion/cliente/crm/visita/archivos.html.twig',[
            'arArchivos' => $arArchivos,
            'form' => $form->createView()
        ]);
    }
}