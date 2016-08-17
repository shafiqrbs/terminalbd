<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Form\InventoryConfigType;

/**
 * InventoryConfig controller.
 *
 */
class InventoryConfigController extends Controller
{

    /**
     * Lists all InventoryConfig entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:InventoryConfig')->findAll();

        return $this->render('InventoryBundle:InventoryConfig:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function excelDataImportFormAction(InventoryConfig $inventory)
    {
        //echo $inventoryConfig->getId();
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('InventoryBundle:ProductImport')->getMasterItem();
        $variant = $em->getRepository('InventoryBundle:ProductImport')->getColorSizeUnit('unit');
        $color = $em->getRepository('InventoryBundle:ProductImport')->getColor();
        $size = $em->getRepository('InventoryBundle:ProductImport')->getSize();
        $vendor = $em->getRepository('InventoryBundle:ProductImport')->getVendor();

        //var_dump($color);
        //$vendor = $em->getRepository('InventoryBundle:ProductImport')->insertVendor($inventory,$vendor);
        //$variant = $em->getRepository('InventoryBundle:ProductImport')->insertVariant($inventory,'unit',$color);
         $em->getRepository('InventoryBundle:ProductImport')->insertColor($inventory,$color);
         $em->getRepository('InventoryBundle:ProductImport')->insertSize($inventory,$size);
        //$entities = $em->getRepository('InventoryBundle:ProductImport')->masterItemAdd($inventory,$data);
        exit;
        return $this->render('InventoryBundle:InventoryConfig:dataImport.html.twig', array(
            'entities' => $data,
        ));

    }



    /**
     * Creates a new InventoryConfig entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new InventoryConfig();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $data = $request->request->all();
            if($data['brandvendor'] == 'vendor'){
                $entity->getIsVendor(true);
                $entity->getIsBrand(false);
            }elseif($data['brandvendor'] == 'vendor'){
                $entity->getIsVendor(false);
                $entity->getIsBrand(true);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('inventoryconfig_show', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:InventoryConfig:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a InventoryConfig entity.
     *
     * @param InventoryConfig $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(InventoryConfig $entity)
    {
        $form = $this->createForm(new InventoryConfigType(), $entity, array(
            'action' => $this->generateUrl('inventoryconfig_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new InventoryConfig entity.
     *
     */
    public function newAction()
    {
        $entity = new InventoryConfig();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:InventoryConfig:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a InventoryConfig entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:InventoryConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InventoryConfig entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:InventoryConfig:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_CONFIG")
     */

    public function editAction()
    {

        $entity = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:InventoryConfig:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a InventoryConfig entity.
    *
    * @param InventoryConfig $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(InventoryConfig $entity)
    {
        $form = $this->createForm(new InventoryConfigType(), $entity, array(
            'action' => $this->generateUrl('inventoryconfig_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_CONFIG")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:InventoryConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InventoryConfig entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $data = $request->request->all();
            if($data['brandvendor'] == 'vendor'){
                $entity->setIsVendor(true);
                $entity->setIsBrand(false);
            }elseif($data['brandvendor'] == 'brand'){
                $entity->setIsVendor(false);
                $entity->setIsBrand(true);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('inventoryconfig_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:InventoryConfig:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a InventoryConfig entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventoryconfig_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
