<?php

namespace App\Controller;

use App\Entity\Sessions;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProgrammeController extends AbstractController
{
    /**
     * @Route("/sessions/programme/{id}", name="detailProgramme")
     */
    public function index(Sessions $sessions = null): Response
    {
        if ($sessions){
            $session = $this->getDoctrine()
                            ->getRepository(Programme::class)
                            ->getProgramme($sessions);
            return $this->render('programme/index.html.twig', [
                'sessions' => $sessions,
                'programmes' => $session
            ]);
        }else{
            $this->addFlash("error", "Cette session n'existe pas.");
            return $this->redirectToRoute('sessions');
        }
    }

    /**
     * @Route("/programme/{id}/delete", name="programme_delete")
     */
    public function deleteProgramme(Programme $programme = null, EntityManagerInterface $manager) {

    
        if ($programme){
            $session = $programme->getsessions()->getId();

            $manager->remove($programme);
            $manager->flush();

            return $this->redirectToRoute('detailProgramme', ['id'=>$session]);
        }else{
            $this->addFlash("error", "Ce programme n'existe pas");
            return $this->redirectToRoute('sessions');
        }
    }
    
     /**
     * @Route("/sessions/{session_id}/programme/unsubscribe/{stagiaire_id}", name="unsubscribe_stagiaire")
     * @ParamConverter("sessions", options={"id" = "session_id"})
     * @ParamConverter("stagiaire", options={"id" = "stagiaire_id"})
     */
    public function removeStagiaire(Sessions $sessions = null, Stagiaire $stagiaire = null, EntityManagerInterface $manager){

        if ($sessions && $stagiaire){
            dump($sessions);
            dump($stagiaire);
            $sessions->removeStagiaire($stagiaire);
            $manager->persist($sessions);
            $manager->flush();

            return $this->redirectToRoute('sessions_detail', ['id' => $sessions->getId()]);
        }else{
            $this->addFlash("error", "Veuillez vérifier la session et/ou le stagiaire.");
            return $this->redirectToRoute('sessions');
        }
    } 
}
