<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Notification;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $notif;

    public function __construct(Security $security, NotificationRepository $notificationRepository)
    {
        $notifications = $notificationRepository->findBy([
            'subscriber' => $security->getUser()
        ]);
        
        $this->notif = 0;
        foreach ($notifications as $key => $value) {
            $this->notif = $this->notif + $value->getUnseen();
        }
    }
    /**
     * @Route("/account/{id<[0-9]+>}", name="app_account")
     */
    public function showAccount(NotificationRepository $notificationRepository, User $user, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request, CardRepository $cardRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Récupère l'id dans la session
        $userCards = $cardRepository->findBy([
            'user' => $user
        ]);
        $notifications = $notificationRepository->findBy([
            'subscriber' => $this->getUser()
        ]);
               

        $hasSubscribed = 'false';
        foreach ($notifications as $value) {
            if ($user->getId() == $value->getAuthor()->getId()) {
                $hasSubscribed = 'true';
            }
        }

        // C'est votre compte
        if ($this->getUser()->getId() == $user->getId()) {
            
            if (!$this->getUser()->isVerified()) {
                $this->addFlash('error', "Veuillez confirmer votre email avant d'accéder à votre compte");
                return $this->render('security/reConfirm.html.twig');
            }
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
                        'subscriptions' => $notifications,
                        'cards' => $userCards,
                        'author' => 'true',
                        'form' => $form->createView(),
                        'error' => $error,
                        'notif' => $this->notif
                    ]);
                }
                // Formulaire correct
                else {
                    $delete = $request->request->get('user')['deleteAccount'] ?? '';
                    if ($delete == 1) {
                        $em->remove($user);
                        $session = new Session();
                        $session->invalidate();
                        $em->flush();
                        return $this->redirectToRoute('app_logout');
                    } else {
                        $user->setFirstName($request->request->get('user')['firstName']);
                        $user->setLastName($request->request->get('user')['lastName']);
                        $em->persist($user);
                        $em->flush();
                        $this->addFlash('success', 'Vos modifications ont été prises en compte');
                    }
                }
            }
            // On vient d'arriver sur la page
            return $this->render('security/account.html.twig', [
                'user' => $this->getUser(),
                'thisuser' => $user,
                'hasSubscribed' => $hasSubscribed,
                'subscriptions' => $notifications,
                'cards' => $userCards,
                'form' => $form->createView(),
                'author' => 'true',
                'notif' => $this->notif
            ]);
        }
        // Ce n'est pas votre compte
        else {
            // Remettre à 0 unseen dans la bdd
            $notification = $notificationRepository->findOneBy([
                'author' => $user,
                'subscriber' => $this->getUSer()
            ]);
            $notification->setUnseen(0);
            $em->persist($notification);
            $em->flush();

            return $this->render('security/account.html.twig', [
                'user' => $this->getUser(),
                'thisuser' => $user,
                'hasSubscribed' => $hasSubscribed,
                'subscriptions' => $notifications,
                'cards' => $userCards,
                'author' => 'false',
                'notif' => $this->notif
            ]);
        }
    }

    /**
     * @Route("/subscribe/{id<[0-9]+>}", name="app_subscribe")
     */
    public function subscribe(User $user, EntityManagerInterface $em)
    {
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('error', "Veuillez confirmer votre email pour pouvoir accéder à cette fonctionnalité");
            return $this->redirectToRoute('app_account', ['id' => $user->getId()]);
        }
        $notification = new Notification;
        $notification->setSubscriber($this->getUser());
        $notification->setAuthor($user);
        $em->persist($notification);
        $em->flush();
        $this->addFlash('success', "Vous êtes maintenant abonné à cet utilisateur");
        return $this->redirectToRoute('app_account', ['id' => $user->getId()]);
    }

    /**
     * @Route("/unsubscribe/{id<[0-9]+>}", name="app_unsubscribe")
     */
    public function unsubscribe(NotificationRepository $notificationRepository, User $user, EntityManagerInterface $em)
    {
        $notification = $notificationRepository->findBy([
            'subscriber' => $this->getUser(),
            'author' => $user
        ]);
        if (isset($notification[0])) {
            $em->remove($notification[0]);
            $em->flush();
        }
        $this->addFlash('success', "Vous n'êtes plus abonné à cet utilisateur");
        return $this->redirectToRoute('app_account', ['id' => $user->getId()]);
    }
}
