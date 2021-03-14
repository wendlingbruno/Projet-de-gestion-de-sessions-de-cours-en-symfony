<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    /**
     * @Route("/stagiaires", name="stagiaire_index")
     */
    public function index()
    {
        $stagiaires = $this->getDoctrine()
            ->getRepository(Stagiaire::class)
            ->getAll();
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }


    /**
     * @Route("/stagiaire/add", name="stagiaire_add")
     * @Route("/stagiaire/{id}/edit", name="stagiaire_edit")
     */
    public function addStagiaire(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $manager) {
        
        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }

        // $stagiaire = new Stagiaire();

        $form = $this->createForm(StagiaireType::class, $stagiaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $stagiaire = $form->getData();
            $manager->persist($stagiaire);
            $manager->flush();
        
            return $this->redirectToRoute('stagiaire_index');
        }
        
        return $this->render("stagiaire/add_edit.html.twig", [
            'formStagiaire' => $form->createView(),
            'editMode' => $stagiaire->getId() !== null,
            'stagiaire' => $stagiaire
        ]);

    }

    /**
     * @Route("/stagiaire/{id}", name="stagiaire_show", methods="GET")
     */
    public function show(Stagiaire $stagiaire = null): Response {

        if ($stagiaire){
            return $this->render('stagiaire/show.html.twig', [
                'stagiaire' => $stagiaire
            ]);
        }else{
            $this->addFlash("error", "Ce stagiaire n'existe pas.");
            return $this->redirectToRoute("stagiaire_index");
        }
    }

    /**
     * @Route("/stagiaire/{id}/delete", name="stagiaire_delete")
     */
    public function deleteStagiaire(Stagiaire $stagiaire = null, EntityManagerInterface $manager) {

        if ($stagiaire){
            foreach ($stagiaire->getSessions() as $sessions){
                $stagiaire->removeSession($sessions);
            }
            $manager->remove($stagiaire);
            $manager->flush();
            return $this->redirectToRoute('stagiaire_index');
        }else{
            $this->addFlash("error", "Ce stagiaire n'existe pas.");
             return $this->redirectToRoute('stagiaire_index'); 
        }
    }
            
}
