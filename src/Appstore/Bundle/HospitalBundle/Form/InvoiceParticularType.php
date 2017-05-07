<?php

namespace Appstore\Bundle\HospitalBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class InvoiceParticularType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('In-progress' => 'In-progress','Done' => 'Done', 'Damage' => 'Damage', 'Impossible' => 'Impossible'),
            ))
            ->add('comment','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Add any comment','autocomplete'=>'off')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_invoice_particular';
    }



}
