<?php

namespace Appstore\Bundle\DomainUserBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
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
    public function checkDuplicateCustomer(GlobalOption $globalOption, $mobile)
    {
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile' => $mobile));
        if($entity) {
            return false;
        }else{
            return true;
        }

    }

    public function defaultCustomer($global)
    {

        $mobile = $global->getMobile();
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $global ,'mobile' => $mobile));
        if($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName('Default');
            $entity->setGlobalOption($global);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }

    public function newExistingCustomer($globalOption,$mobile,$data)
    {
        $em = $this->_em;
        $name = $data['customerName'];
        $address = $data['customerAddress'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'mobile' => $mobile));
        if($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setAddress($address);
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }

    public function newExistingRestaurantCustomer($globalOption,$mobile,$name)
    {
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'mobile' => $mobile));
        if($entity){
            $entity->setName($name);
            $em->flush($entity);
            return $entity;

        }else{

            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }

    public function findExistingCustomer(Sales $sales, $mobile)
    {
        $em = $this->_em;
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $sales->getInventoryConfig()->getGlobalOption(),'mobile'=>$mobile));
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

    public function findHmsExistingCustomer($globalOption, $mobile,$data)
    {
        $em = $this->_em;

        $name = $data['customer']['name'];
        $gender = $data['customer']['gender'];
        $age = $data['customer']['age'];
        $ageType = $data['customer']['ageType'];
        $location = $data['customer']['location'];
        $profession = $data['customer']['profession'];
        $maritalStatus = $data['customer']['maritalStatus'];
        $dob = $data['customer']['dob'];
        $fatherName = $data['customer']['fatherName'];
        $motherName = $data['customer']['motherName'];
        $nationality = $data['customer']['nationality'];
        $bloodGroup = $data['customer']['bloodGroup'];
        $address = $data['customer']['address'];
        $religion = $data['customer']['religion'];

        $alternativeContactPerson = $data['customer']['alternativeContactPerson'];
        $alternativeRelation = $data['customer']['alternativeRelation'];
        $alternativeContactMobile = $data['customer']['alternativeContactMobile'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'name' => $name ,'mobile' => $mobile,'age' => $age,'gender' => $gender));
        if($entity){
            return $entity;
        }else{

            $entity = new Customer();
            if(!empty($location)){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setGender($gender);
            $entity->setAge($age);
            $entity->setAgeType($ageType);
            $entity->setDob($dob);
            $entity->setFatherName($fatherName);
            $entity->setMotherName($motherName);
            $entity->setNationality($nationality);
            $entity->setReligion($religion);
            $entity->setBloodGroup($bloodGroup);
            $entity->setMaritalStatus($maritalStatus);
            $entity->setProfession($profession);
            $entity->setAddress($address);
            $entity->setAlternativeContactPerson($alternativeContactPerson);
            $entity->setAlternativeRelation($alternativeRelation);
            $entity->setAlternativeContactMobile($alternativeContactMobile);
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);

            return $entity;
        }

    }
    public function findHmsExistingCustomerDiagnostic($globalOption, $mobile,$data)
    {
        $em = $this->_em;

        $name = $data['customer']['name'];
        $gender = $data['customer']['gender'];
        $age = $data['customer']['age'];
        $ageType = $data['customer']['ageType'];
        $location = $data['customer']['location'];
        $address = $data['customer']['address'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'name' => $name ,'mobile' => $mobile,'age' => $age,'gender' => $gender));
        if($entity){
            return $entity;
        }else{

            $entity = new Customer();
            if(!empty($location)){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setGender($gender);
            $entity->setAge($age);
            $entity->setAgeType($ageType);
            $entity->setAddress($address);
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function findWithSearch($globalOption,$data)
    {

        $qb = $this->createQueryBuilder('customer');
        $qb->where("customer.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
/*        $qb->andWhere("customer.name != :name");
        $qb->setParameter('name', 'Default');*/
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('customer.created','DESC');
        $qb->getQuery();
        return  $qb;

    }

    protected function handleSearchBetween($qb,$data)
    {
        if(!empty($data))
        {

            $mobile =    isset($data['mobile'])? $data['mobile'] :'';
            $customer =    isset($data['name'])? $data['name'] :'';
            $location =    isset($data['location'])? $data['location'] :'';
            $customerId =    isset($data['customerId'])? $data['customerId'] :'';

            if (!empty($mobile)) {
                $qb->andWhere("customer.mobile = :mobile");
                $qb->setParameter('mobile', $mobile);
            }
            if (!empty($location)) {
                $qb->leftJoin('customer.location','l');
                $qb->andWhere("l.name = :location");
                $qb->setParameter('location', $location);
            }

            if (!empty($customer)) {
                $qb->andWhere("customer.name LIKE :name");
                $qb->setParameter('name','%'. $customer.'%');
            }
            if (!empty($customerId)) {
                $qb->andWhere("customer.customerId LIKE :customerId");
                $qb->setParameter('customerId','%'. $customerId.'%');
            }


        }

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
            return $data = array('customer' => $entity, 'status'=>'invalid');
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
            return $data = array('customer' => $entity, 'status'=>'valid');
        }

    }

    public function insertNewsLetterCustomer($globalOption,$data,$mobile='')
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
            $entity->setGlobalOption($globalOption);
            $entity->setCustomerType('news-letter');
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

        $query->select('e.mobile as id');
        $query->addSelect('e.id as customer');
        $query->addSelect('e.mobile as text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->orderBy('e.name', 'ASC');
        $query->groupBy('e.mobile');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoCompleteName(GlobalOption $globalOption, $q)
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.name as id');
        $query->addSelect('e.id as customer');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoCompleteCode(GlobalOption $globalOption, $q)
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.mobile as id');
        $query->addSelect('e.id as customer');
        $query->addSelect('e.customerId as text');
        //$query->addSelect('CONCAT(e.customerId, " - ", e.name) AS text');
        $query->where($query->expr()->like("e.customerId", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->orderBy('e.customerId', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function patientInsertUpdate($data,Invoice $invoice)
    {
        $em = $this->_em;
        $customer = $data['appstore_bundle_hospitalbundle_invoice']['customer'];
        $patient = $data['patient'];
        $option = $invoice->getHospitalConfig()->getGlobalOption();
        if($patient){
            $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=> $option ,'id' => $patient));
        }else{
            $location = $customer['location'];
            $entity = new Customer();
            if(!empty($location)){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            if($customer['mobile']){
                $entity->setMobile($customer['mobile']);
            }
            if($customer['name']){
                $entity->setName($customer['name']);
            }
            if($customer['gender']){
                $entity->setGender($customer['gender']);
            }
            if($customer['age']){
                $entity->setAge($customer['age']);
            }
            if($customer['ageType']){
                $entity->setAgeType($customer['ageType']);
            }
            if($customer['profession']){
                $entity->setProfession($customer['profession']);
            }
            if($customer['fatherName']){
                $entity->setFatherName($customer['fatherName']);
            }
            if($customer['motherName']){
                $entity->setMotherName($customer['motherName']);
            }
            if($customer['nationality']){
                $entity->setNationality($customer['nationality']);
            }
            if($customer['religion']){
                $entity->setReligion($customer['religion']);
            }
            if($customer['address']){
                $entity->setAddress($customer['address']);
            }
            if($customer['bloodGroup']){
                $entity->setBloodGroup($customer['bloodGroup']);
            }
            if($customer['motherName']){
                $entity->setMotherName($customer['motherName']);
            }
            if($customer['nationality']){
                $entity->setNationality($customer['nationality']);
            }
            if($customer['maritalStatus']){
                $entity->setMaritalStatus($customer['maritalStatus']);
            }
            if($customer['dob']){
                $entity->setDob($customer['dob']);
            }
            if($customer['alternativeRelation']){
                $entity->setAlternativeRelation($customer['alternativeRelation']);
            }
            if($customer['alternativeContactMobile']){
                $entity->setAlternativeContactMobile($customer['alternativeContactMobile']);
            }
            if($customer['alternativeContactPerson']){
                $entity->setAlternativeContactPerson($customer['alternativeContactPerson']);
            }


            $entity->setGlobalOption($option);
            $em->persist($entity);
            $em->flush($entity);
        }
        $em->getRepository('HospitalBundle:Invoice')->updatePatientInfo($invoice, $entity);
        return $entity;

    }




}
