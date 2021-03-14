<?php

namespace App\Controller;

use App\Entity\Sessions;
use App\Entity\Stagiaire;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $sessions = $this->getDoctrine()->getRepository(Sessions::class)->getAll();
        $stagiaires = $this->getDoctrine()->getRepository(Stagiaire::class)->getAllOrder();
        $stagiairesTotal = $this->getDoctrine()->getRepository(Stagiaire::class)->getAll();
        return $this->render('home.html.twig', [
            'sessions' => $sessions,
            "stagiaires" => $stagiaires,
            "stagiairesTotal" => $stagiairesTotal,
            ]);
    }
}
