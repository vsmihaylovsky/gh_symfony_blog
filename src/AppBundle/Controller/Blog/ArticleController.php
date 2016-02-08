<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/17/16
 * Time: 6:02 PM
 */

namespace AppBundle\Controller\Blog;

use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @param $slug
     * @return array
     * @Route("/show/{slug}", name="show_article")
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

        $delete_comment_forms = $this->get('app.delete_form_service')->getCommentsDeleteForms($article[0]->getComments());

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('add_comment', ['slug' => $slug]),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Add comment']);

        return [
            'article' => $article,
            'form' => $form->createView(),
            'delete_comment_forms' => $delete_comment_forms
        ];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/show/{slug}", name="add_comment")
     * @Method("POST")
     * @Template("AppBundle:Blog:article.html.twig")
     */
    public function addCommentAction(Request $request, $slug)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findArticleBySlug($slug);

        $comment = new Comment();
        $comment->setUser($user);
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
     * @return array
     * @Route("/new", name="new_article")
     * @Method("GET")
     * @Template("AppBundle:Article:form.html.twig")
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
     * @Template("AppBundle:Article:form.html.twig")
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }
        $this->denyAccessUnlessGranted('edit', $article);

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
     * @Template("AppBundle:Article:form.html.twig")
     */
    public function createAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MODERATOR')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        $article = new Article();
        $article->setUser($user);

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
     * @Template("AppBundle:Article:form.html.twig")
     */
    public function updateAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }
        $this->denyAccessUnlessGranted('edit', $article);

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
            $this->denyAccessUnlessGranted('edit', $article);

            $em->remove($article);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('homepage'));
    }
}