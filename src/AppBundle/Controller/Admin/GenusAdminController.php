<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\GenusFormType;
use AppBundle\Entity\Genus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 */
class GenusAdminController extends Controller
{
    /**
     * @Route("/genus", name="admin_genus_list")
     */
    public function indexAction()
    {
        $genuses = $this->getDoctrine()
            ->getRepository('AppBundle:Genus')
            ->findAll();

        return $this->render('admin/genus/list.html.twig', array(
            'genuses' => $genuses
        ));
    }

    /**
     * @Route("/genus/new", name="admin_genus_new")
     * @param $request Request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(GenusFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())  {

            $genus = $form->getData();

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($genus);
            $em->flush();

            $this->addFlash('genusAdded', 'It looks kind of shady, but we added your genus');

            return $this->redirectToRoute('admin_genus_list');
        }
        return $this->render('admin/genus/new.html.twig', ['genusForm' => $form->createView()]);
    }

    /**
     * @Route("/genus/{id}/edit", name="admin_genus_edit")
     * @param Request $request
     * @param Genus $genus
     * @return Response
     */
    public function editAction(Request $request, Genus $genus)
    {
        $form = $this->createForm(GenusFormType::class, $genus);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())  {

            $genus = $form->getData();

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($genus);
            $em->flush();

            $this->addFlash('genusAdded', 'It looks kind of shady, but we added your genus');

            return $this->redirectToRoute('admin_genus_list');
        }
        return $this->render('admin/genus/new.html.twig', ['genusForm' => $form->createView()]);
    }
}