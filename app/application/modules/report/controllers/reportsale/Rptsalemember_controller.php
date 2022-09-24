<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");
include APPPATH . 'libraries/spout-3.1.0/src/Spout/Autoloader/autoload.php';
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
date_default_timezone_set("Asia/Bangkok");
class Rptsalemember_controller extends MX_Controller{
        /**
     * ภาษา
     * @var array
     */
    public $aText = [];

    /**
     * จำนวนต่อหน้าในรายงาน
     * @var int
     */
    public $nPerPage = 100;

    /**
     * Page number
     * @var int
     */
    public $nPage = 1;

    /**
     * จำนวนทศนิยม
     * @var int
     */
    public $nOptDecimalShow = 2;

    /**
     * จำนวนข้อมูลใน Temp
     * @var int
     */
    public $nRows = 0;

    /**
     * Computer Name
     * @var string
     */
    public $tCompName;

    /**
     * User Login on Bch
     * @var string
     */
    public $tBchCodeLogin;

    /**
     * Report Code
     * @var string
     */
    public $tRptCode;

    /**
     * Report Group
     * @var string
     */
    public $tRptGroup;

    /**
     * System Language
     * @var int
     */
    public $nLngID;

    /**
     * User Session ID
     * @var string
     */
    public $tUserSessionID;

    /**
     * Report route
     * @var string
     */
    public $tRptRoute;

    /**
     * Report Export Type
     * @var string
     */
    public $tRptExportType;

    /**
     * Filter for Report
     * @var array
     */
    public $aRptFilter = [];

    /**
     * Company Info
     * @var array
     */
    public $aCompanyInfo = [];

    /**
     * User Login Session
     * @var string
     */
    public $tUserLoginCode;

    /**
     * User Login Session
     * @var string
     */
    public $tSysBchCode;


    public function __construct() {
        $this->load->helper('report');
        $this->load->model('company/company/Company_model');
        $this->load->model('report/report/Report_model');
        $this->load->model('report/reportsale/Rptsalemember_model');

        // Init Report
        $this->init();

        parent::__construct();
    }

    private function init() {
        $this->aText = [
            'tTitleReport'         => language('report/report/report', 'tRptSalememBerTitle'),
            'tDatePrint'           => language('report/report/report', 'tRptSalememberDatePrint'),
            'tTimePrint'           => language('report/report/report', 'tRptSalememberTimePrint'),

            // Address Lang
            'tRptAddrBuilding'      => language('report/report/report', 'tRptAddrBuilding'),
            'tRptAddrRoad'          => language('report/report/report', 'tRptAddrRoad'),
            'tRptAddrSoi'           => language('report/report/report', 'tRptAddrSoi'),
            'tRptAddrSubDistrict'   => language('report/report/report', 'tRptAddrSubDistrict'),
            'tRptAddrDistrict'      => language('report/report/report', 'tRptAddrDistrict'),
            'tRptAddrProvince'      => language('report/report/report', 'tRptAddrProvince'),
            'tRptAddrTel'           => language('report/report/report', 'tRptAddrTel'),
            'tRptAddrFax'           => language('report/report/report', 'tRptAddrFax'),
            'tRptAddrBranch'        => language('report/report/report', 'tRptAddrBranch'),
            'tRptAddV2Desc1'        => language('report/report/report', 'tRptAddV2Desc1'),
            'tRptAddV2Desc2'        => language('report/report/report', 'tRptAddV2Desc2'),

            'tRptTaxSalePosTel'     => language('report/report/report', 'tRptTaxSalePosTel'),
            'tRptTaxSalePosFax'     => language('report/report/report', 'tRptTaxSalePosFax'),
            'tRptTaxSalePosBch'     => language('report/report/report', 'tRptTaxSalePosBch'),
            'tRptTaxSalePosTaxId'   => language('report/report/report', 'tRptTaxSalePosTaxId'),

            'tRptTaxSaleMemberDocDateFrom' => language('report/report/report', 'tRptTaxSalePosFilterDocDateFrom'),
            'tRptTaxSaleMemberDocDateTo'   => language('report/report/report', 'tRptTaxSalePosFilterDocDateTo'),
            'tRptTaxSaleMemberBchFrom'     => language('report/report/report', 'tRptTaxSalePosFilterBchFrom'),
            'tRptTaxSaleMemberBchTo'       => language('report/report/report', 'tRptTaxSalePosFilterBchTo'),

            //header
            'tRptCstCode'               => language('report/report/report', 'tRptCstCode'),
            'tRptCstName'               => language('report/report/report', 'tRptCstName'),
            'tRptSale'                  => language('report/report/report', 'tRptSale'),
            'tRptDisCount'              => language('report/report/report', 'tRptDisCount'),
            'tRptSaleTotle'             => language('report/report/report', 'tRptSaleTotle'),

            'tRptRentAmtFolCourSumText' => language('report/report/report', 'tRptRentAmtFolCourSumText'),
            'tRptConditionInReport'     => language('report/report/report', 'tRptConditionInReport'),
            // No Data Report
            'tRptTaxSalePosNoData'      => language('common/main/main', 'tCMNNotFoundData'),

            'tRptBchFrom'               => language('report/report/report', 'tRptBchFrom'),
            'tRptBchTo'                 => language('report/report/report', 'tRptBchTo'),
            'tRptMerFrom'               => language('report/report/report', 'tRptMerFrom'),
            'tRptMerTo'                 => language('report/report/report', 'tRptMerTo'),
            'tRptShopFrom'              => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo'                => language('report/report/report', 'tRptShopTo'),
            'tRptPosFrom'               => language('report/report/report', 'tRptPosFrom'),
            'tRptPosTo'                 => language('report/report/report', 'tRptPosTo'),

            'tRowNumber'                 => language('report/report/report', 'tRowNumber'),

            'tRptPosType'               => language('report/report/report', 'tRptPosType'),
            'tRptPosType1'              => language('report/report/report', 'tRptPosType1'),
            'tRptPosType2'              => language('report/report/report', 'tRptPosType2'),
            'tRptPosTypeName'           => language('report/report/report', 'tRptPosTypeName'),
            'tRptAll'                   => language('report/report/report', 'tRptAll'),
            'tRptCstFrom'               => language('report/report/report', 'tRptCstFrom'),
            'tRptCstTo'                 => language('report/report/report', 'tRptCstTo'),

        ];

        $this->tSysBchCode          = SYS_BCH_CODE;
        $this->tBchCodeLogin        = (!empty($this->session->userdata('tSesUsrBchCom')) ? $this->session->userdata('tSesUsrBchCom') : $this->session->userdata('tSesUsrBchCom'));
        $this->nPerPage             = 100;
        $this->nOptDecimalShow      = FCNxHGetOptionDecimalShow();

        $tIP                        = $this->input->ip_address();
        $tFullHost                  = gethostbyaddr($tIP);
        $this->tCompName            = $tFullHost;

        $this->nLngID               = FCNaHGetLangEdit();
        $this->tRptCode             = $this->input->post('ohdRptCode');
        $this->tRptGroup            = $this->input->post('ohdRptGrpCode');
        $this->tUserSessionID       = $this->session->userdata('tSesSessionID');
        $this->tRptRoute            = $this->input->post('ohdRptRoute');
        $this->tRptExportType       = $this->input->post('ohdRptTypeExport');
        $this->nPage                = empty($this->input->post('ohdRptCurrentPage')) ? 1 : $this->input->post('ohdRptCurrentPage');
        $this->tUserLoginCode       = $this->session->userdata('tSesUsername');

        // Report Filter
        $this->aRptFilter = [
            'tUserSession'      => $this->tUserSessionID,
            'tCompName'         => $tFullHost,
            'tRptCode'          => $this->tRptCode,
            'nLangID'           => $this->nLngID,

            'tTypeSelect'       => !empty($this->input->post('ohdTypeDataCondition')) ? $this->input->post('ohdTypeDataCondition') : "",

            // สาขา(Branch)
            'tBchCodeFrom'      => !empty($this->input->post('oetRptBchCodeFrom')) ? $this->input->post('oetRptBchCodeFrom') : "",
            'tBchNameFrom'      => !empty($this->input->post('oetRptBchNameFrom')) ? $this->input->post('oetRptBchNameFrom') : "",
            'tBchCodeTo'        => !empty($this->input->post('oetRptBchCodeTo')) ? $this->input->post('oetRptBchCodeTo') : "",
            'tBchNameTo'        => !empty($this->input->post('oetRptBchNameTo')) ? $this->input->post('oetRptBchNameTo') : "",
            'tBchCodeSelect'    => !empty($this->input->post('oetRptBchCodeSelect')) ? $this->input->post('oetRptBchCodeSelect') : "",
            'tBchNameSelect'    => !empty($this->input->post('oetRptBchNameSelect')) ? $this->input->post('oetRptBchNameSelect') : "",
            'bBchStaSelectAll'  => !empty($this->input->post('oetRptBchStaSelectAll')) && ($this->input->post('oetRptBchStaSelectAll') == 1) ? true : false,

            // กลุ่มธุรกิจ
            'tRptMerCodeFrom'   => !empty($this->input->post('oetRptMerCodeFrom')) ? $this->input->post('oetRptMerCodeFrom') : "",
            'tRptMerNameFrom'   => !empty($this->input->post('oetRptMerNameFrom')) ? $this->input->post('oetRptMerNameFrom') : "",
            'tRptMerCodeTo'     => !empty($this->input->post('oetRptMerCodeTo')) ? $this->input->post('oetRptMerCodeTo') : "",
            'tRptMerNameTo'     => !empty($this->input->post('oetRptMerNameTo')) ? $this->input->post('oetRptMerNameTo') : "",
            'tMerCodeSelect'    => !empty($this->input->post('oetRptMerCodeSelect')) ? $this->input->post('oetRptMerCodeSelect') : "",
            'tMerNameSelect'    => !empty($this->input->post('oetRptMerNameSelect')) ? $this->input->post('oetRptMerNameSelect') : "",
            'bMerStaSelectAll'  => !empty($this->input->post('oetRptMerStaSelectAll')) && ($this->input->post('oetRptMerStaSelectAll') == 1) ? true : false,

            // ร้านค้า
            'tRptShpCodeFrom'   => !empty($this->input->post('oetRptShpCodeFrom')) ? $this->input->post('oetRptShpCodeFrom') : "",
            'tRptShpNameFrom'   => !empty($this->input->post('oetRptShpNameFrom')) ? $this->input->post('oetRptShpNameFrom') : "",
            'tRptShpCodeTo'     => !empty($this->input->post('oetRptShpCodeTo')) ? $this->input->post('oetRptShpCodeTo') : "",
            'tRptShpNameTo'     => !empty($this->input->post('oetRptShpNameTo')) ? $this->input->post('oetRptShpNameTo') : "",
            'tShpCodeSelect'    => !empty($this->input->post('oetRptShpCodeSelect')) ? $this->input->post('oetRptShpCodeSelect') : "",
            'tShpNameSelect'    => !empty($this->input->post('oetRptShpNameSelect')) ? $this->input->post('oetRptShpNameSelect') : "",
            'bShpStaSelectAll'  => !empty($this->input->post('oetRptShpStaSelectAll')) && ($this->input->post('oetRptShpStaSelectAll') == 1) ? true : false,

            // เครื่องจุดขาย
            'tRptPosCodeFrom'   => !empty($this->input->post('oetRptPosCodeFrom')) ? $this->input->post('oetRptPosCodeFrom') : "",
            'tRptPosNameFrom'   => !empty($this->input->post('oetRptPosNameFrom')) ? $this->input->post('oetRptPosNameFrom') : "",
            'tRptPosCodeTo'     => !empty($this->input->post('oetRptPosCodeTo')) ? $this->input->post('oetRptPosCodeTo') : "",
            'tRptPosNameTo'     => !empty($this->input->post('oetRptPosNameTo')) ? $this->input->post('oetRptPosNameTo') : "",
            'tPosCodeSelect'    => !empty($this->input->post('oetRptPosCodeSelect')) ? $this->input->post('oetRptPosCodeSelect') : "",
            'tPosNameSelect'    => !empty($this->input->post('oetRptPosNameSelect')) ? $this->input->post('oetRptPosNameSelect') : "",
            'bPosStaSelectAll'  => !empty($this->input->post('oetRptPosStaSelectAll')) && ($this->input->post('oetRptPosStaSelectAll') == 1) ? true : false,

            // ลูกค้า
            'tCstCodeFrom'      => !empty($this->input->post('oetRptCstCodeFrom')) ? $this->input->post('oetRptCstCodeFrom') : "",
            'tCstNameFrom'      => !empty($this->input->post('oetRptCstNameFrom')) ? $this->input->post('oetRptCstNameFrom') : "",
            'tCstCodeTo'        => !empty($this->input->post('oetRptCstCodeTo')) ? $this->input->post('oetRptCstCodeTo') : "",
            'tCstNameTo'        => !empty($this->input->post('oetRptCstNameTo')) ? $this->input->post('oetRptCstNameTo') : "",
            'tCstCodeSelect'    => !empty($this->input->post('oetRptCstCodeSelect')) ? $this->input->post('oetRptCstCodeSelect') : "",
            'tCstNameSelect'    => !empty($this->input->post('oetRptCstNameSelect')) ? $this->input->post('oetRptCstNameSelect') : "",
            'bCstStaSelectAll'  => !empty($this->input->post('oetRptCstStaSelectAll')) && ($this->input->post('oetRptCstStaSelectAll') == 1) ? true : false,

            // วันที่เอกสาร(DocNo)
            'tDocDateFrom'      => !empty($this->input->post('oetRptDocDateFrom')) ? $this->input->post('oetRptDocDateFrom') : "",
            'tDocDateTo'        => !empty($this->input->post('oetRptDocDateTo')) ? $this->input->post('oetRptDocDateTo') : "",

            //ประเภทเครื่องจุดขาย
            'tPosType'          =>  !empty($this->input->post('ocmPosType')) ? $this->input->post('ocmPosType') : "",

        ];

         // ดึงข้อมูลบริษัทฯ
         $aCompInfoParams = [
            'nLngID'    => $this->nLngID,
            'tBchCode'  => $this->tBchCodeLogin
        ];

        $this->aCompanyInfo = FCNaGetCompanyInfo($aCompInfoParams)['raItems'];
    }

    public function index() {
        if (!empty($this->tRptCode) && !empty($this->tRptExportType)) {



            // // Call Stored Procedure
            $this->Rptsalemember_model->FSnMExecStoreReport($this->aRptFilter);

            // // //  // Count Rows
             $aCountRowParams = [
                'tCompName'      => $this->tCompName,
                'tRptCode'       => $this->tRptCode,
                'tUsrSessionID'  => $this->tUserSessionID,
                'aDataFilter'    => $this->aRptFilter
            ];

             $this->nRows = $this->Rptsalemember_model->FSnMCountRowInTemp($aCountRowParams);

            // // // // // // Report Type
            switch ($this->tRptExportType) {
                case 'html':
                    $this->FSvCCallRptViewBeforePrint();
                    break;
                case 'excel':
                    $this->FSvCCallRptRenderExcel($this->aRptFilter);
                    break;
                case 'pdf':
                    $this->FSvCCallRptRenderExcel($this->aRptFilter);
                    break;
            }
        }
    }


      /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 21/12/2019 Witsarut(Bell)
     * LastUpdate: -
     * Return: View Report Viewersd
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint(){
        try{

            $aDataReportParams = [
                'nPerPage'      => $this->nPerPage,
                'nPage'         => $this->nPage,
                'tCompName'     => $this->tCompName,
                'tRptCode'      => $this->tRptCode,
                'tUsrSessionID' => $this->tUserSessionID,
                'aDataFilter'   => $this->aRptFilter
            ];

            //Get Data
            $aDataReport = $this->Rptsalemember_model->FSaMGetDataReport($aDataReportParams);

            // Load View Advance Table
            $aDataViewRptParams = [
                'nOptDecimalShow' => $this->nOptDecimalShow,
                'aCompanyInfo'    => $this->aCompanyInfo,
                'aDataReport'     => $aDataReport,
                'aDataTextRef'    => $this->aText,
                'aDataFilter'     => $this->aRptFilter
            ];

            $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportsale/rptSaleMember', 'wRptSaleMemberHtml', $aDataViewRptParams);


            // Data Viewer Center Report
            $aDataViewerParams = [
                'tTitleReport'    => $this->aText['tTitleReport'],
                'tRptTypeExport'  => $this->tRptExportType,
                'tRptCode'        => $this->tRptCode,
                'tRptRoute'       => $this->tRptRoute,
                'tViewRenderKool' => $tRptView,
                'aDataFilter' => $this->aRptFilter,
                'aDataReport' => [
                    'raItems'       => $aDataReport['aRptData'],
                    'rnAllRow'      => $aDataReport['aPagination']['nTotalRecord'],
                    'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                    'rnAllPage'     => $aDataReport['aPagination']['nTotalPage'],
                    'rtCode'        => '1',
                    'rtDesc'        => 'success',

                ]
            ];

            $this->load->view('report/report/wReportViewer', $aDataViewerParams);
            /** =========== End Render View ================================== */

        }catch(Exception $Error){
            echo $Error;
        }
    }

     /**
     * Functionality: Click Page Report (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 15/10/2562 Witsarut(Bell)
     * LastUpdate: -
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrintClickPage(){
        /** =========== Begin Init Variable ================================== */
        $aDataFilter = json_decode($this->input->post('ohdRptDataFilter'), true);
        /** =========== End Init Variable ==================================== */

        $aDataWhere = array(
            'tUserSession'      => $this->tUserSessionID,
            'tCompName'         => $this->tCompName,
            'tUserCode'         => $this->tUserLoginCode,
            'tRptCode'          => $this->tRptCode,
            'nPage'             => $this->nPage,
            'nRow'              => $this->nPerPage,
            'nPerPage'          => $this->nPerPage,
            'tUsrSessionID'     => $this->tUserSessionID,
            'aDataFilter'       => $this->aRptFilter
        );

        //Get Data
        $aDataReport = $this->Rptsalemember_model->FSaMGetDataReport($aDataWhere, $aDataFilter);

        // GetDataSumFootReport
        // $aDataSumFoot = $this->Rptsalemember_model->FSaMGetDataSumFootReport($aDataWhere, $aDataFilter);

        // Load View Advance Table
        $aDataViewRptParams = [
            'aDataReport'       => $aDataReport,
            'aDataTextRef'      => $this->aText,
            'aCompanyInfo'      => $this->aCompanyInfo,
            'nOptDecimalShow'   => $this->nOptDecimalShow,
            'aDataFilter'       => $this->aRptFilter
        ];

        // Load View Advance Tablew
        $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportsale/rptSaleMember', 'wRptSaleMemberHtml', $aDataViewRptParams);

        // Data Viewer Center Report
        $aDataViewer = array(
            'tTitleReport' => $this->aText['tTitleReport'],
            'tRptTypeExport' => $this->tRptExportType,
            'tRptCode' => $this->tRptCode,
            'tRptRoute' => $this->tRptRoute,
            'tViewRenderKool' => $tRptView,
            'aDataFilter' => $aDataFilter,
            'aDataReport' => array(
                'raItems' => $aDataReport['aRptData'],
                'rnAllRow' => $aDataReport['aPagination']['nTotalRecord'],
                'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                'rnAllPage' => $aDataReport['aPagination']['nTotalPage'],
                'rtCode' => '1',
                'rtDesc' => 'success',
            )
        );

        $this->load->view('report/report/wReportViewer', $aDataViewer);

    }

    /**
     * Functionality: Click Page Report (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 23/09/2019 Witsarut
     * LastUpdate:
     * Return: object Status Count Data Report
     * ReturnType: Object
     */
    public function FSoCChkDataReportInTableTemp(){
        try{
            $aDataCountData = [
                'tCompName'     => $this->tCompName,
                'tRptCode'      => $this->tRptCode,
                'tUserSession'  => $this->tUserSessionID,
                'aDataFilter'   => $this->aRptFilter
            ];

            $nDataCountPage     = $this->Rptsalemember_model->FSnMCountRowInTemp($aDataCountData);

            $aResponse = array(
                'nCountPageAll' => $nDataCountPage,
                'nStaEvent'     => 1,
                'tMessage'      => 'Success Count Data All'
            );
        }catch(ErrorException $Error){
            $aResponse = array(
                'nStaEvent' => 500,
                'tMessage' => $Error->getMessage()
            );
        }
        echo json_encode($aResponse);
    }


    /**
     * Functionality: Send Rabbit MQ Report
     * Parameters:  Function Parameter
     * Creator: 23/069/2019 Witsarut
     * LastUpdate:
     * Return: object Send Rabbit MQ Report
     * ReturnType: Object
     */
    public function FSvCCallRptExportFile(){
        $dDateSendMQ     = date('Y-m-d');
        $dTimeSendMQ     = date('H:i:s');
        $dDateSubscribe  = date('Ymd');
        $dTimeSubscribe  = date('His');

        // Set Parameter Send MQ
        $tRptQueueName = 'RPT_' . $this->tSysBchCode . '_' . $this->tRptGroup . '_' . $this->tRptCode;

        $aDataSendMQ    = [
            'tQueueName' => $tRptQueueName,
            'aParams' => [
                'ptRptCode'         => $this->tRptCode,
                'pnPerFile'         => 20000,
                'ptUserCode'        => $this->tUserLoginCode,
                'ptUserSessionID'   => $this->tUserSessionID,
                'pnLngID'           => $this->nLngID,
                'ptFilter'          => $this->aRptFilter,
                'ptRptExpType'      => $this->tRptExportType,
                'ptComName'         => $this->tCompName,
                'ptDate'            => $dDateSendMQ,
                'ptTime'            => $dTimeSendMQ,
                'ptBchCode'         => (!empty($this->session->userdata('tSesUsrBchCom')) ? $this->session->userdata('tSesUsrBchCom') : $this->session->userdata('tSesUsrBchCom'))
            ]
        ];

        FCNxReportCallRabbitMQ($aDataSendMQ);
        $aResponse  = array(
            'nStaEvent' => 1,
            'tMessage'  => 'Success Send Rabbit MQ.',
            'aDataSubscribe'    => array(
                'ptSysBchCode'      => $this->tSysBchCode,
                'ptComName'         => $this->tCompName,
                'ptRptCode'         => $this->tRptCode,
                'ptUserCode'        => $this->tUserLoginCode,
                'ptUserSessionID'   => $this->tUserSessionID,
                'pdDateSubscribe'   => $dDateSubscribe,
                'pdTimeSubscribe'   => $dTimeSubscribe,
            )
        );
        echo json_encode($aResponse);
    }


    public function FSvCScoutPerfomance(){

        // $oWriter = WriterEntityFactory::createODSWriter();
        // $oWriter = WriterEntityFactory::createCSVWriter();


    $tSql = "SELECT TOP 100000
        STKC.FNStkCrdID,
        STKC.FTBchCode,
        STKC.FDStkDate,
        STKC.FTStkDocNo,
        STKC.FTWahCode,
        STKC.FTPdtCode,
        STKC.FTStkType,
        STKC.FCStkQty,
        STKC.FCStkSetPrice,
        STKC.FCStkCostIn,
        STKC.FCStkCostEx,
        STKC.FDCreateOn,
        STKC.FTCreateBy
        FROM
        TCNTPdtStkCrd STKC
        ";

           $oQuery = $this->db->query($tSql);
        //  echo  $this->db->last_query();
        //    die();
           $aData = $oQuery->result_array();
           $tFileName ='ScoutPerfomance_'.date('YmdHis').'.xlsx';
           $oWriter = WriterEntityFactory::createXLSXWriter();
           $oWriter->openToBrowser($tFileName);
        // $oWriter->openToFile('D:/');
        foreach($aData as $aData){
           $aCells = [
            WriterEntityFactory::createCell('Usage: ' . (memory_get_usage(true) / 1024 / 1024) . ' MB' . PHP_EOL),
            WriterEntityFactory::createCell('Peak: ' . (memory_get_peak_usage(true) / 1024 / 1024) . ' MB' . PHP_EOL),
            WriterEntityFactory::createCell($aData['FTBchCode']),
            WriterEntityFactory::createCell($aData['FDStkDate']),
            WriterEntityFactory::createCell($aData['FTStkDocNo']),
            WriterEntityFactory::createCell($aData['FTWahCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTStkType']),
            WriterEntityFactory::createCell($aData['FCStkQty']),
            WriterEntityFactory::createCell($aData['FCStkSetPrice']),
            WriterEntityFactory::createCell($aData['FCStkCostIn']),
            WriterEntityFactory::createCell($aData['FCStkCostEx']),
            WriterEntityFactory::createCell($aData['FDCreateOn']),
            WriterEntityFactory::createCell($aData['FTCreateBy']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            WriterEntityFactory::createCell($aData['FTPdtCode']),
            ];

            // $aMultiRows[] = WriterEntityFactory::createRow($aCells);
            $aSingleRows = WriterEntityFactory::createRow($aCells);
            $oWriter->addRow($aSingleRows);

            //  echo "memory (chunk loaded): " . (memory_get_usage(true) / 1024 / 1024) . ' MB' . PHP_EOL . "\n<br>";
        }
        // echo "memory (all loaded): " . memory_get_usage() . "\n<br>";
        echo 'Usage: ' . (memory_get_usage(true) / 1024 / 1024) . ' MB' . PHP_EOL;
        echo 'Peak: ' . (memory_get_peak_usage(true) / 1024 / 1024) . ' MB' . PHP_EOL;
        // $oWriter->addRows($aMultiRows);

        $oWriter->close();
        unset($aCells);
        unset($aSingleRows);
        unset($aData);
        unset($oQuery);
        // die();
    // $tFileName ='ScoutPerfomance_'.date('YmdHis').'.xlsx';
    // $writer = WriterEntityFactory::createXLSXWriter();

    //     $currentSheet = $writer->getCurrentSheet();

    //     $mergeRanges = ['A1:C1']; // you can list the cells you want to merge like this ['A1:A4','A1:E1']
    //     $currentSheet->setMergeRanges($mergeRanges);

    // $cells = [
    //     WriterEntityFactory::createCell('Carlssssssssssssssssssssssssssssssss'),
    //     WriterEntityFactory::createCell('is'),
    //     WriterEntityFactory::createCell('great!'),
    // ];

    //     /** add multiple rows at a time */
    // $multipleRows = [
    //     WriterEntityFactory::createRow($cells),
    //     WriterEntityFactory::createRow($cells),
    // ];
    // $writer->openToBrowser($tFileName);
    // $writer->addRows($multipleRows);
    // $writer->close();








    }



    /**
     * Functionality: Render Excel Report
     * Parameters:  Function Parameter
     * Creator: 30/07/2020 Nattakit
     * LastUpdate:
     * Return: file
     * ReturnType: file
     */
    public function  FSvCCallRptRenderExcel(){
        ini_set('memory_limit','-1');
        $tFileName = 'Rpt-'.$this->aText['tTitleReport'].'_'.date('YmdHis').'.xlsx';
        $oWriter = WriterEntityFactory::createXLSXWriter();

         $oWriter->openToBrowser($tFileName); // stream data directly to the browser

         $aMulltiRow = $this->FSoCCallRptRenderHedaerExcel();  //เรียกฟังชั่นสร้างส่วนหัวรายงาน
         $oWriter->addRows($aMulltiRow);

            $oBorder = (new BorderBuilder())
                ->setBorderTop(Color::BLACK, Border::WIDTH_THIN)
                ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
                ->build();

            $oStyleColums = (new StyleBuilder())
                ->setBorder($oBorder)
                ->setFontBold()
                ->build();

            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptCstCode']),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell($this->aText['tRptCstName']),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell($this->aText['tRptSale']),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell($this->aText['tRptDisCount']),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell($this->aText['tRptSaleTotle']),
                WriterEntityFactory::createCell(NULL),
                ];

            /** add a row at a time */
            $singleRow = WriterEntityFactory::createRow($aCells,$oStyleColums);
            $oWriter->addRow($singleRow);

        $aDataReportParams = [
            'nPerPage'      => 999999999999,
            'nPage'         => $this->nPage,
            'tCompName'     => $this->tCompName,
            'tRptCode'      => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter'   => $this->aRptFilter
        ];

      //Get Data
      $aDataReport = $this->Rptsalemember_model->FSaMGetDataReport($aDataReportParams);

         /** Create a style with the StyleBuilder */
            $oStyle = (new StyleBuilder())
                    ->setCellAlignment(CellAlignment::RIGHT)
                    ->build();

         if(isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) {
             foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
                       $values= [
                                WriterEntityFactory::createCell($aValue['FTCstCode']),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell($aValue['FTCstName']),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXshTotal'])),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXshDis'])),
                                WriterEntityFactory::createCell(NULL),
                                WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXshGrand'])),
                                WriterEntityFactory::createCell(NULL),
                        ];
                        $aRow = WriterEntityFactory::createRow($values);
                        $oWriter->addRow($aRow);

                    if(($nKey+1)==count($aDataReport['aRptData'])){ //SumFooter
                        $values= [
                            WriterEntityFactory::createCell($this->aText['tRptRentAmtFolCourSumText']),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(FCNnGetNumeric($aValue["FCXshTotal_Footer"])),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(FCNnGetNumeric($aValue["FCXshDis_Footer"])),
                            WriterEntityFactory::createCell(NULL),
                            WriterEntityFactory::createCell(FCNnGetNumeric($aValue["FCXshGrand_Footer"])),
                            WriterEntityFactory::createCell(NULL),
                    ];
                    $aRow = WriterEntityFactory::createRow($values,$oStyleColums);
                    $oWriter->addRow($aRow);

                    }
             }
            }

            $aMulltiRow = $this->FSoCCallRptRenderFooterExcel();//เรียกฟังชั่นสร้างส่วนท้ายรายงาน
            $oWriter->addRows($aMulltiRow);

        $oWriter->close();
    }

    /**
     * Functionality: Render Excel Report Header
     * Parameters:  Function Parameter
     * Creator: 30/07/2020 Nattakit
     * LastUpdate:
     * Return: oject
     * ReturnType: oject
     */
    public function FSoCCallRptRenderHedaerExcel(){
        $oStyle = (new StyleBuilder())
        ->setFontBold()
        ->setFontSize(12)
        ->build();

        $aCells = [
        WriterEntityFactory::createCell($this->aCompanyInfo['FTCmpName']),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell($this->aText['tTitleReport']),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells,$oStyle);

        $tAddress = '';
        if(isset($this->aCompanyInfo) && !empty($this->aCompanyInfo)) {
        if ($this->aCompanyInfo['FTAddVersion'] == '1') {
               $tAddress = $this->aCompanyInfo['FTAddV1No'].' '.$this->aCompanyInfo['FTAddV1Road'].' '.$this->aCompanyInfo['FTAddV1Soi'].' '.$this->aCompanyInfo['FTSudName'].' '.$this->aCompanyInfo['FTDstName'].' '.$this->aCompanyInfo['FTPvnName'].' '.$this->aCompanyInfo['FTAddV1PostCode'];
        }
        if ($this->aCompanyInfo['FTAddVersion'] == '2') {
               $tAddress =  $this->aCompanyInfo['FTAddV2Desc1'].' '.$this->aCompanyInfo['FTAddV2Desc2'];
        }
    }

    $aCells = [
        WriterEntityFactory::createCell($tAddress),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);


        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptAddrTel'].' '.$this->aCompanyInfo['FTCmpTel']),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            ];

        $aMulltiRow[]  = WriterEntityFactory::createRow($aCells);



        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptAddrBranch'] .' '. $this->aCompanyInfo['FTBchName']),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);


        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptTaxSalePosTaxId'] .' '. $this->aCompanyInfo['FTAddTaxNo']),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);


        $aCells = [
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);


        if((isset($this->aRptFilter['tDocDateFrom']) && !empty($this->aRptFilter['tDocDateFrom'])) && (isset($this->aRptFilter['tDocDateTo']) && !empty($this->aRptFilter['tDocDateTo']))){
            $aCells = [
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell($this->aText['tRptTaxSaleMemberDocDateFrom'].' '.date('d/m/Y',strtotime($this->aRptFilter['tDocDateFrom'])).' '.$this->aText['tRptTaxSaleMemberDocDateTo'].' '.date('d/m/Y',strtotime($this->aRptFilter['tDocDateTo']))),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                ];
                $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        $aCells = [
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell($this->aText['tDatePrint'].' '.date('d/m/Y').' '.$this->aText['tTimePrint'].' '.date('H:i:s')),
            ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        return $aMulltiRow;

    }

    /**
     * Functionality: Render Excel Report Footer
     * Parameters:  Function Parameter
     * Creator: 30/07/2020 Nattakit
     * LastUpdate:
     * Return: oject
     * ReturnType: oject
     */
    public function FSoCCallRptRenderFooterExcel(){

        $oStyleFilter = (new StyleBuilder())
        ->setFontBold()
        ->build();
    $aCells = [
        WriterEntityFactory::createCell($this->aText['tRptConditionInReport']),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        WriterEntityFactory::createCell(NULL),
        ];
        $aMulltiRow[] = WriterEntityFactory::createRow($aCells,$oStyleFilter);


        if (isset($this->aRptFilter['tBchCodeSelect']) && !empty($this->aRptFilter['tBchCodeSelect'])) {
          $tBchSelect =  ($this->aRptFilter['bBchStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tBchNameSelect'];
        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptBchFrom'].' : '.$tBchSelect),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            WriterEntityFactory::createCell(NULL),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }


        if (isset($this->aRptFilter['tMerCodeSelect']) && !empty($this->aRptFilter['tMerCodeSelect'])) {
            $tMerSelect =  ($this->aRptFilter['bMerStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tMerNameSelect'];
          $aCells = [
              WriterEntityFactory::createCell($this->aText['tRptMerFrom'].' : '.$tMerSelect),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              WriterEntityFactory::createCell(NULL),
              ];
              $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
          }

          if (isset($this->aRptFilter['tShpCodeSelect']) && !empty($this->aRptFilter['tShpCodeSelect'])) {
            $tShpSelect =  ($this->aRptFilter['bShpStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tShpNameSelect'];
                $aCells = [
                    WriterEntityFactory::createCell($this->aText['tRptAdjShopFrom'].' : '.$tShpSelect),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    WriterEntityFactory::createCell(NULL),
                    ];
                    $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
          }

          if (isset($this->aRptFilter['tPosCodeSelect']) && !empty($this->aRptFilter['tPosCodeSelect'])) {
            $tPosSelect =  ($this->aRptFilter['bPosStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tPosCodeSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptPosFrom'].' : '.$tPosSelect),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                ];
                $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
          }


          if ((isset($this->aRptFilter['tCstCodeFrom']) && !empty($this->aRptFilter['tCstCodeFrom'])) && (isset($this->aRptFilter['tCstCodeTo']) && !empty($this->aRptFilter['tCstCodeTo']))) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptCstFrom'].' : '.$this->aRptFilter['tCstCodeFrom'].' '.$this->aText['tRptCstTo'].' : '.$this->aRptFilter['tCstCodeTo']),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                WriterEntityFactory::createCell(NULL),
                ];
                $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
          }

          return $aMulltiRow;

    }




}
