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
class Rptsalesmonthproduct_controller extends MX_Controller
{

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
     * Sys Bch Code
     * @var string
     */
    public $tSysBchCode;

    public function __construct()
    {
        $this->load->model('company/company/Company_model');
        $this->load->model('report/reportsale/Rptsalesmonthproduct_model');
        $this->load->model('report/report/Report_model');

        // Init Report
        $this->init();

        parent::__construct();
    }

    private function init()
    {
        $this->aText = [
            'tTitleReport' => language('report/report/report', 'tRptTitleSMP'),
            'tDatePrint' => language('report/report/report', 'tRptAdjStkVDDatePrint'),
            'tTimePrint' => language('report/report/report', 'tRptAdjStkVDTimePrint'),

            // Address Lang
            'tRptAddrBuilding' => language('report/report/report', 'tRptAddrBuilding'),
            'tRptAddrRoad' => language('report/report/report', 'tRptAddrRoad'),
            'tRptAddrSoi' => language('report/report/report', 'tRptAddrSoi'),
            'tRptAddrSubDistrict' => language('report/report/report', 'tRptAddrSubDistrict'),
            'tRptAddrDistrict' => language('report/report/report', 'tRptAddrDistrict'),
            'tRptAddrProvince' => language('report/report/report', 'tRptAddrProvince'),
            'tRptAddrTel' => language('report/report/report', 'tRptAddrTel'),
            'tRptAddrFax' => language('report/report/report', 'tRptAddrFax'),
            'tRptAddrBranch' => language('report/report/report', 'tRptAddrBranch'),
            'tRptAddV2Desc1' => language('report/report/report', 'tRptAddV2Desc1'),
            'tRptAddV2Desc2' => language('report/report/report', 'tRptAddV2Desc2'),

            // Table Report
            'tRptBarchCode' => language('report/report/report', 'tRptBarchCode'),
            'tRptBarchName' => language('report/report/report', 'tRptBarchName'),
            'tRptDocDate' => language('report/report/report', 'tRptDocDate'),
            'tRptShopCode' => language('report/report/report', 'tRptShopCode'),
            'tRptShopName' => language('report/report/report', 'tRptShopName'),
            'tRptAmount' => language('report/report/report', 'tRptAmount'),
            'tRptSale' => language('report/report/report', 'tRptSale'),
            'tRptCancelSale' => language('report/report/report', 'tRptCancelSale'),
            'tRptTotalSale' => language('report/report/report', 'tRptTotalSale'),
            'tRptTotalAllSale' => language('report/report/report', 'tRptTotalAllSale'),
            'tRptPayby' => language('report/report/report', 'tRptPayby'),
            'tRptRcvDocumentCode' => language('report/report/report', 'tRptRcvDocumentCode'),
            'tRptDate' => language('report/report/report', 'tRptDate'),
            'tRptRcvTotal' => language('report/report/report', 'tRptRcvTotal'),

            // Filter Heard Report
            'tRptRcvFrom' => language('report/report/report', 'tRptRcvFrom'),
            'tRptRcvTo' => language('report/report/report', 'tRptRcvTo'),
            'tRptBchFrom' => language('report/report/report', 'tRptBchFrom'),
            'tRptBchTo' => language('report/report/report', 'tRptBchTo'),
            'tRptShopFrom' => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo' => language('report/report/report', 'tRptShopTo'),
            'tPdtCodeFrom' => language('report/report/report', 'tPdtCodeFrom'),
            'tPdtCodeTo' => language('report/report/report', 'tPdtCodeTo'),
            'tPdtGrpFrom' => language('report/report/report', 'tPdtGrpFrom'),
            'tPdtGrpTo' => language('report/report/report', 'tPdtGrpTo'),
            'tPdtTypeFrom' => language('report/report/report', 'tPdtTypeFrom'),
            'tPdtTypeTo' => language('report/report/report', 'tPdtTypeTo'),
            'tRptDateFrom' => language('report/report/report', 'tRptDateFrom'),
            'tRptDateTo' => language('report/report/report', 'tRptDateTo'),

            // Table Label
            'tRptBillNo' => language('report/report/report', 'tRptBillNo'),
            'tRptDate' => language('report/report/report', 'tRptDate'),
            'tRptProduct' => language('report/report/report', 'tRptProduct'),
            'tRptCabinetnumber' => language('report/report/report', 'tRptCabinetnumber'),
            'tRptPrice' => language('report/report/report', 'tRptPrice'),

            'tRptDiscount' => language('report/report/report', 'tDiscount'),
            'tRptTax' => language('report/report/report', 'tRptTax'),
            'tRptGrand' => language('report/report/report', 'tRptGrand'),
            'tRptTotalSub' => language('report/report/report', 'tRptTotalSub2'),
            'tRptTotalFooter' => language('report/report/report', 'tRptTotalFooter'),
            'tRptSRCCouponCardNumber' => language('report/report/report', 'tRptSRCCouponCardNumber'),
            'tRptSRCNumber' => language('report/report/report', 'tRptSRCNumber'),
            'tRptSRCBank' => language('report/report/report', 'tRptSRCBank'),
            'tRptTotal' => language('report/report/report', 'tRptTotal'),

            'tRptSRCCash' => language('report/report/report', 'tRptSRCCash'),
            'tRptSRCCredit' => language('report/report/report', 'tRptSRCCredit'),
            'tRptSRCOther' => language('report/report/report', 'tRptSRCOther'),
            'tRptSRCPromtpay' => language('report/report/report', 'tRptSRCPromtpay'),
            'tRptSRCAlipay' => language('report/report/report', 'tRptSRCAlipay'),
            'tRptTaxSalePosTaxId' => language('report/report/report', 'tRptTaxSalePosTaxId'),
            "tRptAdjStkNoData" => language('report/report/report', 'tRptAdjStkNoData'),
            // No Data Report
            'tRptNoData' => language('common/main/main', 'tCMNNotFoundData'),
            'tRptPosTypeName' => language('common/main/main', 'tRptPosTypeName'),
            'tRptPosType' => language('common/main/main', 'tRptPosType'),
            'tRptPosType1' => language('common/main/main', 'tRptPosType1'),
            'tRptPosType2' => language('common/main/main', 'tRptPosType2'),
            'tRptConditionInReport' => language('report/report/report', 'tRptConditionInReport'),

            'tRptAdjMerChantFrom' => language('report/report/report', 'tRptAdjMerChantFrom'),
            'tRptAdjMerChantTo' => language('report/report/report', 'tRptAdjMerChantTo'),
            'tRptAdjShopFrom' => language('report/report/report', 'tRptAdjShopFrom'),
            'tRptAdjShopTo' => language('report/report/report', 'tRptAdjShopTo'),
            'tRptAdjPosFrom' => language('report/report/report', 'tRptAdjPosFrom'),
            'tRptAdjPosTo' => language('report/report/report', 'tRptAdjPosTo'),
            'tRptBranch' => language('report/report/report', 'tRptBranch'),
            'tRptTotal' => language('report/report/report', 'tRptTotal'),
            'tRPCTaxNo' => language('report/report/report', 'tRPCTaxNo'),
            'tRptConditionInReport' => language('report/report/report', 'tRptConditionInReport'),
            'tRptMerFrom' => language('report/report/report', 'tRptMerFrom'),
            'tRptMerTo' => language('report/report/report', 'tRptMerTo'),
            'tRptShopFrom' => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo' => language('report/report/report', 'tRptShopTo'),
            'tRptPosFrom' => language('report/report/report', 'tRptPosFrom'),
            'tRptPosTo' => language('report/report/report', 'tRptPosTo'),
            'tPdtCodeTo' => language('report/report/report', 'tPdtCodeTo'),
            'tPdtCodeFrom' => language('report/report/report', 'tPdtCodeFrom'),
            'tPdtGrpFrom' => language('report/report/report', 'tPdtGrpFrom'),
            'tPdtGrpTo' => language('report/report/report', 'tPdtGrpTo'),
            'tPdtTypeFrom' => language('report/report/report', 'tPdtTypeFrom'),
            'tPdtTypeTo' => language('report/report/report', 'tPdtTypeTo'),
            'tRptAdjWahFrom' => language('report/report/report', 'tRptAdjWahFrom'),
            'tRptAdjWahTo' => language('report/report/report', 'tRptAdjWahTo'),
            'tRptAll' => language('report/report/report', 'tRptAll'),
            'tYear' => language('report/report/report', 'tYear'),

            //table
            'tRptSMPCode' => language('report/report/report', 'tRptSMPCode'),
            'tRptSMPPdtName' => language('report/report/report', 'tRptSMPPdtName'),
            'tRptSMPm1' => language('report/report/report', 'tRptSMPm1'),
            'tRptSMPm2' => language('report/report/report', 'tRptSMPm2'),
            'tRptSMPm3' => language('report/report/report', 'tRptSMPm3'),
            'tRptSMPm4' => language('report/report/report', 'tRptSMPm4'),
            'tRptSMPm5' => language('report/report/report', 'tRptSMPm5'),
            'tRptSMPm6' => language('report/report/report', 'tRptSMPm6'),
            'tRptSMPm7' => language('report/report/report', 'tRptSMPm7'),
            'tRptSMPm8' => language('report/report/report', 'tRptSMPm8'),
            'tRptSMPm9' => language('report/report/report', 'tRptSMPm9'),
            'tRptSMPm10' => language('report/report/report', 'tRptSMPm10'),
            'tRptSMPm11' => language('report/report/report', 'tRptSMPm11'),
            'tRptSMPm12' => language('report/report/report', 'tRptSMPm12'),
            'tRptSMPSum' => language('report/report/report', 'tRptSMPSum'),
            'tRptSMPSumTotal' => language('report/report/report', 'tRptSMPSumTotal'),
            'tRptSMPNoData' => language('report/report/report', 'tRptSMPNoData'),

            'tRptDatePrint' => language('report/report/report', 'tRptDatePrint'),
            'tRptTimePrint' => language('report/report/report', 'tRptTimePrint'),
            'tRptYear' => language('report/report/report', 'tRptYear'),
            'tRptCashierFrom' => language('report/report/report', 'tRptCashierFrom'),
            'tRptCashierTo' => language('report/report/report', 'tRptCashierTo'),

        ];

        $this->tSysBchCode = SYS_BCH_CODE;
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
        $this->nPosType = $this->input->post('ocmPosType');

        // Report Filter
        $this->aRptFilter = [
            'tUserSession' => $this->tUserSessionID,
            'tCompName' => $tFullHost,
            'tRptCode' => $this->tRptCode,
            'nLangID' => $this->nLngID,

            'tTypeSelect' => !empty($this->input->post('ohdTypeDataCondition')) ? $this->input->post('ohdTypeDataCondition') : "",

            // สาขา(Branch)
            'tBchCodeFrom' => !empty($this->input->post('oetRptBchCodeFrom')) ? $this->input->post('oetRptBchCodeFrom') : "",
            'tBchNameFrom' => !empty($this->input->post('oetRptBchNameFrom')) ? $this->input->post('oetRptBchNameFrom') : "",
            'tBchCodeTo' => !empty($this->input->post('oetRptBchCodeTo')) ? $this->input->post('oetRptBchCodeTo') : "",
            'tBchNameTo' => !empty($this->input->post('oetRptBchNameTo')) ? $this->input->post('oetRptBchNameTo') : "",
            'tBchCodeSelect' => !empty($this->input->post('oetRptBchCodeSelect')) ? $this->input->post('oetRptBchCodeSelect') : "",
            'tBchNameSelect' => !empty($this->input->post('oetRptBchNameSelect')) ? $this->input->post('oetRptBchNameSelect') : "",
            'bBchStaSelectAll' => !empty($this->input->post('oetRptBchStaSelectAll')) && ($this->input->post('oetRptBchStaSelectAll') == 1) ? true : false,

            // ร้านค้า(Shop)
            'tShpCodeFrom' => !empty($this->input->post('oetRptShpCodeFrom')) ? $this->input->post('oetRptShpCodeFrom') : "",
            'tShpNameFrom' => !empty($this->input->post('oetRptShpNameFrom')) ? $this->input->post('oetRptShpNameFrom') : "",
            'tShpCodeTo' => !empty($this->input->post('oetRptShpCodeTo')) ? $this->input->post('oetRptShpCodeTo') : "",
            'tShpNameTo' => !empty($this->input->post('oetRptShpNameTo')) ? $this->input->post('oetRptShpNameTo') : "",
            'tShpCodeSelect' => !empty($this->input->post('oetRptShpCodeSelect')) ? $this->input->post('oetRptShpCodeSelect') : "",
            'tShpNameSelect' => !empty($this->input->post('oetRptShpNameSelect')) ? $this->input->post('oetRptShpNameSelect') : "",
            'bShpStaSelectAll' => !empty($this->input->post('oetRptShpStaSelectAll')) && ($this->input->post('oetRptShpStaSelectAll') == 1) ? true : false,

            // Filter Pos (เครื่องจุดขาย)
            'tPosCodeFrom' => (empty($this->input->post('oetRptPosCodeFrom'))) ? '' : $this->input->post('oetRptPosCodeFrom'),
            'tPosNameFrom' => (empty($this->input->post('oetRptPosNameFrom'))) ? '' : $this->input->post('oetRptPosNameFrom'),
            'tPosCodeTo' => (empty($this->input->post('oetRptPosCodeTo'))) ? '' : $this->input->post('oetRptPosCodeTo'),
            'tPosNameTo' => (empty($this->input->post('oetRptPosNameTo'))) ? '' : $this->input->post('oetRptPosNameTo'),
            'tPosCodeSelect' => !empty($this->input->post('oetRptPosCodeSelect')) ? $this->input->post('oetRptPosCodeSelect') : "",
            'tPosNameSelect' => !empty($this->input->post('oetRptPosNameSelect')) ? $this->input->post('oetRptPosNameSelect') : "",
            'bPosStaSelectAll' => !empty($this->input->post('oetRptPosStaSelectAll')) && ($this->input->post('oetRptPosStaSelectAll') == 1) ? true : false,

            //แคชเชียร์
            'tCashierCodeFrom' => (empty($this->input->post('oetRptCashierCodeFrom'))) ? '' : $this->input->post('oetRptCashierCodeFrom'),
            'tCashierNameFrom' => (empty($this->input->post('oetRptCashierNameFrom'))) ? '' : $this->input->post('oetRptCashierNameFrom'),
            'tCashierCodeTo' => (empty($this->input->post('oetRptCashierCodeTo'))) ? '' : $this->input->post('oetRptCashierCodeTo'),
            'tCashierNameTo' => (empty($this->input->post('oetRptCashierNameTo'))) ? '' : $this->input->post('oetRptCashierNameTo'),

            //สินค้า
            'tRptPdtCodeFrom' => !empty($this->input->post('oetRptPdtCodeFrom')) ? $this->input->post('oetRptPdtCodeFrom') : "",
            'tRptPdtNameFrom' => !empty($this->input->post('oetRptPdtNameFrom')) ? $this->input->post('oetRptPdtNameFrom') : "",
            'tRptPdtCodeTo' => !empty($this->input->post('oetRptPdtCodeTo')) ? $this->input->post('oetRptPdtCodeTo') : "",
            'tRptPdtNameTo' => !empty($this->input->post('oetRptPdtNameTo')) ? $this->input->post('oetRptPdtNameTo') : "",

            //ปี
            'tYear' => !empty($this->input->post('oetRptYear')) ? $this->input->post('oetRptYear') : "",
            'nPosType' => !empty($this->input->post('ocmPosType')) ? $this->input->post('ocmPosType') : "",
        ];

        // ดึงข้อมูลบริษัทฯ
        $aCompInfoParams = [
            'nLngID' => $this->nLngID,
            'tBchCode' => $this->tBchCodeLogin,
        ];
        $this->aCompanyInfo = FCNaGetCompanyInfo($aCompInfoParams)['raItems'];
    }

    public function index()
    {

        if (!empty($this->tRptExportType) && !empty($this->tRptCode)) {

            // Execute Stored Procedure
            $this->Rptsalesmonthproduct_model->FSnMExecStoreReport($this->aRptFilter);

            $aDataSwitchCase = array(
                'ptRptRoute' => $this->tRptRoute,
                'ptRptCode' => $this->tRptCode,
                'ptRptTypeExport' => $this->tRptExportType,
                'paDataFilter' => $this->aRptFilter,
            );

            switch ($this->tRptExportType) {
                case 'html':
                    $this->FSvCCallRptViewBeforePrint($aDataSwitchCase);
                    break;
                case 'excel':
                    $this->FSvCCallRptRenderExcel($aDataSwitchCase);
                    break;
                case 'pdf':
                    // $this->FSvCCallRptRenderExcel($aDataMain);
                    break;
            }
        }
    }

    /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 25/2/2020 Nonpawich
     * LastUpdate:
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint()
    {
        // ข้อมูลสำหรับดึงข้อมูลจากฐานข้อมูล
        $aDataWhereRpt = array(
            'nPerPage' => $this->nPerPage,
            'nPage' => '1', // เริ่มทำงานหน้าแรก
            'tCompName' => $this->tCompName,
            'tRptCode' => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter' => $this->aRptFilter,
        );
        $aDataReport = $this->Rptsalesmonthproduct_model->FSaMGetDataReport($aDataWhereRpt);

        // ข้อมูล Render Report
        $aDataViewRpt = array(
            'aDataReport' => $aDataReport,
            'aDataTextRef' => $this->aText,
            'aCompanyInfo' => $this->aCompanyInfo,
            'nOptDecimalShow' => $this->nOptDecimalShow,
            'aDataFilter' => $this->aRptFilter,
        );

        // Load View Advance Table
        $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportsale/rptSalesMonthProduct', 'wRptSalesMonthProductHtml', $aDataViewRpt);
        // Data Viewer Center Report
        $aDataViewer = array(
            'tTitleReport' => $this->aText['tTitleReport'],
            'tRptTypeExport' => $this->tRptExportType,
            'tRptCode' => $this->tRptCode,
            'tRptRoute' => $this->tRptRoute,
            'tViewRenderKool' => $tRptView,
            'aDataFilter' => $this->aRptFilter,
            'aDataReport' => array(
                'raItems' => $aDataReport['aRptData'],
                'rnAllRow' => $aDataReport['aPagination']['nTotalRecord'],
                'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                'rnAllPage' => $aDataReport['aPagination']['nTotalPage'],
                'rtCode' => '1',
                'rtDesc' => 'success',
            ),
        );
        $this->load->view('report/report/wReportViewer', $aDataViewer);
    }

    /**
     * Functionality: Call Rpt Table Kool Report
     * Parameters:  Function Parameter
     * Creator: 04/04/2019 Saharat(Golf)
     * LastUpdate: 24/09/2019 Piya
     * Return: View Kool Report
     * ReturnType: View
     */
    public function FCNvCRenderKoolReportHtml($paDataReport, $paDataFilter)
    {

        // Ref File Kool Report
        require_once APPPATH . 'modules\report\datasources\rptSaleRecive\rptSaleRecive.php';

        // Set Parameter To Report
        $oRptSaleReciveHtml = new rptSaleRecive(array(
            'nCurrentPage' => $paDataReport['rnCurrentPage'],
            'nAllPage' => $paDataReport['rnAllPage'],
            'aCompanyInfo' => $this->aCompanyInfo,
            'aFilterReport' => $this->aRptFilter,
            'aDataTextRef' => $this->aText,
            'aDataReturn' => $paDataReport['raItems'],
        ));

        $oRptSaleReciveHtml->run();
        $tHtmlViewReport = $oRptSaleReciveHtml->render('wRptSaleReciveHtml', true);
        return $tHtmlViewReport;
    }

    /**
     * Functionality: Click Page ดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 20/12/2019 Nonpaiwch(petch)
     * LastUpdate:
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrintClickPage()
    {

        /** =========== Begin Init Variable ================================== */
        $aDataFilter = json_decode($this->input->post('ohdRptDataFilter'), true);
        /** =========== End Init Variable ==================================== */
        // ข้อมูลสำหรับดึงข้อมูลจากฐานข้อมูล
        $aDataWhereRpt = array(
            'nPerPage' => $this->nPerPage,
            'nPage' => $this->nPage,
            'tCompName' => $this->tCompName,
            'tRptCode' => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter' => $this->aRptFilter,
        );
        $aDataReport = $this->Rptsalesmonthproduct_model->FSaMGetDataReport($aDataWhereRpt);

        // ข้อมูล Render Report
        $aDataViewRpt = array(
            'aDataReport' => $aDataReport,
            'aDataTextRef' => $this->aText,
            'aCompanyInfo' => $this->aCompanyInfo,
            'nOptDecimalShow' => $this->nOptDecimalShow,
            'aDataFilter' => $aDataFilter,
        );

        // Load View Advance Tablew
        $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportsale/rptSalesMonthProduct', 'wRptSalesMonthProductHtml', $aDataViewRpt);
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
            ),
        );
        $this->load->view('report/report/wReportViewer', $aDataViewer);
    }

    // Functionality: Function Render Report Excel
    // Parameters:  Function Parameter
    // Creator: 05/08/2019 Wasin(Yoshi)
    // LastUpdate: 22/04/2019 Saharat(Golf)
    // Return: View Report Viewer
    // ReturnType: View
    // private function FSvCCallRptRenderExcel($paDataMain)
    // {
    //     try {
    //         // เตรียมข้อมูลออกรายงาน
    //         $tRptRoute = $paDataMain['ptRptRoute'];
    //         $tRptCode = $paDataMain['ptRptCode'];
    //         $tRptTypeExport = $paDataMain['ptRptTypeExport'];
    //         $aDataFilter = $paDataMain['paDataFilter'];
    //         $nPage = 1;
    //         $nLangEdit = $this->session->userdata("tLangEdit");
    //         $tSesUsername = $this->session->userdata('tSesUsername');
    //         $tUsrSessionID = $this->session->userdata('tSesSessionID');
    //         $dDatePrint = date('Y-m-d');
    //         $dTimePrint = date('H:i:s');

    //         // Check Filter Merchant And
    //         $tAPIReq = "";
    //         $tMethodReq = "GET";
    //         $aDataWhereComp = array('FNLngID' => $nLangEdit);
    //         $aCompData = $this->Company_model->FSaMCMPList($tAPIReq, $tMethodReq, $aDataWhereComp);
    //         if ($aCompData['rtCode'] == '1') {
    //             $tCompName = $aCompData['raItems']['rtCmpName'];
    //             $tBchCode = $aCompData['raItems']['rtCmpBchCode'];
    //             $aDataAddress = $this->Report_model->FSaMRptGetDataAddressByBranchComp($tBchCode, $nLangEdit);
    //         } else {
    //             $tCompName = "-";
    //             $tBchCode = "-";
    //             $aDataAddress = array();
    //         }

    //         // array ข้อมูลภาษาที่เกี่ยวข้องกับรายงาน
    //         $aDataTextRef = array(
    //             'tTitleReport' => language('report/report/report', 'tRptTitleSaleRecive'),
    //             'tRptTaxNo' => language('report/report/report', 'tRptTaxNo'),
    //             'tRptDatePrint' => language('report/report/report', 'tRptDatePrint'),
    //             'tRptDateExport' => language('report/report/report', 'tRptDateExport'),
    //             'tRptTimePrint' => language('report/report/report', 'tRptTimePrint'),
    //             'tRptPrintHtml' => language('report/report/report', 'tRptPrintHtml'),
    //             // Address Lang
    //             'tRptAddrBuilding' => language('report/report/report', 'tRptAddrBuilding'),
    //             'tRptAddrRoad' => language('report/report/report', 'tRptAddrRoad'),
    //             'tRptAddrSoi' => language('report/report/report', 'tRptAddrSoi'),
    //             'tRptAddrSubDistrict' => language('report/report/report', 'tRptAddrSubDistrict'),
    //             'tRptAddrDistrict' => language('report/report/report', 'tRptAddrDistrict'),
    //             'tRptAddrProvince' => language('report/report/report', 'tRptAddrProvince'),
    //             'tRptAddrTel' => language('report/report/report', 'tRptAddrTel'),
    //             'tRptAddrFax' => language('report/report/report', 'tRptAddrFax'),
    //             'tRptAddrBranch' => language('report/report/report', 'tRptAddrBranch'),
    //             'tRptAddV2Desc1' => language('report/report/report', 'tRptAddV2Desc1'),
    //             'tRptAddV2Desc2' => language('report/report/report', 'tRptAddV2Desc2'),
    //             // Filter Heard Report
    //             'tRptBchFrom' => language('report/report/report', 'tRptBchFrom'),
    //             'tRptBchTo' => language('report/report/report', 'tRptBchTo'),
    //             'tRptShopFrom' => language('report/report/report', 'tRptShopFrom'),
    //             'tRptShopTo' => language('report/report/report', 'tRptShopTo'),
    //             'tRptDateFrom' => language('report/report/report', 'tRptDateFrom'),
    //             'tRptDateTo' => language('report/report/report', 'tRptDateTo'),
    //             // Table Report
    //             'tRptBarchCode' => language('report/report/report', 'tRptBarchCode'),
    //             'tRptBarchName' => language('report/report/report', 'tRptBarchName'),
    //             'tRptDocDate' => language('report/report/report', 'tRptDocDate'),
    //             'tRptShopCode' => language('report/report/report', 'tRptShopCode'),
    //             'tRptShopName' => language('report/report/report', 'tRptShopName'),
    //             'tRptAmount' => language('report/report/report', 'tRptAmount'),
    //             'tRptSale' => language('report/report/report', 'tRptSale'),
    //             'tRptCancelSale' => language('report/report/report', 'tRptCancelSale'),
    //             'tRptTotalSale' => language('report/report/report', 'tRptTotalSale'),
    //             'tRptTotalAllSale' => language('report/report/report', 'tRptTotalAllSale'),
    //             'tRptPayby' => language('report/report/report', 'tRptPayby'),
    //             'tRptRcvDocumentCode' => language('report/report/report', 'tRptRcvDocumentCode'),
    //             'tRptDate' => language('report/report/report', 'tRptDate'),
    //             'tRptRcvTotal' => language('report/report/report', 'tRptRcvTotal'),
    //             'tRptTaxSaleLockerTotalSub' => language('report/report/report', 'tRptTaxSaleLockerTotalSub'),
    //             'tRptRcvFrom' => language('report/report/report', 'tRptRcvFrom'),
    //             'tRptRcvTo' => language('report/report/report', 'tRptRcvTo'),
    //             'tRptTaxSaleLockerFilterDocDateFrom' => language('report/report/report', 'tRptTaxSaleLockerFilterDocDateFrom'),
    //             'tRptTaxSaleLockerFilterDocDateTo' => language('report/report/report', 'tRptTaxSaleLockerFilterDocDateTo'),
    //             // No Data Report
    //             'tRptNoData' => language('common/main/main', 'tCMNNotFoundData'),
    //         );

    //         // ข้อมูลสำหรับดึงข้อมูลจากฐานข้อมูล
    //         $aDataWhereRpt = array(
    //             'nPerPage' => 500000,
    //             'nPage' => $nPage,
    //             'tCompName' => $this->tCompName,
    //             'tRptCode' => $tRptCode,
    //             'tUsrSessionID' => $tUsrSessionID,
    //         );
    //         $aDataReport = $this->Rptsalesmonthproduct_model->FSaMGetDataReport($aDataWhereRpt);
    //         // ====================================================================================================================================================================
    //         // ตั้งค่ารายงาน และ หัวข้อรายงาน ==========================================================================================================================================
    //         // ตั้งค่า Font Style
    //         $aReportFontStyle = array('font' => array('name' => 'TH Sarabun New'));
    //         $aStyleRptSizeTitleName = array('font' => array('size' => 14));
    //         $aStyleRptSizeCompName = array('font' => array('size' => 12));
    //         $aStyleRptSizeAddressFont = array('font' => array('size' => 12));
    //         $aStyleRptHeadderTable = array('font' => array('size' => 12, 'color' => array('rgb' => '000000')));
    //         $aStyleRptDataTable = array('font' => array('size' => 10, 'color' => array('rgb' => '000000')));

    //         // Initiate PHPExcel cache
    //         $oCacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
    //         $aCacheSettings = array(' memoryCacheSize ' => '8000MB', 'cacheTime' => 3600 * 120);
    //         PHPExcel_Settings::setCacheStorageMethod($oCacheMethod, $aCacheSettings);

    //         // เริ่ม phpExcel
    //         $objPHPExcel = new PHPExcel();

    //         // A4 ตั้งค่าหน้ากระดาษ
    //         $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

    //         // Set Font Style Text All In Report
    //         $objPHPExcel->getActiveSheet()->getStyle('A1:Z1000')->applyFromArray($aReportFontStyle);

    //         // จัดความกว้างของคอลัมน์
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    //         $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);

    //         // ชื่อหัวรายงาน
    //         $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:I1');
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $aDataTextRef['tTitleReport']);
    //         $objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //         $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($aStyleRptSizeTitleName);

    //         // ====================================================================================================================================================================
    //         // เช็คที่อยู่รายงาน =======================================================================================================================================================
    //         if (isset($aDataAddress) && !empty($aDataAddress)) {
    //             // Company Name
    //             $tRptCompName = (empty($aDataAddress['FTCompName'])) ? '-' : $aDataAddress['FTCompName'];
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $tRptCompName)->getStyle('A2')->applyFromArray($aStyleRptSizeCompName);

    //             // Check Vertion Address
    //             if ($aDataAddress['FTAddVersion'] == 1) {
    //                 // Check Address Line 1
    //                 $tRptAddV1No = (empty($aDataAddress['FTAddV1No'])) ? '-' : $aDataAddress['FTAddV1No'];
    //                 $tRptAddV1Road = (empty($aDataAddress['FTAddV1Road'])) ? '-' : $aDataAddress['FTAddV1Road'];
    //                 $tRptAddV1Soi = (empty($aDataAddress['FTAddV1Soi'])) ? '-' : $aDataAddress['FTAddV1Soi'];
    //                 $tRptAddressLine1 = $tRptAddV1No . ' ' . $aDataTextRef['tRptAddrRoad'] . ' ' . $tRptAddV1Road . ' ' . $aDataTextRef['tRptAddrSoi'] . ' ' . $tRptAddV1Soi;
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $tRptAddressLine1)->getStyle('A3')->applyFromArray($aStyleRptSizeAddressFont);

    //                 // Check Address Line 2
    //                 $tRptAddV1SubDistName = (empty($aDataAddress['FTSudName'])) ? '-' : $aDataAddress['FTSudName'];
    //                 $tRptAddV1DstName = (empty($aDataAddress['FTDstName'])) ? '-' : $aDataAddress['FTDstName'];
    //                 $tRptAddV1PvnName = (empty($aDataAddress['FTPvnName'])) ? '-' : $aDataAddress['FTPvnName'];
    //                 $tRptAddV1PostCode = (empty($aDataAddress['FTAddV1PostCode'])) ? '-' : $aDataAddress['FTAddV1PostCode'];
    //                 $tRptAddressLine2 = $tRptAddV1SubDistName . ' ' . $tRptAddV1DstName . ' ' . $tRptAddV1PvnName . ' ' . $tRptAddV1PostCode;
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $tRptAddressLine2)->getStyle('A4')->applyFromArray($aStyleRptSizeAddressFont);
    //             } else {
    //                 $tRptAddV2Desc1 = (empty($aDataAddress['FTAddV2Desc1'])) ? '-' : $aDataAddress['FTAddV2Desc1'];
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', $tRptAddV2Desc1)->getStyle('A3')->applyFromArray($aStyleRptSizeAddressFont);

    //                 $tRptAddV2Desc2 = (empty($aDataAddress['FTAddV2Desc2'])) ? '-' : $aDataAddress['FTAddV2Desc2'];
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', $tRptAddV2Desc2)->getStyle('A4')->applyFromArray($aStyleRptSizeAddressFont);
    //             }

    //             // Check Data Telephone Number
    //             if (isset($aDataAddress['FTCompTel']) && !empty($aDataAddress['FTCompTel'])) {
    //                 $tRptCompTel = $aDataAddress['FTCompTel'];
    //             } else {
    //                 $tRptCompTel = '-';
    //             }
    //             $tRptCompTelText = $aDataTextRef['tRptAddrTel'] . ' ' . $tRptCompTel;
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A5', $tRptCompTelText)->getStyle('A5')->applyFromArray($aStyleRptSizeAddressFont);

    //             // Check Data Fax Number
    //             if (isset($aDataAddress['FTCompFax']) && !empty($aDatrunaAddress['FTCompFax'])) {
    //                 $tRptCompFax = $aDataAddress['FTCompFax'];
    //             } else {
    //                 $tRptCompFax = '-';
    //             }
    //             $tRptCompFaxText = $aDataTextRef['tRptAddrFax'] . ' ' . $tRptCompFax;
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A6', $tRptCompFaxText)->getStyle('A6')->applyFromArray($aStyleRptSizeAddressFont);
    //         }
    //         // ====================================================================================================================================================================
    //         // ฟิวเตอร์ข้อมูลรายงาน ===================================================================================================================================================
    //         // Row เริ่มต้นของ Filter
    //         $nStartRowFillter = 2;
    //         $tFillterColumLEFT = "D";
    //         $tFillterColumRIGHT = "F";

    //         // Fillter Branch (สาขา)
    //         if (!empty($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeTo'])) {
    //             $tRptFilterBchCodeFrom = $aDataTextRef['tRptBchFrom'] . ' ' . $aDataFilter['tBchNameFrom'];
    //             $tRptFilterBchCodeTo = $aDataTextRef['tRptBchTo'] . ' ' . $aDataFilter['tBchNameTo'];
    //             $tRptTextLeftRightFilter = $tRptFilterBchCodeFrom . ' ' . $tRptFilterBchCodeTo;
    //             $tColumsMergeFilter = $tFillterColumLEFT . $nStartRowFillter . ':' . $tFillterColumRIGHT . $nStartRowFillter;

    //             $objPHPExcel->setActiveSheetIndex(0)->mergeCells($tColumsMergeFilter);
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($tFillterColumLEFT . $nStartRowFillter, $tRptTextLeftRightFilter);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->applyFromArray($aStyleRptSizeAddressFont);
    //             $nStartRowFillter += 1;
    //         }

    //         // Fillter Shop (ร้านค้า)
    //         if (!empty($aDataFilter['tShopCodeFrom']) && !empty($aDataFilter['tShopCodeTo'])) {
    //             $tRptFilterShopCodeFrom = $aDataTextRef['tRptShopFrom'] . ' ' . $aDataFilter['tShopNameFrom'];
    //             $tRptFilterShopCodeTo = $aDataTextRef['tRptShopTo'] . ' ' . $aDataFilter['tShopNameTo'];
    //             $tRptTextLeftRightFilter = $tRptFilterShopCodeFrom . ' ' . $tRptFilterShopCodeTo;
    //             $tColumsMergeFilter = $tFillterColumLEFT . $nStartRowFillter . ':' . $tFillterColumRIGHT . $nStartRowFillter;

    //             $objPHPExcel->setActiveSheetIndex(0)->mergeCells($tColumsMergeFilter);
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($tFillterColumLEFT . $nStartRowFillter, $tRptTextLeftRightFilter);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->applyFromArray($aStyleRptSizeAddressFont);
    //             $nStartRowFillter += 1;
    //         }

    //         // Fillter Rcv (ประเภทชำระเงิน)
    //         if (!empty($aDataFilter['tRcvCodeFrom']) && !empty($aDataFilter['tRcvCodeTo'])) {
    //             $tRptFilterRcvCodeFrom = $aDataTextRef['tRptRcvFrom'] . ' ' . $aDataFilter['tRcvNameFrom'];
    //             $tRptFilterRcvCodeTo = $aDataTextRef['tRptRcvTo'] . ' ' . $aDataFilter['tRcvCodeTo'];
    //             $tRptTextLeftRightFilter = $tRptFilterRcvCodeFrom . ' ' . $tRptFilterRcvCodeTo;
    //             $tColumsMergeFilter = $tFillterColumLEFT . $nStartRowFillter . ':' . $tFillterColumRIGHT . $nStartRowFillter;

    //             $objPHPExcel->setActiveSheetIndex(0)->mergeCells($tColumsMergeFilter);
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($tFillterColumLEFT . $nStartRowFillter, $tRptTextLeftRightFilter);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->applyFromArray($aStyleRptSizeAddressFont);
    //             $nStartRowFillter += 1;
    //         }

    //         // Fillter DocDate (วันที่สร้างเอกสาร)
    //         if (!empty($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateTo'])) {
    //             $tRptFilterDocDateFrom = $aDataTextRef['tRptTaxSaleLockerFilterDocDateFrom'] . ' ' . $aDataFilter['tDocDateFrom'];
    //             $tRptFilterDocDateTo = $aDataTextRef['tRptTaxSaleLockerFilterDocDateTo'] . ' ' . $aDataFilter['tDocDateTo'];
    //             $tRptTextLeftRightFilter = $tRptFilterDocDateFrom . ' ' . $tRptFilterDocDateTo;
    //             $tColumsMergeFilter = $tFillterColumLEFT . $nStartRowFillter . ':' . $tFillterColumRIGHT . $nStartRowFillter;

    //             $objPHPExcel->setActiveSheetIndex(0)->mergeCells($tColumsMergeFilter);
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($tFillterColumLEFT . $nStartRowFillter, $tRptTextLeftRightFilter);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //             $objPHPExcel->getActiveSheet()->getStyle($tFillterColumLEFT . $nStartRowFillter)->applyFromArray($aStyleRptSizeAddressFont);
    //             $nStartRowFillter += 1;
    //         }

    //         // ====================================================================================================================================================================
    //         // ตั้งค่าวันที่เวลาออกรายงาน ===============================================================================================================================================
    //         $tRptDateTimeExportText = $aDataTextRef['tRptDatePrint'] . ' ' . $dDatePrint . ' ' . $aDataTextRef['tRptTimePrint'] . ' ' . $dTimePrint;
    //         $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A7:I7');
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', $tRptDateTimeExportText);
    //         $objPHPExcel->getActiveSheet()->getStyle("A7")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //         $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($aStyleRptSizeAddressFont);
    //         // ====================================================================================================================================================================
    //         // หัวตารางรายงาน =======================================================================================================================================================
    //         // กำหนดจุดเริ่มต้นของแถวหัวตาราง
    //         $nStartRowHeadder = 8;

    //         // กำหนด Style Font ของหัวตาราง
    //         $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowHeadder . ':I' . $nStartRowHeadder)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CFE2F3');
    //         $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowHeadder . ':I' . $nStartRowHeadder)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //         $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowHeadder . ':I' . $nStartRowHeadder)->applyFromArray($aStyleRptHeadderTable);
    //         $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowHeadder . ':I' . $nStartRowHeadder)->applyFromArray(array(
    //             'borders' => array(
    //                 'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
    //                 'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
    //             ),
    //         ));

    //         // กำหนดข้อมูลลงหัวตาราง
    //         $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $nStartRowHeadder . ':B' . $nStartRowHeadder);
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $nStartRowHeadder, $aDataTextRef['tRptPayby']);

    //         $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $nStartRowHeadder . ':E' . $nStartRowHeadder);
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $nStartRowHeadder, $aDataTextRef['tRptRcvDocumentCode']);

    //         $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $nStartRowHeadder . ':G' . $nStartRowHeadder);
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $nStartRowHeadder, $aDataTextRef['tRptDate']);

    //         $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $nStartRowHeadder . ':I' . $nStartRowHeadder);
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $nStartRowHeadder, $aDataTextRef['tRptRcvTotal']);
    //         // ====================================================================================================================================================================
    //         // วนลูปข้อมูลตารางข้อมูล =================================================================================================================================================
    //         // Set Variable Data
    //         $nStartRowData = $nStartRowHeadder + 1;
    //         if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) {
    //             // ******* Step 1 ประกาศตัวแปรใช้ในการเช็คเงื่อนไข
    //             $tGrouppingData = "";
    //             $nGroupMember = "";
    //             $nRowPartID = "";

    //             $nSumFooterFCXrcNet = 0;
    //             foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
    //                 // ******* Step 3 Set ค่า Value FNRptGroupMember And FNRowPartID
    //                 $nRowPartID = $aValue["FNRowPartID"];
    //                 $nGroupMember = $aValue["FNRptGroupMember"];

    //                 // ******* Step 4 : Check Groupping Create Row Groupping
    //                 if ($tGrouppingData != $aValue['FTRcvCode']) {
    //                     $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $nStartRowData . ':I' . $nStartRowData);
    //                     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $nStartRowData, $aValue['FTRcvName']);
    //                     $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData . ':I' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //                     $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->applyFromArray($aStyleRptDataTable);
    //                     $nStartRowData++;
    //                 }

    //                 // ******* Step 5 : Loop Set Data Value
    //                 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $nStartRowData . ':B' . $nStartRowData);
    //                 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $nStartRowData . ':E' . $nStartRowData);
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $nStartRowData, $aValue['FTXshDocNo']);
    //                 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $nStartRowData . ':G' . $nStartRowData);
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $nStartRowData, $aValue['FDXrcRefDate']);
    //                 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $nStartRowData . ':I' . $nStartRowData);
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $nStartRowData, number_format($aValue['FCXrcNet'], 2));

    //                 $objPHPExcel->getActiveSheet()->getStyle('H' . $nStartRowData)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    //                 $objPHPExcel->getActiveSheet()->getStyle('C' . $nStartRowData . ':G' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //                 $objPHPExcel->getActiveSheet()->getStyle('H' . $nStartRowData . ':I' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    //                 // ******* Step 6 : เตรียม Parameter สำหรับ Summary Sub Footer
    //                 if ($nRowPartID == $nGroupMember) {
    //                     $nStartRowData++;
    //                     $nSubSumFCXrcNet = $aValue["FCXrcNet_SubTotal"];
    //                     // Set Color Row Sub Footer
    //                     $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData . ':I' . $nStartRowData)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CFE2F3');
    //                     $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData . ':I' . $nStartRowData)->applyFromArray(array(
    //                         'borders' => array(
    //                             'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
    //                         ),
    //                     ));

    //                     // Text Sum Sub
    //                     $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $nStartRowData . ':G' . $nStartRowData);
    //                     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $nStartRowData, $aDataTextRef['tRptTaxSaleLockerTotalSub'] . $aValue['FTRcvName']);
    //                     $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //                     $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->applyFromArray($aStyleRptDataTable);
    //                     $objPHPExcel->getActiveSheet()->getStyle('H' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    //                     // Value Sum Sub
    //                     $objPHPExcel->setActiveSheetIndex(0)
    //                         ->setCellValue('H' . $nStartRowData, number_format($nSubSumFCXrcNet, 2));

    //                     $objPHPExcel->getActiveSheet()->getStyle('H' . $nStartRowData)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    //                     $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $nStartRowData . ':I' . $nStartRowData);
    //                     $objPHPExcel->getActiveSheet()->getStyle('H' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //                 }

    //                 $nSumFooterFCXrcNet = number_format($aValue["FCXrcNet_Footer"], 2);

    //                 // ******* Step 2 Set ค่า Value ให้กับตัวแปร
    //                 $tGrouppingData = $aValue["FTRcvCode"];
    //                 $nStartRowData++;
    //             }

    //             // Step 7 : Set Footer Text
    //             $nPageNo = $aDataReport["aPagination"]["nDisplayPage"];
    //             $nTotalPage = $aDataReport["aPagination"]["nTotalPage"];
    //             if ($nPageNo == $nTotalPage) {
    //                 // Set Color Row Sub Footer
    //                 $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData . ':I' . $nStartRowData)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CFE2F3');
    //                 $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData . ':I' . $nStartRowData)->applyFromArray(array(
    //                     'borders' => array(
    //                         'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
    //                         'bottom' => array('style' => PHPExcel_Style_Border::BORDER_DOUBLE, 'color' => array('rgb' => '000000')),
    //                     ),
    //                 ));

    //                 // Text Sum Footer
    //                 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $nStartRowData . ':G' . $nStartRowData);
    //                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $nStartRowData, 'รวมทั้งสิ้น');
    //                 $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //                 $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->applyFromArray($aStyleRptDataTable);

    //                 // Value Sum Footer
    //                 $objPHPExcel->setActiveSheetIndex(0)
    //                     ->setCellValue('H' . $nStartRowData, $nSumFooterFCXrcNet);
    //                 $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $nStartRowData . ':I' . $nStartRowData);
    //                 $objPHPExcel->getActiveSheet()->getStyle('H' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //                 $objPHPExcel->getActiveSheet()->getStyle('H1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    //             }
    //         } else {
    //             $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $nStartRowData . ':I' . $nStartRowData);
    //             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $nStartRowData, $aDataTextRef['tRptNoData']);
    //             $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //             $objPHPExcel->getActiveSheet()->getStyle('A' . $nStartRowData)->applyFromArray($aStyleRptDataTable);
    //         }
    //         // ====================================================================================================================================================================
    //         // ตั่งค่า Content Type Export File Excel ================================================================================================================================
    //         $tFilename = 'Report-' . $tRptCode . date("dmYhis") . '.xlsx';

    //         header("Pragma: public");
    //         header("Expires: 0");
    //         header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    //         header("Content-Type: application/force-download");
    //         header("Content-Type: application/octet-stream");
    //         header("Content-Type: application/download");

    //         header("Content-Disposition: attachment;filename=$tFilename");
    //         header("Content-Transfer-Encoding: binary");

    //         $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

    //         if (!is_dir(APPPATH . 'modules/report/assets/exportexcel/')) {
    //             mkdir(APPPATH . 'modules/report/assets/exportexcel/');
    //         }

    //         if (!is_dir(APPPATH . 'modules/report/assets/exportexcel/' . $tRptCode)) {
    //             mkdir(APPPATH . 'modules/report/assets/exportexcel/' . $tRptCode);
    //         }

    //         if (!is_dir(APPPATH . 'modules/report/assets/exportexcel/' . $tRptCode . '/' . $tSesUsername)) {
    //             mkdir(APPPATH . 'modules/report/assets/exportexcel/' . $tRptCode . '/' . $tSesUsername);
    //         }

    //         $tPathExport = APPPATH . 'modules/report/assets/exportexcel/' . $tRptCode . '/' . $tSesUsername . '/';
    //         $oFiles = glob($tPathExport . '*');
    //         foreach ($oFiles as $tFile) {
    //             if (is_file($tFile)) {
    //                 unlink($tFile);
    //             }

    //         }

    //         // Check Status Save File Excel
    //         $objWriter->save($tPathExport . $tFilename);
    //         $aResponse = array(
    //             'nStaExport' => 1,
    //             'tFileName' => $tFilename,
    //             'tPathFolder' => 'application/modules/report/assets/exportexcel/' . $tRptCode . '/' . $tSesUsername . '/',
    //             'tMessage' => "Export File Successfully.",
    //         );
    //         // ====================================================================================================================================================================
    //     } catch (Exception $Error) {
    //         $aResponse = array(
    //             'nStaExport' => 500,
    //             'tMessage' => $Error->getMessage(),
    //         );
    //     }
    //     echo json_encode($aResponse);
    // }

    /**
     * Functionality: Click Page Report (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 16/08/2019 Wasin(Yoshi)
     * LastUpdate: 24/09/2019 Piya
     * Return: object Status Count Data Report
     * ReturnType: Object
     */
    public function FSoCChkDataReportInTableTemp($paDataSwitchCase)
    {
        try {
            $aDataCountData = [
                'tCompName' => $paDataSwitchCase['paDataFilter']['tCompName'],
                'tRptCode' => $paDataSwitchCase['paDataFilter']['tRptCode'],
                'tUserSession' => $paDataSwitchCase['paDataFilter']['tUserSession'],
            ];

            $nDataCountPage = $this->Rptsalesmonthproduct_model->FSnMCountDataReportAll($aDataCountData);

            $aResponse = array(
                'nCountPageAll' => $nDataCountPage,
                'nStaEvent' => 1,
                'tMessage' => 'Success Count Data All',
            );
        } catch (ErrorException $Error) {
            $aResponse = array(
                'nStaEvent' => 500,
                'tMessage' => $Error->getMessage(),
            );
        }
        echo json_encode($aResponse);
    }

    /**
     * Functionality: Send Rabbit MQ Report
     * Parameters:  Function Parameter
     * Creator: 10/10/2019 Piya
     * LastUpdate: -
     * Return: object Send Rabbit MQ Report
     * ReturnType: Object
     */
    public function FSvCCallRptExportFile() {
        try{

            $tRptGrpCode        = $this->tRptGroup;
            $tRptCode           = $this->tRptCode;
            $tUserCode          = $this->tUserLoginCode;
            $tSessionID         = $this->tUserSessionID;
            $nLangID            = FCNaHGetLangEdit();
            $tRptExportType     = $this->tRptExportType;
            $tCompName          = $this->tCompName;
            $dDateSendMQ        = date('Y-m-d');
            $dTimeSendMQ        = date('H:i:s');
            $dDateSubscribe     = date('Ymd');
            $dTimeSubscribe     = date('His');

            // Set Parameter Send MQ
            $tRptQueueName = 'RPT_' .$this->tSysBchCode . '_' . $this->tRptGroup . '_' . $this->tRptCode;

            $aDataSendMQ = [
                'tQueueName' => $tRptQueueName,
                'aParams' => [
                    'ptRptCode'         => $tRptCode,
                    'pnPerFile'         => 20000,
                    'ptUserCode'        => $tUserCode,
                    'ptUserSessionID'   => $tSessionID,
                    'pnLngID'           => $nLangID,
                    'ptFilter'          => $this->aRptFilter,
                    'ptRptExpType'      => $tRptExportType,
                    'ptComName'         => $tCompName,
                    'ptDate'            => $dDateSendMQ,
                    'ptTime'            => $dTimeSendMQ,
                    'ptBchCode'         => (!empty($this->session->userdata('tSesUsrBchCom')) ? $this->session->userdata('tSesUsrBchCom') : $this->session->userdata('tSesUsrBchCom'))
                ]
            ];

            FCNxReportCallRabbitMQ($aDataSendMQ);

            $aResponse = array(
                'nStaEvent' => 1,
                'tMessage' => 'Success Send Rabbit MQ.',
                'aDataSubscribe' => array(
                    'ptSysBchCode' => $this->tSysBchCode,
                    'ptComName' => $tCompName,
                    'ptRptCode' => $tRptCode,
                    'ptUserCode' => $tUserCode,
                    'ptUserSessionID' => $tSessionID,
                    'pdDateSubscribe' => $dDateSubscribe,
                    'pdTimeSubscribe' => $dTimeSubscribe,
                )
            );

        }catch(Exception $Error){
            $aResponse = array(
                'nStaEvent' => 500,
                'tMessage' => $Error->getMessage()
            );
        }

        echo json_encode($aResponse);
    }

    /**
     * Functionality: Render Excel Report
     * Parameters:  Function Parameter
     * Creator: 25/09/2020 Sooksanti
     * LastUpdate:
     * Return: file
     * ReturnType: file
     */
    public function FSvCCallRptRenderExcel()
    {
        ini_set('memory_limit','-1');
        $tFileName = 'Rpt-'.$this->aText['tTitleReport'] . '_' . date('YmdHis') . '.xlsx';

        $oWriter = WriterEntityFactory::createXLSXWriter();

        $oWriter->openToBrowser($tFileName); // stream data directly to the browser

        $aMulltiRow = $this->FSoCCallRptRenderHedaerExcel(); //เรียกฟังชั่นสร้างส่วนหัวรายงาน
        $oWriter->addRows($aMulltiRow);

        $oBorder = (new BorderBuilder())
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
            ->build();

        $oStyleColums = (new StyleBuilder())
            ->setBorder($oBorder)
            ->build();

        $oBorder = (new BorderBuilder())
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
            ->build();

        $oStyleBold = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->build();

        $aCells = [
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPCode'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPPdtName'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm1'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm2'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm3'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm4'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm5'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm6'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm7'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm8'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm9'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm10'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm11'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPm12'), $oStyleBold),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSMPSum'), $oStyleBold),
            WriterEntityFactory::createCell(null),
        ];

        /** add a row at a time */
        $singleRow = WriterEntityFactory::createRow($aCells, $oStyleColums);
        $oWriter->addRow($singleRow);

        $aGetDataReportParams = array(
            'nPerPage' => 999999999999,
            'nPage' => '1', // เริ่มทำงานหน้าแรก
            'tCompName' => $this->tCompName,
            'tRptCode' => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter' => $this->aRptFilter,
        );
        $aDataReport = $this->Rptsalesmonthproduct_model->FSaMGetDataReport($aGetDataReportParams);
        // /** Create a style with the StyleBuilder */
        $oStyle = (new StyleBuilder())
            ->setCellAlignment(CellAlignment::RIGHT)
            ->build();

        if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) {
            foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
                $values = [
                    WriterEntityFactory::createCell($aValue['FTPdtCode']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTPdtName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty01'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty02'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty03'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty04'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty05'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty06'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty07'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty08'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty09'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty10'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty11'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQty12'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQtyTotal'])),
                    WriterEntityFactory::createCell(null),
                ];
                $aRow = WriterEntityFactory::createRow($values);
                $oWriter->addRow($aRow);

                if (($nKey + 1) == count($aDataReport['aRptData'])) { //SumFooter
                    $values = [
                        WriterEntityFactory::createCell($this->aText['tRptTotalFooter'],$oStyleBold),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXsdQtyTotal_Footer'])),
                        WriterEntityFactory::createCell(null),
                    ];
                    $aRow = WriterEntityFactory::createRow($values, $oStyleColums);
                    $oWriter->addRow($aRow);
                }
            }
        }

        $aMulltiRow = $this->FSoCCallRptRenderFooterExcel(); //เรียกฟังชั่นสร้างส่วนท้ายรายงาน
        $oWriter->addRows($aMulltiRow);

        $oWriter->close();
    }

    /**
     * Functionality: Render Excel Report Header
     * Parameters:  Function Parameter
     * Creator: 25/09/2020 Sooksanti
     * LastUpdate:
     * Return: oject
     * ReturnType: oject
     */
    public function FSoCCallRptRenderHedaerExcel()
    {
        $oStyle = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(12)
            ->build();

        $aCells = [
            WriterEntityFactory::createCell($this->aCompanyInfo['FTCmpName']),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell($this->aText['tTitleReport']),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyle);

        $tAddress = '';
        if (isset($this->aCompanyInfo) && !empty($this->aCompanyInfo)) {
            if ($this->aCompanyInfo['FTAddVersion'] == '1') {
                $tAddress = $this->aCompanyInfo['FTAddV1No'] . ' ' . $this->aCompanyInfo['FTAddV1Road'] . ' ' . $this->aCompanyInfo['FTAddV1Soi'] . ' ' . $this->aCompanyInfo['FTSudName'] . ' ' . $this->aCompanyInfo['FTDstName'] . ' ' . $this->aCompanyInfo['FTPvnName'] . ' ' . $this->aCompanyInfo['FTAddV1PostCode'];
            }
            if ($this->aCompanyInfo['FTAddVersion'] == '2') {
                $tAddress = $this->aCompanyInfo['FTAddV2Desc1'] . ' ' . $this->aCompanyInfo['FTAddV2Desc2'];
            }
        }

        $aCells = [
            WriterEntityFactory::createCell($tAddress),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptAddrTel'] . ' ' . $this->aCompanyInfo['FTCmpTel']),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptAddrBranch'] . ' ' . $this->aCompanyInfo['FTBchName']),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptTaxSalePosTaxId'] . ' ' . $this->aCompanyInfo['FTAddTaxNo']),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        if ((isset($this->aRptFilter['tDocDateFrom']) && !empty($this->aRptFilter['tDocDateFrom'])) && (isset($this->aRptFilter['tDocDateTo']) && !empty($this->aRptFilter['tDocDateTo']))) {
            $aCells = [
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell($this->aText['tRptDateFrom'] . ' ' . date('d/m/Y', strtotime($this->aRptFilter['tDocDateFrom'])) . ' ' . $this->aText['tRptDateTo'] . ' ' . date('d/m/Y', strtotime($this->aRptFilter['tDocDateTo']))),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        $aCells = [
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell($this->aText['tRptDatePrint'] . ' ' . date('d/m/Y') . ' ' . $this->aText['tRptTimePrint'] . ' ' . date('H:i:s')),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        return $aMulltiRow;
    }

    /**
     * Functionality: Render Excel Report Footer
     * Parameters:  Function Parameter
     * Creator: 25/09/2020 Sooksanti
     * LastUpdate:
     * Return: oject
     * ReturnType: oject
     */
    public function FSoCCallRptRenderFooterExcel()
    {

        $oStyleFilter = (new StyleBuilder())
            ->setFontBold()
            ->build();
        $aCells = [
            WriterEntityFactory::createCell(null),
        ];
        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        $aCells = [
            WriterEntityFactory::createCell($this->aText['tRptConditionInReport']),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
        ];
        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyleFilter);

        // สาขา แบบเลือก
        if (!empty($this->aRptFilter['tBchCodeSelect'])) {
            $tBchSelectText = ($this->aRptFilter['bBchStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tBchNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptBchFrom'] . ' : ' . $tBchSelectText),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // ร้านค้า แบบเลือก
        if (!empty($this->aRptFilter['tShpCodeSelect'])) {
            $tShpSelectText = ($this->aRptFilter['bShpStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tShpNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptShopFrom'] . ' : ' . $tShpSelectText),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // เครื่องจุดขาย (Pos) แบบเลือก
        if (!empty($this->aRptFilter['tPosCodeSelect'])) {
            $tPosSelectText = ($this->aRptFilter['bPosStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tPosNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptPosFrom'] . ' : ' . $tPosSelectText),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Fillter Shop (ร้านค้า)  แบบช่วง
        if (!empty($this->aRptFilter['tShpCodeFrom']) && !empty($this->aRptFilter['tShpCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptShopFrom'] . ' : ' . $this->aRptFilter['tShpNameFrom'] . '     ' . $this->aText['tRptShopTo'] . ' : ' . $this->aRptFilter['tShpNameTo']),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // เครื่องจุดขาย แบบช่วง
        if (!empty($this->aRptFilter['tPosCodeFrom']) && !empty($this->aRptFilter['tPosCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptPosFrom'] . '  ' . $this->aRptFilter['tPosNameFrom'] . '     ' . $this->aText['tRptPosTo'] . ' ' . $this->aRptFilter['tPosNameTo']),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // ฟิวเตอร์ข้อมูล ประเภทจุดขาย
        if (isset($this->aRptFilter['tPosType'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptPosTypeName'] . ' : ' . $this->aText['tRptPosType' . $this->aRptFilter['tPosType']]),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        // Fillter ปี
        if (!empty($this->aRptFilter['tYear']) && !empty($this->aRptFilter['tYear'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptYear'] . '' . $this->aRptFilter['tYear']),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        if (!empty($this->aRptFilter['tCashierCodeFrom']) && !empty($this->aRptFilter['tCashierCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptCashierFrom'] . ' : ' . $this->aRptFilter['tCashierNameFrom'] . '     ' . $this->aText['tRptCashierTo'] . ' : ' . $this->aRptFilter['tCashierNameTo']),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }

        if (!empty($this->aRptFilter['tRptPdtCodeFrom']) && !empty($this->aRptFilter['tRptPdtCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tPdtCodeFrom'] . ' : ' . $this->aRptFilter['tRptPdtNameFrom'] . '     ' . $this->aText['tPdtCodeTo'] . ' : ' . $this->aRptFilter['tRptPdtNameTo']),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
                WriterEntityFactory::createCell(null),
            ];
            $aMulltiRow[] = WriterEntityFactory::createRow($aCells);
        }


        return $aMulltiRow;

    }
}
