<?php

namespace Appstore\Bundle\ElectionBundle\Importer;
use Liuggio\ExcelBundle\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class VoterDataReader
{

    use ContainerAwareTrait;

    protected $config;

    /**
     * @var Factory
     */
    private $excelFactory;

    public function __construct(Factory $excelFactory) {
        $this->excelFactory = $excelFactory;
    }

    public function getData($file) {

        $objPHPExcel = $this->excelFactory->createPHPExcelObject($file);

        return $this->getNamedArray($objPHPExcel);
    }

    /**
     * @param \PHPExcel $objPHPExcel
     *
     * @return array
     * @throws \PHPExcel_Exception
     * @internal param $file
     *
     */
    protected function getNamedArray(\PHPExcel $objPHPExcel)
    {
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();

        $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, TRUE, TRUE);
        $headingsArray = $headingsArray[1];

        $namedDataArray = array();

        for ($row = 2; $row <= $highestRow; ++$row) {
            $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, TRUE);

            if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {

                $dataArray = array();

                foreach ($headingsArray as $columnKey => $columnHeading) {
                    $dataArray[$columnHeading] = $dataRow[$row][$columnKey];
                }
                $key = $dataArray['Name'].$dataArray['MobileNo'].$dataArray['FatherName']. $dataArray['MotherName'].$dataArray['NID'].$dataArray['Address']
                       .$dataArray['Village'].$dataArray['PostOffice'].$dataArray['PostalCode'].$dataArray['BloodGroup'].$dataArray['Birthday'];
                $namedDataArray[$key] = $dataArray;

            }

        }
        return $namedDataArray;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private function getDoctrain()
    {
        return $this->container->get('doctrine');
    }




}