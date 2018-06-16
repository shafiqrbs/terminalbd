<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

           ->add('overHead','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Over head')))
            ->add('packaging','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Packaging')))
            ->add('utility','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Utility')))
            ->add('marketing','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Marketing')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessParticular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'production';
    }


}
