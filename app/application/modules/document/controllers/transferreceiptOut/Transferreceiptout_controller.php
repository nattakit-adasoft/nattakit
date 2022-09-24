<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transferreceiptout_controller extends MX_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('document/transferreceiptOut/Transferreceiptout_model');
        $this->load->model('company/company/Company_model');
        $this->load->model('payment/rate/Rate_model');

    }

    public function index($nBrowseType, $tBrowseOption){
        $aDataConfigView    = array(
            'nBrowseType'       => $nBrowseType,  
            'tBrowseOption'     => $tBrowseOption, 
            'aPermission'       => FCNaHCheckAlwFunc('TXOOut/0/0'),
            'vBtnSave'          => FCNaHBtnSaveActiveHTML('TXOOut/0/0'), 
            'nOptDecimalShow'   => FCNxHGetOptionDecimalShow(), 
            'nOptDecimalSave'   => FCNxHGetOptionDecimalSave() 
        );
        $this->load->view('document/transferreceiptOut/wTransferreceiptOut',$aDataConfigView);
    }

    //Page - List
    public function FSxCTWOTransferReceiptList(){
        $this->load->view('document/transferreceiptOut/wTransferreceiptOutSearchList');    
    }

    //Page - DataTable
    public function FSxCTWOTransferReceiptDataTable(){
        $tAdvanceSearchData     = $this->input->post('oAdvanceSearch');
        $nPage                  = $this->input->post('nPageCurrent');
        $aAlwEvent              = FCNaHCheckAlwFunc('TXOOut/0/0');
        $nOptDecimalShow        = FCNxHGetOptionDecimalShow();

        if ($nPage == '' || $nPage == null) {
            $nPage = 1;
        } else {
            $nPage = $this->input->post('nPageCurrent');
        }

        $nLangResort            = $this->session->userdata("tLangID");
        $nLangEdit              = $this->session->userdata("tLangEdit");
        $aData = array(
            'FNLngID'           => $nLangEdit,
            'nPage'             => $nPage,
            'nRow'              => 10,
            'aAdvanceSearch'    => $tAdvanceSearchData
        );

        $aResList   = $this->Transferreceiptout_model->FSaMTRNList($aData);
        $aGenTable  = array(
            'aAlwEvent'         => $aAlwEvent,
            'aDataList'         => $aResList,
            'nPage'             => $nPage,
            'nOptDecimalShow'   => $nOptDecimalShow
        );

        $tTWIViewDataTable = $this->load->view('document/transferreceiptOut/wTransferreceiptOutDataTable', $aGenTable ,true);
        $aReturnData = array(
            'tViewDataTable'    => $tTWIViewDataTable,
            'nStaEvent'         => '1',
            'tStaMessg'         => 'Success'
        );
        echo json_encode($aReturnData);
    }

    //Page - Add
    public function FSvCTWOTransferReceiptPageAdd(){
        try{
            // Clear Product List IN Doc Temp
            $tTblSelectData = "TCNTPdtTwiHD";
            $this->Transferreceiptout_model->FSxMTWIClearPdtInTmp($tTblSelectData);

            // Get Option Show Decimal
            $nOptDecimalShow    = FCNxHGetOptionDecimalShow();
            // Get Option Doc Save
            $nOptDocSave        = FCNnHGetOptionDocSave();
            // Get Option Scan SKU
            $nOptScanSku        = FCNnHGetOptionScanSku();
            //Lang ภาษา
            $nLangEdit          = $this->session->userdata("tLangEdit");

            // VAT
            $aDataWhere         = array('FNLngID' => $nLangEdit);
            $tAPIReq            = "";
            $tMethodReq         = "GET";
            $aCompData          = $this->Company_model->FSaMCMPList($tAPIReq,$tMethodReq,$aDataWhere);  
            $tCmpCode           = $aCompData['raItems']['rtCmpCode'];

            if($aCompData['rtCode'] == '1'){
                $tBchCode       = $aCompData['raItems']['rtCmpBchCode'];
                $tCmpRteCode    = $aCompData['raItems']['rtCmpRteCode'];
                $tVatCode       = $aCompData['raItems']['rtVatCodeUse'];
                $aVatRate       = FCNoHCallVatlist($tVatCode); 
                $cVatRate       = $aVatRate['FCVatRate'][0];
                $aDataRate      = array(
                    'FTRteCode' => $tCmpRteCode,
                    'FNLngID'   => $nLangEdit
                );
                $aResultRte     = $this->Rate_model->FSaMRTESearchByID($aDataRate);
                if($aResultRte['rtCode'] == 1){
                    $cXthRteFac = $aResultRte['raItems']['rcRteRate'];
                }else{
                    $cXthRteFac = "";
                }
            }else{
                $tBchCode       = FCNtGetBchInComp();
                $tCmpRteCode    = "";
                $tVatCode       = "";
                $cVatRate       = "";
                $cXthRteFac     = "";
            }

            // Get Department Code
            $tUsrLogin  = $this->session->userdata('tSesUsername');
            $tDptCode   = FCNnDOCGetDepartmentByUser($tUsrLogin);

            // Get ข้อมูลสาขา และ ร้านค้าของ User ที่ login
            $aDataShp   = array(
                'FNLngID'   => $nLangEdit,
                'tUsrLogin' => $tUsrLogin
            );
            $aDataUserGroup = $this->Transferreceiptout_model->FSaMASTGetShpCodeForUsrLogin($aDataShp);

            if(empty($aDataUserGroup)){
                $tBchCode   = "";
                $tBchName   = "";
                $tMerCode   = "";
                $tMerName   = "";
                $tShpType   = "";
                $tShpCode   = "";
                $tShpName   = "";
                $tWahCode   = "";
                $tWahName   = "";
            }else{
                $tBchCode   = "";
                $tBchName   = "";
                $tMerCode   = "";
                $tMerName   = "";
                $tShpType   = "";
                $tShpCode   = "";
                $tShpName   = "";
                $tWahCode   = "";
                $tWahName   = "";

                // เช็ค user ว่ามีการผูกสาขาไว้หรือไม่
                if(isset($aDataUserGroup["FTBchCode"]) && !empty($aDataUserGroup["FTBchCode"])){
                    $tBchCode   = $aDataUserGroup["FTBchCode"];
                    $tBchName   = $aDataUserGroup["FTBchName"];
                }

                // เช็ค user ว่ามีการผูกกลุ่มร้านค้าไว้หรือไม่
                if(isset($aDataUserGroup["FTMerCode"]) && !empty($aDataUserGroup["FTMerCode"])){
                    $tMerCode   = $aDataUserGroup["FTMerCode"];
                    $tMerName   = $aDataUserGroup["FTMerName"];
                }

                // เช็ค user ว่ามีการผูกร้านค้าไว้หรือไม่
                $tShpType   = $aDataUserGroup["FTShpType"];
                if(isset($aDataUserGroup["FTShpCode"]) && !empty($aDataUserGroup["FTShpCode"])){
                    $tShpCode   = $aDataUserGroup["FTShpCode"];
                    $tShpName   = $aDataUserGroup["FTShpName"];
                }

                if(isset($aDataUserGroup["FTWahCode"]) && !empty($aDataUserGroup["FTWahCode"])){
                    $tWahCode   = $aDataUserGroup["FTWahCode"];
                    $tWahName   = $aDataUserGroup["FTWahName"];
                }
            }

            $aDataConfigViewAdd = array(
                'nOptDecimalShow'   => $nOptDecimalShow,
                'nOptDocSave'       => $nOptDocSave,
                'nOptScanSku'       => $nOptScanSku,
                'tCmpRteCode'       => $tCmpRteCode,
                'tVatCode'          => $tVatCode,
                'cVatRate'          => $cVatRate,
                'cXthRteFac'        => $cXthRteFac,
                'tDptCode'          => $tDptCode,
                'tBchCode'          => $tBchCode,
                'tBchName'          => $tBchName,
                'tMerCode'          => $tMerCode,
                'tMerName'          => $tMerName,
                'tShpType'          => $tShpType,
                'tShpCode'          => $tShpCode,
                'tShpName'          => $tShpName,
                'tWahCode'          => $tWahCode,
                'tWahName'          => $tWahName,
                'aDataDocHD'        => array('rtCode'=>'99'),
                'tBchCompCode'      => FCNtGetBchInComp(),
                'tBchCompName'      => FCNtGetBchNameInComp(),
                'tCmpCode'          => $tCmpCode  
            );

            $tViewPageAdd       = $this->load->view('document/transferreceiptOut/wTransferreceiptOutPageAdd',$aDataConfigViewAdd,true);
            $aReturnData        = array(
                'tViewPageAdd'      => $tViewPageAdd,
                'nStaEvent'         => '1',
                'tStaMessg'         => 'Success'
            );
        }catch(Exception $Error){
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //Page - Edit
    public function FSvCTWOTransferReceiptPageEdit(){
        try {
            $tTWIDocNo = $this->input->post('ptDocNumber');

            // Clear Data In Doc DT Temp
            $aWhereClearTemp = [
                'FTXthDocNo'    => $tTWIDocNo,
                'FTXthDocKey'   => 'TCNTPdtTwiHD',
                'FTSessionID'   => $this->session->userdata('tSesSessionID')
            ];
            $this->Transferreceiptout_model->FSxMTWIClearPdtInTmp($aWhereClearTemp);

            $aAlwEvent          = FCNaHCheckAlwFunc('TXOOut/0/0');
            // Get Option Show Decimal
            $nOptDecimalShow    = FCNxHGetOptionDecimalShow();
            // Get Option Doc Save
            $nOptDocSave        = FCNnHGetOptionDocSave();
            // Get Option Scan SKU
            $nOptScanSku        = FCNnHGetOptionScanSku();
            //Lang ภาษา
            $nLangEdit          = $this->session->userdata("tLangEdit");

            // Get Department Code
            $tUsrLogin  = $this->session->userdata('tSesUsername');
            $tDptCode   = FCNnDOCGetDepartmentByUser($tUsrLogin);

            // Get ข้อมูลสาขา และ ร้านค้าของ User ที่ login
            $tUsrLogin = $this->session->userdata('tSesUsername');
            $aDataShp = array(
                'FNLngID'   => $nLangEdit,
                'tUsrLogin' => $tUsrLogin
            );

            // VAT
            $aDataWhere         = array('FNLngID' => $nLangEdit);
            $tAPIReq            = "";
            $tMethodReq         = "GET";
            $aCompData          = $this->Company_model->FSaMCMPList($tAPIReq,$tMethodReq,$aDataWhere);  
            $tCmpCode           = $aCompData['raItems']['rtCmpCode'];
            if($aCompData['rtCode'] == '1'){
                $tBchCode       = $aCompData['raItems']['rtCmpBchCode'];
                $tCmpRteCode    = $aCompData['raItems']['rtCmpRteCode'];
                $tVatCode       = $aCompData['raItems']['rtVatCodeUse'];
                $aVatRate       = FCNoHCallVatlist($tVatCode); 
                $cVatRate       = $aVatRate['FCVatRate'][0];
                $aDataRate      = array(
                    'FTRteCode' => $tCmpRteCode,
                    'FNLngID'   => $nLangEdit
                );
                $aResultRte     = $this->Rate_model->FSaMRTESearchByID($aDataRate);
                if($aResultRte['rtCode'] == 1){
                    $cXthRteFac = $aResultRte['raItems']['rcRteRate'];
                }else{
                    $cXthRteFac = "";
                }
            }else{
                $tBchCode       = FCNtGetBchInComp();
                $tCmpRteCode    = "";
                $tVatCode       = "";
                $cVatRate       = "";
                $cXthRteFac     = "";
            }

            $aDataUserGroup = $this->Transferreceiptout_model->FSaMASTGetShpCodeForUsrLogin($aDataShp);
            if (isset($aDataUserGroup) && empty($aDataUserGroup)) {
                $tUsrBchCode    = "";
                $tUsrBchName    = "";
                $tUsrMerCode    = "";
                $tUsrMerName    = "";
                $tUsrShopType   = "";
                $tUsrShopCode   = "";
                $tUsrShopName   = "";
                $tUsrWahCode    = "";
                $tUsrWahName    = "";
            } else {
                $tUsrBchCode    = $aDataUserGroup["FTBchCode"];
                $tUsrBchName    = $aDataUserGroup["FTBchName"];
                $tUsrMerCode    = $aDataUserGroup["FTMerCode"];
                $tUsrMerName    = $aDataUserGroup["FTMerName"];
                $tUsrShopType   = $aDataUserGroup["FTShpType"];
                $tUsrShopCode   = $aDataUserGroup["FTShpCode"];
                $tUsrShopName   = $aDataUserGroup["FTShpName"];
                $tUsrWahCode    = $aDataUserGroup["FTWahCode"];
                $tUsrWahName    = $aDataUserGroup["FTWahName"];
            }

            // Data Table Document
            $aTableDocument = array(
                'tTableHD'      => 'TCNTPdtTwiHD',
                'tTableHDCst'   => '',
                'tTableHDDis'   => '',
                'tTableDT'      => 'TCNTPdtTwiDT',
                'tTableDTDis'   => ''
            );

            // Array Data Where Get (HD,HDSpl,HDDis,DT,DTDis)
            $aDataWhere = array(
                'FTXthDocNo'    => $tTWIDocNo,
                'FTXthDocKey'   => 'TCNTPdtTwiHD',
                'FNLngID'       => $nLangEdit,
                'nRow'          => 10000,
                'nPage'         => 1,
            );
            $this->db->trans_begin();

            // Get Data Document HD
            $aDataDocHD = $this->Transferreceiptout_model->FSaMTWIGetDataDocHD($aDataWhere);
            
            // Move Data DT TO DTTemp
            $this->Transferreceiptout_model->FSxMTWIMoveDTToDTTemp($aDataWhere);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Error Query Call Edit Page.'
                );
            } else {
                $this->db->trans_commit();
                $tTWIVATInOrEx = ($aDataDocHD['rtCode'] == '1') ? $aDataDocHD['raItems']['FTXthVATInOrEx'] : 1;
                $aCalcDTTempParams = array(
                    'tDataDocEvnCall'   => '1',
                    'tDataVatInOrEx'    => $tTWIVATInOrEx,
                    'tDataDocNo'        => $tTWIDocNo,
                    'tDataDocKey'       => 'TCNTPdtTwiHD',
                    'tDataSeqNo'        => ""
                );
                FCNbHCallCalcDocDTTemp($aCalcDTTempParams);

                $aDataConfigViewAdd = array(
                    'nOptDecimalShow'   => $nOptDecimalShow,
                    'nOptDocSave'       => $nOptDocSave,
                    'nOptScanSku'       => $nOptScanSku,
                    'tCmpRteCode'       => $tCmpRteCode,
                    'tVatCode'          => $tVatCode,
                    'cVatRate'          => $cVatRate,
                    'cXthRteFac'        => $cXthRteFac,
                    'tDptCode'          => $tDptCode,
                    'tBchCode'          => $tUsrBchCode,
                    'tBchName'          => $tUsrBchName,
                    'tMerCode'          => $tUsrMerCode,
                    'tMerName'          => $tUsrMerName,
                    'tShpType'          => $tUsrShopType,
                    'tShpCode'          => $tUsrShopCode,
                    'tShpName'          => $tUsrShopName,
                    'tWahCode'          => $tUsrWahCode,
                    'tWahName'          => $tUsrWahName,
                    'aDataDocHD'        => $aDataDocHD,
                    'tBchCompCode'      => FCNtGetBchInComp(),
                    'tBchCompName'      => FCNtGetBchNameInComp(),
                    'tCmpCode'          => $tCmpCode ,
                    'aAlwEvent'         => $aAlwEvent
                );

                $tViewPageAdd   = $this->load->view('document/transferreceiptOut/wTransferreceiptOutPageAdd', $aDataConfigViewAdd, true);
                $aReturnData    = array(
                    'tViewPageAdd'      => $tViewPageAdd,
                    'nStaEvent'         => '1',
                    'tStaMessg'         => 'Success'
                );
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //Page - Product Table
    public function FSoCTWOPdtAdvTblLoadData() {
        try {
            $tTWIDocNo                = $this->input->post('ptTWIDocNo');
            $tTWIStaApv               = $this->input->post('ptTWIStaApv');
            $tTWIStaDoc               = $this->input->post('ptTWIStaDoc');
            $nTWIPageCurrent          = $this->input->post('pnTWIPageCurrent');
            $tSearchPdtAdvTable       = $this->input->post('ptSearchPdtAdvTable');
            $tVat                     = 1;
            // Edit in line
            $tTWIPdtCode              = '';
            $tTWIPunCode              = '';

            //Get Option Show Decimal
            $nOptDecimalShow            = FCNxHGetOptionDecimalShow();

            // Call Advance Table
            $tTableGetColumeShow        = 'TCNTPdtTwiDT';
            $aColumnShow                = FCNaDCLGetColumnShow($tTableGetColumeShow);
            

            $aDataWhere = array(
                'tSearchPdtAdvTable'    => $tSearchPdtAdvTable,
                'FTXthDocNo'            => $tTWIDocNo,
                'FTXthDocKey'           => 'TCNTPdtTwiHD',
                'nPage'                 => $nTWIPageCurrent,
                'nRow'                  => 10,
                'FTSessionID'           => $this->session->userdata('tSesSessionID'),
            );

            // Calcurate Document DT Temp Array Parameter
            $aCalcDTParams = [
                'tDataDocEvnCall'       => '1',
                'tDataVatInOrEx'        => $tVat,
                'tDataDocNo'            => $tTWIDocNo,
                'tDataDocKey'           => 'TCNTPdtTwiHD',
                'tDataSeqNo'            => ''
            ];
            FCNbHCallCalcDocDTTemp($aCalcDTParams);

            $aDataDocDTTemp     = $this->Transferreceiptout_model->FSaMTWIGetDocDTTempListPage($aDataWhere);
            $aDataDocDTTempSum  = $this->Transferreceiptout_model->FSaMTWISumDocDTTemp($aDataWhere);

            $aDataView = array(
                'nOptDecimalShow'       => $nOptDecimalShow,
                'tTWIStaApv'            => $tTWIStaApv,
                'tTWIStaDoc'            => $tTWIStaDoc,
                'tTWIPdtCode'           => @$tTWIPdtCode,
                'tTWIPunCode'           => @$tTWIPunCode,
                'nPage'                 => $nTWIPageCurrent,
                'aColumnShow'           => $aColumnShow,
                'aDataDocDTTemp'        => $aDataDocDTTemp,
                'aDataDocDTTempSum'     => $aDataDocDTTempSum
            );

            $tTWIPdtAdvTableHtml = $this->load->view('document/transferreceiptOut/wTransferreceiptOutPdtAdvTableData', $aDataView, true);

            // Call Footer Document
            $aEndOfBillParams = array(
                'tSplVatType'   => $tVat,
                'tDocNo'        => $tTWIDocNo,
                'tDocKey'       => 'TCNTPdtTwiHD',
                'nLngID'        => FCNaHGetLangEdit(),
                'tSesSessionID' => $this->session->userdata('tSesSessionID'),
                'tBchCode'      => $this->input->post('tBCH')
            );

            $aTWIEndOfBill['aEndOfBillVat']  = FCNaDOCEndOfBillCalVat($aEndOfBillParams);
            $aTWIEndOfBill['aEndOfBillCal']  = FCNaDOCEndOfBillCal($aEndOfBillParams);
            $aTWIEndOfBill['tTextBath']      = FCNtNumberToTextBaht($aTWIEndOfBill['aEndOfBillCal']['cCalFCXphGrand']);
            $aReturnData = array(
                'tTWIPdtAdvTableHtml'   => $tTWIPdtAdvTableHtml,
                'aTWIEndOfBill'         => $aTWIEndOfBill,
                'nStaEvent'             => '1',
                'tStaMessg'             => "Fucntion Success Return View."
            );
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    } 

    //กดเลือกว่า หน้าต่างจะให้โชว์อะไรบ้าง 
    public function FSoCTWOAdvTblShowColList() {
        try {
            // $tTableShowColums = 'TCNTPdtTwiDT';
            // $aAvailableColumn = FCNaDCLAvailableColumn($tTableShowColums);
            $aAvailableColumn    = $this->Transferreceiptout_model->FSoMTWIlableColumn('TCNTPdtTwiDT');
            
            $aDataViewAdvTbl = array(
                'aAvailableColumn' => $aAvailableColumn
            );
            $tViewTableShowCollist = $this->load->view('document/transferreceiptNew/advancetable/wTransferrenceiptNewTableShowColList', $aDataViewAdvTbl, true);
            $aReturnData = array(
                'tViewTableShowCollist' => $tViewTableShowCollist,
                'nStaEvent'             => '1',
                'tStaMessg'             => 'Success'
            );
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //บันทึกข้อมูลหน้าจอที่ให้โชว์อะไรบ้าง
    public function FSoCTWOAdvTalShowColSave() {
        try {
            $this->db->trans_begin();

            $nTWIStaSetDef       = $this->input->post('pnTWIStaSetDef');
            $aTWIColShowSet      = $this->input->post('paTWIColShowSet');
            $aTWIColShowAllList  = $this->input->post('paTWIColShowAllList');
            $aTWIColumnLabelName = $this->input->post('paTWIColumnLabelName');
            
            $tTableShowColums    = "TCNTPdtTwiDT";
            FCNaDCLSetShowCol($tTableShowColums, '', '');
            if ($nTWIStaSetDef == '1') {
                FCNaDCLSetDefShowCol($tTableShowColums);
            } else {
                for ($i = 0; $i < count($aTWIColShowSet); $i++) {
                    FCNaDCLSetShowCol($tTableShowColums, 1, $aTWIColShowSet[$i]);
                }
            }

            // Reset Seq Advannce Table
            FCNaDCLUpdateSeq($tTableShowColums, '', '', '');
            $q = 1;
            for ($n = 0; $n < count($aTWIColShowAllList); $n++) {
                FCNaDCLUpdateSeq($tTableShowColums, $aTWIColShowAllList[$n], $q, $aTWIColumnLabelName[$n]);
                $q++;
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Eror Not Save Colums'
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'Success'
                );
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //เพิ่มสินค้าลงตาราง Tmp
    public function FSoCTWOAddPdtIntoDocDTTemp() {
        try {
            $tTWIUserLevel       = $this->session->userdata('tSesUsrLevel');
            $tTWIDocNo           = $this->input->post('tTWIDocNo');
            $tTWIBchCode         = $this->input->post('tBCH');
            $tTWIPdtData         = $this->input->post('tTWIPdtData');
            $aTWIPdtData         = JSON_decode($tTWIPdtData);
            $tTWIVATInOrEx       = 1;
            $tTypeInsPDT         = $this->input->post('tType');

            $aDataWhere = array(
                'FTBchCode'     => $tTWIBchCode,
                'FTXthDocNo'    => $tTWIDocNo,
                'FTXthDocKey'   => 'TCNTPdtTwiHD',
            );
            $this->db->trans_begin();
            
            // ทำทีรายการ ตามรายการสินค้าที่เพิ่มเข้ามา
            for ($nI = 0; $nI<count($aTWIPdtData); $nI++) {

                $aItem       = $aTWIPdtData[$nI];
                if($tTypeInsPDT == 'CN'){
                    $tDocRefSO      = $aItem->tDocNo;
                    $tSeqItemSO     = $aItem->ptSeqItem;
                }else if($tTypeInsPDT == 'PDT'){
                    $tDocRefSO      = '';
                    $tSeqItemSO     = '';
                }

                $tTWIPdtCode = $aItem->pnPdtCode;
                $tTWIBarCode = $aItem->ptBarCode;
                $tTWIPunCode = $aItem->ptPunCode;
                $nPrice      = $aItem->packData->Price;
                // $CTWOPrice    = $this->Transferreceiptout_model->FSaMTWIGetPriceBYPDT($tTWIPdtCode);
                // if($CTWOPrice[0]->PDTCostSTD == null){
                //     $nPrice = 0;
                // }else{
                //     $nPrice = $CTWOPrice[0]->PDTCostSTD;
                // }

                $nTWIMaxSeqNo = $this->Transferreceiptout_model->FSaMTWIGetMaxSeqDocDTTemp($aDataWhere);
                $aDataPdtParams = array(
                    'tDocNo'            => $tTWIDocNo,
                    'tBchCode'          => $tTWIBchCode,
                    'tPdtCode'          => $tTWIPdtCode,
                    'tBarCode'          => $tTWIBarCode,
                    'tPunCode'          => $tTWIPunCode,
                    'cPrice'            => $nPrice,
                    'nMaxSeqNo'         => $nTWIMaxSeqNo + 1,
                    'nLngID'            => $this->session->userdata("tLangID"),
                    'tSessionID'        => $this->session->userdata('tSesSessionID'),
                    'tDocKey'           => 'TCNTPdtTwiHD',
                    'tDocRefSO'         => $tDocRefSO
                );

                // Data Master Pdt ข้อมูลรายการสินค้าที่เพิ่มเข้ามา
                $aDataPdtMaster     = $this->Transferreceiptout_model->FSaMTWIGetDataPdt($aDataPdtParams);
                // นำรายการสินค้าเข้า DT Temp
                $nStaInsPdtToTmp    = $this->Transferreceiptout_model->FSaMTWIInsertPDTToTemp($aDataPdtMaster, $aDataPdtParams);

                //ถ้าเลือกนำเข้าข้อมูล จะต้องไปทำให้สินค้าใน CN ว่าถูกใช้งานแล้ว
                if($tTypeInsPDT == 'CN'){
                    //$this->Transferreceiptout_model->FSaMTWIUpdatePDTInCN($tDocRefSO,$tSeqItemSO);
                }
            }

            if($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Error Insert Product Error Please Contact Admin.'
                );
            }else{
                $this->db->trans_commit();
                // Calcurate Document DT Temp Array Parameter
                $aCalcDTParams = [
                    'tDataDocEvnCall'   => '1',
                    'tDataVatInOrEx'    => $tTWIVATInOrEx,
                    'tDataDocNo'        => $tTWIDocNo,
                    'tDataDocKey'       => 'TCNTPdtTwiHD',
                    'tDataSeqNo'        => ''
                ];
                $tStaCalcuRate = FCNbHCallCalcDocDTTemp($aCalcDTParams);
                if ($tStaCalcuRate === TRUE) {
                    // Prorate HD
                    FCNaHCalculateProrate('TCNTPdtTwiHD', $tTWIDocNo);
                    FCNbHCallCalcDocDTTemp($aCalcDTParams);
                    $aReturnData = array(
                        'nStaEvent' => '1',
                        'tStaMessg' => 'Success Add Product Into Document DT Temp.'
                    );
                } else {
                    $aReturnData = array(
                        'nStaEvent' => '500',
                        'tStaMessg' => 'Error Calcurate Document DT Temp Please Contact Admin.'
                    );
                }
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //ลบข้อมูลใน HD (DATATABLE) - ตัวเดียว
    public function FSoCTWODeleteEventDoc(){
        try{    
            $tTWIDocNo  = $this->input->post('tTWIDocNo');
            $aDataMaster = array(
                'tTWIDocNo'     => $tTWIDocNo
            );
            $aResDelDoc = $this->Transferreceiptout_model->FSnMTWIDelDocument($aDataMaster);
            if($aResDelDoc['rtCode'] == '1'){
                $aDataStaReturn  = array(
                    'nStaEvent' => '1',
                    'tStaMessg' => 'Success'
                );
            }else{
                $aDataStaReturn  = array(
                    'nStaEvent' => $aResDelDoc['rtCode'],
                    'tStaMessg' => $aResDelDoc['rtDesc']
                );
            }
        }catch(Exception $Error){
            $aDataStaReturn = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aDataStaReturn);
    }

    //ลบสินค้าใน Tmp (ตารางสินค้า) - ตัวเดียว
    public function FSvCTWORemovePdtInDTTmp() {
        try {
            $this->db->trans_begin();

            $aDataWhere = array(
                'tBchCode'      => $this->input->post('tBchCode'),
                'tDocNo'        => $this->input->post('tDocNo'),
                'tPdtCode'      => $this->input->post('tPdtCode'),
                'nSeqNo'        => $this->input->post('nSeqNo'),
                'tVatInOrEx'    => $this->input->post('tVatInOrEx'),
                'tSessionID'    => $this->session->userdata('tSesSessionID')
            );
            $this->Transferreceiptout_model->FSnMTWIDelPdtInDTTmp($aDataWhere);
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Cannot Delete Item.',
                );
            } else {
                $this->db->trans_commit();
                $aCalcDTParams = [
                    'tDataDocEvnCall'   => '',
                    'tDataVatInOrEx'    => $aDataWhere['tVatInOrEx'],
                    'tDataDocNo'        => $aDataWhere['tDocNo'],
                    'tDataDocKey'       => 'TCNTPdtTwiHD',
                    'tDataSeqNo'        => ''
                ];
                FCNbHCallCalcDocDTTemp($aCalcDTParams);
                $aReturnData = array(
                    'nStaEvent'         => '1',
                    'tStaMessg'         => 'Success Delete Product'
                );
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //ลบสินค้าใน Tmp (ตารางสินค้า) - หลายตัว
    public function FSvCTWORemovePdtInDTTmpMulti() {
        try {
            $this->db->trans_begin();
            $aDataWhere = array(
                'tBchCode'      => $this->input->post('tBchCode'),
                'tDocNo'        => $this->input->post('ptTWIDocNo'),
                'tVatInOrEx'    => $this->input->post('ptTWIVatInOrEx'),
                'aDataPdtCode'  => $this->input->post('paDataPdtCode'),
                'aDataPunCode'  => $this->input->post('paDataPunCode'),
                'aDataSeqNo'    => $this->input->post('paDataSeqNo')
            );

            $this->Transferreceiptout_model->FSnMTWIDelMultiPdtInDTTmp($aDataWhere);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '500',
                    'tStaMessg' => 'Cannot Delete Item.',
                );
            } else {
                $this->db->trans_commit();
                $aCalcDTParams = [
                    'tDataDocEvnCall'   => '',
                    'tDataVatInOrEx'    => $aDataWhere['tVatInOrEx'],
                    'tDataDocNo'        => $aDataWhere['tDocNo'],
                    'tDataDocKey'       => 'TCNTPdtTwiHD',
                    'tDataSeqNo'        => ''
                ];
                FCNbHCallCalcDocDTTemp($aCalcDTParams);
                $aReturnData = array(
                    'nStaEvent'         => '1',
                    'tStaMessg'         => 'Success Delete Product'
                );
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //Event เพิ่ม HD - DT
    public function FSoCTWOAddEventDoc(){
        try {
            $aDataDocument   = $this->input->post();
            $tTWIAutoGenCode = (isset($aDataDocument['ocbTWIStaAutoGenCode'])) ? 1 : 0;
            $tTWIDocNo       = (isset($aDataDocument['oetTWIDocNo'])) ? $aDataDocument['oetTWIDocNo'] : '';
            $tTWIDocDate     = $aDataDocument['oetTWIDocDate'] . " " . $aDataDocument['oetTWIDocTime'];
            $tTWIVATInOrEx   = 1;
            $tTWISessionID   = $this->session->userdata('tSesSessionID');

            // Get Data Comp.
            $nLangEdit      = $this->session->userdata("tLangEdit");
            $aDataWhereComp = array('FNLngID' => $nLangEdit);
            $tAPIReq        = "";
            $tMethodReq     = "GET";
            $aCompData      = $this->Company_model->FSaMCMPList($tAPIReq, $tMethodReq, $aDataWhereComp);

            $aCalDTTempParams = [
                'tDocNo'        => '',
                'tBchCode'      => $aDataDocument['oetSOFrmBchCode'],
                'tSessionID'    => $tTWISessionID,
                'tDocKey'       => 'TCNTPdtTwiHD'
            ];
            $aCalDTTempForHD = $this->FSaCTWOCalDTTempForHD($aCalDTTempParams);

            // Array Data Table Document
            $aTableAddUpdate = array(
                'tTableHD'      => 'TCNTPdtTwiHD',
                'tTableHDDis'   => '-',
                'tTableHDSpl'   => '-',
                'tTableDT'      => 'TCNTPdtTwiDT',
                'tTableDTDis'   => '-',
                'tTableStaGen'  => 5
            );

            // Array Data Where Insert
            $aDataWhere = array(
                'FTBchCode'         => $aDataDocument['oetSOFrmBchCode'],
                'FTXthDocNo'        => $tTWIDocNo,
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FTSessionID'       => $this->session->userdata('tSesSessionID'),
                'FTXthVATInOrEx'    => $tTWIVATInOrEx
            );

            //เงือนไขของ HD
            if($aDataDocument['ocmSelectTransTypeIN'] == 'SPL'){
                //ประเภทคลังสินค้า
                $tRsnType = 3;

                //ร้านค้า
                $tShopFrm = null;
                $tShopTo  = null;

                //คลัง
                $tWahFrm  = null;
                $tWahTo   = $aDataDocument['oetTRINWahFromCode'] == '' ? null : $aDataDocument['oetTRINWahFromCode'];

                //ผู้จำหน่าย
                $tSplCode = $aDataDocument['oetTRINSplFromCode'] == '' ? null : $aDataDocument['oetTRINSplFromCode'];

                //แหล่งอื่น
                $tOther   = null;
            }else{
                //ประเภทคลังสินค้า
                $tRsnType = 4;

                //ร้านค้า
                $tShopFrm = null;
                $tShopTo  = null;

                //คลัง
                $tWahFrm  = null;
                $tWahTo   = $aDataDocument['oetTRINWahEtcCode'] == '' ? null : $aDataDocument['oetTRINWahEtcCode'];

                //ผู้จำหน่าย
                $tSplCode = null;

                //แหล่งอื่น
                $tOther   = $aDataDocument['oetTWIINEtc'] == '' ? null : $aDataDocument['oetTWIINEtc'];
            }

            //เครื่องจุดขาย
            $tPosFrm  = null;
            $tPosTo   = null;

            // Array Data HD Master
            $aDataMaster = array(
                'FTBchCode'             => $aDataDocument['oetSOFrmBchCode'],
                'FTXthDocNo'            => $tTWIDocNo,
                'FNXthDocType'          => 1,
                'FTXthTypRefFrm'        => $tRsnType,
                'FDXthDocDate'          => (!empty($tTWIDocDate)) ? $tTWIDocDate : NULL,
                'FTXthVATInOrEx'        => $tTWIVATInOrEx,
                'FTDptCode'             => $this->session->userdata('tSesUsrDptCode') == '' ? null : $this->session->userdata('tSesUsrDptCode'),
                'FTXthMerCode'          => '',
                'FTXthShopFrm'          => $tShopFrm,
                'FTXthShopTo'           => $tShopTo,
                'FTXthWhFrm'            => $tWahFrm,
                'FTXthWhTo'             => $tWahTo,
                'FTXthPosFrm'           => $tPosFrm,
                'FTXthPosTo'            => $tPosTo,
                'FTSplCode'             => $tSplCode,
                'FTXthOther'            => $tOther,
                'FTUsrCode'             => $this->session->userdata('tSesUserCode'),
                'FTSpnCode'             => null,
                'FTXthApvCode'          => $this->session->userdata('tSesUsername'),
                'FTXthRefExt'           => $aDataDocument['oetTWIRefExtDoc'],
                'FDXthRefExtDate'       => $aDataDocument['oetTWIRefExtDocDate'],
                'FTXthRefInt'           => $aDataDocument['oetTWIRefIntDoc'],
                'FDXthRefIntDate'       => $aDataDocument['oetTWIRefIntDocDate'],
                'FNXthDocPrint'         => 0,
                'FCXthTotal'            => $aCalDTTempForHD['FCXphTotal'],
                'FCXthVat'              => $aCalDTTempForHD['FCXphVat'],
                'FCXthVatable'          => $aCalDTTempForHD['FCXphVatable'],
                'FTXthRmk'              => $aDataDocument['otaTWIFrmInfoOthRmk'],
                'FTXthStaDoc'           => 1, //สถานะ เอกสาร  1:สมบูรณ์, 2:ไม่สมบูรณ์, 3:ยกเลิก
                'FTXthStaApv'           => !empty($aDataDocument['ohdTWIStaApv']) ? $aDataDocument['ohdTWIStaApv'] : NULL,
                'FTXthStaPrcStk'        => !empty($aDataDocument['ohdTWIStaPrcStk']) ? $$aDataDocument['ohdTWIStaPrcStk'] : NULL,
                'FTXthStaDelMQ'         => !empty($aDataDocument['ohdTWIStaDelMQ']) ? $aDataDocument['ohdTWIStaDelMQ'] : NULL,
                'FNXthStaDocAct'        => $aDataDocument['ocbTWIStaDocAct'] == '' ? 0 : 1,
                'FNXthStaRef'           => null,
                'FTRsnCode'             => $aDataDocument['oetTWIReasonCode'] == '' ? null : $aDataDocument['oetTWIReasonCode'],
                'FDLastUpdOn'           => date('Y-m-d H:i:s'),
                'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
                'FDCreateOn'            => date('Y-m-d H:i:s'),
                'FTCreateBy'            => $this->session->userdata('tSesUsername')
            );
            $this->db->trans_begin();

            // Check Auto GenCode Document
            if ($tTWIAutoGenCode == '1') {
                $aStoreParam = array(
                    "tTblName"   => 'TCNTPdtTwiHD',                           
                    "tDocType"   => 5,                                          
                    "tBchCode"   => $aDataDocument['oetSOFrmBchCode'],                                 
                    "tShpCode"   => "",                               
                    "tPosCode"   => "",                     
                    "dDocDate"   => date("Y-m-d H:i:s")       
                );
                $aAutogen   	          = FCNaHAUTGenDocNo($aStoreParam);
                $aDataWhere['FTXthDocNo'] = $aAutogen[0]["FTXxhDocNo"];
            } else {
                $aDataWhere['FTXthDocNo'] = $tTWIDocNo;
            }

            // Add Update Document HD
            $this->Transferreceiptout_model->FSxMTWIAddUpdateHD($aDataMaster, $aDataWhere, $aTableAddUpdate);

            // Update Doc No Into Doc Temp
            $this->Transferreceiptout_model->FSxMTWIAddUpdateDocNoToTemp($aDataWhere, $aTableAddUpdate);

            // Move Doc DTTemp To DT
            $this->Transferreceiptout_model->FSaMTWIMoveDtTmpToDt($aDataWhere, $aTableAddUpdate);

            // Check Status Transection DB
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '900',
                    'tStaMessg' => "Error Unsucess Add Document."
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaCallBack'  => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'   => $aDataWhere['FTXthDocNo'],
                    'nStaReturn'    => '1',
                    'tStaMessg'     => 'Success Add Document.'
                );
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaReturn' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //Event แก้ไข HD - DT
    public function FSoCTWOEditEventDoc(){
        try {
            $aDataDocument          = $this->input->post();
            $tTWIAutoGenCode        = (isset($aDataDocument['ocbTWIStaAutoGenCode'])) ? 1 : 0;
            $tTWIDocNo              = (isset($aDataDocument['oetTWIDocNo'])) ? $aDataDocument['oetTWIDocNo'] : '';
            $tTWIDocDate            = $aDataDocument['oetTWIDocDate'] . " " . $aDataDocument['oetTWIDocTime'];
            $tTWIStaDocAct          = (isset($aDataDocument['ocbTWIStaDocAct'])) ? 1 : 0;
            $tTWIVATInOrEx          = $aDataDocument['ohdTWIFrmSplInfoVatInOrEx'];
            $tTWISessionID          = $this->session->userdata('tSesSessionID');

            // Get Data Comp.
            $nLangEdit      = $this->session->userdata("tLangEdit");
            $aDataWhereComp = array('FNLngID' => $nLangEdit);
            $tAPIReq        = "";
            $tMethodReq     = "GET";
            $aCompData      = $this->Company_model->FSaMCMPList($tAPIReq, $tMethodReq, $aDataWhereComp);

            $aCalDTTempParams = [
                'tDocNo'        => '',
                'tBchCode'      => $aDataDocument['oetSOFrmBchCode'],
                'tSessionID'    => $tTWISessionID,
                'tDocKey'       => 'TCNTPdtTwiHD'
            ];

            $aCalDTTempForHD    = $this->FSaCTWOCalDTTempForHD($aCalDTTempParams);

            // Array Data Table Document
            $aTableAddUpdate = array(
                'tTableHD'      => 'TCNTPdtTwiHD',
                'tTableHDDis'   => '-',
                'tTableHDSpl'   => '-',
                'tTableDT'      => 'TCNTPdtTwiDT',
                'tTableDTDis'   => '-',
                'tTableStaGen'  => 5
            );

            // Array Data Where Insert
            $aDataWhere = array(
                'FTBchCode'         => $aDataDocument['oetSOFrmBchCode'],
                'FTXthDocNo'        => $tTWIDocNo,
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FTSessionID'       => $this->session->userdata('tSesSessionID'),
                'FTXthVATInOrEx'    => $tTWIVATInOrEx
            );

            
            if($aDataDocument['ocmSelectTransTypeIN'] == 'SPL'){
                //ประเภทคลังสินค้า
                $tRsnType = 3;

                //ร้านค้า
                $tShopFrm = null;
                $tShopTo  = null;

                //คลัง
                $tWahFrm  = null;
                $tWahTo   = $aDataDocument['oetTRINWahFromCode'] == '' ? null : $aDataDocument['oetTRINWahFromCode'];

                //ผู้จำหน่าย
                $tSplCode = $aDataDocument['oetTRINSplFromCode'] == '' ? null : $aDataDocument['oetTRINSplFromCode'];

                //แหล่งอื่น
                $tOther   = null;
            }else{
                //ประเภทคลังสินค้า
                $tRsnType = 4;

                //ร้านค้า
                $tShopFrm = null;
                $tShopTo  = null;

                //คลัง
                $tWahFrm  = null;
                $tWahTo   = $aDataDocument['oetTRINWahEtcCode'] == '' ? null : $aDataDocument['oetTRINWahEtcCode'];

                //ผู้จำหน่าย
                $tSplCode = null;

                //แหล่งอื่น
                $tOther   = $aDataDocument['oetTWIINEtc'] == '' ? null : $aDataDocument['oetTWIINEtc'];
            }

            //เครื่องจุดขาย
            $tPosFrm  = null;
            $tPosTo   = null;
            

            // Array Data HD Master
            $aDataMaster = array(
                'FTBchCode'             => $aDataDocument['oetSOFrmBchCode'],
                'FTXthDocNo'            => $tTWIDocNo,
                'FNXthDocType'          => 1,
                'FTXthTypRefFrm'        => $tRsnType,
                'FDXthDocDate'          => (!empty($tTWIDocDate)) ? $tTWIDocDate : NULL,
                'FTXthVATInOrEx'        => $tTWIVATInOrEx,
                'FTDptCode'             => $this->session->userdata('tSesUsrDptCode') == '' ? null : $this->session->userdata('tSesUsrDptCode'),
                'FTXthMerCode'          => '',
                'FTXthShopFrm'          => $tShopFrm,
                'FTXthShopTo'           => $tShopTo,
                'FTXthWhFrm'            => $tWahFrm,
                'FTXthWhTo'             => $tWahTo,
                'FTXthPosFrm'           => $tPosFrm,
                'FTXthPosTo'            => $tPosTo,
                'FTSplCode'             => $tSplCode,
                'FTXthOther'            => $tOther,
                'FTUsrCode'             => $this->session->userdata('tSesUserCode'),
                'FTSpnCode'             => null,
                'FTXthApvCode'          => $this->session->userdata('tSesUsername'),
                'FTXthRefExt'           => $aDataDocument['oetTWIRefExtDoc'],
                'FDXthRefExtDate'       => $aDataDocument['oetTWIRefExtDocDate'],
                'FTXthRefInt'           => $aDataDocument['oetTWIRefIntDoc'],
                'FDXthRefIntDate'       => $aDataDocument['oetTWIRefIntDocDate'],
                'FNXthDocPrint'         => 0,
                'FCXthTotal'            => $aCalDTTempForHD['FCXphTotal'],
                'FCXthVat'              => $aCalDTTempForHD['FCXphVat'],
                'FCXthVatable'          => $aCalDTTempForHD['FCXphVatable'],
                'FTXthRmk'              => $aDataDocument['otaTWIFrmInfoOthRmk'],
                'FTXthStaDoc'           => 1, //สถานะ เอกสาร  1:สมบูรณ์, 2:ไม่สมบูรณ์, 3:ยกเลิก
                'FTXthStaApv'           => !empty($aDataDocument['ohdTWIStaApv']) ? $aDataDocument['ohdTWIStaApv'] : NULL,
                'FTXthStaPrcStk'        => !empty($aDataDocument['ohdTWIStaPrcStk']) ? $$aDataDocument['ohdTWIStaPrcStk'] : NULL,
                'FTXthStaDelMQ'         => !empty($aDataDocument['ohdTWIStaDelMQ']) ? $aDataDocument['ohdTWIStaDelMQ'] : NULL,
                'FNXthStaDocAct'        => $aDataDocument['ocbTWIStaDocAct'] == '' ? 0 : 1,
                'FNXthStaRef'           => null,
                'FTRsnCode'             => $aDataDocument['oetTWIReasonCode'] == '' ? null : $aDataDocument['oetTWIReasonCode'],
                'FDLastUpdOn'           => date('Y-m-d H:i:s'),
                'FTLastUpdBy'           => $this->session->userdata('tSesUsername'),
            );
            $this->db->trans_begin();

            // Check Auto GenCode Document
            if ($tTWIAutoGenCode == '1') {

                $aStoreParam = array(
                    "tTblName"   => 'TCNTPdtTwiHD',                           
                    "tDocType"   => 5,                                          
                    "tBchCode"   => $aDataDocument['oetSOFrmBchCode'],                                 
                    "tShpCode"   => "",                               
                    "tPosCode"   => "",                     
                    "dDocDate"   => date("Y-m-d H:i:s")       
                );
                $aAutogen   	          = FCNaHAUTGenDocNo($aStoreParam);
                $aDataWhere['FTXthDocNo'] = $aAutogen[0]["FTXxhDocNo"];
            } else {
                $aDataWhere['FTXthDocNo'] = $tTWIDocNo;
            }

            // Add Update Document HD
            $this->Transferreceiptout_model->FSxMTWIAddUpdateHD($aDataMaster, $aDataWhere, $aTableAddUpdate);

            // Update Doc No Into Doc Temp
            $this->Transferreceiptout_model->FSxMTWIAddUpdateDocNoToTemp($aDataWhere, $aTableAddUpdate);

            // Move Doc DTTemp To DT
            $this->Transferreceiptout_model->FSaMTWIMoveDtTmpToDt($aDataWhere, $aTableAddUpdate);

            // Check Status Transection DB
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent' => '900',
                    'tStaMessg' => "Error Unsucess Add Document."
                );
            } else {
                $this->db->trans_commit();
                $aReturnData = array(
                    'nStaCallBack'  => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'   => $aDataWhere['FTXthDocNo'],
                    'nStaReturn'    => '1',
                    'tStaMessg'     => 'Success Add Document.'
                );
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaReturn' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //คำนวณค่าจาก DT Temp ให้ HD
    private function FSaCTWOCalDTTempForHD($paParams) {
        $aCalDTTemp = $this->Transferreceiptout_model->FSaMTWICalInDTTemp($paParams);
        if (isset($aCalDTTemp) && !empty($aCalDTTemp)) {
            $aCalDTTempItems = $aCalDTTemp[0];
            // คำนวณหา ยอดปัดเศษ ให้ HD(FCXphRnd)
            $pCalRoundParams = [
                'FCXphAmtV'     => $aCalDTTempItems['FCXphAmtV'],
                'FCXphAmtNV'    => $aCalDTTempItems['FCXphAmtNV']
            ];
            $aRound = $this->FSaCTWOCalRound($pCalRoundParams);
            // คำนวณหา ยอดรวม ให้ HD(FCXphGrand)
            $nRound = $aRound['nRound'];
            $cGrand = $aRound['cAfRound'];

            // จัดรูปแบบข้อความ จากตัวเลขเป็นข้อความ HD(FTXphGndText)
            $tGndText = FCNtNumberToTextBaht(number_format($cGrand, 2));
            $aCalDTTempItems['FCXphRnd']        = $nRound;
            $aCalDTTempItems['FCXphGrand']      = $cGrand;
            $aCalDTTempItems['FTXphGndText']    = $tGndText;
            return $aCalDTTempItems;
        }
    }

    //หาค่าปัดเศษ HD(FCXphRnd)
    private function FSaCTWOCalRound($paParams) {
        $tOptionRound = '1';  // ปัดขึ้น
        $cAmtV  = $paParams['FCXphAmtV'];
        $cAmtNV = $paParams['FCXphAmtNV'];
        $cBath  = $cAmtV + $cAmtNV;
        // ตัดเอาเฉพาะทศนิยม
        $nStang = explode('.', number_format($cBath, 2))[1];
        $nPoint = 0;
        $nRound = 0;
        /* ====================== ปัดขึ้น ================================ */
        if ($tOptionRound == '1') {
            if ($nStang >= 1 and $nStang < 25) {
                $nPoint = 25;
                $nRound = $nPoint - $nStang;
            }
            if ($nStang > 25 and $nStang < 50) {
                $nPoint = 50;
                $nRound = $nPoint - $nStang;
            }
            if ($nStang > 50 and $nStang < 75) {
                $nPoint = 75;
                $nRound = $nPoint - $nStang;
            }
            if ($nStang > 75 and $nStang < 100) {
                $nPoint = 100;
                $nRound = $nPoint - $nStang;
            }
        }
        /* ====================== ปัดขึ้น ================================ */

        /* ====================== ปัดลง ================================ */
        if ($tOptionRound != '1') {
            if ($nStang >= 1 and $nStang < 25) {
                $nPoint = 1;
                $nRound = $nPoint - $nStang;
            }
            if ($nStang > 25 and $nStang < 50) {
                $nPoint = 25;
                $nRound = $nPoint - $nStang;
            }
            if ($nStang > 50 and $nStang < 75) {
                $nPoint = 50;
                $nRound = $nPoint - $nStang;
            }
            if ($nStang > 75 and $nStang < 100) {
                $nPoint = 75;
                $nRound = $nPoint - $nStang;
            }
        }
        /* ====================== ปัดลง ================================ */
        $cAfRound = floatval($cBath) + floatval($nRound / 100);
        return [
            'tRoundType' => $tOptionRound,
            'cBath' => $cBath,
            'nPoint' => $nPoint,
            'nStang' => $nStang,
            'nRound' => $nRound,
            'cAfRound' => $cAfRound
        ];
    }

    //ยกเลิกเอกสาร
    public function FSoCTWOEventCancel(){
        $tTWIDocNo = $this->input->post('tTWIDocNo');

        $aDataUpdate = array(
            'FTXthDocNo' => $tTWIDocNo,
        );
        $aStaApv    = $this->Transferreceiptout_model->FSvMTWICancel($aDataUpdate); 
        
         //อัพเดทใน CN ให้กลับไปใช้งานได้
        $this->Transferreceiptout_model->FSvMCheckDocumentInCN('CANCEL',$aDataUpdate); 

        if($aStaApv['rtCode'] == 1){
            $aApv = array(
                'nSta' => 1,
                'tMsg' => "Cancel done.",
            );
        }else{
            $aApv = array(
                'nSta' => 2,
                'tMsg' => "Not Cancel.",
            );
        }
        echo json_encode($aApv);
    }

    //อัพเดทข้อมูล เป็นเเถว
    public function FSoCTWOEditPdtIntoDocDTTemp(){
        try {
            $tTWIBchCode    = $this->input->post('tTWIBchCode');
            $tTWIDocNo      = $this->input->post('tTWIDocNo');
            $tTWIVATInOrEx  = $this->input->post('tTWIVATInOrEx');
            $nTWISeqNo      = $this->input->post('nTWISeqNo');
            $tTWIFieldName  = $this->input->post('tTWIFieldName');
            $tTWIValue      = $this->input->post('tTWIValue');
            $tTWISessionID  = $this->session->userdata('tSesSessionID');

            $aDataWhere = array(
                'tTWIBchCode'   => $tTWIBchCode,
                'tTWIDocNo'     => $tTWIDocNo,
                'nTWISeqNo'     => $nTWISeqNo,
                'tTWISessionID' => $this->session->userdata('tSesSessionID'),
                'tDocKey'       => 'TCNTPdtTwiHD',
            );
            $aDataUpdateDT = array(
                'tTWIFieldName'  => $tTWIFieldName,
                'tTWIValue'      => $tTWIValue
            );

            $this->db->trans_begin();
            $this->Transferreceiptout_model->FSaMTWIUpdateInlineDTTemp($aDataUpdateDT, $aDataWhere);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturnData = array(
                    'nStaEvent'     => '500',
                    'tStaMessg'     => "Error Update Inline Into Document DT Temp."
                );
            } else {
                $this->db->trans_commit();

                $aCalcDTTempParams = array(
                    'tDataDocEvnCall'   => '1',
                    'tDataVatInOrEx'    => $tTWIVATInOrEx,
                    'tDataDocNo'        => $tTWIDocNo,
                    'tDataDocKey'       => 'TCNTPdtTwiHD',
                    'tDataSeqNo'        => $nTWISeqNo
                );
                $tStaCalDocDTTemp = FCNbHCallCalcDocDTTemp($aCalcDTTempParams);
                if ($tStaCalDocDTTemp === TRUE) {
                    $aReturnData = array(
                        'nStaEvent'     => '1',
                        'tStaMessg'     => "Update And Calcurate Process Document DT Temp Success."
                    );
                } else {
                    $aReturnData = array(
                        'nStaEvent'     => '500',
                        'tStaMessg'     => "Error Cannot Calcurate Document DT Temp."
                    );
                }
            }
        } catch (Exception $Error) {
            $aReturnData = array(
                'nStaEvent' => '500',
                'tStaMessg' => $Error->getMessage()
            );
        }
        echo json_encode($aReturnData);
    }

    //เลือกสินค้าจากตาราง CN - ใบสั้งขาย
    public function FSoCTWOSelectPDTInCN(){
        $tBCHCode = $this->input->post('tBCHCode');
        $tSHPCode = $this->input->post('tSHPCode');
        $tWAHCode = $this->input->post('tWAHCode');

        $aWhere = array(
            'tBCHCode' => $tBCHCode ,
            'tSHPCode' => $tSHPCode ,
            'tWAHCode' => $tWAHCode ,
            'FNLngID'  => $this->session->userdata("tLangEdit")
        );

        $aDataCN = $this->Transferreceiptout_model->FSaMTWIGetPDTInCN($aWhere);
        $aDataViewCN = array(
            'aDataCN'       => $aDataCN
        );
        $tViewCN            = $this->load->view('document/transferreceiptOut/wTransferreceiptOutCN',$aDataViewCN,true);
        $aReturnData        = array(
            'tViewPageAdd'  => $tViewCN,
            'nStaEvent'     => '1',
            'tStaMessg'     => 'Success'
        );
        echo json_encode($aReturnData);
    }

    //อนุมัติ
    public function FSoCTWOApproved(){
        $tXthDocNo      = $this->input->post('tXthDocNo');
        $tXthStaApv     = $this->input->post('tXthStaApv');
        $tUsrBchCode    = FCNtGetBchInComp();

        $aDataUpdate = array(
            'FTXthDocNo'    => $tXthDocNo,
            'FTXthApvCode'  => $this->session->userdata('tSesUsername')
        );
        $aStaApv = $this->Transferreceiptout_model->FSvMTWIApprove($aDataUpdate); 
        try{
            $aMQParams = [
                "queueName" => "TNFWAREHOSEIN",
                "params"    => [
                    "ptBchCode"     => $tUsrBchCode,
                    "ptDocNo"       => $tXthDocNo,
                    "ptDocType"     => '5',
                    "ptUser"        => $this->session->userdata('tSesUsername')
                ]
            ];
            FCNxCallRabbitMQ($aMQParams);
            $aReturn = array(
                'nStaEvent'    => '1',
                'tStaMessg'    => 'ok'
            );
            echo json_encode($aReturn);
        }catch(Exception $Error){
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => language('common/main/main', 'tApproveFail')
            );
            echo json_encode($aReturn);
            return;
        }
    }

    
}
