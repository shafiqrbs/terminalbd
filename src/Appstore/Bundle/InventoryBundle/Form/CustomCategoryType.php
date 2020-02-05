<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class CustomCategoryType extends AbstractType
{

	/** @var  InventoryConfig */

	private $inventoryConfig;

	/** @var  CategoryRepository */
	private $em;

	function __construct(InventoryConfig $inventoryConfig , CategoryRepository $em)
	{
		$this->inventoryConfig = $inventoryConfig;
		$this->em = $em;
	}


	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder

			->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter category name'),
			                           'constraints' =>array(
				                           new NotBlank(array('message'=>'Please input required')),
			                           )
			))

			->add('parent', 'entity', array(
				'required'    => true,
				'empty_value' => '---Select parent category---',
				'attr'=>array('class'=>'category m-wrap span12 select2'),
				'class' => 'ProductProductBundle:Category',
				'property' => 'nestedLabel',
				'choices'=> $this->categoryChoiceList()
			))
		;
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Product\Bundle\ProductBundle\Entity\Category'
		));
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'product_bundle_productbundle_category';
	}

	/**
	 * @return mixed
	 */
	protected function categoryChoiceList()
	{

		return $categoryTree = $this->em->getUseInventoryItemCategory($this->inventoryConfig);

	}


}
