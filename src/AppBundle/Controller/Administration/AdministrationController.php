<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/16/16
 * Time: 7:37 PM
 */

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Article;

/**
 * @Route("/administration")
 */
class AdministrationController extends Controller
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

        $nextPage = $this->get('app.pagination_service')->getNextPageNumber($articlesCount, $currentPage);

        $articles = $repository->findAllArticles($currentPage, $this->getParameter('articles_show_at_a_time'));

        if ($nextPage) {
            $nextPageUrl = $this->generateUrl('administration_homepage', ['page' => $nextPage]);
        } else {
            $nextPageUrl = false;
        }

        $delete_forms = $this->get('app.delete_form_service')->getArticlesDeleteForms($articles);

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderView('AppBundle:Administration:articlesForList.html.twig',
                ['articles' => $articles, 'delete_forms' => $delete_forms, 'nextPageUrl' => $nextPageUrl]);

            return new Response($content);
        }

        return ['articles' => $articles, 'delete_forms' => $delete_forms, 'nextPageUrl' => $nextPageUrl];
    }
}