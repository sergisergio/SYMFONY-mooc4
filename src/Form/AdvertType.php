<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;
use App\Form\CkeditorType;

class AdvertType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('date',      DateTimeType::class)
      ->add('title',     TextType::class)
      ->add('author',    TextType::class)
      ->add('content',   TextAreaType::class)
      //->add('published', CheckboxType::class, array('required' => false))
      ->add('image',     ImageType::class)
      // COLLECTIONTYPE
      /*->add('categories', CollectionType::class, array(
        'entry_type'   => CategoryType::class,
        'allow_add'    => true,
        'allow_delete' => true
      ))*/

      // ENTITYTYPE
      ->add('categories', EntityType::class, array(
        'class'        => Category::class,
        'choice_label' => 'name',
        'multiple'     => true,
      ))
      ->add('save',      SubmitType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Advert'
    ));
  }
}
