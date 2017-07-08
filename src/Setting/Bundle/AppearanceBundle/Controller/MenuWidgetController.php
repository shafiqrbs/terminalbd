<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\ContentBundle\Entity\PageModule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * MenuWidget controller.
 *
 */
class MenuWidgetController extends Controller
{

    /**
     * Lists all MenuWidget entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption'=> $globalOption,'status' => 1));
        return $this->render('SettingAppearanceBundle:MenuWidget:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all MenuWidget entities.
     *
     */
    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:MenuWidget')->findBy(array('globalOption'=> $globalOption),array('sorting'=>'ASC'));
        return $this->render('SettingAppearanceBundle:MenuWidget:sorting.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all MenuWidget entities.
     *
     */
    public function sortedListAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuWidget')->setListOrdering($data);
        exit;
    }



    /**
     * Displays a form to create a new MenuWidget entity.
     *
     */
    public function newAction(Menu $menu)
    {

        /** @var  $pageArr */
        $pageArr = array();

        /** @var PageModule $row */
        foreach ($menu->getPageModules() as $row):
            $pageArr[$row->getModule()->getId()] =  $row;
        endforeach;

        $globalOption = $this->getUser()->getGlobalOption();
        $features = $this->getDoctrine()->getRepository('SettingAppearanceBundle:Feature')->findBy(array('globalOption' => $globalOption));
        return $this->render('SettingAppearanceBundle:MenuWidget:new.html.twig', array(
            'globalOption'    => $globalOption,
            'features'    => $features,
            'featureIds'      => '',
            'menu' => $menu,
            'pageFeature' => $pageArr
        ));
    }

    /**
     * Edits an existing MenuWidget entity.
     *
     */
    public function updateAction(Request $request, Menu $menu)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingContentBundle:PageModule')->createMenuFeatureModule($menu,$data);
        return $this->redirect($this->generateUrl('appearancemenuwidget_new', array('menu' => $menu->getId())));


    }


    /**
     * Deletes a MenuWidget entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingAppearanceBundle:MenuWidget')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
        if (!empty($entity)) {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('appearancefeaturewidget'));
    }

    /**
     * Creates a form to delete a MenuWidget entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('appearancefeaturewidget_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:MenuWidget')->find($id);

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
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('appearancefeaturewidget'));
    }

    public function  featureAction(MenuWidget $entity){

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuWidget entity.');
        }
        return $this->render('SettingAppearanceBundle:MenuWidget:feature.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('item');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuWidget')->setDivOrdering($data);
        exit;

    }

    public function resizeAction(Request $request)
    {
        $data = $request ->request->all();
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:MenuWidget')->setDivResize($data);
        exit;
    }
}
