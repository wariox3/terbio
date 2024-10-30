<?php

namespace App\Controller\Aplicacion\Cliente\Cartera;

use App\Controller\FuncionesController;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReciboController extends AbstractController
{
    #[Route("/cliente/cartera/recibos/lista", name:"cliente_cartera_recibos_lista")]
    public function inicioAction(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        $parametros = [
            'codigoTercero' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url = "/api/cartera/recibo/tercero";
        $arrRegistros = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if (is_null($arrRegistros)) {
            $arrRegistros = [];
        } else {
            if ($arrRegistros->error == false) {
                $arrRegistros = $arrRegistros->recibos;
            } else {
                Mensajes::error($arrRegistros->mensaje);
            }
        }
        if ($form->isSubmitted()) {
            if ($request->request->get('OpImprimir')) {
                $codigo = $request->request->get('OpImprimir');
                $parametros=[
                    'codigoRecibo' => $codigo,
                ];
                $url="/api/cartera/recibo/imprimir";
                $arrFactura = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
                if($arrFactura->error == 0) {
                    $ruta = '/var/www/html/temporal/oxigeno_' . $arrFactura->nombre;
                    $file = fopen($ruta, "wb");
                    fwrite($file, base64_decode($arrFactura->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="' . $arrFactura->nombre . '";');
                    //$response->headers->set('Content-length', $arArchivo->getTamano());
                    $response->sendHeaders();
                    $response->setContent(readfile($ruta));
                    return $response;
                }
            }
        }


        $arRecibos = $paginator->paginate($arrRegistros, $request->query->getInt('page', 1), 1000);
        return $this->render('aplicacion/cliente/cartera/recibo.html.twig', [
            'arRecibos' => $arRecibos,
            'form' => $form->createView()
        ]);
    }
}