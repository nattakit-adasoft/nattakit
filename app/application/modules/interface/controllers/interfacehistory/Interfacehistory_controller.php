<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Interfacehistory_controller extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('interface/interfacehistory/Interfacehistory_model');
        // $this->load->model('interface/interfaceimport/Interfaceimport_model');
    }

    // Functionality : Function index 
    // Parameters :  
    // Creator : nonpawich  5/3/2020
    // Last Modified : -
    // Return :  View
    // Return Type : View
    public function index($nBrowseType, $tBrowseOption)
    {
        $aData['nBrowseType'] = $nBrowseType;
        $aData['tBrowseOption'] = $tBrowseOption;
        $aData['aAlwEvent'] = FCNaHCheckAlwFunc('interfacehistory/0/0'); //Controle Event
        $aData['vBtnSave'] = FCNaHBtnSaveActiveHTML('interfacehistory/0/0'); //Load Html ของปุ่ม Save ที่เก็บ Session ปัจจุบัน

        $this->load->view('interface/interfacehistory/wInterfacehistory', $aData);
    }

    //Functionality : Function Call wlistw history
    //Parameters : 
    //Creator : nonpawich  5/3/2020
    //Last Modified : -
    //Return :  View
    //Return Type : View
    public function FSxCIFHList()
    {
        $aAlwEvent = FCNaHCheckAlwFunc('interfacehistory/0/0');
        $aData = array('aAlwEvent' => $aAlwEvent);
        $tLangEdit = $this->session->userdata("tLangEdit");

        $aData['aDataMasterImport'] = $this->Interfacehistory_model->FSaMIFHGetLnkAll($tLangEdit);
        $this->load->view('interface/interfacehistory/wInterfacehistorylist', $aData);
    }

    //Functionality : Function Call Getdata 
    //Parameters : 
    //Creator : nonpawich  5/3/2020
    //Last Modified : Napat(Jame) 03/04/63
    //Return :  View
    //Return Type : View
    public function FSaCIFHGetDataTable()
    {
        $aAlwEvent = FCNaHCheckAlwFunc('interfacehistory/0/0'); //Controle Event
        $nLangResort    = $this->session->userdata("tLangID");
        $nPage = $this->input->post('nPageCurrent');
        // $tSearchAll = $this->input->post('tSearchAll');
        // $tStatusIFH = $this->input->post('tStatusIFH');
        // $tSearchDocDateFromIFH = $this->input->post('tSearchDocDateFromIFH');
        // $tSearchDocDateToIFH = $this->input->post('tSearchDocDateToIFH');
        // $nIFHType = $this->input->post('nIFHType');
        // $nIFHInfCode = $this->input->post('nIFHInfCode');
        if ($nPage == '' || $nPage == null) {
            $nPage = 1;
        } else {
            $nPage = $this->input->post('nPageCurrent');
        }
        // if(!$tSearchAll){$tSearchAll='';}    
        // if(!$tStatusIFH){$tStatusIFH='';}   
        // if(!$tSearchDocDateFromIFH){$tSearchDocDateFromIFH='';}     
        // if(!$tSearchDocDateToIFH){$tSearchDocDateToIFH='';} 

        $aData  = array(
            'nPage' => $nPage,
            'nRow' => 10,
            'aPackDataSearch' => $this->input->post('aPackDataSearch')
            // 'tSearchAll' => $tSearchAll,
            // 'tStatusIFH' => $tStatusIFH,
            // 'tSearchDocDateFromIFH' => $tSearchDocDateFromIFH,
            // 'tSearchDocDateToIFH' => $tSearchDocDateToIFH,
            // 'nIFHType' => $nIFHType ,
            // 'nIFHInfCode' => $nIFHInfCode ,
        );

        $aResList = $this->Interfacehistory_model->FSaMIFHList($aData);
        $tPathFile = $this->Interfacehistory_model->FStMIFHGetPathLoadFile($aData);

        $aGenTable  = array(
            'nLangResort' => $nLangResort,
            'aAlwEvent' => $aAlwEvent,
            'aDataList' => $aResList,
            'nPage' => $nPage,
            'tPathFile' => $tPathFile
            // 'tSearchAll' => $tSearchAll,
            // 'tStatusIFH' => $tStatusIFH,
            // 'tSearchDocDateFromIFH' => $tSearchDocDateFromIFH,
            // 'tSearchDocDateToIFH' => $tSearchDocDateToIFH
        );

        $this->load->view('interface/interfacehistory/wInterfacehistoryDatatable', $aGenTable);
    }
}
