<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Appstore\Bundle\DmsBundle\Controller\InvoiceController;
use Appstore\Bundle\DmsBundle\Entity\AdmissionPatientDmsParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsConfig;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceParticular;
use Appstore\Bundle\DmsBundle\Entity\Invoice;
use Appstore\Bundle\DmsBundle\Entity\InvoiceDmsParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * DmsInvoiceParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsInvoiceParticularRepository extends EntityRepository
{

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }

    public function invoicePathologicalReportLists(User $user , $mode , $data)
    {
        $hospital = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.dmsInvoice','e');
        $qb->join('ip.particular','p');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $qb->andWhere('p.service = :service')->setParameter('service', 1) ;
       // $this->handleSearchBetween($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function fileUpload(DmsInvoice $invoice,$data,$file)
    {
        $em = $this->_em;
        if(isset($file['file'])){
            $particular = new DmsInvoiceParticular();
            $dmsService = $this->_em->getRepository('DmsBundle:DmsService')->find($data['dmsService']);
            $particular->setDmsInvoice($invoice);
            $particular->setDmsService($dmsService);
            $img = $file['file'];
            $fileName = $img->getClientOriginalName();
            $imgName =  uniqid(). '.' .$fileName;
            $img->move($particular->getUploadDir(), $imgName);
            $particular->setMetaValue($data['investigation']);
            $particular->setPath($imgName);
            $em->persist($particular);
            $em->flush();
        }
    }

    public function insertInvoiceParticularSingle(DmsInvoice $invoice, $data)
    {
        $em = $this->_em;
        $service = $this->_em->getRepository('DmsBundle:DmsService')->findOneBy(array('slug'=>$data['service']));
        $teethNo = !empty($data['teethNo']) ? $data['teethNo'] : '' ;
        $explode = explode(',',$teethNo);
        $entity = new DmsInvoiceParticular();
        $entity->setDmsService($service);
        $entity->setMetaValue($data['procedure']);
        $entity->setDiseases($data['diseases']);
        $entity->setTeethNo($explode);
        $entity->setDmsInvoice($invoice);
        $em->persist($entity);
        $em->flush();

    }

    public function insertInvoiceParticularReturn(DmsInvoice $invoice, $data)
    {
        $em = $this->_em;
        $service = $this->_em->getRepository('DmsBundle:DmsService')->findOneBy(array('slug' => $data['service']));
        $invoiceParticulars = $this->findBy(array('dmsInvoice'=>$invoice,'dmsService'=>$service ));
        $data ='';
        foreach ($invoiceParticulars as $invoiceParticular ):
        $colSpan = empty($invoiceParticular->getTeethNo()[0]) ? 'colspan="2"':'';
        $data .='<tr id="remove-'.$invoiceParticular->getId().'">';
        $data .='<td  class="numeric"'.$colSpan.'>'.$invoiceParticular->getMetaValue().'</td>';
        if (!empty($invoiceParticular->getMetaValue()) and !empty($invoiceParticular->getTeethNo()[0])) {
            $data .= '<td class="numeric">';
            if($invoice->getCustomer()->getAgeGroup() == 'Adult'){
                $data .='<table class="dms-table">';
                $leftTeeths = [8,7,6,5,4,3,2,1];
                $upperRightTeeths = array(9 =>1,10 =>2,11=>3,12=>4,13=>5,14=>6,15=>7,16=>8);
                $lowerLeftTeeths = array(24=>8,23=>7,12=>6,21=>5,20=>4,19=>3,18=>2,17=>1);
                $lowerRightTeeths = array(25 =>1,26 =>2,27=>3,28=>4,29=>5,30=>6,31=>7,32=>8);
                $data .='<tr>';
                $data .='<td class="dms-td dms-td-border-none dms-td-border-bottom">';
                $data .='<ul class="leftTeeth">';
                foreach ($leftTeeths as $left) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($left,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$left.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .='<td class="dms-td dms-td-border-bottom">';
                $data .='<ul class="rightTeeth">';
                foreach ($upperRightTeeths as $key=>$right) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$right.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .= '</tr>';
                $data .='<tr>';
                $data .='<td class="dms-td dms-td-border-none">';
                $data .='<ul class="leftTeeth">';
                foreach ($lowerLeftTeeths as $key=>$left) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$left.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .='<td class="dms-td">';
                $data .='<ul class="rightTeeth">';
                foreach ($lowerRightTeeths as $key=>$right) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$right.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .= '</tr>';
                $data .= '</table>';
            }else{
                $data .='<table class="dms-table">';
                $leftTeeths = array(37=>'E',36=>'D',35=>'C',34=>'B',33=>'A');
                $upperRightTeeths = array(38=>'A',39=>'B',40=>'C',41=>'D',42=>'E');
                $lowerLeftTeeths = array(47=>'E',46=>'D',45=>'C',44=>'B',43=>'A');
                $lowerRightTeeths = array(38=>'A',49=>'B',50=>'C',51=>'D',52=>'E');
                $data .='<tr>';
                $data .='<td class="dms-td dms-td-border-none dms-td-border-bottom">';
                $data .='<ul class="leftTeeth">';
                foreach ($upperRightTeeths as $key=>$right) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$right.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .='<td class="dms-td dms-td-border-bottom">';
                $data .='<ul class="rightTeeth">';
                foreach ($upperRightTeeths as $key=>$right) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$right.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .= '</tr>';
                $data .='<tr>';
                $data .='<td class="dms-td dms-td-border-none">';
                $data .='<ul class="leftTeeth">';
                foreach ($lowerLeftTeeths as $key=>$left) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$left.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .='<td class="dms-td">';
                $data .='<ul class="rightTeeth">';
                foreach ($lowerRightTeeths as $key=>$right) :
                    $selected = (!empty($invoiceParticular->getTeethNo()) and in_array($key,$invoiceParticular->getTeethNo())) ? 'class="active"' : '';
                    $data .='<li '.$selected.'>'.$right.'</li>';
                endforeach;
                $data .='</ul>';
                $data .='</td>';
                $data .= '</tr>';
                $data .= '</table>';
            }

            $data .= '</td>';
        }
        $data .='<td  class="numeric">'.$invoiceParticular->getDiseases().'</td>';
        $data .='<td class="numeric">';
        $data .='<a href="javascript:" class="btn red mini particularDelete" data-tab="'.$service->getSlug().'" data-id="'. $invoiceParticular->getId().'" id="'. $invoiceParticular->getId().'" data-url="/dms/invoice/'.$invoice->getInvoice().'/'.$invoiceParticular->getId().'/particular-delete" ><i class="icon-trash"></i></a>';
        $data .='</td>';
        $data .='</tr>';

        endforeach;

        return $data;
    }


    public function insertInvoiceInvestigationUpload(DmsInvoice $invoice, $data)
    {
        $em = $this->_em;

        $service = $this->_em->getRepository('DmsBundle:DmsService')->find($data['dmsService']);
        $invoiceParticulars = $this->findBy(array('dmsInvoice'=> $invoice,'dmsService' => $service ));
        $data ='';
        /* @var $invoiceParticular DmsInvoiceParticular */
        foreach ($invoiceParticulars as $invoiceParticular ):

            $date = $invoiceParticular->getCreated()->format('d-m-Y');
            $data .='<tr id="remove-'.$invoiceParticular->getId().'">';
            $data .='<td>'.$date.'</td>';
            $data .='<td>'.$invoiceParticular->getMetaValue().'</td>';
            $data .='<td><a target="_blank" href="/'.$invoiceParticular->getWebPath().'">View Image</a></td>';
            $data .='<td class="numeric">';
            $data .='<a href="javascript:" class="btn red mini particularDelete" data-tab="'.$service->getSlug().'" data-id="'. $invoiceParticular->getId().'" id="'. $invoiceParticular->getId().'" data-url="/dms/invoice/'.$invoice->getInvoice().'/'.$invoiceParticular->getId().'/particular-delete" ><i class="icon-trash"></i></a>';
            $data .='</td>';
            $data .='</tr>';

        endforeach;

        return $data;
    }
    public function insertInvoiceItems(DmsInvoice $invoice, $data)
    {
        $em = $this->_em;
        if(!empty($data['metaKey'])) {
            $this->removeInvoiceParticularPreviousCheck($invoice);
            foreach ($data['metaKey'] as $key => $val) {
                $particular = $this->_em->getRepository('DmsBundle:DmsParticular')->find($val);
                $entity = new DmsInvoiceParticular();
                $invoiceDmsParticular = $this->_em->getRepository('DmsBundle:DmsInvoiceParticular')->findOneBy(array('dmsInvoice' => $invoice, 'dmsParticular' => $particular));
                if (!empty($invoiceDmsParticular)) {
                    $entity = $invoiceDmsParticular ;
                    $entity->setMetaValue(trim($data['metaValue'][$key]));
                } else {
                    $entity->setDmsParticular($particular);
                    $entity->setMetaValue(trim($data['metaValue'][$key]));
                }
                $entity->setDmsInvoice($invoice);
                $em->persist($entity);
                $em->flush();
            }
            $this->updateMetaCheckValue($invoice,$data);
        }
    }

    public function updateMetaCheckValue($invoice,$data)
    {
        $em = $this->_em;
        foreach ($data['metaKey'] as $key => $val) {
            if(isset($data['metaCheck'][$key]) and $data['metaCheck'][$key] > 0) {
                $particular = $data['metaCheck'][$key];
                $invoiceDmsParticular = $this->_em->getRepository('DmsBundle:DmsInvoiceParticular')->findOneBy(array('dmsInvoice' => $invoice, 'dmsParticular' => $particular));
                $invoiceDmsParticular->setMetaCheck($data['metaCheck'][$key]);
                $em->flush();
            }
        }

    }

    public function removeInvoiceParticularPreviousCheck(DmsInvoice $invoice)
    {
        $em = $this->_em;
        $update = $em->createQuery("UPDATE DmsBundle:DmsInvoiceParticular e SET e.metaCheck = 0 WHERE e.dmsService IS NULL and  e.dmsInvoice = ".$invoice->getId());
        $update->execute();
    }

    public function removeInvoiceParticularCheckItem(DmsInvoice $invoice)
    {
        $em = $this->_em;
        $remove = $em->createQuery("DELETE DmsBundle:DmsInvoiceParticular e WHERE e.dmsService IS NULL and  e.dmsInvoice = ".$invoice->getId());
        $remove->execute();
    }

    public function getSalesItems(DmsInvoice $sales)
    {
        $entities = $sales->getDmsInvoiceParticulars();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= '<tr id="remove-'. $entity->getId() . '">';
            $data .= '<td class="span1"><span class="badge badge-warning toggle badge-custom" id='. $entity->getId() .'" ><span>[+]</span></span></td>';
            $data .= '<td class="span1" >' . $i . '</td>';
            $data .= '<td class="span1" >' . $entity->getDmsParticular()->getDmsParticularCode() . '</td>';
            $data .= '<td class="span4" >' . $entity->getDmsParticular()->getName() . '</td>';
            $data .= '<td class="span2" >' . $entity->getDmsParticular()->getService()->getName() . '</td>';
            $data .= '<td class="span1" >' . $entity->getQuantity() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSalesPrice() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSubTotal() . '</td>';
            $data .= '<td class="span1" >
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/dms/invoice/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini particularDelete" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function invoiceDmsParticularLists($user){


    }

    public function dmsInvoiceParticularReverse(Invoice $invoice)
    {

        $em = $this->_em;

        /** @var InvoiceDmsParticular $item */

        foreach($invoice->getDmsInvoiceParticulars() as $item ){

            /** @var DmsParticular  $particular */

            $particular = $item->getDmsParticular();
            if( $particular->getService()->getId() == 4 ){
                $qnt = ($particular->getSalesQuantity() - $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }

    }

    public function getLastCode($entity,$datetime)
    {

        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->createQueryBuilder('ip');
        $qb
            ->select('MAX(ip.code)')
            ->join('ip.dmsInvoice','s')
            ->where('s.hospitalConfig = :hospital')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('hospital', $entity->getDmsConfig())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }


    public function reportSalesAccessories(GlobalOption $option ,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.particular','p');
        $qb->join('ip.dmsInvoice','i');
        $qb->select('SUM(ip.quantity * p.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('i.hospitalConfig = :hospital');
        $qb->setParameter('hospital', $option->getDmsConfig());
        $qb->andWhere("i.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        if (!empty($data['startDate']) ) {
            $qb->andWhere("i.updated >= :startDate");
            $qb->setParameter('startDate', $startDate.' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("i.updated <= :endDate");
            $qb->setParameter('endDate', $endDate.' 23:59:59');
        }
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res['totalPurchaseAmount'];

    }

    public function serviceDmsParticularDetails(User $user, $data)
    {

        $hospital = $user->getGlobalOption()->getDmsConfig()->getId();
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        if(!empty($data['service'])){

            $qb = $this->createQueryBuilder('ip');
            $qb->leftJoin('ip.particular','p');
            $qb->leftJoin('ip.dmsInvoice','e');
            $qb->select('SUM(ip.quantity) AS totalQuantity');
            $qb->addSelect('SUM(ip.quantity * p.purchasePrice ) AS purchaseAmount');
            $qb->addSelect('SUM(ip.quantity * ip.salesPrice ) AS salesAmount');
            $qb->addSelect('p.name AS serviceName');
            $qb->where('e.hospitalConfig = :hospital');
            $qb->setParameter('hospital', $hospital);
            $qb->andWhere('p.service = :service');
            $qb->setParameter('service', $data['service']);
            $qb->andWhere("e.process IN (:process)");
            $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death'));
            $this->handleDateRangeFind($qb,$data);
            $qb->groupBy('p.id');
            $res = $qb->getQuery()->getArrayResult();
            return $res;

        }else{

            return false;
        }

    }


    public function searchAutoComplete(DmsConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.dmsInvoice', 'i');
        $query->select('e.metaValue as id');
        $query->where($query->expr()->like("e.metaValue", "'$q%'"  ));
        $query->andWhere("i.dmsConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.metaValue');
        $query->orderBy('e.metaValue', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }

    public function searchProcedureDiseasesComplete(DmsConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.dmsInvoice', 'i');
        $query->select('e.diseases as id');
        $query->where($query->expr()->like("e.diseases", "'$q%'"  ));
        $query->andWhere("i.dmsConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.diseases');
        $query->orderBy('e.diseases', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }
}
