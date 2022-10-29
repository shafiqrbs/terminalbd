<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Repository;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoiceParticular;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService;
use Appstore\Bundle\HospitalBundle\Entity\Service;
use Doctrine\ORM\EntityRepository;


/**
 * DpsParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DpsServiceRepository extends EntityRepository
{

    public function setListOrdering($fieldName,$data)
    {
        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();

        foreach ($data as $key => $value){
            $qb->update('DoctorPrescriptionBundle:DpsService', 'mg')
                ->set('mg.'.$fieldName, $i)
                ->where('mg.id = :id')
                ->setParameter('id', $value)
                ->getQuery()
                ->execute();
            $i++;
        }
    }

    public function prescriptionServiceUpdate(DpsConfig $entity,$data)
    {
        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $this->removeServicePreviousCheck($entity);
        if(isset($data['serviceKey']) and !empty($data['serviceKey'])){
            foreach ($data['serviceKey'] as $key => $value) {
                /* @var $entity DpsService */
                $entity= $em->getRepository('DoctorPrescriptionBundle:DpsService')->find($value);
                $entity->setServicePosition($data['servicePosition'][$key]);
                $entity->setServiceHeight($data['serviceHeight'][$key]);
                $em->flush($entity);
            }

            foreach ($data['serviceShow'] as $key => $value) {
                /* @var $entity DpsService */
                $entity= $em->getRepository('DoctorPrescriptionBundle:DpsService')->find($value);
                $entity->setServiceShow(1);
                $em->flush($entity);

            }
            foreach ($data['serviceHeaderShow'] as $key => $value) {
                /* @var $entity DpsService */
                $entity= $em->getRepository('DoctorPrescriptionBundle:DpsService')->find($value);
                $entity->setServiceHeaderShow(1);
                $em->flush($entity);

            }
        }


    }

    public function removeServicePreviousCheck(DpsConfig $config)
    {
        $em = $this->_em;
        $update = $em->createQuery("UPDATE DoctorPrescriptionBundle:DpsService e SET e.serviceShow = 'NULL' , e.serviceHeight = 'NULL' , e.servicePosition = 'NULL' , e.serviceHeaderShow = 'NULL' WHERE e.status = 1 AND e.dpsConfig=".$config->getId());
        $update->execute();
    }

    public function setPrescriptionServiceOrdering($data)
    {
        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        foreach ($data as $key => $value){
            $qb->update('DoctorPrescriptionBundle:DpsService', 'mg')
                ->set('mg.serviceSorting', $i)
                ->where('mg.id = :id')
                ->setParameter('id', $value)
                ->getQuery()
                ->execute();
            $i++;
        }
    }

    public function getServiceLists(DpsConfig $config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.doctorService is null');
        $qb->andWhere('e.dpsConfig = :config');
        $qb->andWhere("e.serviceFormat !='other-service'");
        $qb->setParameter('config',$config);
        $qb->orderBy('e.sorting','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getServiceForPrescription(DpsConfig $config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status =1');
        $qb->andWhere('e.dpsConfig = :config');
        $qb->setParameter('config',$config);
        $qb->orderBy('e.serviceSorting','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;
    }


    public function findWithSearch($config,$service, $data){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $department = isset($data['department'])? $data['department'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dpsConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.service = :service')->setParameter('service', $service) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if(!empty($category)){
            $qb->andWhere("e.category = :category");
            $qb->setParameter('category', $category);
        }
        if(!empty($department)){
            $qb->andWhere("e.department = :department");
            $qb->setParameter('department', $department);
        }
        $qb->orderBy('e.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function getFindWithParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->where('e.dpsConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getResult();
        return  $qb;
    }

    public function getServices($config,$services){


        $particulars = $this->getServiceWithParticular($config,$services);

        $data = '';
        $service = '';
        foreach ($particulars as $particular) {
            if ($service != $particular['serviceName']) {
                if ($service != '') {
                    $data .= '</optgroup>';
                }
                $data .= '<optgroup label="' .ucfirst($particular['serviceName']) . '">';
            }
            if ($particular['serviceCode'] != '04'){
                $data .= '<option value="/dps/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['minimumPrice'] .' - '.$particular['price'].'</option>';
            }else{
                $data .= '<option value="/dps/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])) . ' - Tk. ' . $particular['minimumPrice'] .' - '.$particular['price'].'</option>';
            }
            $service = $particular['serviceName'];
        }
        if ($service != '') {
            $data .= '</optgroup>';
        }
        return $data ;

    }


    public function getServiceWithParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.dpsConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values($services))
            ->orderBy('e.service','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function getMedicineParticular($hospital){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.dpsConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process',array_values(array(4)))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }
    public function getAccessoriesParticular($hospital){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.dpsConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:process)')
            ->setParameter('process',array_values(array(8)))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function findDpsExistingCustomer($hospital, $mobile,$data)
    {
        $em = $this->_em;

        $name = $data['referredDoctor']['name'];
        $department = $data['referredDoctor']['department'];
        $location = $data['referredDoctor']['location'];
        $address = $data['referredDoctor']['address'];
        $entity = $em->getRepository('DoctorPrescriptionBundle:Particular')->findOneBy(array('dpsConfig' => $hospital ,'service' => 6 ,'mobile' => $mobile));
        if($entity){

            return $entity;

        }else{

            $entity = new Particular();
            if(!empty($location)){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            if(!empty($department)){
                $department = $em->getRepository('DoctorPrescriptionBundle:DpsCategory')->find($department);
                $entity->setDepartment($department);
            }
            $entity->setService($em->getRepository('DoctorPrescriptionBundle:Service')->find(6));
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setAddress($address);
            $entity->setDpsConfig($hospital);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }

    public function getPurchaseUpdateQnt(DpsPurchase $purchase){

        $em = $this->_em;

        /** @var DpsPurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var Particular  $particular */

            $particular = $purchaseItem->getParticular();
            
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function insertAccessories(Invoice $invoice){

        $em = $this->_em;

        $em = $this->_em;
        /** @var InvoiceParticular $item */
        if(!empty($invoice->getInvoiceParticulars())){
            foreach($invoice->getInvoiceParticulars() as $item ){
                /** @var Particular  $particular */
                $particular = $item->getParticular();
                if( $particular->getService()->getId() == 4 ){
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }
    }

    public function getSalesUpdateQnt(Invoice $invoice){

        $em = $this->_em;

        /** @var InvoiceParticular $item */

        foreach($invoice->getInvoiceParticulars() as $item ){

            /** @var Particular  $particular */

            $particular = $item->getParticular();
            if( $particular->getService()->getId() == 4 ){

                $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }
    }

    public function admittedPatientAccessories(InvoiceTransaction $transaction){

        $em = $this->_em;

        /** @var InvoiceParticular $item */
        if(!empty($transaction->getAdmissionPatientParticulars())){

            foreach($transaction->getAdmissionPatientParticulars() as $item ){

                /** @var Particular  $particular */

                $particular = $item->getParticular();
                if( $particular->getService()->getId() == 4 ){
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }

    }

    public function groupServiceBy(){

        $pass2 = array();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dpsConfig = :config')->setParameter('config', 1) ;
        $qb->andWhere('e.service IN(:service)')
            ->setParameter('service',array_values(array(1,2,3,4)));
        $qb->orderBy('e.name','ASC');
        $data = $qb->getQuery()->getResult();

        foreach ($data as $parent => $children){

            foreach($children as $child => $none){
                $pass2[$parent][$child] = true;
                $pass2[$child][$parent] = true;
            }
        }

    }







}
