<?php

namespace App\Controller;

use App\Entity\Configuracion;
use App\Entity\Empresa;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login/{codigoEmpresa}", name="login", requirements={"codigoEmpresa"="\d+"})
     */
    public function login(AuthenticationUtils $authenticationUtils, $codigoEmpresa,  EntityManagerInterface $em): Response
    {
        $nombreEmpresa = "";
        $registroFijo = false;
        $imagen = null;
        if ($codigoEmpresa != 0) {
            $arEmpresa = $em->find(Empresa::class, $codigoEmpresa);
            if ($arEmpresa) {
                $nombreEmpresa = $arEmpresa->getNombre();
                if ($arEmpresa->getLogo()) {
                    $imagen = base64_encode(stream_get_contents($arEmpresa->getLogo()));
                }
                $registroFijo = $arEmpresa->isRegistroFijo();
            }
        }
        $arConfiguracion = $em->getRepository(Configuracion::class)->find(1);
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('aplicacion/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            'nombreEmpresa' => $nombreEmpresa,
            'imagen' => $imagen,
            'telefono' => $arConfiguracion->getTelefonoSoporte(),
            'registroFijo' => $registroFijo,
            'codigoEmpresa' => $codigoEmpresa
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('Esta funcion jamas debe ser llamada directamente');
    }


}
