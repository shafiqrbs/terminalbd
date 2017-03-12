<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Product\Bundle\ProductBundle\Entity\CollectionRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Repository\GlobalOptionRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceMenuType extends AbstractType
{

    /** @var  CategoryRepository */
    private $em;

    private $globalOption;


    function __construct(GlobalOption $globalOption ,CategoryRepository $em)
    {
        $this->em = $em;
        $this->globalOption = $globalOption;
        $this->ecommerceConfig = $globalOption->getEcommerceConfig()->getId();
        $this->inventoryConfig = $globalOption->getInventoryConfig();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter menu group name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('categories', 'entity', array(
                'required'    => true,
                'multiple'      =>true,
                'attr'=>array('class'=>'category form-control'),
                'class' => 'ProductProductBundle:Category',
                'property' => 'name',
                'choices'=> $this->categoryChoiceList()
            ))
            ->add('brands', 'entity', array(
                'required'      => true,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\InventoryBundle\Entity\ItemBrand',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('b')
                            ->andWhere("b.status = 1")
                            ->andWhere("b.inventoryConfig =".$this->inventoryConfig->getId())
                            ->orderBy('b.name','ASC');
                    },
            ))

            ->add('discounts', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig =".$this->ecommerceConfig)
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('promotions', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))
            ->add('tags', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        /*                        ->andWhere('e.type IN (:type)')
                                              ->setParameter('type', array_values(array('tag')))*/
                                               ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('features', 'entity', array(
                'required'    => false,
                'multiple'      =>true,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\Feature',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 multiselect '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption =". $this->globalOption->getId())
                        ->orderBy('e.name','ASC');
                },
            ))

          ;
    }



    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\EcommerceMenu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_ecommercemenu';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {
        return $categoryTree = $this->em->getUserCategoryOptionGroup($this->inventoryConfig);
    }

}
