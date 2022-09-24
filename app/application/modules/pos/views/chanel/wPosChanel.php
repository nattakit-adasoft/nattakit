<input id="oetChnStaBrowse" type="hidden" value="<?php echo $nChnBrowseType; ?>">
<input id="oetChnCallBackOption" type="hidden" value="<?php echo $tChnBrowseOption; ?>">


<?php if(isset($nChnBrowseType) && $nChnBrowseType == 0) : ?>
	<div id="odvChnMainMenu" class="main-menu">
		<div class="xCNMrgNavMenu">
			<div class="row xCNavRow" style="width:inherit;">
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
					<ol id="oliMenuNav" class="breadcrumb">
						<?php FCNxHADDfavorite('chanel/0/0');?> 
						<!-- <li id="oliChnTitle" class="xCNLinkClick" onclick="JSvCallPageChanel('')"><?php echo language('pos/slipmessage/slipmessage','tSMGTitle'); ?></li>
						<li id="oliChnTitleAdd" class="active"><a><?php echo language('pos/slipmessage/slipmessage','tSMGTitleAdd'); ?></a></li>
                        <li id="oliChnTitleEdit" class="active"><a><?php echo language('pos/slipmessage/slipmessage','tSMGTitleEdit'); ?></a></li> -->
                        <li id="oliChnTitle" class="xCNLinkClick" onclick="JSvCallPageChanel('')">ช่องทางการขาย</li>
						<li id="oliChnTitleAdd" class="active"><a>เพิ่มช่องทางการขาย</a></li>
						<li id="oliChnTitleEdit" class="active"><a>แก้ไขช่องทางการขาย</a></li>
					</ol>
				</div>
				<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 text-right p-r-0">
					<div id="odvBtnChnInfo">
						<button id="obtChnAdd" class="xCNBTNPrimeryPlus" type="button" onclick="JSvCallPageChanelAdd()" title="<?php echo language('common/main/main', 'tAdd'); ?>">+</button>
					</div>
					<div id="odvBtnAddEdit">
						<button onclick="JSvCallPageChanel()" class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"> <?php echo language('common/main/main', 'tBack'); ?></button>
						<div class="btn-group">
							<button type="submit" class="btn xWBtnGrpSaveLeft" onclick="$('#obtSubmitChanel').click()"> <?php echo language('common/main/main', 'tSave'); ?></button>
							<?php echo $vBtnSave; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
	<div class="xCNMenuCump xCNPtyBrowseLine" id="odvMenuCump">
		&nbsp;
	</div>
	<div class="main-content">
		<div id="odvContentPageChanel"></div>
	</div>
	<input type="hidden" name="ohdDeleteChooseconfirm" id="ohdDeleteChooseconfirm" value="<?php echo language('common/main/main', 'tModalConfirmDeleteItemsAll') ?>">
	<?php else: ?>
	<div class="modal-header xCNModalHead">
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<a onclick="JCNxBrowseData('<?php echo $tChnBrowseOption; ?>')" class="xWBtnPrevious xCNIconBack" style="float:left;font-size:19px;">
					<i class="fa fa-arrow-left xCNIcon"></i>	
				</a>
				<ol id="oliChnNavBrowse" class="breadcrumb xCNMenuModalBrowse">
					<li class="xWBtnPrevious" onclick="JCNxBrowseData('<?php echo $tChnBrowseOption; ?>')"><a>แสดงข้อมูล : <?php echo language('pos/slipmessage/slipmessage','tSMGTitle'); ?></a></li>
					<li class="active"><a><?php echo language('pos/slipmessage/slipmessage','tSMGTitleAdd'); ?></a></li>
				</ol>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
				<div id="odvChnBtnGroup" class="demo-button xCNBtngroup" style="width:100%;">
					<button type="button" class="btn xCNBTNPrimery" onclick="$('#obtSubmitChanel').click()"><?php echo language('common/main/main', 'tSave'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<div id="odvContentPageChanel" class="modal-body xCNModalBodyAdd">
	</div>
<?php endif;?>
<script src="<?= base_url('application/modules/common/assets/js/jquery-ui-sortable.min.js')?>"></script>
<script src="<?= base_url('application/modules/pos/assets/src/chanel/jPosChanel.js')?>"></script>