<?php

namespace AppBundle\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Route("/", name="homepage")
     * @Method("GET")
     * @Template("AppBundle:Blog:articlesList.html.twig")
     */
    public function indexAction(Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articlesCount = $repository->findAllArticlesCount();

        $nextPage = $this->get('app.pagination_service')->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findAllArticles($currentPage, $this->getParameter('articles_show_at_a_time'));

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('homepage', ['page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        $delete_forms = $this->get('app.delete_form_service')->getArticlesDeleteForms($articles);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                [
                    'articles' => $articles,
                    'nextPageUrl' => $nextPageUrl,
                    'delete_forms' => $delete_forms
                ]);

            return new Response($content);
        }

        return [
            'articles' => $articles,
            'nextPageUrl' => $nextPageUrl,
            'delete_forms' => $delete_forms
        ];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     * @Route("/author/{slug}", name="show_user_articles")
     * @Method("GET")
     * @Template("AppBundle:Blog:articlesList.html.twig")
     */
    public function showUserArticlesAction(Request $request, $slug)
    {
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articlesCount = $repository->findAllUserArticlesCount($slug);

        $nextPage = $this->get('app.pagination_service')->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findAllUserArticles($slug, $currentPage, $this->getParameter('articles_show_at_a_time'));

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('show_user_articles', ['slug' => $slug, 'page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        $delete_forms = $this->get('app.delete_form_service')->getArticlesDeleteForms($articles);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl, 'delete_forms' => $delete_forms]);

            return new Response($content);
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->findOneBy(['slug' => $slug]);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 1, 'text' => $user ? $user->getUsername() : null],
            'nextPageUrl' => $nextPageUrl,
            'delete_forms' => $delete_forms
        ];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     * @Route("/tag/{slug}", name="show_tag_articles")
     * @Method("GET")
     * @Template("AppBundle:Blog:articlesList.html.twig")
     */
    public function showTagArticlesAction(Request $request, $slug)
    {
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articlesCount = $repository->findAllTagArticlesCount($slug);

        $nextPage = $this->get('app.pagination_service')->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findAllTagArticles($slug, $currentPage, $this->getParameter('articles_show_at_a_time'));

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('show_tag_articles', ['slug' => $slug, 'page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        $delete_forms = $this->get('app.delete_form_service')->getArticlesDeleteForms($articles);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl, 'delete_forms' => $delete_forms]);

            return new Response($content);
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag = $repository->findOneBy(['slug' => $slug]);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 2, 'text' => $tag ? $tag->getName() : null],
            'nextPageUrl' => $nextPageUrl,
            'delete_forms' => $delete_forms
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/search/", name="search_articles")
     * @Method("GET")
     * @Template("AppBundle:Blog:articlesList.html.twig")
     */
    public function showSearchArticlesAction(Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);
        $search_string = $request->query->get('q');

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articlesCount = $repository->findSearchedArticlesCount($search_string);

        $nextPage = $this->get('app.pagination_service')->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findSearchedArticles($search_string, $currentPage, $this->getParameter('articles_show_at_a_time'));

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('search_articles', ['q' => $search_string, 'page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        $delete_forms = $this->get('app.delete_form_service')->getArticlesDeleteForms($articles);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl, 'delete_forms' => $delete_forms]);

            return new Response($content);
        }

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 3, 'text' => $search_string],
            'nextPageUrl' => $nextPageUrl,
            'delete_forms' => $delete_forms
        ];
    }
}