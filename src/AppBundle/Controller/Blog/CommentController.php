<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 2/8/16
 * Time: 7:22 PM
 */

namespace AppBundle\Controller\Blog;

use AppBundle\Form\Type\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * @param $id
     * @return array
     * @Route("/edit/{id}", name="edit_comment")
     * @Method("GET")
     * @Template("AppBundle:Comment:form.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')->findOneBy(['id' => $id]);
        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }
        $this->denyAccessUnlessGranted('edit', $comment);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('update_comment', ['id' => $id]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", name="update_comment")
     * @Method("PUT")
     * @Template("AppBundle:Comment:form.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')->findOneBy(['id' => $id]);
        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }
        $this->denyAccessUnlessGranted('edit', $comment);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('update_comment', ['id' => $id]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('show_article', ['slug' => $comment->getArticle()->getSlug()]));
        }

        return ['form' => $form->createView()];
    }
    
    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}", name="delete_comment")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')->findOneBy(['id' => $id]);
        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }
        $this->denyAccessUnlessGranted('edit', $comment);

        $article_slug = $comment->getArticle()->getSlug();

        $form = $this->get('app.delete_form_service')->createCommentDeleteForm($id);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('show_article', ['slug' => $article_slug]));
    }
}