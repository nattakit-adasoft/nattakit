<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Level_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('eticket/level/mLevel', 'mLevel');
        $this->load->library("session");
    }

    public function FSxCLVL($nLocID) {
        $oHeader = $this->Level_model->FSxMLVLHeader($nLocID);
        $oArea = $this->Level_model->FSxMLVLArea($nLocID);
        $oAuthen = FCNaHCheckAlwFunc('EticketBranch');
        $this->load->view('eticket/level/wLevel', array(
            'oAuthen' => $oAuthen,
            'oHeader' => $oHeader,
            'oArea' => $oArea,
            'nLocID' => $nLocID
        ));
    }

    /**
     * FS แสดงข้อมูล Table ในชั้น
     */
    public function FSxCLVLList() {
        $tFTLvlName = $this->input->post('tFTLvlName');
        $nPageNo = $this->input->post('nPageNo');
        $nLocID = $this->input->post('nLocID');
        $nPageActive = $nPageNo;
        $oLvlList = $this->Level_model->FSaMLVLList($tFTLvlName, $nLocID, $nPageActive);
        $oAuthen = FCNaHCheckAlwFunc('EticketBranch');
        $this->load->view('eticket/level/wLevelList', array(
            'oAuthen' => $oAuthen,
            'oLvlList' => $oLvlList
        ));
    }

    public function FStCLVLCount() {
        $tFTLvlName = $this->input->post('tFTLvlName');
        $nLocID = $this->input->post('nLocID');
        $oParkCntSh = $this->Level_model->FStMLVLCount($tFTLvlName, $nLocID);
        $tParkCount = $oParkCntSh [0]->counts;
        echo $tParkCount;
    }

    /**
     * FS เพิ่มข้อมูลชั้น
     */
    public function FSxCLVLAdd($nLocID) {
        $oHeader = $this->Level_model->FSxMLVLHeader($nLocID);
        $oArea = $this->Level_model->FSxMLVLArea($nLocID);
        $this->load->view('eticket/level/wAdd', array(
            'oHeader' => $oHeader,
            'oArea' => $oArea,
            'nLocID' => $nLocID
        ));
    }

    public function FSxCLVLAddAjax() {
        if ($this->input->post('oetFTLevName')) {
            $aData = array(
                'FTLevName' => $this->input->post('oetFTLevName'),
                'FNLocID' => $this->input->post('ohdFNLocID')
            );
            $tLvlID = $this->Level_model->FSxMLVLAdd($aData);
            echo $tLvlID;
        }
    }

    /**
     * FS แก้ไขชั้น
     */
    public function FSxCLVLEdit($nLocID, $nLvlID) {
        $oHeader = $this->Level_model->FSxMLVLHeader($nLocID);
        $oArea = $this->Level_model->FSxMLVLArea($nLocID);
        $oEdit = $this->Level_model->FSxMLVLShowEdit($nLvlID);
        $this->load->view('eticket/level/wEdit', array(
            'oHeader' => $oHeader,
            'oArea' => $oArea,
            'nLocID' => $nLocID,
            'nLvlID' => $nLvlID,
            'oEdit' => $oEdit
        ));
    }

    public function FSxCLVLEditAjax() {
        if ($this->input->post('ohdFNLevID')) {
            $tData = array(
                'FTLevName' => $this->input->post('oetFTLevName'), // ชื่อชั้น
                'FNLevID' => $this->input->post('ohdFNLevID')
            ); // รหัสอ้างอืงสถานที่
            $tDB = $this->Level_model->FSxMLVLEdit($tData);
            echo $tDB;
        }
    }

    /**
     * FS ลบชั้น
     */
    public function FSxCLVLDel() {
        if ($this->input->post('tLevID')) {
            $ocbListItem = $this->input->post('tLevID');
            $aCode = explode(',', $ocbListItem);
            foreach ($aCode as $key => $oValue) {
                $aData = array(
                    'FNLevID' => $oValue
                );
                $o = $this->Level_model->FSxMLVLDel($aData);
                $aJson = array(
                    'count' => $o,
                    'msg' => language('ticket/center/center', 'CheckDel')
                );
                echo json_encode($aJson);
            }
        }
    }

    public function FStCLVLCheck() {
        if ($this->input->post('oetFTLevName')) {
            $tData = array(
                'FTLevName' => $this->input->post('oetFTLevName'),
                'FNLocID' => $this->input->post('ohdFNLocID')
            );
            $tCheck = $this->Level_model->FStMLVLCheck($tData);

            if (@$tCheck [0]->counts > 0) {
                echo 'false';
            } else {
                echo 'true';
            }
        }
    }

}
