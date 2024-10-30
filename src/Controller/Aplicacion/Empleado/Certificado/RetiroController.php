<?php


namespace App\Controller\Aplicacion\Empleado\Certificado;

use App\Controller\FuncionesController;
use App\Formato\RetiroLaboral;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RetiroController extends AbstractController
{
    #[Route("empleado/retirolaboral/lista", name:"retirolaboral_lista")]
    public function lista(Request $request, EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $parametros = ['numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()];
        if ($form->isSubmitted() && $form->isValid()) {
//            if ($request->request->get('opImprimir')) {
//                $codigoContrato = $request->request->get('opImprimir');;
//                $formato = new RetiroLaboral();
//                $formato->generar($em, $codigoContrato, $this->getUser());
//            }
            if ($request->request->get('opImprimir2')) {
                $codigoContrato = $request->request->get('opImprimir2');
                $parametrosImprimir = [
                    'codigoContrato' => $codigoContrato
                ];
                $url = "/recursohumano/api/oxigeno/certificadolaboral/imprimir";
                $respuestaFormato = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametrosImprimir, $url);
                if ($respuestaFormato->error == false) {
                    $archivo = "/var/www/html/temporal/certificadoLaboral{$codigoContrato}.pdf";
                    $nombreArchivo = "certificadoLaboral{$codigoContrato}.pdf";
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

        $arContratos = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, '/recursohumano/api/contrato/terminado');
        return $this->render('aplicacion/empleado/certificado/retiro.html.twig', [
            'arContratos' => $arContratos,
            'form' => $form->createView()
        ]);
    }
}