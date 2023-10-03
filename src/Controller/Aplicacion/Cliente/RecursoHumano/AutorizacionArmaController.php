<?php


namespace App\Controller\Aplicacion\Cliente\RecursoHumano;


use App\Controller\FuncionesController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AutorizacionArmaController extends AbstractController
{
    /**
     * @Route("/cliente/recursohumano/autorizacionarma/lista", name="cliente_recursohumano_autorizacionarma_lista")
     */
    public function inicioAction(Request $request, PaginatorInterface $paginator)
    {
        $arUsuario = $this->getUser();
        $parametros = [];
        $form = $this->createFormBuilder()
            ->add('btnImprimirTodos', SubmitType::class, ['label' => 'Imprimir todos', 'attr' => ['class' => 'btn btn-sm btn-default']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnImprimirTodos')->isClicked()) {
                $parametrosImprimir = [
                ];
                $url = "/recursohumano/api/autorizacionarma/formatotodos";
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
        $arEmpleados = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, "/recursohumano/api/autorizacionarma/listatodos");
        return $this->render('aplicacion/cliente/recursohumano/autorizacionarma/lista.html.twig', [
            'arEmpleados' => $arEmpleados,
            'form' => $form->createView()
        ]);
    }

}