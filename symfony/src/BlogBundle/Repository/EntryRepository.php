<?php

/* REPOSITORIO con metodos complejos */
/* Asignar las etiquetas a las entradas */
/* Primero indicamos el bundle al que pertenece
 * despues extendemos a entityrepository
 * 
 *  
 */

namespace BlogBundle\Repository;

use BlogBundle\Entity\Tag;
use BlogBundle\Entity\EntryTag;

class EntryRepository extends \Doctrine\ORM\EntityRepository {

    public function saveEntryTags($tags = null, $title = null, $category = null, $user = null, $entry = null) {
        $em= $this->getEntityManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        /* buscar en la bd cual es la entrada que tiene lo sigueinte */
        if ($entry == null) {
            $entry = $this->findOneBy(array(
                "title" => $title,
                "category" => $category,
                "user" => $user
            ));
        } else {
            
        }
        
        //al resultado de tags agregar una más
        //para que nos agregue correctamente todas las tags
        $tags .= ",";
        //partir la string de tags por comas        
        $tags = explode(",", $tags);
        foreach ($tags as $tag) {
            //buscar si la tag existe
            $isset_tag = $tag_repo->findOneBy(array("name" => $tag));
            //si no existe
            if (count($isset_tag) == 0) {
                $tag_obj = new Tag();
                $tag_obj->setName($tag);
                $tag_obj->setDescription($tag);
                //si la tag no esta vacia
                //trim elimina espacios en blanco
                if(!empty(trim($tag))){
                    $em->persist($tag_obj);
                    $em->flush();  
                }                
            }
            //volver a buscar
            $tag = $tag_repo->findOneBy(array("name" => $tag));

            //buscar entrytag
            $entryTag = new EntryTag();
            $entryTag->setEntry($entry);
            $entryTag->setTag($tag);
            $em->persist($entryTag);
        }
        $flush = $em->flush();
        return $flush;
    }

}
