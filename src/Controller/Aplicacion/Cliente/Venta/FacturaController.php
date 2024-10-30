<?php


namespace App\Controller\Aplicacion\Cliente\Venta;


use App\Controller\FuncionesController;
use App\Entity\Documental\DocArchivo;
use App\Entity\Documental\DocConfiguracion;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FacturaController extends AbstractController
{
    #[Route("/cliente/venta/factura/lista", name:"cliente_venta_factura_lista")]
    public function inicioAction(Request $request, PaginatorInterface $paginator )
    {
        $arUsuario = $this->getUser();
        $parametros=[
            'codigoCliente' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url="/turno/api/venta/factura";
        $arrRegistros = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('OpImprimir')) {
                $codigo = $request->request->get('OpImprimir');
                $parametros=[
                    'codigoFactura' => $codigo,
                    'codigoCliente' => $arUsuario->getCodigoTerceroErpFk()
                ];
                $url="/turno/api/venta/factura/formato";
                $arrFactura = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
                if($arrFactura->error == 0) {
                    $archivo = $arrFactura->archivo;
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
        $arrFacturas = $paginator->paginate($arrRegistros, $request->query->getInt('page', 1), 50);
        return $this->render('aplicacion/cliente/venta/factura/lista.html.twig',[
            'arrFacturas' => $arrFacturas,
            'form' => $form->createView()
        ]);
    }
}