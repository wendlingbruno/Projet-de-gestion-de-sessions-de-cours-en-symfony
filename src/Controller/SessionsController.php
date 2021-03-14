<?php

namespace App\Controller;

use App\Entity\Modules;
use App\Entity\Sessions;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\ModulesType;
use App\Form\SessionsType;
use App\Form\ProgrammeType;
use App\Form\SessionsStagiaireType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionsController extends AbstractController
{
    /**
     * @Route("/sessions", name="sessions")
     */
    public function index()
    {
        $sessions = $this->getDoctrine()
                           ->getRepository(Sessions::class)
                           ->getAll();
        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }


    /**
     * @Route("/session/{id}/delete", name="session_delete")
     */
    public function deleteSession(Sessions $sessions = null, EntityManagerInterface $manager) {
      
        if ($sessions){
            foreach ($sessions->getProgrammes() as  $programme) {
                $manager->remove($programme);
            }
                foreach ($sessions->getStagiaires() as $stagiaire) {
                    $sessions->removeStagiaire($stagiaire);
                }
                
            $manager->remove($sessions);
            $manager->flush();
        
            return $this->redirectToRoute('sessions');
        }else{
            $this->addFlash("error", "Cette session n'existe pas.");
            return $this->redirectToRoute("sessions");
        }
    }




    /**
     * @Route("/sessions/edit/{id}", name="sessions_edit")
     * @Route("/sessions/add/", name="sessions_add")
     */
    public function addSessions(Sessions $sessions = null, Request $request, ManagerRegistry $manager): Response{
        
        if (!$sessions){
            $edit = false;
            $sessions = new Sessions();
        }else{
            $edit = true;
        }

            $form = $this->createForm(SessionsType::class, $sessions);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){        
                    $valeurs = ($request->request->get('sessions'));
                    if (($valeurs['dateFin']) > $valeurs['dateDebut']){ // pour empêcher d'avoir une date de fin inférieure à la date de début
                        
                        if ($edit){
                            if($sessions->getPlacesMax() - count($sessions->getStagiaires()) < 0 ){ // Vérifie que après édition, on ne met pas moins de places que le nombre actuel d'inscrits dans la session
                                $this->addFlash("error", "Le nombre d'inscrits est supérieur au nombre maximum de la session.");
                                return $this->redirectToRoute('sessions_detail', ["id"=>$sessions->getId()]); 
                            }

                        }
                        $em = $manager->getManager();
                        $em->persist($sessions);
                        $em->flush();

                        return $this->redirectToRoute('sessions');
                    }else{
                        $this->addFlash("error", "Merci de mettre une date de fin supérieure à la date de début.");
                    }
            }
            return $this->render('sessions/add.html.twig',
            [
                'formSessions' => $form->createView(),
                'editMode' => $sessions->getId() !== null,
                'sessions' => $sessions
            ]);
    }

    /**
     * @Route("/sessions/detail/{id}", name="sessions_detail")
     */
    public function detailSession(Sessions $sessions = null): Response{
        
        if (!$sessions){
            $this->addFlash("error", "Cette session n'existe pas.");
            return $this->redirectToRoute('sessions');
        }

            return $this->render('sessions/detail.html.twig',
            [
                'sessions' => $sessions
            ]);
    }

    
    /**
     * @Route("/sessions/add/programme/{id}", name="sessions_add_programme")
     * @Route("/sessions/edit/programme/{id2}", name="sessions_edit_programme")
     * @ParamConverter("session", options={"id" = "id"})
     * @ParamConverter("programme", options={"id" = "id2"})
     */ 
    public function addProgrammeSession(Sessions $sessions = null, Programme $programme = null, Request $request, ManagerRegistry $manager): Response{


        if(!$sessions && !$programme){ //1
            $this->addFlash("error", "Cette session ou ce programme n'existe pas.");
            return $this->redirectToRoute('sessions');
        }
        $edit = false;
        if (!$programme){
            $programme = new Programme();
        }else{
            $edit = true;
            $jours = $programme->getJours();
            $nomModule = $programme->getModules();
        }
 
            $form = $this->createForm(ProgrammeType::class, $programme);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                if (!$edit){ //ADD
                    $redirect = $sessions->getId();
                }else{
                    $redirect = $programme->getSessions()->getId();
                }
                $valeurs = ($request->request->get('programme'));
                $moduleExist = $this->getDoctrine() // juste récupérer ce qu'il y a dans la BDD
                ->getRepository(Programme::class)
                ->verifProgrammeExist($redirect, $valeurs['modules']);
                $chiffre = $valeurs['jours']; //correspond aux nb de jours mis dans le formulaire par l'utilisateur
 
                if (!$moduleExist){ // on  vérifie si le programme n'existe pas déjà (pas su le faire via le  unique entity fields car le formulaire n'a qu'un champ au lieu des deux)
 
                    $em = $manager->getManager();
                    if (!$edit){
                        $programme->setSessions($sessions);
                    }
                    $em->persist($programme);
                    $em->flush();
                    if (!$edit){
                        $this->addFlash("success", "Le programme a bien été ajouté.");
                        return $this->redirectToRoute('detailProgramme', ['id' => $sessions->getId()]);
                        
                    }else{
                        $this->addFlash("success", "Le programme a bien été édité.");
                        return $this->redirectToRoute('detailProgramme', ['id' => $programme->getSessions()->getId()]);
                    }
                }else if ($edit){
                
                    if ($jours != $chiffre){
                        $em = $manager->getManager();
                        $programme->setJours($chiffre);
                        $programme->setModules($nomModule);
                        $em->persist($programme);
                        $em->flush();
                        $this->addFlash("success", "Les jours ont bien été édités");
                        return $this->redirectToRoute('detailProgramme', ['id' => $redirect]);
                    }else{
                        $this->addFlash("error", "Aucune modification n'a été apporté.");
                        return $this->redirectToRoute('detailProgramme', ['id' => $redirect]);
                    }
                }else{
                    $this->addFlash("error", "Le programme existe déjà.");
                    return $this->redirectToRoute('detailProgramme', ['id' => $redirect]);  
                }
            }
            return $this->render('sessions/addProgramme.html.twig',
            [
                'formProgramme' => $form->createView(),
                'editMode' => $programme->getId() !== null,
                'programme' => $programme,
            ]);
    }


    // public function addProgrammeSession( Sessions $sessions = null, Programme $programme = null, Request $request, ManagerRegistry $manager): Response{
        
    //     $edit = false;
    //     if (!$programme){
    //         $programme = new Programme();
    //         $jours = 0;
    //     }else{
    //         $edit = true;
    //         $jours = $programme->getJours();
    //     }   
    //     $programme->setSessions($sessions);

    //         $form = $this->createForm(ProgrammeType::class, $programme);

    //         $form->handleRequest($request);
    //         if($form->isSubmitted() && $form->isValid()){
    //             if (!$edit){
    //                 $redirect = $sessions->getId();
    //             }else{
    //                 $redirect = $programme->getSessions()->getId();
    //             }

    //             $valeurs = ($request->request->get('programme'));
                
    //             $moduleExist = $this->getDoctrine()
    //                 ->getRepository(Programme::class)
    //                 ->verifProgrammeExist($redirect, $valeurs['modules']);
    //             $moduleToAdd = $this->getDoctrine()->getRepository(Modules::class)->find($valeurs['modules']);
    //             $chiffre = $valeurs['jours'];   

    //             if (!$moduleExist){ // on  vérifie si le programme n'existe pas déjà (pas su le faire via le  unique entity fields car le formulaire n'a qu'un champ au lieu des deux)

    //                 $em = $manager->getManager();
    //                 if (!$edit){
    //                     $programme->setSessions($sessions);
    //                 }
    //                 $em->persist($programme);
    //                 $em->flush();
    //                 if (!$edit){
    //                     $this->addFlash("success", "Le programme a bien été ajouté.");
    //                     return $this->redirectToRoute('detailProgramme', ['id' => $sessions->getId()]);
                        
    //                 }else{
    //                     $this->addFlash("success", "Le programme a bien été édité.");
    //                     return $this->redirectToRoute('detailProgramme', ['id' => $programme->getSessions()->getId()]);
    //                 }
    //             }else if($jours != $chiffre){
    //                 $em = $manager->getManager();
    //                 $programme->setJours($chiffre);
    //                 $programme->setModules($moduleToAdd);
                   
    //                 $em->persist($programme);
    //                 $em->flush();
    //                 $this->addFlash("success", "Les jours ont bien été édités");
    //                 return $this->redirectToRoute('detailProgramme', ['id' => $redirect]);
    //             }else{
    //                 $this->addFlash("error", "Le programme existe déjà.");
    //                 return $this->redirectToRoute('detailProgramme', ['id' => $redirect]);  
    //             }
    //         }
    //         return $this->render('sessions/addProgramme.html.twig',
    //         [
    //             'formProgramme' => $form->createView(),
    //             'editMode' => $programme->getId() !== null,
    //             'programme' => $programme,
    //         ]);
    // }

      /**
     * @Route("/sessions/{id}/stagiaires/add/", name="sessionsStagiaires_add")
     */
    public function addStagiaireSession(Sessions $session = null, Request $request, ManagerRegistry $manager): Response{
        
        if ($session){    
            if($session->getPlacesMax() - count($session->getStagiaires()) != 0 ){
                $form = $this->createForm(SessionsStagiaireType::class);
                
                $form->handleRequest($request);
                
                if($form->isSubmitted() && $form->isValid()){   
        
                $em = $manager->getManager();
                foreach ($form->getData() as $stagiaireNew) {
                    $session->addStagiaire($stagiaireNew);
                }
                $em->persist($session);
                $em->flush();
                return $this->redirectToRoute('sessions_detail', ["id"=>$session->getId()]); 

            }
            return $this->render('sessions/addStagiaire.html.twig',
            [
                'formSessionStagiaire' => $form->createView(),
                'session' => $session
            ]);
            }else{
                $this->addFlash("error", "Nombre de personne maximum dans la sessions atteint");
                return $this->redirectToRoute('sessions_detail', ["id"=>$session->getId()]); 
            }
        }else{
            $this->addFlash("error", "Cette session n'existe pas.");
            return $this->redirectToRoute('sessions'); 
        }
        
    }



    // public function addStagiaireSession(Sessions $session = null, Request $request, ManagerRegistry $manager): Response{

    //     $stagiaires = $this->getDoctrine()
    //         ->getRepository(Stagiaire::class)
    //         ->getAll();

    //     $form = $this->createForm(SessionsType::class, $session);

    //     $form->handleRequest($request);
    //     if($form->isSubmitted() && $form->isValid()){   
    //         $stagiaire = ($request->request->get('stagiaire'));  
    //         var_dump($stagiaire); 
    //         // $em = $manager->getManager();
    //         // $em->persist($session);
    //         // $em->flush();

    //         // return $this->redirectToRoute('sessions');
    //     }
    //     return $this->render('sessions/addStagiaire.html.twig',
    //     [
    //         'formSessionStagiaire' => $form->createView(),
    //         'session' => $session,
    //         'stagiaires' => $stagiaires,
    //     ]);

    // }

    /**
     * @Route("/sessions/{id}/stagiaires/addOk/", name="sessionsStagiaires_addOk")
     */
//     public function addStagiaireSession2(Sessions $session = null, Stagiaire $stagiaire = null, Request $request, ManagerRegistry $manager){
 
//         $ok = true;
//         $stagiaires = $this->getDoctrine() // juste récupérer ce qu'il y a dans la BDD
//             ->getRepository(Stagiaire::class)
//             ->specificStagiaire($_POST['stagiaire']);

// // rajouter un filter

//         if ($stagiaires){
//             foreach ($stagiaires as $stagiaire) { // récupérer l'objet stagiaire
//                 $envoi = $stagiaire;
//             }
//             foreach ($session->getStagiaires() as $test){ // vérifie si le stagiaire est pas déjà dans la session
//                 if ($test == $envoi ){
//                     $ok = false;
//                     $this->addFlash("error", "Le stagiaire est déjà dans cette session.");
//                 }
//             }

//                 if ($ok){
//                     $em = $manager->getManager();
//                     $session->addStagiaire($envoi);
//                     $em->persist($session);
//                     $em->flush();
//                     $this->addFlash("success", "Le stagiaire a bien été ajouté.");
//                 }
//         }else{
//             $this->addFlash("error", "Le stagiaire n'existe pas.");
//         }

//             return $this->redirectToRoute('sessions_detail', ['id' => $session->getId()]);

//     }
}
