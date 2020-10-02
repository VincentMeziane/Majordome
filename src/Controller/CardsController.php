<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Form\CardType;
use App\Entity\Notification;
use App\Repository\CardRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardsController extends AbstractController
{
    private $notif;

    public function __construct(Security $security, NotificationRepository $notificationRepository)
    {
        $notifications = $notificationRepository->findBy([
            'subscriber' => $security->getUser()
        ]);
        
        $this->notif = 0;
        foreach ($notifications as $value) {
            $this->notif = $this->notif + $value->getUnseen();
        }
    }

    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(CardRepository $cardRepository): Response
    {
        $cards = $cardRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('cards/index.html.twig', [
            'user' => $this->getUser(),
            'cards' => $cards,
            'author' => 'true',
            'notif' => $this->notif
        ]);
    }

    /**
     * @Route("/card/{id<[0-9]+>}", name="app_card_show", methods="GET")
     */
    public function show(Card $card): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $reader = $this->getUser()->getId();
        $author = $card->getUser()->getId();
        if ($reader === $author) {
            return $this->render('cards/show.html.twig', [
                'user' => $this->getUser(),
                'card' => $card,
                'author' => 'true',
                'notif' => $this->notif
            ]);
        } else {
            return $this->render('cards/show.html.twig', [
                'user' => $this->getUser(),
                'card' => $card,
                'author' => 'false',
                'notif' => $this->notif
            ]);
        }
    }

    /**
     * @Route("/card/create", name="app_card_create" , methods="GET|POST")
     */
    public function create(NotificationRepository $notificationRepository, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if(!$this->getUser()->isVerified())
                {
                    $this->addFlash('error', "Veuillez confirmer votre email avant d'accéder à cette page");
                    return $this->redirectToRoute('app_home');
                }
        $card = new Card;
        $form = $this->createForm(CardType::class, $card);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card->setUser($this->getUser());
            $notification = $notificationRepository->findBy([
                'author' => $this->getUser()
            ]);
            foreach ($notification as $value) {
                if($value->getUnseen() === null)
                {
                    $value->setUnseen(1);
                }
                else{
                    $notifs = $value->getUnseen();
                    $notifs++;
                    $value->setUnseen($notifs);
                }
            }
            $em->persist($card);
            $em->flush();

            $this->addFlash('success', 'Carte créée');
            // Incrémenter ou mettre à 1 la valeur de notification pour toutes les notifs ou je suis l'auteur


            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('cards/create.html.twig', [
                'user' => $this->getUser(),
                'formulaire' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/card/edit/{id<[0-9]+>}", name="app_card_edit" , methods="GET|PUT")
     */
    public function edit(Card $card, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->getUser()->getId() === $card->getUser()->getID()) {
            $form = $form = $this->createForm(CardType::class, $card, [
                'method' => 'PUT'
            ]);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

                $this->addFlash('success', 'Carte modifiée');
                return $this->redirectToRoute('app_home');
            } else if ($form->isSubmitted()) {
                $this->addFlash('error', 'La modification n\'a pas pu être effectuée');
            }

            return $this->render('cards/edit.html.twig', [
                'user' => $this->getUser(),
                'formulaire' => $form->createView(),
                'card' => $card,
                'notif' => $this->notif
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    /**
     * @Route("/card/delete/{id<[0-9]+>}", name="app_card_delete" , methods="DELETE")
     */
    public function delete(Request $request, Card $card, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->getUser()->getId() === $card->getUser()->getID()) {
            $token = $request->request->get('csrf_token');
            dd($this->getUser()->getId() . '' . $card->getUser()->getID());
            if ($this->isCsrfTokenValid('card_deletion_' . $card->getId(), $token)) {
                $em->remove($card);
                $em->flush();
                $this->addFlash('info', 'Carte supprimée');
            }
            return $this->redirectToRoute('app_home');
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
