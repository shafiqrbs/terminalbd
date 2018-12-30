<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Symfony\Component\HttpFoundation\Response;


/**
 * AccountVendor controller.
 *
 */
class VendorController extends Controller
{


    /**
     * Deletes a AccountVendor entity.
     *
     */
    public function deleteAction(AccountVendor $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountVendor entity.');
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

        return $this->redirect($this->generateUrl('inventory_vendor'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

	    $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        exit;

    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
	        $global = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->searchAutoComplete($item,$global);
        }
        return new JsonResponse($item);
    }

    public function searchVendorNameAction($name)
    {
        return new JsonResponse(array(
            'id' => $name,
            'text' => $name
        ));
    }

    public function ledgerAction()
    {
	    $globalOption = $this->getUser()->getGlobalOption();
		$type = $_REQUEST['type'];
		$vendor = $_REQUEST['vendor'];
	    $balance = 0;
	    if(!empty($vendor)){
		    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorSingleOutstanding($globalOption,$type,$vendor);
		    $balance = empty($result) ? 0 : $result;
	    }
	    $taka = number_format($balance).' Taka';
	    return new Response($taka);
	    exit;

    }

}
