<?php

namespace App\Controller;

use App\Clases\Transmitir;
use App\Entity\Movimiento;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MovimientoController extends Controller
{
   /**
    * @Route("/movimiento/lista", name="movimiento_lista")
    */    
    public function lista(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $form = $this->createFormBuilder()
            ->add('btnTransferir', SubmitType::class, ['label' => 'Transferir', 'attr' => ['class' => 'btn btn-sm btn-default']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('btnTransferir')->isClicked()) {
                $arrMovimientos = $request->request->get('ChkSeleccionar');
                if($arrMovimientos) {
                    $transferir = new Transmitir($em);
                    $transferir->transferirMovimientos($arrMovimientos);
                }
                return $this->redirect($this->generateUrl('movimiento_lista'));
            }
        }
        $arMovimientos = $paginator->paginate($em->getRepository(Movimiento::class)->lista(1), $request->query->getInt('page', 1), 30);
        return $this->render('movimiento.html.twig',
            [
                'arMovimientos' => $arMovimientos,
                'form' => $form->createView()
            ]);
    }
}

