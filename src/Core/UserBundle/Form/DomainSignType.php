<?php

namespace Core\UserBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class DomainSignType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', 'choice', array(
                'attr'=>array('class'=>'m-wrap span12  check-list'),
                'required'=>true,
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required'))
                ),
                'multiple'    => true,
                'expanded'  => true,
                'empty_data'  => null,
                'choices'   => $this->getAccessRoleGroup())
            )

            ->add('username','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off' ,'placeholder'=>'Enter your user name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your user name')),
                        new Length(array('max'=>200))
                    ))
            )
            ->add('enabled')
            ->add('plainPassword', 'repeated', array(
                'attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off'),
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter your valid email address'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your email address')),
                        new Length(array('max'=>200))
                    ))
            );
            $builder->add('profile', new DomainProfileType());

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Core_userbundle_user';
    }

    public function getAccessRoleGroup(){


        $array = array(
            'Domain'=> array(
                'ROLE_DOMAIN_USER'  => 'Domain User',
                'ROLE_DOMAIN_MANAGER' => 'Domain Manager'
            ),
            'Inventory'=> array(
              'ROLE_DOMAIN_INVENTORY_SALES' => 'Inventory Sales/Delivery',
              'ROLE_DOMAIN_INVENTORY_PURCHASE' => 'Inventory Purchase/Receive',
              'ROLE_DOMAIN_INVENTORY_MANAGER' => 'Inventory Manager',
              ),

        );

        return $array;

    //    return $array= array('ROLE_DOMAIN_USER' => 'User','ROLE_DOMAIN_INVENTORY_SALES' => 'Inventory Sales/Delivery','ROLE_DOMAIN_INVENTORY_PURCHASE' => 'Inventory Purchase/Receive',  'ROLE_DOMAIN_MANAGER' => 'Domain Manager', 'ROLE_DOMAIN_INVENTORY_MANAGER' => 'Inventory Manager');


    }
}