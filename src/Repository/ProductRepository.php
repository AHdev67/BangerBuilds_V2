<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\DBAL\Connection;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private $connection;
    private $cache;

    public function __construct(ManagerRegistry $registry, Connection $connection, CacheInterface $cache)
    {
        parent::__construct($registry, Product::class);
        $this->connection = $connection;
        $this->cache = $cache;
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
     * @param Category $category
     * @return Product[] Returns an array of Product objects
     */
    public function findByCategory($category)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
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

    public function findCpusByManufacturer(string $manufacturer)
    {
        return $this->createQueryBuilder('p')
            ->where('p.label LIKE :label')
            ->setParameter('label', $manufacturer . '%')
            ->getQuery()
            ->getResult();
    }

    public function findCpusByGeneration(string $manufacturer, string $gen)
    {
        return $this->createQueryBuilder('p')
            ->where('p.label LIKE :label')
            ->andWhere('p.label LIKE :pattern')
            ->setParameter('label', $manufacturer . '%')
            ->setParameter('pattern', '%' . $gen . '%')
            ->getQuery()
            ->getResult();
    }

    public function findProductsBySearchQuery(string $query)
    {
        $cacheKey = 'search_' . md5($query);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($query) {
            $item->expiresAfter(3600); // Cache for 1 hour

            return $this->createQueryBuilder('p')
                ->where('p.label LIKE :pattern')
                ->setParameter('pattern', '%' . $query . '%')
                ->getQuery()
                ->getResult();
        });
    }

    public function findByCategoryOrderedByRating(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')  // Join with the reviews table
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->groupBy('p.id')  // Group by product ID
            ->orderBy('AVG(r.rating)', 'DESC')  // Order by average rating (use ASC for ascending)
            ->getQuery()
            ->getResult();
    }

    public function findTop5Products(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, AVG(r.rating) as avgScore')
            ->leftJoin('p.reviews', 'r')
            ->groupBy('p.id')
            ->orderBy('avgScore', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
