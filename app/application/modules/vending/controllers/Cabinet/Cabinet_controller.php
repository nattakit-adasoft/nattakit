<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Cabinet_controller extends MX_Controller {

    public function __construct() {
        parent::__construct ();
        $this->load->model('vending/Cabinet/Cabinet_model');
        date_default_timezone_set("Asia/Bangkok");
    }

    //Main page
    public function FSvCVDCMain(){
        $tBchCode               = $this->input->post('tBchCode');
        $tShpCode               = $this->input->post('tShpCode');
        $this->load->view('vending/Cabinet/wCabinet',array(
            'tBchCode'              => $tBchCode,
            'tShpCode'              => $tShpCode
        ));
    }

    //List 
    public function FSvCVDCListPage(){
        $tCabinetBchCode        = $this->input->post('tBchCode');
        $tCabinetShpCode        = $this->input->post('tShpCode');
        $nPage                  = $this->input->post('nPageCurrent');
        $tSearchAll             = $this->input->post('tSearchAll');
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}

        $aData  = array(
            'tCabinetShpCode'   => $tCabinetShpCode,
            'tCabinetBchCode'   => $tCabinetBchCode,
            'nPage'             => $nPage,
            'nRow'              => 10,
            'tSearchAll'        => $tSearchAll
        );

        $aResList   = $this->Cabinet_model->FSaMVDCDataList($aData);
        $aGenTable  = array(
            'aDataList'     	=> $aResList,
            'nPage'         	=> $nPage,
            'tSearchAll'    	=> $tSearchAll
        );
        $this->load->view('vending/Cabinet/wCabinetDataTable',$aGenTable);

    }

    //Page Add - Page Edit
    public function FSvCVDCPageAdd(){
        $tBchCode           = $this->input->post('tBchCode');
        $tShpCode           = $this->input->post('tShpCode');
        $nSeq               = $this->input->post('nSeq');
        $tPageEvent         = $this->input->post('tPageEvent');
        
        //Get Name Shop
        $tGetNameShop       = $this->Cabinet_model->FSaMVDCGetNameShop($tShpCode);
        if($tPageEvent   == 'PageAdd'){
            $aGetContent = '';
        }else if($tPageEvent   == 'PageEdit'){
            $aGetContent = $this->Cabinet_model->FSaMVDCGetDataByID($tShpCode,$tBchCode,$nSeq);
        }

        $aGenTable          = array(
            'tBchCode' 	             => $tBchCode,
            'tShpCode'     	         => $tShpCode,
            'tShpName'               => $tGetNameShop[0]->FTShpName,
            'tPageEvent'             => $tPageEvent,
            'aGetContent'            => $aGetContent
        );
        $this->load->view('vending/Cabinet/wCabinetPageAdd',$aGenTable);
    }

    //Event Add
    public function FSaVSTAddEvent(){
        $tBCHCode           = $this->input->post('BCHCode');
        $tSHPCode           = $this->input->post('SHPCode');
        $nCabinetKind       = $this->input->post('ocmCabinetType');
        $nCabinetMaxRow     = $this->input->post('oetCabinetMaxRow'); 
        $nCabinetMaxColumn  = $this->input->post('oetCabinetMaxColumn'); 
        $nShtCode           = $this->input->post('oetCabinetShopTypeCode');
        $tCabinetName       = $this->input->post('oetCabinetName');
        $tCabinetRemark     = $this->input->post('oetCabinetRemark');

        $aBchCode  = explode(", ",$tBCHCode);
        $nCountBch = count($aBchCode); 

        //Run Seq
        $nRunSeq  = $this->Cabinet_model->FSaMVDCRunSeqByShop($tSHPCode,$tBCHCode);
        if($nRunSeq == '' || $nRunSeq == null || $nRunSeq == false){
            $nSeq = 0;
        }else{
            $nSeq = $nRunSeq[0]->FNCabSeq + 1;
        }

        //Pack Data
        $aDataInsert  = array(
            'FTBchCode'     => $tBCHCode,
            'FTShpCode'     => $tSHPCode,
            'FNCabSeq'      => $nSeq,
            'FNCabMaxRow'   => $nCabinetMaxRow,
            'FNCabMaxCol'   => $nCabinetMaxColumn,
            'FNCabType'     => $nCabinetKind,
            'FTShtCode'     => $nShtCode,
            'FDLastUpdOn'   => date('Y-m-d H:i:s'),
            'FTLastUpdBy'   => $this->session->userdata('tSesUsername'),
            'FDCreateOn'    => date('Y-m-d H:i:s'),
            'FTCreateBy'    => $this->session->userdata('tSesUsername'),
            'FNLngID'       => $this->session->userdata("tLangEdit"),
            'FTCabName'     => $tCabinetName,
            'FTCabRmk'      => $tCabinetRemark
        );

        //Insert
        $tResult = $this->Cabinet_model->FSaMVDCInsertCabinet($aDataInsert);
        echo json_encode($tResult);
    }

    //Event Edit
    public function FSaVSTEditEvent(){
        $tBCHCode           = $this->input->post('BCHCode');
        $tSHPCode           = $this->input->post('SHPCode');
        $tSeqCabinet        = $this->input->post('ohdSeqCabinet');
        $nCabinetKind       = $this->input->post('ocmCabinetType');
        $nCabinetMaxRow     = $this->input->post('oetCabinetMaxRow'); 
        $nCabinetMaxColumn  = $this->input->post('oetCabinetMaxColumn'); 
        $nShtCode           = $this->input->post('oetCabinetShopTypeCode');
        $tCabinetName       = $this->input->post('oetCabinetName');
        $tCabinetRemark     = $this->input->post('oetCabinetRemark');

        //Pack Data
        $aDataInsert  = array(
            'FTBchCode'     => $tBCHCode,
            'FTShpCode'     => $tSHPCode,
            'FNCabSeq'      => $tSeqCabinet,
            'FNCabMaxRow'   => $nCabinetMaxRow,
            'FNCabMaxCol'   => $nCabinetMaxColumn,
            'FNCabType'     => $nCabinetKind,
            'FTShtCode'     => $nShtCode,
            'FDLastUpdOn'   => date('Y-m-d H:i:s'),
            'FTLastUpdBy'   => $this->session->userdata('tSesUsername'),
            'FDCreateOn'    => date('Y-m-d H:i:s'),
            'FTCreateBy'    => $this->session->userdata('tSesUsername'),
            'FNLngID'       => $this->session->userdata("tLangEdit"),
            'FTCabName'     => $tCabinetName,
            'FTCabRmk'      => $tCabinetRemark    
        );

        //Update
        $tResult = $this->Cabinet_model->FSaMVDCUpdateCabinet($aDataInsert);
        echo json_encode($tResult);
    }

    //Event Delete
    public function FSaVSTDeleteEvent(){
        $tShpCode   = $this->input->post('tShpCode');
        $tSeqCode   = $this->input->post('tSeqCode');
        $tBCH       = $this->input->post('tBCH'); 
        $aDelete    = array(
            'FTShpCode' => $tShpCode,
            'FNCabSeq'  => $tSeqCode,
            'FTBchCode' => $tBCH 
        );
        $aResultDel = $this->Cabinet_model->FSaMVDCDeleteCabinet($aDelete);

        $aReturn    = array(
            'nStaEvent'  	=> $aResultDel['rtCode'],
            'tStaMessg'  	=> $aResultDel['rtDesc']
        );
        echo json_encode($aReturn);
    }

}
