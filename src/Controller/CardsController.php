<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardsController extends AbstractController
{

    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(CardRepository $cardRepository): Response
    {
        $cards = $cardRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('cards/index.html.twig', [
            'user' => $this->getUser(),
            'cards' => $cards,
            'author' => 'true'
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
                'author' => 'true'
            ]);
        } else {
            return $this->render('cards/show.html.twig', [
                'user' => $this->getUser(),
                'card' => $card,
                'author' => 'false'
            ]);
        }
    }

    /**
     * @Route("/card/create", name="app_card_create" , methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $card = new Card;
        $form = $this->createForm(CardType::class, $card);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card->setUser($this->getUser());
            $em->persist($card);
            $em->flush();

            $this->addFlash('success', 'Carte créée');

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
                'card' => $card
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
