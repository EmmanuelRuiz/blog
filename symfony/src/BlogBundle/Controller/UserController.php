<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\User;
use BlogBundle\Form\UserType;

class UserController extends Controller
{
    //login
    public function loginAction(Request $request){
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isValid()){
            $user = new User();
            $user->setName($form->get("name")->getData());
            $user->setSurname($form->get("surname")->getData());
            $user->setEmail($form->get("email")->getData());
            $user->setPassword($form->get("password")->getData());
            $user->setRole("ROLE_USER");
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $flush = $em->flush();
            
            if($flush == null){
                $status = "Usuario creado correctamente";    
            } else {
                $status = "No se pudo crear el usuario";
            }
             
        } else {
            $stauts = "No se pudo registrar el usuario";
        }
        
        
        return $this->render("BlogBundle:user:login.html.twig", array(
            "error" => $error,
            "last_username" => $lastUsername,
            "form" => $form->createView()
        ));
    }
}
