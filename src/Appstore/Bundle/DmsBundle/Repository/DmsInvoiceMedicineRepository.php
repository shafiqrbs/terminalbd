<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Appstore\Bundle\DmsBundle\Controller\InvoiceController;
use Appstore\Bundle\DmsBundle\Entity\AdmissionPatientDmsParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceMedicine;
use Appstore\Bundle\DmsBundle\Entity\Invoice;
use Appstore\Bundle\DmsBundle\Entity\InvoiceDmsParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * DmsInvoiceParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsInvoiceMedicineRepository extends EntityRepository
{
    public function insertInvoiceMedicine(DmsInvoice $invoice, $data)
    {

        if(!empty($data['medicine'])){
            $medicine = $this->_em->getRepository('MedicineBundle:MedicineBrand')->find($data['medicine']);
        }elseif(!empty($data['generic'])){
            $medicine = $this->_em->getRepository('MedicineBundle:MedicineBrand')->find($data['generic']);
        }
        $em = $this->_em;
        $entity = new DmsInvoiceMedicine();
        $entity->setMedicineQuantity($em->getRepository('DmsBundle:DmsPrescriptionAttribute')->find($data['medicineQuantity'])->getNameBn());
        $entity->setMedicineDose($em->getRepository('DmsBundle:DmsPrescriptionAttribute')->find($data['medicineDose'])->getNameBn());
        $entity->setMedicineDoseTime($em->getRepository('DmsBundle:DmsPrescriptionAttribute')->find($data['medicineDoseTime'])->getNameBn());
        $entity->setMedicineDuration($data['medicineDuration']);
        $entity->setMedicineDurationType($em->getRepository('DmsBundle:DmsPrescriptionAttribute')->find($data['medicineDurationType'])->getNameBn());
        $entity->setDmsInvoice($invoice);
        $entity->setMedicine($medicine);
        $em->persist($entity);
        $em->flush();

    }

    public function getInvoiceMedicines(DmsInvoice $sales)
    {
        $entities = $sales->getInvoiceMedicines();
        $data = '';
        $i = 1;
        /** @var $entity DmsInvoiceMedicine */
        foreach ($entities as $entity) {
            $data .= '<tr id="medicine-'.$entity->getId().'">';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $entity->getMedicine()->getName() . '</td>';
            $data .= '<td class="numeric" >' . $entity->getMedicineQuantity(). '</td>';
            $data .= '<td class="numeric" >' . $entity->getMedicineDose() .'-'. $entity->getMedicineDoseTime() . '</td>';
            $data .= '<td class="numeric" >' . $entity->getMedicineDuration(). $entity->getMedicineDurationType() . '</td>';
            $data .= '<td class="numeric" >
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/dms/invoice/'. $entity->getId(). '/medicine-delete" href="javascript:" class="btn red mini deleteMedicine" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

}
