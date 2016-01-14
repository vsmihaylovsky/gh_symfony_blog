<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/13/16
 * Time: 12:34 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getTagCloud()
    {
        return $this->createQueryBuilder('t')
            ->select('t, sum(a.id) as articles_count')
            ->join('t.articles', 'a')
            ->groupBy('t')
            ->orderBy('t.name')
            ->getQuery()
            ->getResult();
    }
}