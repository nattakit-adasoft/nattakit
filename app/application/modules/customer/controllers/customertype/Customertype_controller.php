<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Customertype_controller extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('customer/customertype/Customertype_model');
        date_default_timezone_set("Asia/Bangkok");
    }

    /**
     * Functionality : Main page for Customer Type
     * Parameters : $nCstTypeBrowseType, $tCstTypeBrowseOption
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function index($nCstTypeBrowseType, $tCstTypeBrowseOption){
        $aDataConfigView    = [
            'nCstTypeBrowseType'     => $nCstTypeBrowseType,
            'tCstTypeBrowseOption'   => $tCstTypeBrowseOption,
            'aAlwEvent'             => FCNaHCheckAlwFunc('customerType/0/0'),
            'vBtnSave'              => FCNaHBtnSaveActiveHTML('customerType/0/0'),
            'nOptDecimalShow'       => FCNxHGetOptionDecimalShow(),
            'nOptDecimalSave'       => FCNxHGetOptionDecimalSave()
        ];
        $this->load->view ('customer/customertype/wCustomerType',$aDataConfigView);
    }

    /**
     * Functionality : Function Call Customer Type Page List
     * Parameters : Ajax and Function Parameter
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCstTypeListPage(){
        $aDataConfigView    = ['aAlwEvent' => FCNaHCheckAlwFunc('customerType/0/0')];
        $this->load->view('customer/customertype/wCustomerTypeList',$aDataConfigView);
    }

    /**
     * Functionality : Function Call DataTables Customer Type
     * Parameters : Ajax and Function Parameter
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCstTypeDataList(){
        $nPage      = $this->input->post('nPageCurrent');
        $tSearchAll = $this->input->post('tSearchAll');
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}
        //Lang ภาษา
        $nLangResort    = $this->session->userdata("tLangID");
	    $nLangEdit      = $this->session->userdata("tLangEdit");
	    // $aLangHave      = FCNaHGetAllLangByTable('TCNMCstType_L');
        // $nLangHave      = count($aLangHave);
        // if($nLangHave > 1){
	    //     if($nLangEdit != ''){
	    //         $nLangEdit = $nLangEdit;
	    //     }else{
	    //         $nLangEdit = $nLangResort;
	    //     }
	    // }else{
	    //     if(@$aLangHave[0]->nLangList == ''){
	    //         $nLangEdit = '1';
	    //     }else{
	    //         $nLangEdit = $aLangHave[0]->nLangList;
	    //     }
        // }

        $aData  = array(
            'nPage'         => $nPage,
            'nRow'          => 10,
            'FNLngID'       => $nLangEdit,
            'tSearchAll'    => $tSearchAll
        );

        $tAPIReq = "";
        $tMethodReq = "GET";
        $aResList = $this->Customertype_model->FSaMCstTypeList($tAPIReq, $tMethodReq, $aData);
        $aGenTable = array(
            'aDataList' => $aResList,
            'nPage' => $nPage,
            'tSearchAll' => $tSearchAll
        );
        $this->load->view('customer/customertype/wCustomerTypeDataTable', $aGenTable);
    }

    /**
     * Functionality : Function CallPage Customer Type Add
     * Parameters : Ajax and Function Parameter
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCstTypeAddPage(){

        $nLangResort    = $this->session->userdata("tLangID");
        $nLangEdit      = $this->session->userdata("tLangEdit");
        // $aLangHave = FCNaHGetAllLangByTable('TCNMCstType_L');
        // $nLangHave = count($aLangHave);
        // if($nLangHave > 1){
	    //     if($nLangEdit != ''){
	    //         $nLangEdit = $nLangEdit;
	    //     }else{
	    //         $nLangEdit = $nLangResort;
	    //     }
	    // }else{
	    //     if(@$aLangHave[0]->nLangList == ''){
	    //         $nLangEdit = '1';
	    //     }else{
	    //         $nLangEdit = $aLangHave[0]->nLangList;
	    //     }
        // }

        $aData  = array(
            'FNLngID'   => $nLangEdit,
        );
        $tAPIReq        = "";
        $tMethodReq     = "GET";
        $aDataAdd = array(
            'aResult'   => array('rtCode'=>'99')
        );

        $this->load->view('customer/customertype/wCustomerTypeAdd',$aDataAdd);
    }

    /**
     * Functionality : Function CallPage Customer Type Edit
     * Parameters : Ajax and Function Parameter
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCstTypeEditPage(){

        $tCstTypeCode   = $this->input->post('tCstTypeCode');
        $nLangResort    = $this->session->userdata("tLangID");
        $nLangEdit      = $this->session->userdata("tLangEdit");
        // $aLangHave      = FCNaHGetAllLangByTable('TCNMCstType_L');
        // $nLangHave      = count($aLangHave);

        // if($nLangHave > 1){
	    //     if($nLangEdit != ''){
	    //         $nLangEdit = $nLangEdit;
	    //     }else{
	    //         $nLangEdit = $nLangResort;
	    //     }
	    // }else{
	    //     if(@$aLangHave[0]->nLangList == ''){
	    //         $nLangEdit = '1';
	    //     }else{
	    //         $nLangEdit = $aLangHave[0]->nLangList;
	    //     }
        // }

        $aData  = array(
            'FTCstTypeCode' => $tCstTypeCode,
            'FNLngID'   => $nLangEdit
        );

        $tAPIReq        = "";
        $tMethodReq     = "GET";
        $aCstTypeData       = $this->Customertype_model->FSaMCstTypeSearchByID($tAPIReq,$tMethodReq,$aData);
        $aDataEdit      = array('aResult' => $aCstTypeData);
        $this->load->view('customer/customertype/wCustomerTypeAdd', $aDataEdit);
    }

    /**
     * Functionality : Event Add Customer Type
     * Parameters : Ajax and Function Parameter
     * Creator : 08/05/2019 saharat(golf)
     * Last Modified : -
     * Return : Status Add Event
     * Return Type : String
     */
    public function FSaCstTypeAddEvent(){
        try{
            date_default_timezone_set("Asia/Bangkok");
            $aDataMaster = array(
                'tIsAutoGenCode'  => $this->input->post('ocbCstTypeAutoGenCode'),
                'FTCstTypeCode'   => $this->input->post('oetCstTypeCode'),
                'FTCstTypeRmk'    => $this->input->post('otaCstTypeRemark'),
                'FTCstTypeName'   => $this->input->post('oetCstTypeName'),
                'FTLastUpdBy'     => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'     => date('Y-m-d H:i:s'),
                'FTCreateBy'      => $this->session->userdata('tSesUsername'),
                'FDCreateOn'      => date('Y-m-d H:i:s'),
                'FNLngID'         => $this->session->userdata("tLangEdit"),
            );

            if($aDataMaster['tIsAutoGenCode'] == '1'){ // Check Auto Gen CustomerType Code?
                // Auto Gen CustomerType Code
                $aGenCode = FCNaHGenCodeV5('TCNMCstType','0');
                if($aGenCode['rtCode'] == '1'){
                    $aDataMaster['FTCstTypeCode'] = $aGenCode['rtCtyCode'];
                }
            }

            $oCountDup  = $this->Customertype_model->FSoMCstTypeCheckDuplicate($aDataMaster['FTCstTypeCode']);
            $nStaDup    = $oCountDup[0]->counts;

            if($nStaDup == 0){
                $this->db->trans_begin();
                $aStaCstTypeMaster  = $this->Customertype_model->FSaMCstTypeAddUpdateMaster($aDataMaster);
                $aStaCstTypeLang    = $this->Customertype_model->FSaMCstTypeAddUpdateLang($aDataMaster);
                if($this->db->trans_status() === false){
                    $this->db->trans_rollback();
                    $aReturn = array(
                        'nStaEvent'    => '900',
                        'tStaMessg'    => "Unsucess Add Event"
                    );
                }else{
                    $this->db->trans_commit();
                    $aReturn = array(
                        'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                        'tCodeReturn'	=> $aDataMaster['FTCstTypeCode'],
                        'nStaEvent'	    => '1',
                        'tStaMessg'		=> 'Success Add Event'
                    );
                }
            }else{
                $aReturn = array(
                    'nStaEvent'    => '801',
                    'tStaMessg'    => "Data Code Duplicate"
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    /**
     * Functionality : Event Edit Customer Type
     * Parameters : Ajax and Function Parameter
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : Status Add Event
     * Return Type : String
     */
    public function FSaCstTypeEditEvent(){
        try{
            $aDataMaster = array(
                'FTCstTypeCode' => $this->input->post('oetCstTypeCode'),
                'FTCstTypeRmk'  => $this->input->post('otaCstTypeRemark'),
                'FTCstTypeName' => $this->input->post('oetCstTypeName'),
                'FTLastUpdBy'  => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'  => date('Y-m-d H:i:s'),
                'FTCreateBy'   => $this->session->userdata('tSesUsername'),
                'FDCreateOn'   => date('Y-m-d H:i:s'),
                'FNLngID'      => $this->session->userdata("tLangEdit"),
            );
            $this->db->trans_begin();
            $aStaCstTypeMaster  = $this->Customertype_model->FSaMCstTypeAddUpdateMaster($aDataMaster);
            $aStaCstTypeLang    = $this->Customertype_model->FSaMCstTypeAddUpdateLang($aDataMaster);
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add Event"
                );
            }else{
                $this->db->trans_commit();
                $aReturn = array(
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCstTypeCode'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Add Event'
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }

    }

    /**
     * Functionality : Event Delete Customer Type
     * Parameters : Ajax and Function Parameter
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : Status Delete Event
     * Return Type : String
     */
    public function FSaCstTypeDeleteEvent(){
        $tIDCode = $this->input->post('tIDCode');
        $aDataMaster = array(
            'FTCstTypeCode' => $tIDCode
        );

        $aResDel = $this->Customertype_model->FSnMCstTypeDel($aDataMaster);
        $aReturn = array(
            'nStaEvent' => $aResDel['rtCode'],
            'tStaMessg' => $aResDel['rtDesc']
        );
        echo json_encode($aReturn);
    }

    /**
     * Functionality : Vatrate unique check
     * Parameters : $tSelect "CstTypecode"
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : Check status true or false
     * Return Type : String
     */
    public function FStCstTypeUniqueValidate($tSelect = ''){

        if($this->input->is_ajax_request()){ // Request check
            if($tSelect == 'CstTypeCode'){

                $tCstTypeCode = $this->input->post('tCstTypeCode');
                $oCustomerType = $this->Customertype_model->FSoMCstTypeCheckDuplicate($tCstTypeCode);

                $tStatus = 'false';
                if($oCustomerType[0]->counts > 0){ // If have record
                    $tStatus = 'true';
                }
                echo $tStatus;

                return;
            }
            echo 'Param not match.';
        }else{
            echo 'Method Not Allowed';
        }
    }

    //Functionality : Function Event Multi Delete
    //Parameters : Ajax Function Delete Customer Type
    //Creator : 14/06/2018 wasin
    //Last Modified : -
    //Return : Status Event Delete And Status Call Back Event
    //Return Type : object
    public function FSoCstTypeDeleteMulti(){
        $tCstTypeCode = $this->input->post('tCstTypeCode');

        $aCstTypeCode = json_decode($tCstTypeCode);
        foreach($aCstTypeCode as $oCstTypeCode){
            $aCstType = ['FTCstTypeCode' => $oCstTypeCode];
            $this->Customertype_model->FSnMCstTypeDel($aCstType);
        }
        echo json_encode($aCstTypeCode);
    }

    /**
     * Functionality : Delete vat rate
     * Parameters : -
     * Creator : 20/09/2018 piya
     * Last Modified : -
     * Return : Vat code
     * Return Type : Object
     */
    public function FSoCstTypeDelete(){
        $tCstTypeCode = $this->input->post('tCstTypeCode');

        $aCstType = ['FTCstTypeCode' => $tCstTypeCode];
        $this->Customertype_model->FSnMCstTypeDel($aCstType);
        echo json_encode($tCstTypeCode);
    }

}
