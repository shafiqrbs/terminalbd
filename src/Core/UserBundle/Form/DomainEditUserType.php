<?php

namespace Core\UserBundle\Form;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class DomainEditUserType extends AbstractType
{

    /** @var  GlobalOption */
    private $globalOption;

    /** @var  LocationRepository */
    private $location;


    function __construct(GlobalOption $globalOption, LocationRepository $location)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter your valid email address'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter your email address')),
                        new Length(array('max'=>200))
                    ))
            )
            ->add('roles', 'choice', array(
                    'attr'=>array('class'=>'category form-control'),
                    'required'=>true,
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required'))
                    ),
                    'multiple'    => true,
                    'empty_data'  => null,
                    'choices'   => $this->getAccessRoleGroup())
            )



            ->add('enabled');
            $builder->add('profile', new DomainUserProfileType($this->globalOption,$this->location));

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


        $globalOption = $this->globalOption;
        $modules = $globalOption->getSiteSetting()->getAppModules();
        $arrSlugs = array();
        if (!empty($globalOption->getSiteSetting()) and !empty($modules)) {
            foreach ($globalOption->getSiteSetting()->getAppModules() as $mod) {
                if (!empty($mod->getModuleClass())) {
                    $arrSlugs[] = $mod->getSlug();
                }
            }
        }

        $array = array();

        $website = array('website');
        $result = array_intersect($arrSlugs, $website);
        if (!empty($result)) {

            $array['Website'] = array(
                'ROLE_DOMAIN_USER' => 'Domain User',
                'ROLE_DOMAIN_MANAGER' => 'Domain Manager'
            );
        }

        $inventory = array('inventory');
        $result = array_intersect($arrSlugs, $inventory);
        if (!empty($result)) {

            $array['Inventory'] = array(
                'ROLE_INVENTORY'                        => 'Inventory',
                'ROLE_DOMAIN_INVENTORY_PURCHASE'        => 'Inventory Purchase',
                'ROLE_DOMAIN_INVENTORY_SALES'           => 'Inventory Sales',
                'ROLE_DOMAIN_INVENTORY_CUSTOMER'        => 'Inventory Customer',
                'ROLE_DOMAIN_INVENTORY_SALES_POS'       => 'Inventory Sales Pos',
                'ROLE_DOMAIN_INVENTORY_SALES_ONLINE'    => 'Inventory Sales Online',
                'ROLE_DOMAIN_INVENTORY_SALES_GENERAL'   => 'Inventory Sales General',
                'ROLE_DOMAIN_INVENTORY_SALES_MANUAL'    => 'Inventory Sales Manual',
                'ROLE_DOMAIN_INVENTORY_APPROVAL'        => 'Inventory Approval',
                'ROLE_DOMAIN_INVENTORY_STOCK'           => 'Inventory Stock',
                'ROLE_DOMAIN_INVENTORY_REPORT'          => 'Inventory Report',
                'ROLE_DOMAIN_INVENTORY_BRANCH'          => 'Inventory Branch',
                'ROLE_DOMAIN_INVENTORY_CONFIG'          => 'Inventory Config',
            );
        }

        $accounting = array('accounting');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {

            $array['Accounting'] = array(
                'ROLE_ACCOUNTING'                           => 'Accounting',
                'ROLE_DOMAIN_ACCOUNTING_EXPENDITURE'        => 'Accounting Expenditure',
                'ROLE_DOMAIN_ACCOUNTING_PURCHASE'           => 'Accounting Purchase',
                'ROLE_DOMAIN_ACCOUNTING_SALES'              => 'Accounting Sales',
                'ROLE_DOMAIN_ACCOUNTING_ECOMMERCE'          => 'Accounting Online Sales',
                'ROLE_DOMAIN_ACCOUNTING_PETTY_CASH'         => 'Accounting Petty Cash',
                'ROLE_DOMAIN_ACCOUNTING_JOURNAL'            => 'Accounting Journal',
                'ROLE_DOMAIN_ACCOUNTING_BANK'               => 'Accounting Bank & Mobile',
                'ROLE_DOMAIN_FINANCE_APPROVAL'              => 'Accounting Approval',
                'ROLE_DOMAIN_ACCOUNTING_TRANSACTION'        => 'Accounting Transaction',
                'ROLE_DOMAIN_ACCOUNTING_CONFIG'             => 'Accounting Config',
                'ROLE_DOMAIN_ACCOUNTING_REPORT'             => 'Accounting Report',
            );
        }

        $payroll = array('payroll');
        $result = array_intersect($arrSlugs, $payroll);
        if (!empty($result)) {

            $array['HR & Payroll'] = array(
                'ROLE_HR'                                   => 'Human Resource',
                'ROLE_HR_BRANCH'                            => 'Branch',
                'ROLE_PAYROLL'                              => 'Payroll',
                'ROLE_PAYROLL_SALARY'                       => 'Payroll Salary',
                'ROLE_PAYROLL_APPROVAL'                     => 'Payroll Approval',
                'ROLE_PAYROLL_REPORT'                       => 'Payroll Report',
            );
        }

        $ecommerce = array('e-commerce');
        $result = array_intersect($arrSlugs, $ecommerce);
        if (!empty($result)) {

            $array['E-commerce'] = array(
                'ROLE_ECOMMERCE'                            => 'E-commerce',
                'ROLE_DOMAIN_ECOMMERCE_PRODUCT'             => 'E-commerce Product',
                'ROLE_DOMAIN_ECOMMERCE_ORDER'               => 'E-commerce Order',
                'ROLE_DOMAIN_ECOMMERCE_SETTING'             => 'E-commerce Setting',
            );
        }
        return $array;
    }
}