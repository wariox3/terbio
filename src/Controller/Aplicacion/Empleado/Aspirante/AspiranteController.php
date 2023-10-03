<?php


namespace App\Controller\Aplicacion\Empleado\Aspirante;


use App\Entity\Aspirante;
use App\Entity\AspiranteIdioma;
use App\Entity\Estudio;
use App\Form\Type\Aspirante\AspiranteIdiomaType;
use App\Form\Type\Aspirante\AspiranteType;
use App\Form\Type\Aspirante\EstudioType;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AspiranteController extends AbstractController
{
    /**
     * @Route("empleado/aspirante/lista/{id}", name="aspirante_lista")
     */
    public function lista(Request $request, PaginatorInterface $paginator, $id = 0,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arAspirante = $em->getRepository(Aspirante::class)->findOneBy(['numeroIdentificacion' => $arUsuario->getNumeroIdentificacion()]);
        $codigo = $arAspirante != null ? $arAspirante->getCodigoAspirantePk() : 0;
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() ) {
            if ($request->request->get('OpEliminarEstudio')) {
                $codigo = $request->request->get('OpEliminarEstudio');
                $em->getRepository(Estudio::class)->eliminar([$codigo]);
            }
            if ($request->request->get('OpEliminarIdioma')) {
                $codigo = $request->request->get('OpEliminarIdioma');
                $em->getRepository(AspiranteIdioma::class)->eliminar([$codigo]);
            }
        }
        $arEstudios = $em->getRepository(Estudio::class)->findBy(['codigoUsuarioFk' => $arUsuario->getCodigoUsuarioPk()]);
        $arIdiomas = $em->getRepository(AspiranteIdioma::class)->findBy(['codigoUsuarioFk' => $arUsuario->getCodigoUsuarioPk()]);
        return $this->render('aplicacion/empleado/aspirante/lista.html.twig', [
            'arAspirante' => $arAspirante,
            'arEstudios' => $arEstudios,
            'arIdiomas' => $arIdiomas,
            'clase' => array('clase' => 'Aspirante', 'codigo' => $codigo),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("empleado/aspirante/nuevo/{id}", name="aspirante_nuevo")
     */
    public function nuevo(Request $request, $id,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arAspirante = new Aspirante();
        if ($id != 0) {
            $arAspirante = $em->getRepository(Aspirante::class)->find($id);
        } else {
            $arAspirante->setFecha(new \DateTime('now'));
            $arAspirante->setNombre1($arUsuario->getNombres());
            $arAspirante->setApellido1($arUsuario->getApellidos());
            $arAspirante->setNumeroIdentificacion($arUsuario->getNumeroIdentificacion());
            $arAspirante->setCorreo($arUsuario->getCorreo());
        }

        $form = $this->createForm(AspiranteType::class, $arAspirante);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('guardar')->isClicked()) {
                $arAspirante->setNombreCorto($arAspirante->getNombre1() . ' ' . $arAspirante->getNombre2() . ' ' . $arAspirante->getApellido1() . ' ' . $arAspirante->getApellido2());
                $em->persist($arAspirante);
                $em->flush();
                return $this->redirect($this->generateUrl('aspirante_lista', ['id' => $arAspirante->getCodigoAspirantePk()]));
            }
        }
        return $this->render('aplicacion/empleado/aspirante/nuevo.html.twig', [
            'form' => $form->createView(),
            'id' => $id,
            'arAspirante' => $arAspirante
        ]);
    }

    /**
     * @Route("empleado/aspirante/estudio/nuevo/{codigoAspirante}/{id}", name="aspirante_estudio_nuevo")
     */
    public function nuevoEstudio(Request $request, $codigoAspirante, $id,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arEstudio = new Estudio();
        $arrEstudioTipos = [
            ["clave" => "", "valor"=>"Nivel de estudios"],
            ["clave" => "educación básica primaria", "valor" => "Educación Básica Primaria"],
            ["clave" => "educación básica secundaria", "valor" => "Educación Básica Secundaria"],
            ["clave" => "bachillerato / educación media", "valor" => "Bachillerato / Educación Media"],
            ["clave" => "universidad / carrera técnica", "valor" => "Universidad / Carrera técnica"],
            ["clave" => "universidad / carrera tecnológica", "valor" => "Universidad / Carrera tecnológica"],
            ["clave" => "universidad / carrera profesional", "valor" => "Universidad / Carrera Profesional"],
            ["clave" => "postgrado / especialización", "valor" => "Postgrado / Especialización"],
            ["clave" => "postgrado / maestría", "valor" => "Postgrado / Maestría"],
            ["clave" => "postgrado / doctorado", "valor" => "Postgrado / Doctorado"]
        ];
        $arrEstudioEstados = [
            ["clave" => "culminado", "valor" => "Culminado"],
            ["clave" => "cursando", "valor" => "Cursando"],
            ["clave" => "abandonado_aplazado", "valor" => "Abandonado / Aplazado"],
        ];
        if ($id != 0) {
            $arEstudio = $em->getRepository(Estudio::class)->find($id);
        } else {
            $arEstudio->setFecha(new \DateTime('now'));
        }

        $form = $this->createForm(EstudioType::class, $arEstudio);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('guardar')->isClicked()) {
                $estudioTipo = $request->request->get('estudioTipo');
                $estadoEstudio = $request->request->get('estadoEstudio');
                if ($estudioTipo != ""){
                    if ($estadoEstudio != "") {
                        $arEstudio->setEstadoEstudio($estadoEstudio);
                        $arEstudio->setEstudioTipo($estudioTipo);
                        $arEstudio->setUsuarioRel($arUsuario);
                        if ($estadoEstudio == 'culminado') {
                            $arEstudio->setGraduado(true);
                            $em->persist($arEstudio);
                            $em->flush();
                            echo "<script languaje='javascript' type='text/javascript'>window.close();window.opener.location.reload();</script>";
                        } else {
                            $arEstudio->setGraduado(false);
                            $em->persist($arEstudio);
                            $em->flush();
                            echo "<script languaje='javascript' type='text/javascript'>window.close();window.opener.location.reload();</script>";
                        }
                    } else {
                        Mensajes::info("Seleccione el estado del estudio");
                    }
                } else {
                    Mensajes::info("Seleccionar un tipo de estudio");
                }

            }
        }
        return $this->render('aplicacion/empleado/aspirante/nuevoEstudio.html.twig', [
            'arEstudio' => $arEstudio,
            'form' => $form->createView(),
            'id' => $codigoAspirante,
            'arrEstudioTipos' => $arrEstudioTipos,
            'arrEstudioEstados' => $arrEstudioEstados
        ]);
    }

    /**
     * @Route("empleado/aspirante/idioma/nuevo/{codigoAspirante}/{id}", name="aspirante_idioma_nuevo")
     */
    public function nuevoIdioma(Request $request, $codigoAspirante, $id,  EntityManagerInterface $em)
    {
        $arUsuario = $this->getUser();
        $arAspiranteIdioma = new AspiranteIdioma();
        if ($id != 0) {
            $arAspiranteIdioma = $em->getRepository(AspiranteIdioma::class)->find($id);
        }
        $form = $this->createForm(AspiranteIdiomaType::class, $arAspiranteIdioma);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('guardar')->isClicked()) {
                $arAspiranteIdioma->setUsuarioRel($arUsuario);
                $em->persist($arAspiranteIdioma);
                $em->flush();
                echo "<script languaje='javascript' type='text/javascript'>window.close();window.opener.location.reload();</script>";
            }
        }
        return $this->render('aplicacion/empleado/aspirante/nuevoIdioma.html.twig', [
            'arAspiranteIdioma' => $arAspiranteIdioma,
            'form' => $form->createView(),
            'id' => $codigoAspirante,
        ]);

}
}