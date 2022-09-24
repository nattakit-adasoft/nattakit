<?php
require_once "stimulsoft/helper.php";
require_once "../decodeURLCenter.php";
?>
<!DOCTYPE html>

<html>
    <head>
        <?php
        if (isset($_GET["infor"])) {
            $aParamiterMap = array(
                "Lang", "ComCode", "BranchCode", "DocCode"
            );
            $aDataMQ = FSaHDeCodeUrlParameter($_GET["infor"], $aParamiterMap);
            
        } else {
            $aDataMQ = false;
        }
        if ($aDataMQ) {
        ?>
        <title>Frm_SQL_SMBillPi.mrt - Viewer</title>
        <link rel="stylesheet" type="text/css" href="css/stimulsoft.viewer.office2013.whiteblue.css">
        <script type="text/javascript" src="scripts/stimulsoft.reports.js"></script>
        <script type="text/javascript" src="scripts/stimulsoft.viewer.js"></script>

        <?php
        $options = StiHelper::createOptions();
        $options->handler = "handler.php";
        $options->timeout = 30;
        StiHelper::initialize($options);
        ?>
        <script type="text/javascript">
            function Start() {
                Stimulsoft.Base.StiLicense.key =
				"6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHlDr8/6PIqNKBuLMEkN8xMUPugEQPeiwAHVm+OV" +
				"bCQVabeN3of7ZbnsixRRu+7irZqJ8c0f4LGB9+5sPaMJomcsE37V4Zf1NuPeQ8n+CDF+5Cp4IOyIAra8" +
				"o4iA3x/nD4ktTT7e/BzGEHvbCZvNgR9i00xpzfC/5xrrzGqNC0AF8PWDnOCg0MPNodj9soA4ZH0NPRLj" +
				"jwNPBOxmmG1pLoKBG3Bh7ALEQ2moT93cIEj124GvRIPnChAkiyLRMZkIlTdPYuBHEa7CPM9knzuGqaiz" +
				"ZrN9eWQ+iGiV/grvhEJU3foCQaGJgwnsRHbMPCSZdHtT/4yxoO42SWgZFayM/pDuOXkVhKytawLWnrrQ" +
				"oNUQpmvSarHOUVDLRe70HbyRswH0AXraboEed4qTfn+CUBtMdSEwQLqj237m6N8OTvsROjcXLi4QfXlP" +
				"A28SpfXbQBvEN2TrGqBr5dyKpgbkG+58x85lFO9s1XcQoKXfml8elYzFhMlcae97o5u4dTE/VIseSJ7W" +
				"/scPHOg5gM3Tn72U32bW53UF8/kcNl4+T0WHpg==";

                Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile("localization/en.xml", true);

                var report = new Stimulsoft.Report.StiReport();
                report.loadFile("reports/Frm_SQL_SMBillPi.mrt");

                report.dictionary.variables.getByName("SP_nLang").valueObject = "1";
                report.dictionary.variables.getByName("nLanguage").valueObject = 1;
                report.dictionary.variables.getByName("SP_tCompCode").valueObject = "C0001";
                report.dictionary.variables.getByName("SP_tCmpBch").valueObject = "00342";
                report.dictionary.variables.getByName("SP_tDocNo").valueObject = "PR000681900003";
                report.dictionary.variables.getByName("SP_nAddSeq").valueObject = 10149;
                
                /*report.dictionary.variables.getByName("SP_nLang").valueObject = "1";
                report.dictionary.variables.getByName("nLanguage").valueObject = 1;
                report.dictionary.variables.getByName("SP_tCompCode").valueObject = "00001";
                report.dictionary.variables.getByName("SP_tCmpBch").valueObject = "00001";
                report.dictionary.variables.getByName("SP_tDocNo").valueObject = "PS000011900001";
                report.dictionary.variables.getByName("SP_nAddSeq").valueObject = 10149;*/
            
                /*report.dictionary.variables.getByName("SP_nLang").valueObject = "<?php echo $aDataMQ["Lang"]; ?>";
                report.dictionary.variables.getByName("nLanguage").valueObject = <?php echo $aDataMQ["Lang"]; ?>;
                report.dictionary.variables.getByName("SP_tCompCode").valueObject = "<?php echo $aDataMQ["ComCode"]; ?>";
                report.dictionary.variables.getByName("SP_tCmpBch").valueObject = "<?php echo $aDataMQ["BranchCode"]; ?>";
                report.dictionary.variables.getByName("SP_tDocNo").valueObject = "<?php echo $aDataMQ["DocCode"]; ?>";
                report.dictionary.variables.getByName("SP_nAddSeq").valueObject = 10149;*/

                var options = new Stimulsoft.Viewer.StiViewerOptions();
                options.appearance.fullScreenMode = true;
                options.toolbar.displayMode = Stimulsoft.Viewer.StiToolbarDisplayMode.Separated;

                var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

                viewer.onBeginProcessData = function (args, callback) {
                    <?php StiHelper::createHandler(); ?>
                };

                viewer.report = report;
                viewer.renderHtml("viewerContent");
            }
        </script>
    <?php } ?>
    </head>
    <body onload="Start()">
        <?php if ($aDataMQ) { ?>
            <div id="viewerContent"></div>
            <?php
        } else {
            echo "ไม่สามารถเข้าถึงข้อมูลนี้ได้";
        }
        ?>
    </body>
</html>
