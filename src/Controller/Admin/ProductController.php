<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Services\Upload;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class ProductController
 * @Route("admin/product")
 * @package App\Controller\Admin
 */
class ProductController extends AbstractController
{

    const SECTION = "Produits";

    /**
     *
     * @Route("/", methods="GET",name="admin_product")
     * @param Request $request
     * @param ProductRepository $repository
     * @return void
     */
    public function index (Request $request, ProductRepository $repository): Response {

        $products = $repository->findBy([],['created_at'=>'DESC']);
        return $this->render('Admin/Product/index.html.twig',[
            'section'=>self::SECTION,
            'page' => "Listing des produits",
            'products'=>$products
        ]);
    }

    /**
     *
     * @Route("/create", methods={"GET|POST"},name="admin_product_create")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return void
     */
    public function create (Request $request, ManagerRegistry $doctrine, Upload $uploader, SluggerInterface $slugger): Response {

        $product = new Product();
        $form    = $this->createForm(ProductType::class, $product,[
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
        if( $form->isSubmitted() && $form->isValid() ) {
            $this->manage($form, $product,$doctrine,$uploader,$slugger,$destination);
            return $this->redirectToRoute('admin_product');
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->render('Admin/Product/create.html.twig',[
            'section'=>self::SECTION,
            'page' => "Ajout d'un nouveau produit",
            'form'=>$form->createView()
        ]);
    }

    /**
     *
     * @Route("/{id<\d+>}/edit",name="admin_product_update", methods={"GET","PUT"})
     * @param Request $request
     * @param Product $product
     * @param ManagerRegistry $doctrine
     * @param Upload $uploader
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function  update (Request $request, Product $product, ManagerRegistry $doctrine, Upload $uploader, SluggerInterface $slugger): Response
    {
        $destination = $this->getParameter('kernel.project_dir').'/public/uploads';

        $form = $this->createForm(ProductType::class, $product,[
            'method' => 'PUT',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manage($form, $product,$doctrine, $uploader,$slugger,$destination,"édité");
            return $this->redirectToRoute('admin_product');
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->render('Admin/Product/edit.html.twig',[
            'section'=>self::SECTION,
            'page' => "Edition d'un produit",
            'form'=>$form->createView()
        ]);
    }


    /**
     *
     * @Route("/{id<\d+>}/delete", methods={"DELETE"},name="admin_product_delete")
     * @param Request $request
     * @param Product $product
     * @param ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    public function delete (Request $request, Product $product, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $em = $doctrine->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('admin_product');
    }

    /**
     * @param FormInterface $form
     * @param $product
     * @param ManagerRegistry $doctrine
     * @param Upload $uploader
     * @param SluggerInterface $slugger
     * @param string $destination
     * @param string $finalWord
     * @return void
     */
    private function manage(FormInterface $form, $product, ManagerRegistry $doctrine, Upload $uploader,SluggerInterface $slugger, string $destination, string $finalWord="ajouté"): void
    {
        $em = $doctrine->getManager();
        $image = $form->get('dlimage')->getData();

        if( $image ){
            $filename = $uploader->uploadImage($image,$slugger,$destination);
            $product->setImage($filename);
        }

        $product->setUpdatedAt(new \DateTime());
        $em->persist($product);
        $em->flush();

        $this->addFlash('success', 'Le produit : "'.$product->getName().'" à bien été '.$finalWord);
    }

}