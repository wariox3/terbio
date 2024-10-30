<?php


namespace App\Controller\Aplicacion\Empresa\Enlace;


use App\Entity\Enlace;
use App\Entity\Usuario;
use App\Form\Type\Empresa\Enlace\EnlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EnlaceController extends AbstractController
{
    //empresa_enlace_lista

    #[oute("/empresa/enlace/lista", name:"empresa_enlace_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $session = new Session();
        $raw = ['filtros'=> $session->get('filtroUsuarios')];
        $form = $this->createFormBuilder()
            ->add('btnEliminar', SubmitType::class, ['label' => 'Eliminar', 'attr' => ['class' => 'btn btn-sm btn-danger']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnEliminar')->isClicked()) {
                $arrSeleccionados = $request->request->get('ChkSeleccionar');
                $em->getRepository(Enlace::class)-> eliminar($arrSeleccionados);
            }
        }
        $arEnlaces = $paginator->paginate($em->getRepository(Enlace::class)->lista( $this->getUser()->getCodigoEmpresaFK()), $request->query->getInt('page', 1), 30);
        return $this->render('aplicacion/empresa/enlace/lista.html.twig', [
            'arEnlaces' => $arEnlaces,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/empresa/enlace/nuevo/{id}", name:"empresa_enlace_nuevo")]
    public function nuevo(Request $request, $id,  EntityManagerInterface $em)
    {
        $arEnlace = new Enlace;
        if ($id != 0 ||  $id != "") {
            $arEnlace = $em->getRepository(Enlace::class)->find($id);
        }
        $form = $this->createForm( EnlaceType::class, $arEnlace);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $arEmpresa = $this->getUser()->getEmpresaRel();
                $arEnlace = $form->getData();
                $arEnlace->setEmpresaRel($arEmpresa);
                $em->persist($arEnlace);
                $em->flush();
                return $this->redirect($this->generateUrl('empresa_enlace_detalle', array('id' => $arEnlace->getcodigoEnlacePk())));
            }
        }
        return $this->render('aplicacion/empresa/enlace/nuevo.html.twig', [
            'form' => $form->createView(),
            'arEnlace' => $arEnlace
        ]);
    }

    #[Route("/empresa/enlace/detalle/{id}", name:"empresa_enlace_detalle")]
    public function detalle(Request $request, PaginatorInterface $paginator, $id,  EntityManagerInterface $em)
    {
        $arRegistro = $em->getRepository(Enlace::class)->find($id);
        return $this->render('aplicacion/empresa/enlace/detalle.html.twig', [
            'arRegistro' => $arRegistro,
        ]);
    }
}