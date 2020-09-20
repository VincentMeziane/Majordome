<?php

namespace App\Controller;

use App\Repository\CardRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardsController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(CardRepository $cardRepository )
    {
        $cards = $cardRepository->findAll();
        return $this->render('cards/index.html.twig', compact('cards'));
    }
}
