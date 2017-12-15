<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Entity\Entry;
use BlogBundle\Form\EntryType;

class EntryController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function addAction(Request $request) {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        /* recoger lo que llega del form */
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
//                $em = $this->getDoctrine()->getManager();
//                $category = new Category();
//                $category->setName($form->get("name")->getData());
//                $category->setDescription($form->get("description")->getData());
//                $em->persist($category);
//                $flush = $em->flush();
//                if ($flush == null) {
//                    $status = "la categoria se ha creado correctamente";
//                } else {
//                    $status = "error al añadir la categoría";
//                }
            } else {
                $status = "la categoría no se ha creado, el form no es valido";
            }
            /* crear flag */
            //     $this->session->getFlashBag()->add("status", $status);
            //   return $this->redirectToRoute("blog_index_category");
        }
        return $this->render("BlogBundle:Entry:add.html.twig", array(
                    "form" => $form->createView()
        ));
    }

}
