<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Deposit_controller extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('company/company/Company_model');
        $this->load->model('document/deposit/Deposit_model');
        $this->load->model('payment/rate/Rate_model');
        $this->load->model('company/vatrate/Vatrate_model');
        $this->load->model('company/branch/Branch_model');
        $this->load->model('company/shop/Shop_model');
        $this->load->model('authen/login/Login_model');
    }

    public function index($nBrowseType, $tBrowseOption)
    {
        $aData['nBrowseType'] = $nBrowseType;
        $aData['tBrowseOption'] = $tBrowseOption;
        $aData['aAlwEvent'] = FCNaHCheckAlwFunc('deposit/0/0');
        $aData['vBtnSave'] = FCNaHBtnSaveActiveHTML('deposit/0/0');
        $aData['nOptDecimalShow'] = FCNxHGetOptionDecimalShow();
        $aData['nOptDecimalSave'] = FCNxHGetOptionDecimalSave();
        $this->load->view('document/deposit/wDeposit', $aData);
    }

    /**
     * Functionality : Main Page List
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : List Page
     * Return Type : View
     */
    public function FSxCDepositList()
    {
        $nLangResort = $this->session->userdata("tLangID");
        $nLangEdit = $this->session->userdata("tLangEdit");
        $aData = array(
            'FTBchCode'    => $this->session->userdata("tSesUsrBchCode"),
            'FTShpCode'    => '',
            'nPage' => 1,
            'nRow' => 20,
            'FNLngID' => $nLangEdit,
            'tSearchAll' => ''
        );

        $aBchData = $this->Branch_model->FSnMBCHList($aData);
        $aShpData = $this->Shop_model->FSaMSHPList($aData);
        $aDataMaster = array(
            'aBchData' => $aBchData,
            'aShpData' => $aShpData
        );

        $this->load->view('document/deposit/wDepositList', $aDataMaster);
    }

    /**
     * Functionality : Get HD Table List
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : HD Table List
     * Return Type : View
     */
    public function FSxCDepositDataTable()
    {
        $tAdvanceSearchData = $this->input->post('oAdvanceSearch');
        $nPage = $this->input->post('nPageCurrent');
        $aAlwEvent = FCNaHCheckAlwFunc('deposit/0/0');
        $nOptDecimalShow = FCNxHGetOptionDecimalShow();



        if ($nPage == '' || $nPage == null) {
            $nPage = 1;
        } else {
            $nPage = $this->input->post('nPageCurrent');
        }

        $nLangResort = $this->session->userdata("tLangID");
        $nLangEdit = $this->session->userdata("tLangEdit");
        $aData = array(
            'FNLngID' => $nLangEdit,
            'nPage' => $nPage,
            'nRow' => 10,
            'aAdvanceSearch' => json_decode($tAdvanceSearchData, true)
        );

        $aResList = $this->Deposit_model->FSaMHDList($aData);
        $aGenTable = array(
            'aAlwEvent' => $aAlwEvent,
            'aDataList' => $aResList,
            'nPage' => $nPage,
            'nOptDecimalShow' => $nOptDecimalShow
        );

        $this->load->view('document/deposit/wDepositDatatable', $aGenTable);
    }

    /**
     * Functionality : Add Page
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Add Page
     * Return Type : View
     */
    public function FSxCDepositAddPage()
    {
        $tUserSessionID = $this->session->userdata('tSesSessionID');

        $aInfoWhere = array(
            "tBchCode" => $this->input->post("tBchCode"),
            "FTXthDocKey" => "TCNTPdtAdjStkHD"
        );
        $this->Deposit_model->FSxMDeleteDoctemForNewEvent($aInfoWhere);

        $aClearInTmpParams = [
            'tUserSessionID' => $tUserSessionID,
            'tDocKey' => 'TFNTBnkDplHD'
        ];
        $this->Deposit_model->FSxMClearInTmp($aClearInTmpParams);

        $nOptDecimalShow = FCNxHGetOptionDecimalShow();
        $nOptDocSave = FCNnHGetOptionDocSave();
        $nOptScanSku = FCNnHGetOptionScanSku();
        // $nLangResort = $this->session->userdata("tLangID");
        $nLangEdit = $this->session->userdata("tLangEdit");

        $aDataWhere  = array(
            'FNLngID' => $nLangEdit
        );

        $tAPIReq = "";
        $tMethodReq = "GET";
        $aResList = $this->Company_model->FSaMCMPList($tAPIReq, $tMethodReq, $aDataWhere);

        if ($aResList['rtCode'] == '1') {
            $tBchCode = $aResList['raItems']['rtCmpBchCode'];
            $tCompCode = $aResList['raItems']['rtCmpCode'];
            $tCmpRteCode = $aResList['raItems']['rtCmpRteCode'];
            $tVatCode = $aResList['raItems']['rtVatCodeUse'];
            $aVatRate = FCNoHCallVatlist($tVatCode);
            if (count($aVatRate) != 0) {
                $cVatRate = $aVatRate['FCVatRate'][0];
            } else {
                $cVatRate = "";
            }

            $aDataRate = array(
                'FTRteCode' => $tCmpRteCode,
                'FNLngID' => $nLangEdit
            );

            $aResultRte = $this->Rate_model->FSaMRTESearchByID($aDataRate);
            $cXthRteFac = $aResultRte['raItems']['rcRteRate'];
        } else {
            $tBchCode = "";
            $tCompCode = "";
            $tCmpRteCode = "";
            $tVatCode = "";
            $cVatRate = "";
            $cXthRteFac = "";
        }

        $tUsrLogin = $this->session->userdata('tSesUsername');
        $tDptCode = FCNnDOCGetDepartmentByUser($tUsrLogin);

        $aDataShp  = array(
            'FNLngID'   => $nLangEdit,
            'tUsrLogin' => $tUsrLogin
        );
        $aDataUserGroup = $this->Deposit_model->FStTFWGetShpCodeForUsrLogin($aDataShp);

        if ($aDataUserGroup == '') {
            $tBchCode = '';
            $tBchName = '';
            $tMchCode = '';
            $tMchName = '';
            $tShpCodeStart = "";
            $tShpNameStart = "";
            $tShpCodeEnd = "";
            $tShpNameEnd = "";
            $tWahCodeStart = "";
            $tWahNameStart = "";
            $tWahCodeEnd = "";
            $tWahNameEnd = "";
            $tShpTypeStart = "";
        } else {
            $tShpTypeStart = $aDataUserGroup["FTShpType"];
            // เช็ค user ว่ามีการผูกสาขาไว้หรือไม่
            if ($aDataUserGroup["FTBchCode"] == '') {
                // ถ้าว่าง ให้ Get Option Def
                $tBchCode = '';
                $tBchName = '';
            } else {
                $tBchCode = $aDataUserGroup["FTBchCode"];
                $tBchName = $aDataUserGroup["FTBchName"];
            }
            // เช็ค user ว่ามีการผูกสาขาไว้หรือไม่
            $tMchCode = $aDataUserGroup["FTMerCode"];
            $tMchName = $aDataUserGroup["FTMerName"];
            // เช็ค user ว่ามีการผูกร้านค้าไว้หรือไม่
            if ($aDataUserGroup["FTShpCode"] == '') {
                // ถ้าว่าง ให้ Get Option Def
                $tShpCodeStart = "";
                $tShpNameStart = "";
                $tShpCodeEnd = "";
                $tShpNameEnd = "";
                $tWahCodeStart = "";
                $tWahNameStart = "";
                $tWahCodeEnd = "";
                $tWahNameEnd = "";
            } else {
                $tShpCodeStart      = $aDataUserGroup["FTShpCode"];
                $tShpNameStart      = $aDataUserGroup["FTShpName"];
                $tShpCodeEnd        = "";
                $tShpNameEnd        = "";
                $tWahCodeStart      = $aDataUserGroup["FTWahCode"];
                $tWahNameStart      = $aDataUserGroup["FTWahName"];
                $tWahCodeEnd        = "";
                $tWahNameEnd        = "";
            }
        }

        $aDataAdd = array(
            'aResult'           =>  array('rtCode' => '99'),
            'aResultOrdDT'      =>  array('rtCode' => '99'),
            'nOptDecimalShow'   =>  $nOptDecimalShow,
            'nOptScanSku'       =>  $nOptScanSku,
            'nOptDocSave'       =>  $nOptDocSave,
            'tCmpRteCode'       =>  $tCmpRteCode,
            'tVatCode'          =>  $tVatCode,
            'cVatRate'          =>  $cVatRate,
            'cXthRteFac'        =>  $cXthRteFac,
            'tDptCode'          =>  $tDptCode,
            'tMchCode'          =>  $tMchCode,
            'tMchName'          =>  $tMchName,
            'tShpCodeStart'     =>  $tShpCodeStart,
            'tShpNameStart'     =>  $tShpNameStart,
            'tShpTypeStart'     =>  $tShpTypeStart,
            'tShpCodeEnd'       =>  $tShpCodeEnd,
            'tShpNameEnd'       =>  $tShpNameEnd,
            'tWahCodeStart'     =>  $tWahCodeStart,
            'tWahNameStart'     =>  $tWahNameStart,
            'tWahCodeEnd'       =>  $tWahCodeEnd,
            'tWahNameEnd'       =>  $tWahNameEnd,
            'tCompCode'         =>  $tCompCode,
            'tBchCode'          =>  $tBchCode,
            'tBchName'          =>  $tBchName,
            'tBchCompCode'      =>  FCNtGetBchInComp(),
            'tBchCompName'      =>  FCNtGetBchNameInComp()
        );
        $this->load->view('document/deposit/wDepositPageadd', $aDataAdd);
    }

    /**
     * Functionality : Add Event
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaCDepositAddEvent()
    {
        try {
            $tUserSessionID = $this->session->userdata('tSesSessionID');
            $tUserLoginCode = $this->session->userdata('tSesUsername');
            $tDocDate = $this->input->post('oetDepositDocDate') . " " . $this->input->post('oetDepositDocTime');

            $aCalInTmpParams = [
                'tUserSessionID' => $tUserSessionID,
                'tDocKey' => 'TFNTBnkDplHD'
            ];
            $CalInTemp = $this->Deposit_model->FSxMCalInTmp($aCalInTmpParams);

            $aDataMaster = array(
                'tIsAutoGenCode' => $this->input->post('ocbDepositAutoGenCode'),
                'FTBchCode' => $this->input->post('oetDepositBchCode'),
                'FTBdhDocNo' => $this->input->post('oetDepositDocNo'),
                'FTBdtCode' => $this->input->post('oetDepositTypeCode'), // รหัสประเภทใบนำฝาก
                'FDBdhDate' => $tDocDate,
                'FTMerCode' => $this->input->post('oetDepositMchCode'),
                'FTShpCode' => $this->input->post('oetDepositShpCode'),
                'FTUsrCode' => $tUserLoginCode, // ผู้บันทึก
                'FTBdhUsrSender' => $this->input->post('oetDepositUsrCode'), // ผู้นำส่ง
                'FTBdhUsrApv' => '', // ผู้อนุมัติ
                'FTBbkCode' => $this->input->post('oetDepositAccountCodeTo'), // รหัสสมุดบัญชี
                'FTBdhRefExt' => $this->input->post('oetDepositBdhRefExt'), // อ้างอิง เลขที่เอกสาร ภายนอก
                'FDBdhRefExtDate' => $this->input->post('oetDepositBdhRefExtDate'), // อ้างอิง วันที่เอกสาร ภายนอก
                'FCBdhTotCash' => floatval($CalInTemp['FCBddRefAmtCashTotal']), // จำนวนเงินสด(Cal in DT)
                'FCBdhTotCheque' => floatval($CalInTemp['FCBddRefAmtChequeTotal']), // จำนวนเงินเช็ค
                'FCBdhTotChqChg' => 0, // จำนวนเงินเช็ค-ชาร์จ
                'FCBdhTotChqVat' => 0, // จำนวนเงินเช็ค-ภาษี
                'FCBdhTotal' => floatval($CalInTemp['FCBddRefAmtTotal']), // จำนวนเงินรวม(Cal in DT)
                'FTBdhRmk' => $this->input->post('otaDepositBdhRmk'), // หมายเหตุ
                'FTBdhStaDoc' => '1', // สถานะ เอกสาร  1:สมบูรณ์, 2:ไม่สมบูรณ์, 3:ยกเลิก
                'FTBdhStaApv' => '', // สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว 
                'FNBdhStaDocAct' => $this->input->post('ocbDepositBdhStaDocAct'), // สถานะ เคลื่อนไหว 0:NonActive, 1:Active
                'FDLastUpdOn' => date('Y-m-d'),
                'FTLastUpdBy' => $tUserLoginCode,
                'FDCreateOn' => date('Y-m-d'),
                'FTCreateBy' => $tUserLoginCode,
            );

            $this->db->trans_begin();

            // Setup Doc No.
            if ($aDataMaster['tIsAutoGenCode'] == '1') { // Check Auto Gen Reason Code?
                // Call Auto Gencode Helper
                $aStoreParam = array(
                    "tTblName" => 'TFNTBnkDplHD',
                    "tDocType" => 7,
                    "tBchCode" => $aDataMaster["FTBchCode"],
                    "tShpCode" => "",
                    "tPosCode" => "",
                    "dDocDate" => date("Y-m-d")
                );
                $aAutogen = FCNaHAUTGenDocNo($aStoreParam);
                $aDataMaster['FTBdhDocNo'] = $aAutogen[0]["FTXxhDocNo"];

                // Auto Gen Reason Code
                /* $aGenCode = FCNaHGenCodeV5('TFNTBnkDplHD', '7');
                if ($aGenCode['rtCode'] == '1') {
                    $aDataMaster['FTBdhDocNo'] = $aGenCode['rtBdhDocNo'];
                } */
            }

            $this->Deposit_model->FSaMAddUpdateHD($aDataMaster);

            $aUpdateDocNoInTmpParams = [
                'tDocNo' => $aDataMaster['FTBdhDocNo'],
                'tDocKey' => 'TFNTBnkDplHD',
                'tUserSessionID' => $tUserSessionID
            ];
            $this->Deposit_model->FSaMUpdateDocNoInTmp($aUpdateDocNoInTmpParams); // Update DocNo ในตาราง Doctemp

            $aTempToDTParams = [
                'tDocNo' => $aDataMaster['FTBdhDocNo'],
                'tBchCode' => $aDataMaster['FTBchCode'],
                'tDocKey' => 'TFNTBnkDplHD',
                'tUserSessionID' => $tUserSessionID,
                'tUserLoginCode' => $tUserLoginCode
            ];
            $this->Deposit_model->FSaMTempToDT($aTempToDTParams); // คัดลอกข้อมูลจาก Temp to DT

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add"
                );
            } else {
                $this->db->trans_commit();
                $aReturn = array(
                    'nStaCallBack' => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn' => $aDataMaster['FTBdhDocNo'],
                    'nStaEvent'    => '1',
                    'tStaMessg' => 'Success Add'
                );
            }

            $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    /**
     * Functionality : Edit Page
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Edit Page
     * Return Type : View
     */
    public function FSvCDepositEditPage()
    {
        $tDocNo = $this->input->post('tDocNo');
        $nLangEdit = $this->session->userdata("tLangEdit");
        $nLangResort = $this->session->userdata("tLangID");
        $aLangHave = FCNaHGetAllLangByTable('TFNMRate_L');
        // $tUsrLogin = $this->session->userdata('tSesUsername');
        $tUserSessionID = $this->session->userdata("tSesSessionID");
        $tUserLevel = $this->session->userdata('tSesUsrLevel');
        // $tBchCodeLogin = $tUserLevel == 'HQ' ? FCNtGetBchInComp() : $this->session->userdata("tSesUsrBchCode");

        $aAlwEvent = FCNaHCheckAlwFunc('deposit/0/0'); // Access Control
        // Get Option Show Decimal
        $nOptDecimalShow = FCNxHGetOptionDecimalShow();
        // Get Option Scan SKU
        $nOptDocSave = FCNnHGetOptionDocSave();
        //Get Option Scan SKU
        $nOptScanSku = FCNnHGetOptionScanSku();

        // Lang ภาษา

        $nLangHave = count($aLangHave);
        if ($nLangHave > 1) {
            if ($nLangEdit != '') {
                $nLangEdit = $nLangEdit;
            } else {
                $nLangEdit = $nLangResort;
            }
        } else {
            if (@$aLangHave[0]->nLangList == '') {
                $nLangEdit = '1';
            } else {
                $nLangEdit = $aLangHave[0]->nLangList;
            }
        }

        // Get Data
        $aGetHDParams = array(
            'tDocNo' => $tDocNo,
            'nLngID' => $nLangEdit,
            'tDocKey' => 'TFNTBnkDplHD',
        );
        $aResult = $this->Deposit_model->FSaMGetHD($aGetHDParams); // Data TFNTBnkDplHD

        $aDTToTempParams = [
            'tDocNo' => $tDocNo,
            'tDocKey' => 'TFNTBnkDplHD',
            'tBchCode' => isset($aResult['raItems']['FTBchCode']) ? $aResult['raItems']['FTBchCode'] : '',
            'tUserSessionID' => $tUserSessionID,
            'nLngID' => $nLangEdit
        ];
        $this->Deposit_model->FSaMDTToTemp($aDTToTempParams);

        $aDataEdit = array(
            'nOptDecimalShow' => $nOptDecimalShow,
            'nOptDocSave' => $nOptDocSave,
            'nOptScanSku' => $nOptScanSku,
            'aResult' => $aResult,
            'aAlwEvent' => $aAlwEvent,
            'tBchCompCode' => FCNtGetBchInComp(),
            'tBchCompName' => FCNtGetBchNameInComp()
        );
        $this->load->view('document/deposit/wDepositPageadd', $aDataEdit);
    }

    /**
     * Functionality : Edit Event
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaCDepositEditEvent()
    {
        try {
            $tUserSessionID = $this->session->userdata('tSesSessionID');
            $tUserLoginCode = $this->session->userdata('tSesUsername');
            $tDocDate = $this->input->post('oetDepositDocDate') . " " . $this->input->post('oetDepositDocTime');

            $aCalInTmpParams = [
                'tUserSessionID' => $tUserSessionID,
                'tDocKey' => 'TFNTBnkDplHD'
            ];
            $CalInTemp = $this->Deposit_model->FSxMCalInTmp($aCalInTmpParams);

            $aDataMaster = array(
                'tIsAutoGenCode' => $this->input->post('ocbDepositAutoGenCode'),
                'FTBchCode' => $this->input->post('oetDepositBchCode'),
                'FTBdhDocNo' => $this->input->post('oetDepositDocNo'),
                'FTBdtCode' => $this->input->post('oetDepositTypeCode'), // รหัสประเภทใบนำฝาก
                'FDBdhDate' => $tDocDate,
                'FTMerCode' => $this->input->post('oetDepositMchCode'),
                'FTShpCode' => $this->input->post('oetDepositShpCode'),
                'FTUsrCode' => $tUserLoginCode, // ผู้บันทึก
                'FTBdhUsrSender' => $this->input->post('oetDepositUsrCode'), // ผู้นำส่ง
                'FTBdhUsrApv' => '', // ผู้อนุมัติ
                'FTBbkCode' => $this->input->post('oetDepositAccountCodeTo'), // รหัสสมุดบัญชี
                'FTBdhRefExt' => $this->input->post('oetDepositBdhRefExt'), // อ้างอิง เลขที่เอกสาร ภายนอก
                'FDBdhRefExtDate' => $this->input->post('oetDepositBdhRefExtDate'), // อ้างอิง วันที่เอกสาร ภายนอก
                'FCBdhTotCash' => floatval($CalInTemp['FCBddRefAmtCashTotal']), // จำนวนเงินสด(Cal in DT)
                'FCBdhTotCheque' => floatval($CalInTemp['FCBddRefAmtChequeTotal']), // จำนวนเงินเช็ค
                'FCBdhTotChqChg' => 0, // จำนวนเงินเช็ค-ชาร์จ
                'FCBdhTotChqVat' => 0, // จำนวนเงินเช็ค-ภาษี
                'FCBdhTotal' => floatval($CalInTemp['FCBddRefAmtTotal']), // จำนวนเงินรวม(Cal in DT)
                'FTBdhRmk' => $this->input->post('otaDepositBdhRmk'), // หมายเหตุ
                'FTBdhStaDoc' => '1', // สถานะ เอกสาร  1:สมบูรณ์, 2:ไม่สมบูรณ์, 3:ยกเลิก
                'FTBdhStaApv' => '', // สถานะ อนุมัติ เอกสาร ว่าง:ยังไม่ทำ, 1:อนุมัติแล้ว 
                'FNBdhStaDocAct' => $this->input->post('ocbDepositBdhStaDocAct'), // สถานะ เคลื่อนไหว 0:NonActive, 1:Active
                'FDLastUpdOn' => date('Y-m-d'),
                'FTLastUpdBy' => $tUserLoginCode,
                'FDCreateOn' => date('Y-m-d'),
                'FTCreateBy' => $tUserLoginCode,
            );
            
            $this->db->trans_begin();

            $this->Deposit_model->FSaMAddUpdateHD($aDataMaster);

            $aUpdateDocNoInTmpParams = [
                'tDocNo' => $aDataMaster['FTBdhDocNo'],
                'tDocKey' => 'TFNTBnkDplHD',
                'tUserSessionID' => $tUserSessionID
            ];
            $this->Deposit_model->FSaMUpdateDocNoInTmp($aUpdateDocNoInTmpParams); // Update DocNo ในตาราง Doctemp

            $aTempToDTParams = [
                'tDocNo' => $aDataMaster['FTBdhDocNo'],
                'tBchCode' => $aDataMaster['FTBchCode'],
                'tDocKey' => 'TFNTBnkDplHD',
                'tUserSessionID' => $tUserSessionID,
                'tUserLoginCode' => $tUserLoginCode
            ];
            $this->Deposit_model->FSaMTempToDT($aTempToDTParams); // คัดลอกข้อมูลจาก Temp to DT

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Add"
                );
            } else {
                $this->db->trans_commit();
                $aReturn = array(
                    'nStaCallBack' => $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn' => $aDataMaster['FTBdhDocNo'],
                    'nStaEvent'    => '1',
                    'tStaMessg' => 'Success Add'
                );
            }

            $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    /**
     * Functionality : Check Doc No. Duplicate
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : String
     */
    public function FStCDepositUniqueValidate()
    {
        $aStatus = ['bStatus' => false];

        if ($this->input->is_ajax_request()) { // Request check
            $tDepositDocCode = $this->input->post('tDepositCode');
            $bIsDocNoDup = $this->Deposit_model->FSbMCheckDuplicate($tDepositDocCode);

            if ($bIsDocNoDup) { // If have record
                $aStatus['bStatus'] = true;
            }
        } else {
            echo 'Method Not Allowed';
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aStatus));
    }

    /**
     * Functionality : Cancel Document
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : String
     */
    public function FStCDepositDocCancel()
    {

        $tDocNo = $this->input->post('tDocNo');

        $this->db->trans_begin();

        $aDocCancelParams = array(
            'tDocNo' => $tDocNo,
        );
        $aStaCancel = $this->Deposit_model->FSaMDocCancel($aDocCancelParams);

        if ($aStaCancel['rtCode'] == 1) {
            $this->db->trans_commit();
            $aCancel = array(
                'nSta' => 1,
                'tMsg' => "Cancel Success",
            );
        } else {
            $this->db->trans_rollback();
            $aCancel = array(
                'nSta' => 2,
                'tMsg' => "Cancel Fail",
            );
        }
        echo json_encode($aCancel);
    }

    /**
     * Functionality : Approve Document
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : String
     */
    public function FStCDepositDocApprove()
    {

        $tDocNo  = $this->input->post('tDocNo');
        // $tStaApv = $this->input->post('tStaApv');
        $tDocType = $this->input->post('tDocType');
        $tUsrBchCode = FCNtGetBchInComp();

        $this->db->trans_begin();

        $aDocApproveParams = array(
            'tDocNo' => $tDocNo,
            'tApvCode' => $this->session->userdata('tSesUsername')
        );
        $this->Deposit_model->FSaMDocApprove($aDocApproveParams);

        try {
            $aMQParams = [
                "queueName" => "BR_QTransfer".$tUsrBchCode,
                "params" => [
                    "ptFunction" => "BankDepositSlip", // ชื่อ Function
                    "ptSource" => "BCH.AdaStoreBack", // ต้นทาง
                    "ptDest" => "BCH.MQ_Rcv", // ปลายทาง
                    "ptFilter" => $tUsrBchCode, // รหัสสาขาตัวเอง
                    "ptData" => [
                        "ptFTBchCode" => $tUsrBchCode, // Branchcode
                        "ptFTBdhDocNo" => $tDocNo, // เลขที่เอกสาร
                    ]
                ]
            ];
            FCNxCallRabbitMQ($aMQParams);

            $this->db->trans_commit();
        } catch (\ErrorException $err) {

            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent' => '900',
                'tStaMessg' => language('common/main/main', 'tApproveFail')
            );
            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($aReturn));
        }
    }

    /**
     * Functionality : Delete Document
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : String
     */
    public function FStDepositDeleteDoc()
    {

        $tDocNo = $this->input->post('tDocNo');

        $this->db->trans_begin();

        $aDelMasterParams = [
            'tDocNo' => trim($tDocNo)
        ];
        $this->Deposit_model->FSaMDelMaster($aDelMasterParams);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Cannot Delete Item.',
            );
        } else {
            $this->db->trans_commit();
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Delete Complete.',
            );
        }
        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($aStatus));
    }

    /**
     * Functionality : Delete Multiple Document
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : String
     */
    public function FStDepositDeleteMultiDoc()
    {
        $aDocNo = $this->input->post('aDocNo');

        $this->db->trans_begin();

        foreach ($aDocNo as $aItem) {
            $aDelMasterParams = [
                'tDocNo' => trim($aItem)
            ];
            $this->Deposit_model->FSaMDelMaster($aDelMasterParams);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Cannot Delete Item.',
            );
        } else {
            $this->db->trans_commit();
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Delete Complete.',
            );
        }
        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($aStatus));
    }
}
