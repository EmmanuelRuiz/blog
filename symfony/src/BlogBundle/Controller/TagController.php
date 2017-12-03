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

    public function addAction(Request $request){
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        
        /*recoger lo que llega del form*/
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $status = "la etiqueta se ha creado correctamente";
            }  else {
                $status = "la etiqueta no se ha creado, el form no es valido";
            }
            /*crear flag*/
        $this->session->getFlashBag()->add("status", $status);
        }
        
        
        
        return $this->render("BlogBundle:Tag:add.html.twig", array(
            "form" => $form->createView()
        ));
    }
}
