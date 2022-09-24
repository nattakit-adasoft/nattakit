<?php
    //Decimal Show ลง 2 ตำแหน่ง
    $nDecShow =  FCNxHGetOptionDecimalShow();

    if(isset($aItems)){
        $tStaEnter      = "2"; //Edit
        $tPdtCodeSet    = $aItems[0]['FTPdtCodeSet'];
        $tPdtNameSet    = $aItems[0]['FTPdtName'];
        $cPdtSetQty     = $aItems[0]['FCPstQty'];
        $cPgdPriceNet   = $aItems[0]['FCPgdPriceNet'];
        $tPunName       = $aItems[0]['FTPunName'];
    }else{
        $tStaEnter      = "1"; //Add
        $tPdtCodeSet    = "";
        $tPdtNameSet    = "";
        $cPdtSetQty     = 1;
        $cPgdPriceNet   = 0;
        $tPunName       = "";
    }
?>

<script>

    var nStaChk = $('#oetPdtSetStaEnter').val();
    if(nStaChk == "2"){
        $('.xWBtnPdtSetAddProduct').attr('disabled',true);
    }

</script>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <input type="text" id="oetPdtSetStaEnter" name="oetPdtSetStaEnter" class="form-control xCNHide" value="<?=$tStaEnter?>">
            <div class="form-group">
                <label class="xCNLabelFrm"><?=language('product/product/product','tPDTCode');?></label>
                <div class="input-group">
                    <input 
                        type            = "text" 
                        id              = "oetPdtSetPdtCode" 
                        name            = "oetPdtSetPdtCode" 
                        class           = "form-control" 
                        value           = "<?=$tPdtCodeSet?>" 
                        placeholder     = "<?=language('product/product/product','tPDTCode');?>" 
                        data-validate   = "<?=language('product/product/product','tPDTSETValidPdtCode');?>"
                        readonly
                    >
                    <span class="input-group-btn">
                        <button type="button" class="btn xCNBtnBrowseAddOn xWBtnPdtSetAddProduct" onclick="JSxPdtSetBrowseProduct()"><img class="xCNIconFind"></button>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="xCNLabelFrm"><span style = "color:red">*</span> <?=language('product/product/product','tPDTName');?></label>
                <input 
                    type="text"
                    class="form-control"
                    maxlength="100"
                    id="oetPdtSetPdtName"
                    name="oetPdtSetPdtName"
                    value="<?=$tPdtNameSet?>"
                    readonly
                    placeholder="<?=language('product/product/product','tPDTName');?>"
                >
            </div>

            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label class="xCNLabelFrm"><span style = "color:red">*</span> <?=language('product/product/product','tPDTSetPstQty');?></label>
                        <input 
                            type="text"
                            class="form-control"
                            maxlength="18"
                            id="oetPdtSetPstQty"
                            name="oetPdtSetPstQty"
                            value="<?=number_format($cPdtSetQty,$nDecShow);?>"
                            placeholder="<?=language('product/product/product','tPDTSetPstQty');?>"
                            autocomplete="off"
                            data-validate = "<?=language('product/product/product','tPDTSETValidPstQty');?>"
                        >
                    </div>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <input 
                        type="text"
                        class="form-control"
                        style="margin-top: 25px;"
                        id="oetPdtSetUnitName"
                        name="oetPdtSetUnitName"
                        value="<?=$tPunName?>"
                        placeholder="<?=language('product/product/product','tPDTTBUnit');?>"
                        readonly
                    >
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <input 
                        type="text"
                        class="form-control"
                        style="margin-top: 25px;"
                        id="oetPdtSetPrice"
                        name="oetPdtSetPrice"
                        value="<?=number_format($cPgdPriceNet,$nDecShow);?>"
                        placeholder="<?=language('product/product/product','tPDTTBPrice');?>"
                        readonly
                    >
                </div>
            </div>
        </div>
    </div>
</div>
