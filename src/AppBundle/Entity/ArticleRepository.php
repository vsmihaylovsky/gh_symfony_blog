<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/13/16
 * Time: 12:34 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ArticleRepository extends EntityRepository
{
    /**
     * @param $slug
     * @return array
     */
    public function findArticleBySlug($slug)
    {
        return $this->createQueryBuilder('a')
            ->select('a, user, t, c, avg(c1.rating) as comments_rating')
            ->leftJoin('a.user', 'user')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('a.comments', 'c1')
            ->groupBy('a, t, c')
            ->orderBy('c.createdAt', 'DESC')
            ->where("a.slug = '$slug'")
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $articlesShowAtATime
     * @param $currentPage
     * @return Article[] array
     */
    public function findAllArticles($currentPage, $articlesShowAtATime)
    {
        $query = $this->getFindAllArticlesQuery()
            ->getQuery()
            ->setFirstResult($articlesShowAtATime * ($currentPage - 1))
            ->setMaxResults($articlesShowAtATime);

        return new Paginator($query);
    }

    public function findAllArticlesCount()
    {
        return $this->getFindAllArticlesCountQuery()
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $slug
     * @param $currentPage
     * @param $articlesShowAtATime
     * @return Article[] array
     */
    public function findAllUserArticles($slug, $currentPage, $articlesShowAtATime)
    {
        $query = $this->getFindAllArticlesQuery()
            ->where("user.slug = '$slug'")
            ->getQuery()
            ->setFirstResult($articlesShowAtATime * ($currentPage - 1))
            ->setMaxResults($articlesShowAtATime);

        return new Paginator($query);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findAllUserArticlesCount($slug)
    {
        return $this->getFindAllArticlesCountQuery()
            ->join('a.user', 'user')
            ->where("user.slug = '$slug'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $slug
     * @param $currentPage
     * @param $articlesShowAtATime
     * @return Article[] array
     */
    public function findAllTagArticles($slug, $currentPage, $articlesShowAtATime)
    {
        $query = $this->getFindAllArticlesQuery()
            ->join('a.tags', 't1')
            ->where("t1.slug = '$slug'")
            ->getQuery()
            ->setFirstResult($articlesShowAtATime * ($currentPage - 1))
            ->setMaxResults($articlesShowAtATime);

        return new Paginator($query);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function findAllTagArticlesCount($slug)
    {
        return $this->getFindAllArticlesCountQuery()
            ->join('a.tags', 't')
            ->where("t.slug = '$slug'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $search_string
     * @param $currentPage
     * @param $articlesShowAtATime
     * @return Article[] array
     */
    public function findSearchedArticles($search_string, $currentPage, $articlesShowAtATime)
    {
        $query = $this->getFindAllArticlesQuery()
            ->where("a.header LIKE '%$search_string%'")
            ->getQuery()
            ->setFirstResult($currentPage)
            ->setMaxResults($articlesShowAtATime);

        return new Paginator($query);
    }

    /**
     * @param $search_string
     * @return mixed
     */
    public function findSearchedArticlesCount($search_string)
    {
        return $this->getFindAllArticlesCountQuery()
            ->where("a.header LIKE '%$search_string%'")
            ->getQuery()
            ->getSingleScalarResult();
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
            ->select('a, user, t, count(c.id) as comments_count, avg(c.rating) as comments_rating')
            ->join('a.user', 'user')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'c')
            ->groupBy('a, t')
            ->orderBy('a.createdAt', 'DESC');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getFindAllArticlesCountQuery()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)');
    }
}