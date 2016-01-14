<?php

namespace AppBundle\Controller\Blog;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Author;
use AppBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template("AppBundle::articleList.html.twig")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAllArticles();

        return ['articles' => $articles, 'side_bar_content' => $this->getSideBarContent()];
    }

    /**
     * @Route("/{slug}", name="show_article")
     * @Method("GET")
     * @Template("AppBundle::article.html.twig")
     */
    public function showArticleAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findArticleBySlug($slug);

        return ['article' => $article, 'side_bar_content' => $this->getSideBarContent()];
    }

    /**
     * @Route("/author/{slug}", name="show_author_articles")
     * @Method("GET")
     * @Template("AppBundle::articleList.html.twig")
     */
    public function showAuthorArticlesAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAllAuthorArticles($slug);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Author');
        /** @var  Author $author */
        $author = $repository->findOneBy(['slug' => $slug]);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 1, 'text' => $author->getName()],
            'side_bar_content' => $this->getSideBarContent()
        ];
    }

    /**
     * @Route("/tag/{slug}", name="show_tag_articles")
     * @Method("GET")
     * @Template("AppBundle::articleList.html.twig")
     */
    public function showTagArticlesAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAllTagArticles($slug);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');
        /** @var  Tag $tag */
        $tag = $repository->findOneBy(['slug' => $slug]);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 2, 'text' => $tag->getName()],
            'side_bar_content' => $this->getSideBarContent()
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/search/", name="search_articles")
     * @Method("GET")
     * @Template("AppBundle::articleList.html.twig")
     */
    public function showSearchArticlesAction(Request $request)
    {
        $search_string = $request->query->get('q');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findSearchedArticles($search_string);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 3, 'text' => $search_string],
            'side_bar_content' => $this->getSideBarContent()
        ];
    }

    /**
     * @return array
     */
    private function getSideBarContent()
    {
        $sideBarContent = [];

        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $sideBarContent['tag_cloud'] = $repository->getTagCloud();

        $tag_weights = array_map(function ($tag) {
            return $tag['articles_count'];
        }, $sideBarContent['tag_cloud']);
        $t_min = min($tag_weights);
        $t_max = max($tag_weights);
        $f_max = 2;
        foreach ($sideBarContent['tag_cloud'] as &$tag) {
            $tag['tag_weight'] = 60 * (1 + (($f_max * ($tag['articles_count'] - $t_min)) / ($t_max - $t_min)));
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $sideBarContent['most_popular_articles'] = $repository->findMostPopularArticles(5);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Comment');
        $sideBarContent['latest_comments'] = $repository->findLatestComments(5);

        return $sideBarContent;
    }
}