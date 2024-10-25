<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;


class MovieController extends AbstractController
{
    #[Route('/movie', name: 'app_movie')]
    public function listMovies(MovieRepository $movieRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $movies = $movieRepository->findPaginatedMoviesWithCategoriesAndActors($page, 20);

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
            'currentPage' => $page,
            'totalPages' => ceil(count($movies) / 20), 
        ]);
    }
}
