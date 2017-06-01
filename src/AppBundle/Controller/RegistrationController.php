<?php

namespace AppBundle\Controller;

use AdminBundle\Entity\Roles;
use AppBundle\Form\UsersType;
use AppBundle\Form\LoginType;
use AdminBundle\Entity\Users;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $em = $this->get('doctrine.orm.entity_manager');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setEnabled(true);
            // must fixed this
            $userRole = $em->getRepository(Roles::class)
                ->findOneBy([
                    'id' => 1
                ]);
            $user->setRole($userRole);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'Registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}