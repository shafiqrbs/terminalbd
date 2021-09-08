<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BusinessStoreLedgerType extends AbstractType
{


    /** @var  $option GlobalOption  */
    public  $option;

    public function __construct(GlobalOption $option)
    {
        $this->option = $option;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('amount','text', array('attr'=>array('class'=>'m-wrap span6','autocomplete'=>'off','placeholder'=>'Enter amount'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Enter amount'))
                    ))
            )
            ->add('transactionType', 'choice', array(
                'attr'=>array('class'=>'m-wrap discount-type span6'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Receive' => 'Receive',
                    'Due' => 'Due',
                    'Opening' => 'Opening',
                ),
            ))
            ->add('store', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessStore',
                'property' => 'name',
                'empty_value' => '---Choose a store ---',
                'attr'=>array('class'=>'span12 m-wrap select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter store'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.businessConfig ={$this->option->getBusinessConfig()->getId()}")
                        ->orderBy("e.name","ASC");
                }
            ))



        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessStoreLedger'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }


}
