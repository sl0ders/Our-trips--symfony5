<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method findByCity()
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    public function findByCountry($country)
    {
        $qb = $this->createQueryBuilder("p")
            ->leftJoin("p.city", "city")->addSelect("city")
            ->leftJoin("city.country", "country")->addSelect("country")
            ->andWhere("country = :country")
            ->setParameter(":country", $country);
       return $qb->getQuery()->getResult();
    }
}
