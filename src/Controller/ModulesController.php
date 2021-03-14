<?php

namespace App\Controller;

use App\Entity\Modules;
use App\Entity\Programme;
use App\Form\ModulesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModulesController extends AbstractController
{
    /**
     * @Route("/modules", name="modules")
     */
    public function index()
    {

        $modules = $this->getDoctrine()
                           ->getRepository(Modules::class)
                           ->getAll();
        return $this->render('modules/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    /**
     * @Route("/modules/add", name="modules_add")
     * @Route("/modules/edit/{id}", name="modules_edit")
     */
    public function addModules(Modules $modules = null, Request $request, ManagerRegistry $manager): Response{

        if (!$modules){
            $modules = new Modules();
        }

        $form = $this->createForm(ModulesType::class, $modules);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){        
            $em = $manager->getManager();
            $em->persist($modules);
            $em->flush();

            return $this->redirectToRoute('modules');
        }
        return $this->render('modules/add.html.twig',
        [
            'formModules' => $form->createView(),
            'editMode' => $modules->getId() !== null,
            'modules' => $modules
        ]);

    }

    /**
     * @Route("/modules/delete/{id}", name="modules_delete")
     */
    public function deletemodules(Modules $modules = null, ManagerRegistry $manager): Response{
        
        $bg = $this->getDoctrine()->getRepository(Programme::class)->deleteProgrammeByModule($modules);

        if (!$modules){
            $this->addFlash("error", "Ce module n'existe pas.");
            return $this->redirectToRoute('modules');
        } 
            $em = $manager->getManager();
            
            foreach ($bg as $module) {
               $em->remove($module);
            }
                $em->remove($modules);

                $em->flush();

            return $this->redirectToRoute('modules');
    }

    /**
     * @Route("/modules/listSessions/{id}", name="modules_by_session")
     */
    public function getSessionsByModule(Modules $modules = null){

        if ($modules){
            $moduleList = $this->getDoctrine()->getRepository(Programme::class)->getSessionByModule($modules->getId());
            return $this->render('modules/listModulesSession.html.twig',
            [
                'modulesList' => $moduleList,
                'module' => $modules

            ]);
        }else{
            $this->addFlash("error", "Ce module n'existe pas.");
            return $this->redirectToRoute('modules');
        }
    }

}
