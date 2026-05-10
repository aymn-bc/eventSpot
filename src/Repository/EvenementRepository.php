<?php

namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\TagEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findProchains(int $limit): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateDebut >= :now')
            ->andWhere('e.status = :statut')
            ->setParameter('now', new \DateTime())
            ->setParameter('statut', 'publie')
            ->orderBy('e.dateDebut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findEventById($id){
        return $this->createQueryBuilder('a')
                    ->andWhere('a.id = :val')
                    ->setParameter('val', $id)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();
    }

    public function findFiveById(array $eventsId): array {
        if (empty($eventsId)){
            return [];
        }
        return $this->createQueryBuilder('a')
                    ->andWhere('a.id in (:ids)')
                    ->setParameter('ids', $eventsId)
                    ->setMaxResults(min( count($eventsId), 5 ))
                    ->getQuery()
                    ->getResult();
    }

    // EvenementRepository
    public function findByFilters(?string $titre, ?string $categorie, ?string $ville, ?TagEvenement $tag): array
    {
        $qb = $this->createQueryBuilder('e');
    
        if ($titre) {
            $qb->andWhere('e.titre LIKE :titre')
               ->setParameter('titre', '%' . $titre . '%');
        }
        if ($categorie) {
            $qb->andWhere('e.categorie = :cat')
               ->setParameter('cat', $categorie);
        }
        if ($ville) {
            $qb->innerJoin('e.lieu', 'l')
               ->andWhere('l.ville LIKE :ville')
               ->setParameter('ville', '%' . $ville . '%');
        }
        if ($tag) {
            $qb->innerJoin('e.tags', 't')
               ->andWhere('t = :tag')
               ->setParameter('tag', $tag);
        }
    
        return $qb->orderBy('e.dateDebut', 'ASC')
                  ->getQuery()->getResult();
    }
    
    /**
     * Find upcoming published events ordered by start date
     * 
     * @param int $limit Maximum number of events to return
     * @return array Array of upcoming Evenement objects
     */
    public function findUpcoming(int $limit = 6): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.dateDebut >= :now')
            ->andWhere('e.status = :statut')
            ->setParameter('now', new \DateTime())
            ->setParameter('statut', 'publie')
            ->orderBy('e.dateDebut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
