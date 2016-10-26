<?php

namespace Appstore\Bundle\DomainUserBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * CustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerRepository extends EntityRepository
{
    public function findExistingCustomer($sales, $mobile)
    {
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$sales->getInventoryConfig()->getGlobalOption(),'mobile'=>$mobile));
        if($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName($mobile);
            $entity->setGlobalOption($sales->getInventoryConfig()->getGlobalOption());
            $em->persist($entity);
            $em->flush();
            return $entity;
        }
    }

    public function findWithSearch($globalOption,$data)
    {

        $qb = $this->createQueryBuilder('customer');
        $qb->where("customer.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->getQuery();
        return  $qb;

    }

    public function insertContactCustomer($globalOption,$data,$mobile='')
    {
        $em = $this->_em;
        $entity  ='';

        if(!empty($mobile)){
            $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile));
        }elseif(isset($data['email']) && $data['email'] !=""){
            $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' =>$globalOption, 'email' => $data['email']));
        }

        if(!empty($entity)){
            return $entity;
        }else {
            $entity = new Customer();
            if(isset($data['email']) && $data['email'] !=""){
                $entity->setEmail($data['email']);
            }
            if(!empty($mobile)) {
                $entity->setMobile($mobile);
            }
            if(isset($data['name']) && $data['name'] !=""){
                $entity->setName($data['name']);
            }
            $entity->setGlobalOption($globalOption);
            $entity->setCustomerType('contact');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }

    }

    public function insertSMSCustomer($data)
    {
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$data['globalOption'],'mobile' => $data['mobile']));
        if($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $globalOption = $this->_em->getRepository('SettingToolBundle:GlobalOption')->find($data['globalOption']);
            $entity->setMobile($data['mobile']);
            $entity->setName($data['name']);
            $entity->setGlobalOption($globalOption);
            $entity->setCustomerType('sms');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }

    }

    public function searchAutoComplete(GlobalOption $globalOption, $q, $type = 'NULL')
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        if(!empty($type)){
            $query->andWhere("e.customerType = :customerType");
            $query->setParameter('customerType', $type );
        }
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }
}
