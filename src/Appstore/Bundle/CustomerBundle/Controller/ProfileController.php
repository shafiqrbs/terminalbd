<?php

namespace Appstore\Bundle\CustomerBundle\Controller;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Form\MemberEditProfileType;
use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\DomainEditSignType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



/**
 * DomainUser controller.
 *
 */
class ProfileController extends Controller
{



    /**
     * Finds and displays a DomainUser entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DomainUser entity.');
        }
        return $this->render('CustomerBundle:Profile:show.html.twig', array(
            'user'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function editAction()
    {
        $user = $this->getUser();
        $profile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(),'user' => $user->getId()));
        $editForm = $this->createEditForm($profile);
        $globalOption = $this->getUser()->getGlobalOption();
        return $this->render('CustomerBundle:Profile:edit.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $profile,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a DomainUser entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Customer $profile)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new MemberEditProfileType($globalOption,$location), $profile, array(
            'action' => $this->generateUrl('domain_update', array('id' => $profile->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing DomainUser entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DomainUser entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('domain_edit', array('id' => $id)));
        }
        return $this->render('CustomerBundle:Profile:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * Deletes a DomainUser entity.
     *
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setIsDelete(true);
        $user->setEnabled(false);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('domain_user'));
    }



    /**
     * Creates a form to edit a DomainUser entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditProfileForm(User $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $designation = $this->getDoctrine()->getRepository('SettingToolBundle:Designation');
        $form = $this->createForm(new DomainEditSignType($globalOption,$location,$designation), $entity, array(
            'action' => $this->generateUrl('domain_update_profile'),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function editProfileAction()
    {
        $user = $this->getUser();
        $editForm = $this->createEditProfileForm($user);
        $globalOption = $user->getGlobalOption();
        return $this->render('CustomerBundle:Profile:profile.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $user,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing DomainUser entity.
     *
     */
    public function updateProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DomainUser entity.');
        }

        $editForm = $this->createEditProfileForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('domain_edit_profile'));
        }

        return $this->render('CustomerBundle:Profile:profile.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }


    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('UserBundle:User')->searchAutoComplete($item,$go);
        }
        return new JsonResponse($item);
    }

    public function searchUserNameAction($user)
    {
        return new JsonResponse(array(
            'id' => $user,
            'text' => $user
        ));
    }

    public function forgetPasswordAction(User $user)
    {
        $password = '*4848#';
        $user->setPlainPassword($password);
        $this->get('fos_user.user_manager')->updateUser($user);
        $this->get('session')->getFlashBag()->add(
            'success',"Password reset successfully"
        );

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$password));
        return $this->redirect($this->generateUrl('domain_edit', array('id' => $user->getId())));
    }

  

}
