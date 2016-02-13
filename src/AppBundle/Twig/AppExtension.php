<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 5:43 PM
 */

namespace AppBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Twig_Extension;
use Twig_SimpleFunction;

class AppExtension extends Twig_Extension
{
    protected $doctrine;
    private $mostPopularArticlesCount;
    private $latestCommentsCount;

    public function __construct(Registry $doctrine, $mostPopularArticlesCount, $latestCommentsCount)
    {
        $this->doctrine = $doctrine;
        $this->mostPopularArticlesCount = $mostPopularArticlesCount;
        $this->latestCommentsCount = $latestCommentsCount;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('tagsCloud', [$this, 'getTagsCloud']),
            new Twig_SimpleFunction('mostPopularArticles', [$this, 'getMostPopularArticles']),
            new Twig_SimpleFunction('latestComments', [$this, 'getLatestComments']),
        ];
    }

    /**
     * @return array
     */
    public function getTagsCloud()
    {
        $repository = $this->doctrine->getRepository('AppBundle:Tag');
        $tagsCloud = $repository->getTagCloud();

        if (!$tagsCloud) {
            return null;
        }

        $tag_weights = array_map(function ($tag) {
            return $tag['articles_count'];
        }, $tagsCloud);
        $t_min = min($tag_weights);
        $t_max = max($tag_weights);
        $f_max = 2;
        foreach ($tagsCloud as &$tag) {
            $tag['tag_weight'] = 65 * (1 + (($f_max * ($tag['articles_count'] - $t_min)) / ($t_max - $t_min)));
        }

        return $tagsCloud;
    }

    /**
     * @return array
     */
    public function getMostPopularArticles()
    {
        $repository = $this->doctrine->getRepository('AppBundle:Article');
        return $repository->findMostPopularArticles($this->mostPopularArticlesCount);
    }

    /**
     * @return array
     */
    public function getLatestComments()
    {
        $repository = $this->doctrine->getRepository('AppBundle:Comment');
        return $repository->findLatestComments($this->latestCommentsCount);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}