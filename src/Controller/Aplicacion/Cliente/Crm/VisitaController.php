<?php


namespace App\Controller\Aplicacion\Cliente\Crm;


use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VisitaController extends AbstractController
{
    /**
     * @Route("/cliente/crm/visita/lista", name="cliente_crm_visita_lista")
     */
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $parametros=[
            'codigoCliente' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url="/crm/api/gestion/visita/lista";
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('OpImprimir')) {
                $codigo = $request->request->get('OpImprimir');
                $parametros=[
                    'codigoVisita' => $codigo
                ];
                $url="/crm/api/gestion/visita/formato";
                $arrVisitas = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
                if($arrVisitas->error == 0) {
                    $archivo = $arrVisitas->archivo;
                    $ruta = '/var/www/html/temporal/oxigeno_' . $archivo->nombre;
                    $file = fopen($ruta, "wb");
                    fwrite($file, base64_decode($archivo->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="' . $archivo->nombre . '";');
                    //$response->headers->set('Content-length', $arArchivo->getTamano());
                    $response->sendHeaders();
                    $response->setContent(readfile($ruta));
                    return $response;
                }
            }
        }
        $arrVisitas = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/cliente/crm/visita/lista.html.twig',[
            'arrVisitas' => $arrVisitas,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente/crm/visita/detalle/{codigovisita}", name="cliente_crm_visita_detalle")
     */
    public function detalle(Request $request, $codigovisita,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $parametros = ['codigoVisita' => $codigovisita];
        $url="/crm/api/gestion/visita/detalle";
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
        $arrVisitas = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        $arVisita = $arrVisitas->visita[0];
        $arVisitaCompromisos = $arrVisitas->compromisos;
        $arVisitaReportes = $arrVisitas->reportes;
        return $this->render('aplicacion/cliente/crm/visita/detalle.html.twig',[
            'arVisita' => $arVisita,
            'arVisitaCompromisos' => $arVisitaCompromisos,
            'arVisitaReportes' => $arVisitaReportes,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cliente/crm/visita/archivo/{tipo}/{codigo}", name="cliente_crm_visita_archivo")
     */
    public function consultarArchivos(Request $request, $tipo, $codigo,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $parametros=[
            'tipo' => $tipo,
            "codigo"=> $codigo
        ];
        $url="/crm/api/gestion/visita/lista/archivos";
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('btnDescargar')) {
                $urlDescarga = "/documental/api/archivo/descargar";
                $arrArchivo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $urlDescarga);
                if ($arrArchivo) {
                    $arrArchivo = $arrArchivo[0];
                    if ($arrArchivo->error == 0) {
                            $ruta = '/var/www/html/temporal/oxigeno_' . $arrArchivo->nombre;
                            $file = fopen($ruta, "wb");
                            fwrite($file, base64_decode($arrArchivo->base64));
                            fclose($file);
                            $response = new Response();
                            $response->headers->set('Cache-Control', 'private');
                            $response->headers->set('Content-type', 'application/pdf');
                            $response->headers->set('Content-Disposition', 'attachment; filename="' . $arrArchivo->nombre . '";');
                            //$response->headers->set('Content-length', $arArchivo->getTamano());
                            $response->sendHeaders();
                            $response->setContent(readfile($ruta));
                            return $response;
                        }
                }
            }
        }
        $arArchivos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/cliente/crm/visita/archivos.html.twig',[
            'arArchivos' => $arArchivos->arrArchivos,
            'form' => $form->createView()
        ]);
    }



}