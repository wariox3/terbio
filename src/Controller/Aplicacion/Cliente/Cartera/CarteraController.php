<?php


namespace App\Controller\Aplicacion\Cliente\Cartera;


use App\Controller\FuncionesController;
use App\Formato\EstadoCuentaFormal;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CarteraController  extends AbstractController
{
    /**
     * @Route("/cliente/cartera/lista", name="cliente_cartera_lista")
     */
    public function inicioAction(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em )
    {
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('btnEstadoCuentaFormal', SubmitType::class, array('label' => 'Estado cuenta formal', 'attr'=>['class' => 'btn-sm btn-link']))
            ->getForm();
        $form->handleRequest($request);
        $parametros=[
            'codigoCliente' => $arUsuario->getCodigoTerceroErpFk()
        ];
        $url="/cartera/api/cuentapendiente";
        $arrRegistros = FuncionesController::consumirApi($arUsuario->getEmpresaRel(), $parametros, $url);
        if(is_null($arrRegistros)) {
            $arrRegistros=[];
        }
        if ($form->isSubmitted()) {
            if ($form->get('btnEstadoCuentaFormal')->isClicked()) {
                $formato = new EstadoCuentaFormal();
                $formato->Generar($em, $arrRegistros, $arUsuario);
            }
        }
        $arCuentasCobrarPendientes = $paginator->paginate($arrRegistros, $request->query->getInt('page', 1), 1000);
        return $this->render('aplicacion/cliente/cartera/pendientes.html.twig',[
            'arCuentasCobrarPendientes' => $arCuentasCobrarPendientes,
            'form'=>$form->createView()
        ]);
    }
}