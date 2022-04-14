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

    public function findByWithFilter($filtre): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        if (!empty($filtre['campus'])) {
            $queryBuilder
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $filtre['campus']);
        }
        if (!empty($filtre['nomSortie'])) {
            $queryBuilder
                ->andWhere('s.nom LIKE :nomSortie')
                ->setParameter('nomSortie', '%' . $filtre['nomSortie'] . '%');
        }
        if (!empty($filtre['dateSortieDebut'])) {
            $queryBuilder
                ->andWhere('s.dateHeureDebut LIKE :dateSortieDebut')
                ->setParameter('dateSortieDebut', $filtre['dateSortieDebut']);
        }
        if (!empty($filtre['$dateSortieFin'])) {
            $queryBuilder
                ->andWhere('s.dateHeureDebut LIKE :dateSortieDebut')
                ->setParameter('dateSortieDebut', $filtre['$dateSortieFin']);
        }
        if (!empty($filtre['organisateur'])) {
            $queryBuilder
                ->andWhere('s.organisateur LIKE :organisateur')
                ->setParameter('organisateur', $filtre['organisateur']);
        }
        if (!empty($filtre['inscrit'])) {
            $queryBuilder
                ->andWhere('s.organisateur LIKE :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
        if (!empty($filtre['pasInscrit'])) {
            $queryBuilder
                ->andWhere('s.organisateur LIKE :organisateur')
                ->setParameter('organisateur', $filtre['pasInscrit']);
        }
        if (!empty($filtre['etat'])) {
            $queryBuilder
                ->andWhere('s.organisateur LIKE :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
        $queryBuilder
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->setMaxResults(100);

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