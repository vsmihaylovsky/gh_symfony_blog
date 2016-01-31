<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 3:20 PM
 */

namespace AppBundle\Services;


class PaginationService
{
    private $articlesShowAtATime;

    /**
     * PaginationService constructor.
     * @param $articlesShowAtATime
     */
    public function __construct($articlesShowAtATime)
    {
        $this->articlesShowAtATime = $articlesShowAtATime;
    }

    /**
     * @param $articlesCount
     * @param $currentPage
     * @return mixed
     */
    public function getNextPageNumber($articlesCount, $currentPage)
    {
        if ($articlesCount > ($this->articlesShowAtATime * $currentPage)) {
            $nextPage = $currentPage + 1;
        } else {
            $nextPage = false;
        }

        return $nextPage;
    }
}