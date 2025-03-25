<?php


namespace App\Controller\Aplicacion\Cliente\General;


use App\Controller\FuncionesController;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformacionController extends AbstractController
{
    #[Route("/cliente/general/informacion", name:"cliente_general_informacion")]
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

    #[Route("/cliente/general/informacion/archivo/{tipo}/{codigo}", name:"cliente_general_informacion_archivo")]
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
        $url="/api/general/tercero/archivos";
        $respuesta = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if($respuesta->error == 0) {
            $arArchivos = $respuesta->arrArchivos;
        }
        return $this->render('aplicacion/cliente/general/archivos.html.twig',[
            'arArchivos' => $arArchivos,
            'form' => $form->createView()
        ]);
    }
}