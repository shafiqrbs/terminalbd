<?php

namespace Appstore\Bundle\AssetsBundle\Form;

use Appstore\Bundle\AssetsBundle\Entity\AssetsConfig;
use Appstore\Bundle\TallyBundle\Entity\TallyConfig;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseOpeningItemType extends AbstractType
{

    /** @var AssetsConfig */

    public  $config;



    function __construct(AssetsConfig $config)
    {

        $this->config = $config;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('item', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\Item',
                'empty_value' => 'Choose a product item name',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' => array( new NotBlank( array( 'message' => 'Please select product item name' ))),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->Where("b.status = 1")
                        ->andWhere("b.config = {$this->config->getId()}");
                },
            ))
            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap vendor'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->config->getGlobalOption()->getId());
                },
            ))

            ->add('stakeholder', 'entity', array(
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'empty_value' => '---Choose a stakeholder---',
                'property' => 'userFullName',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->join('u.profile','p')
                        ->where("u.isDelete != 1")
                        ->andWhere("u.domainOwner = 2")
                        ->andWhere("p.userGroup = 'Stakeholder'")
                        ->andWhere("u.globalOption ={$this->config->getGlobalOption()->getId()}")
                        ->orderBy("u.username", "ASC");
                },
            ))
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12'))) ->add( 'name', 'text', array(
                'attr'        => array( 'class' => 'm-wrap span12', 'placeholder' => 'Enter  product item' ),
            ))
            ->add('price','text', array('attr'=>array('class'=>'m-wrap span12', 'placeholder' => 'Enter  price'),
                'constraints' => array( new NotBlank( array( 'message' => 'Please enter item price' )))
            ))
            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12', 'placeholder' => 'Enter sales price')))
            ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12', 'placeholder' => 'Enter  quantity'),
                'constraints' => array(  new NotBlank( array( 'message' => 'Please add  product quantity' )))
            ))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>'3', 'placeholder' => 'Enter  remarks')))
            ->add('effectedDate', 'date', array(
                    'widget' => 'single_text',
                    'placeholder' => array(
                        'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'
                    ),
                    'format' => 'dd-MM-yyyy',
                    'attr' => array('class'=>'m-wrap span12 dateCalendar','autocomplete' => "off"),
                    'view_timezone' => 'Asia/Dhaka')
            )

            ->add('assuranceFromVendor', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ItemWarning',
                'empty_value' => 'Choose a item warning',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1");
                },
            ))
            ->add('assuranceToCustomer', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ItemWarning',
                'empty_value' => 'Choose a item warning',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1");
                },
            ))
            ->add('assuranceType', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12 m-wrap'),
                'empty_value' => '---Choose an Assurance---',
                'choices' => array(
                    'AMC' => 'AMC',
                    'Guarantee' => 'Guarantee',
                    'Warranty' => 'Warranty',
                    'No-warranty' => 'No-warranty'
                ),
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\PurchaseItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchaseItem';
    }
}
