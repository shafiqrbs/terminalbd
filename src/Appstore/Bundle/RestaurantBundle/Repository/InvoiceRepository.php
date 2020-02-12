<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Service\PosItemManager;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Mike42\Escpos\Printer;

/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRepository extends EntityRepository
{

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $commission = isset($data['commission'])? $data['commission'] :'';
        $assignDoctor = isset($data['doctor'])? $data['doctor'] :'';
        $referred = isset($data['referred'])? $data['referred'] :'';
        $process = isset($data['process'])? $data['process'] :'';
        $customerName = isset($data['name'])? $data['name'] :'';
        $customerMobile = isset($data['mobile'])? $data['mobile'] :'';
        $created = isset($data['created'])? $data['created'] :'';
        $deliveryDate = isset($data['deliveryDate'])? $data['deliveryDate'] :'';
        $transactionMethod = isset($data['transactionMethod'])? $data['transactionMethod'] :'';
        $service = isset($data['service'])? $data['service'] :'';
        $cabinGroup = isset($data['cabinGroup'])? $data['cabinGroup'] :'';
        $cabin = isset($data['cabinNo'])? $data['cabinNo'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->join('e.customer','c');
            $qb->andWhere($qb->expr()->like("c.customerId", "'%$customerName%'"  ));
        }

        if (!empty($customerMobile)) {
            $qb->join('e.customer','m');
            $qb->andWhere($qb->expr()->like("m.mobile", "'%$customerMobile%'"  ));
        }
        if (!empty($created)) {
            $compareTo = new \DateTime($created);
            $created =  $compareTo->format('Y-m-d');
            $qb->andWhere("e.created LIKE :created");
            $qb->setParameter('created', $created.'%');
        }

        if (!empty($deliveryDate)) {
            $compareTo = new \DateTime($deliveryDate);
            $created =  $compareTo->format('Y-m-d');
            $qb->andWhere("e.deliveryDateTime LIKE :deliveryDate");
            $qb->setParameter('deliveryDate', $created.'%');
        }

        if(!empty($commission)){
            $qb->andWhere("e.hmsCommission = :commission");
            $qb->setParameter('commission', $commission);
        }
        if(!empty($assignDoctor)){
            $qb->andWhere("e.assignDoctor = :assignDoctor");
            $qb->setParameter('assignDoctor', $assignDoctor);
        }

        if(!empty($referred)){
            $qb->andWhere("e.referredDoctor = :referredDoctor");
            $qb->setParameter('referredDoctor', $referred);
        }

        if(!empty($process)){
            $qb->andWhere("e.process = :process");
            $qb->setParameter('process', $process);
        }

        if(!empty($transactionMethod)){
            $qb->andWhere("e.transactionMethod = :transactionMethod");
            $qb->setParameter('transactionMethod', $transactionMethod);
        }

        if(!empty($service)){
            $qb->andWhere("e.service = :service");
            $qb->setParameter('service', $service);
        }

        if(!empty($cabin)){
            $qb->andWhere("e.cabin = :cabin");
            $qb->setParameter('cabin', $cabin);
        }
        if(!empty($cabinGroup)){
            $qb->leftJoin('e.cabin','cabin');
            $qb->leftJoin('cabin.serviceGroup','sg');
            $qb->andWhere("sg.id = :cabinGroup");
            $qb->setParameter('cabinGroup', $cabinGroup);
        }
    }

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data['startDate']) and empty($data['endDate'])){
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

    public function todaySalesOverview(User $user , $data , $previous ='')
    {

        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        } elseif (!empty($data['startDate']) and !empty($data['endDate'])) {
            $data['startDate'] = date('Y-m-d', strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d', strtotime($data['endDate']));
        }

        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.total) as total ,sum(e.discount) as discount , sum(e.vat) as vat, sum(e.payment) as payment, sum(e.due) as due');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
        if ($previous == 'true'){

            if (!empty($data['startDate'])) {
                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $data['startDate'] . ' 00:00:00');
            }
            if (!empty($data['endDate'])) {
                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate', $data['endDate'] . ' 23:59:59');
            }

        }elseif ($previous == 'false'){

            if (!empty($data['startDate'])) {
                $qb->andWhere("e.updated < :startDate");
                $qb->setParameter('startDate', $data['startDate'] . ' 00:00:00');
            }
        }
        $qb->andWhere('e.process = :process')->setParameter('process', 'Done');
        $result = $qb->getQuery()->getOneOrNullResult();
        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $total = !empty($result['total']) ? $result['total'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $due = !empty($result['due']) ? $result['due'] :0;
        $receive = !empty($result['payment']) ? $result['payment'] :0;
        $data = array('subTotal'=> $subTotal ,'total'=> $total ,'discount'=> $discount ,'vat'=> $vat,'receive'=> $receive,'due'=>$due);
        return $data;
    }


    public function findWithOverview(User $user , $data , $mode='')
    {

        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','e');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(e.total) as netTotal , sum(e.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $this->handleSearchBetween($qb,$data);
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Delivered'));
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

    public function findWithSalesOverview(User $user , $data = array())
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(e.total) as netTotal , sum(e.payment) as netPayment , sum(e.due) as netDue');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done'));
        $result = $qb->getQuery()->getOneOrNullResult();
        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'total'=> $netTotal , 'receive'=> $netPayment , 'due'=> $netDue);

        return $data;
    }

    public function transactionBaseOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.transactionMethod','t');
        $qb->select('sum(e.total) as amount');
        $qb->addSelect('t.name as name');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Delivered'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('t.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function userSalesOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.salesBy','u');
        $qb->leftJoin('u.profile','p');
        $qb->select('sum(e.total) as amount');
        $qb->addSelect('p.name as name');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Delivered'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('u.id');
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
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.doctorInvoices','ip');
        $qb->leftJoin('ip.assignDoctor','d');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('d.name as referredName');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
        $qb->andWhere('ip.process = :mode')->setParameter('mode', 'Paid');
        if (!empty($data['startDate']) ) {
            $qb->andWhere("ip.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("ip.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }


    public function invoiceLists(User $user,$data)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        //$qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode) ;
        $this->handleSearchBetween($qb,$data);
        $this->handleDateRangeFind($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function invoicePathologicalReportLists(User $user , $mode , $data)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }


    public function doctorInvoiceLists(User $user,$data)
    {
        $config = $user->getGlobalOption()->getRestaurantConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.paymentStatus != :status')->setParameter('status', 'pending') ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function updateInvoiceTotalPrice(Invoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('RestaurantBundle:InvoiceParticular','si')
            ->select('sum(si.subTotal) as subTotal')
            ->where('si.invoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();
        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){
            if ($invoice->getRestaurantConfig()->getVatEnable() == 1 && $invoice->getRestaurantConfig()->getVatPercentage() > 0) {
              //  $totalAmount = ($subTotal- $invoice->getDiscount());
                $vat = $this->getCulculationVat($invoice,$subTotal);
                $invoice->setVat($vat);
            }
            $invoice->setSubTotal($subTotal);
            $total =($invoice->getSubTotal() + $invoice->getVat() - $invoice->getDiscount());
            $invoice->setTotal($total);
            $invoice->setDue($invoice->getTotal() - $invoice->getPayment() );

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

    public function getCulculationVat(Invoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getRestaurantConfig()->getVatPercentage())/100 );
        return round($vat);
    }

    public function getInvoiceDetails(Invoice $invoice){

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from('RestaurantBundle:InvoiceParticular','ip');
        $qb->innerJoin('ip.particular','particular');
        $qb->where('si.invoice = :invoice');
        $qb->setParameter('invoice', $invoice ->getId());
        $qb->groupBy('particular.service');

    }

    public function updatePatientInfo($invoice,Customer $patient)
    {
        $em = $this->_em;
        $invoice = $this->_em->getRepository('RestaurantBundle:Invoice')->find($invoice);
        $invoice->setCustomer($patient);
        $invoice->setMobile($patient->getMobile());
        $em->persist($invoice);
        $em->flush($invoice);

    }

    public function patientAdmissionUpdate($data,Invoice $entity)
    {
        $em = $this->_em;
        $invoiceInfo = $data['appstore_bundle_configbundle_invoice'];
        if($invoiceInfo['cabin']){
            $cabin = $em->getRepository('RestaurantBundle:Particular')->find($invoiceInfo['cabin']);
            $entity->setCabin($cabin);
        }
        if($invoiceInfo['assignDoctor']){
            $assignDoctor = $em->getRepository('RestaurantBundle:Particular')->find($invoiceInfo['assignDoctor']);
            $entity->setAssignDoctor($assignDoctor);
        }
        if($invoiceInfo['department']){
            $department = $em->getRepository('RestaurantBundle:HmsCategory')->find($invoiceInfo['department']);
            $entity->setDepartment($department);
        }
        if($invoiceInfo['cabinNo']){
            $entity->setCabinNo($invoiceInfo['cabinNo']);
        }
        if($invoiceInfo['disease']){
            $entity->setDisease($invoiceInfo['disease']);
        }
        $em->persist($entity);
        $em->flush($entity);

    }

    public function checkTokenBooking($invoice , $tokenNo)
    {
        $invoice = $this->_em->getRepository('RestaurantBundle:Invoice')->find($invoice);
        $cabin = $this->_em->getRepository('RestaurantBundle:Particular')->find($tokenNo);
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.tokenNo) AS cabinCount');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $invoice ->getRestaurantConfig()->getId());
        $qb->andWhere('e.tokenNo = :token')->setParameter('token', $cabin ->getId());
        $qb->andWhere('e.process = :process')->setParameter('process', 'Kitchen');
        $res = $qb->getQuery()->getOneOrNullResult();
        if(!empty($res) and $res['cabinCount'] > 0 ){
            echo 'invalid';
        }else{
            $invoice->setTokenNo($cabin);
            $this->_em->persist($invoice);
            $this->_em->flush();
            echo 'valid';
        }
        exit;

    }

    public function discountCalculation(Invoice $invoice,$discount)
    {
        $type = $invoice->getRestaurantConfig()->getDiscountType();
        if($type == 'flat'){
            $val = ( $invoice->getSubTotal() - $discount );
        }else{
            $val = ( ($invoice->getSubTotal() * (int)$discount)/100 );
        }
        return round($val);
    }

    public function insertNewCustomerWithDiscount(Invoice $invoice,Customer $customer )
    {
         if($invoice->getSubTotal()  > 0 && !empty($customer) ){
            $discount = $invoice->getRestaurantConfig()->getDiscountPercentage();
            $returnDiscount = $this->discountCalculation($invoice,$discount);
            $invoice->setDiscount($discount);
            $invoice->setDiscount($returnDiscount);
            $invoice->setTotal($invoice->getSubTotal() - $returnDiscount);
            $invoice->setDue($invoice->getTotal() - $invoice->getPayment());
            $invoice->setCustomer($customer);
            $invoice->setMobile($customer->getMobile());
            $this->_em->flush();
        }
    }

    public function insertDiscount(Invoice $invoice , $discount)
    {
        $returnDiscount =$this->discountCalculation($invoice,$discount);
        $invoice->setDiscount($discount);
        $invoice->setTotal($invoice->getSubTotal() - $returnDiscount);
        $invoice->setDue($invoice->getTotal() - $invoice->getPayment());
        $this->_em->flush();

    }

    public function salesTransactionReverse(Invoice $entity){

        $em = $this->_em;

        $sales = $this->_em->getRepository('AccountingBundle:AccountSales')->findOneBy(array('restaurantInvoice'=>$entity));

        if(!empty($sales)){

            /* @var $sales AccountSales  */
                $globalOption = $sales->getGlobalOption()->getId();
                $accountRefNo = $sales->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $accountCash->execute();
        }

        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.hmsInvoices = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }

    }

    public function posPrint(Invoice $entity)
    {
        $em = $this->_em;
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $option = $entity->getCreatedBy()->getGlobalOption();
        $config = $entity->getRestaurantConfig();

        $address        = $config->getAddress();
        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getPayment();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();

        $slipNo ='';
        $tableNo ='';
        if($entity->getTokenNo()){
            $tableNo = $entity->getTokenNo()->getName();
        }
        if($entity->getSlipNo()){
            $slipNo = $entity->getSlipNo();
        }
        $table = "Table no. {$slipNo} / $tableNo";

        $transaction    = new PosItemManager('Pay Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));


        /** ===================Invoice Sales Item Information========================= */


        /* Date is kept the same for testing */
        $date = date('d-m-Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> setFont(Printer::FONT_B);
        $printer -> text($address."\n");
        $printer -> feed();
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        if(!empty($vatRegNo)){
            $printer -> text("BIN No - ".$vatRegNo."\n\n");
        }
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::FONT_B);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(false);
        $printer -> text("Invoice no. {$entity->getInvoice()}                  {$table}\n");
        $printer -> setEmphasis(false);
        $printer -> text("Date: {$date}          {$transaction}\n");
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $i=1;
        /* @var $row InvoiceParticular */
        foreach ( $entity->getInvoiceParticulars() as $row){
            $productName = "{$i}. {$row->getParticular()->getName()}";
            $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer->text($discount);
            $printer -> setEmphasis(false);
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($grandTotal);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $printer -> text("Thanks for being here\n");
        if($website){
            $printer -> text("** Visit www.".$website."**\n");
        }
        $printer -> text("Powered by - www.terminalbd.com - 01828148148 \n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;

    }

}
