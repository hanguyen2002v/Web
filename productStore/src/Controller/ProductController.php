<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/index', name: 'product_index')]
    public function productIndex(ProductRepository $productRepository)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        // $products = $productRepository->sortProductByIdDesc();
        return $this->render('product/index.html.twig', ['product' => $products]);
    }

    #[Route('/list', name: 'product_list')]
    public function productList(Request $request, ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        // $products = $paginatorInterface->paginate(
        //     $products,
        //     $request->query->getInt('page', 1), //số trang
        //     limit: 6 //giới hạn số lượng trong 1 trang
        // );
        return $this->render('product/list.html.twig', ['product' => $products]);
    }

    #[Route('/detail/{id}', name: 'product_detail')]
    public function productDetail($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);
        if ($product == null) {
            $this->addFlash('Warning', 'Invalid product ID !');
            return $this->redirectToRoute('product_index');
        }
        return $this->render('product/detail.html.twig', ['product' => $product]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/add', name: 'product_add')]
    public function productAdd(Request $request)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash('Info', 'Add product succed !');
            return $this->redirectToRoute('product_index');
        }
        return $this->renderForm('product/add.html.twig', ['productForm' => $form]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/edit/{id}', name: 'product_edit')]
    function FunctionName($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash('Warning', 'Product not existed !');
        } else {
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($product);
                $manager->flush();
                $this->addFlash('Success', 'Edit product succed!');
                return $this->redirectToRoute('product_index');
            }
            return $this->renderForm('product/edit.html.twig', ['productForm' => $form]);
        }
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/delete/{id}', name: 'product_delete')]
    public function productDelete($id, ManagerRegistry $managerRegistry)
    {
        $product = $managerRegistry->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash('Warning', 'Product not existed !');
        } else {
            $manager = $managerRegistry->getManager();
            $manager->remove($product);
            $manager->flush();
            $this->addFlash('Infor', 'Delete product succed !');
        }
        return $this->redirectToRoute('product_index');
    }

    #[Route('/search', name: 'search_product_name')]
    public function searchProductName(ProductRepository $productRepository, Request $request)
    {
        $name = $request->get('keyword');
        $product = $productRepository->searchProductByName($name);
        // $product = $paginatorInterface->paginate(
        //     $product,
        //     $request->query->getInt('page', 1), //số trang
        //     limit: 6 //giới hạn số lượng trong 1 trang
        // );
        return $this->render('product/list.html.twig', ['product' => $product]);
    }
}
