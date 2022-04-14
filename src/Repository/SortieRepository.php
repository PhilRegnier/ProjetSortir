<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sortie $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Sortie $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByWithFilter(
        $campus,
        $nomSortie,
        $dateSortieDebut,
        $dateSortieFin,
        $organisateur,
        $inscrit,
        $pasInscrit,
        $sortiesPassees): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        if (!empty($campus)) {
            $queryBuilder
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }
        if (!empty($nomSortie)) {
            $queryBuilder
                ->andWhere('s.nom LIKE :nomSortie')
                ->setParameter('nomSortie', '%' . $nomSortie . '%');
        }
        if (!empty($dateSortieDebut)) {
//            $queryBuilder
//                ->andWhere('w.dateHeureDebut LIKE :dateSortieDebut')
//                ->setParameter('dateSortieDebut', $dateSortieDebut);
        }
        if (!empty($dateSortieFin)) {
//            $queryBuilder
//                ->andWhere('w.dateHeureDebut LIKE :dateSortieDebut')
//                ->setParameter('dateSortieDebut', $dateSortieDebut);
        }
        if (!empty($organisateur)) {
            $queryBuilder
                ->andWhere('w.organisateur LIKE :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
        if (!empty($inscrit)) {
//            $queryBuilder
//                ->andWhere('w.organisateur LIKE :organisateur')
//                ->setParameter('organisateur', $organisateur);
        }
        if (!empty($pasInscrit)) {
//            $queryBuilder
//                ->andWhere('w.organisateur LIKE :organisateur')
//                ->setParameter('organisateur', $organisateur);
        }
        if (!empty($sortiesPassees)) {
//            $queryBuilder
//                ->andWhere('w.organisateur LIKE :organisateur')
//                ->setParameter('organisateur', $organisateur);
        }
//        $queryBuilder
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(100);

            return $queryBuilder->getQuery()->getResult();
        }

        // /**
        //  * @return Sortie[] Returns an array of Sortie objects
        //  */
        /*
        public function findByExampleField($value)
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('s.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }
        */

        /*
        public function findOneBySomeField($value): ?Sortie
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
        */

    }