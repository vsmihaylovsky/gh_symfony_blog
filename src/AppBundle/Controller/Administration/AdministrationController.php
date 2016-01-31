<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/16/16
 * Time: 7:37 PM
 */

namespace AppBundle\Controller\Administration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Article;

/**
 * @Route("/administration")
 */
class AdministrationController extends ParentAdministrationController
{
    /**
     * @param Request $request
     * @return array
     * @Route("/", name="administration_homepage")
     * @Method("GET")
     * @Template("AppBundle:Administration:articlesList.html.twig")
     */
    public function indexAction(Request $request)
    {
        $currentPage = $request->query->getInt('page', 1);

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articlesCount = $repository->findAllArticlesCount();

        $nextPage = $this->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findAllArticles($currentPage, $this->articlesShowAtATime);

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('administration_homepage', ['page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        $delete_forms = $this->getArticlesDeleteForms($articles);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Administration:articlesForList.html.twig',
                ['articles' => $articles, 'delete_forms' => $delete_forms, 'nextPageUrl' => $nextPageUrl]);

            return new Response($content);
        }

        return ['articles' => $articles, 'delete_forms' => $delete_forms, 'nextPageUrl' => $nextPageUrl];
    }

    /**
     * @param $articles
     * @return array
     */
    private function getArticlesDeleteForms($articles) {
        $delete_forms = [];
        /** @var Article $article */
        foreach ($articles as $article) {
            $delete_forms[$article[0]->getId()] = $this->createArticleDeleteForm($article[0]->getSlug())->createView();
        }

        return $delete_forms;
    }
}