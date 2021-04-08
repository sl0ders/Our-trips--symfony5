<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method findByCity()
 */
class PictureRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Picture::class);
        $this->paginator = $paginator;
    }

    /**
     * @param $country
     * @return mixed
     */
    public function findByCountry($country): mixed
    {
        $qb = $this->createQueryBuilder("p")
            ->leftJoin("p.city", "city")->addSelect("city")
            ->leftJoin("city.country", "country")->addSelect("country")
            ->andWhere("country = :country")
            ->setParameter(":country", $country);
        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère les produits en lien avec une recherche
     * @param SearchData $search
     * @return mixed
     */
    public function findSearch(SearchData $search): mixed
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('city', 'p', 'country')
            ->leftJoin('p.city', 'city')
            ->leftJoin("city.country", "country");

        if (!empty($search->city)) {
            $query = $query
                ->andWhere('city.id IN (:city)')
                ->setParameter('city', $search->city);
        }
        if (!empty($search->country)) {
            $query = $query
                ->andWhere('country.id IN (:country)')
                ->setParameter('country', $search->country);
        }
        return $query->getQuery()->getResult();
    }
}
