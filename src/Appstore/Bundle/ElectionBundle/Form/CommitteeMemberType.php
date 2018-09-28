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

class CommitteeMemberType extends AbstractType
{

	/** @var  ElectionConfig */

	private $config;


	function __construct(ElectionConfig $config)
	{
		$this->config         = $config;
	}

	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

	        ->add('designation', 'entity', array(
		        'required'    => true,
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionParticular',
		        'empty_value' => '--- Choose the member designation ---',
		        'property' => 'name',
		        'attr'=>array('class'=>'m-wrap span6 inputs'),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('e')
			                  ->join("e.particularType","p")
			                  ->where("e.status = 1")
			                  ->andWhere("p.slug = 'designation'");
		        },
	        ))
	        ->add('electionMember', 'entity', array(
		        'required'    => true,
		        'attr'=>array('class'=>'category m-wrap span6 select2'),
		        'constraints' =>array( new NotBlank(array('message'=>'Choose member for committee')) ),
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionMember',
		        'property' => 'nestedLabel',
		        'choices'=> $this->locationChoiceList()
	        ));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionCommitteeMember'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'committee';
    }

	/**
	 * @return mixed
	 */
	protected function locationChoiceList()
	{
		return $categoryTree = $this->location->getLocationGroup($this->config->getId());

	}



}
