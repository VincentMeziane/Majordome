<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/account/{id<[0-9]+>}", name="app_account")
     */
    public function showAccount(User $user, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request, CardRepository $cardRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupère l'id dans la session
        $userCards = $cardRepository->findBy([
            'user' => $user
        ]);
        $subscriptions = $this->getUser()->getSubscription();
        $hasSubscribed = 'false';
        foreach ($subscriptions as $value) {
            if ($user->getId() == $value->getID()) {
                $hasSubscribed = 'true';
            }
        }

        // C'est votre compte
        if ($this->getUser()->getId() == $user->getId()) {
            $form = $this->createForm(UserType::class);
            // Le formulaire a été soumis et on est revenu sur la page
            if ($request->isMethod('POST')) {
                $plainPassword = $request->request->get('user')['plainPassword'];
                $verify = $passwordEncoder->isPasswordValid($user, $plainPassword);
                // Formulaire incorrect
                if (!$verify) {
                    $error = "Le mot de passe n'est pas valide";
                    return $this->render('security/account.html.twig', [
                        'user' => $this->getUser(),
                        'thisuser' => $user,
                        'hasSubscribed' => $hasSubscribed,
                        'subscriptions' => $subscriptions,
                        'cards' => $userCards,
                        'author' => 'true',
                        'form' => $form->createView(),
                        'error' => $error
                    ]);
                }
                // Formulaire correct
                else {
                    $user->setFirstName($request->request->get('user')['firstName']);
                    $user->setLastName($request->request->get('user')['lastName']);
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Vos modifications ont été prises en compte');
                }
            }
            // On vient d'arriver sur la page
            return $this->render('security/account.html.twig', [
                'user' => $this->getUser(),
                'thisuser' => $user,
                'hasSubscribed' => $hasSubscribed,
                'subscriptions' => $subscriptions,
                'cards' => $userCards,
                'form' => $form->createView(),
                'author' => 'true'
            ]);
        }
        // Ce n'est pas votre compte
        else {
            return $this->render('security/account.html.twig', [
                'user' => $this->getUser(),
                'thisuser' => $user,
                'hasSubscribed' => $hasSubscribed,
                'subscriptions' => $subscriptions,
                'cards' => $userCards,
                'author' => 'false',
            ]);
        }
    }

    /**
     * @Route("/subscribe/{id<[0-9]+>}", name="app_subscribe")
     */
    public function subscribe(User $user, EntityManagerInterface $em)
    {
        $me = $this->getUser();
        $me->addSubscription($user);
        $em->persist($me);
        $em->flush();
        return $this->redirectToRoute('app_account', ['id' => $user->getId()]);
    }
}
