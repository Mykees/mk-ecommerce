<?php

namespace App\Controller\Admin;

use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;

/**
 * Class CategoryController
 * @Route("admin/category")
 * @package App\Controller\Admin
 */
class CategoryController extends AbstractController
{

    const SECTION = 'Categorie';

    /**
     * @ROute("/", name="admin_category")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index (CategoryRepository $categoryRepository): Response {

        $categories = $categoryRepository->findBy([],['created_at'=>'DESC']);
        return $this->render('Admin/Category/index.html.twig',[
            'section'=>self::SECTION,
            'page' => "Listing des categories",
            "categories"=>$categories
        ]);
    }

    /**
     * @ROute("/create", name="admin_category_create")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function create (Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ){
            $this->manage($form, $category,$doctrine,$slugger);
            return $this->redirectToRoute('admin_category');
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }


        return $this->render('Admin/Category/create.html.twig',[
            'section'=>self::SECTION,
            'page' => "Ajout d'une nouvelle categorie",
            'form'=>$form->createView()
        ]);
    }

    /**
     * @ROute("/{id<\d+>}/edit", methods={"GET|PUT"}, name="admin_category_update")
     * @param Request $request
     * @return Response
     */
    public function update (Request $request, Category $category, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(CategoryType::class, $category,[
            'method' => 'PUT',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manage($form, $category,$doctrine,$slugger,"édité");
            return $this->redirectToRoute('admin_category');
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }
        return $this->render('Admin/Category/update.html.twig',[
            'section'=>self::SECTION,
            'page' => "Edition d'une categorie",
            'form'=>$form->createView()
        ]);
    }

    /**
     * @ROute("/{id<\d+>}/delete", name="admin_category_delete", methods={"DELETE"})
     * @param Request $request
     * @param Category $category
     * @param ManagerRegistry $doctrine
     * @return RedirectResponse
     */
    public function delete (Request $request, Category $category, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em = $doctrine->getManager();
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('admin_category');
    }


    private function manage(
        FormInterface $form,
        $category,
        ManagerRegistry $doctrine,
        SluggerInterface $slugger,
        string $finalWord="ajouté")
    {
        $em = $doctrine->getManager();

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', 'La catégorie : "'.$category->getName().'" à bien été '.$finalWord);
    }
}