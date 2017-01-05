<?php

namespace Appstore\Bundle\InventoryBundle\Importer;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\ItemBrand;
use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\InventoryBundle\Entity\StockItem;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Appstore\Bundle\InventoryBundle\Entity\Product;

class Excel
{
    use ContainerAwareTrait;

    protected $inventoryConfig;
    private $data = array();
    private $cache = array();

    public function import($data)
    {
        $this->data = $data;

        foreach($this->data as $key => $item) {

            $item = $this->senatizeItemData($key, $item, 'Default');

//            if($item['Size'] != 16) {
//                continue;
//            }

            $purchaseItem = new PurchaseItem();
            $purchaseItem->setItem($this->getItem($item));
            $purchaseItem->setPurchase($this->getPurchase($item));
            $purchaseItem->setQuantity($item['Quantity']);
            $purchaseItem->setPurchasePrice($item['PurchasePrice']);
            $purchaseItem->setSalesPrice($item['SalesPrice']);
            $purchaseItem->setPurchaseVendorItem($this->getPurchaseVendorItem($item));
            $this->persist($purchaseItem);

            $stockItem = new StockItem();
            $stockItem->setInventoryConfig($this->getInventoryConfig());
            $stockItem->setPurchaseItem($purchaseItem);
            $stockItem->setItem($purchaseItem->getItem());
            $stockItem->setQuantity($purchaseItem->getQuantity());
            $stockItem->setProcess('purchase');

            $this->persist($stockItem);
            $this->flush();

        }
    }

    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }


    private function getItem($item)
    {
        $key = $item['ProductName'] . "_" . $item['Color'] . "_" . $item['Size'] . "_" . $item['Vendor'] . "_" . $item['Category'];

        $itemObj = $this->getCachedData('Item', $key);

        if($itemObj == NULL) {

            $masterItem = $this->getMasterItem($item);
            $itemSize = $this->getSize($item);
            $itemColor = $this->getColor($item);
            $vendor = $this->getVendor($item);
            $brand = $this->getBrand($item);

            $itemObj = $this->checkFindItem($item);

            if($itemObj == NULL) {
                $itemObj = new Item();
                $itemObj->setName($this->sentence_case($item['ProductName']));
                $itemObj->setMasterItem($masterItem);
                //if($this->getInventoryConfig()->getIsColor() == 1) {
                    $itemObj->setColor($itemColor);
                //}
                //if($this->getInventoryConfig()->getIsSize() == 1) {
                    $itemObj->setSize($itemSize);
                //}
                //if($this->getInventoryConfig()->getIsVendor() == 1) {
                    $itemObj->setVendor($vendor);
                //}
                //if($this->getInventoryConfig()->getIsBrand() == 1) {
                    $itemObj->setBrand($brand);
                //}
                $itemObj->setInventoryConfig($this->getInventoryConfig());
                $itemObj = $this->save($itemObj);
            }

            $this->setCachedData('Item', $key, $itemObj);
        }

        return $itemObj;
    }

    private function checkFindItem($item)
    {
        $repository = $this->getItemRepository();

        $masterItem = $this->getMasterItem($item);
        $itemSize = $this->getSize($item);
        if($this->getInventoryConfig()->getIsSize() == 1){
            $itemSize     = $itemSize;
        }else{
            $itemSize     ='NULL';
        }

        $itemColor = $this->getColor($item);
        if($this->getInventoryConfig()->getIsColor() == 1){
            $itemColor     = $itemColor;
        }else{
            $itemColor     ='NULL';
        }

        $vendor = $this->getVendor($item);
        if($this->getInventoryConfig()->getIsVendor() == 1){
            $vendor     = $vendor;
        }else{
            $vendor     ='NULL';
        }
        $brand = $this->getBrand($item);
        if($this->getInventoryConfig()->getIsBrand() == 1){
            $brand     = $brand;
        }else{
            $brand     ='NULL';
        }

        $itemObj = $repository->findOneBy(array(
            'name'            => $this->sentence_case($item['ProductName']),
            'masterItem'      => $masterItem,
            'size'            => $itemSize,
            'color'           => $itemColor,
            'brand'           => $brand,
            'vendor'          => $vendor,
            'inventoryConfig' => $this->getInventoryConfig(),
        ));

        return $itemObj;
    }

    private function getPurchaseVendorItem($item)
        {
            $key = $item['ProductName'] . "_" . $item['Vendor'] . "_" . $item['PurchasePrice']. "_" . $item['Memo'] ;

           /* if($item['ProductName'] =='Ladies Tops'){
               var_dump($item); die();
            }*/

            $itemObj = $this->getCachedData('PurchaseVendorItem', $key);

            $repository = $this->getPurchaseVendorItemRepository();


            if ($itemObj == NULL) {

                $purchase = $this->getPurchase($item);
                $itemObj = $repository->findOneBy(array(
                    'name' => $this->sentence_case($item['ProductName']),
                    'purchasePrice' => $item['PurchasePrice'],
                    'purchase' => $purchase
                ));

                if ($itemObj == NULL) {
                    $itemObj = new PurchaseVendorItem();
                    $itemObj->setName($this->sentence_case($item['ProductName']));
                    $itemObj->setPurchase($purchase);
                    $itemObj->setSource('inventory');
                    $itemObj->setInventoryConfig($purchase->getInventoryConfig());
                    $itemObj->setPurchasePrice($item['PurchasePrice']);
                    $itemObj->setSalesPrice($item['SalesPrice']);
                    $itemObj->setWebPrice($item['SalesPrice']);
                    $itemObj->setQuantity((int)$item['Quantity']);
                    $itemObj = $this->save($itemObj);
                } else {
                    $itemObj->setQuantity($itemObj->getQuantity() + (int)$item['Quantity']);
                }

                $this->setCachedData('PurchaseVendorItem', $key, $itemObj);

            } else {

                $itemObj->setQuantity($itemObj->getQuantity() + (int)$item['Quantity']);
                $this->setCachedData('PurchaseVendorItem', $key, $itemObj);
            }

            return $itemObj;
        }

    private function getMasterItem($item)
    {
        $key = $item['ProductName'] . "_" . $item['Category'];
        $masterItem = $this->getCachedData('MasterItem', $key);
        $category = $this->getCategory($item);

        $masterItemRepository = $this->getMasterItemRepository();

        if($masterItem == NULL) {
            $masterItem = $masterItemRepository->findOneBy(array(
                'name' => $this->sentence_case($item['ProductName']),
                'category' => $category,
                'inventoryConfig' => $this->getInventoryConfig()
            ));

            if($masterItem == NULL){
                $masterItem = new Product();
                $masterItem->setName($this->sentence_case($item['ProductName']));
                $masterItem->setCategory($category);
                $masterItem->setInventoryConfig($this->getInventoryConfig());
                $masterItem = $this->save($masterItem);
            }
            $this->setCachedData('MasterItem', $key, $masterItem);
        }

        return $masterItem;
    }

    private function getColor($item)
    {
        $color = $this->getCachedData('Color', $item['Color']);
        $colorRepository = $this->getColorRepository();
        if($color == NULL) {
            $color = $colorRepository->findOneBy(array(
                'isValid'             => 1,
                'name'                => $item['Color']
            ));
            if($color == NULL) {
                $color = new ItemColor();
                $color->setName($item['Color']);
                $color = $this->save($color);
            }
            $this->setCachedData('Color', $item['Color'], $color);
        }

        return $color;
    }

    private function getSize($item)
    {
        $size = $this->getCachedData('Size', $item['Size']);
        $sizeRepository = $this->getSizeRepository();
        if($size == NULL) {

            $size = $sizeRepository->findOneBy(array(
                'isValid'             => 1,
                'name'                => $item['Size']
            ));

            if($size == null) {
                $size = new ItemSize();
                $size->setName($item['Size']);
                $size = $this->save($size);

            }
            $this->setCachedData('Size', $item['Size'], $size);
        }

        return $size;
    }


    private function getPurchase($item)
    {
        $key = $item['Vendor'] . "_" . $item['Memo'] ;

        $purchase = $this->getCachedData('Purchase', $key);

        $purchaseRepository = $this->getPurchaseRepository();

        if($purchase == NULL) {
            $vendor = $this->getVendor($item);
            $purchase = $purchaseRepository->findOneBy(array(
                'vendor'=> $vendor,
                'memo'=> $item['Memo'],
                'inventoryConfig'=> $this->getInventoryConfig()
            ));

            if($purchase == NULL) {
                $purchase = new Purchase();
                $purchase->setInventoryConfig($this->getInventoryConfig());
                $purchase->setVendor($vendor);
                $purchase->setChalan(1);
                $purchase->setProcess('imported');
                $purchase->setReceiveDate(new \DateTime());
                $purchase->setMemo($item['Memo']);
                $purchase = $this->save($purchase);
            }

            $this->setCachedData('Purchase', $key, $purchase);
        }

        return $purchase;
    }


    private function getCategory($item)
    {
        $category = $this->getCachedData('Category', $item['Category']);
        $categoryRepository = $this->getCategoryRepository();
        if($category == NULL) {
            $category = $categoryRepository->findOneByName($item['Category']);
            $this->setCachedData('Category', $item['Category'], $category);

            if($category == null) {
                $category = new Category();
                $category->setName($item['Category']);
                $category->setInventoryConfig($this->getInventoryConfig());
                $category->setPermission('private');
                $category->setStatus(false);
                $category = $this->save($category);
            }
            $this->setCachedData('Category',  $item['Category'] , $category);
        }

        return $category;

    }

    private function getBrand($item)
    {
        $brand = $this->getCachedData('Brand', $item['Brand']);

        $brandRepository = $this->getBrandRepository();

        if($brand == NULL) {

            $brand = $brandRepository->findOneBy(array(
                'inventoryConfig'   => $this->getInventoryConfig(),
                'name'              => $item['Brand']
            ));

            if($brand == null) {
                $brand = new ItemBrand();
                $brand->setName($item['Brand']);
                $brand->setBrandCode($item['BrandCode']);
                $brand->setInventoryConfig($this->getInventoryConfig());
                $brand = $this->save($brand);
            }

            $this->setCachedData('Brand', $item['Brand'], $brand);
        }

        return $brand;
    }

    private function getVendor($item)
    {
        $vendor = $this->getCachedData('Vendor', $item['Vendor']);

        $vendorRepository = $this->getVendorRepository();

        if($vendor == NULL) {
            $vendor = $vendorRepository->findOneBy(array(
                'inventoryConfig'   => $this->getInventoryConfig(),
                'name'              => $item['Vendor']
            ));
            if($vendor == NULL)  {
                $vendor = new Vendor();
                $vendor->setName($item['Vendor']);
                $vendor->setCompanyName($item['Vendor']);
                $vendor->setVendorCode($item['VendorCode']);
                $vendor->setInventoryConfig($this->getInventoryConfig());
                $vendor = $this->save($vendor);
            }

            $this->setCachedData('Vendor', $item['Vendor'], $vendor);
        }

        return $vendor;
    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\ItemRepository
     */
    private function getItemRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:Item');
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\PurchaseVendorItemRepository
     */
    private function getPurchaseVendorItemRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:PurchaseVendorItem');
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\ProductRepository
     */
    private function getMasterItemRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:Product');
    }

    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    private function persist($entity){
        $this->getEntityManager()->persist($entity);
    }

    private function getCachedData($type, $key)
    {
        if(isset($this->cache[$type][$key])){
            return $this->cache[$type][$key];
        }

        return NULL;
    }

    private function setCachedData($type, $key, $value)
    {
        $this->cache[$type][$key] = $value;
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\ItemColorRepository
     */
    private function getColorRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:ItemColor');
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\ItemSizeRepository
     */
    private function getSizeRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:ItemSize');
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\VendorRepository
     */
    private function getVendorRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:Vendor');
    }

    /**
     * @return  @return \Appstore\Bundle\InventoryBundle\Repository\ItemBrandRepository
     */
    private function getBrandRepository()
    {
        return $this->getDoctrain()->getRepository('InventoryBundle:ItemBrand');
    }

    /**
     * @return  @return \Product\Bundle\ProductBundle\Entity\CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getDoctrain()->getRepository('ProductProductBundle:Category');
    }

    private function getInventoryConfig()
    {
        $inventoryConfig = $this->getCachedData('InventoryConfig', $this->inventoryConfig);

        if($inventoryConfig == NULL) {
            $inventoryConfig = $this->getDoctrain()->getRepository('InventoryBundle:InventoryConfig')->find($this->inventoryConfig);
            $this->setCachedData('InventoryConfig', $this->inventoryConfig, $inventoryConfig);
        }

        return $inventoryConfig;
    }

    /**
     * @return \Appstore\Bundle\InventoryBundle\Repository\PurchaseRepository
     */
    private function getPurchaseRepository()
    {
        $colorRepository = $this->getDoctrain()->getRepository('InventoryBundle:Purchase');

        return $colorRepository;
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
     * @param $key
     * @param $item
     * @param $defaultStr
     *
     * @return mixed
     */
    private function senatizeItemData($key, $item, $defaultStr)
    {
        if (empty($item['Size'])) {
            $item['Size'] = $defaultStr;
        }else{
            $item['Size'] = $item['Size'] . "";
        }

        if (empty($item['Color'])) {
            $item['Color'] = $defaultStr;
        }

        if (empty($item['Vendor'])) {
            $item['Vendor'] = $defaultStr;
        }

        if (empty($item['Brand'])) {
            $item['Brand'] = $defaultStr;
        }

        if (empty($item['Category'])) {
            $item['Category'] = $defaultStr;
        }

        if (empty($item['Memo'])) {
            $item['Memo'] = 9999;
        }

        if (empty($item['Quantity'])) {
            $item['Quantity'] = 1;
        }


        $this->data[$key] = $item;

        return $item;
    }

    function sentence_case($string) {
        $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $new_string = '';
        foreach ($sentences as $key => $sentence) {
            $new_string .= ($key & 1) == 0?
                ucfirst(strtolower(trim($sentence))) :
                $sentence.' ';
        }
        return trim($new_string);
    }


}