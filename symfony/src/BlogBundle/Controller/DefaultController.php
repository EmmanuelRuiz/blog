<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {        
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entries = $entry_repo->findAll();
        
        foreach ($entries as $entry)
        {
            echo $entry->getTitle()."<br>";
            echo $entry->getCategory()->getName()."<br>";
            echo $entry->getCategory()->getDescription()."<br>";
            echo "Creado por: ".$entry->getUser()->getName()."<br><hr>";
            $tags = $entry->getEntryTag();
            foreach($tags as $tag){
                echo $tag->getTag()->getName(). ", ";
            }
            echo "<hr>";
        }
        die();
        return $this->render('BlogBundle:Default:index.html.twig');
    }
}
