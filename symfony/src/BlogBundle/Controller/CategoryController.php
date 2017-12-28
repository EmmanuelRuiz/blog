<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Entity\Category;
use BlogBundle\Form\CategoryType;

class CategoryController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categories = $category_repo->findAll();

        return $this->render("BlogBundle:Category:index.html.twig", array(
                    "categories" => $categories
        ));
    }

    public function addAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        /* recoger lo que llega del form */
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $category = new Category();
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em->persist($category);
                $flush = $em->flush();
                if ($flush == null) {
                    $status = "la categoria se ha creado correctamente";
                } else {
                    $status = "error al añadir la categoría";
                }
            } else {
                $status = "la categoría no se ha creado, el form no es valido";
            }
            /* crear flag */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_category");
        }



        return $this->render("BlogBundle:Category:add.html.twig", array(
                    "form" => $form->createView()
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $category = $category_repo->find($id);

        //si la etiqueta no está en uso se elimina
        if (count($category->getEntries()) == 0) {
            $em->remove($category);
            $em->flush();
        }
        return $this->redirectToRoute("blog_index_category");
    }

    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $category = $category_repo->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em->persist($category);
                $flush = $em->flush();
                if ($flush == null) {
                    $status = "la categoria se ha editado correctamente";
                } else {
                    $status = "error al editar la categoría";
                }
            } else {
                $status = "la categoría no se ha editado, el form no es valido";
            }
            /* crear flag */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_category");
        }



        return $this->render("BlogBundle:Category:edit.html.twig", array(
                    "form" => $form->createView()
        ));
    }

    /* metodo para listar las entradas por categorías */

    public function categoryAction($id, $page) {
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $category = $category_repo->find($id);

        //llamar getCategoryEntries
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entries = $entry_repo->getCategoryEntries($category, 5, $page);

        /* mostrar links */
        $totalItems = count($entries);
        //redondear el resultado de totalitems entre pagesize
        $pagesCount = ceil($totalItems / 5);

        return $this->render("BlogBundle:Category:category.html.twig", array(
                    "category" => $category,
                    "categories" => $category_repo->findAll(),
                    "entries" => $entries,
                    "totalItems" => $totalItems,
                    "pagesCount" => $pagesCount,
                    "page" => $page,
                    "page_max" => $page
        ));
    }

}
