<?php 
    if($aDataList['rtCode'] == '1'){
        $nCurrentPage = $aDataList['rnCurrentPage'];
    }else{
        $nCurrentPage = '1';
    }
?>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped" style="width:100%">
                <thead>
					<tr class="xCNCenter">
					<?php if($aAlwEventBankdeptype['tAutStaFull'] == 1 || $aAlwEventBankdeptype['tAutStaDelete'] == 1) : ?>
                        <th class="xCNTextBold" style="width:5%;"><?= language('ticket/agency/agency','tSelect')?></th>
						<?php endif; ?>
						
						<th class="xCNTextBold"><?= language('bankdeptype/bankdeptype/bankdeptype','tBdtCode')?></th>
                        <th class="xCNTextBold"><?= language('bankdeptype/bankdeptype/bankdeptype','tBdtName')?></th>
						<?php if($aAlwEventBankdeptype['tAutStaFull'] == 1 || $aAlwEventBankdeptype['tAutStaDelete'] == 1) : ?>
						<th class="xCNTextBold" style="width:10%;"><?= language('common/main/main','tCMNActionDelete')?></th>
						<?php endif; ?>
						<?php if($aAlwEventBankdeptype['tAutStaFull'] == 1 || $aAlwEventBankdeptype['tAutStaRead'] == 1) : ?>
						<th class="xCNTextBold" style="width:10%;"><?= language('common/main/main','tCMNActionEdit')?></th>
						<?php endif; ?>
                    </tr>
                </thead>
                <tbody id="odvRGPList">
				<?php if($aDataList['rtCode'] == 1 ):?>
                    <?php foreach($aDataList['raItems'] AS $key=>$aValue){ ?>
                        <tr class="text-center xCNTextDetail2 otrCoupon" id="otrCoupon<?=$key?>" data-code="<?=$aValue['FTBdtCode']?>" data-name="<?=$aValue['FTBdtName']?>">
							<?php if($aAlwEventBankdeptype['tAutStaFull'] == 1 || $aAlwEventBankdeptype['tAutStaDelete'] == 1) : ?>
								<td class="text-center">
									<label class="fancy-checkbox">
										<input id="ocbListItem<?=$key?>" type="checkbox" class="ocbListItem" name="ocbListItem[]">
										<span>&nbsp;</span>
									</label>
								</td>
							<?php endif; ?>
                                
                            <td class="text-left"><?=$aValue['FTBdtCode']?></td>
                            <td class="text-left"><?=$aValue['FTBdtName']?></td>
                                
							<?php if($aAlwEventBankdeptype['tAutStaFull'] == 1 || $aAlwEventBankdeptype['tAutStaDelete'] == 1) : ?>
								<td><img class="xCNIconTable xCNIconDel" src="<?= base_url().'/application/modules/common/assets/images/icons/delete.png'?>" onclick="JSnBdtdelete('<?php echo $nCurrentPage?>','<?=$aValue['FTBdtName']?>','<?php echo $aValue['FTBdtCode']?>','<?= language('common/main/main','tModalConfirmDeleteItemsYN')?>')"></td>
							<?php endif; ?>
							<?php if($aAlwEventBankdeptype['tAutStaFull'] == 1 || $aAlwEventBankdeptype['tAutStaRead'] == 1) : ?>
								<td><img class="xCNIconTable" src="<?= base_url().'/application/modules/common/assets/images/icons/edit.png'?>" onclick="JSvCallEditBdt('<?php echo $aValue['FTBdtCode']?>')"></td>
							<?php endif; ?>
                        </tr>
                    <?php } ?>
                <?php else:?>
                    <tr><td class='text-center xCNTextDetail2' colspan='8'><?= language('common/main/main','tCMNNotFoundData')?></td></tr>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row"> 
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<p><?= language('common/main/main','tResultTotalRecord')?> <?=$aDataList['rnAllRow']?> <?= language('common/main/main','tRecord')?> <?= language('common/main/main','tCurrentPage')?> <?=$aDataList['rnCurrentPage']?> / <?=$aDataList['rnAllPage']?></p>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <div class="xWCDCPaging btn-toolbar pull-right">
			<?php if($nPage == 1){ $tDisabled = 'disabled'; }else{ $tDisabled = '-';} ?>
            <button onclick="JSvCPNClickPage('previous')" class="btn btn-white btn-sm" <?=$tDisabled?>><i class="fa fa-chevron-left f-s-14 t-plus-1"></i></button>

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
            		<button onclick="JSvCPNClickPage('<?=$i?>')" type="button" class="btn xCNBTNNumPagenation <?=$tActive?>" <?=$tDisPageNumber ?>><?=$i?></button>
			<?php } ?>

            <?php if($nPage >= $aDataList['rnAllPage']){ $tDisabled = 'disabled'; }else{ $tDisabled = '-'; } ?>
			<button onclick="JSvCPNClickPage('next')" class="btn btn-white btn-sm" <?=$tDisabled?>><i class="fa fa-chevron-right f-s-14 t-plus-1"></i></button>
        </div>
    </div>
</div>

<div class="modal fade" id="odvModalDelBank">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header xCNModalHead">
				<label class="xCNTextModalHeard"><?=language('common/main/main', 'tModalDelete')?></label>
			</div>
			<div class="modal-body">
				<span id="ospConfirmDelete"> - </span>
				<input type='hidden' id="ohdConfirmIDDelete">
			</div>
			<div class="modal-footer">
				<button id="osmConfirm" type="button" class="btn xCNBTNPrimery" onClick="JSnBankDelChoose('<?=$nCurrentPage?>')"><?=language('common/main/main', 'tModalConfirm')?></button>
        		<button type="button" class="btn xCNBTNDefult" data-dismiss="modal"><?=language('common/main/main', 'tModalCancel')?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$('ducument').ready(function(){
    JSxShowButtonChoose();
	var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
	var nlength = $('#odvRGPList').children('tr').length;
	for($i=0; $i < nlength; $i++){
		var tDataCode = $('#otrBank'+$i).data('code')
		if(aArrayConvert == null || aArrayConvert == ''){
		}else{
			var aReturnRepeat = findObjectByKey(aArrayConvert[0],'nCode',tDataCode);
			if(aReturnRepeat == 'Dupilcate'){
				$('#ocbListItem'+$i).prop('checked', true);
			}else{ }
		}
	}

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
    })
});
</script>
