<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }


      /**
     * @Route("/admin/{id}", name="admin_show", methods="GET")
     */
    public function show(Admin $admin = null){

        if ($admin){
            return $this->render('admin/index.html.twig', [
                'admin' => $admin
            ]);
        }else{
            $this->addFlash("error", "Cet administrateur n'existe pas.");
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/admin/{id}/edit", name="admin_edit")
     */
    public function editAdmin(Admin $admin = null, Request $request, EntityManagerInterface $manager): Response {

        if ($admin){
            if($admin->getId() == $this->getUser()->getId()){

                $form = $this->createForm(AdminType::class, $admin);

                $form->handleRequest($request);
        
                if($form->isSubmitted() && $form->isValid()) {
        
                    // dump($admin);
                    // $admin = $form->getData();
                    $manager->persist($admin);
                    $manager->flush();
                    $this->addFlash("success", "Le profil a bien été édité.");
                    return $this->redirectToRoute("admin_show", ['id'=>$admin->getId()]);
                }
        
                return $this->render("admin/edit.html.twig", [
                    "formAdmin" => $form->createView(),
                    'admin' => $admin
                ]);

            } else {
                $this->addFlash("error", "Accès interdit.");
                return $this->redirectToRoute("home");
            }
        }else{
            $this->addFlash("error", "Cet administrateur n'existe pas.");
            return $this->redirectToRoute('home');
        }
       

    }

    /**
     * @Route("/admin/{id}/changePassword", name="admin_edit_password")
     */
    public function editAdminPassword(Admin $admin = null, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response {

        if ($admin){
            if($admin->getId() == $this->getUser()->getId()){

                $form = $this->createForm(ChangePasswordType::class);

                $form->handleRequest($request);
        
                if($form->isSubmitted() && $form->isValid()) {
        
                    $mdp = $form->get('plainPassword')->getData();
                    $mdp = $passwordEncoder->encodePassword($admin, $mdp);
                    $this->getDoctrine()->getRepository(Admin::class)->upgradePassword($admin, $mdp);    
                    $this->addFlash("success", "Le mot de passe a bien été changé.");
                    return $this->redirectToRoute("admin_show", ['id'=>$admin->getId()]);          
                }
        
                return $this->render("admin/editPassword.html.twig", [
                    "formAdmin" => $form->createView()
                ]);

            } else {
                $this->addFlash("error", "Accès interdit.");
                return $this->redirectToRoute("home");
            }
        }else{
            $this->addFlash("error", "Cet administrateur n'existe pas.");
            return $this->redirectToRoute('home');
        }
       

    }

}
