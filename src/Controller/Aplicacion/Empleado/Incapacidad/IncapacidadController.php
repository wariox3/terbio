<?php


namespace App\Controller\Aplicacion\Empleado\Incapacidad;


use App\Entity\Incapacidad;
use App\Form\Type\Empleado\Incapacidad\IncapacidadType;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class IncapacidadController extends AbstractController
{
    #[Route("/empleado/incapacidad/lista", name:"empleado_incapacidad_lista")]
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $form = $this->createFormBuilder()
            ->getForm();
        $arIncapacidades = $paginator->paginate($em->getRepository(Incapacidad::class)->lista($this->getUser()->getUsername()), $request->query->getInt('page', 1), 30);
        return $this->render('aplicacion/empleado/incapacidad/lista.html.twig', [
            'arIncapacidades' => $arIncapacidades,
            'form' => $form->createView()
        ]);
    }

    #[Route("/empleado/incapacidad/nuevo/{id}", name:"empleado_incapacidad_nuevo")]
    public function nuevo(Request $request, PaginatorInterface $paginator, $id,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arIncapacidad = new Incapacidad();
        if ($id != 0) {
            $arIncapacidad = $em->getRepository(Incapacidad::class)->find($id);
        } else {
            $arIncapacidad->setFechaRegistro(new \DateTime('now'));
        }
        $form = $this->createForm(IncapacidadType::class, $arIncapacidad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                if ($arIncapacidad->getFechaDesde() <= $arIncapacidad->getFechaHasta()) {
                    $arIncapacidad->setUsuarioRel($arUsuario);
                    $em->persist($arIncapacidad);
                    $em->flush();
                    return $this->redirect($this->generateUrl('empleado_incapacidad_detalle', array('id' => $arIncapacidad->getCodigoIncapacidadPk())));
                } else {
                    Mensajes::error("La fecha desde debe ser inferior o igual a la fecha hasta de la incapacidad");
                }
            }
        }
        return $this->render('aplicacion/empleado/incapacidad/nuevo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/empleado/incapacidad/detalle/{id}", name:"empleado_incapacidad_detalle")]
    public function detalle(Request $request, PaginatorInterface $paginator, $id,  EntityManagerInterface $em)
    {
        $arIncapacidad = $em->getRepository(Incapacidad::class)->find($id);
        return $this->render('aplicacion/empleado/incapacidad/detalle.html.twig', [
            'arIncapacidad' => $arIncapacidad,
            'clase' => array('clase' => 'Incapacidad', 'codigo' => $id),
        ]);
    }

    #[Route("/empleado/incapacidad/firma/{id}", name:"empleado_incapacidad_firma")]
    public function firma(Request $request,$id,  EntityManagerInterface $em)
    {
        $arIncapacidad = $em->getRepository(Incapacidad::class)->find($id);
        $form = $this->createFormBuilder()
            ->getForm();
        $entidad = 'RhuIncapacidad';
        $codigo = '50';
        return $this->render('aplicacion/empleado/incapacidad/firma.html.twig', [
            'arIncapacidad' => $arIncapacidad,
            'entidad' => $entidad,
            'codigo' => $codigo,
            'form' => $form->createView()
        ]);
    }
}