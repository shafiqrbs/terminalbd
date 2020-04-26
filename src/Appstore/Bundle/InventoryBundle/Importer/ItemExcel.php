<?php

namespace Appstore\Bundle\InventoryBundle\Importer;


use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Appstore\Bundle\EcommerceBundle\Entity\ItemImport;
use Product\Bundle\ProductBundle\Entity\Category;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ItemExcel
{
    use ContainerAwareTrait;

    /* @var $itemImport ItemImport */
    protected $itemImport;

    private $data = array();
    private $cache = array();

    public function import($data)
    {
        $this->data = $data;
        $vendor = $this->itemImport->getVendor();
        $config = $this->itemImport->getEcommerceConfig();

        foreach($this->data as $key => $item) {

            $name = ucfirst(strtolower($item['Name']));
            $productOld = $this->getDoctrain()->getRepository('EcommerceBundle:Item')->findOneBy(array('ecommerceConfig' => $config,'webName' => $name));
            if(empty($productOld) and !empty($item['Name'])){
                $product = new Item();
                $product->setEcommerceConfig($config);
                $product->setName(ucfirst(strtolower($item['Name'])));
                $product->setWebName(ucfirst(strtolower($item['Name'])));
                $product->setQuantity( $item['Quantity']);
                $product->setMasterQuantity($item['Quantity']);
                $product->setPurchasePrice( $item['PurchasePrice']);
                $product->setSalesPrice( $item['SalesPrice']);
                if($vendor){
                    $product->setVendor($vendor);
                }
                 $category = $item['Category'];
                 if($category){
                     $category = $this->getCategory(ucfirst(strtolower($category)));
                     $product->setCategory($category);
                 }
                 $brand = $item['Brand'];
                 if($brand){
                     $brand = $this->getBrand(ucfirst(strtolower($brand)));
                     $product->setBrand($brand);
                 }
                $unit = $item['Unit'];
                if($unit){
                    $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                    $product->setProductUnit($unit);
                }
                $this->save($product);
            }

        }

    }

    private function getCategory($item)
    {
        $config = $this->itemImport->getEcommerceConfig();
        $categoryRepository = $this->getCategoryRepository();

        $category = $categoryRepository->findOneBy(array(
            'ecommerceConfig'   => $config,
            'name'              => $item
        ));
        if($category){
            return $category;
        }else{
            $category = new Category();
            $category->setName($item);
            $category->setEcommerceConfig($config);
            $category = $this->save($category);
            return $category;
        }


    }

    private function getBrand($item)
    {

        $config = $this->itemImport->getEcommerceConfig();
        $brandRepository = $this->getBrandRepository();

        $brand = $brandRepository->findOneBy(array(
            'ecommerceConfig'   => $config,
            'name'              => $item
        ));
        if($brand){
            return $brand;
        }else{
            $brand = new ItemBrand();
            $brand->setName($item);
            $brand->setEcommerceConfig($config);
            $brand = $this->save($brand);
            return $brand;
        }

    }

    public function setItemImport($itemImport)
    {
        $this->itemImport = $itemImport;
    }

    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
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
     * @return  @return \Appstore\Bundle\EcommerceBundle\Repository\ItemBrandRepository
     */
    private function getBrandRepository()
    {
        return $this->getDoctrain()->getRepository('EcommerceBundle:ItemBrand');
    }

    /**
     * @return  @return \Product\Bundle\ProductProductBundle\Entity\CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getDoctrain()->getRepository('ProductProductBundle:Category');
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

        if (empty($item['Brand'])) {
            $item['Brand'] = $defaultStr;
        }

        if (empty($item['Category'])) {
            $item['Category'] = $defaultStr;
        }


        if (empty($item['Quantity'])) {
            $item['Quantity'] = 1;
        }

        if (empty($item['Unit'])) {
            $item['Unit'] = $defaultStr;
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