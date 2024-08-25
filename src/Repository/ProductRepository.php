<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
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

    public function getBaseQueryBuilderForCategory(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->groupBy('p.id')
            ->orderBy('AVG(r.rating)', 'DESC');
    }

    public function findByFilters(Category $category, array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->groupBy('p.id');

        // Apply brand filter by searching in the label
        if (!empty($filters['filterByBrand'])) {
            $brandConditions = $qb->expr()->orX();

            foreach ($filters['filterByBrand'] as $brand) {
                $brandConditions->add(
                    $qb->expr()->like('p.label', $qb->expr()->literal('%' . $brand . '%'))
                );
            }

            $qb->andWhere($brandConditions);
        }

        // Apply generation filter by searching in the label
        if (!empty($filters['filterByGen'])) {
            $generationConditions = $qb->expr()->orX();

            foreach ($filters['filterByGen'] as $generation) {
                $generationConditions->add(
                    $qb->expr()->like('p.label', $qb->expr()->literal('%' . $generation . '%'))
                );
            }

            $qb->andWhere($generationConditions);
        }

        // Apply model filter by searching in the label
        if (!empty($filters['filterByModel'])) {
            $modelConditions = $qb->expr()->orX();

            foreach ($filters['filterByModel'] as $model) {
                $modelConditions->add(
                    $qb->expr()->like('p.label', $qb->expr()->literal('%' . $model . '%'))
                );
            }

            $qb->andWhere($modelConditions);
        }

        // Apply ordering
        if (!empty($filters['orderBy'])) {
            switch ($filters['orderBy']) {
                case 'score_DESC':
                    $qb->orderBy('AVG(r.rating)', 'DESC');
                    break;
                case 'price_ASC':
                    $qb->orderBy('p.price', 'ASC');
                    break;
                case 'price_DESC':
                    $qb->orderBy('p.price', 'DESC');
                    break;
                case 'brand_ASC':
                    $qb->orderBy('p.label', 'ASC');
                    break;
            }
        }

        return $qb;
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
            ->leftJoin('p.reviews', 'r')
            ->where('p.category = :category')
            ->setParameter('category', $category)
            ->groupBy('p.id')
            ->orderBy('AVG(r.rating)', 'DESC')
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
