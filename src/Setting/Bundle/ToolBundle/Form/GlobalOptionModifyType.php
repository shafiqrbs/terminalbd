<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ContentBundle\Form\ContactOpeningType;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Setting\Bundle\ToolBundle\Form\SiteSettingType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GlobalOptionModifyType extends AbstractType
{


    /** @var  LocationRepository */

    private $location;

    function __construct(LocationRepository $location)
    {
        $this->location = $location;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userID = (!empty($options['data'])) ? $options['data']->getId():0;
        $syndicateId = (!empty($options['data'])) ? $options['data']->getSyndicate()->getParent()->getId():0;

        if($userID > 0){

            parent::buildForm($builder, $options);
            $builder

                ->add('name','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Enter Name' , 'data-original-title' =>'Please enter name of organization' , 'data-trigger' => 'hover'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ))

                ->add('location', 'entity', array(
                    'required'    => false,
                    'empty_value' => '---Select Location---',
                    'attr'=>array('class'=>'select2 span12'),
                    'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                    'choices'=> $this->LocationChoiceList(),
                    'choices_as_values' => true,
                    'choice_label' => 'nestedLabel',
                ))
                ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 tooltips','placeholder'=>'Please enter public relational mobile no' , 'data-original-title' =>'Please enter public relational mobile no' , 'data-trigger' => 'hover'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ))
                /*->add('syndicate', 'entity', array(
                    'required'    => true,
                    'class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate',
                    'empty_value' => '---Select Syndicate ---',
                    'property' => 'name',
                    'attr'     =>array('id' => '' , 'class' => 'm-wrap placeholder-no-fix selectbox'),
                    'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('s')
                                ->andWhere("s.status = 1")
                                ->andWhere("s.parent is null")
                                ->orderBy('s.name','ASC');
                        },
                ))*/

                ->add('domain','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter domain name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ))
                ->add('subDomain','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter sub-domain name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ))

                ->add('email','text', array('attr'=>array('class'=>'m-wrap span12')))
                ->add('facebookPageUrl','text', array('attr'=>array('class'=>'m-wrap span12')))
                ->add('instagramPageUrl','text', array('attr'=>array('class'=>'m-wrap span12')))
                ->add('customizeDesign')
                ->add('twitterUrl','text', array('attr'=>array('class'=>'m-wrap span12')))
                ->add('googlePlus','text', array('attr'=>array('class'=>'m-wrap span12')))
                ->add('webMail','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter your web mail url')))
                ->add('smsIntegration')
                ->add('emailIntegration')
                ->add('facebookAds')
                ->add('facebookApps')
                ->add('promotion')
                ->add('googleAds')
                ->add('primaryNumber')
                ->add('status', 'choice', array(
                    'attr'=>array('class'=>'selectbox span12'),
                    'choices' => array(1 => 'Active','2' => 'In-active'),
                ))
                /*->add('callBackEmail')
                ->add('callBackContent','textarea', array('attr'=>array('class'=>' m-wrap span12','rows'=>4)))
                ->add('callBackNotify')
                ->add('leaveEmail')
                ->add('leaveContent','textarea', array('attr'=>array('class'=>' m-wrap span12','rows'=>4)))*/


            ;
            $builder->add('contactPage', new ContactOpeningType());
            $builder->add('templateCustomize', new TemplateSidebarType());
            $builder->add('siteSetting', new SiteSettingType($syndicateId));

        }else{

            $builder

                ->add('name','text', array('attr'=>array('class'=>'m-wrap tooltips','placeholder'=>'Enter Name' , 'data-original-title' =>'Please enter name of organization' , 'data-trigger' => 'hover'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                        new Length(array('max'=>200))
                    )
                ))

                ->add('syndicate', 'entity', array(
                'required'    => true,
                'class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate',
                'empty_value' => '---Select Syndicate ---',
                'property' => 'name',
                'attr'     =>array('id' => '' , 'class' => 'm-wrap placeholder-no-fix selectbox span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('s')
                            ->andWhere("s.status = 1")
                            ->andWhere("s.parent is null")
                            ->orderBy('s.name','ASC');
                    },
            ))
            ->add('status','checkbox', array(
                        'attr'=>array('class'=>'')
                    )
            );

        }


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

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
