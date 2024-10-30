<?php

namespace App\Controller\Aplicacion\Empleado\Pago;

use App\Controller\FuncionesController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagoController extends AbstractController
{

    #[Route("/empleado/pago/lista", name:"empleado_pago_lista")]
    public function lista(Request $request)
    {
        $arUsuario = $this->getUser();
        $parametros = ['identificacion' => $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('opImprimir')) {
                $codigoPago = $request->request->get('opImprimir');
                $urlFormato = "/recursohumano/api/oxigeno/pago/imprimir";
                $parametrosFormato = [
                    'codigoPago' => $codigoPago
                ];
                $respuestaFormato = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosFormato, $urlFormato);
                if ($respuestaFormato->error == false) {
                    $archivo = "/var/www/html/temporal/pago{$codigoPago}.pdf";
                    $nombreArchivo = "pago{$codigoPago}.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaFormato->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', "attachment; filename=$nombreArchivo;");
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
        }

        $arrPagos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/pago");
        return $this->render('aplicacion/empleado/pago/lista.html.twig', [
            'arPagos' => $arrPagos,
            'form' => $form->createView()
        ]);
    }

    #[Route("/empleado/pago/detalle/{codigoPago}", name:"pago_detalle")]
    public function detalle(Request $request, $codigoPago)
    {
        $arUsuario = $this->getUser();
        $parametros = ['id' => $codigoPago];
        $url = "/recursohumano/api/pago/detalle";
        $form = $this->createFormBuilder()
            ->add('btnImprimir', SubmitType::class, ['label' => 'Imprimir', 'attr' => ['class' => 'btn btn-default btn-sm']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnImprimir')->isClicked()) {
                $urlFormato = "/recursohumano/api/oxigeno/pago/imprimir";
                $parametrosFormato = [
                    'codigoPago' => $codigoPago
                ];
                $respuestaFormato = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosFormato, $urlFormato);
                if ($respuestaFormato->error == false) {
                    $archivo = "/var/www/html/temporal/pago{$codigoPago}.pdf";
                    $nombreArchivo = "pago{$codigoPago}.pdf";
                    $file = fopen($archivo, "wb");
                    fwrite($file, base64_decode($respuestaFormato->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', "attachment; filename=$nombreArchivo;");
                    $response->sendHeaders();
                    $response->setContent(readfile($archivo));
                    return $response;
                }
            }
        }

        $informacion = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        $arPago = $informacion->pago;
        $arPagoDetalles = $informacion->pagoDetalles;
        return $this->render('aplicacion/empleado/pago/detalle.html.twig', [
            'arPago' => $arPago,
            'arPagoDetalles' => $arPagoDetalles,
            'form' => $form->createView()
        ]);
    }
}
