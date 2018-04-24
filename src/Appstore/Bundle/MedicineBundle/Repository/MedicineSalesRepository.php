<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * MedicineSalesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineSalesRepository extends EntityRepository
{

    public function getLastInvoice(MedicineConfig $config)
    {
        $entity = $this->findOneBy(
            array('medicineConfig' => $config),
            array('id' => 'DESC')
        );
        return $entity;
    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $assignDoctor = isset($data['doctor'])? $data['doctor'] :'';
        $process = isset($data['process'])? $data['process'] :'';
        $customerName = isset($data['name'])? $data['name'] :'';
        $customerMobile = isset($data['mobile'])? $data['mobile'] :'';
        $createdStart = isset($data['createdStart'])? $data['createdStart'] :'';
        $createdEnd = isset($data['createdEnd'])? $data['createdEnd'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->join('e.customer','c');
            $qb->andWhere($qb->expr()->like("c.name", "'$customerName%'"  ));
        }

        if (!empty($customerMobile)) {
            $qb->join('e.customer','m');
            $qb->andWhere($qb->expr()->like("m.mobile", "'%$customerMobile%'"  ));
        }
        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d');
            $qb->andWhere("e.created >= :created");
            $qb->setParameter('created', $created);
        }

        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
            $createdEnd =  $compareTo->format('Y-m-d');
            $qb->andWhere("e.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }

        if(!empty($assignDoctor)){
            $qb->andWhere("e.assignDoctor = :assignDoctor");
            $qb->setParameter('assignDoctor', $assignDoctor);
        }

        if(!empty($process)){
            $qb->andWhere("e.process = :process");
            $qb->setParameter('process', $process);
        }


    }

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
            $qb->andWhere("it.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("it.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }


    public function findWithOverview(User $user , $data , $mode='')
    {

        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(it.total) as netTotal , sum(it.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
       // $this->handleSearchBetween($qb,$data);
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Released','Death','Dead'));
        $result = $qb->getQuery()->getOneOrNullResult();

        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $netCommission = !empty($result['netCommission']) ? $result['netCommission'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'netTotal'=> $netTotal , 'netPayment'=> $netPayment , 'netDue'=> $netDue , 'netCommission'=> $netCommission);

        return $data;
    }

    public function findWithSalesOverview(User $user , $data , $mode='')
    {
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(e.total) as netTotal , sum(e.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $result = $qb->getQuery()->getOneOrNullResult();
        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $netCommission = !empty($result['netCommission']) ? $result['netCommission'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'netTotal'=> $netTotal , 'netPayment'=> $netPayment , 'netDue'=> $netDue , 'netCommission'=> $netCommission);

        return $data;
    }

    public function findWithServiceOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('e.invoiceParticulars','ip');
        $qb->leftJoin('ip.particular','p');
        $qb->leftJoin('p.service','s');
        $qb->select('sum(ip.subTotal) as subTotal');
        $qb->addSelect('s.name as serviceName');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('s.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithTransactionOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('ip.transactionMethod','p');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('p.name as transName');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('p.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithCommissionOverview(User $user, $data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }


        $config = $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.doctorInvoices','ip');
        $qb->leftJoin('ip.assignDoctor','d');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('d.name as referredName');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        $qb->andWhere('ip.process = :mode')->setParameter('mode', 'Paid');
        if (!empty($data['startDate']) ) {
            $qb->andWhere("ip.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("ip.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }

        $qb->groupBy('ip.assignDoctor');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }


    public function invoiceLists(User $user, $data)
    {
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }


    public function updateMedicineSalesTotalPrice(MedicineSales $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicineSalesItem','si')
            ->select('sum(si.subTotal) as subTotal')
            ->where('si.medicineSales = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();

        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){
            $invoice->setSubTotal(round($subTotal));
            $invoice->setNetTotal(round($subTotal));
            $invoice->setDue(round($subTotal));
        }else{
            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setDue(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
        }
        $em->persist($invoice);
        $em->flush();
        return $invoice;

    }

    public function updatePaymentReceive(MedicineSales $invoice)
    {
        $em = $this->_em;
        $res = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicineTreatmentPlan','si')
            ->select('sum(si.price) as subTotal ,sum(si.payment) as payment ,sum(si.discount) as discount')
            ->where('si.medicineInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->andWhere('si.status = :status')
            ->setParameter('status', 1)
            ->getQuery()->getOneOrNullResult();
        $subTotal = !empty($res['subTotal']) ? $res['subTotal'] :0;
        $payment = !empty($res['payment']) ? $res['payment'] :0;
        $discount = !empty($res['discount']) ? $res['discount'] :0;
        $invoice->setSubTotal($subTotal);
        $invoice->setPayment($payment);
        $invoice->setDiscount($discount);
        $invoice->setTotal($invoice->getSubTotal() - $discount);
        $invoice->setDue($invoice->getTotal() - $invoice->getPayment());
        $em->flush();

    }
    public function getCulculationVat(MedicineSales $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getMedicineConfig()->getVatPercentage())/100 );
        return round($vat);
    }

    public function updatePatientInfo($invoice,Customer $patient)
    {
        $em = $this->_em;
        $invoice = $this->_em->getRepository('MedicineBundle:MedicineSales')->find($invoice);
        $invoice->setCustomer($patient);
        $invoice->setMobile($patient->getMobile());
        $em->persist($invoice);
        $em->flush($invoice);

    }


    public function reportSalesOverview(User $user ,$data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.netTotal) as total ,sum(s.received) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branch = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }


    public  function reportSalesItemPurchaseSalesOverview(User $user, $data = array()){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.medicineSalesItems','si');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('COUNT(si.id) AS totalItem');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice) AS purchasePrice');
        $qb->addSelect('SUM(si.quantity * si.salesPrice) AS salesPrice');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }

    public function reportSalesTransactionOverview(User $user , $data = array())
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.transactionMethod','t');
        $qb->select('t.name as transactionName , sum(s.subTotal) as subTotal , sum(s.netTotal) as total ,sum(s.received) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->groupBy("s.transactionMethod");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function reportSalesProcessOverview(User $user,$data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.process as name , sum(s.subTotal) as subTotal , sum(s.netTotal) as total ,sum(s.received) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch")->setParameter('branch', $userBranch);
        }
        $qb->groupBy("s.process");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function salesReport( User $user , $data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->leftJoin('s.transactionMethod', 't');
        $qb->select('u.username as salesBy');
        $qb->addSelect('t.name as transactionMethod');
        $qb->addSelect('s.id as id');
        $qb->addSelect('s.created as created');
        $qb->addSelect('s.process as process');
        $qb->addSelect('s.invoice as invoice');
        $qb->addSelect('(s.due) as due');
        $qb->addSelect('(s.subTotal) as subTotal');
        $qb->addSelect('(s.netTotal) as total');
        $qb->addSelect('(s.received) as payment');
        $qb->addSelect('(s.discount) as discount');
        $qb->addSelect('(s.vat) as vat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function salesUserReport( User $user , $data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->select('u.username as salesBy');
        $qb->addSelect('u.id as userId');
        $qb->addSelect('SUM(s.due) as due');
        $qb->addSelect('SUM(s.subTotal) as subTotal');
        $qb->addSelect('SUM(s.netTotal) as total');
        $qb->addSelect('SUM(s.received) as payment');
        $qb->addSelect('SUM(s.discount) as discount');
        $qb->addSelect('SUM(s.vat) as vat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('salesBy');
        $qb->orderBy('total','DESC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }


    public function salesPurchasePriceReport(User $user,$data,$x)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $ids = array();
        foreach ($x as $y){
            $ids[]=$y['id'];
        }

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.medicineSalesItems','si');
        $qb->select('s.id as salesId');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $qb->andWhere("s.id IN (:salesId)")->setParameter('salesId', $ids);
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('totalPurchaseAmount','DESC');
        $qb->groupBy('salesId');
        $result = $qb->getQuery()->getArrayResult();
        $array= array();
        foreach ($result as $row ){
            $array[$row['salesId']]= $row['totalPurchaseAmount'];
        }
        return $array;
    }

    public  function reportSalesItem(User $user, $data=''){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $group = isset($data['group']) ? $data['group'] :'medicineStock';

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.medicineSalesItems','si');
        $qb->join('si.medicinePurchaseItem','item');
        $qb->join('si.medicineStock','m');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('SUM(si.purchasePrice) AS purchasePrice');
        $qb->addSelect('SUM(si.salesPrice) AS salesPrice');
        $qb->addSelect('m.name AS name');
        if($group == 'purchaseItem') {
            $qb->join('stock.purchaseItem','pi');
            $qb->addSelect('pi.barcode AS barcode');
        }
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->groupBy('si.'.$group);
        $qb->orderBy('s.created','DESC');
        $result = $qb->getQuery();
        return $result;
    }

    public function salesUserPurchasePriceReport(User $user,$data)
    {
        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->join('s.medicineSalesItems','si');
        $qb->select('u.username as salesBy');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('totalPurchaseAmount','DESC');
        $qb->groupBy('salesBy');
        $result = $qb->getQuery()->getArrayResult();
        $array= array();
        foreach ($result as $row ){
            $array[$row['salesBy']]= $row['totalPurchaseAmount'];
        }
        return $array;
    }


}
