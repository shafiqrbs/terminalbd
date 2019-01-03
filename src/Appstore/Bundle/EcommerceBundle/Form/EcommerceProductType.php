<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceProductType extends AbstractType
{

    /** @var  EcommerceConfig */

    private $config;

    /** @var  CategoryRepository */
    private $em;


    function __construct(CategoryRepository $em , EcommerceConfig $config)
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

            ->add('webName','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Web product name')))
            ->add('brand', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\ItemBrand',
                'property' => 'name',
                'empty_value' => '-Choose a brand-',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.ecommerceConfig =".$this->config->getId())
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('warningText','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('warningLabel', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a warning label---',
                'choices' => array(
                    'Warranty' => 'Warranty',
                    'Guarantee' => 'Guarantee',
                ),
            ))
            ->add('size', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemSize',
                'empty_value' => '-Choose a size-',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.isValid = 1")
                        ->orderBy("p.name","ASC");
                },
            ))
            ->add('country', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',
                'empty_value' => '---Choose a country ---',
                'property' => 'name',
                'attr'=>array('class'=>'span12 '),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy("p.name","ASC");

                },
            ))
	        ->add('category', 'entity', array(
		        'required'    => true,
		        'empty_value' => '---Select product category---',
		        'attr'=>array('class'=>'category m-wrap span12 select2'),
		        'constraints' =>array(
			        new NotBlank(array('message'=>'Please input required'))
		        ),
		        'class' => 'ProductProductBundle:Category',
		        'property' => 'nestedLabel',
		        'choices'=> $this->categoryChoiceList()
	        ))
            ->add('productUnit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'empty_value' => '-Choose a unit-',
                'property' => 'name',
                'attr'=>array('class'=>'span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))

            ->add('quantity','number', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'quantity')))

            ->add('purchasePrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'purchase price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add purchase price'))
            )))

            ->add('overHeadCost','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'over head cost')))

            ->add('salesPrice','text', array('attr'=>array('class'=>'m-wrap span12 numeric','placeholder'=>'sales price'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please add sales price'))
            )))
            ->add('content','textarea', array('attr'=>array('class'=>'no-resize span12','rows'=>8)))
            ->add('tag', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '-Choose a tags-',
                'property' => 'name',
                'multiple' => 'multiple',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('p');
	                $qb->where("p.ecommerceConfig ={$this->config->getId()}");
	                $qb->andWhere("p.status = 1");
                    $qb->andWhere($qb->expr()->like('p.type', ':type'));
                    $qb->setParameter('type','%Tag%');
                    $qb->orderBy("p.name","ASC");
                    return $qb;
                },
            ))
            ->add('promotion', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '-Choose a promotion-',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    $qb = $er->createQueryBuilder('p');
	                $qb->where("p.ecommerceConfig ={$this->config->getId()}");
	                $qb->andWhere("p.status = 1");
                    $qb->andWhere($qb->expr()->like('p.type', ':type'));
                    $qb->setParameter('type','%Promotion%');
                    $qb->orderBy("p.name","ASC");
                    return $qb;
                },
            ))
            ->add('itemColors', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemColor',
                'empty_value' => '-Choose a color-',
                'property' => 'name',
                'multiple' => 'multiple',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->andWhere("p.isValid = 1")
                        ->orderBy("p.name","ASC");
                },
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\Item'
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
        return $categoryTree = $this->em->getUseEcommerceItemCategory($this->config);
    }
}