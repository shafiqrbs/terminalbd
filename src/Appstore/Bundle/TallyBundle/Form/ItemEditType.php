<?php

namespace Appstore\Bundle\TallyBundle\Form;

use Appstore\Bundle\TallyBundle\Entity\Category;;

use Appstore\Bundle\TallyBundle\Entity\TallyConfig;
use Appstore\Bundle\TallyBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotNull;

class ItemEditType extends AbstractType
{

    /** @var TallyConfig */

    public  $config;

    /** @var  CategoryRepository */
    private $em;

    function __construct(TallyConfig $config , CategoryRepository $em)
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
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 ','placeholder'=>'Enter product item name')))
            ->add('vatName','text', array('attr'=>array('class'=>'m-wrap span12 selectVatSearch','placeholder'=>'Enter vat name')))
            ->add('country', 'entity', array(
                'required'      => true,
                'expanded'      =>false,
                'placeholder' => 'Choose made in',
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name','ASC');
                },
            ))
            ->add('productUnit', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\ProductUnit',
                'property' => 'name',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please select required'))
                ),
                'empty_value' => '---Choose a item unit ---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where("p.status = 1")
                        ->orderBy("p.name","ASC");
                },
            ))

           ->add('category', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select parent category---',
                'attr'=>array('class'=>'category m-wrap span12 select2'),
                'class' => 'Appstore\Bundle\TallyBundle\Entity\Category',
                'property' => 'nestedLabel',
                'choices'=> $this->categoryChoiceList()
            ))
           ->add('brand', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TallyBundle\Entity\Brand',
                'empty_value' => 'Choose a brand',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->Where("b.status = 1")
                        ->andWhere("b.globalOption = {$this->config->getId()}");
                },
            ))
            ->add('vendor', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\AccountingBundle\Entity\AccountVendor',
                'empty_value' => 'Choose a vendor',
                'property' => 'companyName',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.globalOption = {$config->getGlobalOption()->getId()}");
                },
            ))
            ->add('productGroup', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TallyBundle\Entity\Setting',
                'empty_value' => 'Choose a setting',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.type = 'Product Group'");
                },
            ))
            ->add('itemWarning', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\TallyBundle\Entity\ItemWarning',
                'empty_value' => 'Choose a item warning',
                'property' => 'name',
                'attr'=>array('class'=>'span12 m-wrap'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1");
                },
            ))
            ->add('manufacture','text', array('attr'=>array('class'=>'m-wrap span12')))
	        ->add('itemPrefix','text', array('attr'=>array('class'=>'m-wrap span12', 'mexlength' => 4)))

	        ->add('warningLabel', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a warning label---',
                'choices' => array(
	                'AMC' => 'AMC',
                    'Warranty' => 'Warranty',
                    'Guarantee' => 'Guarantee',
	                'No-warranty' => 'No-warranty'
                ),
            ))
            ->add('serialFormat', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a Serial Format---',
                'choices' => array(
                    '2' => '2 Digit',
                    '3' => '3 Digit',
                    '4' => '4 Digit',
                    '5' => '5 Digit',
                ),
            ))
	        ->add('serialGeneration', 'choice', array(
                'required'    => false,
                'attr'=>array('class'=>'span12'),
                'empty_value' => '---Choose a serial generation---',
                'choices' => array(
                    'auto' => 'Auto',
                    'manual' => 'Manual'
                ),
            ))
            ->add('content','textarea', array('attr'=>array('class'=>'m-wrap span12','row'=>3)))
            ->add('file')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\TallyBundle\Entity\Item'
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
