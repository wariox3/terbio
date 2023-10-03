<?php


namespace App\Controller\Aplicacion\Empresa;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InicioController extends AbstractController
{
    /**
     * @Route("/empresa", name="empresa_inicio")
     */
    public function inicioAction(Request $request)
    {
        return $this->render('aplicacion/inicio.html.twig');
    }
}