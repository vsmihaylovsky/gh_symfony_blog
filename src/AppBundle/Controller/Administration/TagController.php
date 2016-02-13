<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 2/7/16
 * Time: 11:01 PM
 */

namespace AppBundle\Controller\Administration;

use AppBundle\Form\Type\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Tag;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/administration/tag")
 */
class TagController extends Controller
{
    /**
     * @return array
     * @Route("/", name="show_all_tags")
     * @Method("GET")
     * @Template("AppBundle:Tag:showAll.html.twig")
     */
    public function showAllAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');

        $tags = $repository->findAll();
        $delete_forms = $this->get('app.delete_form_service')->getTagsDeleteForms($tags);

        return ['tags' => $tags, 'delete_forms' => $delete_forms];
    }

    /**
     * @return array
     * @Route("/new", name="new_tag")
     * @Method("GET")
     * @Template("AppBundle:Tag:form.html.twig")
     */
    public function newAction()
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('create_tag'),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Create']);

        return ['form' => $form->createView()];
    }

    /**
     * @param $slug
     * @return array
     * @Route("/edit/{slug}", name="edit_tag")
     * @Method("GET")
     * @Template("AppBundle:Tag:form.html.twig")
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->findOneBy(['slug' => $slug]);
        if (!$tag) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('update_tag', ['slug' => $slug]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/", name="create_tag")
     * @Method("POST")
     * @Template("AppBundle:Tag:form.html.twig")
     */
    public function createAction(Request $request)
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('create_tag'),
            'method' => 'POST',
        ])
            ->add('save', SubmitType::class, ['label' => 'Create']);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->redirect($this->generateUrl('new_tag'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{slug}", name="update_tag")
     * @Method("PUT")
     * @Template("AppBundle:Tag:form.html.twig")
     */
    public function updateAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->findOneBy(['slug' => $slug]);
        if (!$tag) {
            throw $this->createNotFoundException('Unable to find Tag entity.');
        }

        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('update_tag', ['slug' => $slug]),
            'method' => 'PUT',
        ])
            ->add('save', SubmitType::class, ['label' => 'Update']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('edit_tag', ['slug' => $tag->getSlug()]));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{slug}", name="delete_tag")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $slug)
    {
        $form = $this->get('app.delete_form_service')->createTagDeleteForm($slug);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $tag = $em->getRepository('AppBundle:Tag')->findOneBy(['slug' => $slug]);
            if (!$tag) {
                throw $this->createNotFoundException('Unable to find Tag entity.');
            }

            $em->remove($tag);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('show_all_tags'));
    }
}