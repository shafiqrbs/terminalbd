<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\DomainUserBundle\Form\SalesCustomerType;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SalesGeneralType extends AbstractType
{



    /** @var GlobalOption */
    public  $globalOption;

    /** @var  LocationRepository */
    private $location;


    function __construct(GlobalOption $globalOption,  LocationRepository $location )
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('cardNo','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment card/cheque no','data-original-title'=>'Add payment card/cheque no','autocomplete'=>'off')))
            ->add('transactionId','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add payment transaction id','data-original-title'=>'Add payment transaction id','autocomplete'=>'off')))
            ->add('paymentMobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add payment mobile no','data-original-title'=>'Add payment mobile no','autocomplete'=>'off')))
            ->add('transactionMethod', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\TransactionMethod',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2 transactionMethod'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('paymentCard', 'entity', array(
                'required'    => false,
                'property' => 'name',
                'class' => 'Setting\Bundle\ToolBundle\Entity\PaymentCard',
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose payment method---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy("e.id","ASC");
                }
            ))
            ->add('salesBy', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Sales By---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->where("u.isDelete != 1")
                        ->andWhere("u.globalOption =".$this->globalOption->getId())
                        ->orderBy("u.username", "ASC");
                }
            ))

            ->add('accountBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Choose receive bank account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ))

            ->add('accountMobileBank', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Choose receive mobile account---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption =".$this->globalOption->getId())
                        ->orderBy("b.name", "ASC");
                }
            ));
            $builder->add('customer', new SalesCustomerType($this->location));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Sales'
        ));
    }

}
