<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use App\Repository\CardRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Vous êtes déjà connecté.');
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['user' => $this->getUser(), 'last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/account/{id<[0-9]+>}", name="app_account")
     */
    public function showAccount(User $user,EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request, CardRepository $cardRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupère l'id dans la session
        $userCards = $cardRepository->findBy([
            'user' => $user
        ]);
        
        if ($this->getUser()->getId() == $user->getId()) {
            $form = $this->createForm(UserType::class);
            if($request->isMethod('POST')){
                $plainPassword = $request->request->get('user')['plainPassword'];
                $verify = $passwordEncoder->isPasswordValid($user, $plainPassword);

                if(!$verify)
                {
                $error = "Le mot de passe n'est pas valide";
                return $this->render('security/account.html.twig', [
                    'user' => $this->getUser(),
                    'thisuser' => $user,
                    'cards' => $userCards,
                    'author' => 'true',
                    'form' => $form->createView(),
                    'error' => $error
                ]);
                }
            else{
                // mettre les infos dans user
                $user->setFirstName($request->request->get('user')['firstName']);
                $user->setLastName($request->request->get('user')['lastName']);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Vos modifications ont été prises en compte');      
            }
            
            }
            return $this->render('security/account.html.twig', [
                'user' => $this->getUser(),
                'thisuser' => $user,
                'cards' => $userCards,
                'form' => $form->createView(),
                'author' => 'true'
            ]);
        }
        else{
            return $this->render('security/account.html.twig', [
                'user' => $this->getUser(),
                'thisuser' => $user,
                'cards' => $userCards,
                'author' => 'false',
            ]);
        }
        // Affiche une vue et lui passe user
    }
}
