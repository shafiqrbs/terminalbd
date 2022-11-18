<?php

namespace Appstore\Bundle\ProcurementBundle\Entity;


use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrder;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * Purchase
 *
 * @ORM\Table(name="pro_requisition")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ProcurementBundle\Repository\RequisitionRepository")
 */
    class Requisition
    {
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\ProcurementConfig", inversedBy="purchaseOrders" )
         * @ORM\JoinColumn(onDelete="CASCADE")
         **/
        private  $config;


        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\RequisitionItem", mappedBy="requisition"  )
         **/
        private  $requisitionItems;


         /**
         * @ORM\ManyToMany(targetEntity="Appstore\Bundle\AssetsBundle\Entity\Club")
         **/
        private  $club;


        /**
         * @Gedmo\Blameable(on="create")
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
         **/
        private  $createdBy;


        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
         **/
        private  $approvedBy;


        /**
         * @var string
         *
         * @ORM\Column(name="remark", type="text", nullable=true)
         */
        private $remark = 0;

         /**
         * @var float
         *
         * @ORM\Column(name="subTotal", type="float", nullable=true)
         */
        private $subTotal = 0;

        /**
         * @var \DateTime
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(name="created", type="datetime")
         */
        private $created;

        /**
         * @var \DateTime
         * @ORM\Column(name="updated", type="datetime", nullable = true)
         */
        private $updated;


        /**
         * Get id
         *
         * @return integer
         */
        public function getId()
        {
            return $this->id;
        }



        /**
         * @return \DateTime
         */
        public function getCreated()
        {
            return $this->created;
        }

        /**
         * @param \DateTime $created
         */
        public function setCreated($created)
        {
            $this->created = $created;
        }

        /**
         * @return \DateTime
         */
        public function getUpdated()
        {
            return $this->updated;
        }

        /**
         * @param \DateTime $updated
         */
        public function setUpdated($updated)
        {
            $this->updated = $updated;
        }


        /**
         * @return mixed
         */
        public function getCreatedBy()
        {
            return $this->createdBy;
        }

        /**
         * @param mixed $createdBy
         */
        public function setCreatedBy($createdBy)
        {
            $this->createdBy = $createdBy;
        }



        /**
         * @return mixed
         */
        public function getApprovedBy()
        {
            return $this->approvedBy;
        }

        /**
         * @param mixed $approvedBy
         */
        public function setApprovedBy($approvedBy)
        {
            $this->approvedBy = $approvedBy;
        }


        /**
         * @return string
         */
        public function getRemark()
        {
            return $this->remark;
        }

        /**
         * @param string $remark
         */
        public function setRemark($remark)
        {
            $this->remark = $remark;
        }



        /**
         * @return ProcurementConfig
         */
        public function getConfig()
        {
            return $this->config;
        }

        /**
         * @param ProcurementConfig $config
         */
        public function setConfig($config)
        {
            $this->config = $config;
        }

        /**
         * @return mixed
         */
        public function getClub()
        {
            return $this->club;
        }

        /**
         * @param mixed $club
         */
        public function setClub($club)
        {
            $this->club = $club;
        }

        /**
         * @return mixed
         */
        public function getRequisitionItems()
        {
            return $this->requisitionItems;
        }


        /**
         * @return float
         */
        public function getSubTotal()
        {
            return $this->subTotal;
        }

        /**
         * @param float $subTotal
         */
        public function setSubTotal($subTotal)
        {
            $this->subTotal = $subTotal;
        }




    }

