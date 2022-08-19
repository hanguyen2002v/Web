<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted("ROLE_ADMIN")]
#[Route('/category')]
class CategoryController extends AbstractController
{

    #[Route('/index', name: 'category_index')]
    public function categoryIndex()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', ['category' => $categories]);
    }

    #[Route('/list', name: 'category_list')]
    public function categoryList()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', ['category' => $categories]);
    }

    #[Route('/add', name: 'category_add')]
    public function categoryAdd(Request $request)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('Info', 'Category add succed');
            return $this->redirectToRoute('category_index');
        }
        return $this->renderForm('category/add.html.twig', ['categoryForm' => $form]);
    }

    #[Route('/edit/{id}', name: 'category_edit')]
    public function categoryEdit($id, Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash('Error', 'Category not found !');
        } else {
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($category);
                $manager->flush();
                $this->addFlash('Success', 'Edit category success !');
                return $this->redirectToRoute('category_index');
            }
            return $this->renderForm('category/edit.html.twig', ['categoryForm' => $form]);
        }
    }

    #[Route('/delete/{id}', name: 'category_delete')]
    public function categoryDelete($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);
        if ($category == null) {
            $this->addFlash('Error', 'category not found !');
        } else if (count($category->getProducts()) >= 1) {
            $this->addFlash('Warning', 'Can not delete this category');
        } else {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($category);
            $manager->flush();
            $this->addFlash('Success', 'Delete category success');
        }
        return $this->redirectToRoute('category_index');
    }
}
