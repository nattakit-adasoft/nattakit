<div class="panel panel-headline"> <!-- เพิ่ม -->
	<div class="panel-heading">
		<section id="ostSearchPromotion">
				<div class="row">
					<div class="col-xs-3 col-md-3">
						<div class="form-group">
								<div class="input-group">
								<input class="form-control xCNInputWithoutSingleQuote" type="text" id="oetSearchAll" name="oetSearchAll" placeholder="<?= language('document/producttransferbranch/producttransferbranch','tTBFillTextSearch')?>" onkeyup="Javascript:if(event.keyCode==13) JSvCallPageTBPdtDataTable()" autocomplete="off">
									<span class="input-group-btn">
										<button type="button" class="btn xCNBtnDateTime" onclick="JSvCallPageTBPdtDataTable()">
											<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/search-24.png'?>">
										</button>
									</span>
								</div>
						</div>
					</div>
					<a id="oahTBAdvanceSearch" class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;"><?php echo language('common/main/main', 'tAdvanceSearch'); ?></a>
					<a id="oahTBSearchReset" class="btn xCNBTNDefult xCNBTNDefult1Btn" href="javascript:;" onclick="JSxTBClearSearchData()"><?php echo language('common/main/main', 'tClearSearch'); ?></a>
				</div>

				<div class="row hidden" id="odvTBAdvanceSearchContainer" style="margin-bottom:20px;">
					<div class="col-xs-12 col-md-6 col-lg-6">
						<div class="col-lg-12 col-md-12 col-xs-12 no-padding">
							<label class="xCNLabelFrm"><?php echo language('document/producttransferbranch/producttransferbranch','tTBBranch'); ?></label>
						</div>

						<?php
							if($this->session->userdata("tSesUsrLevel") != "HQ"){
								$tTBBchCode 		= $this->session->userdata("tSesUsrBchCode");
								$tTBBchName 		= $this->session->userdata("tSesUsrBchName");
								$tStaBlockBrowse 	= "disabled";
							}else{
								$tTBBchCode 		= "";
								$tTBBchName 		= "";
								$tStaBlockBrowse 	= "";
							}
						?>

						<div class="col-lg-6 col-md-6 col-xs-6 no-padding padding-right-15">
							<div class="form-group">
								<div class="input-group">
									<input class="form-control xCNHide" id="oetBchCodeFrom" name="oetBchCodeFrom" maxlength="5" value="<?=$tTBBchCode;?>">
									<input class="form-control xWPointerEventNone" type="text" id="oetBchNameFrom" name="oetBchNameFrom" value="<?=$tTBBchName;?>" placeholder="<?php echo language('document/producttransferbranch/producttransferbranch','tFrom'); ?>" readonly>
									<!-- ถ้า user มีสาขาจะไม่สามารถ Brw ได้ -->
									<span class="input-group-btn" >
										<button id="obtTBBrowseBchFrom" type="button" class="btn xCNBtnBrowseAddOn" <?=$tStaBlockBrowse;?>>
											<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/find-24.png'?>">
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-xs-6 no-padding padding-left-15">
							<div class="form-group">
								<div class="input-group">
									<input class="form-control xCNHide" id="oetBchCodeTo" name="oetBchCodeTo" value="<?=$tTBBchCode;?>" maxlength="5">
									<input class="form-control xWPointerEventNone" type="text" id="oetBchNameTo" name="oetBchNameTo" value="<?=$tTBBchName;?>" placeholder="<?php echo language('document/producttransferbranch/producttransferbranch','tTo'); ?>" readonly>
									<!-- ถ้า user มีสาขาจะไม่สามารถ Brw ได้ -->
									<span class="input-group-btn" >
										<button id="obtTBBrowseBchTo" type="button" class="btn xCNBtnBrowseAddOn" <?=$tStaBlockBrowse;?>>
											<img src="<?php echo  base_url().'application/modules/common/assets/images/icons/find-24.png'?>">
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-6">
						<div class="col-lg-12 col-md-12 col-xs-12 no-padding">
							<label class="xCNLabelFrm"><?php echo language('document/producttransferbranch/producttransferbranch','tTBTBDocDate'); ?></label>
						</div>
						<div class="col-lg-6 col-md-6 col-xs-6 no-padding padding-right-15">
							<div class="form-group">
								<div class="input-group">
								<input  class="form-control input100 xCNDatePicker" type="text" id="oetSearchDocDateFrom" name="oetSearchDocDateFrom" placeholder="<?php echo language('document/producttransferbranch/producttransferbranch', 'tFrom'); ?>">
									<span class="input-group-btn" >
										<button id="obtSearchDocDateFrom" type="button" class="btn xCNBtnDateTime">
											<img src="<?php echo base_url();?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-xs-6 no-padding padding-left-15">
							<div class="form-group">
								<div class="input-group">
								<input  class="form-control input100 xCNDatePicker" type="text" id="oetSearchDocDateTo" name="oetSearchDocDateTo" placeholder="<?php echo language('document/producttransferbranch/producttransferbranch', 'tTo'); ?>">
									<span class="input-group-btn" >
										<button id="obtSearchDocDateTo" type="button" class="btn xCNBtnDateTime">
											<img src="<?php echo base_url();?>application/modules/common/assets/images/icons/icons8-Calendar-100.png">
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-3 col-lg-3">
						<div class="col-lg-12 col-md-12 col-xs-12 no-padding">
							<label class="xCNLabelFrm"><?php echo language('document/producttransferbranch/producttransferbranch','tTBTBStaDoc'); ?></label>
						</div>
						<div class="form-group">
							<select class="selectpicker form-control" id="ocmStaDoc" name="ocmStaDoc">
								<option value='0'><?php echo language('common/main/main','tStaDocAll'); ?></option>
								<option value='1'><?php echo language('common/main/main','tStaDocComplete'); ?></option>
								<option value='2'><?php echo language('common/main/main','tStaDocinComplete'); ?></option>
								<option value='3'><?php echo language('common/main/main','tStaDocCancel'); ?></option>
							</select>
						</div>
					</div>
					<div class="col-xs-12 col-md-3 col-lg-3">
						<div class="col-lg-12 col-md-12 col-xs-12 no-padding">
							<label class="xCNLabelFrm"><?php echo language('document/producttransferbranch/producttransferbranch','tTBTBStaApv'); ?></label>
						</div>
						<div class="form-group">
							<select class="selectpicker form-control" id="ocmStaApprove" name="ocmStaApprove">
								<option value='0'><?php echo language('common/main/main','tAll'); ?></option>
								<option value='1'><?php echo language('common/main/main','tStaDocApv'); ?></option>
								<option value='2'><?php echo language('common/main/main','tStaDocPendingApv'); ?></option>
							</select>
						</div>
					</div>
					<div class="col-xs-12 col-md-3 col-lg-3">
						<div class="col-lg-12 col-md-12 col-xs-12 no-padding">
							<label class="xCNLabelFrm"><?php echo language('document/producttransferbranch/producttransferbranch','tTBTBStaPrc'); ?></label>
						</div>
						<div class="form-group">
							<select class="selectpicker form-control" id="ocmStaPrcStk" name="ocmStaPrcStk">
								<option value='0'><?php echo language('common/main/main','tAll'); ?></option>
								<option value='1'><?php echo language('common/main/main','tStaDocProcessor'); ?></option>
								<option value='2'><?php echo language('common/main/main','tStaDocProcessing'); ?></option>
								<option value='3'><?php echo language('common/main/main','tStaDocPendingProcessing'); ?></option>
							</select>
						</div>
					</div>
					<div class="col-xs-12 col-md-3 col-lg-3">
						<div class="form-group">
                            <label class="xCNLabelFrm">&nbsp;</label>
                            <button id="obtTBSubmitFrmSearchAdv" class="btn xCNBTNPrimery" style="width:100%"><?php echo language('common/main/main', 'tSearch'); ?></button>
                        </div>
					</div>
				</div>
		</section>
	</div>
	<div class="panel-heading"> <!-- เพิ่ม -->
		<div class="row">
			<div class="col-xs-8 col-md-4 col-lg-4">
			</div>
			<div class="col-xs-4 col-md-8 col-lg-8 text-right" style="margin-top:-35px;">
				<div id="odvMngTableList" class="btn-group xCNDropDrownGroup">
					<button type="button" class="btn xCNBTNMngTable" data-toggle="dropdown">
						<?php echo language('common/main/main','tCMNOption')?>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li id="oliBtnDeleteAll" class="disabled">
							<a data-toggle="modal" data-target="#odvModalDel"><?php echo language('common/main/main','tDelAll')?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-body">
		<section id="odvTBContentList"></section>
	</div>
</div>

<script src="<?php echo  base_url('application/modules/common/assets/js/jquery.mask.js')?>"></script>
<script src="<?php echo  base_url('application/modules/common/assets/src/jFormValidate.js')?>"></script>
<?php include('script/jProducttransferbranchFormSearchList.php')?>
