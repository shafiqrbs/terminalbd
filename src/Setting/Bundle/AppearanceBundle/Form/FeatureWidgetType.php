<?php

namespace Setting\Bundle\AppearanceBundle\Form;

use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FeatureWidgetType extends AbstractType
{

    /** @var  CategoryRepository */
    private $category;

    private $globalOption;

    public function __construct(GlobalOption $globalOption, CategoryRepository $category)
    {
        $this->globalOption = $globalOption;
        $this->globalId = $globalOption->getId();
        $this->ecommerceConfig = $globalOption->getEcommerceConfig()->getId();
        $this->category = $category;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('position', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'empty_value' => '---Select Position---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select feature position'))
                ),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'header-top'                => 'Header Top',
                    'body-top'                  => 'Body Top',
                    'body-bottom'               => 'Body Bottom',
                    'sidebar-top'               => 'Sidebar Top',
                    'sidebar-bottom'            => 'Sidebar Bottom',
                    'footer'                    => 'Footer',
                ),
            ))
            ->add('page', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ContentBundle\Entity\Page',
                'empty_value' => '---Select Page---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption = $this->globalId")
                        ->orderBy('e.name','ASC');
                },
            ))
            ->add('module', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Module',
                'empty_value' => '---Select page module---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2'),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.isSingle = 1")
                        ->orderBy('e.name','ASC');
                },
            ))
            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))

            ->add('discount', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'empty_value' => '---Select Discount---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig =".$this->ecommerceConfig)
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('promotion', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '---Select Promotion---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        /*->andWhere('e.type IN (:type)')
                        ->setParameter('type', array('promotion'))*/
                        ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('tag', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Promotion',
                'empty_value' => '---Select Tag---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('jsFeature', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\AppearanceBundle\Entity\JsFeature',
                'empty_value' => '---Select Any Feature---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('content','textarea', array('attr'=>array('class'=>'span12 m-wrap','rows'=>4)))
            ->add('pageName', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12 select2 '),
                'empty_value' => '---Select E-commerce Page ---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select feature page'))
                ),
                'required'    => false,
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Home'              => 'Home',
                    'Product'           => 'Product',
                    'Brand'             => 'Brand',
                    'Category'          => 'Category',
                    'Promotion'         => 'Promotion',
                    'Tag'               => 'Tag',
                    'Discount'          => 'Discount'
                ),
            ))
            ->add('featureBrand')
            ->add('featureCategory')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\AppearanceBundle\Entity\FeatureWidget'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_appearancebundle_featurewidget';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->category->getUseInventoryItemCategory($this->globalOption->getInventoryConfig());

    }

}
