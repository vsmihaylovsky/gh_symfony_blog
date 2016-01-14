<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/13/16
 * Time: 12:34 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    /**
     * @param $slug
     * @return Article
     */
    public function findArticleBySlug($slug)
    {
        return $this->createQueryBuilder('a')
            ->select('a, author, t, c')
            ->leftJoin('a.author', 'author')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'c')
            ->orderBy('c.createdAt', 'DESC')
            ->where("a.slug = '$slug'")
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return Article[] array
     */
    public function findAllArticles()
    {
        return $this->getFindAllArticlesQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $slug
     * @return Article[] array
     */
    public function findAllAuthorArticles($slug)
    {
        return $this->getFindAllArticlesQuery()
            ->where("author.slug = '$slug'")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $slug
     * @return Article[] array
     */
    public function findAllTagArticles($slug)
    {
        return $this->getFindAllArticlesQuery()
            ->leftJoin('a.tags', 't1')
            ->where("t1.slug = '$slug'")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $search_string
     * @return Article[] array
     */
    public function findSearchedArticles($search_string)
    {
        return $this->getFindAllArticlesQuery()
            ->where("a.header LIKE '%$search_string%'")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $articles_count
     * @return array
     */
    public function findMostPopularArticles($articles_count)
    {
        return $this->createQueryBuilder('a')
            ->select('a.header, a.slug, avg(c.rating) as comments_rating')
            ->join('a.comments', 'c')
            ->groupBy('a.header, a.slug')
            ->orderBy('comments_rating', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($articles_count)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getFindAllArticlesQuery()
    {
        return $this->createQueryBuilder('a')
            ->select('a, author, t, count(c.id) as comments_count, avg(c.rating) as comments_rating')
            ->join('a.author', 'author')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'c')
            ->groupBy('a, t')
            ->orderBy('a.createdAt', 'DESC');
    }
}