<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class cMovement extends MX_Controller {

    public function __construct(){
        parent::__construct ();
        $this->load->model('Movement/Movement/mMovement');
        date_default_timezone_set("Asia/Bangkok");
    }

    public function index($nMovementType,$tMovementOption){
        $vBtnSave           = FCNaHBtnSaveActiveHTML('movement/0/0');
        $aAlwEventMovement	= FCNaHCheckAlwFunc('movement/0/0');
        $this->load->view ( 'Movement/Movement/wMovement', array (
            'vBtnSave'          =>$vBtnSave,
            'nMovementType'     =>$nMovementType,
            'tMovementOption'   =>$tMovementOption,
            'aAlwEventMovement' =>$aAlwEventMovement
        ));
    }

    public function FSxMmtContentTab(){
        $this->load->view('Movement/Movement/wMmtContentTab');
    }

    //Functionality : Function Call Movement Page List
    //Parameters : Ajax and Function Parameter
    //Creator :  10/03/2020 Saharat(Golf)
    //Last Modified : 15/04/2020 surawat
    //Return : String View
    //Return Type : View
    public function FSvCMovementListPage(){
        $aAlwEventMovement	       = FCNaHCheckAlwFunc('movement/0/0');
        $tUsrBchCode     = $this->session->userdata("tSesUsrBchCode");
        $tUsrShpCode     = $this->session->userdata("tSesUsrShpCode");
        $this->load->view('Movement/Movement/wMovementList', array(
            'aAlwEventMovement'    => $aAlwEventMovement,
            'tUsrBchCode'          => $tUsrBchCode,
            'tUsrShpCode'          => $tUsrShpCode
        ));
    }

    //Functionality : Function Call DataTables Movement
    //Parameters : Ajax Call View DataTable
    //Creator : 11/03/2020 Saharat(Golf)
    //Last Modified : 15/04/2020 surawat
    //Return : String View
    //Return Type : View
    public function FSvCMovementDataList(){
        try{

            $nPage          = ($this->input->post('nPageCurrent') == '' || null)? 1 : $this->input->post('nPageCurrent');   // Check Number Page
            $nRow           = 10;
            $nLangResort    = $this->session->userdata("tLangID");
            $nLangEdit      = $this->session->userdata("tLangEdit");
            $tDataSearch    = $this->input->post('tDataFilter');
            $tSearchAll     = json_decode($tDataSearch, true);
            $aData  = array(
                'nPage'         => $nPage,
                'nRow'          => $nRow,
                'FNLngID'       => $nLangEdit,
                'tSearchAll'    => $tSearchAll
            );
            $aMovementDataList               = $this->mMovement->FSaMMovementList($aData);
            $aAlwEventMovement	             = FCNaHCheckAlwFunc('movement/0/0');
            $aGenTable  = array(
                'aDataList'                 => $aMovementDataList,
                'nPage'                     => $nPage,
                'nRow'                         => $nRow,
                'tSearchAll'                => $tSearchAll,
                'aAlwEventMovement'         => $aAlwEventMovement,
                'tSearchAll'                => $tSearchAll
            );
            $this->load->view('Movement/Movement/wMovementDataTable',$aGenTable);
        }catch(Exception $Error){
            echo $Error;
        }
    }





}

