<?php

namespace Appstore\Bundle\ElectionBundle\Form;

use Appstore\Bundle\ElectionBundle\Entity\ElectionConfig;
use Appstore\Bundle\ElectionBundle\Repository\ElectionLocationRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class VotecenterType extends AbstractType
{

	/** @var  ElectionConfig */

	private $config;

	/** @var  ElectionLocationRepository */

	private $location;

	function __construct(ElectionConfig $config, ElectionLocationRepository $location)
	{
		$this->config         = $config;
		$this->location       = $location;
	}

	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

	        ->add('electionType', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionParticular',
		        'empty_value' => '--- Choose the type of election ---',
		        'property' => 'name',
		        'attr'=>array('class'=>'m-wrap span12 inputs'),
		        'constraints' =>array( new NotBlank(array('message'=>'Choose the type of election')) ),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->join("e.particularType","p")
			                  ->where("e.status = 1")
			                  ->andWhere("p.slug = 'election-type'");
		        },
	        ))
	        ->add('location', 'entity', array(
		        'required'    => true,
		        'property' => 'voteCenterName',
		        'attr'=>array('class'=>'m-wrap span12 select2'),
		        'constraints' =>array( new NotBlank(array('message'=>'Choose location for vote center')) ),
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionLocation',
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->join("e.locationType","p")
			                  ->where("e.status = 1")
			                  ->andWhere("e.electionConfig =". $this->config->getId())
			                  ->andWhere("p.slug = 'vote-center'");
		        },
	        ))
	        ->add('representative', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionMember',
		        'empty_value' => '---Choose a representative ---',
		        'property' => 'nameMobile',
		        'attr'=>array('class'=>'m-wrap span12 inputs'),
		        'constraints' =>array( new NotBlank(array('message'=>'Select representative')) ),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->where("e.electionConfig =".$this->config->getId())
			                  ->andWhere("e.status = 1");
		        },
	        ))
	        ->add('representativeMobile','text', array('attr'=>array('class'=>'m-wrap span12 inputs', 'autocomplete'=>'off','placeholder'=>'Enter representative mobile no')))
	        ->add('electionDate','date', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter election date')))
	        ->add('presiding','text', array('attr'=>array('class'=>'m-wrap span12 inputs ','autocomplete'=>'off','placeholder'=>'Enter presiding officer name')))
	        ->add('presidingDesignation','text', array('attr'=>array('class'=>'m-wrap span12 inputs ','autocomplete'=>'off','placeholder'=>'Enter presiding designation')))
	        ->add('presidingAddress','textarea', array('attr'=>array('class'=>'m-wrap span12 inputs','rows'=> 3,'autocomplete'=>'off','placeholder'=>'Enter presiding address')))
	        ->add('presidingMobile','text', array('attr'=>array('class'=>'m-wrap span12 inputs', 'autocomplete'=>'off','placeholder'=>'Enter presiding mobile')))
	        ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 inputs', 'rows'=> 4,'autocomplete'=>'off','placeholder'=>'Enter center addree')))
	       ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionVotecenter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'votecenter';
    }

	/**
	 * @return mixed
	 */
	protected function locationChoiceList()
	{
		return $categoryTree = $this->location->getLocationGroup($this->config->getId());

	}



}
