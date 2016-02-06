<?php

namespace AppBundle\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Author;
use AppBundle\Entity\Tag;
use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl]);

            return new Response($content);
        }

        return [
            'articles' => $articles,
            'nextPageUrl' => $nextPageUrl
        ];
    }

    /**
     * @param $slug
     * @return array
     * @Route("/article/{slug}", name="show_article")
     * @Method("GET")
     * @Template("AppBundle:Blog:article.html.twig")
     */
    public function showArticleAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findArticleBySlug($slug);

        if (!$article) {
            return $this->redirectToRoute('homepage');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('add_comment', ['slug' => $slug]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Add comment']);

        return [
            'article' => $article,
            'form' => $form->createView()
        ];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/article/{slug}", name="add_comment")
     * @Method("POST")
     * @Template("AppBundle:Blog:article.html.twig")
     */
    public function addCommentAction(Request $request, $slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findArticleBySlug($slug);

        $comment = new Comment();
        $comment->setArticle($article[0]);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('add_comment', ['slug' => $slug]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Add comment']);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('show_article', ['slug' => $comment->getArticle()->getSlug()]));
        }

        return [
            'article' => $article,
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     * @Route("/author/{slug}", name="show_author_articles")
     * @Method("GET")
     * @Template("AppBundle:Blog:articlesList.html.twig")
     */
    public function showAuthorArticlesAction(Request $request, $slug)
    {
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articlesCount = $repository->findAllAuthorArticlesCount($slug);

        $nextPage = $this->get('app.pagination_service')->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findAllAuthorArticles($slug, $currentPage, $this->getParameter('articles_show_at_a_time'));

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('show_author_articles', ['slug' => $slug, 'page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl]);

            return new Response($content);
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Author');
        $author = $repository->findOneBy(['slug' => $slug]);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 1, 'text' => $author ? $author->getName() : null],
            'nextPageUrl' => $nextPageUrl
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

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl]);

            return new Response($content);
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag = $repository->findOneBy(['slug' => $slug]);

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 2, 'text' => $tag ? $tag->getName() : null],
            'nextPageUrl' => $nextPageUrl
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

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Blog:articlesForList.html.twig',
                ['articles' => $articles, 'nextPageUrl' => $nextPageUrl]);

            return new Response($content);
        }

        return [
            'articles' => $articles,
            'articles_description' => ['type' => 3, 'text' => $search_string],
            'nextPageUrl' => $nextPageUrl
        ];
    }
}