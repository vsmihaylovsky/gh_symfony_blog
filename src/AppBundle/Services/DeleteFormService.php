<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 4:31 PM
 */

namespace AppBundle\Services;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Tag;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Router;

class DeleteFormService
{
    private $formFactory;
    private $router;

    public function __construct(FormFactory $formFactory, Router $router)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param $slug
     * @return \Symfony\Component\Form\Form The form
     */
    public function createArticleDeleteForm($slug)
    {
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate('delete_article', ['slug' => $slug]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * @param $articles
     * @return array
     */
    public function getArticlesDeleteForms($articles) {
        $delete_forms = [];
        foreach ($articles as $article) {
            /** @var Article $article_entity */
            $article_entity = $article[0];
            $delete_forms[$article_entity->getId()] = $this->createArticleDeleteForm($article_entity->getSlug())->createView();
        }

        return $delete_forms;
    }

    /**
     * @param $slug
     * @return \Symfony\Component\Form\Form The form
     */
    public function createTagDeleteForm($slug)
    {
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate('delete_tag', ['slug' => $slug]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * @param $tags
     * @return array
     */
    public function getTagsDeleteForms($tags) {
        $delete_forms = [];
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $delete_forms[$tag->getId()] = $this->createTagDeleteForm($tag->getSlug())->createView();
        }

        return $delete_forms;
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCommentDeleteForm($id)
    {
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->formFactory->createBuilder();

        return $formBuilder
            ->setAction($this->router->generate('delete_comment', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * @param $comments
     * @return array
     */
    public function getCommentsDeleteForms($comments) {
        $delete_forms = [];
        /** @var Comment $comment */
        foreach ($comments as $comment) {
            $delete_forms[$comment->getId()] = $this->createCommentDeleteForm($comment->getId())->createView();
        }

        return $delete_forms;
    }
}