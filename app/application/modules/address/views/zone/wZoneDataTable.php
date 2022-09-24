<?php 
    if($aDataList['rtCode'] == '1'){
        $nCurrentPage = $aDataList['rnCurrentPage'];
    }else{
        $nCurrentPage = '1';
    }
?>

<div class="row">
	<div class="col-md-12">
    <input type="hidden" id="nCurrentPageTB" value="<?=$nCurrentPage;?>">
		<div class="table-responsive">
			<table class="table table-striped" style="width:100%">
				<thead>
					<tr>
                        <?php if($aAlwEventZone['tAutStaFull'] == 1 || $aAlwEventZone['tAutStaDelete'] == 1) : ?>
						<th nowrap class="xCNTextBold" style="width:5%;text-align:center;"><?php echo language('address/zone/zone','tZNEChoose')?></th>
						<?php endif; ?>
                        <th nowrap class="xCNTextBold" style="width:10%;text-align:center;"><?php echo language('address/zone/zone','tZNECode')?></th>
						<th nowrap class="xCNTextBold" style="width:20%;text-align:center;"><?php echo language('address/zone/zone','tZNEName')?></th>
						<th nowrap class="xCNTextBold" style="width:20%;text-align:center;"><?php echo language('address/zone/zone','tZNEChainName')?></th>
                        <?php if($aAlwEventZone['tAutStaFull'] == 1 || $aAlwEventZone['tAutStaDelete'] == 1) : ?>
                        <th nowrap class="xCNTextBold" style="width:10%;text-align:center;"><?php echo language('common/main/main','tCMNActionDelete')?></th>
						<?php endif; ?>
                        <?php if($aAlwEventZone['tAutStaFull'] == 1 || ($aAlwEventZone['tAutStaEdit'] == 1 || $aAlwEventZone['tAutStaRead'] == 1))  : ?>
                        <th nowrap class="xCNTextBold" style="width:10%;text-align:center;"><?php echo language('common/main/main','tCMNActionEdit')?></th>
                        <?php endif; ?>
                    </tr>
				</thead>
                <tbody id="odvRGPList">
                <?php if($aDataList['rtCode'] == 1 ):?>
                    <?php if(!empty($aDataList['raItems'])) { ?>
                    <?php foreach($aDataList['raItems'] AS $key=>$aValue){ ?>
						<tr class="xCNTextDetail2 otrZone" id="otrZone<?php echo $key?>" data-code="<?php echo $aValue['rtZneCode']?>" data-name="<?php echo $aValue['rtZneName']?>">
                        <?php if($aAlwEventZone['tAutStaFull'] == 1 || $aAlwEventZone['tAutStaDelete'] == 1) : ?>
                        <td nowrap class="text-center">
                            <label class="fancy-checkbox">
                                <input id="ocbListItem<?php echo $key?>" class="ocbListItem" name="ocbListItem[]" type="checkbox">
                                <span>&nbsp;</span>
                            </label>
                        </td>
                        <?php endif; ?>
                        <td nowrap class="text-left"><?php echo $aValue['rtZneChain']?></td>
                        <td nowrap class="text-left"><?php echo $aValue['rtZneName']?></td>
                        <td nowrap class="text-left"><?php echo $aValue['rtZneChainName']?></td>
                        <?php if($aAlwEventZone['tAutStaFull'] == 1 || $aAlwEventZone['tAutStaDelete'] == 1) : ?>
                        <td nowrap style="text-align: center;">
                            <img class="xCNIconTable xCNIconDel" src="<?php echo  base_url().'/application/modules/common/assets/images/icons/delete.png'?>" onClick="JSnZoneDel('<?php echo $nCurrentPage?>','<?php echo $aValue['rtZneName']?>','<?php echo $aValue['rtZneCode']?>','<?php echo language('common/main/main','tBCHYesOnNo')?>')">
                        </td>
                        <?php endif; ?>
                        <?php if($aAlwEventZone['tAutStaFull'] == 1 || ($aAlwEventZone['tAutStaEdit'] == 1 || $aAlwEventZone['tAutStaRead'] == 1)) : ?>
                        <td nowrap style="text-align: center;"><img class="xCNIconTable" src="<?php echo  base_url().'/application/modules/common/assets/images/icons/edit.png'?>" onClick="JSvCallPageZoneEdit('<?php echo $aValue['rtZneCode']?>')"></td>
                        <?php endif; ?>
                        </tr>
                    <?php } ?>
                    <?php } ?>
                <?php else:?>
                    <tr><td nowrap class='text-center xCNTextDetail2' colspan='6'><?php echo language('common/main/main', 'tCMNNotFoundData')?></td></tr>
                <?php endif;?>
                </tbody>
			</table>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
        <p><?php echo language('common/main/main','tResultTotalRecord')?> <?php echo $aDataList['rnAllRow']?> <?php echo language('common/main/main','tRecord')?> <?php echo language('common/main/main','tCurrentPage')?> <?php echo $aDataList['rnCurrentPage']?> / <?php echo $aDataList['rnAllPage']?></p>
    </div>
    <div class="col-md-6">
		<ul class="xWZNEPaging btn-toolbar pull-right">
			<?php if($nPage == 1){ $tDisabledLeft = 'disabled'; }else{ $tDisabledLeft = '-';} ?>
			<button onclick="JSvClickPage('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
            </button>
			<?php for($i=max($nPage-2, 1); $i<=max(0, min($aDataList['rnAllPage'],$nPage+2)); $i++){?>
				<?php 
                    if($nPage == $i){ 
                        $tActive = 'active'; 
                        $tDisPageNumber = 'disabled';
                    }else{ 
                        $tActive = '';
                        $tDisPageNumber = '';
                    }
                ?>
				<button onclick="JSvClickPage('<?php echo $i?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i?></button>
			<?php } ?>
			<?php if($nPage >= $aDataList['rnAllPage']){  $tDisabledRight = 'disabled'; }else{  $tDisabledRight = '-';  } ?>
            <button onclick="JSvClickPage('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>> <!-- เปลี่ยนชื่อ Onclick เป็นของเรื่องนั้นๆ --> 
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
		</ul>
    </div>
</div>

<div class="modal fade" id="odlmodaldelete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?php echo language('common/main/main', 'tModalDelete')?></label>
			</div>
			<div class="modal-body">
				<span id="ospConfirmDelete" class="xCNTextModal" style="display: inline-block; word-break:break-all"></span>
				<input type='hidden' id="ohdConfirmIDDelete">
			</div>
			<div class="modal-footer">
				<button id="osmConfirm" type="button" class="btn xCNBTNPrimery" onClick="JSnZoneDelChoose('<?php echo $nCurrentPage?>')">
        			<?php echo language('common/main/main', 'tModalConfirm')?> 
				</button>
        		<button type="button" class="btn xCNBTNDefult" data-dismiss="modal">
        			<?php echo language('common/main/main', 'tModalCancel')?> 
				</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){

	$('.ocbListItem').click(function(){
        var nCode = $(this).parent().parent().parent().data('code');  //code
        var tName = $(this).parent().parent().parent().data('name');  //code
        $(this).prop('checked', true);
        var LocalItemData = localStorage.getItem("LocalItemData");
        var obj = [];
        if(LocalItemData){
            obj = JSON.parse(LocalItemData);
        }else{ }
        var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
        if(aArrayConvert == '' || aArrayConvert == null){
            obj.push({"nCode": nCode, "tName": tName });
            localStorage.setItem("LocalItemData",JSON.stringify(obj));
            JSxPaseCodeDelInModal();
        }else{
            var aReturnRepeat = findObjectByKey(aArrayConvert[0],'nCode',nCode);
            if(aReturnRepeat == 'None' ){           //ยังไม่ถูกเลือก
                obj.push({"nCode": nCode, "tName": tName });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxPaseCodeDelInModal();
            }else if(aReturnRepeat == 'Dupilcate'){	//เคยเลือกไว้แล้ว
                localStorage.removeItem("LocalItemData");
                $(this).prop('checked', false);
                var nLength = aArrayConvert[0].length;
                for($i=0; $i<nLength; $i++){
                    if(aArrayConvert[0][$i].nCode == nCode){
                        delete aArrayConvert[0][$i];
                    }
                }
                var aNewarraydata = [];
                for($i=0; $i<nLength; $i++){
                    if(aArrayConvert[0][$i] != undefined){
                        aNewarraydata.push(aArrayConvert[0][$i]);
                    }
                }
                localStorage.setItem("LocalItemData",JSON.stringify(aNewarraydata));
                JSxPaseCodeDelInModal();
            }
        }
        JSxShowButtonChoose();
	});
	
});
</script>