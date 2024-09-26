<?php
namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function findPaginatedMoviesWithCategoriesAndActors(int $page, int $limit = 20)
    {
        $query = $this->createQueryBuilder('m')
            ->leftJoin('m.categories', 'c')  
            ->addSelect('c')                
            ->leftJoin('m.actors', 'a')     
            ->addSelect('a')                 
            ->orderBy('m.title', 'ASC')      
            ->getQuery();

        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) 
            ->setMaxResults($limit);               

        return $paginator;
    }
}
