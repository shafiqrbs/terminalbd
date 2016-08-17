<?php

namespace Core\UserBundle\Form;

use Core\UserBundle\Form\Type\ProfileType;
use Core\UserBundle\Form\Type\SignupProfileType;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Form\InitialOptionType;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class SignupType extends AbstractType
{

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
        $builder->add('profile', new SignupProfileType());
        $builder->add('globalOption', new InitialOptionType($this->em));

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User',
            'cascade_validation' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Core_userbundle_user';
    }
}