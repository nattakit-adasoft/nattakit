$('ducument').ready(function() {
    JSxCheckPinMenuClose(); /*Check เปิดปิด Menu ตาม Pin*/
    //โหลดข้อมูล view ค้นหา 
    // JSvCallPageMevementList(1);
    JSxMmtRenderContentTab();
});

function JSxMmtRenderContentTab() {
    $.ajax({
        type: "GET",
        url: "mmtMMTPageContentTab",
        cache: false,
        success: function(tResult) {
            $('#odvMmtContentTabContainer').html(tResult);
            JSvCallPageMevementList(1);
            // JCNxInvList(1);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}

///function : โหลด แถบ ค้นหา 
//Parameters : เลข Page
//Creator:	10/03/2020 Saharat(Golf)
//Update:  -
//Return : View
//Return Type : View
function JSvCallPageMevementList(pnPage) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        $.ajax({
            type: "POST",
            url: "movementList",
            cache: false,
            success: function(tResult) {
                $('#odvContentPageMovement').html(tResult);
                JSvMevementDataTable(pnPage);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    } else {
        JCNxShowMsgSessionExpired();
    }
}

///function : โหลดข้อมูล view ความเคลื่อนไหวสินค้า
//Parameters : เลข Page
//Creator:	10/03/2020 Saharat(Golf)
//Update:   
//Return : View
//Return Type : View
function JSvMevementDataTable(pnPage) {
    var nStaSession = JCNxFuncChkSessionExpired();
    if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
        //ตรวจสอบ Value สาขา
        var ptBchCode = $('#oetMmtBchCodeSelect').val();
        if (ptBchCode == undefined || ptBchCode == '') {
            tBchCode = '';
        } else {
            tBchCode = $('#oetMmtBchCodeSelect').val();
        }
        //ตรวจสอบ Value ร้านค้า
        var ptShpCode = $('#oetMmtShpCodeSelect').val();
        if (ptShpCode == undefined || ptShpCode == '') {
            tShpCode = '';
        } else {
            tShpCode = $('#oetMmtShpCodeSelect').val();
        }
        //ตรวจสอบ Value คลังสินค้า
        var ptWahCode = $('#oetMmtWahCodeSelect').val();
        if (ptWahCode == undefined || ptWahCode == '') {
            tWahCode = '';
        } else {
            tWahCode = $('#oetMmtWahCodeSelect').val();
        }
        //ตรวจสอบ Value สินค้า
        var ptPdtCode = $('#oetMmtPdtCodeSelect').val();
        if (ptPdtCode == undefined || ptPdtCode == '') {
            tPdtCode = '';
        } else {
            tPdtCode = $('#oetMmtPdtCodeSelect').val();
        }
        //ตรวจสอบ Value วันที่
        var pdDateStart = $('#oetMmtDateStart').val();
        if (pdDateStart == undefined || pdDateStart == '') {
            dDateStart = '';
        } else {
            dDateStart = $('#oetMmtDateStart').val();
        }
        //ตรวจสอบ Value วันที่
        var pdDateTo = $('#oetMmtDateTo').val();
        if (pdDateTo == undefined || pdDateTo == '') {
            dDateTo = '';
        } else {
            dDateTo = $('#oetMmtDateTo').val();
        }
        //set value json
        var tDataFilter = {
            "tBchCode": tBchCode,
            "tShpCode": tShpCode,
            "tWahCode": tWahCode,
            "tPdtCode": tPdtCode,
            "dDateStart": dDateStart,
            "dDateTo": dDateTo
        };
        var tSearchAll = $('#oetSearchAll').val();
        var nPageCurrent = pnPage;
        if (nPageCurrent == undefined || nPageCurrent == '') {
            nPageCurrent = '1';
        }
        JCNxOpenLoading();
        $.ajax({
            type: "POST",
            url: "movementDataTable",
            data: {
                tDataFilter: JSON.stringify(tDataFilter),
                nPageCurrent: nPageCurrent,
            },
            cache: false,
            Timeout: 0,
            success: function(tResult) {
                $('#odvContentMovement').html(tResult);

                JCNxLayoutControll();
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

//Functionality: เปลี่ยนหน้า pagenation
//Parameters: -
//Creator: 11/03/202020 Saharat(Golf)
//Update: 15/04/2020 surawat
//Return: View
//Return Type: View
function JSvClickPage(ptPage) {
    var nPageCurrent = '';
    var nPageNew;
    switch (ptPage) {
        case 'next': //กดปุ่ม Next
            nPageCurrent = $('#obtMmtNextPage').data('ngotopage');
            break;
        case 'previous': //กดปุ่ม Previous
            nPageCurrent = $('#obtMmtPreviousPage').data('ngotopage');
            break;
        default:
            nPageCurrent = ptPage;
    }
    JSvMevementDataTable(nPageCurrent);
}

//function : ค้นหา ข้อมูล
//Parameters : Data Filter
//Creator:	11/03/2020 Saharat(Golf)
//Update:   -
//Return : View
//Return Type : View
function JSvMevementSearchData() {
    //ตรวจสอบ Value สาขา
    var ptBchCode = $('#oetMmtBchCodeSelect').val();
    if (ptBchCode == undefined || ptBchCode == '') {
        tBchCode = '';
    } else {
        tBchCode = $('#oetMmtBchCodeSelect').val();
    }
    //ตรวจสอบ Value ร้านค้า
    var ptShpCode = $('#oetMmtShpCodeSelect').val();
    if (ptShpCode == undefined || ptShpCode == '') {
        tShpCode = '';
    } else {
        tShpCode = $('#oetMmtShpCodeSelect').val();
    }
    //ตรวจสอบ Value คลังสินค้า
    var ptWahCode = $('#oetMmtWahCodeSelect').val();
    if (ptWahCode == undefined || ptWahCode == '') {
        tWahCode = '';
    } else {
        tWahCode = $('#oetMmtWahCodeSelect').val();
    }
    //ตรวจสอบ Value สินค้า
    var ptPdtCode = $('#oetMmtPdtCodeSelect').val();
    if (ptPdtCode == undefined || ptPdtCode == '') {
        tPdtCode = '';
    } else {
        tPdtCode = $('#oetMmtPdtCodeSelect').val();
    }
    //ตรวจสอบ Value วันที่
    var pdDateStart = $('#oetMmtDateStart').val();
    if (pdDateStart == undefined || pdDateStart == '') {
        dDateStart = '';
    } else {
        dDateStart = $('#oetMmtDateStart').val();
    }
    //ตรวจสอบ Value วันที่
    var pdDateTo = $('#oetMmtDateTo').val();
    if (pdDateTo == undefined || pdDateTo == '') {
        dDateTo = '';
    } else {
        dDateTo = $('#oetMmtDateTo').val();
    }
    //set value json
    var tDataFilter = {
        "tBchCode": tBchCode,
        "tShpCode": tShpCode,
        "tWahCode": tWahCode,
        "tPdtCode": tPdtCode,
        "dDateStart": dDateStart,
        "dDateTo": dDateTo
    };
    JCNxOpenLoading();
    $.ajax({
        type: "POST",
        url: "movementDataTable",
        data: { tDataFilter: JSON.stringify(tDataFilter) },
        success: function(oResult) {
            $('#odvContentMovement').html(oResult);
            JCNxCloseLoading();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });

}