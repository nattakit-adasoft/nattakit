<div class="panel panel-headline">
    <div class="panel-heading">
    <div class="row">
        <div class="col-xs-12 col-md-9 col-lg-9">
            <div class="row">
                <div class="col-xs-12 col-md-2 col-lg-2">
                    <div class="col-lg-12 col-md-12 col-xs-12 no-padding">
                        <label class="xCNLabelFrm"><?php echo language('document/promotion/promotion', 'tLabel17'); ?></label>
                    </div>
                    <div class="form-group">
                        <select class="selectpicker form-control" id="ocmUsedStatus" name="ocmUsedStatus" onchange="JSvRDHCallPageDataTable()">
                            <option value='0'><?php echo language('common/main/main', 'tAll'); ?></option>
                            <option value='1'><?php echo language('document/promotion/promotion', 'tPausedTemporarily'); ?></option>
                            <option value='2'><?php echo language('document/promotion/promotion', 'tActive'); ?></option>
                            <option value='3'><?php echo language('document/promotion/promotion', 'tLabel12'); ?></option>
                            <option value='4'><?php echo language('document/promotion/promotion', 'tPmhDateExp'); ?></option>
                            <option value='5'><?php echo language('document/promotion/promotion', 'tStaDoc3'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4 col-lg-4">
                    <label class="xCNLabelFrm"><?php echo language('common/main/main', 'tSearch'); ?></label>
                    <div class="form-group">
                        <div class="input-group">
                            <input
                                class="form-control xCNInpuTXOthoutSingleQuote"
                                type="text"
                                id="oetRDHSearchAllDocument"
                                name="oetRDHSearchAllDocument"
                                placeholder="<?php echo language('document/conditionredeem/conditionredeem','tRDHFillTextSearch')?>"
                                autocomplete="off"
                            >
                            <span class="input-group-btn">
                                <button id="obtRDHSerchAllDocument" type="button" class="btn xCNBtnDateTime"><img class="xCNIconSearch"></button>
                            </span>
                        </div>    
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <div style="margin-top:25px;">
                        <button id="obtRDHAdvanceSearch" class="btn xCNBTNDefult xCNBTNDefult1Btn"><?php echo language('common/main/main', 'tAdvanceSearch'); ?></button>
                        <button id="obtRDHSearchReset" class="btn xCNBTNDefult xCNBTNDefult1Btn"><?php echo language('common/main/main', 'tClearSearch'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="odvRDHAdvanceSearchContainer" class="hidden" style="margin-bottom:20px;">
        <form id="ofmRDHFromSerchAdv" class="validate-form" action="javascript:void(0)" method="post">
            <div class="row">
                <!-- From Search Advanced  Branch -->
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchBranch'); ?></label>
                        <div class="input-group">
                            <input class="form-control xCNHide" type="text" id="oetRDHAdvSearchBchCodeFrom" name="oetRDHAdvSearchBchCodeFrom" maxlength="5">
                            <input
                                class="form-control xWPointerEventNone"
                                type="text"
                                id="oetRDHAdvSearchBchNameFrom"
                                name="oetRDHAdvSearchBchNameFrom"
                                placeholder="<?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchFrom'); ?>"
                                readonly
                            >
                            <span class="input-group-btn">
                                <button id="obtRDHAdvSearchBrowseBchFrom" type="button" class="btn xCNBtnBrowseAddOn" ><img class="xCNIconFind"></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="xCNLabelFrm"></label>
                        <div class="input-group">
                            <input class="form-control xCNHide" id="oetRDHAdvSearchBchCodeTo"name="oetRDHAdvSearchBchCodeTo" maxlength="5">
                            <input
                                class="form-control xWPointerEventNone"
                                type="text"
                                id="oetRDHAdvSearchBchNameTo"
                                name="oetRDHAdvSearchBchNameTo"
                                placeholder="<?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchTo'); ?>"
                                readonly
                            >
                            <span class="input-group-btn">
                                <button id="obtRDHAdvSearchBrowseBchTo" type="button" class="btn xCNBtnBrowseAddOn" ><img class="xCNIconFind"></button>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- From Search Advanced  DocDate -->
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchDocDate'); ?></label>
                        <div class="input-group">
                            <input
                                class="form-control xCNDatePicker"
                                type="text"
                                id="oetRDHAdvSearcDocDateFrom"
                                name="oetRDHAdvSearcDocDateFrom"
                                placeholder="<?php echo language('document/conditionredeem/conditionredeem', 'tRDHAdvSearchDateFrom'); ?>"
                            >
                            <span class="input-group-btn" >
                                <button id="obtRDHAdvSearchDocDateForm" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <label class="xCNLabelFrm"></label>
                    <div class="input-group">
                        <input
                            class="form-control xCNDatePicker"
                            type="text"
                            id="oetRDHAdvSearcDocDateTo"
                            name="oetRDHAdvSearcDocDateTo"
                            placeholder="<?php echo language('document/conditionredeem/conditionredeem', 'tRDHAdvSearchDateTo'); ?>"
                        >
                        <span class="input-group-btn" >
                            <button id="obtRDHAdvSearchDocDateTo" type="button" class="btn xCNBtnDateTime"><img class="xCNIconCalendar"></button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- From Search Advanced Status Doc -->
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 hidden">
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchStaDoc'); ?></label>
                        <select class="selectpicker form-control" id="ocmRDHAdvSearchStaDoc" name="ocmRDHAdvSearchStaDoc">
                            <option value='0'><?php echo language('common/main/main','tStaDocAll'); ?></option>
                            <option value='1'><?php echo language('common/main/main','tStaDocComplete'); ?></option>
                            <option value='2'><?php echo language('common/main/main','tStaDocinComplete'); ?></option>
                            <option value='3'><?php echo language('common/main/main','tStaDocCancel'); ?></option>
                        </select>
                    </div>
                </div>
                <!-- From Search Advanced Status Approve -->
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchStaApprove'); ?></label>
                        <select class="selectpicker form-control" id="ocmRDHAdvSearchStaApprove" name="ocmRDHAdvSearchStaApprove">
                            <option value='0'><?php echo language('common/main/main','tAll'); ?></option>
                            <option value='1'><?php echo language('common/main/main','tStaDocApv'); ?></option>
                            <option value='2'><?php echo language('common/main/main','tStaDocPendingApv'); ?></option>
                        </select>
                    </div>    
                </div>
                <!-- From Search Advanced Status Process Stock -->
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label class="xCNLabelFrm"><?php echo language('document/conditionredeem/conditionredeem','tRDHAdvSearchStaPrcStk'); ?></label>
                        <select class="selectpicker form-control" id="ocmRDHAdvSearchStaPrcStk" name="ocmRDHAdvSearchStaPrcStk">
                            <option value='0'><?php echo language('common/main/main','tAll'); ?></option>
                            <option value='1'><?php echo language('common/main/main','tStaDocProcessor'); ?></option>
                            <option value='2'><?php echo language('common/main/main','tStaDocProcessing'); ?></option>
                            <option value='3'><?php echo language('common/main/main','tStaDocPendingProcessing'); ?></option>
                        </select>
                    </div>
                </div>
                <!-- Button Form Search Advanced -->
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group" style="width:60%;float:right;">
                        <label class="xCNLabelFrm">&nbsp;</label>
                        <button id="obtRDHAdvSearchSubmitForm" class="btn xCNBTNPrimery" style="width:100%"><?php echo language('common/main/main', 'tSearch'); ?></button>
                    </div>
                </div>
            </div>    
        </form>
    </div>
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8 col-sm-4 col-md-4 col-lg-4">
            </div>
            <div class="col-xs-4 col-md-8 col-lg-8 text-right" style="margin-top:-35px;">
                <div id="odvRDHMngTableList" class="btn-group xCNDropDrownGroup">
                    <button type="button" class="btn xCNBTNMngTable" data-toggle="dropdown">
                        <?php echo language('common/main/main','tCMNOption')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
						<li id="oliRDHBtnDeleteAll" class="disabled">
							<a data-toggle="modal" data-target="#odvRDHModalDelDocMultiple"><?php echo language('common/main/main','tCMNDeleteAll')?></a>
						</li>
					</ul>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <section id="ostRDHDataTableDocument"></section>
    </div>
</div>
<script src="<?php echo  base_url('application/modules/common/assets/js/jquery.mask.js')?>"></script>
<script src="<?php echo  base_url('application/modules/common/assets/src/jFormValidate.js')?>"></script>
<?php include('script/jConditionRedeemFormSearchList.php')?>