<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Entity\Tag;
use BlogBundle\Form\TagType;

class TagController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tags = $tag_repo->findAll();

        return $this->render("BlogBundle:Tag:index.html.twig", array(
                    "tags" => $tags
        ));
    }

    public function addAction(Request $request) {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        /* recoger lo que llega del form */
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $tag = new Tag();
                $tag->setName($form->get("name")->getData());
                $tag->setDescription($form->get("description")->getData());
                $em->persist($tag);
                $flush = $em->flush();
                if ($flush == null) {
                    $status = "la etiqueta se ha creado correctamente";
                } else {
                    $status = "error al añadir la etiqueta";
                }
            } else {
                $status = "la etiqueta no se ha creado, el form no es valido";
            }
            /* crear flag */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_tag");
        }



        return $this->render("BlogBundle:Tag:add.html.twig", array(
                    "form" => $form->createView()
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tag = $tag_repo->find($id);
        
        //si la etiqueta no está en uso se elimina
        if (count($tag->getEntryTag()) == 0) {
            $em->remove($tag);
            $em->flush();  
        }
        return $this->redirectToRoute("blog_index_tag");
    }

}
