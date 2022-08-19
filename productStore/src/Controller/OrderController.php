<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\OrderRepository;

use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/order/view', name: 'order_view')]
    public function orderView()
    {
        $order = $this->getDoctrine()->getRepository(Order::class)->findAll();
        return $this->render('order/view.html.twig', ['order' => $order]);
    }

    #[Route('/cart/info', name: 'add_to_cart')]
    public function addToCart(Request $request)
    {
        $session = $request->getSession();
        $id = $request->get('id');
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $quantity = $request->get('quantity');
        $datetime = date('Y/m/d H:i:s');
        $user = $this->getUser();
        $productprice = $product->getPrice();
        $totalprice = $productprice * $quantity;
        $session->set('product', $product);
        $session->set('user', $user);
        $session->set('quantity', $quantity);
        $session->set('totalprice', $totalprice);
        $session->set('datetime', $datetime);

        $order = new Order();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($request->get('id'));
        $order->getProduct('product', $product);
        $quantity = $request->get('quantity');
        $order->getQuantity('quantity', $quantity);
        $datetime = date('Y/m/d H:i:s');
        $order->getDatetime('datetime', $datetime);
        $price = $product->getPrice();
        $totalprice = $price * $quantity;
        $order->getTotalprice('price', $totalprice);
        $user = $this->getUser();
        $order->getUser('user', $user);

        $order->setUser($user);
        $order->setProduct($product);
        $order->setQuantity($quantity);
        $order->setTotalprice($totalprice);
        $order->setDatetime(\DateTime::createFromFormat('Y/m/d H:i:s', $datetime));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($order);
        $manager->flush();
        $this->addFlash('Info', 'Order succed !');
        return $this->render('order/index.html.twig');
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/delete/{id}', name: 'order_delete')]
    public function orderDelete($id, ManagerRegistry $managerRegistry)
    {
        $order = $managerRegistry->getRepository(Order::class)->find($id);
        if ($order == null) {
            $this->addFlash('Warning', 'Product not existed !');
        } else {
            $manager = $managerRegistry->getManager();
            $manager->remove($order);
            $manager->flush();
            $this->addFlash('Infor', 'Delete product succed !');
        }
        return $this->redirectToRoute('order_view');
    }

    #[Route('/order/list', name: 'order_list')]
    public function orderList(OrderRepository $orderRepository)
    {
        $order = $orderRepository->findAll();
        return $this->render('order/list.html.twig', ['order' => $order]);
    }
}
