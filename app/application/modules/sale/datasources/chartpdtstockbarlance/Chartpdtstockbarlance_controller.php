<?php
require APPPATH."libraries/koolreport/autoload.php";

class Chartpdtstockbarlance_controller extends \koolreport\Koolreport {
    use \koolreport\clients\jQuery;
    use \koolreport\clients\Bootstrap;

    public function settings(){
        return array(
            "assets"=>array(
                "path"  => "../../assets/koolreport",
                "url"   => base_url()."application/modules/sale/assets/koolreport"
            ),
            "dataSources"   =>array(
                "DataReport"    =>array(
                    "class"         =>  "\koolreport\datasources\ArrayDataSource",
                    "data"          =>  $this->params["aDataReturn"],
                    "dataFormat"    =>  "associate"
                )
            )
        );
    }

    protected function setup(){
        $this->src('DataReport')->pipe($this->dataStore('ChartPdtStockBarlanceData'));
    }
}
