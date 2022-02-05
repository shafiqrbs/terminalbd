<?php

namespace Appstore\Bundle\HumanResourceBundle\Repository;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayrollParticular;
use Appstore\Bundle\HumanResourceBundle\Entity\Payroll;
use Core\UserBundle\Entity\User;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * LeavePolicyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeePayrollRepository extends \Doctrine\ORM\EntityRepository
{


    public function getPayrollEmployee($option)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.employee','u');
        $qb->join('u.profile','p');
        $qb->leftJoin('p.designation','d');
        $qb->select('e.id as id');
        $qb->addSelect('d.name as designationName');
        $qb->addSelect('p.name as name','p.mobile as mobile','p.joiningDate');
        $qb->where("e.globalOption =".$option);
        $qb->andWhere('e.domainOwner = 2');
        $qb->andWhere('e.isDelete != 1');
        $qb->orderBy("p.name","ASC");
        $result = $qb->getQuery()->getResult();
        return $result;

    }

    public function getPayrollEmployeeGroup(Payroll $payroll)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.employee','u');
        $qb->join('e.profile','p');
        $qb->select('e');
        $qb->where("e.globalOption =".$payroll->getGlobalOption()->getId());
        if($payroll->getEmployeeType()){
            $qb->andWhere("p.employeeType =".$payroll->getEmployeeType());
        }
        if($payroll->getBranch()){
            $qb->andWhere("p.branches =".$payroll->getBranch()->getId());
        }
        if($payroll->getSalaryType()){
            $qb->andWhere("e.salaryType ='{$payroll->getSalaryType()}'");
        }
        if($payroll->isBonusApplicable() == 1){
            $qb->andWhere("e.bonusApplicable =1");
            $qb->andWhere("e.bonusAmount >= 0");
        }
        if($payroll->isArearApplicable() == 1){
            $qb->andWhere("e.arearAmount >= 0");
        }
        $qb->andWhere('u.domainOwner = 2');
        $qb->andWhere('u.isDelete != 1');
        $result = $qb->getQuery()->getResult();
        return $result;


    }

    public function userInsertUpdate(User $user)
    {

        $em = $this->_em;
        if($user->getEmployeePayroll()){

            return $user->getEmployeePayroll();

        }else{

            $entity = new EmployeePayroll();
            $entity->setEmployee($user);
            $entity->setProfile($user->getProfile());
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->setEmployeeName($user->getProfile()->getName());
            $em->persist($entity);
            $em->flush();
            return $entity;
        }
    }

    public function insertUpdateParticular(EmployeePayroll $payroll , $data)
    {
        $em = $this->_em;
        $basic = $payroll->getBasicAmount();

        foreach ($data['particular'] as $key => $value):

            if(!empty($data['unit'][$key])){
                $particular = $em->getRepository('HumanResourceBundle:PayrollSetting')->find($value);
                $exist = $em->getRepository('HumanResourceBundle:EmployeePayrollParticular')->findOneBy(array('employeePayroll' => $payroll,'particular' => $particular));
                if($exist){
                    $exist->setUnit($data['unit'][$key]);
                    if($data['unit'][$key] and $data['type'][$key] == 'Percentage'){
                        $amount = $this->calculateAmount($basic,$data['unit'][$key]);
                        $exist->setAmount($amount);
                    }else{
                        $exist->setAmount($data['unit'][$key]);
                    }
                    $exist->setType($data['type'][$key]);
                    $em->persist($exist);
                    $em->flush();

                }else{

                    $entity = new EmployeePayrollParticular();
                    $entity->setEmployeePayroll($payroll);
                    $entity->setParticular($particular);
                    $entity->setMode($particular->getMode());
                    $entity->setUnit($data['unit'][$key]);
                    if($data['unit'][$key] and $data['type'][$key] == 'Percentage'){
                        $amount = $this->calculateAmount($basic,$data['unit'][$key]);
                        $entity->setAmount($amount);
                    }else{
                        $entity->setAmount($data['unit'][$key]);
                    }
                    $entity->setType($data['type'][$key]);
                    $em->persist($entity);
                    $em->flush();
                }
            }
        endforeach;
    }

    private function calculateAmount($basic,$unit){

        $amount = (($basic * $unit)/100);
        return $amount;
    }

    public function insertUpdate(EmployeePayroll $payroll)
    {
        $allowance = $this->getAllowanceDeduction($payroll->getId(),'allowance');
        $deduction = $this->getAllowanceDeduction($payroll->getId(),'deduction');

        $em = $this->_em;
        $payroll->setAllowanceAmount($allowance);
        $payroll->setDeductionAmount($deduction);
        $amount = ($payroll->getBasicAmount() + $allowance);
        $payroll->setTotalAmount($amount);
        if($payroll->isBonusApplicable() == 1){
            $payroll->setBonusAmount($this->calculateAmount($payroll->getBasicAmount(),$payroll->getBonusPercentage()));
        }
        $payable = (($payroll->getTotalAmount() + $payroll->getArearAmount() + $payroll->getBonusAmount()) - ($deduction + $payroll->getLoanInstallment() + $payroll->getAdvanceAmount()));
        $payroll->setPayableAmount($payable);
        $em->persist($payroll);
        $em->flush();

    }

    public function getAllowanceDeduction( $payroll,$mode)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.employeePayrollParticulars','p');
        $qb->select('SUM(p.amount) as amount');
        $qb->where("e.id ={$payroll}");
        $qb->andWhere("p.mode ='{$mode}'");
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result['amount'];
    }

    public function getAllowanceDeductionParticular( $payroll,$mode)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.employeePayrollParticulars','p');
        $qb->join('p.particular','particular');
        $qb->select('particular.id as particularId','p.amount as amount');
        $qb->where("e.id ={$payroll}");
        $qb->andWhere("p.mode ='{$mode}'");
        $result = $qb->getQuery()->getArrayResult();
        $array = array();
        foreach ($result as $row):
            $array[$row['particularId']] = $row['amount'];
        endforeach;
        if(!empty($result)){
            return json_encode($array);
        }
        return false;

    }
}