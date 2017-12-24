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
                $em = $this->getDoctrine()->getManager();
                //repositorio para juntar la id del select
                //con la id de la categoria de la bd
                $category_repo = $em->getRepository("BlogBundle:Category");
                //objeto entry para guardar datos
                $entry = new Entry();
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());
                
                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time().".".$ext;
                $file->move("uploads",$file_name);
                
                
                $entry->setImage($file_name);

                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);
                
                $user = $this->getUser();
                $entry->setUser($user);
                $em->persist($entry);
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
            //return $this->redirectToRoute("blog_index_entry");
        }
        return $this->render("BlogBundle:Entry:add.html.twig", array(
                    "form" => $form->createView()
        ));
    }

}
