<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Showtime_controller extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ticket/showtime/mShowTime', 'mShowTime');
        $this->load->library("session");
    }

    // โหลดรายการสาขา Ajax
    public function FSxCSHT($nEvnID) {
        $oAuthen = FCNaHCheckAlwFunc('EticketEvent');
        $oInfo = $this->Showtime_model->FSxMSHTEventInfo($nEvnID);
        $this->load->view('ticket/showtime/wShowTime', array(
            'oEvent' => $oInfo,
            'oAuthen' => $oAuthen,
            'nEvnID' => $nEvnID
        ));
    }

    public function FSxCSHTLocLoadList() {
        $tFTLocName = $this->input->post('tFTSHTALocName');
        $nPageNo = $this->input->post('nPageNo');
        $nEventID = $this->input->post('nEventID');
        $nPageActive = $nPageNo;
        $oLocList = $this->Showtime_model->FSaMSHTLocList($tFTLocName, $nEventID, $nPageActive);
        $this->load->view('ticket/showtime/wShowTimeLocList', array(
            'oLocList' => $oLocList,
            'nEventID' => $nEventID
        ));
    }

    // นับจำนวนสาขา
    public function FSxCSHTLocCount() {
        $tFTLocName = $this->input->post('tFTSHTALocName');
        $nEventID = $this->input->post('nEventID');
        $oEventCntSh = $this->Showtime_model->FSaMSHTLocCount($tFTLocName, $nEventID);
        $tEventCount = $oEventCntSh [0]->counts;
        echo $tEventCount;
    }

    public function FSxCSHTAdd($nEvnID) {
        $oAuthen = FCNaHCheckAlwFunc('EticketEvent');
        $oPrk = $this->Showtime_model->FSxMSHTShowPrk($nEvnID);
        $this->load->view('ticket/showtime/wAdd', array(
            'oAuthen' => $oAuthen,
            'nEvnID' => $nEvnID,
            'oPrk' => $oPrk
        ));
    }

    public function FSxCSHTLocList() {
        if ($this->input->post('ocmPrkPmoID')) {
            $aData = array(
                'FNPmoID'   => $this->input->post('ocmPrkPmoID'),
                'FTLocName' => $this->input->post('oetLocName'),
                'FNEvnID'   => $this->input->post('tGetEventId')
            );
            $nEventId = $this->input->post('tGetEventId');
            $oLocList = $this->Showtime_model->FSxMSHTLocList($aData);
            $oCheck = $this->Showtime_model->FSxMSHTCheckLocList($nEventId);
            $this->load->view('ticket/showtime/wShowTimeList', array(
                'oLocList' => $oLocList,
                'oCheck' => $oCheck,
                'oEventId' => $this->input->post('tGetEventId')
            ));
        }
    }

    public function FSxCSHTAddLoc() {
        if ($this->input->post('ocbFNLocID')) {
            $ocbFNLocID = $this->input->post('ocbFNLocID');
            $oetFDShwStartDate = $this->input->post('oetFDShwStartDate');
            $oetFDShwEndDate = $this->input->post('oetFDShwEndDate');
            foreach ($ocbFNLocID as $key => $tValue) {
                $aData = array(
                    'FNLocID' => $ocbFNLocID [$key],
                    'FNEvnID' => $this->input->post('ohdFNEvnID')
                );
                $oLocList = $this->Showtime_model->FSxMSHTAddLoc($aData);
            }
            if ($this->input->post('ocmFNPkgID')) {
                $ocbFNPkgID = $this->input->post('ocmFNPkgID');
                foreach ($ocbFNPkgID as $key => $tValue) {
                    $aData = array(
                        'FNPkgID' => $ocbFNPkgID [$key],
                        'FNEvnID' => $this->input->post('ohdFNEvnID')
                    );
                    $oLocList = $this->Showtime_model->FSxMSHTUpdatePkg($aData);
                }
            }
        }
    }

    public function FSxCSHTDelShowTime() {
        if ($this->input->post('nFNEvnID')) {
            $ocbListItem = $this->input->post('nFNLocID');
            $aCode = explode(',', $ocbListItem);
            foreach ($aCode as $key => $oValue) {
                $Pkg = $this->Showtime_model->FSxMSHTChkPkg($oValue);
                $aData = array(
                    'FNEvnID' => $this->input->post('nFNEvnID'),
                    'FNLocID' => $oValue
                );
                $this->Showtime_model->FSxMSHTDelShowTime($aData);
                foreach ($Pkg as $oValue) {
                    $aPkg = array(
                        'FNEvnID' => $this->input->post('nFNEvnID'),
                        'FNPkgID' => $oValue->FNPkgID
                    );
                    $this->Showtime_model->FSxMSHTDelPkg($aPkg);
                }
            }
        }
    }

    public function FSxCSHTShowTimePackageList($nEvnID, $nLocID) {
        $oInfo = $this->Showtime_model->FSxMSHTEventInfo($nEvnID);
        $oPackageList = $this->Showtime_model->FSxMSHTPackageList($nLocID, $nEvnID);
        $oAuthen = FCNaHCheckAlwFunc('EticketEvent');
        $this->load->view('ticket/showtime/package/wPackageList', array(
            'oEvent' => $oInfo,
            'oPackageList' => $oPackageList,
            'nEvnID' => $nEvnID,
            'oAuthen' => $oAuthen,
            'nLocID' => $nLocID
        ));
    }

    public function FSxCSHTShowTimeAddPackage($nEvnID, $nLocID) {
        $oInfo = $this->Showtime_model->FSxMSHTEventInfo($nEvnID);
        $oPkgList = $this->Showtime_model->FSxMSHTPkgList($nLocID);
        $this->load->view('ticket/showtime/package/wAdd', array(
            'oEvent' => $oInfo,
            'nEvnID' => $nEvnID,
            'oPkgList' => $oPkgList,
            'nLocID' => $nLocID
        ));
    }

    public function FSxCSHTShowTimeAddPackageAjax() {
        if ($this->input->post('ohdFNLocID')) {
            if ($this->input->post('ocmFNPkgID')) {
                $ocbFNPkgID = $this->input->post('ocmFNPkgID');
                foreach ($ocbFNPkgID as $key => $tValue) {
                    $aData = array(
                        'FNPkgID' => $ocbFNPkgID [$key],
                        'FNEvnID' => $this->input->post('ohdFNEvnID')
                    );
                    $this->Showtime_model->FSxMSHTUpdatePkg($aData);
                }
            }
        }
    }

    public function FSxCSHTShowTimeDelPackage() {
        if ($this->input->post('nFNPkgID')) {
            $aPkg = array(
                'FNPkgID' => $this->input->post('nFNPkgID'),
                'FNEvnID' => $this->input->post('nEvnID')
            );
            $this->Showtime_model->FSxMSHTDelPkg($aPkg);
        }
    }

}
