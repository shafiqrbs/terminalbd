<?php

namespace Appstore\Bundle\RestaurantBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter accessories name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('service', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Service',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose product type ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere('e.slug IN (:slugs)')
                        ->setParameter('slugs',array('consuamble','stockable'))
                        ->orderBy("e.sorting","ASC");
                }
            ))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ))
            ->add('category', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\RestaurantBundle\Entity\Category',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a category ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.sorting","ASC");
                }
            ))
            ->add('unit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a unit ---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('file')

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\RestaurantBundle\Entity\Particular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_particular';
    }


}
