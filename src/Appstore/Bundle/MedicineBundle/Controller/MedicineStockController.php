<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\AccessoriesStockType;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


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
     * Lists all MedicineStock entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findWithSearch($config,$data);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:MedicineStock:index.html.twig', array(
            'pagination'    => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));

    }

    public function itemShortListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
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
                'class' => 'horizontal-form',
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
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing MedicineStock entity.
     *
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
                'class' => 'horizontal-form',
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
                'class' => 'horizontal-form',
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
     * Deletes a MedicineStock entity.
     *
     */
    public function deleteAction(MedicineStock $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('medicine_stock'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(MedicineStock $entity)
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
        return $this->redirect($this->generateUrl('medicine_stock'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineStock')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        if('openingQuantity' == $data['name']){
            $setField = 'set'.$data['name'];
            $quantity =abs($data['value']);
            $entity->$setField($quantity);
            $remainingQuantity = $entity->getRemainingQuantity()+$quantity;
            $entity->setRemainingQuantity($remainingQuantity);
        }else{
            $setField = 'set' . $data['name'];
            $entity->$setField(abs($data['value']));
        }
        $em->flush();
        exit;

    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoNameSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->searchNameAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchNameAction($stock)
    {
        return new JsonResponse(array(
            'id'=>$stock,
            'text'=>$stock
        ));
    }
    public function autoSearchBrandAction(Request $request)
    {
        $item = $_REQUEST['q'];
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
}
