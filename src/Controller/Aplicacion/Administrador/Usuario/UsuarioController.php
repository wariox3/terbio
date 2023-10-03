<?php


namespace App\Controller\Aplicacion\Administrador\Usuario;


use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Entity\Usuario;
use App\Form\Type\Administracion\Empresa\UsuarioType;
use App\Servicios\Correo;
use App\Utilidades\Mensajes;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UsuarioController extends AbstractController
{
    /**
     * @Route("/administrador/empresa/usuario/lista", name="administrador_empresa_usuario_lista")
     */
    public function lista(Request $request, PaginatorInterface $paginator,  EntityManagerInterface $em)
    {
        $session = new Session();
        $raw = ['filtros'=> $session->get('filtroUsuariosEmpresa')];
        $arUsuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('codigoUsuario', TextType::class, array('required' => false, 'data' => isset($raw['filtros']['codigoUsuario'])?$raw['filtros']['codigoUsuario']:null) )
            ->add('numeroIdentificacion', TextType::class, ['required' => false, 'data' => isset($raw['filtros']['numeroIdentificacion']) ? $raw['filtros']['numeroIdentificacion'] : null])
            ->add('nombre', TextType::class, array('required' => false, 'data'=> isset( $raw['filtros']['nombre'])  ? $raw['filtros']['nombre'] :null))
            ->add('btnFiltro', SubmitType::class, array('label' => 'Filtrar'))
            ->getForm();
        $form->handleRequest($request);
        $raw= [];
        if ($form->isSubmitted()) {
            if ($form->get('btnFiltro')->isClicked()) {
                $raw['filtros'] = $this->filtros($form);
            }
        }
        $arrRegistros = $em->getRepository(Usuario::class)->listaUsuarioEmpresa($raw);
        $arUsuarios = $paginator->paginate($arrRegistros, $request->query->getInt('page', 1), 40);
        return $this->render('aplicacion/administrador/usuarios/lista.html.twig', [
            'arUsuarios' => $arUsuarios,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/administrador/empresa/usuario/nuevo/{id}", name="administrador_empresa_usuario_nuevo")
     */
    public function nuevo(Request $request, $id, EntityManagerInterface $em)
    {
        $arRegistro = new Usuario;
        $tipoRol = null;
        $arEmpresa = $this->getUser()->getEmpresaRel();
        if ($id) {
            $arRegistro = $em->getRepository(Usuario::class)->find($id);
        }
        $form = $this->createForm(UsuarioType::class, $arRegistro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnGuardar')->isClicked()) {
                $booUsuarioNumeroIdentificacion = $em->getRepository(Usuario::class)->validarNumeroIdentificacionEmpresas($arRegistro);
                if ($booUsuarioNumeroIdentificacion){
                    $booUsuarioNombre =  $em->getRepository(Usuario::class)->validarNombreUsuario($arRegistro);
                    if($booUsuarioNombre){
                        $arRegistro->setClave($request->request->get('contrasena'));
                        $arRegistro->setEmpresaRel($arEmpresa);
                        $arRegistro->setCodigoRolFk("ROLE_USER");
                        $arRegistro->setFechaRegistro(new \DateTime('now'));
                        $arRegistro->setEmpresa(true);
                        $arRegistro->setCliente(false);
                        $arRegistro->setProveedor(false);
                        $arRegistro->setEmpleado(false);
                        $em->persist($arRegistro);
                        $em->flush();
                        Mensajes::success("Registro completo");
                        return $this->redirect($this->generateUrl('administrador_empresa_usuario_detalle', ['id' => $arRegistro->getCodigoUsuarioPk()]));
                    }
                }
            }
        }
        return $this->render('aplicacion/administrador/usuarios/nuevo.html.twig', [
            'arRegistro' => $arRegistro,
            'id' => $id,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/administrador/empresa/usuario/detalle/{id}", name="administrador_empresa_usuario_detalle")
     */
    public function detalle(Request $request, PaginatorInterface $paginator, $id,  EntityManagerInterface $em)
    {
        $arRegistro = $em->getRepository(Usuario::class)->find($id);
        return $this->render('aplicacion/administrador/usuarios/detalle.html.twig', [
            'arRegistro' => $arRegistro,
        ]);
    }

    public function filtros($form)
    {
        $session = new Session();
        $filtro = [
            "numeroIdentificacion"=> $form->get('numeroIdentificacion')->getData(),
            "nombre"=> $form->get('nombre')->getData(),
            "codigoUsuario"=> $form->get('codigoUsuario')->getData()
        ];
        $session->set('filtroUsuariosEmpresa', $filtro);
        return $filtro;
    }

}