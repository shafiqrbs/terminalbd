<?php

namespace Appstore\Bundle\AssetsBundle\Form;

use Appstore\Bundle\AssetsBundle\Entity\AssetsConfig;
use Appstore\Bundle\AssetsBundle\Entity\TallyConfig;
use Appstore\Bundle\AssetsBundle\Repository\AssetsCategoryRepository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping;
use Appstore\Bundle\InventoryBundle\Repository\ItemTypeGroupingRepository;
use Appstore\Bundle\AssetsBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotNull;

class AssetsItemType extends AbstractType
{

    /** @var AssetsConfig */

    public  $config;

    /** @var  CategoryRepository */
    private $em;

    function __construct(AssetsConfig $config , CategoryRepository $em)
    {
        $this->em = $em;
        $this->config = $config;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add( 'name', 'text', array(
                'attr'        => array( 'class' => 'm-wrap span12', 'placeholder' => 'Enter  product item' ),
                'constraints' => array(
                    new NotBlank( array( 'message' => 'Please add  product item' ) )
                )
            ))
            ->add( 'productType', 'hidden', array('attr' => array('value'=>'Assets')))
            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select product category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\Category',
                'constraints' => array(
                    new NotBlank( array( 'message' => 'Please select  product category' ) )
                ),
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))

            ->add('productGroup', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\Setting',
                'empty_value' => 'Choose a product group',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' => array(
                    new NotBlank( array( 'message' => 'Please select  product group' ) )
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.type = 'Product Group'");
                },
            ))

            ->add('brand', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AssetsBundle\Entity\Brand',
                'empty_value' => 'Choose a brand',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->Where("b.status = 1")
                        ->andWhere("b.config = {$this->config->getId()}");
                },
            ))
            ->add('file')
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\AssetsBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'item';
    }


    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
        return $categoryTree = $this->em->getFlatCategoryTree($this->config);
    }
}
