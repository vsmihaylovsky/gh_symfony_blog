<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/18/16
 * Time: 10:49 PM
 */
namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;
use AppBundle\Entity\Comment;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'rating' => '5',
            'messageText' => 'message text',
        );

        $form = $this->factory->create(CommentType::class);

        $object = new Comment();
        $object->setRating(5);
        $object->setMessageText('message text');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}