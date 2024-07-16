<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private $connection;

    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Product::class);
        $this->connection = $connection;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByCategory($category)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.label LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findMotherboardsBySocket(string $socket)
    {
        $sql = 'SELECT * FROM product WHERE category_id = :categoryId AND JSON_UNQUOTE(JSON_EXTRACT(specs, "$.socket")) = :socket';
        $params = ['categoryId' => 3, 'socket' => $socket];
        return $this->connection->executeQuery($sql, $params)->fetchAllAssociative();
    }

    public function findAllIntelCpus()
    {
        return $this->createQueryBuilder('p')
            ->where('p.label LIKE :label')
            ->setParameter('label', 'Intel%')
            ->getQuery()
            ->getResult();
    }

    public function findIntelCpusByGeneration(string $gen)
    {
        return $this->createQueryBuilder('p')
            ->where('p.label LIKE :label')
            ->andWhere('p.label LIKE :pattern')
            ->setParameter('label', 'Intel%')
            ->setParameter('pattern', '%' . $gen . '%')
            ->getQuery()
            ->getResult();
    }
}
