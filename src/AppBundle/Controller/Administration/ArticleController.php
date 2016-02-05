<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/17/16
 * Time: 6:02 PM
 */

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/administration/article")
 */
class ArticleController extends Controller
{
    /**
     * @return array
     * @Route("/new", name="new_article")
     * @Method("GET")
     * @Template("AppBundle:Administration/Article:form.html.twig")
     */
    public function newAction()
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('create_article'),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Create']);

        return ['form' => $form->createView()];
    }

    /**
     * @param $slug
     * @return array
     * @Route("/edit/{slug}", name="edit_article")
     * @Method("GET")
     * @Template("AppBundle:Administration/Article:form.html.twig")
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', ['slug' => $slug]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        return ['form' => $form->createView(), 'article' => $article];
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/", name="create_article")
     * @Method("POST")
     * @Template("AppBundle:Administration/Article:form.html.twig")
     */
    public function createAction(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('create_article'),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Create']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirect($this->generateUrl('show_article', ['slug' => $article->getSlug()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{slug}", name="update_article")
     * @Method("PUT")
     * @Template("AppBundle:Administration/Article:form.html.twig")
     */
    public function updateAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', ['slug' => $slug]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('show_article', ['slug' => $article->getSlug()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{slug}", name="delete_article")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $slug)
    {
        $form = $this->get('app.delete_form_service')->createArticleDeleteForm($slug);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository('AppBundle:Article')->findOneBy(['slug' => $slug]);

            if (!$article) {
                throw $this->createNotFoundException('Unable to find Article entity.');
            }

            $em->remove($article);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('administration_homepage'));
    }
}