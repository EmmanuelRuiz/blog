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
        $this->createForm(TagType::class, $tag);
        
        
        return $this->render("BlogBundle:Tag:add.html.twig", array(
            "form" => $form->createView()
        ));
    }
}
