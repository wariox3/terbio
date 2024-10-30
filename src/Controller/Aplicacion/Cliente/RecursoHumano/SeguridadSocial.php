<?php


namespace App\Controller\Aplicacion\Cliente\RecursoHumano;


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
    #[Route("/cliente/recursohumano/seguridadsocial/lista", name:"cliente_recursohumano_seguridadsocial_lista")]
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $fechaActual = new \DateTime('now');
        $mesActualFijo = $fechaActual->format('Y-m');
        $mesActual = $fechaActual->format('Y-m');
        $parametros = [
            'codigoCliente'=> $arUsuario->getCodigoTerceroErpFk(),
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
                    'tipo' => "RhuAporteContrato",
                    'codigo' => $codigo
                ];
                $url="/documental/api/archivo/descargar";
                $arrArchivo = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosArchivos, $url);
                if($arrArchivo) {
                    $arrArchivo = $arrArchivo[0];
                    if($arrArchivo->error == 0) {
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
                } else {
                    Mensajes::error("No se encuentra el archivo de la seguridad social para este este empleado, por favor validar");
                }
            }
        }
        $url="/recursohumano/api/aporte/contrato/cliente";
        $arrContratos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        return $this->render('aplicacion/cliente/recursohumano/seguridadsocial/lista.html.twig',[
            "mesActual"=>$mesActual,
            "mesActualFijo"=>$mesActualFijo,
            'arrContratos' => $arrContratos,
            'form' => $form->createView()
        ]);
    }
}