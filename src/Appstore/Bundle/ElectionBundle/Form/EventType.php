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

class EventType extends AbstractType
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

            ->add('organiser', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionMember',
		        'empty_value' => '---Choose a member ---',
		        'property' => 'nameMobile',
		        'attr'=>array('class'=>'m-wrap span12 inputs'),
		        'constraints' =>array( new NotBlank(array('message'=>'Select event organiser')) ),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			               ->where("e.electionConfig =".$this->config->getId())
			               ->andWhere("e.status = 1");
		        },
	        ))
	        ->add('eventDate','date', array('attr'=>array('class'=>'m-wrap span12 inputs','placeholder'=>'Enter receive date')))
	        ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 inputs','autocomplete'=>'off','placeholder'=>'Enter campaign title')))
	        ->add('email','text', array('attr'=>array('class'=>'m-wrap span12 inputs','autocomplete'=>'off','placeholder'=>'Enter campaign title')))
	        ->add('facebookEvent','text', array('attr'=>array('class'=>'m-wrap span12 inputs','autocomplete'=>'off','placeholder'=>'Enter campaign title')))
	        ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 inputs','autocomplete'=>'off','placeholder'=>'Enter campaign title'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter event name')),
                )
            ))
	        ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12 inputs','rows' => 5,'autocomplete'=>'off','placeholder'=>'Enter campaign content')))
	        ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 inputs','rows' => 8,'autocomplete'=>'off','placeholder'=>'Enter campaign content')))
	        ->add('eventType', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionParticular',
		        'empty_value' => '---Choose a event type ---',
		        'property' => 'name',
		        'attr'=>array('class'=>'m-wrap span12 inputs'),
		        'constraints' =>array( new NotBlank(array('message'=>'Select event type')) ),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->join('e.particularType','p')
			                  ->where("e.status = 1")
			                  ->andWhere("p.slug = 'event'");
		        },
	        ))
	        ->add('location', 'entity', array(
		        'required'    => true,
		        'attr'=>array('class'=>'m-wrap span12 select2'),
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionLocation',
		        'property' => 'nestedLabel',
		        'choices'=> $this->locationChoiceList()
	        ))
	        ->add('removeImage')
	        ->add('file')
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionEvent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'event';
    }

	/**
	 * @return mixed
	 */
	protected function locationChoiceList()
	{
		return $categoryTree = $this->location->getFlatLocationTree();

	}



}
