<?php
if($aResult['rtCode'] == '1')
{
	//Table Master
    $tBchCode       	= $aResult['roItem']['rtBchCode'];
    $tBchType       	= $aResult['roItem']['rtBchType'];
    $tBchPriority       = $aResult['roItem']['rtBchPriority'];
    $tBchRefID       	= $aResult['roItem']['rtBchRefID'];
    $dBchStart       	= $aResult['roItem']['rdBchStart'];
    $dBchStop       	= $aResult['roItem']['rdBchStop'];
    $dBchSaleStart      = $aResult['roItem']['rdBchSaleStart'];
    $dBchSaleStop       = $aResult['roItem']['rdBchSaleStop'];
	$tBchStaActive      = $aResult['roItem']['rtBchStaActive'];
    $tBchRegNo			= $aResult['roItem']['rtBchRegNo'];
    
    //Table Lang
    $tBchName       = $aResult['roItem']['rtBchName'];
    $tBchRmk        = $aResult['roItem']['rtBchRmk'];

    //Img
    $tImgObj     = $aResult['roItem']['rtImgObj'];

}
?>
<input type="hidden" id="tBchPriority" name="tBchPriority" value="<?php echo @$tBchPriority?>">
<input type="hidden" id="tBchType" name="tBchType" value="<?php echo @$tBchType?>">
<input type="hidden" id="tBchStaActive" name="tBchStaActive" value="<?php echo @$tBchStaActive?>">
<script>

    oBchPriority = $('#tBchPriority').val();
    $("#ocmBchPriority option[value='" + oBchPriority + "']").attr('selected', true).trigger('change');

    tBchType = $('#tBchType').val();
    $("#ocmBchType option[value='" + tBchType + "']").attr('selected', true).trigger('change');

    tBchType = $('#tBchStaActive').val();
    $("#ocmBchStaActive option[value='" + tBchType + "']").attr('selected', true).trigger('change');

    
    $('.selectpicker').selectpicker();
    $(document).ready(function () {
        $(".xWEditPark").validate({
            rules: {
                oetFTPmoName: "required",
                oetFTPmoNameOth: "required",
                oetBchRegNo: "required",
                oetBchName: "required"
            },
            messages: {
                oetFTPmoName: "",
                oetFTPmoNameOth: "",
                oetBchRegNo:"",
                oetBchName:""
            },
            errorClass: "alert-validate",
            validClass: "",
            highlight: function (element, errorClass, validClass) {
                $(element).parent('.validate-input').addClass(errorClass).removeClass(validClass);
                $(element).parent().parent('.validate-input').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent('.validate-input').removeClass(errorClass).addClass(validClass);
                $(element).parent().parent('.validate-input').removeClass(errorClass).addClass(validClass);
            },
            submitHandler: function (form) {
                $('button[type=submit]').attr('disabled', true);
                $('.xCNOverlay').show();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>EticketEditBranchAjax",
                    data: $(".xWEditPark").serialize(),
                    cache: false,
                    success: function (msg) {
                        var nDataId = $('.xWBtnSaveActive').data('id');
                        if (nDataId == '1') {
                            JSxCallPage('<?= base_url() ?>EticketEditBranch/<?php echo @$tBchCode?>?prk=1')
                        } else if (nDataId == '2') {
                            JSxCallPage('<?= base_url() ?>EticketAddBranch')
                        } else if (nDataId == '3') {
                        <?php if (@$_GET['prk'] == '1'): ?>
                            JSxCallPage('<?php echo base_url('EticketLocation/') ?><?php echo @$tBchCode?>');
                        <?php else : ?>
                            JSxCallPage('<?php echo base_url('EticketBranch') ?>');
                        <?php endif; ?>
                        }
                        $('.xCNOverlay').hide();
                    },
                    error: function (data) {
                        console.log(data);
                        $('.xCNOverlay').hide();
                    }
                });
                return false;
            }
        });
        $('[title]').tooltip();
    });
</script>


<div class="main-menu">
    <form action="" method="post" autocorrect="off" autocapitalize="off" autocomplete="off" class="xWEditPark">
        <div class="xCNMrgNavMenu">
                <div class="row xCNavRow" style="width:inherit;">
                    <div class="xCNBchVMaster">
                        <div class="col-xs-8 col-md-8">
                            <ol id="oliMenuNav" class="breadcrumb">
                                <li id="oliBCHTitle" class="xCNLinkClick" onclick="JSxCallPage('<?php echo base_url('EticketBranch') ?>')"><?= language('ticket/park/park', 'tBranchInformation') ?></li> 
                                <li  class="xCNLinkClick"><?= language('ticket/park/park', 'tEditPark') ?></li>
                                </ol>       
                                </div>
                                <div class="col-xs-12 col-md-4 text-right p-r-0">
                                <button type="button" onclick="JSxCallPage('<?php echo base_url('EticketBranch') ?>')" class="btn btn-default xCNBTNDefult"><?= language('common/main/main', 'tBack') ?></button>
                            <div class="btn-group">
                                <button class="btn btn-default xWBtnGrpSaveLeft" type="submit"><?= language('ticket/user/user', 'tSave') ?></button>
                                <button type="button" class="btn btn-default xWBtnGrpSaveRight dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu xWDrpDwnMenuMargLft">
                                    <li class="xWolibtnsave1 xWBtnSaveActive" data-id="1" onclick="JSvChangeBtnSaveAction(1)"><a href="#"><?= language('common/main/main', 'tCMNSaveAndView') ?></a></li>
                                    <li class="xWolibtnsave2" data-id="2" onclick="JSvChangeBtnSaveAction(2)"><a href="#"><?= language('common/main/main', 'tCMNSaveAndNew') ?></a></li>
                                    <li class="xWolibtnsave3" data-id="3" onclick="JSvChangeBtnSaveAction(3)"><a href="#"><?= language('common/main/main', 'tCMNSaveAndBack') ?></a></li>
                                </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    <div class="main-content">
        <div class="panel panel-headline">
            <div class="panel-heading "> 

        <!--Head Tab1 -->
            <div class="custom-tabs-line tabs-line-bottom left-aligned">
                <ul class="nav" role="tablist">
                    <li class="nav-item  active">
                        <a class="nav-link flat-buttons active" data-toggle="tab" href="#oBranchTab1" role="tab" aria-expanded="false">
                            <?= language('ticket/park/park', 'tGeneralInfo') ?> 
                            </a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link flat-buttons" data-toggle="tab" href="#oBranchTab2" role="tab" aria-expanded="false">
                            <?= language('company/branch/branch', 'tBCHAddV1') ?>
                            </a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link flat-buttons" data-toggle="tab" href="#oBranchTab3" role="tab" aria-expanded="false">
                            <?= language('ticket/park/park', 'tManagelocations') ?>
                            </a>
                        </li>
                    </ul>
                </div>
            <!--END Head Tab1 -->	
                
            <!--Body Tab 1 -->
            <div class="tab-content">
                <div class="tab-pane active" style="margin-top:10px;" id="oBranchTab1" role="tabpanel" aria-expanded="true" >
					<div class="row" style="margin-right: -30px; margin-left: -30px;">
                        <div class="col-md-12">                  
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="upload-img" id="oImgUpload" style="margin-bottom: 10px;">
                                            <?php
                                                if(isset($tImgObj) && !empty($tImgObj)){
                                                    $tFullPatch = './application/modules/'.$tImgObj;
                                                    if (file_exists($tFullPatch)){
                                                        $tPatchImg = base_url().'/application/modules/'.$tImgObj;
                                                    }else{
                                                        $tPatchImg = base_url().'application/modules/common/assets/images/Noimage.png';
                                                    }
                                                }else{
                                                    $tPatchImg = base_url().'application/modules/common/assets/images/Noimage.png';
                                                }
                                            ?>

                                            <?php if(isset($tImgObj) && !empty($tImgObj)): ?>
                                                <a href="javascript:void(0)" id="oDelImgLoc" onclick="FSxDelImgPrk('<?php echo @$tBchCode; ?>', '<?php echo $tImgObj; ?>', '<?= language('ticket/center/center', 'Confirm') ?>')" style="border: 0 !important; position: absolute; right: 5px; top: 5px;"><i class="fa fa-times" style="color: red; font-size: 18px;"></i></a>
                                            <?php endif; ?>

                                            <img src="<?= $tPatchImg; ?>" style="width: 100%;" id="oimImgMasterbranch">	
                                            <span class="btn-file"> 
                                                 <input type="hidden" name="oetImgInputbranch" id="oetImgInputbranch">
                                            </span>
                                        </div>			
                           
                                        <div class="xCNUplodeImage">
                                <button type="button" class="btn xCNBTNDefult" onclick="JSvImageCallTempNEW('', '', 'branch', '16/8')"><i class="fa fa-camera"></i> <?= language('ticket/park/park', 'tSelectPhoto') ?></button>
                            </div>
                             
                        </div>					
                    <div class="col-md-8 xCNDivOverFlow">
                <div class="row">
                    <div class="col-md-6">
                        <label class="xCNLabelFrm"><span style = "color:red">*</span><?php echo  language('company/branch/branch','tBCHCode')?></label>
                            <div class="form-group" id="oetDepartCode">
                            <div class="validate-input">
                            <label class="fancy-checkbox">
                            <!-- <input type="checkbox" id="ocbBchAutoGenCode" name="ocbBchAutoGenCode" checked="true" value="1" > -->
                            <!-- <span> <?php echo language('common/main/main', 'tGenerateAuto'); ?></span> -->
                            </label>
                        </div>
                    </div>
                <div class="form-group" id="odvBchCodeForm">
                    <div class="validate-input">
                        <input 
                            type="text" 
                            class="form-control xCNInputWithoutSpcNotThai  disabled" 
                            maxlength="5" 
                            id="oetBchCode" 
                            name="oetBchCode"
                            data-is-created="<?php echo @$tBchCode?>"
                            placeholder="<?php echo language('company/branch/branch','tSHPValiBranchCode')?>"
                            value="<?php echo @$tBchCode?>" 
                            data-validate="<?php echo language('company/branch/branch','tSHPValiBranchCode')?>">
                        </div>
                    </div>
                </div> 
                </div> 
                <div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchRegNo')?></label>
							<input class="form-control" type="text" id="oetBchRegNo" name="oetBchRegNo" maxlength="30" value="<?php echo @$tBchRegNo?>" data-validate="<?php echo language('company/branch/branch','tBCHBchRegNo')?>" value="<?php echo @$tBchRegNo?>">
						</div>
					</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="xCNLabelFrm"><span class="text-danger">*</span><?php echo  language('company/branch/branch','tBCHName')?></label>
						<input class="form-control" type="text" id="oetBchName" name="oetBchName" data-toggle="tooltip" title="<?php echo language('company/branch/branch','tSHPValiBranchName')?>" data-validate="<?php echo language('company/branch/branch','tSHPValiBranchName')?>" value="<?php echo @$tBchName?>">
					</div>
				</div>
			</div>
            <div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchPriority')?></label>
						<select class="selectpicker form-control" id="ocmBchPriority" name="ocmBchPriority">
							<!-- <option value=""><?php echo  language('common/main/main', 'tCMNBlank-NA') ?></option> -->
							<option value="1"><?php echo  language('company/branch/branch', 'tBCHPriority1') ?></option>
							<option value="2"><?php echo  language('company/branch/branch', 'tBCHPriority2') ?></option>
							<option value="3"><?php echo  language('company/branch/branch', 'tBCHPriority3') ?></option>
							<option value="4"><?php echo  language('company/branch/branch', 'tBCHPriority4') ?></option>
							<option value="5"><?php echo  language('company/branch/branch', 'tBCHPriority5') ?></option>
						</select>
					</div>
				</div>
					<div class="col-md-6">
						<div class="form-group">
                        <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchType')?></label>
							<select class="selectpicker form-control" id="ocmBchType" name="ocmBchType" data-validate="?????????????????????????????? <?php echo  language('common/main/main','tBCHBchType')?>">
								<option value=""><?php echo  language('common/main/main', 'tCMNBlank-NA') ?></option>
								<option value="1"><?php echo  language('company/branch/branch', 'tBCHBchTypeSEL1') ?></option>
								<option value="2"><?php echo  language('company/branch/branch', 'tBCHBchTypeSEL2') ?></option>
								<option value="3"><?php echo  language('company/branch/branch', 'tBCHBchTypeSEL3') ?></option>
							</select>
						</div>
					</div>
				</div>
                <div class="row">
						<div class="col-md-6">
							<div class="form-group">
                                <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchSaleStart')?></label>
							        <div class="input-group">
								    <input type="text" class="form-control xCNDatePicker xCNInputMaskDate" id="oetBchSaleStart" name="oetBchSaleStart" value="<?php echo  @$dBchSaleStart ?>">
									<span class="input-group-btn">
									<button id="obtBchSaleStart" type="button" class="btn xCNBtnDateTime">
									<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/icons8-Calendar-100.png'?>">
                                    </button>
                                </span>
                            </div>
						</div>
					</div>
				<div class="col-md-6">
					<div class="form-group">
                        <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchSaleStop')?></label>
                            <div class="input-group">
                                <input type="text" class="form-control xCNDatePicker xCNInputMaskDate" id="oetBchSaleStop" name="oetBchSaleStop" value="<?php echo  @$dBchSaleStop ?>">
                                    <span class="input-group-btn">
                                    <button id="obtBchSaleStop" type="button" class="btn xCNBtnDateTime">
                                    <img src="<?php echo  base_url().'application/modules/common/assets/images/icons/icons8-Calendar-100.png'?>">
                                    </button>
									</span>
								</div>
							</div>
						</div>
					</div>
                <div class="row">
					<div class="col-md-6">
						<div class="form-group">
                        <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchStart')?></label>
                        <div class="input-group">
                            <input type="text" class="form-control xCNDatePicker xCNInputMaskDate" id="oetBchStart" name="oetBchStart" value="<?php echo  @$dBchStart ?>">
								<span class="input-group-btn">
                                    <button id="obtBchStart" type="button" class="btn xCNBtnDateTime">
                                    <img src="<?php echo  base_url().'application/modules/common/assets/images/icons/icons8-Calendar-100.png'?>">
                                    </button>
								</span>
							    </div>
							</div>
						</div>	
					<div class="col-md-6">
                <div class="form-group">
                    <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchStop')?></label>
                        <div class="input-group">
                            <input type="text" class="form-control xCNDatePicker xCNInputMaskDate" id="oetBchStop" name="oetBchStop" value="<?php echo  @$dBchStop ?>">
                                <span class="input-group-btn">
									<button id="obtBchStop" type="button" class="btn xCNBtnDateTime">
									<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/icons8-Calendar-100.png'?>">
									</button>
                                </span>
                            </div>
						</div>
                    </div>
                </div>
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchRefID')?></label>
							<input class="form-control xCNInputWithoutSpc" type="text" id="oetBchRefID" name="oetBchRefID"  maxlength="30" value="<?php echo @$tBchRefID?>">
						    </div>
						<div class="form-group">
                    <label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHBchStaActive')?></label>
                <select class="selectpicker form-control" id="ocmBchStaActive" name="ocmBchStaActive" data-validate="?????????????????????????????? <?php echo  language('common/main/main','tBCHBchType')?>">
                    <option value=""><?php echo  language('common/main/main', 'tCMNBlank-NA') ?></option>
                        <option value="1"><?php echo  language('company/branch/branch', 'tBCHStaActive1') ?></option>
                            <option value="2"><?php echo  language('company/branch/branch', 'tBCHStaActive2') ?></option>
							</select>
						</div>
					</div>	
				</div>							
			</div>
		</div>
    </div>        
		<!--END Body Tab 1 -->

        <!--Body Tab 2 -->
        <div class="tab-pane" style="margin-top:10px;" id="oBranchTab2" role="tabpanel" aria-expanded="true" >
				<div class="row" style="margin-right: -30px; margin-left: -30px;">.
                    		<!-- Address -->
			<div class="main-content">
			<div class="col-md-12">
			<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHAddAreCode')?></label>
					<div class="input-group">
						<input class="form-control xCNHide" id="oetBchAreCode" name="oetBchAreCode" maxlength="5" onchange="JSxResetVal('oetBchAreCode','<?php echo @$aCnfAddPanal[0]->FTAreCode;?>',1)" value="<?php echo @$aCnfAddPanal[0]->FTAreCode;?>">
						<input class="form-control xWPointerEventNone" type="text" id="oetBchAreName" name="oetBchAreName" value="<?php echo @$aCnfAddPanal[0]->FTAreName;?>" readonly>
						<span class="input-group-btn">
							<button id="obtBchBrowseArea" type="button" class="btn xCNBtnBrowseAddOn">
								<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/find-24.png'?>">
							</button>
						</span>
					</div>
				</div>
				</div>
			</div>
			<div class="row">
			<div class="col-md-12 <?php if(@$nCnfAddVersion == '2'){ echo 'xCNHide'; } ?>">
				<input class="xCNHide" id="ohdAddVersion" name="ohdAddVersion" value="<?php echo  @$nCnfAddVersion ?>">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHAddV1PvnCode')?></label>
								<div class="input-group">
									<input class="form-control xCNHide" id="oetAddV1PvnCode" name="oetAddV1PvnCode" maxlength="5" onchange="JSxResetVal('oetAddV1PvnCode','<?php echo @$aCnfAddPanal[0]->FTAddV1PvnCode;?>',3)" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1PvnCode; } ?>">
									<input class="form-control xWPointerEventNone" type="text" id="oetAddV1PvnName" name="oetAddV1PvnName" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTPvnName; } ?>" readonly>
									<span class="input-group-btn">
										<button id="obtBchBrowseProvince" type="button" class="btn xCNBtnBrowseAddOn">
											<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/find-24.png'?>">
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHAddV1DstCode')?></label>
								<div class="input-group">
									<input class="form-control xCNHide" id="oetAddV1DstCode" name="oetAddV1DstCode" maxlength="5" onchange="JSxResetVal('oetAddV1DstCode','<?php echo @$aCnfAddPanal[0]->FTAddV1DstCode;?>',4)" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1DstCode; } ?>">
									<input class="form-control xWPointerEventNone" type="text" id="oetAddV1DstName" name="oetAddV1DstName" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTDstName; } ?>" readonly>
									<span class="input-group-btn">
										<button id="obtBchBrowseDistrict" type="button" class="btn xCNBtnBrowseAddOn">
											<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/find-24.png'?>">
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV1No") ?></label>
								<input class="form-control" type="text" id="oetAddV1No" name="oetAddV1No" maxlength="30" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1No; } ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV1Village")?></label>
								<input class="form-control" type="text" id="oetAddV1Village" name="oetAddV1Village" maxlength="70" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1Village; } ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo  language('company/branch/branch','tBCHAddV1SubDist')?></label>
								<div class="input-group">
									<input class="form-control xCNHide" id="oetAddV1SubDistCode" name="oetAddV1SubDistCode" maxlength="5" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1SubDist; } ?>">
									<input class="form-control xWPointerEventNone" type="text" id="oetAddV1SubDistName" name="oetAddV1SubDistName" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTSudName; } ?>" readonly>
									<span class="input-group-btn">
										<button id="obtBchBrowseSubDistrict" type="button" class="btn xCNBtnBrowseAddOn">
											<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/find-24.png'?>">
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV1Road")?></label>
								<input class="form-control" type="text" id="oetAddV1Road" name="oetAddV1Road" maxlength="30" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1Road; } ?>">
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV1Soi") ?></label>
								<input class="form-control" type="text" id="oetAddV1Soi" name="oetAddV1Soi" maxlength="30" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1Soi; } ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV1PostCode")?></label>
								<input class="form-control" type="text" id="oetAddV1PostCode" name="oetAddV1PostCode" maxlength="5" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV1PostCode; } ?>">
							</div>
						</div>
					</div>
					</div>
					<div class="col-md-12 <?php if(@$nCnfAddVersion == '1'){ echo 'xCNHide'; } ?>">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV2Desc1")?></label>
									<input class="form-control" type="text" id="oetAddV2Desc1" name="oetAddV2Desc1" maxlength="255" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV2Desc1; } ?>">
								</div>
							</div>
						<div class="col-md-6">
					<div class="form-group">
						<label class="xCNLabelFrm"><?php echo language("company/branch/branch","tBCHAddV2Desc2")?></label>
							<input class="form-control" type="text" id="oetAddV2Desc2" name="oetAddV2Desc2" maxlength="255" value="<?php if(@$nCnfAddVersion == '1'){ echo @$aCnfAddPanal[0]->FTAddV2Desc2; } ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
				</div>
					</div>
                </div>
            </div>
        <!--END Body Tab 2 -->
        
        <!-- Body Tab 3 -->
        <div class="tab-pane" style="margin-top:10px;" id="oBranchTab3" role="tabpanel" aria-expanded="true" >
            <div class="row" style="margin-right: -30px; margin-left: -30px;">
                    <div id="odvCallEditLocation"></div>
                </div>
            </div>
        <!--END Body Tab 3 -->

        </div>
    </form>
</div>

<?php include 'script/jBranchAdd.php'; ?>
