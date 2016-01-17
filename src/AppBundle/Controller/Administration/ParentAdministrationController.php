<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/17/16
 * Time: 2:29 PM
 */

namespace AppBundle\Controller\Administration;

use AppBundle\Controller\ParentController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ParentAdministrationController extends ParentController
{
    /**
     * @param $slug
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createArticleDeleteForm($slug)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete_article', ['slug' => $slug]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class)
            ->getForm();
    }
}