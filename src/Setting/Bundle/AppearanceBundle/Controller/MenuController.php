<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\AppearanceBundle\Form\MenuType;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Menu controller.
 *
 */
class MenuController extends Controller
{

    /**
     * Lists all GlobalOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findAll();

        return $this->render('SettingAppearanceBundle:Menu:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all Menu manage entities.
     *
     */
    public function menuManageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getUser()->getGlobalOption()->getId();
        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption'=>$id));
        $form = $this->createEditForm($id);
        return $this->render('SettingAppearanceBundle:Menu:edit.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Lists all menu modify entities.
     *
     */
    public function modifyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingAppearanceBundle:Menu')->findBy(array('globalOption'=>$globalOption),array('sorting'=>'asc'));
        $form = $this->createEditForm($globalOption->getMenu());
        return $this->render('SettingAppearanceBundle:Menu:edit.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a form to edit a Menu entity.
     *
     * @param Menu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($id)
    {
        $form = $this->createForm(new MenuType(), Null, array(
            'action' => $this->generateUrl('menu_update', array('id' => $id)),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Edits an existing MenuGroup entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->updateMenu($data);
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
    public function stopMenuAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

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
        return $this->redirect($this->generateUrl('menu'));
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction( $globalOption, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));

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
        return $this->redirect($this->generateUrl('menu_manage'));
    }

    public function resetMenuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $globalOption->getId();
        $entities = $em->getRepository('SettingAppearanceBundle:MenuCustom')->findAll();
        foreach( $entities as $custom){

            $exist = $em->getRepository('SettingAppearanceBundle:Menu')->findOneBy(array('globalOption'=>$globalOption,'menuCustom' => $custom->getId()));

            if(empty($exist)){
                $menu = new Menu();
                $menu->setGlobalOption($globalOption);
                $menu->setMenuCustom($custom);
                $menu->setMenu($custom->getMenu());
                $menu->setMenuSlug($globalOption->getSlug().'-'.$custom->getSlug());
                $menu->setSlug($custom->getSlug());
                $menu->setStatus(0);
                $em->persist($menu);
                $em->flush($menu);
            }
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Reset menu has been updated successfully"
        );
        return $this->redirect($this->generateUrl('menu_manage'));
    }





}
