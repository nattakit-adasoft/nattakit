<?php

defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set("Asia/Bangkok");

include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel.php';
include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel/IOFactory.php';
include APPPATH . 'third_party/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

class Rptrentamountdetail_controller extends MX_Controller {

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

    public function __construct() {
        $this->load->helper('report');
        $this->load->model('company/company/Company_model');
        $this->load->model('report/report/Report_model');
        $this->load->model('report/reportlocker/Rptrentamountdetail_model');

        // Init Report
        $this->init();

        parent::__construct();
    }

    private function init() {
        $this->aText = [
            'tTitleReport'  => language('report/report/report', 'tRptRentAmtForDetailTitle'),
            'tDatePrint'    => language('report/report/report', 'tRptAdjStkVDDatePrint'),
            'tTimePrint'    => language('report/report/report', 'tRptAdjStkVDTimePrint'),
            // Address Lang
            'tRptAddrBuilding'      => language('report/report/report', 'tRptAddrBuilding'),
            'tRptAddrRoad'          => language('report/report/report', 'tRptAddrRoad'),
            'tRptAddrSoi'           => language('report/report/report', 'tRptAddrSoi'),
            'tRptAddrSubDistrict'   => language('report/report/report', 'tRptAddrSubDistrict'),
            'tRptAddrDistrict'      => language('report/report/report', 'tRptAddrDistrict'),
            'tRptAddrProvince'      => language('report/report/report', 'tRptAddrProvince'),
            'tRptAddrTel'           => language('report/report/report', 'tRptAddrTel'),
            'tRptAddrFax'           => language('report/report/report', 'tRptAddrFax'),
            'tRptAddrTaxNo'         => language('report/report/report', 'tRptAddrTaxNo'),
            'tRptAddrBranch'        => language('report/report/report', 'tRptAddrBranch'),
            'tRptAddV2Desc1'        => language('report/report/report', 'tRptAddV2Desc1'),
            'tRptAddV2Desc2'        => language('report/report/report', 'tRptAddV2Desc2'),
            // Table Label
            'tRptRentAmtForDetailSerailPos' => language('report/report/report', 'tRptRentAmtForDetailSerailPos'),
            'tRptRentAmtForDetailUser' => language('report/report/report', 'tRptRentAmtForDetailUser'),
            'tRptRentAmtForDetailDocno' => language('report/report/report', 'tRptRentAmtForDetailDocno'),
            'tRptRentAmtForDetailDocDate' => language('report/report/report', 'tRptRentAmtForDetailDocDate'),
            'tRptRentAmtForDetailRack' => language('report/report/report', 'tRptRentAmtForDetailRack'),
            'tRptRentAmtForDetailSubRack' => language('report/report/report', 'tRptRentAmtForDetailSubRack'),
            'tRptRentAmtForDetailSize' => language('report/report/report', 'tRptRentAmtForDetailSize'),
            'tRptRentAmtForDetailDateGet' => language('report/report/report', 'tRptRentAmtForDetailDateGet'),
            'tRptRentAmtForDetailLoginTo' => language('report/report/report', 'tRptRentAmtForDetailLoginTo'),
            'tRptRentAmtForDetailStaPayment' => language('report/report/report', 'tRptRentAmtForDetailStaPayment'),
            'tRptRentAmtForDetailAmtPayment' => language('report/report/report', 'tRptRentAmtForDetailAmtPayment'),
            'tRptTaxSalePosTel' => language('report/report/report', 'tRptTaxSalePosTel'),
            'tRptTaxSalePosFax' => language('report/report/report', 'tRptTaxSalePosFax'),
            'tRptTaxSalePosBch' => language('report/report/report', 'tRptTaxSalePosBch'),
            // No Data Report
            'tRptAdjStkNoData' => language('common/main/main', 'tCMNNotFoundData'),
            // in report
            'tRptRentAmtForDetailStaPaymentNoPay'       => language('report/report/report', 'tRptRentAmtForDetailStaPaymentNoPay'),
            'tRptRentAmtForDetailStaPaymentSome'        => language('report/report/report', 'tRptRentAmtForDetailStaPaymentSome'),
            'tRptRentAmtForDetailStaPaymentAlready'     => language('report/report/report', 'tRptRentAmtForDetailStaPaymentAlready'),
            'tRptRentAmtForDetailSumText'               => language('report/report/report', 'tRptRentAmtForDetailSumText'),
            'tRptRentAmtForDetailSumTextLast'           => language('report/report/report', 'tRptRentAmtForDetailSumTextLast'),
            // report filter
            'tRptBchFrom'   => language('report/report/report', 'tRptBchFrom'),
            'tRptBchTo'     => language('report/report/report', 'tRptBchTo'),
            'tRptShopFrom'  => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo'    => language('report/report/report', 'tRptShopTo'),
            'tRptPosFrom'   => language('report/report/report', 'tRptPosFrom'),
            'tRptPosTo'     => language('report/report/report', 'tRptPosTo'),
            'tRptDateFrom'  => language('report/report/report', 'tRptDateFrom'),
            'tRptDateTo'    => language('report/report/report', 'tRptDateTo'),
            'tRptRackFrom'  => language('report/report/report', 'tRptRackFrom'),
            'tRptRackTo'    => language('report/report/report', 'tRptRackTo'),
            // Lang Condition Other Report
            'tRptConditionInReport' => language('report/report/report', 'tRptConditionInReport'),
        ];

        $this->tBchCodeLogin = (!empty($this->session->userdata('tSesUsrBchCom')) ? $this->session->userdata('tSesUsrBchCom') : $this->session->userdata('tSesUsrBchCom'));
        $this->nPerPage = 100;
        $this->nOptDecimalShow = FCNxHGetOptionDecimalShow();
        
        $tIP = $this->input->ip_address();
        $tFullHost = gethostbyaddr($tIP);
        $this->tCompName = $tFullHost;
        
        $this->nLngID = FCNaHGetLangEdit();
        $this->tRptCode = $this->input->post('ohdRptCode');
        $this->tRptGroup = $this->input->post('ohdRptGrpCode');
        $this->tUserSessionID = $this->session->userdata('tSesSessionID');
        $this->tRptRoute = $this->input->post('ohdRptRoute');
        $this->tRptExportType = $this->input->post('ohdRptTypeExport');
        $this->nPage = empty($this->input->post('ohdRptCurrentPage')) ? 1 : $this->input->post('ohdRptCurrentPage');
        $this->tUserLoginCode = $this->session->userdata('tSesUsername');

        // Report Filter
        $this->aRptFilter = [
            'tUsrSessionID' => $this->tUserSessionID,
            'tCompName'     => $this->tCompName,
            'tRptCode'      => $this->tRptCode,
            'nLangID'       => $this->nLngID,
            'tBchCodeFrom'  => empty($this->input->post('oetRptBchCodeFrom')) ? '' : $this->input->post('oetRptBchCodeFrom'),
            'tBchNameFrom'  => empty($this->input->post('oetRptBchNameFrom')) ? '' : $this->input->post('oetRptBchNameFrom'),
            'tBchCodeTo'    => empty($this->input->post('oetRptBchCodeTo')) ? '' : $this->input->post('oetRptBchCodeTo'),
            'tBchNameTo'    => empty($this->input->post('oetRptBchNameTo')) ? '' : $this->input->post('oetRptBchNameTo'),
            'tShopCodeFrom' => empty($this->input->post('oetRptShpCodeFrom')) ? '' : $this->input->post('oetRptShpCodeFrom'),
            'tShopNameFrom' => empty($this->input->post('oetRptShpNameFrom')) ? '' : $this->input->post('oetRptShpNameFrom'),
            'tShopCodeTo'   => empty($this->input->post('oetRptShpCodeTo')) ? '' : $this->input->post('oetRptShpCodeTo'),
            'tShopNameTo'   => empty($this->input->post('oetRptShpNameTo')) ? '' : $this->input->post('oetRptShpNameTo'),
            'tPosCodeFrom'  => empty($this->input->post('oetRptPosCodeFrom')) ? '' : $this->input->post('oetRptPosCodeFrom'),
            'tPosNameFrom'  => empty($this->input->post('oetRptPosNameFrom')) ? '' : $this->input->post('oetRptPosNameFrom'),
            'tPosCodeTo'    => empty($this->input->post('oetRptPosCodeTo')) ? '' : $this->input->post('oetRptPosCodeTo'),
            'tPosNameTo'    => empty($this->input->post('oetRptPosNameTo')) ? '' : $this->input->post('oetRptPosNameTo'),
            'tRackCodeFrom' => empty($this->input->post('oetSMLBrowseGroupCodeFrom')) ? '' : $this->input->post('oetSMLBrowseGroupCodeFrom'),
            'tRackNameFrom' => empty($this->input->post('oetSMLBrowseGroupNameFrom')) ? '' : $this->input->post('oetSMLBrowseGroupNameFrom'),
            'tRackCodeTo'   => empty($this->input->post('oetSMLBrowseGroupCodeTo')) ? '' : $this->input->post('oetSMLBrowseGroupCodeTo'),
            'tRackNameTo'   => empty($this->input->post('oetSMLBrowseGroupNameTo')) ? '' : $this->input->post('oetSMLBrowseGroupNameTo'),
            'tDocDateFrom'  => empty($this->input->post('oetRptDocDateFrom')) ? '' : $this->input->post('oetRptDocDateFrom'),
            'tDocDateTo'    => empty($this->input->post('oetRptDocDateTo')) ? '' : $this->input->post('oetRptDocDateTo'),
            'FNResult'      => 0
        ];

        // ดึงข้อมูลบริษัทฯ
        $aCompInfoParams = [
            'nLngID' => $this->nLngID,
            'tBchCode' => $this->tBchCodeLogin
        ];
        $this->aCompanyInfo = FCNaGetCompanyInfo($aCompInfoParams)['raItems'];
    }

    public function index() {
        if (!empty($this->tRptCode) && !empty($this->tRptExportType)) {

            // Execute Stored Procedure
            $this->Rptrentamountdetail_model->FSnMExecStoreReport($this->aRptFilter);
            // Count Rows
            $aCountRowParams = [
                'tCompName' => $this->tCompName,
                'tRptCode' => $this->tRptCode,
                'tSessionID' => $this->tUserSessionID
            ];
            $this->nRows = $this->Rptrentamountdetail_model->FSaMCountDataReportAll($aCountRowParams);

            // Report Type
            switch ($this->tRptExportType) {
                case 'html':
                    $this->FSvCCallRptViewBeforePrint();
                    break;
                case 'excel':
                    $this->FSoCChkDataReportInTableTemp();
                    break;
                case 'pdf':
                    break;
            }
        }
    }

    /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 22/07/2019 Piya
     * LastUpdate: 25/09/2019 Saharat(Golf)
     * Return: View Report Viewersd
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint() {
        try {
            /*===== Begin Get Data =====================================================*/
            // ดึงข้อมูลจากฐานข้อมูล Temp
            $aDataReportParams = [
                'nPerPage' => $this->nPerPage,
                'nPage' => $this->nPage,
                'tCompName' => $this->tCompName,
                'tRptCode' => $this->tRptCode,
                'tUsrSessionID' => $this->tUserSessionID,
            ];
            $aDataReport = $this->Rptrentamountdetail_model->FSaMGetDataReport($aDataReportParams);
            /*===== End Get Data =======================================================*/

            /*===== Begin Render View ==================================================*/
            // Load View Advance Table
            $aDataViewRptParams = [
                'nOptDecimalShow' => $this->nOptDecimalShow,
                'aCompanyInfo' => $this->aCompanyInfo,
                'aDataReport' => $aDataReport,
                'aDataTextRef' => $this->aText,
                'aDataFilter' => $this->aRptFilter
            ];
            $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportRentAmountDetail', 'wRptRentAmountDetailHtml', $aDataViewRptParams);

            // Data Viewer Center Report
            $aDataViewerParams = [
                'tTitleReport' => $this->aText['tTitleReport'],
                'tRptTypeExport' => $this->tRptExportType,
                'tRptCode' => $this->tRptCode,
                'tRptRoute' => $this->tRptRoute,
                'tViewRenderKool' => $tRptView,
                'aDataFilter' => $this->aRptFilter,
                'aDataReport' => [
                    'raItems' => $aDataReport['aRptData'],
                    'rnAllRow' => $aDataReport['aPagination']['nTotalRecord'],
                    'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                    'rnAllPage' => $aDataReport['aPagination']['nTotalPage'],
                    'rtCode' => '1',
                    'rtDesc' => 'success',
                ]
            ];
            $this->load->view('report/report/wReportViewer', $aDataViewerParams);
            /*===== End Render View ====================================================*/
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    /**
     * Functionality: Click Page ดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 22/07/2019 Piya
     * LastUpdate: -
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrintClickPage() {

        /*===== Begin Init Variable ====================================================*/
        $aDataFilter = json_decode($this->input->post('ohdRptDataFilter'), true);
        /*===== End Init Variable ======================================================*/

        /*===== Begin Get Data =========================================================*/
        // ดึงข้อมูลจากฐานข้อมูล Temp
        $aDataReportParams = [
            'nPerPage' => $this->nPerPage,
            'nPage' => $this->nPage,
            'tCompName' => $this->tCompName,
            'tRptCode' => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
        ];
        $aDataReport = $this->Rptrentamountdetail_model->FSaMGetDataReport($aDataReportParams);
        /*===== End Get Data ========================================================== */

        /*===== Begin Render View ======================================================*/
        // Load View Advance Table
        $aDataViewRptParams = array(
            'nOptDecimalShow' => $this->nOptDecimalShow,
            'aCompanyInfo' => $this->aCompanyInfo,
            'aDataReport' => $aDataReport,
            'aDataTextRef' => $this->aText,
            'aDataFilter' => $aDataFilter
        );
        $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportRentAmountDetail', 'wRptRentAmountDetailHtml', $aDataViewRptParams);

        // Data Viewer Center Report
        $aDataViewerParams = array(
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
                'rtDesc' => 'success'
            )
        );
        $this->load->view('report/report/wReportViewer', $aDataViewerParams);
        /*===== End Render View ========================================================*/
    }

    /**
     * Functionality: Click Page Report (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 22/07/2019 Piya
     * LastUpdate: 25/09/2019 Saharat(Golf)
     * Return: Object Status Count Data Report
     * ReturnType: Object
     */
    public function FSoCChkDataReportInTableTemp() {
        try {
            $aDataCountData = [
                'tCompName' => $this->tCompName,
                'tRptCode' => $this->tRptCode,
                'tSessionID' => $this->tUserSessionID,
            ];

            $nDataCountPage = $this->Rptrentamountdetail_model->FSaMCountDataReportAll($aDataCountData);

            $aResponse = array(
                'nCountPageAll' => $nDataCountPage,
                'nStaEvent' => 1,
                'tMessage' => 'Success Count Data All'
            );
        } catch (ErrorException $Error) {
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
     * Creator: 22/07/2019 Piya
     * LastUpdate: 25/09/2019 Sahaart(Golf)
     * Return: object Send Rabbit MQ Report
     * ReturnType: Object
     */
    public function FSvCCallRptExportFile() {
        try {
            $tRptGrpCode = $this->tRptGroup;
            $tRptCode = $this->tRptCode;
            $tUserCode = $this->tUserLoginCode;
            $tSessionID = $this->tUserSessionID;
            $nLangID = FCNaHGetLangEdit();
            $tRptExportType = $this->tRptExportType;
            $tCompName = $this->tCompName;
            $dDateSendMQ = date('Y-m-d');
            $dTimeSendMQ = date('H:i:s');
            $dDateSubscribe = date('Ymd');
            $dTimeSubscribe = date('His');

            // Set Parameter Send MQ
            $tRptQueueName = 'RPT_' . $tRptGrpCode . '_' . $tRptCode;

            $aDataSendMQ = [
                'tQueueName' => $tRptQueueName,
                'aParams' => [
                    'ptRptCode' => $tRptCode,
                    'pnPerFile' => 20000,
                    'ptUserCode' => $tUserCode,
                    'ptUserSessionID' => $tSessionID,
                    'pnLngID' => $nLangID,
                    'ptFilter' => $this->aRptFilter,
                    'ptRptExpType' => $tRptExportType,
                    'ptComName' => $tCompName,
                    'ptDate' => $dDateSendMQ,
                    'ptTime' => $dTimeSendMQ,
                    'ptBchCode' => (!empty($this->session->userdata('tSesUsrBchCom')) ? $this->session->userdata('tSesUsrBchCom') : $this->session->userdata('tSesUsrBchCom'))
                ]
            ];

            FCNxReportCallRabbitMQ($aDataSendMQ);

            $aResponse = array(
                'nStaEvent' => 1,
                'tMessage' => 'Success Send Rabbit MQ.',
                'aDataSubscribe' => array(
                    'ptComName' => $tCompName,
                    'ptRptCode' => $tRptCode,
                    'ptUserCode' => $tUserCode,
                    'ptUserSessionID' => $tSessionID,
                    'pdDateSubscribe' => $dDateSubscribe,
                    'pdTimeSubscribe' => $dTimeSubscribe,
                )
            );
        } catch (Exception $Error) {
            $aResponse = array(
                'nStaEvent' => 500,
                'tMessage' => $Error->getMessage()
            );
        }
        echo json_encode($aResponse);
    }

}


