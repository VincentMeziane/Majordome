<?php

namespace App\Controller;

use App\Entity\Card;
use App\Repository\CardRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardsController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(CardRepository $cardRepository ): Response
    {
        $cards = $cardRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('cards/index.html.twig', compact('cards'));
    }

    /**
     * @Route("/card/{id<[0-9]+>}", name="app_cards_show")
     */
    public function show(Card $card): Response
    {
        return $this->render('cards/show.html.twig', compact('card'));
    }
}
