<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Salemachine_controller extends MX_Controller {

    public function __construct(){
        parent::__construct ();
        $this->load->model('company/branch/Branch_model');
        $this->load->model('pos/salemachine/Salemachine_model');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index($nBrowseType,$tPosBrowseOption){
        //เช็คการ Browse
        $aBrowseType = explode("-",$nBrowseType);
		if(isset($aBrowseType[1])){
			$nPosBrowseType = $aBrowseType[0];
			$tRouteFromName = $aBrowseType[1];
		}else{
			$nPosBrowseType = $nBrowseType;
			$tRouteFromName = '';
        }
        $aDataConfigView = array(
            'tRouteFromName'    => $tRouteFromName,
            'nPosBrowseType'    => $nPosBrowseType,
            'tPosBrowseOption'  => $tPosBrowseOption,
            'aAlwEvent'         => FCNaHCheckAlwFunc('salemachine/0/0'), // Controle Event
            'vBtnSave'          => FCNaHBtnSaveActiveHTML('salemachine/0/0'), // Load Html ของปุ่ม Save ที่เก็บ Session ปัจจุบัน
            'nOptDecimalShow'   => FCNxHGetOptionDecimalShow(),
            'nOptDecimalSave'   => FCNxHGetOptionDecimalSave()
        );
        $this->load->view('pos/salemachine/wSaleMachine',$aDataConfigView);
    }

    //Functionality : Function Call SaleMachine Page List
    //Parameters : Ajax and Function Parameter
    //Creator : 30/10/2018 Witsarut
    //Return : String View
    //Return Type : View
    public function FSvCPOSListPage(){ 
        $aAlwEvent          = FCNaHCheckAlwFunc('salemachine/0/0');
		$aNewData  		   = ['aAlwEvent' => $aAlwEvent];
        $this->load->view('pos/salemachine/wSaleMachineList',$aNewData);
    }

    //Functionality : Function Call DataTables SaleMachine
    //Parameters : Ajax Call View DataTable
    //Creator : 30/10/2018 Witsarut
    //Return : String View
    //Return Type : View
    public function FSvCPOSDataList(){
        try{
            $tSearchAll     = $this->input->post('tSearchAll');
            $nPage          = ($this->input->post('nPageCurrent') == '' || null)? 1 : $this->input->post('nPageCurrent');   // Check Number Page
            $aData  = array(
                'nPage'         => $nPage,
                'nRow'          => 10,
                'tSearchAll'    => $tSearchAll,
                'FNLngID'   	=> $this->session->userdata("tLangEdit"),
            );
            $aPosDataList   = $this->Salemachine_model->FSaMPOSList($aData); 
            $aGenTable  = array(
                'aPosDataList'  => $aPosDataList,
                'nPage'         => $nPage,
                'tSearchAll'    => $tSearchAll
            );
            $this->load->view('pos/salemachine/wSaleMachineDataTable',$aGenTable);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Function CallPage SaleMachine Add
    //Parameters : Ajax Call View Add
    //Creator : 30/10/2018 Witsarut
    //update  : 23/09/2019 Saharat(Golf)
    //Return : String View
    //Return Type : View
    public function FSvCPOSAddPage(){
        $aCnfAddPanal = $this->FSvCBCHGenViewAddress();
        $aCnfAddVersion = FCNaHAddressFormat('TCNMPos');
        $nType = $this->input->post('nRoutetype');
        //เช็ค type กรณี เรียกใช้งานแบบเบาร์จากหน้าร้านค้า>เครื่องจุดขาย
        if(isset($nType) && !empty($nType)){
           switch ($nType) { 
            case  "1": 
                $tPosOptionType = '<option value="1" >'. language('pos/salemachine/salemachine','tPOSSalePoint') .'</option>';               
            break;
            // case  "2": 
            //     $tPosOptionType = '<option value="2" >'. language('pos/salemachine/salemachine','tPOSPrePaid') .'</option>';
            // break;
            // case  "3": 
            //     $tPosOptionType = '<option value="3" >'. language('pos/salemachine/salemachine','tPOSCheckPoint') .'</option>';
            // break;
            case  "4": 
                $tPosOptionType = '<option value="4" >'. language('pos/salemachine/salemachine','tPOSVending') .'</option>';
            break;
            // case  "5": 
            //     $tPosOptionType = '<option value="5" >'. language('pos/salemachine/salemachine','tPOSSmartLoc') .'</option>';
            // break;
            // case  "6": 
            //     $tPosOptionType = '<option value="6" >'. language('pos/salemachine/salemachine','tPOsVansale') .'</option>';
            // break;
            default: 
                $tPosOptionType = "";
            }
        }else{
                $tPosOptionType = '
                    <option value="1" >'. language('pos/salemachine/salemachine','tPOSSalePoint') .'</option>
                    <option value="4" >'. language('pos/salemachine/salemachine','tPOSVending') .'</option>
                    ';
                // <option value="5" >'. language('pos/salemachine/salemachine','tPOSSmartLoc') .'</option>
                // <option value="3" >'. language('pos/salemachine/salemachine','tPOSCheckPoint') .'</option>		
                // <option value="2" >'. language('pos/salemachine/salemachine','tPOSPrePaid') .'</option>		
                // <option value="6" >'. language('pos/salemachine/salemachine','tPOsVansale') .'</option>
        }
        try{
            //ตรวจสอบ Level ของ User
            $tSesUsrLevel =	$this->session->userdata("tSesUsrLevel");
            if($tSesUsrLevel == "HQ"){
                $tStaUsrLevel = "HQ";
                $tUsrBchCode = "";  
                $tUsrBchName = "";
            }else{
                $tStaUsrLevel = $this->session->userdata("tSesUsrLevel"); 
                $tUsrBchCode = $this->session->userdata("tSesUsrBchCode"); 
                $tUsrBchName = $this->session->userdata("tSesUsrBchName"); 
            }

            $aDataSaleMachine = array(
                'nStaAddOrEdit' => 99,
                'aCnfAddPanal' => $aCnfAddPanal,
                'nCnfAddVersion' => $aCnfAddVersion,
                'tPosOptionType' => $tPosOptionType,
                'tStaUsrLevel' => $tStaUsrLevel,
                'tUsrBchCode' => $tUsrBchCode,
                'tUsrBchName' => $tUsrBchName
            );
            $this->load->view('pos/salemachine/wSaleMachineAdd',$aDataSaleMachine);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Function CallPage SaleMachine Edits
    //Parameters : Ajax Call View Add
    //Creator : 30/10/2018 Witsarut
    //Return : String View
    //Return Type : View
    public function FSvCPOSEditPage(){
        try{
            $tBchCode  = $this->input->post('tBchCode');
            $tPosCode  = $this->input->post('tPosCode');
            $tPosType  = $this->input->post('tPosType');
      
            switch ($tPosType) {
                case "4":
                        // ประเภท ตู้ขายสินค้า
                        $aCodeBchShp = $this->Salemachine_model->FSaMSMGGetVDPosShopDataList($tPosCode);
                    break;
                case "5":
                         // ประเภท ตู้ฝากของ
                        $aCodeBchShp = $this->Salemachine_model->FSaMSMGGetSMPosShopDataList($tPosCode);
                    break;
                default:
                        // ไม่ใช่ ประเภท 4,5 ให้เป็นค่าว่าง
                        $aCodeBchShp = array('rtCode' => '99');
            }
            $tPosOptionType = '
                <option value="1" >'. language('pos/salemachine/salemachine','tPOSSalePoint') .'</option>
                <option value="4" >'. language('pos/salemachine/salemachine','tPOSVending') .'</option>
                ';
            // <option value="3" >'. language('pos/salemachine/salemachine','tPOSCheckPoint') .'</option>		
            // <option value="2" >'. language('pos/salemachine/salemachine','tPOSPrePaid') .'</option>		
            // <option value="5" >'. language('pos/salemachine/salemachine','tPOSSmartLoc') .'</option>
            // <option value="6" >'. language('pos/salemachine/salemachine','tPOsVansale') .'</option>
            $aData  = array(
                'FTPosCode' => $tPosCode,
                'FTBchCode' => $tBchCode
            );
                                                
            $aPosData       = $this->Salemachine_model->FSaMPOSGetDataByID($aData);

            // Create By Witsarut 09/09/2019
            // Get Datalist SlipMessage
            $aSlipMessage   = $this->Salemachine_model->FSaMSMGGetDataList($tPosCode);
            // ==========================================================
            //Start : Address
            $aCnfAddVersion     = FCNaHAddressFormat('TCNMPos');
            $aCnfAddPanal       = $this->FSvCBCHGenViewAddress($aPosData,$aCnfAddVersion);
            //ตรวจสอบ Level ของ User
            
            $tSesUsrLevel =	$this->session->userdata("tSesUsrLevel");
            if($tSesUsrLevel == "HQ"){
                $tStaUsrLevel = "HQ";  
                $tUsrBchCode  = "";
                $tUsrBchName  = "";
            }else{
                $tStaUsrLevel = $this->session->userdata("tSesUsrLevel"); 
                $tUsrBchCode  = $this->session->userdata("tSesUsrBchCode"); 
                $tUsrBchName  = $this->session->userdata("tSesUsrBchName"); 
            }

            //End : Address
            $aDataSaleMachine   = array(
                'nStaAddOrEdit'     => 1,
                'aCnfAddPanal' 		=> $aCnfAddPanal,
                'nCnfAddVersion' 	=> $aCnfAddVersion,
                'aPosData'          => $aPosData,
                'aSlipMessage'      => $aSlipMessage,
                'aCodeBchShp'       => $aCodeBchShp,
                'tPosCodeEvent'     => $tPosCode,
                'tPosOptionType'    => $tPosOptionType,
                'tStaUsrLevel'      => $tStaUsrLevel,
                'tUsrBchCode'       => $tUsrBchCode,
                'tUsrBchName'       => $tUsrBchName,
                // 'trtBchCode'     => $trtBchCode
            );

        $this->load->view('pos/salemachine/wSaleMachineAdd',$aDataSaleMachine);

        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Detail Address
    public function FSvCBCHGenViewAddress($paResList = '',$nCnfAddVersion = ''){
		$nLangResort = $this->session->userdata("tLangID");
		$nLangEdit	 = $this->session->userdata("tLangEdit");
		if(isset($paResList['raItems']['rtPosCode'])){
			$tPosCode = $paResList['raItems']['rtPosCode'];
			$aData = array(
				'FNLngID' 			=> $nLangEdit,
				'FTAddGrpType' 		=> '6',
				'FTAddVersion' 		=> $nCnfAddVersion,
				'FTAddRefCode' 		=> $tPosCode,
			);
			$aCnfAddEdit    = $this->Branch_model->FSvMBCHGetAddress($aData);
		}else{
			$tPosCode       = '';
			$aCnfAddEdit    = '';
		}
		return $aCnfAddEdit;
	}

    //Functionality : Event Add SaleMachine
    //Parameters : Ajax Event
    //Creator : 30/10/2018 Witsarut
    //Update : 28/03/2019 Pap
    //Return : Status Add Event
    //Return Type : String
    public function FSoCPOSAddEvent(){ 
        try{
            $tPosType = $this->input->post("ocmPosType");
            //เช็ค type PosType
            if($tPosType == 4 || $tPosType == 5 ){
                    $tStaShift = 2;
                }else{
                    $tStaShift = 1;
            }
            $nAddVersion = $this->input->post("ohdAddVersion");
            if($nAddVersion==1){
                $aDataPos   = array(
                    'tIsAutoGenCode'        => $this->input->post("ocbPosAutoGenCode"),
                    'oetPosCode'            => $this->input->post("oetPosCode"),
                    'FTBchCode'             => $this->input->post("oetPosBchCode"),
                    'FTPosStaShift'         => $tStaShift,
                    'ocmPosType'            => $this->input->post("ocmPosType"),
                    'oetBchWahCode'         => $this->input->post("oetBchWahCode"),
                    'oetPosRegNo'           => $this->input->post("oetPosRegNo"),
                    'oetSmgCode'            => $this->input->post("oetSmgCode"),  // Get data SlipMessage By Witsarut
                    'ocbPOSStaPrnEJ'        => $this->input->post("ocbPOSStaPrnEJ"),
                    'ocbPosStaVatSend'      => 1,
                    'ocbPosStaUse'          => $this->input->post("ocbPosStaUse"),
                    'oetPosName'            => $this->input->post("oetPosName"),

                    'ocbPOSStaSumProductBySacn'     => $this->input->post("ocbPOSStaSumProductBySacn"), 
                    'ocbPOSStaSumProductByPrint'    => $this->input->post("ocbPOSStaSumProductByPrint"),


                    'FTChnCode'             => $this->input->post("oetPosChanelCode")
                );
            }else{
                $aDataPos   = array(
                    'tIsAutoGenCode'    => $this->input->post("ocbPosAutoGenCode"),
                    'oetPosCode'        => $this->input->post("oetPosCode"),
                    'FTBchCode'         => $this->input->post("oetPosBchCode"),
                    'FTPosStaShift'     => $tStaShift,
                    'ocmPosType'        => $this->input->post("ocmPosType"),
                    'oetBchWahCode'     => $this->input->post("oetBchWahCode"),
                    'oetPosRegNo'       => $this->input->post("oetPosRegNo"),
                    'oetSmgCode'        => $this->input->post("oetSmgCode"),     // Get data SlipMessage By Witsarut
                    'ocbPOSStaPrnEJ'    => $this->input->post("ocbPOSStaPrnEJ"),
                    'ocbPosStaVatSend'  => 1,
                    'ocbPosStaUse'      => $this->input->post("ocbPosStaUse"),
                    'oetPosName'        => $this->input->post("oetPosName"),

                    'ocbPOSStaSumProductBySacn'     => $this->input->post("ocbPOSStaSumProductBySacn"), 
                    'ocbPOSStaSumProductByPrint'    => $this->input->post("ocbPOSStaSumProductByPrint"), 

                    'FTChnCode'             => $this->input->post("oetPosChanelCode")
                );
            }

            if($aDataPos['tIsAutoGenCode'] == '1'){ 
                // Auto Gen Reason Code
                // $aGenCode = FCNaHGenCodeV5('TCNMPos','0',$this->input->post("oetPosBchCode"));
                // if($aGenCode['rtCode'] == '1'){
                //     $aDataPos['oetPosCode'] = $aGenCode['rtPosCode'];
                // }
                //ถ้า CodeDup ให้ GenCode ใหมไ่ปเรื่อยๆ จนกว่าจะไม่ซ้ำ ในตอนสร้าง
                // $nPosCup = $this->Salemachine_model->FSnMPOSCheckDuplicate($aDataPos['oetPosCode'],$this->input->post("oetPosBchCode"));

                // while($nPosCup>0) {
                //     $aGenCode = FCNaHGenCodeV5('TCNMPos','0',$this->input->post("oetPosBchCode"));
                //     if($aGenCode['rtCode'] == '1'){
                //         $aDataPos['oetPosCode'] = $aGenCode['rtPosCode'];
                //     }
                //     $nPosCup = $this->Salemachine_model->FSnMPOSCheckDuplicate($aDataPos['oetPosCode'],$this->input->post("oetPosBchCode"));
                // }

                // Update new gencode
                // 15/05/2020 Napat(Jame)
                $aStoreParam = array(
                    "tTblName"    => 'TCNMPos',                           
                    "tDocType"    => 0,                                          
                    "tBchCode"    => $this->input->post("oetPosBchCode"),                                 
                    "tShpCode"    => "",                               
                    "tPosCode"    => "",                     
                    "dDocDate"    => date("Y-m-d")       
                );
                $aAutogen                    = FCNaHAUTGenDocNo($aStoreParam);
                $aDataPos['oetPosCode']      = $aAutogen[0]["FTXxhDocNo"];

            }


         $nPosCup = $this->Salemachine_model->FSnMPOSCheckDuplicate($aDataPos['oetPosCode'],$this->input->post("oetPosBchCode"));
            if($nPosCup==0){

                    $this->db->trans_begin();
                    $this->Salemachine_model->FSxMPOSInsertPos($aDataPos);
                    $this->Salemachine_model->FSxMPOSInsertPosWaHouse($aDataPos);
                    if($this->db->trans_status() === false){
                        // not success
                        $this->db->trans_rollback();
                        $aReturn = array(
                            'nStaEvent'    => '900',
                            'tStaMessg'    => "Unsucess Add SaleMachine Group"
                        );
                    }else{
                        // success
                        $this->db->trans_commit();
                        $aReturn = array(
                            'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                            'tCodeReturn'	=> $aDataPos['oetPosCode'],
                            'tBchCode'      => $aDataPos['FTBchCode'],
                            'tPosType'      => $aDataPos['ocmPosType'],
                            'nStaEvent'	    => '1',
                            'tStaMessg'		=> 'Success Add SaleMachine Group'
                        );
                    }
        }else{
                    $aReturn = array(
                        'nStaEvent'    => '900',
                        'tStaMessg'    => "Unsucess Add Data Duplicate"
                    );
        }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Event Edit SaleMachine
    //Parameters : Ajax Event
    //Creator : 30/10/2018 Witsarut
    //Update : 28/03/2019 pap
    //Return : Status Edit Event
    //Return Type : String
    public function FSoCPOSEditEvent(){ 
        try{
            $tPosType = $this->input->post("ocmPosType");
            //เช็ค type PosType
            if($tPosType == 4 || $tPosType == 5 ){
                    $tStaShift = 2;
                }else{
                    $tStaShift = 1;
            }
            $nAddVersion = $this->input->post("ohdAddVersion");
            if($nAddVersion==1){
                $aDataPos   = array(
                    'tIsAutoGenCode'        => $this->input->post("ocbPosAutoGenCode"),
                    'oetPosCode'            => $this->input->post("oetPosCode"),
                    'FTBchCode'             => $this->input->post("oetPosBchCode"),
                    'FTPosStaShift'         => $tStaShift,
                    'ocmPosType'            => $this->input->post("ocmPosType"),
                    'oetBchWahCode'         => $this->input->post("oetBchWahCode"),
                    'oetBchWahCodeOld'      => $this->input->post("oetBchWahCodeOld"),
                    'oetPosRegNo'           => $this->input->post("oetPosRegNo"),
                    'oetSmgCode'            => $this->input->post("oetSmgCode"),  // Get data SlipMessage By Witsarut
                    'ocbPOSStaPrnEJ'        => $this->input->post("ocbPOSStaPrnEJ"),
                    'ocbPosStaVatSend'      => 1,
                    'ocbPosStaUse'          => $this->input->post("ocbPosStaUse"),

                    'ocbPOSStaSumProductBySacn'     => $this->input->post("ocbPOSStaSumProductBySacn"), 
                    'ocbPOSStaSumProductByPrint'    => $this->input->post("ocbPOSStaSumProductByPrint"), 

                    'FTChnCode'             => $this->input->post("oetPosChanelCode")

                );
            }else{
                $aDataPos   = array(
                    'tIsAutoGenCode'    => $this->input->post("ocbPosAutoGenCode"),
                    'oetPosCode'        => $this->input->post("oetPosCode"),
                    'FTBchCode'         => $this->input->post("oetPosBchCode"),
                    'FTBchOldCode'      => $this->input->post("ohdBchCode"),
                    'FTPosStaShift'     => $tStaShift,
                    'ocmPosType'        => $this->input->post("ocmPosType"),
                    'oetBchWahCode'     => $this->input->post("oetBchWahCode"),
                    'oetBchWahCodeOld'  => $this->input->post("oetBchWahCodeOld"),
                    'oetPosRegNo'       => $this->input->post("oetPosRegNo"),
                    'oetSmgCode'        => $this->input->post("oetSmgCode"),  // Get data SlipMessage By Witsarut
                    'ocbPOSStaPrnEJ'    => $this->input->post("ocbPOSStaPrnEJ"),
                    'ocbPosStaVatSend'  => 1,
                    'ocbPosStaUse'      => $this->input->post("ocbPosStaUse"),
                    'FTPosName'        => $this->input->post("oetPosName"),

                    'ocbPOSStaSumProductBySacn'     => $this->input->post("ocbPOSStaSumProductBySacn"), 
                    'ocbPOSStaSumProductByPrint'    => $this->input->post("ocbPOSStaSumProductByPrint"), 

                    'FTChnCode'             => $this->input->post("oetPosChanelCode")
                );
            }

            $nPosCup = $this->Salemachine_model->FSnMPOSCheckDuplicate($aDataPos['oetPosCode'],$this->input->post("oetPosBchCode"));
                if($this->input->post("oetPosBchCode")==$this->input->post("oetPosBchCode")){
                    $nPosCup = 0;
                }

            if($nPosCup==0){


            // $this->db->trans_begin();
            $this->Salemachine_model->FSaMPOSAddUpdateLang($aDataPos);
            $this->Salemachine_model->FSxMPOSUpdatePos($aDataPos);
            $this->Salemachine_model->FSxMPOSUpdatePosWaHouse($aDataPos);
            if($this->db->trans_status() === false){
                // not success
                $this->db->trans_rollback();
                $aReturn = array(
                                'nStaEvent'    => '900',
                                'tStaMessg'    => "Unsucess Add SaleMachine Group"
                            );
            }else{
                // success
                $this->db->trans_commit();
                $aReturn = array(
                                'nStaCallBack'	=> $this->session->userdata('tBtnSaveStaActive'),
                                'tCodeReturn'	=> $aDataPos['oetPosCode'],
                                'tBchCode'      => $aDataPos['FTBchCode'],
                                'tPosType'      => $aDataPos['ocmPosType'],
                                'nStaEvent'	    => '1',
                                'tStaMessg'		=> 'Success Add SaleMachine Group'
                            );
            }
        }else{
            $aReturn = array(
                'nStaEvent'    => '900',
                'tStaMessg'    => "Unsucess Add Data Duplicate"
            );
        }
            echo json_encode($aReturn);
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Event Delete SaleMachine
    //Parameters : Ajax jReason()
    //Creator : 30/10/2018 Witsarut
    //Update : 03/04/2019 pap
    //Return : Status Delete Event
    //Return Type : String
    public function FSoCPOSDeleteEvent(){
        $tIDCode = $this->input->post('tIDCode');
        $aDataMaster = array(
            'FTPosCode' => $tIDCode,
            'FTBchCode' => $this->input->post('tBchCode'),
        );
        $aResDel    = $this->Salemachine_model->FSaMPOSDelAll($aDataMaster);
        $nNumRowPos = $this->Salemachine_model->FSnMLOCGetAllNumRow();
        if($nNumRowPos!==false){
            $aReturn    = array(
                'nStaEvent' => $aResDel['rtCode'],
                'tStaMessg' => $aResDel['rtDesc'],
                'nNumRowPos' => $nNumRowPos
            );
            echo json_encode($aReturn);
        }else{
            echo "database error!";
        }
    }

    /*===== Begin Import By Excel ======================================================*/
    /**
     * Functionality : Get Import Data in Temp
     * Parameters : Ajax
     * Creator : 30/10/2018 Piya
     * Last Modified : -
     * Return : html
     * Return Type : string
     */
    public function FStCImportGetDataInTmp(){
        $tTextSearch = $this->input->post('tSearch');
        $this->load->view('pos/salemachine/wSaleMachineImportDataTable', ['tSearch' => $tTextSearch]);
    }

    /**
     * Functionality : Get Import Data in Temp (JSON)
     * Parameters : Ajax
     * Creator : 30/10/2018 Piya
     * Last Modified : -
     * Return : Import Data
     * Return Type : Json Object
     */
    public function FSoCImportGetDataJsonInTmp(){
        $aGetImportDataInTmpParams = array(
			'nPageNumber' => ($this->input->post('nPageNumber') == 0) ? 1 : $this->input->post('nPageNumber'),
			'nLangEdit'	=> $this->session->userdata("tLangEdit"),
			'tTableKey'	=> 'TCNMPos',
			'tUserSessionID' => $this->session->userdata("tSesSessionID"),
			'tTextSearch' => $this->input->post('tSearch') 
		);
		$aDataInTemp = $this->Salemachine_model->FSaMImportGetDataInTmp($aGetImportDataInTmpParams);
        
        $aData['draw'] = ($this->input->post('nPageNumber') == 0) ? 1 : $this->input->post('nPageNumber');
        $aData['recordsTotal'] = $aDataInTemp['nNumrow'];
        $aData['recordsFiltered'] = $aDataInTemp['nNumrow'];
        $aData['data'] = $aDataInTemp;
        $aData['error'] = array();
        $aData['tTextSearch'] = $aGetImportDataInTmpParams['tTextSearch'];
        
        $this->output->set_content_type('application/json')->set_output(json_encode($aData));
    }

    /**
     * Functionality : Delete in Temp by SeqNo
     * Parameters : Ajax
     * Creator : 30/10/2018 Piya
     * Last Modified : -
     * Return : Status
     * Return Type : Json Object
     */
	public function FSoCImportDeleteInTempBySeqNo(){
        $aImportDataItem = json_decode($this->input->post('tPdtDataItem'), JSON_FORCE_OBJECT);
        $tUserSessionID = $this->session->userdata('tSesSessionID');

        $this->db->trans_begin();
        $aImportDeleteInTempBySeqParams = array(
            'tUserSessionID' => $tUserSessionID,
            'tTableKey' => 'TCNMPos',
            'aSeqNo' => $aImportDataItem['aSeqNo']
        );
        $aResDel = $this->Salemachine_model->FSaMImportDeleteInTempBySeq($aImportDeleteInTempBySeqParams);

        foreach($aImportDataItem['aItems'] as $aItem){
            // ตรวจสอบกรอกข้อมูลซ้ำ Temp
            if($aItem['tSta'] == "5"){ 
                $aParams = [
                    'tUserSessionID' => $tUserSessionID, 
                    'aFieldName' => [['FTBchCode', $aItem['tBchCode']], ['FTPosCode', $aItem['tPosCode']]]
                ];
                FCNnMasTmpChkInlineCodeMultiDupInTemp($aParams);
            }
        }

        if($this->db->trans_status() === false){
            // not success
            $this->db->trans_rollback();
            $aReturn = array(
                'nStaEvent' => '900',
                'tStaMessg' => "Unsucess Add SaleMachine Group"
            );
        }else{
            // success
            $this->db->trans_commit();
            $aReturn = array(
                'nStaEvent' => $aResDel['tCode'],
                'tStaMessg' => $aResDel['tDesc']
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
    }

    /**
     * Functionality : Temp to Master
     * Parameters : Ajax
     * Creator : 30/10/2018 Piya
     * Last Modified : -
     * Return : Status
     * Return Type : Json Object
     */
	public function FSoCImportTempToMaster(){
        $aDataMaster = array(
            'nLangEdit'	=> $this->session->userdata("tLangEdit"),
            'tTableKey'	=> 'TCNMPos',
            'tUserSessionID' => $this->session->userdata("tSesSessionID"),
            'tCreatedOn' => date('Y-m-d H:i:s'),
            'tCreatedBy' => $this->session->userdata("tSesUsername"),
            'tTypeCaseDuplicate' => $this->input->post('tTypeCaseDuplicate')
        );

        /* echo $this->input->post('tTypeCaseDuplicate');
        return; */
        $this->db->trans_begin();

        $this->Salemachine_model->FSxMImportTempToMaster($aDataMaster);
        $this->Salemachine_model->FSxMBCHImportTempToMasterWithReplaceOrInsert($aDataMaster);
        $this->Salemachine_model->FSxMBCHImportDeleteAllInTemp($aDataMaster);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $aReturn = array(
                'tCode' => '99',
                'tDesc' => 'Insert Temp to Master Fail'
            );
        } else {
            $this->db->trans_commit();
            $aReturn = array(
                'tCode' => '1',
                'tDesc' => 'Insert Temp to Master Success'
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
    }

    /**
     * Functionality : Clear Temp
     * Parameters : Ajax
     * Creator : 30/10/2018 Piya
     * Last Modified : -
     * Return : Status
     * Return Type : Json Object
     */
	public function FSoCImportClearInTemp(){
        $aDataMaster = array(
            'tTableKey'	=> 'TCNMPos',
            'tUserSessionID' => $this->session->userdata("tSesSessionID"),
        );

        $this->db->trans_begin();
        $this->Salemachine_model->FSxMBCHImportDeleteAllInTemp($aDataMaster);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $aReturn = array(
                'tCode' => '99',
                'tDesc' => 'Clear Temp Fail'
            );
        } else {
            $this->db->trans_commit();
            $aReturn = array(
                'tCode' => '1',
                'tDesc' => 'Clear Temp Success'
            );
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($aReturn));
    }

    /**
     * Functionality : Get Status in Temp
     * Parameters : Ajax
     * Creator : 30/10/2018 Piya
     * Last Modified : -
     * Return : Status
     * Return Type : Json Object
     */
    public function FSoCSALImportGetStaInTemp(){
        $aData = [];

        $aGetStaInTempParams = array(
            'tTableKey'	=> 'TCNMPos',
            'tUserSessionID' => $this->session->userdata("tSesSessionID"),
        );
        $aGetStaInTemp = $this->Salemachine_model->FSaMSALGetStaInTemp($aGetStaInTempParams);
        
        $aData['nRecordTotal'] = isset($aGetStaInTemp[0]['nRecordTotal'])?$aGetStaInTemp[0]['nRecordTotal']:0;
        $aData['nStaSuccess'] = isset($aGetStaInTemp[0]['nStaSuccess'])?$aGetStaInTemp[0]['nStaSuccess']:0;
        $aData['nStaNewOrUpdate'] = isset($aGetStaInTemp[0]['nStaNewOrUpdate'])?$aGetStaInTemp[0]['nStaNewOrUpdate']:0;
        
        $this->output->set_content_type('application/json')->set_output(json_encode($aData));
    }

    /*===== End Import By Excel ========================================================*/

}