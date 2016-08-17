<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SearchType extends AbstractType
{

    /** @var  SyndicateRepository */

    private $em;

    function __construct(SyndicateRepository $em)
    {
        $this->em = $em;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


            $builder

                ->add('name','text', array('attr'=>array('class'=>'m-wrap tooltips','placeholder'=>'search keyword....'),
                    'constraints' =>array()
                ))

                ->add('syndicate', 'entity', array(
                    'required'    => true,
                    'empty_value' => '---Select business sector---',
                    'attr'=>array('class'=>'select2'),
                    'class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate',
                    'choices'=> $this->SyndicateChoiceList(),
                    'choices_as_values' => true,
                    'choice_label' => 'nestedLabel',
                ))


                ->add('location', 'entity', array(
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please your location name required'))
                    ),
                    'required'    => false,
                    'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                    'empty_value' => '---Select Location ---',
                    'property' => 'name',
                    'attr'     =>array('id' => '' , 'class' => 'select2'),
                    'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('l')
                                ->andWhere("l.level = 2")
                                ->orderBy('l.name','ASC');
                        }
                ))

               ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\GlobalOption'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_globaloption';
    }

    /**
     * @return mixed
     */
    protected function SyndicateChoiceList()
    {
        return $syndicateTree = $this->em->getSyndicateOptionGroup();

    }
}


