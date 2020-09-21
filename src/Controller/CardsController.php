<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
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
        return $this->render('cards/index.html.twig', compact('cards'));
    }

    /**
     * @Route("/card/{id<[0-9]+>}", name="app_card_show", methods="GET")
     */
    public function show(Card $card): Response
    {
        return $this->render('cards/show.html.twig', compact('card'));
    }

    /**
     * @Route("/card/create", name="app_card_create" , methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $card = new Card;
        $form = $this->createForm(CardType::class, $card);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($card);
            $em->flush();
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('cards/create.html.twig', ['formulaire' => $form->createView()]);
        }
    }

    /**
     * @Route("/card/edit/{id<[0-9]+>}", name="app_card_edit" , methods="GET|PUT")
     */
    public function edit(Card $card, Request $request, EntityManagerInterface $em): Response
    {
        $form = $form = $this->createForm(CardType::class, $card, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->render('cards/edit.html.twig', [
            'formulaire' => $form->createView(),
            'card' => $card
        ]);
    }

    /**
     * @Route("/card/delete/{id<[0-9]+>}", name="app_card_delete" , methods="DELETE")
     */
    public function delete(Request $request, Card $card, EntityManagerInterface $em): Response
    {
        $token = $request->request->get('csrf_token');
        if ($this->isCsrfTokenValid('card_deletion_' . $card->getId(), $token)) {
            $em->remove($card);
            $em->flush();
        }
        return $this->redirectToRoute('app_home');
    }
}
