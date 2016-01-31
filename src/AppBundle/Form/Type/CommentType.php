<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 1/14/16
 * Time: 11:02 PM
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Name'
                ]])
            ->add('email', EmailType::class)
            ->add('rating', NumberType::class, [
                'attr' => [
                    'class' => 'rating',
                    'data-min' => 0,
                    'data-max' => 5,
                    'data-step' => 1,
                    'data-show-clear' => 'false'
                ]])
            ->add('messageText', TextareaType::class,
                [
                    'attr' => [
                        'class' => 'tinymce'
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Comment',
        ]);
    }
}