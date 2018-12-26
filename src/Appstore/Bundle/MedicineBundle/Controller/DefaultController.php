<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Dompdf\Dompdf;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

class DefaultController extends Controller
{

	/**
	 * Lists all HotelCategory entities.
	 *
	 * @Secure(roles="ROLE_MEDICINE,ROLE_DOMAIN");
	 *
	 */

	public function indexAction()
    {

    	/* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
	    $data['endDate'] = $datetime->format('Y-m-d');
	    $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineIncome($this->getUser(),$data);
	    $user = $this->getUser();
	    $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
	    $salesUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,array('startDate'=>$data['startDate'],'endDate'=>$data['endDate']));
	 //   $userEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data);
	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $medicineSalesMonthly = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->medicineSalesMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $medicinePurchaseMonthly = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->medicinePurchaseMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $shortMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findMedicineShortListCount($user);
	    $expiryMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->expiryMedicineCount($user);

	    //   $purchaseUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	  //  $userSalesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));

	    $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());

	    $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->currentMonthSales($user,$data);
	    $userSalesAmount = array();
	    foreach($entities as $row) {
		    $userSalesAmount[$row['salesBy'].$row['month']] = $row['total'];
	    }

	    $medicineSalesMonthlyArr = array();
	    foreach($medicineSalesMonthly as $row) {
		    $medicineSalesMonthlyArr[$row['month']] = $row['total'];
	    }
	    $medicinePurchaseMonthlyArr = array();
	    foreach($medicinePurchaseMonthly as $row) {
		    $medicinePurchaseMonthlyArr[$row['month']] = $row['total'];
	    }


	    return $this->render('MedicineBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'income'                    => $income,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'medicineSalesMonthly'      => $medicineSalesMonthlyArr ,
            'medicinePurchaseMonthly'   => $medicinePurchaseMonthlyArr ,
            'salesUserReport'           => $salesUserReport ,
            'userSalesAmount'           => $userSalesAmount ,
            'employees'                 => $employees ,
            'shortMedicineCount'        => $shortMedicineCount ,
            'expiryMedicineCount'       => $expiryMedicineCount ,
            'searchForm'                => $data ,
        ));

    }

    public function updateExpirationMedicineAction()
    {
	    $em = $this->getDoctrine()->getManager();
    	$globalOption = $this->getUser()->getGlobalOption();
	    set_time_limit(0);
	    ignore_user_abort(true);
	    $config = $globalOption->getMedicineConfig();
	    $data = array('endDate'=>'2018-10-31');
	    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->findWithSearch($config,$data);

	    /* @var $entity MedicinePurchase */
	    /* @var $item MedicinePurchaseItem */

	    foreach ($entity->getQuery()->getResult() as $entity){
	    	foreach ($entity->getMedicinePurchaseItems() as $item){
			    $item->setRemainingQuantity(0);
			    $em->flush();
		    }
	    }
	    return $this->redirect($this->generateUrl('homepage'));
	  }

	public function copyToMedicineParticularAction(GlobalOption $option)
	  {
		  $em = $this->getDoctrine()->getManager();
		  set_time_limit(0);
		  ignore_user_abort(true);
		  $config = $option->getMedicineConfig();

		  $globalOption = $this->getUser()->getGlobalOption();
		  $existConfig = $globalOption->getMedicineConfig();


		  $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config));

		  /* @var $entity MedicineParticular */

		  foreach ($entities as $entity){

			  $newEntity = new MedicineParticular();
			  $newEntity->setMedicineConfig($existConfig);
			  $newEntity->setName($entity->getName());
			  $newEntity->setStatus(1);
			  $newEntity->setParticularType($entity->getParticularType());
			  $newEntity->setSlug($entity->getSlug());
			  $em->persist($newEntity);
			  $em->flush();
		  }
		  return $this->redirect($this->generateUrl('homepage'));

	  }

	public function copyToMedicineStockAction(GlobalOption $option)
	{
		$em = $this->getDoctrine()->getManager();
		set_time_limit(0);
		ignore_user_abort(true);
		$config = $option->getMedicineConfig();

		$globalOption = $this->getUser()->getGlobalOption();
		$existConfig = $globalOption->getMedicineConfig();


		$data = array();
		$entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($config,$data);

		/* @var $entity MedicineStock */

		foreach ($entity->getQuery()->getResult() as $entity){



			$newEntity = new MedicineStock();
			$newEntity->setMedicineConfig($existConfig);
			$newEntity->setName($entity->getName());
			$newEntity->setName($entity->getMode());
			$newEntity->setMedicineBrand($entity->getMedicineBrand());
			$newEntity->setBrandName($entity->getBrandName());
			$newEntity->setUnit($entity->getUnit());
			$newEntity->setPurchasePrice($entity->getPurchasePrice());
			$newEntity->setSalesPrice($entity->getSalesPrice());
			$newEntity->setMinQuantity($entity->getMinQuantity());
			if(!empty($entity->getRackNo())) {
				$rack = $this->getDoctrine()->getRepository( 'MedicineBundle:MedicineParticular' )->findOneBy( array(
					'medicineConfig' => $existConfig,
					'name'           => $entity->getRackNo()->getName()
				) );
				$newEntity->setRackNo( $rack );
			}
			if(!empty($entity->getAccessoriesBrand())){
				$accessories = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findOneBy(array('medicineConfig' => $existConfig,'name' => $entity->getAccessoriesBrand()->getName()));
				$newEntity->setAccessoriesBrand($accessories);
			}
			$em->persist($newEntity);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('homepage'));
	}

	public function copyToMedicineVendorAction(GlobalOption $option)
	{
		$em = $this->getDoctrine()->getManager();
		set_time_limit(0);
		ignore_user_abort(true);
		$config = $option->getMedicineConfig();

		$globalOption = $this->getUser()->getGlobalOption();
		$existConfig = $globalOption->getMedicineConfig();


		$entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findBy(array('medicineConfig'=> $config));

		/* @var $entity MedicineVendor */

		foreach ($entities as $entity){

			$newEntity = new MedicineVendor();
			$newEntity->setMedicineConfig($existConfig);
			$newEntity->setName($entity->getName());
			$newEntity->setCompanyName($entity->getCompanyName());
			$newEntity->setVendorCode($entity->getVendorCode());
			$newEntity->setStatus(1);
			$newEntity->setSlug($entity->getSlug());
			$em->persist($newEntity);
			$em->flush();
		}
		return $this->redirect($this->generateUrl('homepage'));
	}


}
