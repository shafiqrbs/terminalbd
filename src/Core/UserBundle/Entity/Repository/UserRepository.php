<?php

namespace Core\UserBundle\Entity\Repository;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->findAll();
    }

    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function update($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
        return $this->_em;
    }

    public function searchAutoComplete($q, GlobalOption $globalOption)
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.username as id');
        $query->addSelect('e.username as text');
        $query->where($query->expr()->like("e.username", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.username', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    /**
     * @param array $criteria
     * @return array
     */
    public function getEntityByIdAndStatusCriteria(array $criteria)
    {
        if ( $criteria['username']) {
            return $this->createQueryBuilder('e')
                ->andWhere('e.username = :username')
                ->setParameter('username', $criteria['username'])
                ->getQuery()
                ->getResult();
        }

        return [];
    }

    public function checkExistingUser($mobile)
    {
        return (boolean)$this->createQueryBuilder('u')
            ->andWhere('u.username = :user')
            ->setParameter('user', $mobile)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAccessRoleGroup(GlobalOption $globalOption){


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


        $inventory = array('inventory');
        $result = array_intersect($arrSlugs, $inventory);
        if (!empty($result)) {

            $array['Inventory'] = array(
                'ROLE_INVENTORY'                                    => 'Inventory',
                'ROLE_DOMAIN_INVENTORY_SALES'                       => 'Inventory Sales',
                'ROLE_DOMAIN_INVENTORY_DUE'                         => 'Inventory Due Receive',
                'ROLE_DOMAIN_INVENTORY'                             => 'Inventory Domain',
                'ROLE_DOMAIN_INVENTORY_PURCHASE'                    => 'Inventory Purchase',
                'ROLE_DOMAIN_INVENTORY_CUSTOMER'                    => 'Inventory Customer',
                'ROLE_DOMAIN_INVENTORY_APPROVAL'                    => 'Inventory Approval',
                'ROLE_DOMAIN_INVENTORY_REVERSE'                     => 'Purchase/Sales Reverse',
                'ROLE_DOMAIN_INVENTORY_STOCK'                       => 'Inventory Stock',
                'ROLE_DOMAIN_INVENTORY_REPORT'                      => 'Inventory Report',
                'ROLE_DOMAIN_INVENTORY_BRANCH'                      => 'Inventory Branch',
                'ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER'              => 'Inventory Branch Manager',
                'ROLE_DOMAIN_INVENTORY_MANAGER'                     => 'Inventory Manager',
                'ROLE_DOMAIN_INVENTORY_CONFIG'                      => 'Inventory Config',
                'ROLE_DOMAIN_INVENTORY_ADMIN'                       => 'Inventory Admin',
            );
        }

        $accounting = array('accounting');
        $result = array_intersect($arrSlugs, $accounting);
        if (!empty($result)) {

            $array['Accounting'] = array(
                'ROLE_ACCOUNTING'                           => 'Accounting',
                'ROLE_DOMAIN_ACCOUNTING_EXPENDITURE'        => 'Accounting Expenditure',
                'ROLE_DOMAIN_ACCOUNTING_PURCHASE'           => 'Accounting Purchase',
                'ROLE_DOMAIN_ACCOUNTING_EXPENDITURE_PURCHASE'           => 'Accounting Expenditure Purchase',
                'ROLE_DOMAIN_ACCOUNTING_SALES_ADJUSTMENT'           => 'Accounting Cash Adjustment',
                'ROLE_DOMAIN_ACCOUNTING_RECONCILIATION'           => 'Accounting Cash Reconciliation',
                'ROLE_DOMAIN_ACCOUNTING_SALES'              => 'Accounting Sales',
                'ROLE_DOMAIN_ACCOUNTING_ECOMMERCE'          => 'Accounting Online Sales',
                'ROLE_DOMAIN_ACCOUNTING_PETTY_CASH'         => 'Accounting Petty Cash',
                'ROLE_DOMAIN_ACCOUNTING_JOURNAL'            => 'Accounting Journal',
                'ROLE_DOMAIN_ACCOUNTING_BANK'               => 'Accounting Bank & Mobile',
                'ROLE_DOMAIN_FINANCE_APPROVAL'              => 'Accounting Approval',
                'ROLE_DOMAIN_ACCOUNTING_TRANSACTION'        => 'Accounting Transaction',
                'ROLE_DOMAIN_ACCOUNTING_CONFIG'             => 'Accounting Config',
                'ROLE_DOMAIN_ACCOUNTING_REPORT'             => 'Accounting Report',
                'ROLE_DOMAIN_ACCOUNT_REVERSE'               => 'Accounting Reverse',
                'ROLE_DOMAIN_ACCOUNTING'                    => 'Accounting Admin',
            );
        }

        $payroll = array('payroll');
        $result = array_intersect($arrSlugs, $payroll);
        if (!empty($result)) {

            $array['HR & Payroll'] = array(
                'ROLE_HR'                                   => 'Human Resource',
                'ROLE_HR_EMPLOYEE'                          => 'HR Employee',
                'ROLE_HR_ATTENDANCE'                        => 'HR Attendance',
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

        $business = array('business');
        $result = array_intersect($arrSlugs, $business);
        if (!empty($result)) {

            $array['Business'] = array(
                'ROLE_BUSINESS'                             => 'Business',
                'ROLE_BUSINESS_INVOICE'                     => 'Business Invoice',
                'ROLE_BUSINESS_PURCHASE'                    => 'Business Purchase',
                'ROLE_BUSINESS_STOCK'                       => 'Business Stock',
                'ROLE_BUSINESS_INVOICE_REVERSE'             => 'Business Invoice Reverse',
                'ROLE_BUSINESS_PURCHASE_REVERSE'            => 'Business Purchase Reverse',
                'ROLE_BUSINESS_REPORT'                      => 'Business Reports',
                'ROLE_BUSINESS_MANAGER'                     => 'Business Manager',
            );
        }

		$reservation = array('hotel');
        $result = array_intersect($arrSlugs, $reservation);
        if (!empty($result)) {

            $array['Hotel & Restaurant'] = array(
                'ROLE_HOTEL'                             => 'Hotel & Reservation',
                'ROLE_HOTEL_INVOICE'                     => 'Invoice',
                'ROLE_HOTEL_PURCHASE'                    => 'Purchase',
                'ROLE_HOTEL_STOCK'                       => 'Stock',
                'ROLE_HOTEL_MANAGER'                     => 'Manager',
                'ROLE_HOTEL_REPORTS'                     => 'Reports',
            );
        }


        $hms = array('hms');
        $result = array_intersect($arrSlugs, $hms);
        if (!empty($result)) {

            $array['HMS'] = array(
                'ROLE_HOSPITAL'                              => 'Hms',
                'ROLE_DOMAIN_HOSPITAL_ADMISSION'             => 'Hms Patient Admission',
                'ROLE_DOMAIN_HOSPITAL_MANAGER'               => 'Hms Manager',
                'ROLE_DOMAIN_HOSPITAL_COMMISSION'            => 'Hms Commission',
                'ROLE_DOMAIN_HOSPITAL_OPERATOR'              => 'Hms Operator',
                'ROLE_DOMAIN_HOSPITAL_LAB'                   => 'Hms Lab Assistant',
                'ROLE_DOMAIN_HOSPITAL_DOCTOR'                => 'Hms Doctor',
                'ROLE_DOMAIN_HOSPITAL_MASTERDATA'            => 'Hms Master Data',
                'ROLE_DOMAIN_HOSPITAL_ADMIN'                 => 'Hms Admin',
                'ROLE_DOMAIN_HOSPITAL_PURCHASE'              => 'Hms Purchase',
                'ROLE_DOMAIN_HOSPITAL_STOCK'                 => 'Hms Issue',
                'ROLE_DOMAIN_HOSPITAL_REPORT'                => 'Hms Report',
                'ROLE_DOMAIN_HOSPITAL_CONFIG'                => 'Hms Config',
            );
        }

        $restaurant = array('restaurant');
        $result = array_intersect($arrSlugs, $restaurant);
        if (!empty($result)) {
            $array['RESTAURANT'] = array(
                'ROLE_RESTAURANT'                           => 'Restaurant',
                'ROLE_DOMAIN_RESTAURANT'                    => 'Restaurant Operator',
                'ROLE_DOMAIN_RESTAURANT_MANAGER'            => 'Restaurant Manager',
                'ROLE_DOMAIN_RESTAURANT_ADMIN'              => 'Restaurant Admin',
            );
        }

        $dms = array('dms');
        $result = array_intersect($arrSlugs, $dms);
        if (!empty($result)) {
            $array['DMS'] = array(
                'ROLE_DMS'                           => 'Dental',
                'ROLE_DMS_LAB'                       => 'Dental Lab',
                'ROLE_DMS_DOCTOR'                    => 'Dental Doctor',
                'ROLE_DMS_MANAGER'                   => 'Dental Manager',
                'ROLE_DMS_ADMIN'                     => 'Dental Admin',
            );
        }

        $miss = array('miss');
        $result = array_intersect($arrSlugs, $miss);
        if (!empty($result)) {
            $array['Medicine'] = array(
                'ROLE_MEDICINE'                                  => 'Medicine',
                'ROLE_MEDICINE_SALES'                            => 'Medicine Sales',
                'ROLE_MEDICINE_PURCHASE'                         => 'Medicine Purchase',
                'ROLE_MEDICINE_STOCK'                            => 'Medicine Stock',
                'ROLE_MEDICINE_MANAGER'                          => 'Medicine Manager',
                'ROLE_MEDICINE_REVERSE'                          => 'Medicine Reverse',
                'ROLE_MEDICINE_REPORT'                           => 'Medicine Report',
                'ROLE_MEDICINE_ADMIN'                            => 'Medicine Admin',
            );
        }

        $dms = array('dps');
        $result = array_intersect($arrSlugs, $dms);
        if (!empty($result)) {
            $array['DMS'] = array(
                'ROLE_DPS'                                      => 'Doctor Prescription',
                'ROLE_DPS_DOCTOR'                               => 'Doctor',
                'ROLE_DPS_ADMIN'                                => 'Doctor Admin',
            );
        }

        $dms = array('election');
        $result = array_intersect($arrSlugs, $dms);
        if (!empty($result)) {
            $array['ELECTION'] = array(
                'ROLE_ELECTION'                        => 'Election',
                'ROLE_ELECTION_OPERATOR'               => 'Operator',
                'ROLE_ELECTION_MANAGER'                => 'Manager',
                'ROLE_ELECTION_ADMIN'                  => 'Admin',
            );
        }

        $array['Customer'] = array(
            'ROLE_CRM'                  => 'Customer',
            'ROLE_CUSTOMER_REHAB'       => 'Rehab Patient',
            'ROLE_CRM_MANAGER'          => 'Manage Customer ',
        );

	    $website = array('website');
	    $result = array_intersect($arrSlugs, $website);
	    if (!empty($result)) {

		    $array['Website'] = array(
			    'ROLE_WEBSITE'                  => 'Website',
			    'ROLE_DOMAIN_WEBSITE_MANAGER'   => 'Website Manager',
			    'ROLE_DOMAIN_WEBSITE_SETTING'   => 'Website Setting',
			    'ROLE_DOMAIN_WEBSITE'           => 'Website Admin',
		    );
	    }
	    $array['SMS'] = array(
            'ROLE_SMS'                                          => 'Sms/E-mail',
            'ROLE_SMS_MANAGER'                                  => 'Sms/E-mail Manager',
            'ROLE_SMS_CONFIG'                                   => 'SMS/E-mail Setup',
            'ROLE_SMS_BULK'                                     => 'SMS Bulk',

        );
        $array['Android Apps'] = array(
            'ROLE_MANAGER'                                   => 'Manager',
            'ROLE_PURCHASE'                                  => 'Purchase',
            'ROLE_SALES'                                     => 'Sales',
            'ROLE_EXPENSE'                                   => 'Expense',
            'ROLE_STOCK'                                     => 'Stock',
        );


        return $array;
    }

    public function getAndroidRoleGroup(){

        $array = array();
        $array['Android Apps'] = array(
            'ROLE_MANAGER'                                   => 'Manager',
            'ROLE_PURCHASE'                                  => 'Purchase',
            'ROLE_SALES'                                     => 'Sales',
            'ROLE_EXPENSE'                                   => 'Expense',
            'ROLE_STOCK'                                     => 'Stock',
        );


        return $array;
    }

    public function getEmployees(GlobalOption $option, $data = [])
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.profile','p');
        $qb->leftJoin('p.location','l');
        $qb->leftJoin('e.employeePayroll','ep');
        $qb->leftJoin('p.designation','d');
        $qb->select('e.id as id','e.username as username');
        $qb->addSelect('d.name as designationName');
        $qb->addSelect('l.name as locationName');
        $qb->addSelect('p.name as name','p.mobile as mobile','p.address as address','p.employeeType as employeeType','p.joiningDate as joiningDate','p.userGroup as userGroup');
        $qb->addSelect('ep.basicAmount as basicAmount','ep.allowanceAmount as allowance','ep.deductionAmount as deduction','ep.loanAmount as loan','ep.advanceAmount as advance','ep.arearAmount as arear','ep.salaryType as salaryType','ep.totalAmount as total','ep.payableAmount as payable');
        $qb->where("e.globalOption =".$option->getId());
        $qb->andWhere('e.domainOwner = 2');
        $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("p.name","ASC");
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function getEmployeeEntities(GlobalOption $option)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption =".$option->getId());
        $qb->andWhere('e.domainOwner = 2');
        $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("e.username","ASC");
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function insertAccountUser(GlobalOption $option,$mobile,$data)
    {
        $user = new User();
        $em = $this->_em;
        if(empty($data['profile']['email'])){
            $email = $mobile."@gmail.com";
        }else{
            $email = $data['profile']['email'];
        }
        $email = $mobile."@gmail.com";
        $user->setUsername($mobile);
        $user->setEmail($email);
        $user->setPassword('*4848#');
        $user->setUserGroup('account');
        $user->setGlobalOption($option);
        $user->setDomainOwner(2);
        $em->persist($user);
        $em->flush();
        return $user;

    }

    public function getCustomers(GlobalOption $option){

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.profile','p');
        $qb->select('e.username as username','e.email as email', 'e.id as id', 'e.appPassword as appPassword', 'e.appRoles as appRoles');
        $qb->addSelect('p.name as fullName');
        $qb->andWhere("e.globalOption =".$option->getId());
        $qb->andWhere('e.domainOwner = 2');
        $qb->andWhere('e.enabled = 1');
        $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("p.name","ASC");
        $result = $qb->getQuery()->getArrayResult();

        foreach($result as $key => $row){

            $data[$key]['user_id'] = (int) $row['id'];
            $data[$key]['username'] = $row['username'];
            $data[$key]['fullName'] = $row['fullName'];
            $data[$key]['email'] = $row['email'];
            $data[$key]['password'] = $row['appPassword'];
            $data[$key]['roles'] = unserialize(serialize($row['appRoles']));

        }
        return $data;

    }




}
