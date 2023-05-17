<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\EcommerceBundle\Entity\Coupon;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * OnlineOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

            $invoice = isset($data['invoice'])  ? $data['invoice'] : '';
            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
            $deliveryDate =   isset($data['deliveryDate'])  ? $data['deliveryDate'] : '';
            $name =    isset($data['name'])? $data['name'] :'';
            $mobile =    isset($data['mobile'])? $data['mobile'] :'';
            $process =    isset($data['process'])? $data['process'] :'';
            $processHead =    isset($data['processHead'])? $data['processHead'] :'';
            if($name){
                $qb->andWhere($qb->expr()->like("e.customerName", "'%$name%'" ));
            }
            if($mobile){
                $qb->andWhere($qb->expr()->like("e.customerMobile", "'%$mobile%'" ));
            }
            if (!empty($deliveryDate)) {
                $compareTo = new \DateTime($deliveryDate);
                $startDelDate =  $compareTo->format('Y-m-d 00:00:00');
                $endDelDate =  $compareTo->format('Y-m-d 23:59:59');
                $qb->andWhere("e.deliveryDate >= :startDate")->setParameter('startDate', $startDelDate);
                $qb->andWhere("e.deliveryDate <= :endDate")->setParameter('endDate', $endDelDate);
            }
             if (!empty($startDate) and !empty($endDate) ) {
                $compareTo = new \DateTime($startDate);
                $startDate =  $compareTo->format('Y-m-d 00:00:00');
                $qb->andWhere("e.created >= :startDate")->setParameter('startDate', $startDate);
            }
            if (!empty($startDate) and !empty($endDate) ) {
                $compareTo = new \DateTime($endDate);
                $endDate =  $compareTo->format('Y-m-d 23:59:59');
                $qb->andWhere("e.created <= :endDate")->setParameter('endDate', $endDate);
            }
            if (!empty($invoice)) {
                $qb->andWhere("e.invoice = :invoice")->setParameter('invoice', $invoice);
            }
            if (!empty($processHead)) {
                $qb->andWhere("e.processHead = :process")->setParameter('process', $processHead);
            }
            if (!empty($process)) {
                $qb->andWhere("e.process = :process")->setParameter('process', $process);
            }

    }

    public function searchEcommerceCustomer(GlobalOption $option ,$q)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('UserBundle:User','e');
        $qb->select('e.id as id');
        $qb->addSelect('e.username as text');
        $qb->where($qb->expr()->like("e.username", "'%$q%'"  ));
        $qb->andWhere("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.userGroup = :group")->setParameter('group', 'customer');
        $qb->groupBy('e.username');
        $qb->orderBy('e.username', 'ASC');
        $qb->setMaxResults( '30' );
        return $qb->getQuery()->getArrayResult();

    }


    public function findWithSearch($option, $data)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
            $sort = $sortBy[0];
            $order = $sortBy[1];
        }
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.process != :head")->setParameter('head', "Delete");
        $qb->andWhere("e.isArchive != 1");
        $this->handleSearchBetween($qb,$data);
        if (empty($data['sortBy'])){
            $qb->orderBy('e.updated', 'DESC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        $res = $qb->getQuery();
        return  $res;

    }

    public function todayOrderDashboard($option)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.process != :head")->setParameter('head', "Delete");
        $qb->andWhere("e.isArchive != 1");
        $qb->orderBy('e.updated', 'DESC');
        $res = $qb->getQuery()->getResult();
        return  $res;

    }

    public function OrderDashboard($option)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('MAX(e.code) as totalOrder','SUM(e.subTotal) as subTotal','SUM(e.total) as total')
            ->where('e.globalOption = :option') ->setParameter('option', $option)
            ->andWhere("e.process != :head")->setParameter('head', "Delete");
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }


    public function findWithArchive($option, $data)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
            $sort = $sortBy[0];
            $order = $sortBy[1];
        }
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.isArchive = 1");
        if (empty($data['sortBy'])){
            $qb->orderBy('e.updated', 'DESC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        $res = $qb->getQuery();
        return  $res;

    }




    public function insertOrder(GlobalOption $globalOption)
    {
        $em = $this->_em;
        $order = new Order();
        $user = $em->getRepository('UserBundle:User')->find(30);
        $order->setCreatedBy($user);
        $order->setEcommerceConfig($globalOption->getEcommerceConfig());
        $em->persist($order);
        $em->flush();
        return $order;
    }

    /**
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    /*public function getLastCode($datetime, $entity)
    {
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->_em->getRepository('EcommerceBundle:Order')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.globalOption = :option')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('option', $entity)
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }*/



    public function fileUploader(Order $entity, $file = '')
    {
        $em = $this->_em;
        if(isset($file['uploadFile'])){
            $img = $file['uploadFile'];
            $fileName = $img->getClientOriginalName();
            $imgName =  uniqid(). '.' .$fileName;
            $img->move($entity->getUploadDir(), $imgName);
            $entity->setPath($imgName);
        }
        $em->persist($entity);
        $em->flush();
    }


    public function insertNewCustomerOrder(User $user,$cart, $data = '',$files = '')
    {

        $em = $this->_em;
        $couponCode     = empty($data['couponCode']) ? '' : $data['couponCode'];
        $comment        = empty($data['comment']) ? '' : $data['comment'];
        $name           = empty($data['customerName']) ? '' : $data['customerName'];
        $phone          = empty($data['customerMobile']) ? '' : $data['customerMobile'];
        $location       = empty($data['deliveryLocation']) ? '' : $data['deliveryLocation'];
        $address        = empty($data['deliveryAddress']) ? '' : $data['deliveryAddress'];
        $deliveryDate   = empty($data['deliveryDate']) ? '' : $data['deliveryDate'];
        $timePeriod     = empty($data['timePeriod']) ? '' : $data['timePeriod'];
        $accountMobile  = empty($data['accountMobile']) ? '' : $data['accountMobile'];
        $paymentMobile  = empty($data['paymentMobile']) ? '' : $data['paymentMobile'];
        $transactionId  = empty($data['transactionId']) ? '' : $data['transactionId'];
        $grandDiscount  = empty($data['grandDiscount']) ? '' : $data['grandDiscount'];
        $shippingCharge = empty($data['shippingCharge']) ? '' : $data['shippingCharge'];
        $order = new Order();
        $globalOption = $user->getGlobalOption();
        $order->setGlobalOption($globalOption);
        $customer = $this->getDomainCustomer($user, $globalOption);
        $order->setCustomer($customer);
        $order->setCustomerName($name);
        if($phone){
            $order->setCustomerMobile($user);
        }else{
            $order->setCustomerMobile($phone);
        }
        $order->setAddress($address);
        if($location){
            $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
            $order->setLocation($loc);
        }
        if($timePeriod){
             $period = $em->getRepository('EcommerceBundle:TimePeriod')->find($timePeriod);
            $order->setTimePeriod($period);
        }
        if(empty($deliveryDate)){
            $order->setDeliveryDate(new \DateTime("now"));
        }else{
            $date =new \DateTime($deliveryDate);
            $order->setDeliveryDate($date);
        }
        if($accountMobile){
            $account = $em->getRepository('AccountingBundle:AccountMobileBank')->find($accountMobile);
            $order->setAccountMobileBank($account);
            $order->setPaymentMobile($paymentMobile);
            $order->setTransaction($transactionId);
            $order->setCashOnDelivery(false);
        }else{
            $order->setCashOnDelivery(true);
        }

        $order->setEcommerceConfig($globalOption->getEcommerceConfig());
        $order->setShippingCharge($shippingCharge);
        $vat = $this->getCulculationVat($globalOption, $cart->total());
        $order->setVat($vat);
        $order->setComment($comment);
        $order->setCreatedBy($user);
        $order->setSubTotal($cart->total());
        $order->setItem($cart->total_items());
        $grandTotal = $cart->total() + $order->getShippingCharge() + $vat;
        if (!empty($couponCode)) {
            $coupon = $this->_em->getRepository('EcommerceBundle:Coupon')->getValidCouponCode($globalOption,$couponCode);
            if (!empty($coupon)){
                $couponAmount = $this->getCalculatorCouponAmount($order->getSubTotal(), $coupon);
                $order->setTotal($grandTotal - $couponAmount);
                $order->setCoupon($coupon);
                $order->setCouponAmount($couponAmount);
            }
        }else{
            $order->setTotal($grandTotal);
        }
        $em->persist($order);
        $em->flush();
        $this->insertOrderItem($order,$cart);
        $this->fileUploader($order,$files);
        return $order;

    }

    public function getDomainCustomer($user,GlobalOption $globalOption)
    {

        $customer = $this->_em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile' => $user->getUsername()));
        if(!empty($customer)){

            return $customer;

        }else{

            $em = $this->_em;
            $entity = new Customer();
            $entity->setGlobalOption($globalOption);
            $entity->setMobile($user->getUsername());
            $entity->setEmail($user->getEmail());
            $entity->setAddress($user->getProfile()->getAddress());
            $entity->setName($user->getProfile()->getName());
            $entity->setCustomerType('online');
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }
    }

    public function insertOrderItem(Order $order,$cart)
    {

        $em = $this->_em;


        foreach ($cart->contents() as $row){
            $item = $em->getRepository('EcommerceBundle:Item')->find($row['id']);
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            if($item){
                $orderItem->setItem($item);
            }
            $orderItem->setPrice($row['price']);
            $orderItem->setQuantity($row['quantity']);
            $orderItem->setUnitName($row['productUnit']);
            $orderItem->setItemName($row['name']);
            $orderItem->setBrandName($row['brand']);
            $orderItem->setCategoryName($row['category']);
            $orderItem->setSubTotal($row['subtotal']);
            $em->persist($orderItem);
            $em->flush();
        }

    }

    public function getCulculationVat(GlobalOption $globalOption,$total)
    {
        /* @var EcommerceConfig $config */
        $totalVat = 0;
        $config = $globalOption->getEcommerceConfig();
        if($config->isVatEnable() == 1 and $config->getVat() > 0 ){
            $vat = $config->getVat();
            $totalVat = round(($total  * $vat )/100);
        }
        return $totalVat;


    }

    public function getCalculatorCouponAmount( $grandTotal = 0, Coupon $coupon)
    {
        if ($coupon->getPercentage() == 1 ){
            $percentage = round(($grandTotal  * $coupon->getAmount() )/100);
            if($percentage >= $coupon->getAmountLimit()){
                $couponAmount = $coupon->getAmountLimit();
            }else{
                $couponAmount = $percentage;
            }
        }else{
            $couponAmount = $coupon->getAmount();
        }
        return $couponAmount;
    }


    public function updateOrder(Order $order)
    {
        $em = $this->_em;
        $orderItem = $em->getRepository('EcommerceBundle:OrderItem')->getItemOverview($order);
        $totalAmount = $orderItem['totalAmount'];
        $totalItem = $orderItem['totalQuantity'];
        $order->setSubTotal($totalAmount);
        $order->setItem($totalItem);
        $vat = $this->getCulculationVat($order->getGlobalOption(),$totalAmount);
        $grandTotal = $totalAmount + $order->getShippingCharge() + $vat - $order->getDiscountAmount();
        $order->setVat($vat);
        $order->setTotal($grandTotal);
        if (!empty($order->getCoupon())) {
            $couponAmount = $this->getCalculatorCouponAmount($totalAmount, $order->getCoupon());
            $order->setTotal($grandTotal - $couponAmount);
            $order->setCouponAmount($couponAmount);
        }else{
            $order->setTotal($grandTotal);
        }
        if($order->getPaidAmount() > $grandTotal ){
            $order->setReturnAmount($order->getPaidAmount()  - $grandTotal);
            $order->setDueAmount(0);
        }elseif($totalAmount < $grandTotal ){
            $order->setReturnAmount(0);
            $due = (int)$grandTotal - ((int) $order->getPaidAmount());
            $order->setDueAmount($due);
        }
        $em->flush();
        if($order->getEcommerceConfig()->isOrderDirectProcess() == 1 and $order->getEcommerceConfig()->getStockApplication()->getSlug() == "miss"){
            $em->getRepository("MedicineBundle:MedicineSales")->insertEcommerceDirectOrder($order);
        }
        if($order->getEcommerceConfig()->isOrderDirectProcess() == 1 and $order->getEcommerceConfig()->getStockApplication()->getSlug() == "inventory"){
            $em->getRepository("InventoryBundle:Sales")->insertEcommerceDirectOrder($order);
        }
    }

    public function updateOrderPayment(Order $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('EcommerceBundle:OrderPayment','e')
            ->select('sum(e.amount) as totalAmount')
            ->where('e.order = :order')
            ->andWhere('e.status = :status')
            ->setParameter('order', $entity ->getId())
            ->setParameter('status', 1)
            ->getQuery()->getSingleResult();

        $entity->setPaidAmount(floatval($total['totalAmount']));
        $due = $entity->getGrandTotalAmount() - $entity->getPaidAmount();
        $entity->setDueAmount($due);
        $em->persist($entity);
        $em->flush();
    }

    public function insertAndroidUploadOrder(GlobalOption $option , $orderJson= array())
    {


        $userId         = empty($userJson['userId']) ? '' : $userJson['userId'];
        $user           = $em->getRepository('UserBundle:User')->find($userId);
        $address        = empty($userJson['address']) ? '' : $userJson['address'];
        $mobile         = empty($userJson['mobile']) ? '' : $userJson['mobile'];
        $location       = empty($userJson['location']) ? '' : $userJson['location'];

        $orderId        = empty($orderJson['id']) ? '' : $orderJson['id'];
        $comment        = empty($orderJson['comment']) ? '' : $orderJson['comment'];
        $deliveryDate   = empty($orderJson['deliveryDate']) ? '' : $orderJson['deliveryDate'];
        $timePeriod     = empty($orderJson['timePeriod']) ? '' : $orderJson['timePeriod'];
        $find = $this->findOneBy(array('globalOption' => $option,'orderId'=> $orderId));
        if(empty($find)){
            $order = new Order();
            $order->setGlobalOption($option);
            $order->setCreatedBy($user);
            $order->setAddress($address);
            $order->setCustomerMobile($mobile);
            $order->setComment($mobile);
            $order->setOrderId($orderId);
            if($location){
                $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                $order->setLocation($loc);
            }
            if($timePeriod){
                $period = $em->getRepository('EcommerceBundle:TimePeriod')->find($timePeriod);
                $order->setTimePeriod($period);
            }
            if(empty($deliveryDate)){
                $order->setDeliveryDate(new \DateTime("now"));
            }else{
                $date =new \DateTime($deliveryDate);
                $order->setDeliveryDate($date);
            }
            $order->setCashOnDelivery(true);
            $order->setEcommerceConfig($option->getEcommerceConfig());
            $em->persist($order);
            $em->flush();
            return $order;
        }
        return false;
    }


    public function insertAndroidOrder(GlobalOption $option , $data= array())
    {

        $orderJson = json_decode($data['jsonOrder'],true);
        $orderJsonItem = json_decode($data['jsonOrderItem'],true);
        $em = $this->_em;
        $userId        = empty($orderJson['userId']) ? '' : $orderJson['userId'];
        $user = $em->getRepository(User::class)->find($userId);
        $addressId        = empty($orderJson['addressId']) ? '' : $orderJson['addressId'];
        $addressInfo = "";
        if($addressId){
            $addressInfo = $em->getRepository("DomainUserBundle:CustomerAddress")->find($addressId);
        }
        $orderId        = empty($orderJson['id']) ? '' : $orderJson['id'];
        $location       = empty($orderJson['locationId']) ? '' : $orderJson['locationId'];
        $couponCode     = empty($orderJson['couponCode']) ? '' : $orderJson['couponCode'];
        $comment        = empty($orderJson['comment']) ? '' : $orderJson['comment'];
        $deliveryDate   = empty($orderJson['deliveryDate']) ? '' : $orderJson['deliveryDate'];
        $timePeriod     = empty($orderJson['timePeriod']) ? '' : $orderJson['timePeriod'];
        $receiveAccount  = empty($orderJson['receiveAccount']) ? '' : $orderJson['receiveAccount'];
        $paymentMobile  = empty($orderJson['paymentMobile']) ? '' : $orderJson['paymentMobile'];
        $transactionMethod = empty($orderJson['transactionMethod']) ? '' : $orderJson['transactionMethod'];
        $transactionId  = empty($orderJson['transactionId']) ? '' : $orderJson['transactionId'];
        $subTotal       = empty($orderJson['subTotal']) ? '' : $orderJson['subTotal'];
        $total          = empty($orderJson['total']) ? '' : $orderJson['total'];
        $shippingCharge = empty($orderJson['shippingCharge']) ? '' : $orderJson['shippingCharge'];
        $find = $this->findOneBy(array('globalOption' => $option->getId(),'orderId'=> $orderId));
        if(empty($find)){
            $order = new Order();
            $order->setGlobalOption($option);
            $order->setCreatedBy($user);
            $order->setJsonOrder($data['jsonOrder']);
            $order->setJsonOrderItem($data['jsonOrderItem']);
            if($addressInfo){
                $order->setAddress($addressInfo->getAddress());
                $order->setCustomerMobile($addressInfo->getMobile());
                $order->setCustomerName($addressInfo->getName());
                $order->setCustomer($addressInfo->getCustomer());
                $order->setCustomerAddress($addressInfo);
            }
            $order->setOrderId($orderId);
            if($location){
                $loc = $em->getRepository('EcommerceBundle:DeliveryLocation')->find($location);
                $order->setLocation($loc);
            }
            if($timePeriod){
                $period = $em->getRepository('EcommerceBundle:TimePeriod')->find($timePeriod);
                $order->setTimePeriod($period);
            }
            if(in_array($transactionMethod,array('mobile','bank'))){
                $method = $em->getRepository('SettingToolBundle:TransactionMethod')->findOneBy(array('slug' => $transactionMethod));
                $order->setTransactionMethod($method);
            }
            if(empty($deliveryDate)){
                $order->setDeliveryDate(new \DateTime("now"));
            }else{
                $date =new \DateTime($deliveryDate);
                $order->setDeliveryDate($date);
            }
            if($receiveAccount and $transactionMethod == "mobile"){
                $account = $em->getRepository('AccountingBundle:AccountMobileBank')->find($receiveAccount);
                $order->setAccountMobileBank($account);
                $order->setPaymentMobile($paymentMobile);
                $order->setTransaction($transactionId);
                $order->setCashOnDelivery(false);

            }elseif($receiveAccount and $transactionMethod == "bank"){
                $account = $em->getRepository('AccountingBundle:AccountBank')->find($receiveAccount);
                $order->setAccountBank($account);
                $order->setCashOnDelivery(false);
            }else{
                $order->setCashOnDelivery(true);
            }
            $order->setEcommerceConfig($option->getEcommerceConfig());
            $order->setShippingCharge($shippingCharge);
            $vat = $this->getCulculationVat($option, $subTotal);
            $order->setVat($vat);
            $order->setComment($comment);
            $order->setSubTotal($subTotal);
            $order->setTotal($total);
            $grandTotal = $total + $shippingCharge + $vat;
            if (!empty($couponCode)) {
                $coupon = $this->_em->getRepository('EcommerceBundle:Coupon')->getValidCouponCode($option,$couponCode);
                if (!empty($coupon)){
                    $couponAmount = $this->getCalculatorCouponAmount($order->getTotal(), $coupon);
                    $order->setTotal($grandTotal - $couponAmount);
                    $order->setCoupon($coupon);
                    $order->setCouponAmount($couponAmount);
                }
            }else{
                $order->setTotal($grandTotal);
            }
            $em->persist($order);
            $em->flush();
            $this->insertJsonOrderItem($order,$data);
            return $order;
        }
        return false;
    }

    public function insertJsonOrderItem(Order $order,$data)
    {

        $em = $this->_em;
        $orderItem = json_decode($data['jsonOrderItem'],true);
        foreach ($orderItem as $row){
            $find = $em->getRepository('EcommerceBundle:OrderItem')->findOneBy(array('order' => $order,'orderItemId'=>$row['id']));
            $itemId = (empty($row['itemId']) and $row['itemId'] == null) ? '' : $row['itemId'];
            if(empty($find) and !empty($itemId)){
                $item = $em->getRepository('EcommerceBundle:Item')->find($itemId);
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setOrderItemId($row['id']);
                $orderItem->setOrderId($row['orderId']);
                $quantity = (isset($row['orderedQuantity']) and $row['orderedQuantity'] > 0) ? $row['orderedQuantity'] : 1;
                $price = isset($row['price']) ? $row['price'] : $item->getSalesPrice();
                if($item){
                    $orderItem->setItem($item);
                    if($item->getCategory()) {
                        $orderItem->setCategoryName($item->getCategory()->getName());
                    }
                    $orderItem->setItemName($item->getName());
                    if( $item->getBrand()){
                        $orderItem->setBrandName($item->getBrand()->getName());
                    }
                    $orderItem->setPrice($price);
                    $orderItem->setQuantity($quantity);
                    $orderItem->setSubTotal($price * $quantity);
                    $em->persist($orderItem);
                    $em->flush();
                }

            }

        }
        $this->updateOrder($order);

    }

    public function getApiOrders($option, $arr)
    {
        $user = $arr['user'];
        $process = (isset($arr['process']) and $arr['process']) ? $arr['process']:'';
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option")->setParameter('option', $option->getId());
        $qb->andWhere("e.createdBy = :user")->setParameter('user', $user);
        if($process and $process != "all"){
            $qb->andWhere("e.process =:process")->setParameter('process', $process);
        }
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery()->getResult();
        $data = array();
        /* @var $row Order */
        foreach ($result as $key => $row){

            $data[$key]['order_id'] = (int)$row->getId();
            $data[$key]['invoice'] = $row->getInvoice();
            $data[$key]['customerName'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getName() : $row->getCustomerName();
            $data[$key]['customerMobile'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getMobile() : $row->getCustomerMobile();
            $data[$key]['customerAddress'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getAddress() : $row->getAddress();
            $data[$key]['created'] = $row->getCreated()->format('Y-m-d H:i');
            $data[$key]['createdTime'] = $row->getCreated()->format('g:i A');
            $data[$key]['updated'] = $row->getUpdated()->format('Y-m-d H:i');
            $data[$key]['updatedTime'] = $row->getUpdated()->format('g:i A');
            $data[$key]['subTotal'] = (double) $row->getSubTotal();
            $data[$key]['discount'] = (double) ($row->getDiscount());
            $data[$key]['shippingCharge'] = (double) ($row->getShippingCharge());
            $data[$key]['vat'] = (double) $row->getVat();
            $data[$key]['total'] = (double) $row->getTotal();
            $data[$key]['timePeriod'] = ($row->getTimePeriod()) ? $row->getTimePeriod()->getName():'';
            $data[$key]['location'] = ($row->getLocation()) ? $row->getLocation()->getName():'';
            $data[$key]['process'] = $row->getProcess();
            $data[$key]['pickupAddress'] = $row->getAddress();
            $data[$key]['transactionId'] = ($row->getTransaction()) ? $row->getTransaction() :'';
            $data[$key]['paymentMobile'] = ($row->getPaymentMobile()) ? $row->getPaymentMobile() : '';
            $data[$key]['deliveryDate'] = $row->getDeliveryDate()->format('Y-m-d H:i');
            $data[$key]['deliveryTime'] = $row->getDeliveryDate()->format('g:i A');
            $data[$key]['method'] = ($row->getTransactionMethod()) ? $row->getTransactionMethod()->getName() :'';
            $data[$key]['cashOnDelivery'] = ($row->isCashOnDelivery() == true) ? 1 :0;

        }
        return $data;
    }

    public function getApiProcessOrders($option, $arr)
    {
        $user = $arr['user'];
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option")->setParameter('option', $option->getId());
        $qb->andWhere("e.createdBy = :user")->setParameter('user', $user);
        $qb->andWhere("e.process IN (:process)")->setParameter('process', array('wfc','confirm','created','courier'));
        $compareTo = new \DateTime('now');
        $startDate =  $compareTo->format('Y-m-01 00:00:00');
        $qb->andWhere("e.created >= :startDate")->setParameter('startDate', $startDate);
        $endDate =  $compareTo->format('Y-m-t 23:59:59');
        $qb->andWhere("e.created <= :endDate")->setParameter('endDate', $endDate);
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery()->getResult();
        $data = array();
        /* @var $row Order */
        foreach ($result as $key => $row){
            $data[$key]['invoice'] = $row->getInvoice();
            $data[$key]['customerName'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getName() : $row->getCustomerName();
            $data[$key]['customerMobile'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getMobile() : $row->getCustomerMobile();
            $data[$key]['customerAddress'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getAddress() : $row->getCustomer()->getAddress();
            $data[$key]['created'] = $row->getCreated()->format('Y-m-d H:i');
            $data[$key]['createdTime'] = $row->getCreated()->format('g:i A');
            $data[$key]['updated'] = $row->getUpdated()->format('Y-m-d H:i');
            $data[$key]['updatedTime'] = $row->getUpdated()->format('g:i A');
            $data[$key]['subTotal'] = $row->getSubTotal();
            $data[$key]['discount'] = ($row->getDiscount()) ? $row->getDiscount():'';
            $data[$key]['shippingCharge'] = ($row->getShippingCharge()) ? $row->getShippingCharge():'';
            $data[$key]['vat'] = $row->getVat();
            $data[$key]['total'] = $row->getTotal();
            $data[$key]['timePeriod'] = ($row->getTimePeriod()) ? $row->getTimePeriod()->getName():'';
            $data[$key]['location'] = ($row->getLocation()) ? $row->getLocation()->getName():'';
            $data[$key]['process'] = $row->getProcess();
            $data[$key]['pickupAddress'] = $row->getAddress();
            $data[$key]['transactionId'] = ($row->getTransaction()) ? $row->getTransaction() :'';
            $data[$key]['paymentMobile'] = ($row->getPaymentMobile()) ? $row->getPaymentMobile() : '';
            $data[$key]['deliveryDate'] = $row->getDeliveryDate()->format('Y-m-d H:i');
            $data[$key]['deliveryTime'] = $row->getDeliveryDate()->format('g:i A');
            $data[$key]['method'] = ($row->getTransactionMethod()) ? $row->getTransactionMethod()->getName() :'';
            $data[$key]['cashOnDelivery'] = ($row->isCashOnDelivery() == true) ? 1 :0;

        }
        return $data;

    }

    public function getApiOrderDetails($order)
    {

        /* @var $row Order */

        $row = $this->find($order);
        $data = array();
        $data['order_id'] = (int)$row->getId();
        $data['invoice'] = $row->getInvoice();
        $data['customerName'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getName() : $row->getCustomerName();
        $data['customerMobile'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getMobile() : $row->getCustomerMobile();
        $data['customerAddress'] = ($row->getCustomerAddress()) ? $row->getCustomerAddress()->getAddress() : $row->getAddress();
        $data['created'] = $row->getCreated()->format('Y-m-d H:i');
        $data['createdTime'] = $row->getCreated()->format('g:i A');
        $data['updated'] = $row->getUpdated()->format('Y-m-d H:i');
        $data['updatedTime'] = $row->getUpdated()->format('g:i A');
        $data['deliveryDate'] = $row->getDeliveryDate()->format('Y-m-d H:i');
        $data['deliveryTime'] = $row->getDeliveryDate()->format('g:i A');
        $data['subTotal'] = (double)$row->getSubTotal();
        $data['discount'] = (double) ($row->getDiscount());
        $data['shippingCharge'] = (double) $row->getShippingCharge();
        $data['vat'] = (double) $row->getVat();
        $data['total'] =(double) $row->getTotal();
        $data['timePeriod'] = ($row->getTimePeriod()) ? $row->getTimePeriod()->getName():'';
        $data['location'] = ($row->getLocation()) ? $row->getLocation()->getName():'';
        $data['process'] = $row->getProcess();
        $data['pickupAddress'] = $row->getAddress();
        $data['transactionId'] = (int) $row->getTransaction();
        $data['paymentMobile'] = $row->getPaymentMobile();
        $data['method'] = ($row->getTransactionMethod()) ? $row->getTransactionMethod()->getName() :'';
        $data['cashOnDelivery'] = ($row->isCashOnDelivery() == true) ? 1 :0;


        $orderItems = $row->getOrderItems();
        if ($orderItems) {
            /* @var $subs OrderItem */
            foreach ($orderItems as $i => $subs):
                $data['orderItem'][$i]['itemId'] = (integer)$subs->getId();
                $data['orderItem'][$i]['orderId'] = (int)$row->getId();
                $data['orderItem'][$i]['name'] = (string)$subs->getItemName();
                $data['orderItem'][$i]['price'] = (double)$subs->getPrice();
                $data['orderItem'][$i]['quantity'] = (double)$subs->getQuantity();
                $data['orderItem'][$i]['category'] = (string)$subs->getCategoryName();
                $data['orderItem'][$i]['brand'] = (string)$subs->getBrandName();
                $data['orderItem'][$i]['quantity'] = (integer)$subs->getQuantity();
                $data['orderItem'][$i]['size'] = (string)$subs->getSize();
                $data['orderItem'][$i]['color'] = (string)$subs->getColor();
                $data['orderItem'][$i]['imagePath'] = (string)$subs->getImagePath();
            endforeach;
        } else {
            $data['orderItem'] = array();
        }
        return $data;

    }




}
