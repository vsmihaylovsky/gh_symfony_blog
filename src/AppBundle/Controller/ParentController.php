<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/17/16
 * Time: 2:29 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParentController extends Controller
{
    protected $articlesShowAtATime = 4;

    /**
     * @param $articlesCount
     * @param $currentPage
     * @return mixed
     */
    protected function getNextPageNumber($articlesCount, $currentPage)
    {
        if ($articlesCount > ($this->articlesShowAtATime * $currentPage)) {
            $nextPage = $currentPage + 1;
        } else {
            $nextPage = false;
        }

        return $nextPage;
    }

}