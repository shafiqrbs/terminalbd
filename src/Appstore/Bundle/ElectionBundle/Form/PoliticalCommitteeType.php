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

class PoliticalCommitteeType extends AbstractType
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

            ->add('wing', 'entity', array(
                'required'    => true,
                'class' => 'ElectionBundle:ElectionParticular',
                'empty_value' => '--- Choose the type of election ---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'constraints' =>array( new NotBlank(array('message'=>'Choose the type of election')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","p")
                        ->where("e.status = 1")
                        ->andWhere("p.slug = 'political-wings'");
                },
            ))

            ->add('type', 'entity', array(
                'required'    => true,
                'class' => 'ElectionBundle:ElectionParticular',
                'empty_value' => '--- Choose the type of election ---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 inputs'),
                'constraints' =>array( new NotBlank(array('message'=>'Choose the type of committee')) ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->join("e.particularType","p")
                        ->where("e.status = 1")
                        ->andWhere("p.slug = 'committee-type'");
                },
            ))

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span6 inputs','autocomplete'=>'off','placeholder'=>'Enter committee name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter committee name')),
                )
            ))

	        ->add('location', 'entity', array(
		        'required'    => true,
		        'attr'=>array('class'=>'category m-wrap span6 select2'),
		        'constraints' =>array( new NotBlank(array('message'=>'Choose location for committee')) ),
		        'class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionLocation',
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
            'data_class' => 'Appstore\Bundle\ElectionBundle\Entity\ElectionCommittee'
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
		return $categoryTree = $this->location->getFlatLocationTree();
		//return $categoryTree = $this->location->getLocationGroupByTree($this->config->getId());

	}



}
