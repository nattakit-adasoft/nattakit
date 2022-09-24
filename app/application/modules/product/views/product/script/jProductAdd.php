<script type="text/javascript">
    $('document').on('keydown', function() {
        alert(event.keyCode);
    });

    var tBaseURL = '<?php echo base_url(); ?>';
    var nLangEdits = '<?php echo $this->session->userdata("tLangEdit") ?>';
    var nStaAddOrEdit = '<?php echo $nStaAddOrEdit; ?>';
    var nSesUsrShpCount = '<?php echo $this->session->userdata("nSesUsrShpCount") ?>';
    $(document).ready(function() {
        //เรียกหน้า กำหนดเงื่อนไขการควบคุมสต๊อก 23/01/2020 Saharat(Golf) 
        JSvPdtCallpageStockConditions();
        if (nStaAddOrEdit != "" && nStaAddOrEdit == 1) {
            var nCountDataImgItem = $('#odvImageTumblr #otbImageListProduct tbody tr td.xWTDImgDataItem').length;
            if (nCountDataImgItem > 0) {
                $('#odvImageTumblr #otbImageListProduct tbody tr td.xWTDImgDataItem').each(function() {
                    var tDataTumblr = $(this).find('.xCNImgTumblr').data('tumblr');
                    if (tDataTumblr == 0) {
                        var tImgSrcFirstRow = $('#oimTumblrProduct' + tDataTumblr).attr('src');
                        $('#oimImgMasterProduct').attr('src', tImgSrcFirstRow);
                    }
                });
            }
        }

        if (JSbProductIsCreatePage()) {
            $("#oetPdtCode").attr("disabled", true);
            $('#ocbProductAutoGenCode').change(function() {
                if ($('#ocbProductAutoGenCode').is(':checked')) {
                    $('#oetPdtCode').val('');
                    $("#oetPdtCode").attr("disabled", true);
                    $('#odvProductCodeForm').removeClass('has-error');
                    $('#odvProductCodeForm em').remove();
                } else {
                    $("#oetPdtCode").attr("disabled", false);
                }
            });
            JSxProductVisibleComponent('#odvReasonAutoGenCode', true);
        }

        if (JSbProductIsUpdatePage()) {
            $("#oetPdtCode").attr("readonly", true);
            $('#odvProductAutoGenCode input').attr('disabled', true);
            JSxProductVisibleComponent('#odvProductAutoGenCode', false);
        }

        $('#oetPdtCode').blur(function() {
            JSxCheckProductCodeDupInDB();
        });

        if (nSesUsrShpCount != 1) {
            //ปิดปุ่ม browse ร้านค้า หากยังไม่เลือกสาขา
            if ($('#oetPdtBchCode').val() == '' || $('#oetPdtBchCode').val() == null) {
                $("#obtBrowsePdtInfoShp").attr("disabled", true);
            } else {
                $("#obtBrowsePdtInfoShp").attr("disabled", false);
            }
        }
    });


    $('.xWPdtSelectBox').selectpicker();
    $('.selectpicker').selectpicker();

    $('.xCNDatePicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date(),
    });

    $('#obtPdtSaleStart').click(function() {
        $('#oetPdtSaleStart').datepicker('show')
    });

    $('#obtPdtSaleStop').click(function() {
        $('#oetPdtSaleStop').datepicker('show')
    });

    $('#obtSubmitProduct').click(function() {
        JSoAddEditProduct('<?php echo $tRoute ?>')
    });

    $('#obtPdtGenCode').click(function() {
        JSoGenerateProductCode()
    });

    $('#olbDelAllPdtEvnNotSale').click(function() {
        JSxDelAllPdtEvnNotSale()
    });

    // Browse Modal Image Multiole
    $('#odvPdtAddImageBtn,#oimImgMasterProduct').click(function() {
        JSvImageCallTempNEW('1', '2', 'Product')
    });

    $('#oetModalAebBarCode').keydown(function(event) {
        if (event.keyCode == '32') {
            event.preventDefault();
        }
    });


    //Functionality: Event Check Product Duplicate
    //Parameters: Event Blur Input Product Code
    //Creator: 26/03/2019 wasin (Yoshi)
    //Return: -
    //ReturnType: -
    function JSxCheckProductCodeDupInDB() {
        if (!$('#ocbProductAutoGenCode').is(':checked')) {
            $.ajax({
                type: "POST",
                url: "CheckInputGenCode",
                data: {
                    tTableName: "TCNMPdt",
                    tFieldName: "FTPdtCode",
                    tCode: $("#oetPdtCode").val()
                },
                async: false,
                cache: false,
                timeout: 0,
                success: function(tResult) {
                    var aResult = JSON.parse(tResult);
                    $("#ohdCheckDuplicatePdtCode").val(aResult["rtCode"]);
                    JSxProductSetValidEventBlur();
                    $('#ofmAddEditProduct').submit();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    }

    //Functionality: Set Validate Event Blur
    //Parameters: Validate Event Blur
    //Creator: 26/03/2019 wasin (Yoshi)
    //Return: -
    //ReturnType: -
    function JSxProductSetValidEventBlur() {
        $('#ofmAddEditProduct').validate().destroy();

        // Set Validate Dublicate Code
        $.validator.addMethod('dublicateCode', function(value, element) {
            if ($("#ohdCheckDuplicatePdtCode").val() == 1) {
                return false;
            } else {
                return true;
            }
        }, '');

        // From Summit Validate
        $('#ofmAddEditProduct').validate({
            rules: {
                oetPdtCode: {
                    "required": {
                        // ตรวจสอบเงื่อนไข validate
                        depends: function(oElement) {
                            if ($('#ocbProductAutoGenCode').is(':checked')) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    },
                    "dublicateCode": {}
                },
                oetPdtName: {
                    "required": {}
                },
            },
            messages: {
                oetPdtCode: {
                    "required": $('#oetPdtCode').attr('data-validate-required'),
                    "dublicateCode": $('#oetPdtCode').attr('data-validate-dublicateCode')
                },
                oetPdtName: {
                    "required": $('#oetPdtName').attr('data-validate-required'),
                },
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
                error.addClass("help-block");
                if (element.prop("type") === "checkbox") {
                    error.appendTo(element.parent("label"));
                } else {
                    var tCheck = $(element.closest('.form-group')).find('.help-block').length;
                    if (tCheck == 0) {
                        error.appendTo(element.closest('.form-group')).trigger('change');
                    }
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass("has-error").removeClass("has-success");
            },
            unhighlight: function(element, errorClass, validClass) {
                var nStaCheckValid = $(element).parents('.form-group').find('.help-block').length
                if (nStaCheckValid != 0) {
                    $(element).closest('.form-group').addClass("has-success").removeClass("has-error");
                }
            },
            submitHandler: function(form) {}
        });
    }

    /** =================================================== Option Browse Info Product ================================================== */

    // Option Browse Product Vat
    var oPdtBrowseVat = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tTextLeftJoin = "( SELECT Result.* ";
        tTextLeftJoin += " FROM ";
        tTextLeftJoin += " ( ";
        tTextLeftJoin += "   SELECT VatAtv.* FROM (  ";
        tTextLeftJoin += "     SELECT  ";
        tTextLeftJoin += "        row_number() over (partition by TCNMVatRate.FTVatCode order by FDVatStart DESC) as VatRateActive, ";
        tTextLeftJoin += "        TCNMVatRate.FDVatStart, ";
        tTextLeftJoin += "        TCNMVatRate.FTVatCode,  ";
        tTextLeftJoin += "        TCNMVatRate.FCVatRate ";
        tTextLeftJoin += "     FROM TCNMVatRate ";
        tTextLeftJoin += "     WHERE 1 = 1 ";
        tTextLeftJoin += "    AND (CONVERT(VARCHAR(19), GETDATE(), 121) >= CONVERT(VARCHAR(19), TCNMVatRate.FDVatStart, 121)) ";
        tTextLeftJoin += " ) VatAtv WHERE VatAtv.VatRateActive = 1 ";
        tTextLeftJoin += " ) AS Result ";
        tTextLeftJoin += ") AS TVJOIN ";

        var oOptionReturn = {
            Title: ['company/vatrate/vatrate', 'tVATTitle'],
            Table: {
                Master: 'TCNMVatRate',
                PK: 'FTVatCode'
            },
            Join: {
                Table: [tTextLeftJoin],
                SpecialJoin: ['INNER JOIN'],
                On: ['TCNMVatRate.FTVatCode = TVJOIN.FTVatCode AND TCNMVatRate.FDVatStart = TVJOIN.FDVatStart']
            },
            GrideView: {
                ColumnPathLang: 'company/vatrate/vatrate',
                ColumnKeyLang: ['tVATTBCode', 'tVATTBRate', 'tVATDateStart'],
                DataColumns: ['TCNMVatRate.FTVatCode', 'TCNMVatRate.FCVatRate', 'TCNMVatRate.FDVatStart'],
                Perpage: 10,
                OrderBy: ['TCNMVatRate.FDCreateOn DESC'],
                // SourceOrder		: "ASC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMVatRate.FTVatCode"],
                Text: [tInputReturnName, "TCNMVatRate.FCVatRate"],
            },
            // DebugSQL : true
        };
        return oOptionReturn;
    }

    //กลือกกลุ่มธุรกิจ
    var oPdtBrowseMerchant = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var oOptionReturn = {
            Title: ['company/merchant/merchant', 'tMerchantTitle'],
            Table: {
                Master: 'TCNMMerchant',
                PK: 'FTMerCode'
            },
            Join: {
                Table: ['TCNMMerchant_L'],
                On: ['TCNMMerchant_L.FTMerCode = TCNMMerchant.FTMerCode AND TCNMMerchant_L.FNLngID = ' + nLangEdits, ]
            },
            Where: {
                Condition: [
                    "AND TCNMMerchant.FTMerStaActive = '1'",
                ]
            },
            GrideView: {
                ColumnPathLang: 'company/merchant/merchant',
                ColumnKeyLang: ['tMerCode', 'tMerName'],
                ColumnsSize: ['15%', '75%'],
                DataColumns: ['TCNMMerchant.FTMerCode', 'TCNMMerchant_L.FTMerName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMMerchant.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMMerchant.FTMerCode"],
                Text: [tInputReturnName, "TCNMMerchant_L.FTMerName"],
            },
            RouteAddNew: 'merchant',
            BrowseLev: nStaPdtBrowseType,
            NextFunc: {
                FuncName: 'JSxClearBrowseCondition',
                ArgReturn: []
            }
        };
        return oOptionReturn;
    }

    //หลังจากเลือกกลุ่มธุรกิจต้องล้างค่า
    function JSxClearBrowseCondition() {
        // $('#oetPdtBchName').val('');
        // $('#oetPdtBchCode').val('');

        $('#oetPdtInfoShpCode').val('');
        $('#oetPdtInfoShpName').val('');

        $('#oetPdtInfoMgpName').val('');
        $('#oetPdtInfoMgpCode').val('');

        $("#obtBrowsePdtInfoMgp").attr("disabled", false);
    }



    //เลือกสาขา
    var oPdtBrowseAgency = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tBchCodeWhere = poReturnInput.tBchCodeWhere;

        var oOptionReturn = {
            Title: ['ticket/agency/agency', 'tAggTitle'],
            Table: {
                Master: 'TCNMAgency',
                PK: 'FTAgnCode'
            },
            Join: {
                Table: ['TCNMAgency_L'],
                On: ['TCNMAgency_L.FTAgnCode = TCNMAgency.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'ticket/agency/agency',
                ColumnKeyLang: ['tAggCode', 'tAggName'],
                ColumnsSize: ['15%', '85%'],
                WidthModal: 50,
                DataColumns: ['TCNMAgency.FTAgnCode', 'TCNMAgency_L.FTAgnName'],
                DataColumnsFormat: ['', ''],
                Perpage: 10,
                OrderBy: ['TCNMAgency.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMAgency.FTAgnCode"],
                Text: [tInputReturnName, "TCNMAgency_L.FTAgnName"],
            },
            RouteAddNew: 'agency',
            BrowseLev: nStaPdtBrowseType,
            NextFunc: {
                FuncName: 'JSxClearBrowseConditionAgn',
                ArgReturn: ['FTAgnCode']
            }
        }
        return oOptionReturn;
    }


    function JSxClearBrowseConditionAgn(ptData) {
        // aData = JSON.parse(ptData);
        if (ptData != '' || ptData != 'NULL') {

            $('#oetPdtBchCode').val('');
            $('#oetPdtBchName').val('');

            $('#oetPdtInfoShpCode').val('');
            $('#oetPdtInfoShpName').val('');
        }
    }

    //เลือกสาขา
    var oPdtBrowseBranch = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tAgnCodeWhere = poReturnInput.tAgnCodeWhere;

        $nCountBCH = '<?= $this->session->userdata('nSesUsrBchCount') ?>';
        if ($nCountBCH > 1) {
            //ถ้าสาขามากกว่า 1 
            tBCH = "<?= $this->session->userdata('tSesUsrBchCodeMulti'); ?>";
            tWhereBCH = " AND TCNMBranch.FTBchCode IN ( " + tBCH + " ) ";
        } else {
            tWhereBCH = '';
        }

        if (tAgnCodeWhere == '' || tAgnCodeWhere == null) {
            tWhereAgn = '';
        } else {
            tWhereAgn = " AND TCNMBranch.FTAgnCode = '" + tAgnCodeWhere + "'";
        }


        var oOptionReturn = {
            Title: ['company/branch/branch', 'tBCHTitle'],
            Table: {
                Master: 'TCNMBranch',
                PK: 'FTBchCode'
            },
            Join: {
                Table: ['TCNMBranch_L', 'TCNMAgency_L'],
                On: [
                    'TCNMBranch_L.FTBchCode = TCNMBranch.FTBchCode AND TCNMBranch_L.FNLngID = ' + nLangEdits,
                    'TCNMAgency_L.FTAgnCode = TCNMBranch.FTAgnCode AND TCNMAgency_L.FNLngID = ' + nLangEdits,
                ]
            },
            Where: {
                Condition: [tWhereBCH + tWhereAgn]
            },
            GrideView: {
                ColumnPathLang: 'company/branch/branch',
                ColumnKeyLang: ['tBCHCode', 'tBCHName'],
                ColumnsSize: ['15%', '75%'],
                DataColumns: ['TCNMBranch.FTBchCode', 'TCNMBranch_L.FTBchName', 'TCNMAgency_L.FTAgnName', 'TCNMBranch.FTAgnCode'],
                DataColumnsFormat: ['', '', '', ''],
                DisabledColumns: [2, 3],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMBranch.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMBranch.FTBchCode"],
                Text: [tInputReturnName, "TCNMBranch_L.FTBchName"],
            },
            RouteAddNew: 'branch',
            BrowseLev: nStaPdtBrowseType,
            NextFunc: {
                FuncName: 'JSxClearBrowseConditionBCH',
                ArgReturn: ['FTAgnName', 'FTAgnCode']
            }
        }
        return oOptionReturn;
    }

    //หลังจากเลือกสาขาต้องล้างค้า
    function JSxClearBrowseConditionBCH(ptData) {


        $('#oetPdtInfoShpCode').val('');
        $('#oetPdtInfoShpName').val('');

        if (ptData == '' || ptData == 'NULL') {
            $("#obtBrowsePdtInfoShp").attr("disabled", true);
        } else {
            aData = JSON.parse(ptData);
            var FTAgnName = aData[0];
            var FTAgnCode = aData[1];

            $('#oetPdtAgnCode').val(FTAgnCode);
            $('#oetPdtAgnName').val(FTAgnName);
            $("#obtBrowsePdtInfoShp").attr("disabled", false);
        }
    }

    // Option Browse Merchant Group
    var oPdtBrowseMerchantGroup = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tMerCodeWhere = poReturnInput.tMerCodeWhere;
        var oOptionReturn = {
            Title: ['company/merchant/merchant', 'tMerchantTitle'],
            Table: {
                Master: 'TCNMMerPdtGrp',
                PK: 'FTMgpCode'
            },
            Join: {
                Table: ['TCNMMerPdtGrp_L'],
                On: ['TCNMMerPdtGrp_L.FTMgpCode = TCNMMerPdtGrp.FTMgpCode AND TCNMMerPdtGrp_L.FNLngID = ' + nLangEdits, ]
            },
            Where: {
                Condition: ["AND TCNMMerPdtGrp.FTMerCode = '" + tMerCodeWhere + "'"]
            },
            GrideView: {
                ColumnPathLang: 'company/merchant/merchant',
                ColumnKeyLang: ['tMerCode', 'tMerName'],
                ColumnsSize: ['15%', '75%'],
                DataColumns: ['TCNMMerPdtGrp.FTMgpCode', 'TCNMMerPdtGrp_L.FTMgpName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMMerPdtGrp.FDCreateOn DESC'],
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMMerPdtGrp.FTMgpCode"],
                Text: [tInputReturnName, "TCNMMerPdtGrp_L.FTMgpName"],
            },
            RouteAddNew: 'merchant',
            BrowseLev: nStaPdtBrowseType,
            NextFunc: {
                FuncName: '',
                ArgReturn: []
            }
        };
        return oOptionReturn;
    }

    // Option Browse Product Group
    var oPdtBrowsePdtGrp = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var oOptionReturn = {
            Title: ['product/pdtgroup/pdtgroup', 'tPGPTitle'],
            Table: {
                Master: 'TCNMPdtGrp',
                PK: 'FTPgpChain'
            },
            Join: {
                Table: ['TCNMPdtGrp_L'],
                On: ['TCNMPdtGrp.FTPgpChain = TCNMPdtGrp_L.FTPgpChain AND TCNMPdtGrp_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtgroup/pdtgroup',
                ColumnKeyLang: ['tPGPCode', 'tPGPChainCode', 'tPGPName', 'tPGPChain'],
                ColumnsSize: ['10%', '15%', '40%', '35%'],
                DataColumns: ['TCNMPdtGrp.FTPgpCode', 'TCNMPdtGrp.FTPgpChain', 'TCNMPdtGrp_L.FTPgpName', 'TCNMPdtGrp_L.FTPgpChainName'],
                DataColumnsFormat: ['', '', '', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdtGrp.FDCreateOn DESC'],
                // SourceOrder: "ASC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtGrp.FTPgpChain"],
                Text: [tInputReturnName, "TCNMPdtGrp_L.FTPgpChainName"],
            },
            // RouteAddNew : 'pdtgroup',
            // BrowseLev : nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Option Browse Product Type
    var oPdtBrowsePdtType = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var oOptionReturn = {
            Title: ['product/pdttype/pdttype', 'tPTYTitle'],
            Table: {
                Master: 'TCNMPdtType',
                PK: 'FTPtyCode'
            },
            Join: {
                Table: ['TCNMPdtType_L'],
                On: ['TCNMPdtType_L.FTPtyCode = TCNMPdtType.FTPtyCode AND TCNMPdtType_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdttype/pdttype',
                ColumnKeyLang: ['tPTYCode', 'tPTYName'],
                ColumnsSize: ['10%', '90%'],
                DataColumns: ['TCNMPdtType.FTPtyCode', 'TCNMPdtType_L.FTPtyName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 5,
                OrderBy: ['TCNMPdtType.FTPtyCode'],
                // SourceOrder: "DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtType.FTPtyCode"],
                Text: [tInputReturnName, "TCNMPdtType_L.FTPtyName"],
            },
            // RouteAddNew: 'pdttype',
            // BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Option Browse Product Brand
    var oPdtBrowsePdtBrand = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var oOptionReturn = {
            Title: ['product/pdtbrand/pdtbrand', 'tPBNTitle'],
            Table: {
                Master: 'TCNMPdtBrand',
                PK: 'FTPbnCode'
            },
            Join: {
                Table: ['TCNMPdtBrand_L'],
                On: ['TCNMPdtBrand_L.FTPbnCode = TCNMPdtBrand.FTPbnCode AND TCNMPdtBrand_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtbrand/pdtbrand',
                ColumnKeyLang: ['tPBNCode', 'tPBNName'],
                ColumnsSize: ['10%', '90%'],
                DataColumns: ['TCNMPdtBrand.FTPbnCode', 'TCNMPdtBrand_L.FTPbnName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdtBrand.FDCreateOn DESC'],
                // SourceOrder: "DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtBrand.FTPbnCode"],
                Text: [tInputReturnName, "TCNMPdtBrand_L.FTPbnName"],
            },
            // RouteAddNew : 'pdtbrand',
            // BrowseLev : nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Option Browse Product Model
    var oPdtBrowsePdtModel = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var oOptionReturn = {
            Title: ['product/pdtmodel/pdtmodel', 'tPMOTitle'],
            Table: {
                Master: 'TCNMPdtModel',
                PK: 'FTPmoCode'
            },
            Join: {
                Table: ['TCNMPdtModel_L'],
                On: ['TCNMPdtModel_L.FTPmoCode = TCNMPdtModel.FTPmoCode AND TCNMPdtModel_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtmodel/pdtmodel',
                ColumnKeyLang: ['tPMOCode', 'tPMOName'],
                ColumnsSize: ['10%', '90%'],
                DataColumns: ['TCNMPdtModel.FTPmoCode', 'TCNMPdtModel_L.FTPmoName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdtModel.FDCreateOn DESC'],
                // SourceOrder: "DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtModel.FTPmoCode"],
                Text: [tInputReturnName, "TCNMPdtModel_L.FTPmoName"],
            },
            // RouteAddNew: 'pdtmodel',
            // BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Option Browse Product Touch Group
    var oPdtBrowsePdtTouchGrp = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var oOptionReturn = {
            Title: ['product/pdttouchgroup/pdttouchgroup', 'tTCGTitle'],
            Table: {
                Master: 'TCNMPdtTouchGrp',
                PK: 'FTTcgCode'
            },
            Join: {
                Table: ['TCNMPdtTouchGrp_L'],
                On: ['TCNMPdtTouchGrp_L.FTTcgCode = TCNMPdtTouchGrp.FTTcgCode AND TCNMPdtTouchGrp_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdttouchgroup/pdttouchgroup',
                ColumnKeyLang: ['tTCGCode', 'tTCGName'],
                ColumnsSize: ['10%', '90%'],
                DataColumns: ['TCNMPdtTouchGrp.FTTcgCode', 'TCNMPdtTouchGrp_L.FTTcgName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdtTouchGrp.FDCreateOn DESC'],
                // SourceOrder:"DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtTouchGrp.FTTcgCode"],
                Text: [tInputReturnName, "TCNMPdtTouchGrp_L.FTTcgName"],
            },
            RouteAddNew: 'pdtTouchGroup',
            BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Option Add Browse Product Unit 
    var oPdtBrowseUnit = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['product/pdtunit/pdtunit', 'tPUNTitle'],
            Table: {
                Master: 'TCNMPdtUnit',
                PK: 'FTPunCode',
                PKName: 'FTPunName'
            },
            Join: {
                Table: ['TCNMPdtUnit_L'],
                On: ['TCNMPdtUnit_L.FTPunCode = TCNMPdtUnit.FTPunCode AND TCNMPdtUnit_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtunit/pdtunit',
                ColumnKeyLang: ['tPUNCode', 'tPUNName'],
                ColumnsSize: ['10%', '90%'],
                WidthModal: 50,
                DataColumns: ['TCNMPdtUnit.FTPunCode', 'TCNMPdtUnit_L.FTPunName'],
                DataColumnsFormat: ['', ''],
                Perpage: 10,
                OrderBy: ['TCNMPdtUnit.FDCreateOn DESC'],
                // SourceOrder     : "ASC"
            },
            CallBack: {
                ReturnType: 'M',
                Value: [tInputReturnCode, "TCNMPdtUnit.FTPunCode"],
                Text: [tInputReturnName, "TCNMPdtUnit_L.FTPunName"],

            },
            NextFunc: {
                FuncName: tNextFuncName,
                ArgReturn: ['FTPunCode', 'FTPunName']
            },
            RouteAddNew: 'pdtunit',
            BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Option Add Browse Product Set
    var oPdtBrowsePdtSet = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['product/product/product', 'tPDTTitle'],
            Table: {
                Master: 'TCNMPdt',
                PK: 'FTPdtCode',
                PKName: 'FTPdtName'
            },
            Join: {
                Table: ['TCNMPdt_L'],
                On: ['TCNMPdt_L.FTPdtCode = TCNMPdt.FTPdtCode AND TCNMPdt_L.FNLngID = ' + nLangEdits]
            },
            Where: {
                Condition: [
                    "AND TCNMPdt.FTPdtForSystem = '1'",
                    "AND TCNMPdt.FTPdtSetOrSN   = '1'",
                ]
            },
            NotIn: {
                Selector: 'oetPdtCode',
                Table: 'TCNMPdt',
                Key: 'FTPdtCode'
            },
            GrideView: {
                ColumnPathLang: 'product/product/product',
                ColumnKeyLang: ['tPDTCode', 'tPDTName', 'tPDTNameOth', 'tPDTNameABB'],
                ColumnsSize: ['10%', '30%', '30%', '30%'],
                DataColumns: ['TCNMPdt.FTPdtCode', 'TCNMPdt_L.FTPdtName', 'TCNMPdt_L.FTPdtNameOth', 'TCNMPdt_L.FTPdtNameABB'],
                DataColumnsFormat: ['', '', '', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdt.FTPdtCode'],
                SourceOrder: "ASC"
            },
            CallBack: {
                ReturnType: 'M',
                Value: [tInputReturnCode, "TCNMPdt.FTPdtCode"],
                Text: [tInputReturnName, "TCNMPdt_L.FTPdtName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
                ArgReturn: ['FTPdtCode', 'FTPdtName']
            },
            RouteAddNew: 'product',
            BrowseLev: 1
        }
        return oOptionReturn;
    }

    // Option Add Browse Product Event Not Sale
    var oPdtBrowsePdtEvnNoSale = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['product/pdtnoslebyevn/pdtnoslebyevn', 'tEVNTitle'],
            Table: {
                Master: 'TCNMPdtNoSleByEvn_L',
                PK: 'FTEvnCode',
                PKName: 'FTEvnName'
            },
            Where: {
                Condition: ["AND TCNMPdtNoSleByEvn_L.FNLngID = " + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtnoslebyevn/pdtnoslebyevn',
                ColumnKeyLang: ['tEVNTBCode', 'tEVNTBName'],
                ColumnsSize: ['20%', '80%'],
                DataColumns: ['TCNMPdtNoSleByEvn_L.FTEvnCode', 'TCNMPdtNoSleByEvn_L.FTEvnName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdtNoSleByEvn_L.FTEvnCode'],
                SourceOrder: "DESC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtNoSleByEvn_L.FTEvnCode"],
                Text: [tInputReturnName, "TCNMPdtNoSleByEvn_L.FTEvnName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
                ArgReturn: ['FTEvnCode', 'FTEvnName']
            },
            RouteAddNew: 'productNoSaleEvent',
            BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    /** ================================================================================================================================= */

    /** =================================================== Event Browse Info Product =================================================== */
    //Click Browse Vat
    $('#obtBrowseVat').on('click', function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowseVatOption = oPdtBrowseVat({
                'tReturnInputCode': 'ocmPdtVatCode',
                'tReturnInputName': 'ocmPdtVatName'
            });
            JCNxBrowseData('oPdtBrowseVatOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });





    // Click Browse Merchant
    $('#obtBrowseMerchant').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxCheckPinMenuClose();
            window.oPdtBrowseMerchantOption = oPdtBrowseMerchant({
                'tReturnInputCode': 'oetPdtMerCode',
                'tReturnInputName': 'oetPdtMerName',
            });
            JCNxBrowseData('oPdtBrowseMerchantOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Click Browse Agency
    $('#obtBrowseAgency').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxCheckPinMenuClose();
            window.oPdtBrowseAgencyOption = oPdtBrowseAgency({
                'tReturnInputCode': 'oetPdtAgnCode',
                'tReturnInputName': 'oetPdtAgnName',
                'tBchCodeWhere': $('#oetPdtBchCode').val(),
            });
            JCNxBrowseData('oPdtBrowseAgencyOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });



    // Click Browse Branch
    $('#obtBrowseBranch').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxCheckPinMenuClose();
            window.oPdtBrowseBranchOption = oPdtBrowseBranch({
                'tReturnInputCode': 'oetPdtBchCode',
                'tReturnInputName': 'oetPdtBchName',
                'tAgnCodeWhere': $('#oetPdtAgnCode').val(),
            });
            JCNxBrowseData('oPdtBrowseBranchOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // เลือกร้านค้า
    $('#obtBrowsePdtInfoShp').click(function(e) {
        e.preventDefault();
        var tSQLWhere = "";
        if ($("#oetPdtBchCode").val() != '' && $("#oetPdtMerCode").val() != '') {
            tSQLWhere = "AND TCNMShop.FTMerCode = '" + $("#oetPdtMerCode").val() + "' AND TCNMShop.FTBchCode = '" + $("#oetPdtBchCode").val() + "'";
        } else if ($("#oetPdtBchCode").val() != '' && $("#oetPdtMerCode").val() == '') {
            tSQLWhere = "AND TCNMShop.FTBchCode = '" + $("#oetPdtBchCode").val() + "'";
        }
        var nStaSession = JCNxFuncChkSessionExpired();
        JSxCheckPinMenuClose();
        window.oPdtBrowseShopOption = oPdtBrowseShop({
            'tReturnInputCode': 'oetPdtInfoShpCode',
            'tReturnInputName': 'oetPdtInfoShpName',
            'tSQLWhere': tSQLWhere
        });
        JCNxBrowseData('oPdtBrowseShopOption');
    });

    // Click Browse Merchant Group
    $('#obtBrowsePdtInfoMgp').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowseMerchantGroupOption = oPdtBrowseMerchantGroup({
                'tReturnInputCode': 'oetPdtInfoMgpCode',
                'tReturnInputName': 'oetPdtInfoMgpName',
                'tMerCodeWhere': $('#oetPdtMerCode').val(),
            });
            JCNxBrowseData('oPdtBrowseMerchantGroupOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Click Browse Product Group
    $('#obtBrowsePdtGrp').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowsePdtGrpOption = oPdtBrowsePdtGrp({
                'tReturnInputCode': 'oetPdtPgpChain',
                'tReturnInputName': 'oetPdtPgpChainName',
            });
            JCNxBrowseData('oPdtBrowsePdtGrpOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Click Browse Product Type
    $('#obtBrowsePdtType').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowsePdtTypeOption = oPdtBrowsePdtType({
                'tReturnInputCode': 'oetPdtPtyCode',
                'tReturnInputName': 'oetPdtPtyName',
            });
            JCNxBrowseData('oPdtBrowsePdtTypeOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Click Browse Product Brand
    $('#obtBrowsePdtBrand').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowsePdtBrandOption = oPdtBrowsePdtBrand({
                'tReturnInputCode': 'oetPdtPbnCode',
                'tReturnInputName': 'oetPdtPbnName',
            });
            JCNxBrowseData('oPdtBrowsePdtBrandOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Click Browse Product Model
    $('#obtBrowsePdtModel').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowsePdtModelOption = oPdtBrowsePdtModel({
                'tReturnInputCode': 'oetPdtPmoCode',
                'tReturnInputName': 'oetPdtPmoName',
            });
            JCNxBrowseData('oPdtBrowsePdtModelOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Click Browse Product Touch Group
    $('#obtBrowsePdtTouchGrp').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowsePdtTouchGrpOption = oPdtBrowsePdtTouchGrp({
                'tReturnInputCode': 'oetPdtTcgCode',
                'tReturnInputName': 'oetPdtTcgName',
            });
            JCNxBrowseData('oPdtBrowsePdtTouchGrpOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    //Click Browse Poduct Unit
    $('#obtAddProductUnit').click(function() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowseUnitOption = oPdtBrowseUnit({
                'tReturnInputCode': 'ohdPdtUnitCode',
                'tReturnInputName': 'ohdPdtUnitName',
                'tNextFuncName': 'JSxAddDataUnitPackSizeToTable'
            });
            JCNxBrowseData('oPdtBrowseUnitOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    //Click Browse Poduct Unit
    $('#obtAddPdtSpcWah').click(function() {
        var nStaSession = JCNxFuncChkSessionExpired();
        alert(nStaSession);
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            window.oPdtBrowseUnitOption = oPdtBrowseUnit({
                'tReturnInputCode': 'ohdPdtUnitCode',
                'tReturnInputName': 'ohdPdtUnitName',
                'tNextFuncName': 'JSxAddDataUnitPackSizeToTable'
            });
            JCNxBrowseData('oPdtBrowseUnitOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    /** ================================================================================================================================= */

    /** =================================================== Function And Event Pack Size ================================================ */

    var oPdtBrowseColor = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['product/pdtcolor/pdtcolor', 'tCLRTitle'],
            Table: {
                Master: 'TCNMPdtColor',
                PK: 'FTClrCode'
            },
            Join: {
                Table: ['TCNMPdtColor_L'],
                On: ['TCNMPdtColor_L.FTClrCode = TCNMPdtColor.FTClrCode AND TCNMPdtColor_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtcolor/pdtcolor',
                ColumnKeyLang: ['tCLRCode', 'tCLRName'],
                ColumnsSize: ['15%', '85%'],
                DataColumns: ['TCNMPdtColor.FTClrCode', 'TCNMPdtColor_L.FTClrName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 5,
                OrderBy: ['TCNMPdtColor.FTClrCode'],
                SourceOrder: "DESC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtColor.FTClrCode"],
                Text: [tInputReturnName, "TCNMPdtColor_L.FTClrName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
            },
            RouteAddNew: 'productColor',
            BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    var oPdtBrowseSize = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['product/pdtsize/pdtsize', 'tPSZTitle'],
            Table: {
                Master: 'TCNMPdtSize',
                PK: 'FTPszCode'
            },
            Join: {
                Table: ['TCNMPdtSize_L'],
                On: ['TCNMPdtSize_L.FTPszCode = TCNMPdtSize.FTPszCode AND TCNMPdtSize_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtsize/pdtsize',
                ColumnKeyLang: ['tPSZCode', 'tPSZName'],
                ColumnsSize: ['15%', '85%'],
                DataColumns: ['TCNMPdtSize.FTPszCode', 'TCNMPdtSize_L.FTPszName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 20,
                OrderBy: ['TCNMPdtSize.FTPszCode'],
                SourceOrder: "DESC"
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtSize.FTPszCode"],
                Text: [tInputReturnName, "TCNMPdtSize_L.FTPszName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
            },
            RouteAddNew: 'productSize',
            BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    var oPdtBrowseShop = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tSQLWhere = poReturnInput.tSQLWhere;
        var oOptionReturn = {
            Title: ['authen/user/user', 'tBrowseSHPTitle'],
            Table: {
                Master: 'TCNMShop',
                PK: 'FTShpCode'
            },
            Join: {
                Table: ['TCNMShop_L'], //,'TRTMPdtRental'
                On: ['TCNMShop_L.FTShpCode = TCNMShop.FTShpCode AND TCNMShop_L.FTBchCode = TCNMShop.FTBchCode  AND TCNMShop_L.FNLngID = ' + nLangEdits] //'TRTMPdtRental.FTShpCode = TCNMShop.FTShpCode'
            },
            Where: {
                Condition: [
                    tSQLWhere
                ]
            },
            GrideView: {
                ColumnPathLang: 'authen/user/user',
                ColumnKeyLang: ['tBrowseSHPCode', 'tBrowseSHPName'],
                ColumnsSize: ['15%', '85%'],
                DataColumns: ['TCNMShop.FTShpCode', 'TCNMShop_L.FTShpName'],
                DataColumnsFormat: ['', ''],
                DistinctField: [0],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMShop.FDCreateOn DESC'],
                // SourceOrder: "DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMShop.FTShpCode"],
                Text: [tInputReturnName, "TCNMShop_L.FTShpName"],
            }
            // RouteAddNew : 'productSize',
            // BrowseLev : nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    $('#obtModalPszBrowseColor').click(function(e) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            $('#odvModalMngUnitPackSize').modal("hide");
            window.oPdtBrowseColorOption = oPdtBrowseColor({
                'tReturnInputCode': 'oetModalPszClrCode',
                'tReturnInputName': 'oetModalPszClrName',
                'tNextFuncName': 'JSxShowModalMngUnitPackSize'
            });
            JCNxBrowseData('oPdtBrowseColorOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    $('#obtModalPszBrowseSize').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxCheckPinMenuClose();
            $('#odvModalMngUnitPackSize').modal("hide");
            window.oPdtBrowseSizeOption = oPdtBrowseSize({
                'tReturnInputCode': 'oetModalPszSizeCode',
                'tReturnInputName': 'oetModalPszSizeName',
                'tNextFuncName': 'JSxShowModalMngUnitPackSize'
            })
            JCNxBrowseData('oPdtBrowseSizeOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    $('#obtBrowsePdtRetShp').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            $('#odvModalMngUnitPackSize').modal("hide");
            window.oPdtBrowseShopRentOption = oPdtBrowseShop({
                'tReturnInputCode': 'oetModalShpCode',
                'tReturnInputName': 'oetModalShpName'
            })
            JCNxBrowseData('oPdtBrowseShopRentOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Function: Function Get Data Product PackSize
    // Parameters:  Object In Next Funct Modal Browse
    // Creator:	08/02/2019 wasin(Yoshi)
    // Last Update : 09/06/2020 napat(jame)
    // Return: object View Product Set
    // Return Type: object
    function JSxAddDataUnitPackSizeToTable(poDataNextFunc) {
        // กรณีเลือกหน่วยจาก Browse
        if (poDataNextFunc[0] != null) {
            var aDataUnitFact = [];
            $('.xWPdtUnitFact').each(function() {
                aDataUnitFact.push($(this).val());
            });

            // ตรวจสอบหน่วยที่เลือกมา
            var aPdtUnitCode = [];
            for (var i = 0; i < poDataNextFunc.length; i++) {
                aColDatas = JSON.parse(poDataNextFunc[i]);
                if (aColDatas != null) {
                    aPdtUnitCode.push(aColDatas[0]);
                }
            }
            // ตรวจสอบหน่วยที่อยู่ในเบส
            var aTrUnitList = [];
            $('.xWPdtDataUnitRow').each(function() {
                aTrUnitList.push($(this).attr('data-puncode'));
            });
            // ตรวจสอบระหว่าง หน่วยที่อยู่ในเบส กับหน่วยที่เลือก
            var aDiffPunCode = [];
            jQuery.grep(aTrUnitList, function(el) {
                if (jQuery.inArray(el, aPdtUnitCode) == -1) aDiffPunCode.push(el);
            });
            // ถ้าพบหน่วยแตกต่าง ก็ให้ทำการลบ
            if (aDiffPunCode.length > 0) {
                JSxPdtDelPszUnitInTable(4, aDiffPunCode);
            }

            var tPdtCode = $('#oetPdtCode').val();
            $.ajax({
                type: "POST",
                url: "productAddPackSizeUnit",
                data: {
                    FTPdtCode: tPdtCode,
                    aPunCode: poDataNextFunc,
                    paDataUnitFact: aDataUnitFact
                },
                cache: false,
                timeout: 0,
                async: false,
                success: function(tResult) {
                    JsxCallPackSizeDataTable(tPdtCode);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            var nCountTR = 0;
            $('.xWPdtDataUnitRow').each(function() {
                nCountTR++;
            });

            // กรณีไม่เลือกรายการ ตรวจสอบว่ามีหน่วยที่อยู่ใน DataTable ไหม ?
            // ถ้ามีก็ให้ไปลบในตาราง TsysMasTmp ทั้งหมด
            if (nCountTR > 0) {
                JSxPdtDelPszUnitInTable(3, '');
            }
        }

        // var aPdtUnitCode    = [];
        // for(var i = 0; i < poDataNextFunc.length; i++){
        //     aColDatas   = JSON.parse(poDataNextFunc[i]);
        //     if(aColDatas != null){
        //         aPdtUnitCode.push(aColDatas[0]);
        //     }
        // }
        // if(aPdtUnitCode.length !== 0){
        //     $.ajax({
        //         type: "POST",
        //         url: "productGetPackSizeUnit",
        //         data: {aPdtUnitCode : aPdtUnitCode},
        //         cache: false,
        //         timeout: 0,
        //         async: false,
        //         success: function(oResult){
        //             var aReturnData = JSON.parse(oResult);
        //             console.log(aReturnData);
        //             if(aReturnData['nStaEvent'] == '1'){
        //                 $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable table tbody .xWPdtPackSizeNoData').remove();
        //                 var aPdtPackSizeRow   = aReturnData['aPdtPackSizeRow'];
        //                 $.each(aPdtPackSizeRow,function(nKey,aValue){
        //                     var tPdtPunFindRowDup   = 'otrPdtDataUnitRow'+aValue['tPdtUnitCode'];
        //                     var nPdtPunFindRowDup   = $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable table tbody').find('#'+tPdtPunFindRowDup).length
        //                     if(nPdtPunFindRowDup == 0){
        //                         $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable table tbody').append(aValue['tPdtPackSizeRow']).css('opacity',0).slideDown('slow')
        //                         .animate(
        //                             { opacity: 1 },
        //                             { queue: false, duration: 'slow' }
        //                         );
        //                     }
        //                 });
        //             }else{
        //                 var tMessageError   = aReturnData['tStaMessg'];
        //                 FSvCMNSetMsgErrorDialog(tMessageError);
        //             }
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             JCNxResponseError(jqXHR,textStatus,errorThrown);
        //         }
        //     });
        // }
    }

    // Function: Func. Call Back Show Modal Mng Unit Pack Size
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // Return: Open Pop Up Modal Manage PackSize Unit
    // Return Type: -
    function JSxShowModalMngUnitPackSize() {
        $('#odvModalMngUnitPackSize').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#odvModalMngUnitPackSize').modal("show");
    }

    // Function: Func.Manage Unit PackSize
    // Parameters: Obj Event Click
    // Creator:	11/02/2019 wasin(Yoshi)
    // Return: open Pop Up Modal Manage PackSize Unit
    // Return Type: -
    function JSxPdtMngPszUnitInTable(oEvent) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var tPszUnitCode = $(oEvent).parents('.xWPdtDataUnitRow').data('puncode');
            var tPszUnitName = $(oEvent).parents('.xWPdtDataUnitRow').data('punname');
            var tPszUnitFact = $("#otrPdtDataUnitRow" + tPszUnitCode + " #oetPdtUnitFact" + tPszUnitCode).val();
            var tPszGrade = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtGrandRow" + tPszUnitCode).val();
            var tPszWeight = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtWeightRow" + tPszUnitCode).val();
            var tPszClrCode = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtClrCodeRow" + tPszUnitCode).val();
            var tPszClrName = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtClrNameRow" + tPszUnitCode).val();
            var tPszSizeCode = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtSizeCodeRow" + tPszUnitCode).val();
            var tPszSizeName = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtSizeNameRow" + tPszUnitCode).val();
            var tPszUnitDim = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtUnitDimRow" + tPszUnitCode).val();
            var tPszPackageDim = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtPkgDimRow" + tPszUnitCode).val();
            var tPszStaAlwPick = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtStaAlwPickRow" + tPszUnitCode).val();
            var tPszStaAlwPoHQ = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtStaAlwPoHQRow" + tPszUnitCode).val();
            var tPszStaAlwBuy = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtStaAlwBuyRow" + tPszUnitCode).val();
            var tPszStaAlwSale = $(oEvent).parents('.xWPdtDataUnitRow').find("#ohdPdtStaAlwSaleRow" + tPszUnitCode).val();

            $('#ohdModalPszUnitCode').val(tPszUnitCode);
            $('#ohdModalPszUnitName').val(tPszUnitName);
            $("#odvModalMngUnitPackSize #olbModalPszUnitTitle").text('<?php echo language("product/product/product", "tPDTViewPackMDUnit"); ?>' + " : " + tPszUnitName);
            $('#oetModalPszUnitFact').val(tPszUnitFact);
            $('#oetModalPszGrade').val(tPszGrade);
            $('#oetModalPszWeight').val(tPszWeight);
            $('#oetModalPszClrCode').val(tPszClrCode);
            $('#oetModalPszClrName').val(tPszClrName);
            $('#oetModalPszSizeCode').val(tPszSizeCode);
            $('#oetModalPszSizeName').val(tPszSizeName);

            // Chack Data Unit Dim
            if (tPszUnitDim != "") {
                var aSplitPszUnitDim = tPszUnitDim.split(";");
                $('#oetModalPszUnitDimWidth').val(aSplitPszUnitDim[0]);
                $('#oetModalPszUnitDimLength').val(aSplitPszUnitDim[1]);
                $('#oetModalPszUnitDimHeight').val(aSplitPszUnitDim[2]);
            } else {
                $('#oetModalPszUnitDimWidth').val('');
                $('#oetModalPszUnitDimLength').val('');
                $('#oetModalPszUnitDimHeight').val('');
            }

            // Chack Data Pkg Dim
            if (tPszPackageDim != "") {
                var aSplitPszPackageDim = tPszPackageDim.split(";");
                $('#oetModalPszPackageDimWidth').val(aSplitPszPackageDim[0]);
                $('#oetModalPszPackageDimLength').val(aSplitPszPackageDim[1]);
                $('#oetModalPszPackageDimHeight').val(aSplitPszPackageDim[2]);
            } else {
                $('#oetModalPszPackageDimWidth').val('');
                $('#oetModalPszPackageDimLength').val('');
                $('#oetModalPszPackageDimHeight').val('');
            }
            // Chk Status Allow Pick
            if (tPszStaAlwPick != "" && tPszStaAlwPick == 1) {
                $("#ocbModalPszStaAlwPick").prop('checked', true);
            } else {
                $("#ocbModalPszStaAlwPick").prop('checked', false);
            }

            // Chk Status Allow HQ
            if (tPszStaAlwPoHQ != "" && tPszStaAlwPoHQ == 1) {
                $("#ocbModalPszStaAlwPoHQ").prop('checked', true);
            } else {
                $("#ocbModalPszStaAlwPoHQ").prop('checked', false);
            }

            // Chk Status Allow Buy
            if (tPszStaAlwBuy != "" && tPszStaAlwBuy == 1) {
                $("#ocbModalPszStaAlwBuy").prop('checked', true);
            } else {
                $("#ocbModalPszStaAlwBuy").prop('checked', false);
            }

            // Chk Status Allow Sale
            if (tPszStaAlwSale != "" && tPszStaAlwSale == 1) {
                $("#ocbModalPszStaAlwSale").prop('checked', true);
            } else {
                $("#ocbModalPszStaAlwSale").prop('checked', false);
            }

            $('#odvModalMngUnitPackSize').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#odvModalMngUnitPackSize').modal('show');
            $.getScript("application/modules/common/assets/src/jFormValidate.js")
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // Function: Func.Save Manage Unit PackSize
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // Return: Save Pop Up Modal Manage PackSize Unit
    // Return Type: -
    function JSxPdtSaveMngPszUnitInTable() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var tPdtCode = $('#oetPdtCode').val();
            var tSaveMngPunCode = $('#ohdModalPszUnitCode').val();

            var tSaveMngUnitFact = $('#oetModalPszUnitFact').val();
            var tSaveMngPszGrade = $('#oetModalPszGrade').val();
            var tSaveMngPszWeight = $('#oetModalPszWeight').val();
            var tSaveMngPszClrCode = $('#oetModalPszClrCode').val();
            var tSaveMngPszClrName = $('#oetModalPszClrName').val();
            var tSaveMngPszSizeCode = $('#oetModalPszSizeCode').val();
            var tSaveMngPszSizeName = $('#oetModalPszSizeName').val();
            // Concat String Pack Size Unit Dim
            var tSaveMngPszUnitDimWidth = $('#oetModalPszUnitDimWidth').val();
            var tSaveMngPszUnitDimLength = $('#oetModalPszUnitDimLength').val();
            var tSaveMngPszUnitDimHeight = $('#oetModalPszUnitDimHeight').val();
            var tConcPszUnitDim = tSaveMngPszUnitDimWidth + ';' + tSaveMngPszUnitDimLength + ';' + tSaveMngPszUnitDimHeight
            // Concat String Pack Size Package Dim
            var tSaveMngPszPackageDimWidth = $('#oetModalPszPackageDimWidth').val();
            var tSaveMngPszPackageDimLength = $('#oetModalPszPackageDimLength').val();
            var tSaveMngPszPackageDimHeight = $('#oetModalPszPackageDimHeight').val();
            var tConcPszPackageDim = tSaveMngPszPackageDimWidth + ';' + tSaveMngPszPackageDimLength + ';' + tSaveMngPszPackageDimHeight
            // Status Manage Pack Size
            var tSaveMngStaPszStaAlwPick = $('#ocbModalPszStaAlwPick').is(':checked') ? '1' : '2';
            var tSaveMngStaPszStaAlwPoHQ = $('#ocbModalPszStaAlwPoHQ').is(':checked') ? '1' : '2';
            var tSaveMngStaPszStaAlwBuy = $('#ocbModalPszStaAlwBuy').is(':checked') ? '1' : '2';
            var tSaveMngStaPszStaAlwSale = $('#ocbModalPszStaAlwSale').is(':checked') ? '1' : '2';

            $.ajax({
                type: "POST",
                url: "productUpdatePackSizeUnit",
                data: {
                    FTPdtCode: tPdtCode,
                    FTPunCode: tSaveMngPunCode,
                    FCPdtUnitFact: tSaveMngUnitFact,
                    FTPdtGrade: tSaveMngPszGrade,
                    FCPdtWeight: tSaveMngPszWeight,
                    FTClrCode: tSaveMngPszClrCode,
                    FTClrName: tSaveMngPszClrName,
                    FTPszCode: tSaveMngPszSizeCode,
                    FTPszName: tSaveMngPszSizeName,
                    FTPdtUnitDim: tConcPszUnitDim,
                    FTPdtPkgDim: tConcPszPackageDim,
                    FTPdtStaAlwPick: tSaveMngStaPszStaAlwPick,
                    FTPdtStaAlwPoHQ: tSaveMngStaPszStaAlwPoHQ,
                    FTPdtStaAlwBuy: tSaveMngStaPszStaAlwBuy,
                    FTPdtStaAlwSale: tSaveMngStaPszStaAlwSale,
                    pnUpdateType: 1
                },
                cache: false,
                timeout: 0,
                async: false,
                success: function(oResult) {
                    var aDataReturn = JSON.parse(oResult);
                    if (aDataReturn['rtCode'] == 1) {
                        // Set Value In Input PackSize Data Row
                        $('#oetPdtUnitFact' + tSaveMngPunCode).val(tSaveMngUnitFact);
                        $('#oetPdtUnitFact' + tSaveMngPunCode).parent().parent().find('.xWShowInLine').val(tSaveMngUnitFact);
                        $('#ohdPdtGrandRow' + tSaveMngPunCode).val(tSaveMngPszGrade);
                        $('#ohdPdtWeightRow' + tSaveMngPunCode).val(tSaveMngPszWeight);
                        $('#ohdPdtClrCodeRow' + tSaveMngPunCode).val(tSaveMngPszClrCode);
                        $('#ohdPdtClrNameRow' + tSaveMngPunCode).val(tSaveMngPszClrName);
                        $('#ohdPdtSizeCodeRow' + tSaveMngPunCode).val(tSaveMngPszSizeCode);
                        $('#ohdPdtSizeNameRow' + tSaveMngPunCode).val(tSaveMngPszSizeName);
                        $('#ohdPdtUnitDimRow' + tSaveMngPunCode).val(tConcPszUnitDim);
                        $('#ohdPdtPkgDimRow' + tSaveMngPunCode).val(tConcPszPackageDim);
                        $('#ohdPdtStaAlwPickRow' + tSaveMngPunCode).val(tSaveMngStaPszStaAlwPick);
                        $('#ohdPdtStaAlwPoHQRow' + tSaveMngPunCode).val(tSaveMngStaPszStaAlwPoHQ);
                        $('#ohdPdtStaAlwBuyRow' + tSaveMngPunCode).val(tSaveMngStaPszStaAlwBuy);
                        $('#ohdPdtStaAlwSaleRow' + tSaveMngPunCode).val(tSaveMngStaPszStaAlwSale);
                    } else {
                        console.log(aDataReturn['rtDesc']);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });

            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow'+tSaveMngPunCode+' #oetPdtUnitFact'+tSaveMngPunCode).val(tSaveMngUnitFact);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtGrandRow'+tSaveMngPunCode).val(tSaveMngPszGrade);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtWeightRow'+tSaveMngPunCode).val(tSaveMngPszWeight);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtClrCodeRow'+tSaveMngPunCode).val(tSaveMngPszClrCode);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtClrNameRow'+tSaveMngPunCode).val(tSaveMngPszClrName);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtSizeCodeRow'+tSaveMngPunCode).val(tSaveMngPszSizeCode);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtSizeNameRow'+tSaveMngPunCode).val(tSaveMngPszSizeName);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtUnitDimRow'+tSaveMngPunCode).val(tConcPszUnitDim);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtPkgDimRow'+tSaveMngPunCode).val(tConcPszPackageDim);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtStaAlwPickRow'+tSaveMngPunCode).val(tSaveMngStaPszStaAlwPick);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtStaAlwPoHQRow'+tSaveMngPunCode).val(tSaveMngStaPszStaAlwPoHQ);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtStaAlwBuyRow'+tSaveMngPunCode).val(tSaveMngStaPszStaAlwBuy);
            // $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtDataUnitRow'+tSaveMngPunCode+' #ohdPdtStaAlwSaleRow'+tSaveMngPunCode).val(tSaveMngStaPszStaAlwSale);
            $('#odvModalMngUnitPackSize').modal("toggle");
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // Function: Func.Delete Unit Pack Size In Table
    // Parameters: Obj Event Click
    // Creator:	11/02/2019 wasin(Yoshi)
    // Last Update: 09/06/2020 Napat(Jame)
    // Return: Delete Row Data Unit Pack Size In Table
    // Return Type: -
    function JSxPdtDelPszUnitInTable(ptType, paPunCode) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {

            // Last Update Napat(Jame) 09/06/2020
            // เพิ่มฟังค์ชั่นให้มีลบหน่วย ทั้งหมด
            var aPunCode = paPunCode;
            var tPdtCode = $('#oetPdtCode').val();
            var nTypeAction = ptType;

            // if(ptType == 2){
            //     tPunCode        = "";
            //     tPdtCode        = "";
            //     nTypeAction     = 2;
            // }else{
            //     tPunCode        = $(oEvent).parents('.xWPdtDataUnitRow').data('puncode');
            //     tPdtCode        = $(oEvent).parents('.xWPdtDataUnitRow').data('pdtcode');
            //     nTypeAction     = 1;
            // }

            $.ajax({
                type: "POST",
                url: "productDelPackSizeUnit",
                data: {
                    FTPunCode: aPunCode,
                    FTPdtCode: tPdtCode,
                    pnTypeAction: nTypeAction
                },
                cache: false,
                timeout: 0,
                async: false,
                success: function(tResult) {
                    // $('#odvPdtSetPackSizeTable').html(tResult);
                    JsxCallPackSizeDataTable(tPdtCode);
                    JCNxLayoutControll();
                    JCNxCloseLoading();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });

            // var tObjUnitDelName = "#otrPdtDataUnitRow"+nPunCode+",#otrPdtUnitBarCodeRow"+nPunCode;
            // $(tObjUnitDelName).fadeOut(500,function(){
            //     $(this).remove();
            //     var tPdtUnitCode = '';
            //     var tPdtUnitName = '';
            //     $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable tbody .xWPdtDataUnitRow').each(function(){
            //         var tCode = $(this).data('puncode');
            //         var tName = $(this).data('punname');
            //         tPdtUnitCode += tCode+','
            //         tPdtUnitName += tName+','
            //     });
            //     $('#ohdPdtUnitCode').val(tPdtUnitCode.substring(0,tPdtUnitCode.length - 1));
            //     $('#ohdPdtUnitName').val(tPdtUnitName.substring(0,tPdtUnitName.length - 1));
            //     var nCoutePdtUnitPszRow = $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable tbody').children().length;
            //     if(nCoutePdtUnitPszRow == 0){
            //         $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable tbody').append($('<tr>')
            //         .attr('class','xWPdtPackSizeNoData')
            //             .append($('<td>')
            //             .attr('class','text-center xCNTextDetail2')
            //             .attr('colspan','99')
            //             .text('<?php echo language("common/main/main", "tCMNNotFoundData"); ?>')
            //             )
            //         )
            //     }
            // });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    /** ================================================================================================================================= */

    /** =================================================== Function And Event BarCode ================================================== */

    var oPdtBrowseLocation = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['product/pdtlocation/pdtlocation', 'tLOCTitle'],
            Table: {
                Master: 'TCNMPdtLoc',
                PK: 'FTPlcCode'
            },
            Join: {
                Table: ['TCNMPdtLoc_L'],
                On: ['TCNMPdtLoc_L.FTPlcCode = TCNMPdtLoc.FTPlcCode AND TCNMPdtLoc_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'product/pdtlocation/pdtlocation',
                ColumnKeyLang: ['tLOCFrmLocCode', 'tLOCFrmLocName'],
                ColumnsSize: ['10%', '75%'],
                DataColumns: ['TCNMPdtLoc.FTPlcCode', 'TCNMPdtLoc_L.FTPlcName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMPdtLoc.FDCreateOn DESC'],
                // SourceOrder: "DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMPdtLoc.FTPlcCode"],
                Text: [tInputReturnName, "TCNMPdtLoc_L.FTPlcName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
            },
            RouteAddNew: 'productLocation',
            BrowseLev: nStaPdtBrowseType
        };
        return oOptionReturn;
    }

    // Event Browse Location In Modal
    $('#obtModalAebBrowsePdtLocation').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Create By Witsarut 04/10/2019
            JSxCheckPinMenuClose();
            // Create By Witsarut 04/10/2019
            $('#odvModalAddEditBarCode').modal("hide");
            window.oPdtBrowseLocationOption = oPdtBrowseLocation({
                'tReturnInputCode': 'oetModalAebPlcCode',
                'tReturnInputName': 'oetModalAebPlcName',
                'tNextFuncName': 'JSxShowModalAddBarCode'
            })
            JCNxBrowseData('oPdtBrowseLocationOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    $('.xWPDTSubmitAddBar').off('click');
    $('.xWPDTSubmitAddBar').on('click', function() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxPdtSaveBarCodeInUnitPack();
        } else {
            JCNxShowMsgSessionExpired();
        }
    });


    // Function: Func. Call Back Show Modal Add BarCode
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // Return: Open Pop Up Modal Manage PackSize Unit
    // Return Type: -
    function JSxShowModalAddBarCode() {
        // Clear Validate BarCode Input
        $('#oetModalAebBarCode').parents('.form-group').removeClass("has-error");
        $('#oetModalAebBarCode').parents('.form-group').removeClass("has-success");
        $('#oetModalAebBarCode').parents('.form-group').find(".help-block").fadeOut('slow').remove();
        // Clear Validate Product Location Input
        $('#oetModalAebPlcName').parents('.form-group').removeClass("has-error");
        $('#oetModalAebPlcName').parents('.form-group').removeClass("has-success");
        $('#oetModalAebPlcName').parents('.form-group').find(".help-block").fadeOut('slow').remove();
        $('#odvModalAddEditBarCode').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#odvModalAddEditBarCode').modal("show");
    }

    // Function: Func. Add BarCode In Unit Pack
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // LastUpdate: 13/02/2019 wasin(Yoshi)
    // Return: -
    // Return Type: -
    function JSxPdtCallModalAddEditBardCode(oEvent, tCallAddOrEdit) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxPdtModalBarCodeClear();
            $('.xWModalBarCodeDataTable').html('');
            // Set Add/Edit In Input Hidden In Modal Add Bracode
            // $('#ohdModalAebStaCallAddOrEdit').val(tCallAddOrEdit);
            if (tCallAddOrEdit == 'Add') {
                // Get Data Unit Pack Size And Add Value Into Input And Append Text Label Unit Title
                var tAebPdtCode = $('#oetPdtCode').val();
                var tAebUnitCode = $(oEvent).parents('.xWPdtDataUnitRow').data('puncode');
                var tAebUnitName = $(oEvent).parents('.xWPdtDataUnitRow').data('punname');

                JCNxOpenLoading();
                JSxPdtGetBarCodeDataByID(tAebPdtCode, tAebUnitCode);

                $('#ohdModalFTPunCode').val(tAebUnitCode);
                $('#ohdModalFTPdtCode').val(tAebPdtCode);
                $('#ospTxtPdtCode').text(tAebPdtCode);
                $('#ospTxtPdtName').text($('#oetPdtName').val());
                $('#ospTxtPunName').text(tAebUnitName);

                // $('#ohdModalAebUnitCode').val(tAebUnitCode);
                // $('#ohdModalAebUnitName').val(tAebUnitName);
                $("#odvModalAddEditBarCode #olbModalAebUnitTitle").text('<?php echo language("product/product/product", "tPDTViewPackMDUnit"); ?>' + " : " + tAebUnitName);
                // Clear Value In Input
                // $('#oetModalAebBarCode').val('');
                // $('#oetModalAebBarCode').prop('disabled',false).removeAttr('style');
                // $('#oetModalAebPlcCode').val('');
                // $('#oetModalAebPlcName').val('');
                // $('#ocbModalAebBarStaUse').prop("checked",false);
                // $('#ocbModalAebBarStaAlwSale').prop("checked",false);
                // Clear Validate BarCode Input
                // $('#oetModalAebBarCode').parents('.form-group').removeClass("has-error");
                // $('#oetModalAebBarCode').parents('.form-group').removeClass("has-success");
                // $('#oetModalAebBarCode').parents('.form-group').find(".help-block").fadeOut('slow').remove();

                // Clear Validate Product Location Input
                // $('#oetModalAebPlcName').parents('.form-group').removeClass("has-error");
                // $('#oetModalAebPlcName').parents('.form-group').removeClass("has-success");
                // $('#oetModalAebPlcName').parents('.form-group').find(".help-block").fadeOut('slow').remove();

                // Show Modal
                $('#odvModalAddEditBarCode').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#odvModalAddEditBarCode').modal('show');
                $.getScript("application/modules/common/assets/src/jFormValidate.js");
            } else if (tCallAddOrEdit == 'Edit') {
                // Get Value For Input
                var tAebUnitCode = $(oEvent).parents('.xWPdtUnitBarCodeRow').data('puncode');
                var tAebUnitName = $(oEvent).parents('.xWPdtUnitBarCodeRow').data('punname');
                var tAebBarcode = $(oEvent).parents('.xWBarCodeItem').find('.xWPdtAebBarCodeItem').val();
                var tAebPlcCode = $(oEvent).parents('.xWBarCodeItem').find('.xWPdtAebPlcCodeItem').val();
                var tAebPlcName = $(oEvent).parents('.xWBarCodeItem').find('.xWPdtAebPlcNameItem').val();
                var tAebBarStaUseItem = $(oEvent).parents('.xWBarCodeItem').find('.xWPdtAebBarStaUseItem').val();
                var tAebBarStaAlwSaleItem = $(oEvent).parents('.xWBarCodeItem').find('.xWPdtAebBarStaAlwSaleItem').val();
                // console.log(tAebUnitCode+'/'+tAebUnitName+'/'+tAebBarcode+'/'+tAebPlcCode+'/'+tAebPlcName+'/'+tAebBarStaUseItem+'/'+tAebBarStaAlwSaleItem);
                $('#odvModalAddEditBarCode #ohdModalAebUnitCode').val(tAebUnitCode);
                $('#odvModalAddEditBarCode #ohdModalAebUnitName').val(tAebUnitName);
                $("#odvModalAddEditBarCode #olbModalAebUnitTitle").text('<?php echo language("product/product/product", "tPDTViewPackMDUnit"); ?>' + " : " + tAebUnitName);
                // Set Value In Input
                $('#odvModalAddEditBarCode #oetModalAebBarCode').val(tAebBarcode);
                $('#odvModalAddEditBarCode #oetModalAebBarCode').prop("disabled", true).css('cursor', 'not-allowed');
                $('#odvModalAddEditBarCode #oetModalAebPlcCode').val(tAebPlcCode);
                $('#odvModalAddEditBarCode #oetModalAebPlcName').val(tAebPlcName);
                (tAebBarStaUseItem == 1) ? $('#odvModalAddEditBarCode #ocbModalAebBarStaUse').prop("checked", true): $('#odvModalAddEditBarCode #ocbModalAebBarStaUse').prop("checked", false);
                (tAebBarStaAlwSaleItem == 1) ? $('#odvModalAddEditBarCode #ocbModalAebBarStaAlwSale').prop("checked", true): $('#odvModalAddEditBarCode #ocbModalAebBarStaAlwSale').prop("checked", false);
                // Clear Validate BarCode Input
                $('#odvModalAddEditBarCode #oetModalAebBarCode').parents('.form-group').removeClass("has-error");
                $('#odvModalAddEditBarCode #oetModalAebBarCode').parents('.form-group').removeClass("has-success");
                $('#odvModalAddEditBarCode #oetModalAebBarCode').parents('.form-group').find(".help-block").fadeOut('slow').remove();

                // Clear Validate Product Location Input
                $('#odvModalAddEditBarCode #oetModalAebPlcName').parents('.form-group').removeClass("has-error");
                $('#odvModalAddEditBarCode #oetModalAebPlcName').parents('.form-group').removeClass("has-success");
                $('#odvModalAddEditBarCode #oetModalAebPlcName').parents('.form-group').find(".help-block").fadeOut('slow').remove();

                // Show Modal
                $('#odvModalAddEditBarCode').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#odvModalAddEditBarCode').modal('show');
                $.getScript("application/modules/common/assets/src/jFormValidate.js")
            } else {}
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // Function: Func.Save BarCode In Unit Pack
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // LastUpdate: 13/02/2019 wasin(Yoshi)
    // Return: -
    // Return Type: -
    function JSxPdtSaveBarCodeInUnitPack() {
        $('#ofmModalAebBarCode').validate({
            rules: {
                oetModalAebBarCode: "required"
            },
            messages: {
                oetModalAebBarCode: {
                    "required": $('#oetModalAebBarCode').attr('data-validate-required'),
                }
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
                error.addClass("help-block");
                if (element.prop("type") === "checkbox") {
                    error.appendTo(element.parent("label"));
                } else {
                    var tCheck = $(element.closest('.form-group')).find('.help-block').length;
                    if (tCheck == 0) {
                        error.appendTo(element.closest('.form-group')).trigger('change');
                    }
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass("has-error").removeClass("has-success");
            },
            unhighlight: function(element, errorClass, validClass) {
                var nStaCheckValid = $(element).parents('.form-group').find('.help-block').length
                if (nStaCheckValid != 0) {
                    $(element).closest('.form-group').addClass("has-success").removeClass("has-error");
                }
            },
            submitHandler: function(form) {

                JCNxOpenLoading();

                $.ajax({
                    type: "POST",
                    url: "productUpdateBarCode",
                    data: {
                        'FTPdtCode': $('#ohdModalFTPdtCode').val(),
                        'FTBarCode': $('#oetModalAebBarCode').val(),
                        'tOldBarCode': $('#oetModalAebOldBarCode').val(),
                        'FTPunCode': $('#ohdModalFTPunCode').val(),
                        'FTPlcCode': $('#oetModalAebPlcCode').val(),
                        'FTPlcName': $('#oetModalAebPlcName').val(),
                        'FTSplCode': $('#oetModalAesSplCode').val(),
                        'FTSplName': $('#oetModalAesSplName').val(),
                        'StatusAddEdit': $('#oetEditData').val(),
                        'FTBarStaUse': ($('#ocbModalAebBarStaUse').is(':checked')) ? '1':'',
                        'FTBarStaAlwSale': ($('#ocbModalAebBarStaAlwSale').is(':checked')) ? '1':'',
                        'FTSplStaAlwPO': ($('#ocbModalAesSplStaAlwPO').is(':checked')) ? '1':'',
                    },
                    async: false,
                    cache: false,
                    timeout: 0,
                    success: function(oResult) {
                        var aReturn = JSON.parse(oResult);
                        if (aReturn['nStaQuery'] == 1) {
                            var test = $('#ohdModalFTPdtCode').val();
                            JSxPdtGetBarCodeDataByID($('#ohdModalFTPdtCode').val(), $('#ohdModalFTPunCode').val());
                            var nCount = parseInt($('#ohdPdtBarCodeRow' + $('#ohdModalFTPunCode').val()).val());
                            $('#ohdPdtBarCodeRow' + $('#ohdModalFTPunCode').val()).val(nCount + 1);
                        } else {
                            JCNxCloseLoading();
                            alert('สินค้านี้มีบาร์โค๊ดนี้แล้ว');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            }
        });
    }



    // Function: Func.Check Barcode Duplicate In DB
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // LastUpdate: 13/02/2019 wasin(Yoshi)
    // Return: Status Check Barcode Duplicate In DB
    // Return Type: Numeric
    function JSnChkDupBarcodeItemInDB(ptPdtCode, ptBarcode) {
        var nDataChkReturn = "";
        $.ajax({
            type: "POST",
            url: "productChkBarCodeDup",
            data: {
                tPdtCode: ptPdtCode,
                tBarCode: ptBarcode
            },
            cache: false,
            timeout: 0,
            async: false,
            success: function(oResult) {
                var aDataChkDup = JSON.parse(oResult);
                if (aDataChkDup['nStaEvent'] == 1) {
                    nDataChkReturn = aDataChkDup['nStaBarCodeDup'];
                } else {
                    var tMsgError = aDataReturn['tStaMessg'];
                    FSvCMNSetMsgErrorDialog(tMsgError);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
        return nDataChkReturn;
    }

    // Function: Func.Check Barcode Duplicate In Html Add
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // LastUpdate: 13/02/2019 wasin(Yoshi)
    // Return: Status Check Barcode Duplicate In HTML Div
    // Return Type: Numeric
    function JSnChkDupBarcodeItemInHtml(ptBarcode) {
        var aDataBarCode = [];
        $('#odvPdtSetPackSizeTable .xWPdtUnitBarCodeRow .xWBarCodeItem .xWPdtAebBarCodeItem').each(function() {
            var tDataBarCode = $(this).val();
            aDataBarCode.push(tDataBarCode.toString());
        });
        if (jQuery.inArray(ptBarcode, aDataBarCode) != -1) {
            return 1;
        } else {
            return 0;
        }
    }

    // Function: Func.Delete Barcode
    // Parameters: Obj Event Click
    // Creator:	13/02/2019 wasin(Yoshi)
    // Return: -
    // Return Type: -
    function JSxPdtDelBarCodeInTable(oEvent) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var tMDAebPunCode = $(oEvent).parents('.xWPdtUnitBarCodeRow').data('puncode');
            var tMDAebBarCode = $(oEvent).parents('.xWDelBarCodeItem').data('barcode');
            var oObjDeleteBarCode = $('#odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tMDAebPunCode + ' .xWBarCodeRow' + tMDAebBarCode);
            $(oObjDeleteBarCode).fadeOut('slow', function() {
                $(this).remove();
            });
            var oObjDeleteSupplier = $('#odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tMDAebPunCode + ' .xWSupplierDt[data-barcode="' + tMDAebBarCode + '"]');
            $(oObjDeleteSupplier).fadeOut('slow', function() {
                $(this).remove();
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }


    /** ================================================================================================================================= */

    /** =================================================== Function And Event Supplier ================================================= */

    var oPdtBrowseSupplier = function(poReturnInput) {
        var tInputReturnCode = poReturnInput.tReturnInputCode;
        var tInputReturnName = poReturnInput.tReturnInputName;
        var tNextFuncName = poReturnInput.tNextFuncName;
        var oOptionReturn = {
            Title: ['supplier/supplier/supplier', 'tSPLTitle'],
            Table: {
                Master: 'TCNMSpl',
                PK: 'FTSplCode'
            },
            Join: {
                Table: ['TCNMSpl_L'],
                On: ['TCNMSpl_L.FTSplCode = TCNMSpl.FTSplCode AND TCNMSpl_L.FNLngID = ' + nLangEdits]
            },
            GrideView: {
                ColumnPathLang: 'supplier/supplier/supplier',
                ColumnKeyLang: ['tSPLTBCode', 'tSPLTBName'],
                ColumnsSize: ['10%', '75%'],
                DataColumns: ['TCNMSpl.FTSplCode', 'TCNMSpl_L.FTSplName'],
                DataColumnsFormat: ['', ''],
                WidthModal: 50,
                Perpage: 10,
                OrderBy: ['TCNMSpl.FDCreateOn DESC'],
                // SourceOrder: "DESC" 
            },
            CallBack: {
                ReturnType: 'S',
                Value: [tInputReturnCode, "TCNMSpl.FTSplCode"],
                Text: [tInputReturnName, "TCNMSpl_L.FTSplName"],
            },
            NextFunc: {
                FuncName: tNextFuncName,
            },
            RouteAddNew: 'supplier',
            BrowseLev: nStaPdtBrowseType
        }
        return oOptionReturn;
    }

    // Event Browse Supplier In Modal
    $('#obtModalAebBrowsePdtSupplier').click(function(e) {
        e.preventDefault();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            $('#odvModalAddEditBarCode').modal("hide");
            window.oPdtBrowseSupplierOption = oPdtBrowseSupplier({
                'tReturnInputCode': 'oetModalAesSplCode',
                'tReturnInputName': 'oetModalAesSplName',
                'tNextFuncName': 'JSxShowModalAddEditSupplier'
            })
            JCNxBrowseData('oPdtBrowseSupplierOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Event Click Summit Product
    $('#obtModalAesSupplierSubmit').click(function() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            JSxPdtSaveSupplierInUnitPack();
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Function: Func. Call Back In Browse Show Modal Add Edit Supplier
    // Parameters: Obj Event Click
    // Creator:	12/02/2019 wasin(Yoshi)
    // Return: Open Pop Up Modal Manage PackSize Unit
    // Return Type: -
    function JSxShowModalAddEditSupplier() {
        $('#odvModalAddEditBarCode #oetModalAesSplName').parents('.form-group').removeClass("has-error");
        $('#odvModalAddEditBarCode #oetModalAesSplName').parents('.form-group').removeClass("has-success");
        $('#odvModalAddEditBarCode #oetModalAesSplName').parents('.form-group').find(".help-block").fadeOut('slow').remove();

        $('#odvModalAddEditBarCode').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#odvModalAddEditBarCode').modal("show");
    }

    // Function: ฟังก์ชั่นดึงข้อมูลบาร์โค๊ดจากตารางคอลัมน์บาร์โค๊ด
    // Parameters:
    // Creator:	13/02/2019 wasin(Yoshi)
    // LastUpdate: -
    // Return: Array Data BarCode
    // Return Type: Array
    function JSaPdtGetDataBarCodeInColumBarCode(ptUnitCode) {
        var aDataBarCode = [];
        $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + ptUnitCode + ' .xWBarCodeItem .xWPdtAebBarCodeItem').each(function() {
            var tBarCode = $(this).val();
            aDataBarCode.push(tBarCode.toString());
        });
        return aDataBarCode;
    }

    // Function: ฟังก์ชั่นดึงข้อมูลบาร์โค๊ดจากตารางคอลัมน์ผู้จำหน่าย
    // Parameters:
    // Creator:	14/02/2019 wasin(Yoshi)
    // LastUpdate: -
    // Return: Array Data BarCode
    // Return Type: Array
    function JSaPdtGetDataBarCodeInColumSupplier(ptUnitCode) {
        var aDataBarCodeSpl = [];
        $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + ptUnitCode + ' .xWSupplierDt .xWPdtAesBarCodeItem').each(function() {
            var tBarCode = $(this).val();
            aDataBarCodeSpl.push(tBarCode.toString());
        });
        return aDataBarCodeSpl;
    }

    // Function: Func.Add Supplier
    // Parameters: Obj Event Click
    // Creator:	14/02/2019 wasin(Yoshi)
    // LastUpdate: -
    // Return: Show View Modal
    // Return Type: None
    function JSxPdtCallModalAddEditSupplier(oEvent, tCallAddOrEdit) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            // Set Sta Add Or Edit
            $('#odvModalAddEditSupplier #ohdModalAesStaCallAddOrEdit').val(tCallAddOrEdit);
            if (tCallAddOrEdit == 'Add') {
                var tAesUnitCode = $(oEvent).parents('.xWPdtDataUnitRow').data('puncode');
                var tAesUnitName = $(oEvent).parents('.xWPdtDataUnitRow').data('punname');
                var nRowBarCodePsz = $('#odvPdtContentInfo1 #odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tAesUnitCode + ' .xWPdtUnitDataBarCode .xWBarCodeItem').length;
                if (nRowBarCodePsz > 0) {
                    $('#odvModalAddEditSupplier #ohdModalAesUnitCode').val(tAesUnitCode);
                    $('#odvModalAddEditSupplier #ohdModalAesUnitName').val(tAesUnitName);
                    $("#odvModalAddEditSupplier #olbModalAesUnitTitle").text('<?php echo language("product/product/product", "tPDTViewPackMDUnit"); ?>' + " : " + tAesUnitName);
                    // ===== Get Data BarCode And Append In Modal [odvModalAddEditSupplier]
                    var aDataBarCodeInSupplier = JSaPdtGetDataBarCodeInColumSupplier(tAesUnitCode);
                    var aDataBarCodeInBarCode = JSaPdtGetDataBarCodeInColumBarCode(tAesUnitCode);

                    $("#odvModalAddEditSupplier #odvMdAesSelectBarCode").empty()
                        .append($('<label>')
                            .attr('class', 'xCNLabelFrm')
                            .text('<?php echo language("product/product/product", "tPDTViewPackMDSplBarCode"); ?>')
                        )
                        .append($('<select>')
                            .attr('id', 'ostModalAesBarcode')
                            .attr('class', 'selectpicker form-control')
                            .attr('name', 'ostModalAesBarcode')
                            .attr('data-validate', '<?php echo language('product/product/product', 'tPDTViewPackMDMsgSplNotSltBarCode'); ?>')
                            .append($('<option>')
                                .attr('value', '')
                                .text('<?php echo language("common/main/main", "tCMNBlank-NA"); ?>')
                            )
                        )
                    $.each(aDataBarCodeInBarCode, function(nKey, tBarCode) {
                        if (jQuery.inArray(tBarCode, aDataBarCodeInSupplier) != -1) {} else {
                            $('#odvModalAddEditSupplier #odvMdAesSelectBarCode #ostModalAesBarcode').append($('<option>')
                                .attr('value', tBarCode)
                                .text(tBarCode)
                            )
                        }
                    });

                    if (aDataBarCodeInSupplier.length === aDataBarCodeInBarCode.length) {
                        $('#odvModalAddEditSupplier #ostModalAesBarcode').prop("disabled", true);
                        $('#odvModalAddEditSupplier #obtModalAebBrowsePdtSupplier').prop("disabled", true);
                        $('#odvModalAddEditSupplier #ocbModalAesSplStaAlwPO').prop("disabled", true);
                        $('#odvModalAddEditSupplier #obtModalAddSupplierSubmit').hide();
                    } else {
                        $('#odvModalAddEditSupplier #ostModalAesBarcode').prop("disabled", false);
                        $('#odvModalAddEditSupplier #obtModalAebBrowsePdtSupplier').prop("disabled", false);
                        $('#odvModalAddEditSupplier #ocbModalAesSplStaAlwPO').prop("disabled", false);
                        $('#odvModalAddEditSupplier #obtModalAddSupplierSubmit').show();
                    }

                    $('#ostModalAesBarcode').selectpicker('refresh');
                    $.getScript("application/modules/common/assets/vendor/bootstrap-select-1.13.2/dist/css/bootstrap-select.min.css");
                    $.getScript("application/modules/common/assets/vendor/bootstrap-select-1.13.2/dist/js/bootstrap-select.min.js");
                    // ===== End Get Data BarCode

                    // ===== Clear Data In Input From
                    $('#ostModalAesBarcode').val('');
                    $('#oetModalAesSplCode').val('');
                    $('#oetModalAesSplName').val('');
                    $('#ocbModalAesSplStaAlwPO').prop("checked", false);
                    // ===== Clear Data In Input From

                    // ===== Clear Validate Modal Suppler
                    $('#odvModalAddEditSupplier #ostModalAesBarcode').parents('.form-group').removeClass("has-error");
                    $('#odvModalAddEditSupplier #ostModalAesBarcode').parents('.form-group').removeClass("has-success");
                    $('#odvModalAddEditSupplier #ostModalAesBarcode').parents('.form-group').find(".help-block").fadeOut('slow').remove();

                    $('#odvModalAddEditSupplier #oetModalAesSplName').parents('.form-group').removeClass("has-error");
                    $('#odvModalAddEditSupplier #oetModalAesSplName').parents('.form-group').removeClass("has-success");
                    $('#odvModalAddEditSupplier #oetModalAesSplName').parents('.form-group').find(".help-block").fadeOut('slow').remove();
                    // ===== End Clear Validate Modal Suppler
                    $('#odvModalAddEditSupplier').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#odvModalAddEditSupplier').modal('show');
                    $.getScript("application/modules/common/assets/src/jFormValidate.js")
                } else {
                    var tMsgBarCodeNotFound = '<?php echo language("product/product/product", "tPDTViewPackMDMsgSplBarCodeNotFound"); ?>';
                    FSvCMNSetMsgWarningDialog(tMsgBarCodeNotFound);
                }
            } else if (tCallAddOrEdit == 'Edit') {
                // Get Value For Input
                var tAesUnitCode = $(oEvent).parents('.xWPdtUnitBarCodeRow').data('puncode');
                var tAesUnitName = $(oEvent).parents('.xWPdtUnitBarCodeRow').data('punname');
                var tAesBarcode = $(oEvent).parents('.xWAddSupplierItem').find('.xWPdtAesBarCodeItem').val();
                var tAesSplCode = $(oEvent).parents('.xWAddSupplierItem').find('.xWPdtAesSplCodeItem').val();
                var tAesSplName = $(oEvent).parents('.xWAddSupplierItem').find('.xWPdtAesSplNameItem').val();
                var tAesSplStaAlwPO = $(oEvent).parents('.xWAddSupplierItem').find('.xWPdtAesSplStaAlwPOItem').val();

                // Set Title Modal Supplier
                $('#odvModalAddEditSupplier #ohdModalAesUnitCode').val(tAesUnitCode);
                $('#odvModalAddEditSupplier #ohdModalAesUnitName').val(tAesUnitName);
                $('#odvModalAddEditSupplier #olbModalAesUnitTitle').text('<?php echo language("product/product/product", "tPDTViewPackMDUnit"); ?>' + " : " + tAesUnitName);

                // Set Value In Input
                // ===== Get Data BarCode And Append In Modal [odvModalAddEditSupplier]
                var aDataBarCodeInBarCode = JSaPdtGetDataBarCodeInColumBarCode(tAesUnitCode);

                $("#odvModalAddEditSupplier #odvMdAesSelectBarCode").empty()
                    .append($('<label>')
                        .attr('class', 'xCNLabelFrm')
                        .text('<?php echo language("product/product/product", "tPDTViewPackMDSplBarCode"); ?>')
                    )
                    .append($('<select>')
                        .attr('id', 'ostModalAesBarcode')
                        .attr('class', 'selectpicker form-control')
                        .attr('name', 'ostModalAesBarcode')
                        .attr('data-validate', '<?php echo language('product/product/product', 'tPDTViewPackMDMsgSplNotSltBarCode'); ?>')
                        .append($('<option>')
                            .attr('value', '')
                            .text('<?php echo language("common/main/main", "tCMNBlank-NA"); ?>')
                        )
                    )

                $.each(aDataBarCodeInBarCode, function(nKey, tBarCode) {
                    $('#odvModalAddEditSupplier #odvMdAesSelectBarCode #ostModalAesBarcode').append($('<option>')
                        .attr('value', tBarCode)
                        .text(tBarCode)
                    )
                })

                $('#odvModalAddEditSupplier #ostModalAesBarcode').val(tAesBarcode);
                $('#odvModalAddEditSupplier #ostModalAesBarcode').prop("disabled", true);
                $('#odvModalAddEditSupplier #obtModalAebBrowsePdtSupplier').prop("disabled", false);
                $('#odvModalAddEditSupplier #ocbModalAesSplStaAlwPO').prop("disabled", false);
                $('#odvModalAddEditSupplier #obtModalAddSupplierSubmit').show();

                $('#ostModalAesBarcode').selectpicker('refresh');
                $.getScript("application/modules/common/assets/vendor/bootstrap-select-1.13.2/dist/css/bootstrap-select.min.css");
                $.getScript("application/modules/common/assets/vendor/bootstrap-select-1.13.2/dist/js/bootstrap-select.min.js");

                $('#odvModalAddEditSupplier #oetModalAesSplCode').val(tAesSplCode);
                $('#odvModalAddEditSupplier #oetModalAesSplName').val(tAesSplName);

                if (tAesSplStaAlwPO == '1') {
                    $('#odvModalAddEditSupplier #ocbModalAesSplStaAlwPO').prop("checked", true);
                } else {
                    $('#odvModalAddEditSupplier #ocbModalAesSplStaAlwPO').prop("checked", false);
                }

                // ===== Clear Validate Modal Suppler
                $('#odvModalAddEditSupplier #ostModalAesBarcode').parents('.form-group').removeClass("has-error");
                $('#odvModalAddEditSupplier #ostModalAesBarcode').parents('.form-group').removeClass("has-success");
                $('#odvModalAddEditSupplier #ostModalAesBarcode').parents('.form-group').find(".help-block").fadeOut('slow').remove();

                $('#odvModalAddEditSupplier #oetModalAesSplName').parents('.form-group').removeClass("has-error");
                $('#odvModalAddEditSupplier #oetModalAesSplName').parents('.form-group').removeClass("has-success");
                $('#odvModalAddEditSupplier #oetModalAesSplName').parents('.form-group').find(".help-block").fadeOut('slow').remove();
                // ===== End Clear Validate Modal Suppler
                $('#odvModalAddEditSupplier').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#odvModalAddEditSupplier').modal('show');
                $.getScript("application/modules/common/assets/src/jFormValidate.js")
            } else {}
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    // Function: Func. Save Supplier By BarCode In PackSize Table
    // Parameters: Obj Event Click
    // Creator:	14/02/2019 wasin(Yoshi)
    // Return: Append Data Supplier In Div
    // Return Type: None
    function JSxPdtSaveSupplierInUnitPack() {
        $('#ofmModalAesSupplier').validate({
            rules: {
                ostModalAesBarcode: {
                    required: true
                },
                oetModalAesSplName: {
                    required: true
                },
            },
            messages: {
                ostModalAesBarcode: $('#ostModalAesBarcode').data('validate'),
                oetModalAesSplName: $('#oetModalAesSplName').data('validate'),
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
                error.addClass("help-block");
                if (element.prop("type") === "checkbox") {
                    error.appendTo(element.parent("label"));
                } else {
                    var tCheck = $(element.closest('.form-group')).find('.help-block').length;
                    if (tCheck == 0) {
                        error.appendTo(element.closest('.form-group')).trigger('change');
                    }
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass("has-error").removeClass("has-success");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass("has-success").removeClass("has-error");
            },
            submitHandler: function(form) {
                var tMdAesAddOrEdit = $('#ohdModalAesStaCallAddOrEdit').val();
                var tMdAesUnitCode = $('#ohdModalAesUnitCode').val();
                var tMdAesUnitName = $('#ohdModalAesUnitName').val();
                var tMdAesBarCode = $('#ostModalAesBarcode').val();
                var tMdAesSplCode = $('#oetModalAesSplCode').val();
                var tMdAesSplName = $('#oetModalAesSplName').val();
                var tMdAesSplStaAlwPO = $('#ocbModalAesSplStaAlwPO').is(':checked') ? '1' : '2';
                if (tMdAesAddOrEdit == 'Add') {
                    // Append Supplier Data In Div
                    $('#odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tMdAesUnitCode + ' .xWPdtUnitDataSupplier .xWAddSupplierItem[data-barcode="' + tMdAesBarCode + '"]').empty()
                        .append($('<input>')
                            .attr('type', 'hidden')
                            .attr('class', 'form-control xWPdtAesBarCodeItem')
                            .val(tMdAesBarCode)
                        )
                        .append($('<input>')
                            .attr('type', 'hidden')
                            .attr('class', 'form-control xWPdtAesSplCodeItem')
                            .val(tMdAesSplCode)
                        )
                        .append($('<input>')
                            .attr('type', 'hidden')
                            .attr('class', 'form-control xWPdtAesSplNameItem')
                            .val(tMdAesSplName)
                        )
                        .append($('<input>')
                            .attr('type', 'hidden')
                            .attr('class', 'form-control xWPdtAesSplStaAlwPOItem')
                            .val(tMdAesSplStaAlwPO)
                        )
                        .append($('<lable>')
                            .attr('class', 'xCNTextLink xWPdtSplDetail')
                            .append($('<i>')
                                .attr('class', 'fa fa-users')
                                .text(' ' + tMdAesSplName)
                                .click(function() {
                                    JSxPdtCallModalAddEditSupplier(this, 'Edit');
                                })
                            )
                        )
                    // End Supplier Data In Div

                    // Append Delete Supplier Data In Div
                    $('#odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tMdAesUnitCode + ' .xWPdtDelUnitSupplier .xWDelSupplierItem[data-barcode="' + tMdAesBarCode + '"]').empty()
                        .append($('<img>')
                            .attr('class', 'xCNIconTable xWPdtDelSupplierItem')
                            .attr('src', '<?php echo base_url() . "/application/modules/common/assets/images/icons/delete.png"; ?>')
                            .click(function() {
                                JSxPdtDelSupplierInTable(this);
                            })
                        )
                    // End Delete Supplier Data In Div
                    $('#odvModalAddEditSupplier').modal("hide");
                } else if (tMdAesAddOrEdit == 'Edit') {
                    var oPdtSplEdit = $('#odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tMdAesUnitCode + ' .xWPdtUnitDataSupplier .xWAddSupplierItem[data-barcode="' + tMdAesBarCode + '"]');
                    $(oPdtSplEdit).find('.xWPdtAesSplCodeItem').val(tMdAesSplCode);
                    $(oPdtSplEdit).find('.xWPdtAesSplNameItem').val(tMdAesSplName);
                    $(oPdtSplEdit).find('.xWPdtAesSplStaAlwPOItem').val(tMdAesSplStaAlwPO);
                    $(oPdtSplEdit).find('.xWPdtSplDetail').empty()
                        .append($('<i>')
                            .attr('class', 'fa fa-users')
                            .text(' ' + tMdAesSplName)
                            .click(function() {
                                JSxPdtCallModalAddEditSupplier(this, 'Edit');
                            })
                        );

                    $('#odvModalAddEditSupplier').modal("hide");
                } else {}
            }
        });
    }

    // Function: Func. Delete Supplier 
    // Parameters: Obj Event Click
    // Creator:	14/02/2019 wasin(Yoshi)
    // Return: -
    // Return Type: -
    function JSxPdtDelSupplierInTable(oEvent) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var tMDAesPunCode = $(oEvent).parents('.xWPdtUnitBarCodeRow').data('puncode');
            var tMDAesBarCode = $(oEvent).parents('.xWDelSupplierItem').data('barcode');
            var oMDAesDelDataSpl = $('#odvPdtSetPackSizeTable #otrPdtUnitBarCodeRow' + tMDAesPunCode + ' .xWSupplierDt[data-barcode="' + tMDAesBarCode + '"]');
            $(oMDAesDelDataSpl).fadeOut('slow', function() {
                $(this).empty().removeAttr('style').append("&nbsp;");
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }



    /** ================================================================================================================================= */

    /** ==================================================== Function And Event Product Set ============================================= */

    $('#obtPdtSetAdd').off('click');
    $('#obtPdtSetAdd').on('click', function() {
        JSxPdtSetCallPageAdd();
    });

    $('#olbPdtSetInfo').off('click');
    $('#olbPdtSetInfo').on('click', function() {
        JSxPdtSetCallDataTable();
    });

    $('#obtPdtSetBack').off('click');
    $('#obtPdtSetBack').on('click', function() {
        JSxPdtSetCallDataTable();
    });

    $('#obtPdtSetSave').off('click');
    $('#obtPdtSetSave').on('click', function() {
        JSxPdtSetEventAdd();
    });

    $('.xWPdtStaSetPri').off('click');
    $('.xWPdtStaSetPri').on('click', function() {
        var tPdtStaSetPri = $("input[name=orbPdtStaSetPri]:checked").val();
        JSxPdtSetUpdateStaSetPri(tPdtStaSetPri);
    });

    $('.xWPdtStaSetShwDT').off('click');
    $('.xWPdtStaSetShwDT').on('click', function() {
        var tPdtStaSetShwDT = $("input[name=orbPdtStaSetShwDT]:checked").val();
        JSxPdtSetUpdateStaSetShwDT(tPdtStaSetShwDT);
    });



    // Click Browse Product Product Set
    // $('#olbAddPdtSet').click(function(){
    //     var nStaSession = JCNxFuncChkSessionExpired();
    //     if(typeof(nStaSession) !== 'undefined' && nStaSession == 1){
    //         window.oPdtBrowsePdtSetOption   =   oPdtBrowsePdtSet({
    //             'tReturnInputCode'  : 'ohdPdtSetCode',
    //             'tReturnInputName'  : 'ohdPdtSetName',
    //             'tNextFuncName'     : 'JSxAddDataPdtSetToTable'
    //         });
    //         JCNxBrowseData('oPdtBrowsePdtSetOption');
    //     }else{
    //         JCNxShowMsgSessionExpired();
    //     }
    // });

    // Function: Function Get Data Product Set
    // Parameters:  Object In Next Funct Modal Browse
    // Creator:	07/02/2019 wasin(Yoshi)
    // Return: object View Product Set
    // Return Type: object
    // function JSxAddDataPdtSetToTable(poDataNextFunc){
    //     var aProductCode = [];
    //     for(var i = 0; i < poDataNextFunc.length; i++){
    //         aColDatas   = JSON.parse(poDataNextFunc[i]);
    //         if(aColDatas != null){
    //             aProductCode.push(aColDatas[0]);
    //         }
    //     }
    //     if(aProductCode.length !== 0){
    //         JCNxOpenLoading();
    //         $.ajax({
    //             type: "POST",
    //             url: "productGetDataPdtSet",
    //             data: { aPdtCode : aProductCode },
    //             cache: false,
    //             timeout: 0,
    //             async: false,
    //             success: function(oResult){
    //                 var aReturnData = JSON.parse(oResult);
    //                 if(aReturnData['nStaEvent'] == '1'){
    //                     $('#odvPdtContentSet #odvPdtSetTable #odvPdtSetDataTable').empty().html(aReturnData['vPdtDataSet']).hide().fadeIn('slow');
    //                     JSxAddRowDataConfigPdtSet();
    //                 }else{
    //                     var tMessageError   = aReturnData['tStaMessg'];
    //                     FSvCMNSetMsgErrorDialog(tMessageError);
    //                 }
    //                 JCNxLayoutControll();
    //                 JCNxCloseLoading();
    //             },
    //             error: function(jqXHR, textStatus, errorThrown) {
    //                 JCNxResponseError(jqXHR,textStatus,errorThrown);
    //             }
    //         });
    //     }
    // }
    /** ================================================================================================================================= */

    /** ================================================ Function And Event Product Event No Sale ======================================== */
    // Click Browse Product Event Not Sale
    $('#olbAddPdtEvnNotSale').click(function() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            window.oPdtBrowsePdtEvnNoSaleOption = oPdtBrowsePdtEvnNoSale({
                'tReturnInputCode': 'ohdPdtEvnNoSleCode',
                'tReturnInputName': 'ohdPdtEvnNoSleName',
                'tNextFuncName': 'JSoAddDataPdtEvnNotSaleToTable'
            });
            JCNxBrowseData('oPdtBrowsePdtEvnNoSaleOption');
        } else {
            JCNxShowMsgSessionExpired();
        }
    });

    // Function: Function Get Data Event Not Sale To Table
    // Parameters:  Object In Next Funct Modal Browse
    // Creator:	07/02/2019 wasin(Yoshi)
    // Return: object View Event Not Sale Data Table
    // Return Type: object
    function JSoAddDataPdtEvnNotSaleToTable(poDataNextFunc) {
        if (poDataNextFunc != 'NULL') {
            var aDataPdtEvnNotSale = $.parseJSON(poDataNextFunc);
            var tEvnCode = aDataPdtEvnNotSale[0];
            JCNxOpenLoading();
            $.ajax({
                type: "POST",
                url: "productGetEvnNotSale",
                data: {
                    tEvnCode: tEvnCode
                },
                cache: false,
                timeout: 0,
                success: function(oResult) {
                    var aReturnData = JSON.parse(oResult);
                    if (aReturnData['nStaEvent'] == '1') {
                        $('#odvPdtContentEvnNotSale #odvPdtEvnNotSaleTable #odvPdtEvnNotSaleDataTable').empty().html(aReturnData['vPdtEvnNotSale']).hide().fadeIn('slow');
                    } else {
                        var tMessageError = aReturnData['tStaMessg'];
                        FSvCMNSetMsgErrorDialog(tMessageError);
                    }
                    JCNxLayoutControll();
                    JCNxCloseLoading();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }
    }

    // Function : Delete All Prodcut Event Not Sale
    // Parameters : 
    // Creator : 07/02/2019 wasin(Yoshi)
    // Return : Delete Data All In Table Event Not Sale
    // Return Type : -
    function JSxDelAllPdtEvnNotSale() {
        var nRowDataEvnNotSale = $('#odvPdtContentEvnNotSale #odvPdtEvnNotSaleDataTable table tbody tr.xWEvnNotSaleRow').length;
        if (nRowDataEvnNotSale > 0) {
            $('#odvPdtContentEvnNotSale #odvPdtEvnNotSaleDataTable table tbody').empty().append($('<tr>')
                .attr('class', 'xWPdtEvnNoSaleNoData')
                .append($('<td>')
                    .attr('class', 'text-center xCNTextDetail2')
                    .attr('colspan', '99')
                    .text('<?php echo language("common/main/main", "tCMNNotFoundData"); ?>')
                )
            ).hide().fadeIn('slow');
            $('#ohdPdtEvnNoSleCode').val('');
            $('#ohdPdtEvnNoSleName').val('');
        } else {
            var tTextMessage = '<?php echo language("product/product/product", "tPDTDelNotFoundEvnNotSale"); ?>';
            FSvCMNSetMsgWarningDialog(tTextMessage);
        }
    }

    /** ================================================================================================================================== */

    /** ============================================= Function Show Price Detail All ===================================================== */

    $('#olbPdtPriceAllData').unbind().click(function() {
        JSvPdtCallModalPriceList();
    });


    // Function: ฟังก์ชั่น Call Modal View Product Price Detail
    // Parameters: Object In Next Funct Modal Browse
    // Creator:	27/02/2019 wasin(Yoshi)
    // Return: View Modal Price Detail
    // ReturnType: View
    function JSvPdtCallModalPriceList() {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var tPriceDTPdtCode = $('#oetPdtCode').val();
            var tPriceDTPdtName = $('#oetPdtName').val();
            $.ajax({
                type: "POST",
                url: "productCallModalPriceList",
                data: {
                    ptPriceDTPdtCode: tPriceDTPdtCode,
                    ptPriceDTPdtName: tPriceDTPdtName
                },
                cache: false,
                timeout: 0,
                async: false,
                success: function(oResult) {
                    var aReturnData = JSON.parse(oResult);
                    if (aReturnData['nStaEvent'] == 1) {
                        $('#odvModallAllPriceList').empty();
                        $('#odvModallAllPriceList').append(aReturnData['vPdtModalPriceList']);
                        $('#odvModallAllPriceList #odvModalPdtPriceDetail').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $('#odvModallAllPriceList #odvModalPdtPriceDetail').modal('show');
                        $.getScript("application/modules/common/assets/src/jFormValidate.js");
                    } else if (aReturnData['nStaEvent'] == 500) {
                        var tMessageError = aReturnData['tStaMessg'];
                        FSvCMNSetMsgErrorDialog(tMessageError);
                    } else {}
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    /** ================================================================================================================================== */

    function JSxModalPdtBarCodeEdit(ptBarCode) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var FTBarStaUse = $('#ohdModalFTBarStaUse' + ptBarCode).val();
            var FTBarStaAlwSale = $('#ohdModalFTBarStaAlwSale' + ptBarCode).val();
            var FTSplStaAlwPO = $('#ohdModalFTSplStaAlwPO' + ptBarCode).val();
            $('#oetEditData').val(1);
            $('#oetModalAebBarCode').val(ptBarCode);
            $('#oetModalAebOldBarCode').val(ptBarCode);
            $('#oetModalAebPlcCode').val($('#ohdModalFTPlcCode' + ptBarCode).val());
            $('#oetModalAebPlcName').val($('#ohdModalFTPlcName' + ptBarCode).val());
            $('#oetModalAesSplCode').val($('#ohdModalFTSplCode' + ptBarCode).val());
            $('#oetModalAesSplName').val($('#ohdModalFTSplName' + ptBarCode).val());

            if (FTBarStaUse == 1) {
                $('#ocbModalAebBarStaUse').prop("checked", true);
            } else {
                $('#ocbModalAebBarStaUse').prop("checked", false);
            }

            if (FTBarStaAlwSale == 1) {
                $('#ocbModalAebBarStaAlwSale').prop("checked", true);
            } else {
                $('#ocbModalAebBarStaAlwSale').prop("checked", false);
            }

            if (FTSplStaAlwPO == 1) {
                $('#ocbModalAesSplStaAlwPO').prop("checked", true);
            } else {
                $('#ocbModalAesSplStaAlwPO').prop("checked", false);
            }
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    function JSxModalPdtBarCodeDelete(ptBarCode) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            var tPdtCode = $('#ohdModalFTPdtCode').val();
            var tPunCode = $('#ohdModalFTPunCode').val();
            $.ajax({
                type: "POST",
                url: "productDeleteBarCode",
                data: {
                    FTBarCode: ptBarCode,
                    FTPdtCode: tPdtCode,
                    FTPunCode: tPunCode
                },
                cache: false,
                timeout: 0,
                async: false,
                success: function(tResult) {
                    JSxPdtGetBarCodeDataByID(tPdtCode, tPunCode);
                    // $('.xWModalBarCodeDataTable').html(tResult);
                    var nCount = parseInt($('#ohdPdtBarCodeRow' + tPunCode).val());
                    $('#ohdPdtBarCodeRow' + tPunCode).val(nCount - 1);
                    JCNxCloseLoading();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }

    $('document').ready(function() {
        if ($('#ocmRetPdtType').val() == 2) {
            $('.xWPdtRetBrwShp').show();
        } else {
            $('.xWPdtRetBrwShp').hide();
        }
    });

    function JSxPdtRentSelectType(tType) {
        if (tType == '2') {
            $('.xWPdtRetBrwShp').show();
        } else {
            $('.xWPdtRetBrwShp').hide();
        }
        $('#oetModalShpCode').val('');
        $('#oetModalShpName').val('');
    }

    $('#oliPdtDataAddSet').on('click', function(oEvent) {
        if ($(this).hasClass('xCNCloseTabNav') == false) {
            JSxPdtSetCallDataTable();
        }
    });

    $('#oliPdtDataAddSetDisable').off('click');
    $('#oliPdtDataAddSetDisable').on('click', function() {
        // var tMsg = "สินค้านี้เป็นสินค้าชุดของ : " + $(this).data('pdtcode') + " (" + $(this).data('pdtname') + ")";
        var tMsg = "สินค้านี้เป็นสินค้าลูกของสินค้าอื่นแล้ว ไม่สามารถเพิ่มสินค้าชุดได้";
        FSvCMNSetMsgWarningDialog(tMsg);
    });



    // function: Get Product Content 
    // Create By Witsarut 16/01/2020
    function JSxPdtGetContent() {

        var ptPdtCode = '<?php echo $tPdtCode; ?>';

        // Check Login Expried
        var nStaSession = JCNxFuncChkSessionExpired();

        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            $.ajax({
                type: "POST",
                url: "pdtDrugPageAdd/0/0",
                data: {
                    tPdtCode: ptPdtCode
                },
                catch: false,
                timeout: 0,
                success: function(tResult) {
                    $('#odvPdtContentDrug').html(tResult);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }


    // Function: ฟังก์ชั่น Call View กำหนดเงื่อนไขการควบคุมสต๊อก
    // Parameters: -
    // Creator: 23/01/2020 Saharat(GolF)
    // Return: View
    // ReturnType: View
    function JSvPdtCallpageStockConditions() {
        let tPdtCode = $('#oetPdtCode').val();
        $.ajax({
            type: "POST",
            url: "pdtEventPageStockConditionsList",
            data: {
                ptPdtCode: tPdtCode
            },
            cache: false,
            success: function(oResult) {
                $('#odvStockConditions').html(oResult);
                JCNxCloseLoading();
            }
        });
    }

    // Function: ฟังก์ชั่น Call Modal Add กำหนดเงื่อนไขการควบคุมสต๊อก
    // Parameters: -
    // Creator: 23/01/2020 Saharat(GolF)
    // Return: View
    // ReturnType: View
    function JSvPdtStockConditionsPageAdd() {
        document.forms["ofmAddStockConditions"].reset();
        let tPdtCode = $('#oetPdtCode').val();
        $('#odvModalStockConditions').modal('show');
        $('#oetStockConditionPdtCode').val(tPdtCode);
        $('#oetStockConditionRoute').val('pdtEventAddStockConditions');

    }

    // Function: ฟังก์ชั่น Call Modal Edit กำหนดเงื่อนไขการควบคุมสต๊อก
    // Parameters: -
    // Creator: 23/01/2020 Saharat(GolF)
    // Return: View
    // ReturnType: View
    function JSvPdtStockConditionsPageEdit(ptPdtCode, ptBchCode, ptWahCode) {
        document.forms["ofmAddStockConditions"].reset();
        $.ajax({
            type: "POST",
            url: "pdtEventPageStockConditionsEdit",
            data: {
                ptPdtCode: ptPdtCode,
                ptBchCode: ptBchCode,
                ptWahCode: ptWahCode
            },
            cache: false,
            success: function(oResult) {
                var aReturn = JSON.parse(oResult);
                if (aReturn['rtCode'] == 1) {
                    $('#odvModalStockConditions').modal('show');
                    $('#oetStockConditionPdtCode').val(aReturn['raItems']['FTPdtCode']);
                    $('#oetStockConditionBchCode').val(aReturn['raItems']['FTBchCode']);
                    $('#oetStockConditionhBchName').val(aReturn['raItems']['FTBchName']);
                    $('#oetStockConditionWahCode').val(aReturn['raItems']['FTWahCode']);
                    $('#oetStockConditionhWahName').val(aReturn['raItems']['FTWahName']);
                    $('#oetStockConditionsMin').val(aReturn['raItems']['FCSpwQtyMin']);
                    $('#oetStockConditionsMax').val(aReturn['raItems']['FCSpwQtyMax']);
                    $('#oetStockConditionsRemark').val(aReturn['raItems']['FTSpwRmk']);

                    $('#oetStockConditionBchCodeOld').val(aReturn['raItems']['FTBchCode']);
                    $('#oetStockConditionWahCodeOld').val(aReturn['raItems']['FTWahCode']);

                    //Route
                    $('#oetStockConditionRoute').val('pdtEventEditStockConditions');
                } else {
                    alert(aReturn['rtDesc']);
                }
            }
        });
    }

    // Function: ฟังก์ชั่น บันทึกข้อมูล กำหนดเงื่อนไขการควบคุมสต๊อก
    // Parameters: -
    // Creator: 23/01/2020 Saharat(GolF)
    // Return: View
    // ReturnType: View
    function JSvPdtStockConditionsEventAddEdit() {
        let tRoute = $('#oetStockConditionRoute').val();
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            $('#ofmAddStockConditions').validate().destroy();
            $('#ofmAddStockConditions').validate({
                rules: {
                    oetStockConditionhBchName: {
                        "required": {}
                    },
                    oetStockConditionhWahName: {
                        "required": {}
                    },
                    oetStockConditionsMin: {
                        "required": {}
                    },
                    oetStockConditionsMax: {
                        "required": {}
                    },
                },
                messages: {
                    oetStockConditionhBchName: {
                        "required": $('#oetStockConditionhBchName').attr('data-validate-required'),
                    },
                    oetStockConditionhWahName: {
                        "required": $('#oetStockConditionhWahName').attr('data-validate-required'),
                    },
                    oetStockConditionsMin: {
                        "required": $('#oetStockConditionsMin').attr('data-validate-required'),
                    },
                    oetStockConditionsMax: {
                        "required": $('#oetStockConditionsMax').attr('data-validate-required'),
                    },
                },
                errorElement: "em",
                errorPlacement: function(error, element) {
                    error.addClass("help-block");
                    if (element.prop("type") === "checkbox") {
                        error.appendTo(element.parent("label"));
                    } else {
                        var tCheck = $(element.closest('.form-group')).find('.help-block').length;
                        if (tCheck == 0) {
                            error.appendTo(element.closest('.form-group')).trigger('change');
                        }
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).closest('.form-group').addClass("has-error").removeClass("has-success");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).closest('.form-group').addClass("has-success").removeClass("has-error");
                },
                submitHandler: function(form) {
                    JCNxOpenLoading();
                    $.ajax({
                        type: "POST",
                        url: tRoute,
                        data: $('#ofmAddStockConditions').serialize(),
                        cache: false,
                        timeout: 0,
                        success: function(tResult) {
                            let aReturn = JSON.parse(tResult);
                            if (aReturn['rtCode'] == 1) {
                                // $('#odvModalStockConditions').modal('hide');
                                $('#odvModalStockConditions').modal('toggle');
                                JSvPdtCallpageStockConditions();
                            } else {
                                $('#odvModalStockConditionsAlert').modal('show');
                            }
                            JCNxCloseLoading();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            JCNxResponseError(jqXHR, textStatus, errorThrown);
                        }
                    });
                },
            });
        }
    }

    // Functionality: ลบข้อมูล
    // Parameters: Event Icon Delete
    // Creator: 23/01/2020 Saharat(GolF)
    // Return: object Status Delete
    // ReturnType: object
    function JSoPdtStockConditionsDelete(ptPdtCode, ptBchCode, ptWahCode, ptBchName) {
        var nStaSession = JCNxFuncChkSessionExpired();
        if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
            let tDeleteYesOrNot = $('#oetTextComfirmDeleteYesOrNot').val();
            $('#odvModalDeleteStockConditions #ospTextConfirmDelSingle').html($('#oetTextComfirmDeleteSingle').val() + ptPdtCode + ' (' + ptBchName + ') ' + tDeleteYesOrNot);
            $('#odvModalDeleteStockConditions').modal('show');
            $('#odvModalDeleteStockConditions #osmConfirmDelSingle').unbind().click(function() {
                JCNxOpenLoading();
                $.ajax({
                    type: "POST",
                    url: "pdtEventDeleteStockConditions",
                    data: {
                        ptPdtCode: ptPdtCode,
                        ptBchCode: ptBchCode,
                        ptWahCode: ptWahCode
                    },
                    cache: false,
                    timeout: 0,
                    success: function(oResult) {
                        var aReturn = JSON.parse(oResult);
                        if (aReturn['nStaEvent'] == 1) {
                            $('#odvModalDeleteStockConditions').modal('hide');
                            $('#odvModalDeleteStockConditions #ospTextConfirmDelSingle').html($('#oetTextComfirmDeleteSingle').val());
                            $('.modal-backdrop').remove();
                            JSvPdtCallpageStockConditions();
                        } else {
                            alert(aReturn['tStaMessg']);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        JCNxResponseError(jqXHR, textStatus, errorThrown);
                    }
                });
            });
        } else {
            JCNxShowMsgSessionExpired();
        }
    }
</script>