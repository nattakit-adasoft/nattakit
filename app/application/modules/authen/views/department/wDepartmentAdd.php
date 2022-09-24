<?php
    if(isset($nStaAddOrEdit) && $nStaAddOrEdit == 1){
        $tRoute     = "departmentEventEdit"; 
        $tDptCode   = $aDptData['raItems']['rtDptCode'];
        $tDptName   = $aDptData['raItems']['rtDptName'];
        $tDptRmk    = $aDptData['raItems']['rtDptRmk'];
    }else{
        $tRoute     = "departmentEventAdd";
        $tDptCode   = "";
        $tDptName   = "";
        $tDptRmk    = "";
    }
?>

<form class="validate-form" action="javascript:void(0)" method="post" enctype="multipart/form-data" id="ofmAddEditDepartment">
    <button style="display:none" type="submit" id="obtSubmitDpt" onclick="JSoAddEditDpt('<?php echo  $tRoute?>')"></button>
    <div class="panel-body" style="padding-top:20px !important;">
        <div class="row">
            <div class="col-xs-12 col-md-5 col-lg-5">
                <label class="xCNLabelFrm"><span style="color:red">*</span><?php echo language('authen/department/department','tDPTCode');?></label>
                <div class="form-group" id="odvDepartmentAutoGenCode">
                    <div class="validate-input">
                        <label class="fancy-checkbox">
                            <input type="checkbox" id="ocbDepartmentAutoGenCode" name="ocbDepartmentAutoGenCode" checked="true" value="1">
                            <span> <?php echo language('common/main/main', 'tGenerateAuto'); ?></span>
                        </label>
                    </div>
                </div>
                <div class="form-group" id="odvDepartmentCodeForm">
                    <input type="hidden" id="ohdCheckDuplicateDptCode" name="ohdCheckDuplicateDptCode" value="1"> 
                    <div class="validate-input">
                        <input 
                            type="text" 
                            class="form-control xCNGenarateCodeTextInputValidate" 
                            maxlength="5" 
                            id="oetDptCode" 
                            name="oetDptCode"
                            placeholder="<?php echo language('authen/department/department','tDPTCode');?>"
                            data-is-created="<?php echo $tDptCode; ?>"
                            value="<?php echo $tDptCode; ?>" 
                            autocomplete="off"
                            data-validate-required = "<?php echo language('authen/department/department','tDPTValidCode');?>"
                            data-validate-dublicateCode = "<?php echo language('authen/department/department','tDPTValidCodeDup')?>"
                        >
                    </div>
                </div>
                <div class="form-group">
                    <label class="xCNLabelFrm"><span style =color:red>*</span><?php echo language('authen/department/department','tDPTName')?></label> <!-- เปลี่ยนชื่อ Class -->
                    <input type="text" class="form-control" maxlength="50" id="oetDptName" name="oetDptName" value="<?php echo $tDptName?>" 
                    placeholder="<?php echo language('authen/department/department','tDPTName')?>"
                    autocomplete="off"
                    data-validate-required = "<?php echo language('authen/department/department','tDPTValidName')?>"> <!-- เปลี่ยนชื่อ Class เพิ่ม DataValidate -->
                </div>
                <div class="form-group">
                    <div class="validate-input">
                        <label class="xCNLabelFrm"><?php echo language('authen/department/department','tDPTRemark')?></label>
                        <textarea class="form-control" maxlength="100" rows="4" id="otaDptRemark" name="otaDptRemark"><?php echo $tDptRmk; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url(); ?>application/modules/common/assets/js/jquery.mask.js"></script>
<script src="<?php echo base_url(); ?>application/modules/common/assets/src/jFormValidate.js"></script>
<?php include "script/jDepartmentAdd.php"; ?>