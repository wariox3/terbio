<?php


namespace App\Controller\Aplicacion\Empleado\SeguridadSocial;


use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeguridadSocial extends AbstractController
{
    #[Route("/empleado/certificadoseguridadsocial/lista", name:"empleado_certificadoseguridadsocial_lista")]
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $fechaActual = new \DateTime('now');
        $mesActualFijo = $fechaActual->format('Y-m');
        $mesActual = $fechaActual->format('Y-m');
        $parametros = [
            'numeroIdentificacion'=> $arUsuario->getNumeroIdentificacion(),
            "anio"=> $fechaActual->format("Y"),
            "mes"=> $fechaActual->format("n")
        ];
        $form = $this->createFormBuilder()
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnFiltro')->isClicked() || $request->request->get('OpDescargar')) {
                $filtroMes = new \DateTime($request->request->get("mes"));
                $parametros["mes"]= $filtroMes->format("n");
                $parametros["anio"]= $filtroMes->format("Y");
                $mesActual = $filtroMes->format('Y-m');
            }
            if ($request->request->get('OpDescargar')) {
                $codigo = $request->request->get('OpDescargar');
                $parametrosArchivos=[
                    'codigo' => $codigo
                ];
                $url="/api/documental/archivo/descargacodigo";
                $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosArchivos, $url);
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
                } else {
                    Mensajes::error("No se encuentra el archivo de la seguridad social para este este empleado, por favor validar");
                }
            }
        }
        $url="/recursohumano/api/aporte/contrato/empleado";
        $arrContratos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/empleado/seguridadsocial/lista.html.twig',[
            "mesActual"=>$mesActual,
            "mesActualFijo"=>$mesActualFijo,
            'arrContratos' => $arrContratos,
            'form' => $form->createView()
        ]);
    }

    #[Route("/empleado/certificadoseguridadsocial/archivo/{codigo}", name:"empleado_certificadoseguridadsocial_archivo")]
    public function consultarArchivos(Request $request, $codigo)
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
            'codigoModelo' => 'RhuAporteContrato',
            "codigo"=> $codigo
        ];
        $url="/api/documental/archivo/lista";
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if($respuesta->error == 0) {
            $arArchivos = $respuesta->archivos;
        }
        return $this->render('aplicacion/empleado/seguridadsocial/archivos.html.twig',[
            'arArchivos' => $arArchivos,
            'form' => $form->createView()
        ]);
    }
}