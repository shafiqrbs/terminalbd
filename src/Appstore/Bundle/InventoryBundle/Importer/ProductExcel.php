<?php

namespace Appstore\Bundle\InventoryBundle\Importer;

use Appstore\Bundle\InventoryBundle\Entity\ExcelImporter;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\Product;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class ProductExcel
{
    use ContainerAwareTrait;

    /* @var $excelImport  ExcelImporter */
    protected $excelImport;


    private $data = array();

    public function isValid($data) {

        return true;

    }

    public function import($data)
    {
        $this->data = $data;
        $inventory = $this->excelImport->getInventoryConfig();
        foreach($this->data as $key => $item) {

            $name = ucfirst(strtolower($item['ProductName']));
          //  $productID = $item['ProductID'];
            $productOld = $this->getDoctrain()->getRepository('InventoryBundle:Item')->findOneBy(array('inventoryConfig' => $inventory,'name' => $name));
            if(empty($productOld)){
                $salesPrice = empty($item['SalesPrice']) ? 0 : $item['SalesPrice'];
                $purchasePrice = empty($item['PurchasePrice']) ? 0 : $item['PurchasePrice'];
                $unit = empty($item['Unit']) ? 'Pcs' : $item['Unit'];
                $barcode = empty($item['Barcode']) ? '' : $item['Barcode'];
                $product = new Item();
                $product->setInventoryConfig($inventory);
                $product->setName($name);
                $product->setSalesPrice($salesPrice);
                $product->setPurchasePrice($purchasePrice);
                $product->setBarcode($barcode);
                if ($name) {
                    $master = $this->getMasterItem($name,$unit);
                    $product->setMasterItem($master);
                }
                $this->save($product);
            }

        }

    }

    private function getMasterItem($item,$unit)
    {
        $inventory = $this->excelImport->getInventoryConfig();
        $masterRepository = $this->getProductRepository();

        $product = $masterRepository->findOneBy(array(
            'inventoryConfig'   => $inventory,
            'name'              => $item
        ));
        if($product){
            return $product;
        }else{
            $product = new Product();
            $product->setName($item);
            $product->setInventoryConfig($inventory);
            $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
            if(!empty($unit)){
                $product->setProductUnit($unit);
            }
            $product = $this->save($product);
            return $product;
        }
    }

    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function setExcelImport($excelImport)
    {
        $this->excelImport = $excelImport;
    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }


    private function persist($entity){
        $this->getEntityManager()->persist($entity);
    }

    private function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private function getDoctrain()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return  @return \Appstore\Bundle\InventoryBundle\Repository\ProductRepository
     */
    private function getProductRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:Product');
    }


}