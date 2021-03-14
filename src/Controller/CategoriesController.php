<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Categories;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/categories", name="categories_index")
     */
    public function index()
    {   
        $categories = $this->getDoctrine()
            ->getRepository(Categories::class)
            ->getAll();
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/categories/{id}/edit", name="categories_edit")
     * @Route("/categories/add", name="categories_add")
     */
    public function addCategories(Request $request, EntityManagerInterface $manager, Categories $categories = null) {
                    // = null car pas id
        if(!$categories) {

            $categories = new Categories();
        }

        $form = $this->createForm(CategorieType::class, $categories);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
       
           
            $manager->persist($categories);
            $manager->flush();


        return $this->redirectToRoute('modules');
        }   
        
        return $this->render("categories/add.html.twig", [
            'formCategories' => $form->createView(),
            'editMode' => $categories->getId() !== null,
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/categories/{id}/delete", name="categorie_delete")
     */

     public function deleteCategorie(Categories $categories, EntityManagerInterface $manager) {

        // foreach ($categories->getModules() as $modules) {
           
        //    $manager->remove($modules);
        // }

        // $manager->remove($categories);
        // $manager->flush();
     
        return $this->redirectToRoute('modules');
    }

    /**
     * @Route("/categorie/{id}/remove", name="categorie_remove")
     */

     public function removeCategorie(Categories $categories, EntityManagerInterface $manager) {
        foreach ($categories->getModules() as $modules) {
            $modules->removeCategories($categories);
        }

        $manager->persist($categories);
        $manager->flush();
     
        return $this->redirectToRoute('modules');
    }
}
