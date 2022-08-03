<?php

namespace Appstore\Bundle\AssetsBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PurchaseType extends AbstractType
{



    /** @var  $option GlobalOption */

    public  $option;


    public function __construct(GlobalOption $option)
    {
        $this->option = $option;
        $this->config = $option->getAssetsConfig()->getId();

    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('vendor', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => '---Choose a vendor ---',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap vendor'),
                'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =".$this->option->getId());
                },
            ))
            ->add('officeNotes', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\OfficeNote',
                'empty_value' => '---Choose a office notes ---',
                'property' => 'refNo',
                'attr'=>array('class'=>'span12 m-wrap'),
                //'constraints' =>array( new NotBlank(array('message'=>'Please select office notes')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.isDelete IS NULL")
                       // ->andWhere("e.process ='Approved'")
                        ->andWhere("e.config =".$this->config);
                },
            ))
            ->add('remark','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Enter narration')))
            ->add('discount','text', array('attr'=>array('class'=>'m-wrap span12 inputs discount','placeholder'=>'Enter discount amount')));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\Purchase'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'purchase';
    }


}
