<?php
defined('BASEPATH') or exit('No direct script access allowed');

include APPPATH.'third_party/PHPExcel/Classes/PHPExcel.php';
include APPPATH.'third_party/PHPExcel/Classes/PHPExcel/IOFactory.php';
include APPPATH.'third_party/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

class Rptsaleshopbyexpensesshp_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('reportanalysis/Reportanalysis_model');
        $this->load->model('pos5/company/Company_model');
    }

    //Functionality: เช็ค Data Export PDF In DataBase
    //Parameters:  Function Parameter
    //Creator: 13/12/2018 Wasin(Yoshi)
    //Return: object Status Check In DB
    //ReturnType: Object
    public function FSoChkDataExport(){
        try{
            $aDataInputReport   = $this->input->post('oInputCondition');
            





        }catch(Exception $Error){
            echo "Eror Rptsaleshopbyexpensesshp_controller Function(FSoChkDataExport) => ".$Error;
        }
    }








}