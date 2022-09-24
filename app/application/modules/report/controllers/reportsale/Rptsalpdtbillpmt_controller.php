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
class Rptsalpdtbillpmt_controller extends MX_Controller
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
        $this->load->model('report/reportsale/Rptsalpdtbillpmt_model');

        // Init Report
        $this->init();

        parent::__construct();
    }

    private function init()
    {
        $this->aText = [
            // Title
            'tTitleReport' => language('report/report/report', 'tRptSalPdtBillPmt'),
            'tDatePrint' => language('report/report/report', 'tRptTaxSalePosDatePrint'),
            'tTimePrint' => language('report/report/report', 'tRptTaxSalePosTimePrint'),

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
            'tRptTaxSalePosTel' => language('report/report/report', 'tRptTaxSalePosTel'),
            'tRptTaxSalePosFax' => language('report/report/report', 'tRptTaxSalePosFax'),
            'tRptTaxSalePosBch' => language('report/report/report', 'tRptTaxSalePosBch'),
            'tRptTaxSalePosTaxId' => language('report/report/report', 'tRptTaxSalePosTaxId'),

            // Filter Text Label
            'tRptTaxSalePosFilterBchFrom' => language('report/report/report', 'tRptTaxSalePosFilterBchFrom'),
            'tRptTaxSalePosFilterBchTo' => language('report/report/report', 'tRptTaxSalePosFilterBchTo'),
            'tRptTaxSalePosFilterShopFrom' => language('report/report/report', 'tRptTaxSalePosFilterShopFrom'),
            'tRptTaxSalePosFilterShopTo' => language('report/report/report', 'tRptTaxSalePosFilterShopTo'),
            'tRptTaxSalePosFilterPosFrom' => language('report/report/report', 'tRptTaxSalePosFilterPosFrom'),
            'tRptTaxSalePosFilterPosTo' => language('report/report/report', 'tRptTaxSalePosFilterPosTo'),
            'tRptTaxSalePosFilterPayTypeFrom' => language('report/report/report', 'tRptTaxSalePosFilterPayTypeFrom'),
            'tRptTaxSalePosFilterPayTypeTo' => language('report/report/report', 'tRptTaxSalePosFilterPayTypeTo'),
            'tRptTaxSalePosFilterDocDateFrom' => language('report/report/report', 'tRptTaxSalePosFilterDocDateFrom'),
            'tRptTaxSalePosFilterDocDateTo' => language('report/report/report', 'tRptTaxSalePosFilterDocDateTo'),
            'tRptTaxSalePosTaxId' => language('report/report/report', 'tRptTaxSalePosTaxId'),

            'tRptDateFrom' => language('report/report/report', 'tRptDateFrom'),
            'tRptDateTo' => language('report/report/report', 'tRptDateTo'),
            'tRptBchFrom' => language('report/report/report', 'tRptBchFrom'),
            'tRptBchTo' => language('report/report/report', 'tRptBchTo'),
            'tRptMerFrom' => language('report/report/report', 'tRptMerFrom'),
            'tRptMerTo' => language('report/report/report', 'tRptMerTo'),
            'tRptShopFrom' => language('report/report/report', 'tRptShopFrom'),
            'tRptShopTo' => language('report/report/report', 'tRptShopTo'),
            'tRptPosFrom' => language('report/report/report', 'tRptPosFrom'),
            'tRptPosTo' => language('report/report/report', 'tRptPosTo'),
            'tPdtCodeFrom' => language('report/report/report', 'tPdtCodeFrom'),
            'tPdtCodeTo' => language('report/report/report', 'tPdtCodeTo'),
            'tPdtGrpFrom' => language('report/report/report', 'tPdtGrpFrom'),
            'tPdtGrpTo' => language('report/report/report', 'tPdtGrpTo'),
            'tPdtTypeFrom' => language('report/report/report', 'tPdtTypeFrom'),
            'tPdtTypeTo' => language('report/report/report', 'tPdtTypeTo'),

            'tRptTotalFooter' => language('report/report/report', 'tRptTotalFooter'),
            'tRptTaxSalePosNoData' => language('common/main/main', 'tCMNNotFoundData'),
            'tRptConditionInReport' => language('report/report/report', 'tRptConditionInReport'),

            'tRptDocSale' => language('report/report/report', 'tRptDocSale'),
            'tRptDocReturn' => language('report/report/report', 'tRptDocReturn'),
            'tRptPosType' => language('report/report/report', 'tRptPosType'),
            'tRptDatePrint' => language('report/report/report', 'tRptDatePrint'),
            'tRptAll' => language('report/report/report', 'tRptAll'),
            'tRptAdjShopFrom' => language('report/report/report', 'tRptAdjShopFrom'),
            'tRptCstFrom' => language('report/report/report', 'tRptCstFrom'),
            'tRptCstTo' => language('report/report/report', 'tRptCstTo'),

            'tRptFilterChannelFrom' => language('report/report/report', 'tRptFilterChannelFrom'),
            'tRptFilterChannelTo'   => language('report/report/report', 'tRptFilterChannelTo'),
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

        // Report Filter
        $this->aRptFilter = [
            'tUserSession' => $this->tUserSessionID,
            'tCompName' => $tFullHost,
            'tRptCode' => $this->tRptCode,
            'nLangID' => $this->nLngID,

            'tTypeSelect' => !empty($this->input->post('ohdTypeDataCondition')) ? $this->input->post('ohdTypeDataCondition') : "",

            // Filter Branch (สาขา)
            'tBchCodeFrom' => !empty($this->input->post('oetRptBchCodeFrom')) ? $this->input->post('oetRptBchCodeFrom') : "",
            'tBchNameFrom' => !empty($this->input->post('oetRptBchNameFrom')) ? $this->input->post('oetRptBchNameFrom') : "",
            'tBchCodeTo' => !empty($this->input->post('oetRptBchCodeTo')) ? $this->input->post('oetRptBchCodeTo') : "",
            'tBchNameTo' => !empty($this->input->post('oetRptBchNameTo')) ? $this->input->post('oetRptBchNameTo') : "",
            'tBchCodeSelect' => !empty($this->input->post('oetRptBchCodeSelect')) ? $this->input->post('oetRptBchCodeSelect') : "",
            'tBchNameSelect' => !empty($this->input->post('oetRptBchNameSelect')) ? $this->input->post('oetRptBchNameSelect') : "",
            'bBchStaSelectAll' => !empty($this->input->post('oetRptBchStaSelectAll')) && ($this->input->post('oetRptBchStaSelectAll') == 1) ? true : false,

            // Filter Merchant (กลุ่มธุรกิจ)
            'tMerCodeFrom' => (empty($this->input->post('oetRptMerCodeFrom'))) ? '' : $this->input->post('oetRptMerCodeFrom'),
            'tMerNameFrom' => (empty($this->input->post('oetRptMerNameFrom'))) ? '' : $this->input->post('oetRptMerNameFrom'),
            'tMerCodeTo' => (empty($this->input->post('oetRptMerCodeTo'))) ? '' : $this->input->post('oetRptMerCodeTo'),
            'tMerNameTo' => (empty($this->input->post('oetRptMerNameTo'))) ? '' : $this->input->post('oetRptMerNameTo'),
            'tMerCodeSelect' => !empty($this->input->post('oetRptMerCodeSelect')) ? $this->input->post('oetRptMerCodeSelect') : "",
            'tMerNameSelect' => !empty($this->input->post('oetRptMerNameSelect')) ? $this->input->post('oetRptMerNameSelect') : "",
            'bMerStaSelectAll' => !empty($this->input->post('oetRptMerStaSelectAll')) && ($this->input->post('oetRptMerStaSelectAll') == 1) ? true : false,

            // Filter Shop (ร้านค้า)
            'tShpCodeFrom' => (empty($this->input->post('oetRptShpCodeFrom'))) ? '' : $this->input->post('oetRptShpCodeFrom'),
            'tShpNameFrom' => (empty($this->input->post('oetRptShpNameFrom'))) ? '' : $this->input->post('oetRptShpNameFrom'),
            'tShpCodeTo' => (empty($this->input->post('oetRptShpCodeTo'))) ? '' : $this->input->post('oetRptShpCodeTo'),
            'tShpNameTo' => (empty($this->input->post('oetRptShpNameTo'))) ? '' : $this->input->post('oetRptShpNameTo'),
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

            // สินค้า
            'tRptPdtCodeFrom' => !empty($this->input->post('oetRptPdtCodeFrom')) ? $this->input->post('oetRptPdtCodeFrom') : "",
            'tRptPdtNameFrom' => !empty($this->input->post('oetRptPdtNameFrom')) ? $this->input->post('oetRptPdtNameFrom') : "",
            'tRptPdtCodeTo' => !empty($this->input->post('oetRptPdtCodeTo')) ? $this->input->post('oetRptPdtCodeTo') : "",
            'tRptPdtNameTo' => !empty($this->input->post('oetRptPdtNameTo')) ? $this->input->post('oetRptPdtNameTo') : "",
            'tPdtCodeSelect' => !empty($this->input->post('oetRptPdtCodeSelect')) ? $this->input->post('oetRptPdtCodeSelect') : "",
            'tPdtNameSelect' => !empty($this->input->post('oetRptPdtNameSelect')) ? $this->input->post('oetRptPdtNameSelect') : "",
            'bPdtStaSelectAll' => !empty($this->input->post('oetRptPdtStaSelectAll')) && ($this->input->post('oetRptPdtStaSelectAll') == 1) ? true : false,

            // ลูกค้า
            'tCstCodeFrom'      => !empty($this->input->post('oetRptCstCodeFrom'))  ? $this->input->post('oetRptCstCodeFrom')   : "",
            'tCstNameFrom'      => !empty($this->input->post('oetRptCstNameFrom'))  ? $this->input->post('oetRptCstNameFrom')   : "",
            'tCstCodeTo'        => !empty($this->input->post('oetRptCstCodeTo'))    ? $this->input->post('oetRptCstCodeTo')   : "",
            'tCstNameTo'        => !empty($this->input->post('oetRptCstNameTo'))    ? $this->input->post('oetRptCstNameTo')   : "",

            // คลังสินค้า(Warehouse)
            'tWahCodeFrom' => !empty($this->input->post('oetRptWahCodeFrom')) ? $this->input->post('oetRptWahCodeFrom') : "",
            'tWahCodeTo' => !empty($this->input->post('oetRptWahCodeTo')) ? $this->input->post('oetRptWahCodeTo') : "",
            'tWahNameFrom' => !empty($this->input->post('oetRptWahNameFrom')) ? $this->input->post('oetRptWahNameFrom') : "",
            'tWahNameTo' => !empty($this->input->post('oetRptWahNameTo')) ? $this->input->post('oetRptWahNameTo') : "",
            'tWahCodeSelect' => !empty($this->input->post('oetRptWahCodeSelect')) ? $this->input->post('oetRptWahCodeSelect') : "",
            'tWahNameSelect' => !empty($this->input->post('oetRptWahNameSelect')) ? $this->input->post('oetRptWahNameSelect') : "",
            'bWahStaSelectAll' => !empty($this->input->post('oetRptWahStaSelectAll')) && ($this->input->post('oetRptWahStaSelectAll') == 1) ? true : false,

            // วันที่เอกสาร(DocNo)
            'tDocDateFrom'  => !empty($this->input->post('oetRptDocDateFrom')) ? $this->input->post('oetRptDocDateFrom') : "",
            'tDocDateTo'    => !empty($this->input->post('oetRptDocDateTo')) ? $this->input->post('oetRptDocDateTo') : "",

            // ช่องทางการขาย
            'tChannelCodeFrom'  => !empty($this->input->post('oetRptChannelCodeFrom')) ? $this->input->post('oetRptChannelCodeFrom') : "",
            'tChannelNameFrom'  => !empty($this->input->post('oetRptChannelNameFrom')) ? $this->input->post('oetRptChannelNameFrom') : "",
            'tChannelCodeTo'    => !empty($this->input->post('oetRptChannelCodeTo')) ? $this->input->post('oetRptChannelCodeTo') : "",
            'tChannelNameTo'    => !empty($this->input->post('oetRptChannelNameTo')) ? $this->input->post('oetRptChannelNameTo') : "",
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
            $this->Rptsalpdtbillpmt_model->FSnMExecStoreReport($this->aRptFilter);

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
            }
        }
    }

    /**
     * Functionality: ฟังก์ชั่นดูตัวอย่างก่อนพิมพ์ (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 10/11/2019 Sooksanti(Non)
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrint($paDataSwitchCase)
    {

        // ข้อมูลสำหรับดึงข้อมูลจากฐานข้อมูล
        $aDataWhereRpt = array(
            'nPerPage' => $this->nPerPage,
            'nPage' => 1, // เริ่มรายงานหน้าแรก
            'tCompName' => $this->tCompName,
            'tRptCode' => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
        );
        $aDataReport = $this->Rptsalpdtbillpmt_model->FSaMGetDataReport($aDataWhereRpt);

        // print_r($aDataReport);
        // exit();
        // ข้อมูล Render Report
        $aDataViewRptParams = [
            'nOptDecimalShow' => $this->nOptDecimalShow,
            'aCompanyInfo' => $this->aCompanyInfo,
            'aDataReport' => $aDataReport,
            'aDataTextRef' => $this->aText,
            'aDataFilter' => $this->aRptFilter,
        ];

        // Load View Advance Table
        $tRptView = JCNoHLoadViewAdvanceTable('report/datasources/reportsale/rptSalPdtBillPmt', 'wRptSalPdtBillPmtHtml', $aDataViewRptParams);

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

            ],
        ];
        $this->load->view('report/report/wReportViewer', $aDataViewerParams);
    }

    /**
     * Functionality: Click Page Report (Report Viewer)
     * Parameters:  Function Parameter
     * Creator: 19/10/2020 Sooksanti(Non)
     * LastUpdate: -
     * Return: View Report Viewer
     * ReturnType: View
     */
    public function FSvCCallRptViewBeforePrintClickPage()
    {

        /** =========== Begin Init Variable ================================== */
        $aDataFilter = json_decode($this->input->post('ohdRptDataFilter'), true);
        /** =========== End Init Variable ==================================== */
        $aDataWhere = array(
            'tUserSession' => $this->tUserSessionID,
            'tCompName' => $this->tCompName,
            'tUserCode' => $this->tUserLoginCode,
            'tRptCode' => $this->tRptCode,
            'nPage' => $this->nPage,
            'nRow' => $this->nPerPage,
            'nPerPage' => $this->nPerPage,
            'tUsrSessionID' => $this->tUserSessionID,
            'aDataFilter' => $this->aRptFilter,
        );

        // Get Data ReportFSaMGetDataReport
        $aDataReport = $this->Rptsalpdtbillpmt_model->FSaMGetDataReport($aDataWhere, $aDataFilter);
        // print_r($aDataReport);
        // exit;

        // Load View Advance Table
        $aDataViewRptParams = [
            'nOptDecimalShow' => $this->nOptDecimalShow,
            'aCompanyInfo' => $this->aCompanyInfo,
            'aDataReport' => $aDataReport,
            'aDataTextRef' => $this->aText,
            'aDataFilter' => $this->aRptFilter,
        ];

        $tViewRenderKool = JCNoHLoadViewAdvanceTable('report/datasources/reportsale/rptSalPdtBillPmt', 'wRptSalPdtBillPmtHtml', $aDataViewRptParams);

        // Data Viewer Center Report
        $aDataView = [
            'tTitleReport' => $this->aText['tTitleReport'],
            'tRptTypeExport' => $this->tRptExportType,
            'tRptCode' => $this->tRptCode,
            'tRptRoute' => $this->tRptRoute,
            'tViewRenderKool' => $tViewRenderKool,
            'aDataFilter' => $this->aRptFilter,
            'aDataReport' => [
                'raItems' => $aDataReport['aRptData'],
                'rnAllRow' => $aDataReport['aPagination']['nTotalRecord'],
                'rnCurrentPage' => $aDataReport['aPagination']['nDisplayPage'],
                'rnAllPage' => $aDataReport['aPagination']['nTotalPage'],
                'rtCode' => '1',
                'rtDesc' => 'success',
            ],
        ];

        $this->load->view('report/report/wReportViewer', $aDataView);
    }

    /**
     * Functionality: Render Excel Report
     * Parameters:  Function Parameter
     * Creator: 11/11/2020 Sooksanti
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

        $oBorder = (new BorderBuilder())
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN)
            ->build();

        $oStyleColums = (new StyleBuilder())
            ->setBorder($oBorder)
            ->setFontBold()
            ->build();

        $aCells = [
            WriterEntityFactory::createCell(language('report/report/report', 'tRptBarchCode')),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptBarchName')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSRCDate')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptBillNo')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptDocRef')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSOKADSNumber')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleByBillRptType')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleByBillDataType')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptPosTypeName')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptCst')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tChannel')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptPdtCode')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptPdtName')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptCabinetnumber')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptUnit')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptPricePerUnit')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSales')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptDiscount')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSalPdtBillSalDis')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSalPdtBillPmtNamePmt')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleByBillTaxSeparated')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleByBillVAT')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleByBillReduceEndOfBill')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSalPdtBillPmtDisPnt')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptSaleByBillTotalBillReduction')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptRndVal')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptGrandSale')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptXshGrand')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptPaymentToTal')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptPayby')),
            WriterEntityFactory::createCell(null),
            WriterEntityFactory::createCell(language('report/report/report', 'tRptXrcRef')),
        ];

        /** add a row at a time */
        $singleRow = WriterEntityFactory::createRow($aCells, $oStyleColums);
        $oWriter->addRow($singleRow);


        $aDataWhereRpt = array(
            'nPerPage' => 999999999999,
            'nPage' => 1, // เริ่มรายงานหน้าแรก
            'tCompName' => $this->tCompName,
            'tRptCode' => $this->tRptCode,
            'tUsrSessionID' => $this->tUserSessionID,
        );
        $aDataReport = $this->Rptsalpdtbillpmt_model->FSaMGetDataReport($aDataWhereRpt);

        /** Create a style with the StyleBuilder */
        $oStyle = (new StyleBuilder())
            ->setCellAlignment(CellAlignment::RIGHT)
            ->build();

        if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) {
            foreach ($aDataReport['aRptData'] as $nKey => $aValue) {
                $tPosType = $aValue['FNAppType'];
                $tPosTypeText = $this->aText['tRptPosType'];
                $tPosTypeTextPosType = $tPosTypeText.$tPosType;
                $tDocType = $aValue['FNXshDocType'];
                $tDocTypeText = '';
                if ($tDocType == '1') {
                    $tDocTypeText = $this->aText['tRptDocSale'];
                }
                if ($tDocType == '9') {
                    $tDocTypeText = $this->aText['tRptDocReturn'];
                }

                $tDataType = $aValue['FNType'];
                $tDataTypeText = '';
                if ($tDataType == '1') {
                    $tDataTypeText = 'Header';
                }
                if ($tDataType == '2') {
                    $tDataTypeText = 'Detail';
                }
                if ($tDataType == '3') {
                    $tDataTypeText = 'Recieve';
                }

                $tFDXshDocDate = empty($aValue['FDXshDocDate']) ? '' : date('d/m/Y', strtotime($aValue['FDXshDocDate']));
                $nFCXsdQty = empty($aValue['FCXsdQty']) ? 0 : $aValue['FCXsdQty'];
                $cFCXsdSetPrice = empty($aValue['FCXsdSetPrice']) ? 0 : $aValue['FCXsdSetPrice'];
                $cFCXsdAmt = empty($aValue['FCXsdAmt']) ? 0 : $aValue['FCXsdAmt'];
                $cFCXsdDis = empty($aValue['FCXsdDis']) ? 0 : $aValue['FCXsdDis'];
                $cFCXshVatable = empty($aValue['FCXshVatable']) ? 0 : $aValue['FCXshVatable'];
                $cFCXshVat = empty($aValue['FCXshVat']) ? 0 : $aValue['FCXshVat'];
                $cFCXshDis = empty($aValue['FCXshDis']) ? 0 : $aValue['FCXshDis']+($aValue['FCXshDisPnt']);
                $cFCXshTotalAfDis = empty($aValue['FCXshTotalAfDis']) ? 0 : $aValue['FCXshTotalAfDis'];
                $cFCXshRnd = empty($aValue['FCXshRnd']) ? 0 : $aValue['FCXshRnd'];
                $cFCXsdNet = empty($aValue['FCXsdNet']) ? 0 : $aValue['FCXsdNet'];
                $cFCXshGrand = empty($aValue['FCXshGrand']) ? 0 : $aValue['FCXshGrand'];
                $cFCXrcNet = empty($aValue['FCXrcNet']) ? 0 : $aValue['FCXrcNet'];

                $cFCXsdQty_SumFooter = empty($aValue['FCXsdQty_SumFooter']) ? 0 : $aValue['FCXsdQty_SumFooter'];
                $cFCXsdSetPrice_SumFooter = empty($aValue['FCXsdSetPrice_SumFooter']) ? 0 : $aValue['FCXsdSetPrice_SumFooter'];
                $cFCXsdAmt_SumFooter = empty($aValue['FCXsdAmt_SumFooter']) ? 0 : $aValue['FCXsdAmt_SumFooter'];
                $cFCXsdDis_SumFooter = empty($aValue['FCXsdDis_SumFooter']) ? 0 : $aValue['FCXsdDis_SumFooter'];
                $cFCXshVatable_SumFooter = empty($aValue['FCXshVatable_SumFooter']) ? 0 : $aValue['FCXshVatable_SumFooter'];
                $cFCXshVat_SumFooter = empty($aValue['FCXshVat_SumFooter']) ? 0 : $aValue['FCXshVat_SumFooter'];
                $cFCXshDis_SumFooter = empty($aValue['FCXshDis_SumFooter']) ? 0 : $aValue['FCXshDis_SumFooter'];
                $cFCXshTotalAfDis_SumFooter = empty($aValue['FCXshTotalAfDis_SumFooter']) ? 0 : $aValue['FCXshTotalAfDis_SumFooter'];
                $cFCXshRnd_SumFooter = empty($aValue['FCXshRnd_SumFooter']) ? 0 : $aValue['FCXshRnd_SumFooter'];
                $cFCXsdNet_SumFooter = empty($aValue['FCXsdNet_SumFooter']) ? 0 : $aValue['FCXsdNet_SumFooter'];
                $cFCXshGrand_SumFooter = empty($aValue['FCXshGrand_SumFooter']) ? 0 : $aValue['FCXshGrand_SumFooter'];
                $cFCXrcNet_SumFooter = empty($aValue['FCXrcNet_SumFooter']) ? 0 : $aValue['FCXrcNet_SumFooter'];
                $values = [
                    WriterEntityFactory::createCell($aValue['FTBchCode']),
                    WriterEntityFactory::createCell($aValue['FTBchName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($tFDXshDocDate),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTXshDocNo']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTXshRefInt']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTXshSOKADS']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($tDocTypeText),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($tDataTypeText),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($tPosTypeTextPosType),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTCstName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTChnName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTPdtCode']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTPdtName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($nFCXsdQty)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTPunName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdSetPrice)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdAmt)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdDis)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXdtDisPmt'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTPmhName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshVatable)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshVat)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshDis)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXshDisPnt'])),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshTotalAfDis)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshRnd)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdNet)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshGrand)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell(FCNnGetNumeric($cFCXrcNet)),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTRcvName']),
                    WriterEntityFactory::createCell(null),
                    WriterEntityFactory::createCell($aValue['FTXrcRefNo1']),
                ];
                $aRow = WriterEntityFactory::createRow($values);
                $oWriter->addRow($aRow);

                if (($nKey + 1) == count($aDataReport['aRptData'])) { //SumFooter
                    $values = [
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
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdQty_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdSetPrice_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdAmt_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdDis_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXdtDisPmt_SumFooter'])),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshVatable_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshVat_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshDis_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($aValue['FCXshDisPnt_SumFooter'])),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshTotalAfDis_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshRnd_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXsdNet_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXshGrand_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(FCNnGetNumeric($cFCXrcNet_SumFooter)),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
                        WriterEntityFactory::createCell(null),
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
     * Creator: 11/11/2020 Sooksanti
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
            WriterEntityFactory::createCell($this->aText['tDatePrint'] . ' ' . date('d/m/Y') . ' ' . $this->aText['tTimePrint'] . ' ' . date('H:i:s')),
        ];

        $aMulltiRow[] = WriterEntityFactory::createRow($aCells);

        return $aMulltiRow;

    }

    /**
     * Functionality: Render Excel Report Footer
     * Parameters:  Function Parameter
     * Creator: 01/10/2020 Sooksanti
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
        $aMulltiRow[] = WriterEntityFactory::createRow($aCells, $oStyleFilter);

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

        if (isset($this->aRptFilter['tBchCodeSelect']) && !empty($this->aRptFilter['tBchCodeSelect'])) {
            $tBchSelect = ($this->aRptFilter['bBchStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tBchNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptBchFrom'] . ' : ' . $tBchSelect),
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

        if (isset($this->aRptFilter['tMerCodeSelect']) && !empty($this->aRptFilter['tMerCodeSelect'])) {
            $tMerSelect = ($this->aRptFilter['bMerStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tMerNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptMerFrom'] . ' : ' . $tMerSelect),
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

        if (isset($this->aRptFilter['tShpCodeSelect']) && !empty($this->aRptFilter['tShpCodeSelect'])) {
            $tShpSelect = ($this->aRptFilter['bShpStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tShpNameSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptAdjShopFrom'] . ' : ' . $tShpSelect),
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

        if (isset($this->aRptFilter['tPosCodeSelect']) && !empty($this->aRptFilter['tPosCodeSelect'])) {
            $tPosSelect = ($this->aRptFilter['bPosStaSelectAll']) ? $this->aText['tRptAll'] : $this->aRptFilter['tPosCodeSelect'];
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptPosFrom'] . ' : ' . $tPosSelect),
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

        if ((isset($this->aRptFilter['tCstCodeFrom']) && !empty($this->aRptFilter['tCstCodeFrom'])) && (isset($this->aRptFilter['tCstCodeTo']) && !empty($this->aRptFilter['tCstCodeTo']))) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptCstFrom'] . ' : ' . $this->aRptFilter['tCstCodeFrom'] . ' ' . $this->aText['tRptCstTo'] . ' : ' . $this->aRptFilter['tCstCodeTo']),
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

        // ช่องทางการขาย
        if (!empty($this->aRptFilter['tChannelCodeFrom']) && !empty($this->aRptFilter['tChannelCodeTo'])) {
            $aCells = [
                WriterEntityFactory::createCell($this->aText['tRptFilterChannelFrom'] . ' ' . $this->aRptFilter['tChannelNameFrom'] . '     ' . $this->aText['tRptFilterChannelTo'] . ' ' . $this->aRptFilter['tChannelNameTo']),
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
