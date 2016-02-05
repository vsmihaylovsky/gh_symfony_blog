<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/31/16
 * Time: 4:31 PM
 */

namespace AppBundle\Services;

use AppBundle\Entity\Article;
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
}