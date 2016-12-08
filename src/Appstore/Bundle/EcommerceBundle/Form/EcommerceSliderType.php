<?php

namespace Appstore\Bundle\EcommerceBundle\Form;

use Product\Bundle\ProductBundle\Entity\Category;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EcommerceSliderType extends AbstractType
{

    private $globalOption;

    /** @var  CategoryRepository */
    private $category;

    public function __construct(GlobalOption $globalOption, CategoryRepository $category)
    {
        $this->globalOption = $globalOption;

        $this->category = $category;

        $this->ecommerceConfig =  $this->globalOption->getEcommerceConfig()->getId();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'span12 m-wrap','placeholder'=>'Enter full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'span12 m-wrap')))

            ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'class' => 'ProductProductBundle:Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))

            ->add('discount', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\EcommerceBundle\Entity\Discount',
                'empty_value' => '---Select Discount---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12 '),
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
                'attr'=>array('class'=>'m-wrap span12 '),
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
                'attr'=>array('class'=>'m-wrap span12 '),
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.ecommerceConfig = $this->ecommerceConfig")
                        ->orderBy('e.name','ASC');
                },
            ))

            ->add('targetTo', 'choice', array(
                'attr'=>array('class'=>'span12  m-wrap targetTo'),
                'empty_value' => '---Select Target URL ---',
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array(
                    'Category'          => 'Category',
                    'Promotion'         => 'Promotion',
                    'Tag'               => 'Tag',
                    'Discount'          => 'Discount'
                ),
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\EcommerceBundle\Entity\EcommerceSlider'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_ecoomercebundle_ecommorceslider';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->category->getUseInventoryItemCategory($this->globalOption->getInventoryConfig());

    }
}
