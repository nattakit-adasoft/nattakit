var nDSHSALBrowseType = $("#ohdDSHSALBrowseType").val();
var tDSHSALBrowseOption = $("#ohdDSHSALBrowseOption").val();

$("document").ready(function() {
    localStorage.removeItem("LocalItemData");
    JSxCheckPinMenuClose();
    if (typeof(nDSHSALBrowseType) != 'undefined' && nDSHSALBrowseType == 0) {

        $('.xCNDatePicker').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: true,
            enableOnReadonly: false,
            disableTouchKeyboard: true,
            autoclose: true
        });

        // Event Click Button Filter Date Data Form
        $('#obtDSHSALDateDataForm').unbind().click(function() {
            $('#oetDSHSALDateDataForm').datepicker('show');
        });

        // Event Click Button Filter Date Data To
        $('#obtDSHSALDateDataTo').unbind().click(function() {
            $('#oetDSHSALDateDataTo').datepicker('show');
        });


        $('#oetDSHSALDateDataForm').change(function() {
            JCNxOpenLoading();
            JSvDSHSALPageDashBoardMain();
        });

        $('#oetDSHSALDateDataTo').change(function() {
            JCNxOpenLoading();
            JSvDSHSALPageDashBoardMain();
        });

        // Event Click Navigater Title (คลิก Title ของเอกสาร)
        $('#oliDSHSALTitle').unbind().click(function() {
            let nStaSession = JCNxFuncChkSessionExpired();
            if (typeof(nStaSession) !== "undefined" && nStaSession == 1) {
                JSvDSHSALPageDashBoardMain();
            } else {
                JCNxShowMsgSessionExpired();
            }
        });

        JCNxOpenLoading();
        JSvDSHSALPageDashBoardMain();
    }
});

// Function: Call Main Page DashBoard
// Parameters: Document Ready Or Parameter Event
// Creator: 14/01/2020 Wasin(Yoshi)
// Return: View Page Main
// ReturnType: View
function JSvDSHSALPageDashBoardMain() {
    JCNxOpenLoading();
    const tDateDataForm = $('#oetDSHSALDateDataForm').val();
    const tDateDataTo = $('#oetDSHSALDateDataTo').val();
    $.ajax({
        type: "POST",
        url: "dashboardsaleTableMainPage",
        cache: false,
        data: {
            'ptDateDataForm': tDateDataForm,
            'ptDateDataTo': tDateDataTo
        },
        timeout: 0,
        success: function(tResult) {
            $("#odvDSHSALContentPage").html(tResult);
  

            JSvDSHSALCallViewTotalByBranch();
            JCNxCloseLoading();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}

// Function: Call Modal Option Modal Filter
// Parameters: Document Ready Or Parameter Event
// Creator: 31/01/2020 Wasin(Yoshi)
// Return: View Page Main
// ReturnType: View
function JSvDSHSALCallModalFilterDashBoard(ptFilterDataKey, ptFilterDataGrp) {
    $.ajax({
        type: "POST",
        url: "dashboardsaleTableCallModalFilter",
        data: {
            'ptFilterDataKey': ptFilterDataKey,
            'ptFilterDataGrp': ptFilterDataGrp,
        },
        cache: false,
        timeout: 0,
        success: function(ptViewModalHtml) {
            $('#odvDSHSALModalFilterHTML').html(ptViewModalHtml);
            $('#odvDSHSALModalFilter').modal({ backdrop: 'static', keyboard: false })
            $('#odvDSHSALModalFilter').modal('show');
        },
        error: function(xhr, status, error) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}

// Function: Confirm Filter DashBoard
// Parameters: Document Ready Or Parameter Event
// Creator: 06/02/2020 Wasin(Yoshi)
// Return: View Page Main
// ReturnType: View
function JCNxDSHSALConfirmFilter(ptFilterKey) {
    const tDateDataForm = $('#oetDSHSALDateDataForm').val();
    const tDateDataTo = $('#oetDSHSALDateDataTo').val();
    $.ajax({
        type: "POST",
        url: "dashboardsaleTableConfirmFilter",
        data: $('#odvDSHSALModalFilter #ofmDSHSALFormFilter').serialize() + "&ptDateDataForm=" + tDateDataForm + "&ptDateDataTo=" + tDateDataTo + "",
        cache: false,
        timeout: 0,
        success: function(paDataReturn) {
            const aDataReturn = JSON.parse(paDataReturn);
            $('#odvDSHSALModalFilter').modal('hide');
            JCNxDSHSALOpenLodingChart(ptFilterKey);
            setTimeout(function() {
                switch (aDataReturn['rtDSHSALTypeKey']) {
                   
                    case 'FBB':
                        {
                            // ข้อมูลการขายตามสาขา ตามจุดขาย
                            JSvDSHSALCallViewTotalByBranch();
                            break;
                        }
                
                }
            }, 1000)
            setTimeout(function() {
                JCNxDSHSALCloseLodingChart(aDataReturn['rtDSHSALTypeKey']);
            }, 2000)
        },
        error: function(xhr, status, error) {
            JCNxResponseError(jqXHR, textStatus, errorThrown);
        }
    });
}

// Function: Open Loding DashBoard
// Parameters: Document Ready Or Parameter Event
// Creator: 13/02/2020 Wasin(Yoshi)
// Return: View Open Loding
// ReturnType: Open Loding
function JCNxDSHSALOpenLodingChart(ptFilterKey) {
    $(".xWOverlayLodingChart[data-keyfilter='" + ptFilterKey + "']").delay(5).fadeIn();
}

// Function: Close Loding DashBoard
// Parameters: Document Ready Or Parameter Event
// Creator: 13/02/2020 Wasin(Yoshi)
// Return: View Open Loding
// ReturnType: Open Loding
function JCNxDSHSALCloseLodingChart(ptFilterKey) {
    $(".xWOverlayLodingChart[data-keyfilter='" + ptFilterKey + "']").delay(10).fadeOut();
}


// ============================================== Show View DashBoard ==============================================





// Function: Call View Tabal Total By Branch
// Parameters: Document Ready Or Parameter Event
// Creator: 10/06/2020 Worakorn
// Return: View Page Main
// ReturnType: View
function JSvDSHSALCallViewTotalByBranch() {

    JSvTotalByBranchDataTable();
}

//function: Call Product Type Data List
//Parameters: Ajax Success Event 
//Creator:	06/10/2020 Worakorn
//Return: View
//Return Type: View

function JSvTotalByBranchDataTable(pnPage) {

    const nPageCurrent = (pnPage === undefined || pnPage == '') ? '1' : pnPage;
    const tDateDataForm = $('#oetDSHSALDateDataForm').val();
    const tDateDataTo = $('#oetDSHSALDateDataTo').val();
    const oetDSHSALSort = $('#oetDSHSALSort').val();
    const oetDSHSALFild = $('#oetDSHSALFild').val();



        $.ajax({
            type: "POST",
            url: "dashboardsaleTableTotalByBranch",
            cache: false,
            data: $('#odvDSHSALModalFilter #ofmDSHSALFormFilter').serialize() + "&ptDateDataForm=" + tDateDataForm + "&ptDateDataTo=" + tDateDataTo + "&nPageCurrent=" + nPageCurrent + "&oetDSHSALSort=" + oetDSHSALSort + "&oetDSHSALFild=" + oetDSHSALFild + "",
            timeout: 0,
            success: function(ptResult) {
                $('#odvDSHSALPanelLeft5 .xWDSHSALDataPanel').html(
                    ptResult
                );
            },
            error: function(xhr, status, error) {
                JCNxResponseError(jqXHR, textStatus, errorThrown);
            }
        });
    
}

// Function: Sort And Filter Total By Branch
// Parameters: Document Ready Or Parameter Event
// Creator: 17/06/2020 Worakorn
// Return: View Page Main
// ReturnType: View
function JSvTotalByBranchSort(ptFild) {

    const oetDSHSALSort = $('#oetDSHSALSort').val();

    $('#oetDSHSALFild').val(ptFild);

    if (oetDSHSALSort == 'ASC') {
        $('#oetDSHSALSort').val('DESC');
    } else {
        $('#oetDSHSALSort').val('ASC');
    }
    JSvTotalByBranchDataTable();

}
// ============================================== Show View DashBoard ==============================================