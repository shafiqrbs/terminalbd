<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\AccessoriesStockType;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;


/**
 * MedicineStockController controller.
 *
 */
class MedicineStockController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }


	/**
	 * @Secure(roles="ROLE_MEDICINE_STOCK")
	 */

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($config,$data);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
        $pagination = $this->paginate($entities);
        $selected = explode(',', $request->cookies->get('barcodes', ''));
        return $this->render('MedicineBundle:MedicineStock:index.html.twig', array(
            'pagination'    => $pagination,
            'config' => $config,
            'selected' => $selected,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));

    }

    /**
     * @Secure(roles="ROLE_MEDICINE_STOCK")
     */

    public function stockSearchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $pagination = "";
        if(isset($data['name']) and $data['name']){
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithGlobalSearch($data);
            $pagination = $this->paginate($entities);
        }
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));

        return $this->render('MedicineBundle:MedicineStock:stockSearch.html.twig', array(
            'pagination'    => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));

    }



    /**
	 * @Secure(roles="ROLE_MEDICINE_STOCK")
	 */


	public function itemShortListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
	    $data['minQnt'] = 'minimum';
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $em->getRepository('MedicineBundle:MedicineStock')->findWithShortListSearch($config,$data);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
        return $this->render('MedicineBundle:MedicineStock:shortList.html.twig', array(
            'pagination' => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }


	/**
	 * @Secure(roles="ROLE_MEDICINE_STOCK")
	 */


	public function stockItemHistoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
	    $pagination = '';
	    $entity = '';
	    if(!empty($data['name']) and $data['report'] == 'Purchase'){
		    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'name'=> $data['name']));
		    $entities = $em->getRepository('MedicineBundle:MedicineStock')->getPurchaseDetails($config,$entity);
		    $pagination = $this->paginate($entities);
	    }
        if(!empty($data['name']) and $data['report'] == 'Purchase-Return'){
		    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'name'=> $data['name']));
		    $entities = $em->getRepository('MedicineBundle:MedicineStock')->getPurchaseReturnDetails($config,$entity);
		    $pagination = $this->paginate($entities);
	    }
        if(!empty($data['name']) and $data['report'] == 'Sales'){
		    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'name'=> $data['name']));
		    $entities = $em->getRepository('MedicineBundle:MedicineStock')->getSalesDetails($config,$entity);
		    $pagination = $this->paginate($entities);
	    }
        if(!empty($data['name']) and $data['report'] == 'Sales-Return'){
		    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'name'=> $data['name']));
		    $entities = $em->getRepository('MedicineBundle:MedicineStock')->getSalesReturnDetails($config,$entity);
		    $pagination = $this->paginate($entities);
	    }
        if(!empty($data['name']) and $data['report'] == 'Damage'){
		    $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'name'=> $data['name']));
		    $entities = $em->getRepository('MedicineBundle:MedicineStock')->getDamageDetails($config,$entity);
		    $pagination = $this->paginate($entities);
	    }

        return $this->render('MedicineBundle:MedicineStock:itemDetails.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'searchForm' => $data,
        ));
    }

	/**
	 * @Secure(roles="ROLE_MEDICINE_STOCK")
	 */

    public function newAction()
    {

		$entity = new MedicineStock();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineStock:medicine.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function accessoriesAction()
    {
        $entity = new MedicineStock();
        $form = $this->createCreateAccessoriesForm($entity);
        return $this->render('MedicineBundle:MedicineStock:accessories.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new MedicineStock entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineStock();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if(empty($data['medicineId'])) {
            $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockNonMedicine($config, $entity->getName());
        }else{
            $medicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($data['medicineId']);
            $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockMedicine($config, $medicine);
        }
        if ($form->isValid() and empty($checkStockMedicine)){
            $entity->setMedicineConfig($config);
            if(empty($data['medicineId'])){
                if($entity->getAccessoriesBrand()) {
                    $brand = $entity->getAccessoriesBrand();
                    $entity->setBrandName($brand->getName());
                    $entity->setMode($brand->getParticularType()->getSlug());
                }
            }else{
                $entity->setMedicineBrand($medicine);
                $name = $medicine->getMedicineForm().' '.$medicine->getName().' '.$medicine->getStrength();
                $entity->setName($name);
                $entity->setBrandName($medicine->getMedicineCompany()->getName());
                $entity->setMode('medicine');
            }
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stock_new'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Required or Duplicate has been exist"
        );
        return $this->render('MedicineBundle:MedicineStock:medicine.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function accessoriesCreateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = new MedicineStock();
        $form = $this->createCreateAccessoriesForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {

            $entity->setMedicineConfig($config);
            $brand = $entity->getAccessoriesBrand();
            $entity->setBrandName($brand->getName());
            $entity->setMode($brand->getParticularType()->getSlug());
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stock'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        return $this->render('MedicineBundle:MedicineStock:accessories.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MedicineStock entity.
     *
     * @param MedicineStock $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineStock $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new MedicineStockType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stock_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createCreateAccessoriesForm(MedicineStock $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new AccessoriesStockType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stock_accessories_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


	/**
	 * @Secure(roles="ROLE_MEDICINE_STOCK")
	 */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }
        if($entity->getMode() =='accessories'){
            $editForm = $this->createEditAccessoriesForm($entity);
            $template = 'accessories';
        }else{
            $editForm = $this->createEditForm($entity);
            $template = 'medicine';
        }
        return $this->render('MedicineBundle:MedicineStock:'.$template.'.html.twig', array(
            'entity'            => $entity,
            'formShow'          => 'show',
            'form'              => $editForm->createView(),
        ));
    }
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig' => $config,'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }
        return $this->render('MedicineBundle:MedicineStock:show.html.twig', array(
            'entity'            => $entity,
        ));
    }

    /**
     * Creates a form to edit a MedicineStock entity.
     *
     * @param MedicineStock $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicineStock $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new MedicineStockType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
     private function createEditAccessoriesForm(MedicineStock $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new AccessoriesStockType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing MedicineStock entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }

        if($entity->getMode() =='accessories'){
            $editForm = $this->createEditAccessoriesForm($entity);
            $template = 'accessories';
        }else{
            $editForm = $this->createEditForm($entity);
            $template = 'medicine';
        }
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stock'));
        }

        return $this->render('MedicineBundle:MedicineStock:'.$template.'.html.twig', array(
            'entity'            => $entity,
            'form'              => $editForm->createView(),
        ));
    }

    /**
	 * @Secure(roles="ROLE_MEDICINE_STOCK")
	 */

    public function deleteAction(Request $request , MedicineStock $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $msg = 'invalid';
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $msg = "success";
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
            $msg = "invalid";
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
            $msg = "invalid";
        }
        return new Response($msg);
       // $referer = $request->headers->get('referer');
       // return new RedirectResponse($referer);
    }

    public function rackSelectAction()
    {
        $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig' => $inventory, 'particularType' => 1,'status'=>1),array('name'=>'ASC'));
        $items  = array();
        foreach ($entity as $row){
            $items[]= array('value' => $row->getId(),'text' => $row->getName());
        }
        return new JsonResponse($items);
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request , MedicineStock $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * Status a Page entity.
     *
     */
    public function isWebAction(Request $request,MedicineStock $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findOneBy(array('medicineConfig'=> $config,'id' => $entity->getId()));
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->isWeb();
        if($status == 1){
            $entity->setIsWeb(0);
        } else{
            $entity->setIsWeb(1);
            $this->getDoctrine()->getRepository('EcommerceBundle:Item')->insertCopyMedicineItem($entity);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        if('openingQuantity' == $data['name']) {
            $setField = 'set' . $data['name'];
            $quantity = abs($data['value']);
            $entity->$setField($quantity);
            $remainingQuantity = $entity->getRemainingQuantity() + $quantity;
            $entity->setRemainingQuantity($remainingQuantity);
        }elseif('rackNo' == $data['name']){
            $rackNo = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->find($data['value']);
            $entity->setRackNo($rackNo);
        }else{
            $setField = 'set' . $data['name'];
            $entity->$setField(abs($data['value']));
        }
        $em->flush();
        exit;

    }

    public function autoSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoPurchaseStockSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchAutoPurchaseStock($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoNameSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchNameAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchNameAction($stock)
    {
        return new JsonResponse(array(
            'id'=>  $stock,
            'text'=> $stock
        ));
    }
    public function searchStockItemAction(MedicineStock $stock)
    {
        return new JsonResponse(array(
            'id' => $stock->getId(),
            'text' => $stock->getName()
        ));
    }
    public function autoSearchBrandAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchAutoCompleteBrandName($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchBrandNameAction($brand)
    {
        return new JsonResponse(array(
            'id' => $brand,
            'text' => $brand
        ));
    }

    public function stockPriceUpdateAction()
    {
	    set_time_limit(0);
	    ignore_user_abort(true);

	    $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
	    $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findBy(array('medicineConfig'=>$config));

	    /* @var $item MedicineStock */
	    foreach ($entities as $item){

		    $percentage = floatval(12.50);
		    $purchasePrice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->stockInstantPurchaseItemPrice($percentage,$item->getSalesPrice());
		    $item->setPurchasePrice($purchasePrice);
		    $em->persist($item);
		    $em->flush();
	    }
	    return $this->redirect($this->generateUrl('medicine_stock'));
    }

    public function stockUpdatepaginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            500  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }


    public function stockQuantityUpdateAction()
	{
		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $data = [];
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
		/* @var MedicineStock $item */
		foreach ($pagination as $item){
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'sales');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'sales-return');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'purchase-return');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item,'damage');
		}
		return $this->redirect($this->generateUrl('medicine_stock'));
	}



}
