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

    public function indexAction($page) {
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");

        $category_repo = $em->getRepository("BlogBundle:Category");

        $categories = $category_repo->findAll();
        $pageSize = 5;
        $entries = $entry_repo->getPaginateEntries($pageSize, $page);

        /* mostrar links */
        $totalItems = count($entries);
        //redondear el resultado de totalitems entre pagesize
        $pagesCount = ceil($totalItems / $pageSize);


        return $this->render("BlogBundle:Entry:index.html.twig", array(
                    "entries" => $entries,
                    "categories" => $categories,
                    "totalItems" => $totalItems,
                    "pagesCount" => $pagesCount,
            "page" => $page,
            "page_max" => $page
        ));
    }

    public function addAction(Request $request) {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        /* recoger lo que llega del form */
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                //entry_repo es para poder mandar a llamar a entryRepository
                $entry_repo = $em->getRepository("BlogBundle:Entry");
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
                $file_name = time() . "." . $ext;
                $file->move("uploads", $file_name);


                $entry->setImage($file_name);

                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);

                $user = $this->getUser();
                $entry->setUser($user);
                $em->persist($entry);
                $flush = $em->flush();

                //llamar entryRepository
                //le pasamos lo que acabamos de guardar del formulario
                //(las tags, el titulo, el usuario, la categoria)
                $entry_repo->saveEntryTags(
                        $form->get("tags")->getData(), $form->get("title")->getData(), $category, $user
                );

                if ($flush == null) {
                    $status = "la entrada se ha creado correctamente";
                } else {
                    $status = "error al añadir la entrada";
                }
            } else {
                $status = "la entrada no se ha creado, el form no es valido";
            }
            /* crear flag */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_homepage");
        }
        return $this->render("BlogBundle:Entry:add.html.twig", array(
                    "form" => $form->createView()
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        /* para poder eliminar entradas que tengan tags asociadas */
        $entry_tag_repo = $em->getRepository("BlogBundle:EntryTag");
        //hacemos find a entrytag
        $entry = $entry_repo->find($id);


        $entry_tags = $entry_tag_repo->findBy(array("entry" => $entry));
        //recorremos el resultado
        foreach ($entry_tags as $et) {
            if (is_object($et)) {
                $em->remove($et);
                $em->flush();
            }
        }
        if (is_object($entry)) {
            $em->remove($entry);
            $em->flush();
        }

        return $this->redirectToRoute("blog_homepage");
    }

    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $category_repo = $em->getRepository("BlogBundle:Category");

        $entry = $entry_repo->find($id);
        /* bucle para sacar las etiqetas en el form de editar */

        $tags = "";
        foreach ($entry->getEntryTag() as $entryTag) {
            $tags .= $entryTag->getTag()->getName() . ",";
        }

        /* generar el form */
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /* set a la entrada */
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time() . "." . $ext;
                $file->move("uploads", $file_name);

                $entry->setImage($file_name);
                /* obtener y setear categoria */
                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);

                $user = $this->getUser();
                $entry->setUser($user);

                $em->persist($entry);
                $flush = $em->flush();

                /* borrar etiquetas cuando se edita la entrada */
                $entry_tag_repo = $em->getRepository("BlogBundle:EntryTag");
                $entry_tags = $entry_tag_repo->findBy(array("entry" => $entry));
                foreach ($entry_tags as $et) {
                    if (is_object($et)) {
                        $em->remove($et);
                        $em->flush();
                    }
                }
                /* fin */

                $entry_repo->saveEntryTags(
                        $form->get("tags")->getData(), $form->get("title")->getData(), $category, $user
                );

                if ($flush == null) {
                    $status = "La entrada se ha editado correctamente";
                } else {
                    $status = "No se ha podido editar la entrada";
                }
            } else {
                $status = "El formulario no es valido";
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_homepage");
        }
        return $this->render("BlogBundle:Entry:edit.html.twig", array(
                    "form" => $form->createView(),
                    "entry" => $entry,
                    "tags" => $tags
        ));
    }

}
