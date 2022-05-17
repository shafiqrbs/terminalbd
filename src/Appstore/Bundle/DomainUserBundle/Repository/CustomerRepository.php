<?php

namespace Appstore\Bundle\DomainUserBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Core\UserBundle\Entity\User;
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
        $name = $data['sales_online']['customer']['name'];
        $location = $data['sales_online']['customer']['location'];
        $address = $data['sales_online']['customer']['address'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'mobile' => $mobile));
        if($entity){
            return $entity;
        }else{
            $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setLocation($location);
            $entity->setAddress($address);
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }


    public function eCommerceCustomer(User $user,$data)
    {
        $em = $this->_em;
        $name = $data['name'];
        $location = $data['location'];
        $address = $data['address'];
        $email = $data['email'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption() ,'mobile' => $user->getId()));
        if($entity){
            return $entity;
        }else{

            $entity = new Customer();
            $entity->setMobile($user->getUsername());
            $entity->setName($name);
            $entity->setEmail($email);
            if($location){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            $entity->setAddress($address);
            $entity->setGlobalOption($user->getGlobalOption());
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }


    public function newExistingCustomerForSales($globalOption,$mobile,$data)
    {
        $em = $this->_em;
        $name = $data['customerName'];
        $address = isset($data['customerAddress']) ? $data['customerAddress']:'';
        $email = isset($data['customerEmail']) ? $data['customerEmail']:'';
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'mobile' => $mobile));
        if($entity){
            $entity->setAddress($address);
            if($email){ $entity->setEmail($email); }
            $em->flush($entity);
            return $entity;
        }else{
            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setAddress($address);
            if($email){$entity->setEmail($email); }
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }

    public function newExistingCustomerForHotel($globalOption,$mobile,$data)
    {
        $em = $this->_em;
        $namePrefix = isset($data['namePrefix']) ? $data['namePrefix']:array();
        $email = $data['email'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $address = $data['address'];
        $profession = $data['profession'];
        $organization = $data['organization'];
        $postalCode = $data['postalCode'];
        $remark = $data['remark'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'mobile' => $mobile));
        if($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $entity->setNamePrefix($namePrefix);
            $entity->setMobile($mobile);
            $entity->setEmail($email);
            $entity->setFirstName($firstName);
            $entity->setLastName($lastName);
            $entity->setAddress($address);
            $entity->setProfession($profession);
            $entity->setCompany($organization);
            $entity->setPostalCode($postalCode);
            $entity->setRemark($remark);
            $entity->setName($entity->getFirstName().' '.$entity->getLastName());
            $entity->setGlobalOption($globalOption);
            if(!empty($data['location'])){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($data['location']);
                $entity->setLocation($location);
            }
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

    public function findHmsExistingCustomerDiagnostic(GlobalOption $globalOption, $mobile,$patientId,$data)
    {
        $em = $this->_em;
        $customer = $data['customer'];
        $name = $customer['name'];
        $gender = $customer['gender'];
        $ageGroup = isset($customer['ageGroup']) ? $customer['ageGroup']:'';
        $age = $customer['age'];
        $ageType = $customer['ageType'];
        $address = isset($customer['address']) ? $customer['address']:'';
        $height = isset($customer['height']) ? $customer['height']:'';
        $width = isset($customer['width']) ? $customer['width']:'';
        $bloodPressure = isset($customer['bloodPressure']) ? $customer['bloodPressure']:'';
        $patient = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'customerId' => $patientId));
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'name' => $name ,'mobile' => $mobile,'age' => $age,'gender' => $gender));
        if($patient){
            return $entity = $patient;
        }elseif($entity){
            return $entity;
        }else{
            $entity = new Customer();
            $entity->setPatientId($patientId);
            $entity->setCustomerId($patientId);
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setGender($gender);
            $entity->setAge($age);
            $entity->setAgeGroup($ageGroup);
            $entity->setAgeType($ageType);
            if($address){
                $entity->setAddress($address);
            }
            if($height){
                $entity->setHeight($height);
            }
            if($width){
                $entity->setWeight($width);
            }
            if($bloodPressure){
                $entity->setBloodPressure($bloodPressure);
            }
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function updateExistingCustomer(GlobalOption $globalOption,Customer $entity, $mobile,$data)
    {
        $em = $this->_em;
        $name       = isset($data['name'])? $data['name']:'';
        $gender     = isset($data['gender'])? $data['gender']:'';
        $ageGroup   = isset($data['ageGroup'])? $data['ageGroup']:'';
        $age        = isset($data['age'])? $data['age']:'';
        $ageType    = isset($data['ageType'])? $data['ageType']:'';
        $location   = isset($data['location'])? $data['location']:'';
        $address       = isset($data['address'])? $data['address']:'';
        if(!empty($location)){
            $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
            $entity->setLocation($location);
        }
        $entity->setMobile($mobile);
        if(!empty($name)){
            $entity->setName($name);
        }
        if(!empty($gender)){
            $entity->setGender($gender);
        }
        if(!empty($age)){
            $entity->setAge($age);
        }
        if(!empty($ageGroup)){
            $entity->setAgeGroup($ageGroup);
        }
        if(!empty($ageType)){
            $entity->setAgeType($ageType);
        }
        if(!empty($address)){
            $entity->setAddress($address);
        }
        $em->persist($entity);
        $em->flush($entity);
        return $entity;

    }
    
    public function findWithSearch($globalOption,$data)
    {

        $qb = $this->createQueryBuilder('customer');
        $qb->where("customer.globalOption = :globalOption")->setParameter('globalOption', $globalOption);
        $qb->andWhere("customer.name != :name")->setParameter('name', 'Default');
        $qb->andWhere("customer.mobile IS NOT NULL");
        $qb->andWhere("customer.status =1");
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('customer.created','DESC');
        $qb->getQuery();
        return  $qb;

    }

    public function customerCount($globalOption,$id,$mobile)
    {

        $qb = $this->createQueryBuilder('customer');
        $qb->select('COUNT(customer.id) as customerCount');
        $qb->where("customer.globalOption = :globalOption")->setParameter('globalOption', $globalOption);
        $qb->andWhere("customer.mobile = :mobile")->setParameter('mobile', $mobile);
        $qb->andWhere("customer.id != :id")->setParameter('id', $id);
        $count = $qb->getQuery()->getSingleScalarResult();
        return  $count;

    }

    protected function handleSearchBetween($qb,$data)
    {
        if($data)
        {
            $mobile =    isset($data['mobile'])? $data['mobile'] :'';
            $customer =    isset($data['name'])? $data['name'] :'';
            $location =    isset($data['location'])? $data['location'] :'';
            $customerId =    isset($data['customerId'])? $data['customerId'] :'';
            $customerType =    isset($data['type'])? $data['type'] :'';
            $studentBatch =    isset($data['studentBatch'])? $data['studentBatch'] :'';
            $process =    isset($data['process'])? $data['process'] :'';
            $bloodGroup =    isset($data['bloodGroup'])? $data['bloodGroup'] :'';
            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
            if(!empty($data['keyword'])){
                $keyword = $data['keyword'];
                $qb->andWhere('customer.name LIKE :searchTerm OR customer.mobile LIKE :searchTerm');
                $qb->setParameter('searchTerm', '%'.strtolower($keyword).'%');
            }
            if (!empty($mobile)) {
                $qb->andWhere("customer.mobile LIKE :mobile");
                $qb->setParameter('mobile','%'. $mobile.'%');
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
            if (!empty($bloodGroup)) {
                $qb->andWhere("customer.bloodGroup = :bloodGroup");
                $qb->setParameter('bloodGroup',$bloodGroup);
            }
            if (!empty($customerId)) {
                $qb->andWhere("customer.customerId LIKE :customerId");
                $qb->setParameter('customerId','%'. $customerId.'%');
            }
            if (!empty($customerType)) {
                $qb->andWhere("customer.customerType = :type");
                $qb->setParameter('type',$customerType);
            }
            if (!empty($process)) {
                $qb->andWhere("customer.process = :process");
                $qb->setParameter('process',$process);
            }
            if (!empty($studentBatch)) {
                $qb->andWhere("customer.studentBatch = :studentBatch");
                $qb->setParameter('studentBatch',$studentBatch);
            }
            if (!empty($startDate) ) {
                $start = date('Y-m-d 00:00:00',strtotime($data['startDate']));
                $qb->andWhere("customer.dob >= :startDate");
                $qb->setParameter('startDate', $start);
            }
            if (!empty($endDate)) {
                $end = date('Y-m-d 23:59:59',strtotime($data['endDate']));
                $qb->andWhere("customer.dob <= :endDate");
                $qb->setParameter('endDate',$end);
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

    public function insertStudentMember(User $user,$data)
    {
        $em = $this->_em;
        $exist = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(), 'user' => $user->getId()));
        if(empty($exist)){
            $code = $data['country'];
            $entity = new Customer();
            $country = $this->_em->getRepository("SettingLocationBundle:Country")->find($code);
            $entity->setMobile($user->getUsername());
            $entity->setCountry($country);
            if(isset($data['name']) && $data['name'] !=""){
                $entity->setName($data['name']);
            }
            if(isset($data['email']) && $data['email'] !=""){
                $entity->setEmail($data['email']);
            }
            if(isset($data['facebookId']) && $data['facebookId'] !=""){
                $entity->setFacebookId($data['facebookId']);
            }
            if(isset($data['address']) && $data['address'] !=""){
                $entity->setAddress($data['address']);
            }
            $entity->setUser($user->getId());
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->setCustomerType('member');
            $em->persist($entity);
            $em->flush();
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
        $query->addSelect('CONCAT(e.mobile, \' - \', e.name) AS text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->orWhere($query->expr()->like("e.name", "'%$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->orderBy('e.name', 'ASC');
        $query->groupBy('e.mobile');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchMobileAutoComplete(GlobalOption $globalOption, $q, $type = 'NULL')
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.mobile as id');
        $query->addSelect('e.id as customer');
        $query->addSelect('CONCAT(e.mobile, \'-\', e.name) AS text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->orderBy('e.mobile', 'ASC');
        $query->groupBy('e.mobile,e.name');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }

    public function searchCustomerAutoComplete(GlobalOption $globalOption, $q, $type = 'NULL')
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.name as id');
        $query->addSelect('e.id as name');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.mobile", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->orderBy('e.name', 'ASC');
        $query->groupBy('e.mobile,e.name');
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

    public function studentBatchChoiceList()
    {
        $array = array(

            "Batch 60’62" => "Batch 60’62",
            "Batch 61’63" => "Batch 61’63",
            "Batch 62’64" => "Batch 62’64",
            "Batch 63’65" => "Batch 63’65",
            "Batch 64’66" => "Batch 64’66",
            "Batch 65’67" => "Batch 65’67",
            "Batch 66’68" => "Batch 66’68",
            "Batch 67’69" => "Batch 67’69",
            "Batch 68’70" => "Batch 68’70",
            "Batch 69’71" => "Batch 69’71",
            "Batch 70’72" => "Batch 70’72",
            "Batch 71’73" => "Batch 71’73",
            "Batch 72’74" => "Batch 72’74",
            "Batch 73’75" => "Batch 73’75",
            "Batch 74’76" => "Batch 74’76",
            "Batch 75’77" => "Batch 75’77",
            "Batch 76’78" => "Batch 76’78",
            "Batch 77’79" => "Batch 77’79",
            "Batch 78’80" => "Batch 78’80",
            "Batch 79’81" => "Batch 79’81",
            "Batch 80’82" => "Batch 80’82",
            "Batch 81’83" => "Batch 81’83",
            "Batch 82’84" => "Batch 82’84",
            "Batch 83’85" => "Batch 83’85",
            "Batch 84’86" => "Batch 84’86",
            "Batch 85’87" => "Batch 85’87",
            "Batch 86’88" => "Batch 86’88",
            "Batch 87’89" => "Batch 87’89",
            "Batch 89’91" => "Batch 89’91",
            "Batch 90’92" => "Batch 90’92",
            "Batch 91’93" => "Batch 91’93",
            "Batch 92’94" => "Batch 92’94",
            "Batch 93’95" => "Batch 93’95",
            "Batch 94’96" => "Batch 94’96",
            "Batch 95’97" => "Batch 95’97",
            "Batch 96’98" => "Batch 96’98",
            "Batch 97’99" => "Batch 97’99",
            "Batch 98’2000" => "Batch 98’2000",
            "Batch 99’2001" => "Batch 99’2001",
            "Batch 2000’02" => "Batch 2000’02",
            "Batch 01’03" => "Batch 01’03",
            "Batch 02’04" => "Batch 02’04",
            "Batch 03’05" => "Batch 03’05",
            "Batch 04’06" => "Batch 04’06",
            "Batch 05’07" => "Batch 05’07",
            "Batch 06’08" => "Batch 06’08",
            "Batch 07’09" => "Batch 07’09",
            "Batch 08’10" => "Batch 08’10",
            "Batch 09’11" => "Batch 09’11",
            "Batch 10’12" => "Batch 10’12",
            "Batch 11’13" => "Batch 11’13",
            "Batch 12’14" => "Batch 14’14",
            "Batch 13’15" => "Batch 13’15",
            "Batch 14’16" => "Batch 14’16",
            "Batch 15’17" => "Batch 15’17",
            "Batch 16’18" => "Batch 16’18",
            "Batch 17’19" => "Batch 17’19",
            "Batch 18’20" => "Batch 18’20",
            "Batch 19’21" => "Batch 19’21",
            "Batch 20’22" => "Batch 20’22",
            "Batch 21’23" => "Batch 21’23",
            "Batch 22’24" => "Batch 24’24",
            "Batch 23’25" => "Batch 23’25",
            "Batch 24’26" => "Batch 24’26",
            "Batch 25’27" => "Batch 2527",
            "Batch 26’28" => "Batch 26’28",
            "Batch 27’29" => "Batch 27’29",
            "Batch 28’30" => "Batch 28’30",
            "Batch 29’31" => "Batch 29’31",
            "Batch 30’32" => "Batch 30’32",
        );

        return $array;


    }

    public function batchYearChoiceList(){

        $array = array(
            "1 Year"=> "1 Year",
            "2 Years"=> "2 Years",
            "3 Years"=> "3 Years",
            "4 Years"=> "4 Years",
            "5 Years"=> "5 Years",
            "6 Years"=> "6 Years",
            "7 Years"=> "7 Years",
            "8 Years"=> "8 Years",
            "9 Years"=> "9 Years",
            "10 Years"=> "10 Years",
            "11 Years"=> "11 Years",
            "12 Years"=> "12 Years",
        );

        return $array;
    }

    public function bloodsChoiceList(){
        $array = array(
            "A+"=> "A+",
            "A-"=> "A-",
            "B+-"=> "B+",
            "B-"=> "B-",
            "O+"=> "O+",
            "O-"=> "O-",
            "AB+"=> "AB+",
            "AB-"=> "A-",
        );

        return $array;
    }


    public function getApiCustomer(GlobalOption $option)
    {
        $qb = $this->createQueryBuilder('customer');
        $qb->select('customer.id as customerId','customer.name as name','customer.mobile as mobile');
        $qb->where("customer.globalOption = :globalOption");
        $qb->andWhere("customer.mobile IS NOT NULL");
        $qb->setParameter('globalOption', $option->getId());
        $qb->orderBy('customer.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['customer_id']          = (int) $row['customerId'];
            $data[$key]['name']                 = $row['name'];
            $data[$key]['mobile']               = $row['mobile'];
        }

        return $data;

    }

    public function apiCreateCustomer(GlobalOption $global,$data)
    {
        $em = $this->_em;
        $mobile = trim($data['mobile']);
        $name = trim($data['name']);
        $address = trim($data['address']);
        $userId = trim($data['userId']);
        $openingBalance = floatval($data['openingBalance']);
        $email = $data['email'];
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $global ,'mobile' => $mobile));
        if($entity){
            return 'invalid';
        }else {
            $entity = new Customer();
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setEmail($email);
            $entity->setAddress($address);
            $entity->setGlobalOption($global);
            $em->persist($entity);
            $em->flush();
            if ($openingBalance > 0){
                $em->getRepository("AccountingBundle:AccountSales")->apiInsertOpeningBalance($entity,$userId,$openingBalance);
            }
            return 'success';
        }
    }

    public function apiUpdateCustomer(GlobalOption $globalOption,$data)
    {
        $em = $this->_em;
        $customerId = trim($data['customer']);
        $name = trim($data['name']);
        $address = trim($data['address']);
        $email = $data['email'];
        $customer = $this->find($customerId);
        $customer->setName($name);
        $customer->setAddress($address);
        $customer->setEmail($email);
        $em->persist($customer);
        $em->flush();
        return 'success';
    }

}
