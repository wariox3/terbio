<?php


namespace App\Controller\Aplicacion\Empleado\AutorizacionArma;


use App\Controller\FuncionesController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AutorizacionArmaController extends AbstractController
{
    /**
     * @Route("/empleado/autorizacionarma/lista", name="empleado_autorizacionarma_lista")
     */
    public function inicioAction(Request $request, PaginatorInterface $paginator)
    {
        $arUsuario = $this->getUser();
        $parametros = ['identificacion' => $arUsuario->getCodigoIdentificacionFk(), 'numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->get('btnImprimir')) {
                $codigo = $request->request->get('btnImprimir');
                $parametrosImprimir = [
                    'codigoEmpleado' => $codigo
                ];
                $url = "/recursohumano/api/autorizacionarma/formato";
                $arrEmpleados = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosImprimir, $url);
                if ($arrEmpleados->error == false) {
                    $archivo = $arrEmpleados->archivo;
                    $ruta = '/var/www/html/temporal/' . $archivo->nombre;
                    $file = fopen($ruta, "wb");
                    fwrite($file, base64_decode($archivo->base64));
                    fclose($file);
                    $response = new Response();
                    $response->headers->set('Cache-Control', 'private');
                    $response->headers->set('Content-type', 'application/pdf');
                    $response->headers->set('Content-Disposition', 'attachment; filename="' . $archivo->nombre . '";');
                    $response->sendHeaders();
                    $response->setContent(readfile($ruta));
                    return $response;
                }
            }
        }
        $arEmpleados = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/autorizacionarma/lista");
        return $this->render('aplicacion/empleado/autorizacionarma/lista.html.twig', [
            'arEmpleados' => $arEmpleados,
            'form' => $form->createView()
        ]);
    }
}