<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

require_once (APPPATH.'third_party/PHPExcel/Classes/PHPExcel.php');
require_once (APPPATH.'third_party/PHPExcel/Classes/PHPExcel/IOFactory.php');
require_once (APPPATH.'third_party/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php');

class Cardshiftreturn_controller extends MX_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->load->model('document/cardshiftreturn/Cardshiftreturn_model');
        $this->load->model('authen/user/User_model');
        $this->load->library('upload');
        $this->load->helper('file');
    }
    
    /**
     * Functionality : Main page for Card Shift
     * Parameters : $nCardShiftReturnBrowseType, $tCardShiftReturnBrowseOption
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function index($nCardShiftReturnBrowseType, $tCardShiftReturnBrowseOption){
        $nMsgResp = array('title'=>"Province");
        $isXHR = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) === 'XMLHTTPREQUEST';
        if(!$isXHR){
            $this->load->view ('common/wHeader', $nMsgResp);
            $this->load->view ('common/wTopBar', array ('nMsgResp'=>$nMsgResp));
            $this->load->view ('common/wMenu', array ('nMsgResp'=>$nMsgResp));
        }
        $vBtnSave = FCNaHBtnSaveActiveHTML('cardShiftReturn/0/0'); // Load Html ของปุ่ม Save ที่เก็บ Session ปัจจุบัน
        $aPermission = FCNaHCheckAlwFunc("cardShiftReturn/0/0");
        $this->load->view ('document/cardshiftreturn/wCardShiftReturn', array (
            'nMsgResp'=>$nMsgResp,
            'vBtnSave' => $vBtnSave,
            'aPermission' => $aPermission,
            'nCardShiftReturnBrowseType'=>$nCardShiftReturnBrowseType,
            'tCardShiftReturnBrowseOption'=>$tCardShiftReturnBrowseOption
        ));
        
    }
    
    /**
     * Functionality : Function Call Card Shift Page List
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCardShiftReturnListPage(){
        $aAlwEvent	    = FCNaHCheckAlwFunc('cardShiftReturn/0/0');
		$aNewData  		= array( 'aAlwEvent' => $aAlwEvent );
        $this->load->view('document/cardshiftreturn/wCardShiftReturnList',$aNewData);
    }

    /**
     * Functionality : Function Call DataTables Card Shift
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCardShiftReturnDataList(){
        $nPage = $this->input->post('nPageCurrent');
        $tSearchAll = $this->input->post('tSearchAll');
        $tAdvanceSearch = json_decode($this->input->post('tAdvanceSearch'));
        $tUsrBchCode = $this->session->userdata('tSesUsrBchCom');
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}
        // Lang ภาษา
        $nLangResort = $this->session->userdata("tLangID");
	    $nLangEdit = $this->session->userdata("tLangEdit");

        $aData = array(
            'nPage' => $nPage,
            'nRow' => 10,
            'FNLngID' => $nLangEdit,
            'tSearchAll' => $tSearchAll,
            'tAdvanceSearch' => $tAdvanceSearch,
            'FTBchCode' => $tUsrBchCode,
            'tUserLevel' => $this->session->userdata("tSesUsrLevel")
        );

        $tAPIReq = "";
        $tMethodReq = "GET";
        $aResList = $this->Cardshiftreturn_model->FSaMCardShiftReturnList($tAPIReq, $tMethodReq, $aData);
        $aAlwEvent = FCNaHCheckAlwFunc('cardShiftReturn/0/0');
        $aGenTable = array(
            'aAlwEvent' => $aAlwEvent,
            'aDataList' => $aResList,
            'nPage' => $nPage,
            'tSearchAll' => $tSearchAll
        );
        $this->load->view('document/cardshiftreturn/wCardShiftReturnDataTable', $aGenTable);
    }

    /**
     * Functionality : Query card shift HD and card shift DT
     * Parameters : -
     * Creator : 26/10/2018 piya
     * Last Modified : -
     * Return : Card data on HD
     * Return Type : string
     */
    public function FSaCardShiftReturnGetCardOnHD(){
        $aData = [];
        $aData[''] = $this->input->post('');
        $aCardDT = $this->Cardshiftreturn_model->FSaMCardShiftReturnGetCardOnHD($aData);
        
        $aCardNo = [];
        if($aCardDT["rtCode"] == "1"){
            foreach ($aCardDT['raItems'] as $aResult){
                $tCode = $aResult["rtCardShiftReturnCrdCode"];
                $aCardNo[] = "'$tCode'";
            }
        }
        echo json_encode($aCardNo);
    }
    /**
     * Functionality : Function Call DataTables Card Shift
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCardShiftReturnDataSourceList(){
        $nPage              = $this->input->post('nPageCurrent');
        $tSearchAll         = $this->input->post('tSearchAll');
        $tDocNo             = $this->input->post('tDocNo');
        $aCardTypeRange     = json_decode($this->input->post('tCardTypeRange'));
        $aCardNumberRange   = json_decode($this->input->post('tCardNumberRange'));
        $aCardNumber        = json_decode($this->input->post('tCardNumber'));
        $aNotInCardNumber   = json_decode($this->input->post('tNotInCardNumber'));
        $tSetEmpty          = $this->input->post('tSetEmpty');
        $tStaShift          = $this->input->post('tStaShift');
        $tIsTemp            = $this->input->post('tIsTemp');
        $tIsDataOnly        = $this->input->post('tIsDataOnly');
        $tStaPrcDoc         = $this->input->post('tStaPrcDoc');
        $tStaDoc            = $this->input->post('tStaDoc');
        $tStaType           = $this->input->post('tStaType');
        $tLastIndex         = $this->input->post('tLastIndex');
        $tUsrBchCode        = empty($this->session->userdata('tSesUsrBchCode'))?FCNtGetBchInComp():$this->session->userdata('tSesUsrBchCode');
        $tOptionDocNo       = $this->input->post('tOptionDocNo');
        
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}
        // Lang ภาษา
        $nLangResort        = $this->session->userdata("tLangID");
        $nLangEdit          = $this->session->userdata("tLangEdit");

        /** ===================== เช็ค Validate เอกสารเบิกบัตร =========================== */
        $paParams['tSessionID']     = $this->session->userdata("tSesSessionID");
        $paParams['tSeqNo']         = "";
        $paParams['bStaCardShift']  = false;
        $paParams['nCrdStaActive']  = 1;

        if($tStaPrcDoc == "" AND $tStaDoc == ""){
            //(5)
            FSnHReturnCrdChkCrdCodeFoundInDB($paParams);

            //(4)
            FSnHReturnCrdChkCrdCodeNotDupTemp($paParams);

            //(8)
            FSnHReturnCrdChkStaShiftInCard($paParams);

            //(9)
            FSnHReturnCrdChkStaActiveInCard($paParams);
        }else{
            if($tStaPrcDoc == "" AND $tStaDoc == "1"){ // Document pending status(approve) or complete status(doc status)
                //(5)
                FSnHReturnCrdChkCrdCodeFoundInDB($paParams);

                //(4)
                FSnHReturnCrdChkCrdCodeNotDupTemp($paParams);

                //(8)
                FSnHReturnCrdChkStaShiftInCard($paParams);

                //(9)
                FSnHReturnCrdChkStaActiveInCard($paParams);
            }
        }
        /** ========================================================================== */

        /** ======================= ดึงข้อมูลจาก ตาราง Temp =========================== */
        $aDataTemp  = array(
            'nPage'         => $nPage,
            'nRow'          => 10,
            'FNLngID'       => $nLangEdit,
            'tSearchAll'    => $tSearchAll,
            'FTBchCode'     => "",
            'ptDocType'     => "cardShiftReturn"
        );
        $aGetDataCrdShiftOutFrmTemp = FSaSelectDataForDocType($aDataTemp);
        $aCardRefund                   = FSaSelectAllBySessionID("cardShiftReturn");
        /** ========================================================================== */

        $aGenTable  = array(
            'aDataList'     => $aGetDataCrdShiftOutFrmTemp,
            'tDataListAll'  => json_encode($aCardRefund),
            'rnAllRow'      => !empty($aCardRefund['rnAllRow']) ? $aCardRefund['rnAllRow'] : null,
            'ptDocType'     => "cardShiftReturn",
            'tIDElement'    => "",
            'tIsTemp'       => $tIsTemp,
            'tIsDataOnly'   => $tIsDataOnly,
            'nPage'         => $nPage,
            'tSearchAll'    => $tSearchAll,
            'tStaPrcDoc'    => $tStaPrcDoc,
            'tStaDoc'       => $tStaDoc,
            'tLastIndex'    => $tLastIndex,
            'tOptionDocNo'  => $tOptionDocNo
        );
        $this->load->view('document/cardshiftreturn/wCardShiftReturnDataSourceTable', $aGenTable);
    }
    
    /**
     * Functionality : Function CallPage Card Shift Add
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCardShiftReturnAddPage(){
        
        $nLangResort = $this->session->userdata("tLangID");
        $nLangEdit = $this->session->userdata("tLangEdit");
        $nLangEdit = $this->session->userdata("tLangEdit");
        $tUsrBchCode = empty($this->session->userdata('tSesUsrBchCode'))?FCNtGetBchInComp():$this->session->userdata('tSesUsrBchCode');

        $aData  = array(
            'FNLngID'   => $nLangEdit,
            'FTUsrCode' => $this->session->userdata("tSesUsername")
        );
        $aUser = $this->User_model->FSaMUSRByID($aData);
        
        $aDataAdd = array(
            'aResult' => ['rtCode' => '99'],
            'aUser' => $aUser,
            'aUserApv' => ['rtCode' => '99'],
            'aCardCode' => [],
            'aUserCreated' => ['rtCode' => '99'],
            'nLangEdit' => $nLangEdit,
            'tUsrBchCode' => $tUsrBchCode
        );
        
        $this->load->view('document/cardshiftreturn/wCardShiftReturnAdd', $aDataAdd);
    }
    
    /**
     * Functionality : Function CallPage Card Shift Edit
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCardShiftReturnEditPage(){
        
        $tCardShiftReturnDocNo = $this->input->post('tCardShiftReturnDocNo');
        $nLangResort = $this->session->userdata("tLangID");
        $nLangEdit = $this->session->userdata("tLangEdit");
        $tUsrBchCode = empty($this->session->userdata('tSesUsrBchCode'))?FCNtGetBchInComp():$this->session->userdata('tSesUsrBchCode');
        
        $aData  = array(
            'FTCshDocNo' => $tCardShiftReturnDocNo,
            'FNLngID'   => $nLangEdit,
            'FTUsrCode' => $this->session->userdata("tSesUsername")
        );
        $aUser = $this->User_model->FSaMUSRByID($aData);
        $aPermission = FCNaHCheckAlwFunc("cardShiftReturn/0/0");
        $tAPIReq = "";
        $tMethodReq = "GET";
        $aCardShiftReturnData = $this->Cardshiftreturn_model->FSaMCardShiftReturnSearchByID($tAPIReq, $tMethodReq, $aData);
        
        $aData['FTUsrCode'] = $aCardShiftReturnData['raItems']['rtCardShiftReturnUsrCode'];
        $aUserCreated = $this->User_model->FSaMUSRByID($aData);
        
        $aData['FTUsrCode'] = $aCardShiftReturnData['raItems']['rtCardShiftReturnApvCode'];
        $aUserApv = $this->User_model->FSaMUSRByID($aData);
        
        $aData['FTBchCode'] = $aCardShiftReturnData['raItems']['rtCardShiftReturnBchCode'];
        $aData['FTCshDocNo'] = $aCardShiftReturnData['raItems']['rtCardShiftReturnDocNo'];
        //$aCardShiftReturnCardDataInDT = $this->Cardshiftreturn_model->FSaMCardShiftReturnGetDTByDocNo($aData);

        // Remove in temp
        FCNoCARDataListDeleteOnlyTable("TFNTCrdShiftTmp");
        
        // Copy from DT to temp
        FSxDocHelperDTToTemp("cardShiftReturn", $aData['FTCshDocNo']);

        
        $aDataEdit = array(
            'aResult'       => $aCardShiftReturnData,
            'aUser'         => $aUser,
            'aUserApv'      => $aUserApv,
            'aUserCreated'  => $aUserCreated,
            'aPermission'   => $aPermission,
            'nLangEdit'     => $nLangEdit,
            'tUsrBchCode'   => $tUsrBchCode
        );
        
        $this->load->view('document/cardshiftreturn/wCardShiftReturnAdd', $aDataEdit);
    }
    
    /**
     * Functionality : Event Add Card Shift
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : Status Add Event
     * Return Type : String
     */
    public function FSaCardShiftReturnAddEvent(){
        try{
            $aDataMaster = array(
                'tIsAutoGenCode' => $this->input->post('ocbCardShiftReturnAutoGenCode'),
                'FTCshDocNo'        => $this->input->post('oetCardShiftReturnCode'),
                'FDCshDocDate'      => $this->input->post('oetCardShiftReturnDocDate') . ' ' . date('H:i:s'), 
                'FTCshDocType'      => "2", // Take return card
                'FTBchCode'         => $this->session->userdata('tSesUsrBchCom'),
                'FTUsrCode'         => $this->session->userdata("tSesUsername"),
                'FNCshCardQty'      => FSnSelectCountResult('TFNTCrdShiftTmp'),
                'aCardCode'         => json_decode($this->input->post('aCardCode')),
                'FTCshApvCode'      => $this->input->post('ohdCardShiftReturnApvCode'),
                'FTCshStaPrcDoc'    => $this->input->post('ohdCardShiftReturnCardStaPrcDoc'),
                'FTCshStaDoc'       => empty($this->input->post('hdCardShiftReturnCardStaDoc')) ? "1" : $this->input->post('hdCardShiftReturnCardStaDoc'),
                'FNCshStaDocAct'    => empty($this->input->post('hdCardShiftReturnCardStaDoc')) ? 1 : $this->input->post('hdCardShiftReturnCardStaDoc'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FNLngID'           => $this->session->userdata("tLangEdit"),
            );
            
            // Setup DocNo
            if($aDataMaster['tIsAutoGenCode'] == '1'){ // Check Auto Gen DocNo?
                // Auto Gen DocNo Code
                $aGenCode = FCNaHGenCodeV5('TFNTCrdShiftHD', 2);
                if($aGenCode['rtCode'] == '1'){
                    $aDataMaster['FTCshDocNo'] = $aGenCode['rtCshDocNo'];
                }
            }
            
            $oCountDup  = $this->Cardshiftreturn_model->FSoMCardShiftReturnCheckDuplicate($aDataMaster['FTCshDocNo']);
            $nStaDup    = $oCountDup[0]->counts;
            
            if($nStaDup == 0){
                $this->db->trans_begin();
                
                $aStaCardShiftReturnHD = $this->Cardshiftreturn_model->FSaMCardShiftReturnAddUpdateHD($aDataMaster);
                
                if($aStaCardShiftReturnHD['rtCode'] == "1"){

                    // Update DocNo on Temp
                    FCNCallUpdateDocNo($aDataMaster['FTCshDocNo'], 'TFNTCrdShiftTmp');
                    
                    // Copy from temp to DT
                    FSxDocHelperTempToDT("cardShiftReturn");
                    
                    // Remove in temp
                    FCNoCARDataListDeleteOnlyTable("TFNTCrdShiftTmp");
                    
                }
                
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
                        'tCodeReturn'	=> $aDataMaster['FTCshDocNo'],
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
     * Functionality : Event Edit Card Shift
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : Status Add Event
     * Return Type : String
     */
    public function FSaCardShiftReturnEditEvent(){
        try{
            $aDataMaster = array(
                'FTCshDocNo'        => $this->input->post('oetCardShiftReturnCode'),
                'FDCshDocDate'      => $this->input->post('oetCardShiftReturnDocDate') . ' ' . date('H:i:s'), 
                'FTCshDocType'      => "2", // Take return card
                'FTBchCode'         => $this->session->userdata('tSesUsrBchCom'),
                'FNCshCardQty'      => FSnSelectCountResult('TFNTCrdShiftTmp'),
                'aCardCode'         => json_decode($this->input->post('aCardCode')),
                'FTCshApvCode'      => $this->input->post('ohdCardShiftReturnApvCode'),
                'FTCshStaPrcDoc'    => $this->input->post('ohdCardShiftReturnCardStaPrcDoc'),
                'FTCshStaDoc'       => empty($this->input->post('hdCardShiftReturnCardStaDoc')) ? "1" : $this->input->post('hdCardShiftReturnCardStaDoc'),
                'FNCshStaDocAct'    => empty($this->input->post('hdCardShiftReturnCardStaDocAct')) ? 1 : $this->input->post('hdCardShiftReturnCardStaDocAct'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FNLngID'           => $this->session->userdata("tLangEdit"),
            );
            
            $this->db->trans_begin();
            
            $aCardShiftReturnHD = $this->Cardshiftreturn_model->FSaMCardShiftReturnSearchByID("", "", $aDataMaster);
            if( 
                ($aCardShiftReturnHD['rtCode'] == "1") // Query HD success
                && (empty($aCardShiftReturnHD["raItems"]['rtCardShiftReturnStaPrcDoc'])) // On pending approve status 
                && ($aCardShiftReturnHD["raItems"]['rtCardShiftReturnStaDoc'] == "1") // On document complete status
            ) 
            {
                $aStaCardShiftReturnHD  = $this->Cardshiftreturn_model->FSaMCardShiftReturnAddUpdateHD($aDataMaster);
                
                /*=============================================================*/
                if(($aStaCardShiftReturnHD['rtCode'] == "1")){ // Update HD success
                    
                    $paParams['tDocType']   = "cardShiftReturn";
                    $paParams['tDocNo']     = $aDataMaster['FTCshDocNo'];

                    // Remove on DT
                    FSaDeleteDatainTableDT($paParams);

                    // Copy from Temp to DT
                    FSxDocHelperTempToDT("cardShiftReturn");

                    // Remove in temp
                    FCNoCARDataListDeleteOnlyTable("TFNTCrdShiftTmp");
                    
                }
                /*=============================================================*/
            }
            
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
                    'tCodeReturn'	=> $aDataMaster['FTCshDocNo'],
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
     * Functionality : Event Edit Card Shift
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : Status Add Event
     * Return Type : String
     */
    public function FSaCardShiftReturnUpdateApvDocAndCancelDocEvent(){
        try{
            $aDataMaster = array(
                'FTCshDocNo'        => $this->input->post('oetCardShiftReturnCode'),
                'FDCshDocDate'      => $this->input->post('oetCardShiftReturnDocDate') . ' ' . date('H:i:s'), 
                'FTCshDocType'      => "2", // Take return card
                'FTBchCode'         => $this->session->userdata('tSesUsrBchCom'),
                'FTUsrCode'         => $this->session->userdata("tSesUsername"),
                'FNCshCardQty'      => FSnSelectCountResult('TFNTCrdShiftTmp'),
                'aCardCode'         => json_decode($this->input->post('aCardCode')),
                'FTCshApvCode'      => $this->input->post('ohdCardShiftReturnApvCode'),
                'FTCshStaPrcDoc'    => $this->input->post('ohdCardShiftReturnCardStaPrcDoc'),
                'FDCshApvDate'      => date('Y-m-d H:i:s'),
                'FTCshStaDoc'       => empty($this->input->post('hdCardShiftReturnCardStaDoc')) ? "1" : $this->input->post('hdCardShiftReturnCardStaDoc'),
                'FNCshStaDocAct'    => empty($this->input->post('hdCardShiftReturnCardStaDocAct')) ? 1 : $this->input->post('hdCardShiftReturnCardStaDocAct'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDLastUpdOn'       => date('Y-m-d H:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername'),
                'FDCreateOn'        => date('Y-m-d H:i:s'),
                'FNLngID'           => $this->session->userdata("tLangEdit"),
            );
            
            $this->db->trans_begin();
            
            $aCardShiftReturnHD = $this->Cardshiftreturn_model->FSaMCardShiftReturnSearchByID("", "", $aDataMaster);
            
            /*=========================Return Approve==========================*/
            if( ($aDataMaster['FTCshStaPrcDoc'] == "2") // Update status approve is true
                && ($aCardShiftReturnHD['rtCode'] == "1") // Query HD success
                && (empty($aCardShiftReturnHD["raItems"]['rtCardShiftReturnStaPrcDoc'])) // On pending approve status 
                && ($aCardShiftReturnHD["raItems"]['rtCardShiftReturnStaDoc'] == "1") // On document complete status
            ) 
            {
                $aStaCardShiftReturnHD = $this->Cardshiftreturn_model->FSaMCardShiftReturnUpdateApvDocAndCancelDocHD($aDataMaster);
                try{
                    $aMQParams = [
                        "queueName" => "CARDRETURN", 
                            "params" => [
                                "ptBchCode" => $aDataMaster['FTBchCode'], 
                                "ptDocNo" => $aDataMaster['FTCshDocNo'],
                                "ptUsrCode" => $aDataMaster['FTUsrCode']
                                ]
                        ];
                    FCNxCallRabbitMQ($aMQParams);
                }catch(\ErrorException $err){
                    $this->db->trans_rollback();
                    $aReturn = array(
                        'nStaEvent'    => '900',
                        'tStaMessg'    => language('document/card/cardreturn', 'tCardShiftReturnApproveFail')
                    );
                    echo json_encode($aReturn);
                    return;
                }
            }
            /*=================================================================*/
            
            /*==========================Return Cancel==========================*/
            if(($aDataMaster['FTCshStaDoc'] == "3")){ // Have card and update status document is cancel
                $aStaCardShiftReturnHD = $this->Cardshiftreturn_model->FSaMCardShiftReturnUpdateApvDocAndCancelDocHD($aDataMaster);
            }
            /*=================================================================*/
            
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $aReturn = array(
                    'nStaEvent'    => '900',
                    'tStaMessg'    => "Unsucess Update Event"
                );
            }else{
                $this->db->trans_commit();
                $aReturn = array(
                    'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                    'tCodeReturn'	=> $aDataMaster['FTCshDocNo'],
                    'nStaEvent'	    => '1',
                    'tStaMessg'		=> 'Success Update Event'
                );
            }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
      
    }
    
    /**
     * Functionality : Event Delete Card Shift
     * Parameters : Ajax and Function Parameter
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : Status Delete Event
     * Return Type : String
     */
    public function FSaCardShiftReturnDeleteEvent(){
        $tIDCode = $this->input->post('tIDCode');
        $aDataMaster = array(
            'FTCardShiftReturnCode' => $tIDCode
        );

        $aResDel = $this->Cardshiftreturn_model->FSnMCardShiftReturnDel($aDataMaster);
        $aReturn = array(
            'nStaEvent' => $aResDel['rtCode'],
            'tStaMessg' => $aResDel['rtDesc']
        );
        echo json_encode($aReturn);
    }

    /**
     * Functionality : Vatrate unique check
     * Parameters : $tSelect "CardShiftReturncode"
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : Check status true or false
     * Return Type : String
     */
    public function FStCardShiftReturnUniqueValidate($tSelect = ''){
        
        if($this->input->is_ajax_request()){ // Request check
            if($tSelect == 'cardShiftReturnCode'){
                
                $tCardShiftReturnCode   = $this->input->post('tCardShiftReturnCode');
                $oCustomerGroup         = $this->Cardshiftreturn_model->FSoMCardShiftReturnCheckDuplicate($tCardShiftReturnCode);
                
                $tStatus = 'false';
                if($oCustomerGroup[0]->counts > 0){ // If have record
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
    
    /**
     * Functionality : Function Event Multi Delete
     * Parameters : Ajax Function Delete Card Shift
     * Creator : 09/10/2018 piya
     * Last Modified : -
     * Return : Status Event Delete And Status Call Back Event
     * Return Type : object
     */
    public function FSoCardShiftReturnDeleteMulti(){
        $tCardShiftReturnCode = $this->input->post('tCardShiftReturnCode');
      
        $aCardShiftReturnCode = json_decode($tCardShiftReturnCode);
        foreach($aCardShiftReturnCode as $oCardShiftReturnCode){
            $aCardShiftReturn = ['FTCardShiftReturnCode' => $oCardShiftReturnCode];
            $this->Cardshiftreturn_model->FSnMCardShiftReturnDel($aCardShiftReturn);
        }
        echo json_encode($aCardShiftReturnCode);
    }
    
    /**
     * Functionality : Delete vat rate
     * Parameters : -
     * Creator : 25/10/2018 piya
     * Last Modified : -
     * Return : Vat code
     * Return Type : Object
     */
    public function FSoCardShiftReturnDelete(){
        $tCardShiftReturnCode = $this->input->post('tCardShiftReturnCode');
        
        $aCardShiftReturn = ['FTCardShiftReturnCode' => $tCardShiftReturnCode];
        $this->Cardshiftreturn_model->FSnMCardShiftReturnDel($aCardShiftReturn);
        echo json_encode($tCardShiftReturnCode);
    }
    
    /**
     * Functionality : Function Call DataTables Card Shift by file (xls or xlsx)
     * Parameters : Ajax and Function Parameter
     * Creator : 05/11/2018 piya
     * Last Modified : -
     * Return : String View
     * Return Type : View
     */
    public function FSvCardShiftReturnDataSourceListByFile(){
        $nPage              = $this->input->post('nPageCurrent');
        $tSearchAll         = $this->input->post('tSearchAll');
        $aCardTypeRange     = json_decode($this->input->post('tCardTypeRange'));
        $aCardNumberRange   = json_decode($this->input->post('tCardNumberRange'));
        $aCardNumber        = json_decode($this->input->post('tCardNumber'));
        $aNotInCardNumber   = json_decode($this->input->post('tNotInCardNumber'));
        $tSetEmpty          = $this->input->post('tSetEmpty');
        $tStaShift          = $this->input->post('tStaShift');
        $tIsTemp            = $this->input->post('tIsTemp');
        $tIsDataOnly        = $this->input->post('tIsDataOnly');
        $tStaPrcDoc         = $this->input->post('tStaPrcDoc');
        $tStaDoc            = $this->input->post('tStaDoc');
        $tStaType           = $this->input->post('tStaType');
        $tLastIndex         = $this->input->post('tLastIndex');
        $tDocNo             = $this->input->post('tDocNo');
        
        if($nPage == '' || $nPage == null){$nPage = 1;}else{$nPage = $this->input->post('nPageCurrent');}
        if(!$tSearchAll){$tSearchAll='';}
        
        // Lang ภาษา
        $nLangResort    = $this->session->userdata("tLangID");
	$nLangEdit      = $this->session->userdata("tLangEdit");
        
        try{
            $aDataFiles = (isset($_FILES['oefCardShiftReturnImport']) && !empty($_FILES['oefCardShiftReturnImport']))? $_FILES['oefCardShiftReturnImport'] : null;
            
            if(isset($aDataFiles) && !empty($aDataFiles) && is_array($aDataFiles)){
                // Insert
                $aDataFiles     = (isset($_FILES['aFile']) && !empty($_FILES['aFile']))? $_FILES['aFile'] : null;
                $ptDocType      = 'cardShiftReturn';
                $ptDataSetType  = 'Excel';
                $paDataExcel    = [
                    'file' => $aDataFiles,
                    'reasonfile' => '', 
                    'optionfile_newcard' => 0,
                    'nDocno' => $tDocNo
                ];
                
                $paDataSet = [];
                if(isset($_FILES['aFile'])){
                    $tResult = FCNaCARInsertDataToTempFileCenter($ptDocType, $ptDataSetType, $paDataExcel, $paDataSet);
                }

                if($tResult['nStaEvent'] == 1){
                    $aDataReturn = array(
                        'tStaLog'   => 'Success',
                        'tDocType'  => $ptDocType
                    );
                    echo json_encode($aDataReturn);
                }else{  
                    $aDataReturn = array(
                        'tStaLog'   => $tResult['tTextError'],
                        'tDocType'  => $ptDocType
                    );
                    echo json_encode($aDataReturn);
                }
            }
        }catch(Exception $Error){
            echo "Controller Err FSvCardShiftReturnDataSourceListByFile = ".$Error;
        }
    }

    /**
     * Functionality : Update Edit inline
     * Parameters : -
     * Creator : 27/12/2018 Supawat
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function FSxCardShiftReturnUpdateInlineOnTemp(){
        $tDocType           = "cardShiftReturn";
        $nSeq               = $this->input->post('nSeq');
        $tCardCode          = $this->input->post('tCardCode');
        $aDataSet = [
            "tCardCode"     => $tCardCode
        ];
        FSxUpdateTempBySeq($tDocType, $nSeq, $aDataSet);
    }

    /**
     * Functionality : Save To Temp พี่เสือเขียนไว้
     * Parameters : -
     * Creator : 27/12/2018 Supawat
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function FSxCardShiftReturnInsertToTemp(){
        $tUsrBchCode    = empty($this->session->userdata('tSesUsrBchCode'))?FCNtGetBchInComp():$this->session->userdata('tSesUsrBchCode');
        $tUserCode      = $this->session->userdata("tSesUsername");
        $aRangeCardCode = json_decode($this->input->post('tRangeCardCode'));
        $aRangeCardType = json_decode($this->input->post('tRangeCardType'));
        $aCardCode      = json_decode($this->input->post('tCardCode'));
        $tInsertType    = $this->input->post('tInsertType');
        $tDocNo         = $this->input->post('tDocNo');
        
        $tDocType       = "cardShiftReturn";
        $tDataSetType   = "";
        if($tInsertType == "between"){
            $tDataSetType = "Between";
            $aDataSet = [
                "tDocNo"    => $tDocNo,
                "tBchCode"  => $tUsrBchCode,
                "tCreateBy" => $tUserCode,
                "aCardCode" => $aRangeCardCode,
                "aCardType" => $aRangeCardType
            ];
        }
        
        if($tInsertType == "choose"){
            $tDataSetType = "ChooseCard";
            $aDataSet = [
                "tDocNo"    => $tDocNo,
                "tBchCode"  => $tUsrBchCode,
                "tCreateBy" => $tUserCode,
                "aCardCode" => $aCardCode
            ];
        }

        FCNaCARDInsertDataToTemp($tDocType, $tDataSetType, [], $aDataSet);
    }
    
}







