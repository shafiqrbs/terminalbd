<?php

namespace Appstore\Bundle\ServiceBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServiceInvoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

	        ->add('assignBy', 'entity', array(
		        'required'    => true,
		        'class' => 'Core\UserBundle\Entity\User',
		        'property' => 'userFullName',
		        'attr'=>array('class'=>'span12 m-wrap select2'),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('u')
			                  ->where("u.isDelete != 1")
			                  ->andWhere("u.mode IS NULL")
			                  ->orderBy("u.username", "ASC");
		        }
	        ))
	        ->add('assuranceType', 'choice', array(
		        'required'    => false,
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'empty_value' => '---Choose a warning ---',
		        'choices' => array(
			        'AMC' => 'AMC',
			        'Warranty' => 'Warranty',
			        'Guarantee' => 'Guarantee',
			        'No-warranty' => 'No-warranty'
		        ),
	        ))
	        ->add('process', 'choice', array(
		        'required'    => false,
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'empty_value' => '---Choose a process ---',
		        'choices' => array(
			        'Created' => 'Created',
			        'In-Progress' => 'In-Progress',
			        'Hold' => 'Hold',
			        'Approved' => 'Approved',
			        'Done' => 'Done',
			        'Canceled' => 'Canceled'
		        ),
	        ))
	        ->add('urgency', 'choice', array(
		        'required'    => false,
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'empty_value' => '---Choose a urgency---',
		        'choices' => array(
			        'Emergency' => 'Emergency',
			        'Elective' => 'Elective',
			        'Urgent' => 'Urgent',
			        'Required' => 'Required'
		        ),
	        ))
	        ->add('serviceCost','text', array('attr'=>array('class'=>'m-wrap span12')))
	        ->add('totalCost','text', array('attr'=>array('class'=>'m-wrap span12', 'mexlength' => 4)))
	        ->add('accessoriesCost','text', array('attr'=>array('class'=>'m-wrap span12', 'mexlength' => 4)))
	        ->add('serviceType', 'choice', array(
		        'required'    => false,
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'empty_value' => '---Choose a service---',
		        'choices' => array(
			        'New Replace' => 'New Replace',
			        'Repair' => 'Repair',
			        'Servicing' => 'Servicing',
			        'Brand New' => 'Brand New',
			        ),
	        ))
	        ->add('serviceDescription','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>5)))
	        ->add('description','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3)))
	        ->add('totalServiceHour','text', array('attr'=>array('class'=>'m-wrap span12')))
	        ->add('serviceStatus', 'choice', array(
		        'required'    => false,
		        'attr'=>array('class'=>'span12 m-wrap'),
		        'empty_value' => 'Choose a service status',
		        'choices' => array(
			        'Done' => 'Done',
			        'Partial' => 'Partial',
			        'Temporary' => 'Temporary',
			        'Damage' => 'Damage',
		        ),
	        ))
	        ->add('responsiblePerson','text', array('attr'=>array('class'=>'m-wrap span12')))
	        ->add('responsibleMobile','text', array('attr'=>array('class'=>'m-wrap span12')))
	        ->add('initiationDate', 'date', array(
		        'widget' => 'single_text',
		        'placeholder' => array(
			        'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

		        ),
		        'format' => 'dd-MM-yyyy',
		        'attr' => array('class'=>'m-wrap span12 datePicker'),
		        'view_timezone' => 'Asia/Dhaka'))

	        ->add('resolvedDate', 'date', array(
		        'widget' => 'single_text',
		        'placeholder' => array(
			        'mm' => 'mm', 'dd' => 'dd','YY' => 'YY'

		        ),
		        'format' => 'dd-MM-yyyy',
		        'attr' => array('class'=>'m-wrap span12 datePicker'),
		        'view_timezone' => 'Asia/Dhaka'))
			;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\ServiceBundle\Entity\ServiceInvoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'serviceinvoice';
    }
}
