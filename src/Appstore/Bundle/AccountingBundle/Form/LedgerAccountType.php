<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class LedgerAccountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter vendor name')))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter mobile no')))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter ledger account address')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email address')));


    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\LedgerAccount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ledger';
    }
}
