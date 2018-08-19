<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BusinessDamageType extends AbstractType
{

	/** @var  $config BusinessConfig */

	public  $config;

	public function __construct(BusinessConfig  $config)
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

	        ->add('businessParticular', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessParticular',
		        'empty_value' => '---Choose a product ---',
		        'property' => 'name',
		        'attr'=>array('class'=>'span12 m-wrap select2'),
		        'constraints' =>array( new NotBlank(array('message'=>'Please select your vendor name')) ),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->where("e.status = 1")
			                  ->andWhere("e.businessConfig =".$this->config->getId());
		        },
	        ))
	        ->add('notes','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter notes ')))
	        ->add('quantity','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter medicine name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                )
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessDamage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'businessParticular';
    }


}
