$(document).ready(function () {

    $('body').on("keyup blur change", '.xCNInputNumericWithDecimal', function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $('body').on("keypress", '.xCNInputNumericWithoutDecimal', function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        InputId = event.target.id;
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $("body").on("keyup", ".xCNInputNumeric", function (event) {
        var nInput = $(this).val();
        var tNumPattern = /^[0-9]+$/;
        if (!nInput.match(tNumPattern)) {
            $(this).val("");
            event.preventDefault();
        }
    });

    $("body").on("change", ".xCNInputLimitDecimal", function (event) {
        var nLimit = parseInt($(this).data('limit'));
        var cInput = parseFloat($(this).val());
        $(this).val(cInput.toFixedNoRounding(nLimit));
    });

    $("body").on("keyup change", ".xCNInputLength", function (event) {
        var nLength = $(this).data('length');
        var nInputLength = $(this).val().length;
        if (nInputLength > nLength) {
            $(this).val("");
            event.preventDefault();
        }
    });

    $("body").on("keypress keyup blur change", ".xCNInputMaxValue", function (event) {
        var nMaxValue = $(this).data('max');
        var nInputValue = $(this).val();
        console.log(nInputValue > nMaxValue && nMaxValue !== "");
        if (nInputValue > nMaxValue && nMaxValue !== "") {
            $(this).val("");
            event.preventDefault();
        }
    });

    $("body").on("keypress keyup blur change", ".xCNInputMinValue", function (event) {
        var nMinValue = $(this).data('min');
        var nInputValue = $(this).val();
        if (nInputValue < nMinValue && nMinValue !== "") {
            $(this).val("");
            event.preventDefault();
        }
    });

    $(".xCNInputAddressNumber").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /^\s*[0-9,/,-]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $('.xCNInputWithoutSpc').on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        // var tCharacterReg = /^\s*[a-z,A-Z,ก-๙, ,0-9,@,-]+\s*$/;
        var tCharacterReg = /^\s*[a-z,A-Z,ก-๙,0-9,@,-]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $('.xCNInputWithoutSpcNotThai').on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /^\s*[a-z,A-Z,0-9,ก-๙]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $(".xCNInputOnlyEng").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /[A-Za-z0-9]/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $(".xCNInputWithoutSingleQuote").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        // var tCharacterReg = /(?=.*[!@#$%\^&*()+}{";'?><,])/g;
        var tCharacterReg = /(?=.*['"])/g;
        if (tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $(".xCNGenarateCodeTextInputValidate").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /(?=.*[!\\^;?><,'"])/g;
        if (tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $(".xCNInputVandingTemperature").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /^\s*[0-9\.,-]+\s*$/;
        if (!tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $("input , textarea").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /(?=.*['"])/g;
        if (tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    // create : 10/04/2019 pap
    // function : event input no single and double quote only
    $(".xCNInputWithoutSingleAndDoubleQuote").on("keypress keyup blur", function (event) {
        var tInputVal = $(this).val();
        var tCharacterReg = /(?=.*[!\^\"\'])/g;
        if (tCharacterReg.test(tInputVal) && tInputVal != '') {
            $(this).val(tInputVal.slice(0, -1));
            event.preventDefault();
        }
    });

    $('.xCNInputMaskTel').mask('000-0000000');
    $('.xCNInputMaskFax').mask('000-0000000');
    $('.xCNInputMaskTaxNo').mask('0000000000000');
    $('.xCNInputMaskDateTime').mask('0000-00-00 00:00:00');

    $('.xCNInputMaskDate').mask('0000-00-00');
    $('.xCNInputMaskTime').mask('00:00:00');

    $('.xCNInputMaskCurrency').on("blur", function () {
        var tInputVal = $(this).val();
        tInputVal += '';
        tInputVal = tInputVal.replace(',', '');
        tInputVal = tInputVal.split('.');
        tValCurency = tInputVal[0];
        tDegitInput = tInputVal.length > 1 ? '.' + tInputVal[1] : '';
        var tCharecterComma = /(\d+)(\d{3})/;
        while (tCharecterComma.test(tValCurency))
            tValCurency = tValCurency.replace(tCharecterComma, '$1' + ',' + '$2');
        var tInputReplaceComma = tValCurency + tDegitInput;
        var tSearch = ".";
        var tStrinreplace = ".00";
        var tInputCommaDegit = ""
        if (tInputReplaceComma.indexOf(tSearch) == -1 && tInputReplaceComma != "") {
            tInputCommaDegit = tInputReplaceComma.concat(tStrinreplace);
        } else {
            tInputCommaDegit = tInputReplaceComma;
        }
        $(this).val(tInputCommaDegit);
    });

    //Chk Percent
    $('.xCNInputMaskPercent').mask("#,###0,000 %", { reverse: true });
});




//Functionality : validate number
//Parameters : ptObjName = [ID input] , ptTypeNumber = [FN,FC] , pnPosition = [2]
//Creator : 01/10/2018 Phisan(arm)
//Return : validate number
//Return Type : Decimal
function JCNdValidatelength8Decimal(ptObjName, ptTypeNumber, ptMaxlength, pnPosition) {
    var cNum;
    var nVal;

    var nValx = $('#' + ptObjName).val(); //ดักกดจุดอย่างเดียว
    var tNumberx = nValx.toString(); //ดักกดจุดอย่างเดียว
    var tNumNotCommax = tNumberx.replace(",", ""); //ดักกดจุดอย่างเดียว
    var bDotx = tNumNotCommax.includes(".");
    if (tNumNotCommax.length == 1) { //ดักกดจุดอย่างเดียว
        if (bDotx == true) {
            nVal = '0.00';
        } else {
            nVal = $('#' + ptObjName).val() * 1; //*1เพราะมี0นำหน้ามา
        }
    } else {
        nVal = $('#' + ptObjName).val() * 1; //*1เพราะมี0นำหน้ามา
    }
    var tNumber = nVal.toString();
    var tNumNotComma = tNumber.replace(",", "");

    if (ptTypeNumber == 'FC') {
        var bDot = tNumNotComma.includes(".");
        if (bDot == true) {
            var cPow = Math.pow(10, pnPosition);
            var dRound = Math.round(tNumNotComma * cPow) / cPow;
            var tRound = dRound.toString()
            var bRound = tRound.includes(".");
            if (bRound == true) {
                cNum = tRound;
            } else {
                cNum = tRound + '.00';
            }
        } else {
            cNum = tNumNotComma + '.00';
        }
        var tMaxValML = '9';
        var tFinalMaxValML = '9';
        for (var i = 1; i < ptMaxlength; i++) {
            tFinalMaxValML += tMaxValML;
        }
        var tMaxValPT = '9';
        var tFinalMaxValPT = '9';
        for (var i = 1; i < pnPosition; i++) {
            tFinalMaxValPT += tMaxValPT;
        }
        var cFinalMaxVal = parseFloat(tFinalMaxValML + '.' + tFinalMaxValPT);
        if (cNum > cFinalMaxVal) {
            cNum = cFinalMaxVal;
        }
    } else {
        cNum = tNumNotComma;
    }

    var cNumx = parseFloat(cNum);

    if (ptObjName == 'oetCrdDeposit' || ptObjName == 'oetCtyDeposit') {
        if (cNumx >= 100) {
            var cNumFinal = 100;
        } else {
            var cNumFinal = cNumx.toLocaleString({ minimumFractionDigits: 4 });
        }
    } else {
        var cNumFinal = cNumx.toLocaleString({ minimumFractionDigits: 4 });
    }

    $('#' + ptObjName).val(accounting.formatNumber(cNumFinal, pnPosition));
}

$.fn.selectRange = function (start, end) {
    return this.each(function () {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
            var tID = this.id;
            $('#' + tID).addClass('ValiforFN');
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};


function JCNdValidateComma(ptObjName, ptMaxlength, ptTypeNumber) {

    //$('#'+ptObjName).attr('maxlength',ptMaxlength);
    var nVal = $('#' + ptObjName).val();
    var tNumber = nVal.toString();
    var tNumNotComma = tNumber.replace(/,/g, "");
    $('#' + ptObjName).val(tNumNotComma);
    $('#' + ptObjName).selectRange(0, 500);

    var tCheckClass = $('#' + ptObjName).hasClass('ValiforFN');


    $("#" + ptObjName).on("keypress keyup", function (event) {
        var nVal = $('#' + ptObjName).val();
        var tNumber = nVal.toString();
        var tNumNotComma = tNumber.replace(/,/g, "");

        if (ptTypeNumber == 'FC') {
            var bDot = tNumNotComma.includes(".");
            //มี DOT
            if (bDot == true) {
                var aSplit = tNumNotComma.split(".");
                var nNumberArray = aSplit[0].length;
                if (nNumberArray > ptMaxlength) {
                    var nResultModi = aSplit[0].substr(-1);
                    var nResultOrg = aSplit[0].substr(0, ptMaxlength);
                    var nResultDecimal = aSplit[1];
                    $('#' + ptObjName).val(nResultOrg + '.' + nResultModi + nResultDecimal);
                }
            }
            //ไม่มี DOT
            else {
                var nNum = $('#' + ptObjName).val();
                var nNumberArray = nNum.length;
                if (nNumberArray > ptMaxlength) {
                    var nResultModi = nNum.substr(-1);
                    var nResultOrg = nNum.substr(0, ptMaxlength);
                    $('#' + ptObjName).val(nResultOrg + '.' + nResultModi);
                }
            }
            $('#' + ptObjName).removeClass('ValiforFN');
        } else { //if(ptTypeNumber == 'FN')
            var nValue = $('#' + ptObjName).val();
            var nLenValue = nValue.length;

            var tCheckClass = $('#' + ptObjName).hasClass('ValiforFN');
            if (tCheckClass == true) {
                $('#' + ptObjName).val('');
                $('#' + ptObjName).removeClass('ValiforFN');
            } else {
                if (nLenValue == ptMaxlength) {
                    event.preventDefault();
                }
            }
        }
    });
}

Number.prototype.toFixedNoRounding = function (n) {
    const reg = new RegExp("^-?\\d+(?:\\.\\d{0," + n + "})?", "g")
    const a = this.toString().match(reg)[0];
    const dot = a.indexOf(".");
    if (dot === -1) { // integer, insert decimal dot and pad up zeros
        return a + "." + "0".repeat(n);
    }
    const b = n - (a.length - dot) + 1;
    return b > 0 ? (a + "0".repeat(b)) : a;
}