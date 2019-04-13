<?php

namespace Appstore\Bundle\AccountingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountConfigType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accountClose')
            ->add('purchase')
            ->add('borderColor','text', array('attr'=>array(
                'class'=>'m-wrap span9 colorpicker-default',
                'placeholder'=>'')
            ))
            ->add('bodyFontSize', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12'),
                'choices' => array('' => 'Font Size','10px' => '10px',  '11px' => '11px','12px' => '12px','13px' => '13px','14px' => '14px', '15px' => '15px', '16px' => '16px', '17px' => '17px','18px' => '18px',  '20px' => '20px', '22px' => '22px','24px' => '24px', '26px' => '26px',  '28px' => '28px','30px' => '39px','32px' => '32px','34px' => '34px','36px' => '36px', '38px' => '38px', '40px' => '40px','42px' => '42px',  '44px' => '44px', '46px' => '46px','48px' => '48px'),
            ))
            ->add('borderWidth','text',array('attr'=>array('class'=>'m-wrap numeric span8','maxLength'=>2)))
            ->add('isPowered')
            ->add('isPrintHeader')
            ->add('isPrintFooter')
            ->add('invoiceWidth','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('invoiceHeight','text',array('attr'=>array('class'=>'m-wrap numeric span12')))
            ->add('printLeftMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
            ->add('printTopMargin','text',array('attr'=>array('class'=>'m-wrap numeric span8')))
           ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountingConfig'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'config';
    }

}
