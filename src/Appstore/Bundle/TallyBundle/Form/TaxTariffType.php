<?php

namespace Appstore\Bundle\TallyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaxTariffType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'name', 'text', array(
                'attr'        => array( 'class' => 'm-wrap span12', 'placeholder' => 'Enter tax tariff name' ),
                'constraints' => array(
                    new NotBlank( array( 'message' => 'Please tax tariff name' ) )
                )
            ))

            ->add( 'hsCode', 'text', array(
                'attr'        => array( 'class' => 'm-wrap span3','placeholder' => 'Enter tax tariff code' ),
                'constraints' => array(
                    new NotBlank( array( 'message' => 'Please tax tariff code' ) )
                )
            ))
            ->add( 'customsDuty', 'text', array(
                'attr'        => array( 'class' => 'm-wrap span3', 'placeholder' => 'Enter customs duty' )))
              ->add( 'supplementaryDuty', 'text', array(
                    'attr'        => array( 'class' => 'm-wrap span3', 'placeholder' => 'Enter customs duty' )))
              ->add( 'valueAddedTax', 'text', array(
                    'attr'        => array( 'class' => 'm-wrap span3', 'placeholder' => 'Enter customs duty' )))
              ->add( 'advanceIncomeTax', 'text', array(
                    'attr'        => array( 'class' => 'm-wrap span3', 'placeholder' => 'Enter customs duty' )))
              ->add( 'advanceTradeVat', 'text', array(
                    'attr'        => array( 'class' => 'm-wrap span3', 'placeholder' => 'Enter customs duty' )))
            ->add( 'recurringDeposit', 'text', array(
                    'attr'        => array( 'class' => 'm-wrap span3', 'placeholder' => 'Enter customs duty' )))

          ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\TallyBundle\Entity\TaxTariff'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tariffUpload';
    }
}
