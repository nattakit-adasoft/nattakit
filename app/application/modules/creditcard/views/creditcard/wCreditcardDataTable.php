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
						<?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                        	<th class="xCNTextBold" style="width:5%;"><?= language('common/main/main','tCMNChoose')?></th>
						<?php endif; ?>
                        <th style="width:10%;"><?= language('creditcard/creditcard/creditcard','tCDCTBIMG')?></th>
                        <th><?= language('creditcard/creditcard/creditcard','tCDCTBCode')?></th>
                        <th><?= language('creditcard/creditcard/creditcard','tCDCTBName')?></th>
                        <th><?= language('creditcard/creditcard/creditcard','tCDCTBBank')?></th>
                        <?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
							<th class="xCNTextBold" style="width:10%;"><?= language('common/main/main','tCMNActionDelete')?></th>
						<?php endif; ?>
                        <?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1) : ?>
						<th class="xCNTextBold" style="width:10%;"><?= language('common/main/main','tCMNActionEdit')?></th>
						<?php endif; ?>
                    </tr>
                </thead>
                <tbody id="odvRGPList">
				<?php if($aDataList['rtCode'] == 1 ):?>
                    <?php foreach($aDataList['raItems'] AS $key=>$aValue){ ?>
						<tr class="text-center xCNTextDetail2 otrCreditcard" id="otrCreditcard<?=$key?>" data-code="<?=$aValue['FTCrdCode']?>" data-name="<?=$aValue['FTCrdName']?>">
							<?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
								<td class="text-center">
									<label class="fancy-checkbox">
										<input id="ocbListItem<?=$key?>" type="checkbox" class="ocbListItem" name="ocbListItem[]">
										<span>&nbsp;</span>
									</label>
								</td>
							<?php endif; ?>
                            <?php
                                $tImgObjPath = $aValue['FTImgObj'];
                                if(isset($tImgObjPath) && !empty($tImgObjPath)){
                                    $tImgObj        = $aValue['FTImgObj'];
                                    $aImgObj        = explode("application/modules/",$tImgObj);
                                    $aImgObjName    = explode("/",$tImgObj);
                                    $tImgName		= end($aImgObjName);
                                    $aImgObj        = explode("application",$tImgObjPath);
                                    $tFullPatch     = './application'.$aImgObj[1];

                                    if (file_exists($tFullPatch)){
                                        $tPatchImg = base_url().'/application'.$aImgObj[1];
                                    }else{
                                        $tPatchImg = base_url().'application/modules/common/assets/images/200x200.png';
                                    }
                                }else{
                                    $tPatchImg = base_url().'application/modules/common/assets/images/200x200.png';
                                }
                                if(isset($tImgName) && !empty($tImgName)){
                                    switch ($tImgName) {
                                        case 'blue406306.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                            break;    
                                        case 'blue406256.png';
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                            break;
                                        case 'Brown4097583.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                            break;
                                        case 'green407354.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                            break;
                                        case 'orange402021.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                            break;
                                        case 'purple40506.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                            break;
                                        case 'red40303.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                        break;
                                        case 'black40811.png':
                                                $tStyleImg = "border-radius: 50%; width: 20%";
                                        break;
                                        default:
                                                $tStyleImg = "";
                                    }
                                }
                            ?>
                            <td class="text-center"><img src="<?php echo $tPatchImg?>" style='width:30px;<?php echo $tStyleImg; ?>'></td>
                            <td><?=$aValue['FTCrdCode']?></td>
                            <td class="text-left"><?=$aValue['FTCrdName']?></td>
                            <td class="text-left"><?=$aValue['FTBnkName']?></td>
							<?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
								<td><img class="xCNIconTable xCNIconDel" src="<?= base_url().'/application/modules/common/assets/images/icons/delete.png'?>" onClick="JSnCreditcardDel('<?=$nCurrentPage?>','<?=$aValue['FTCrdName']?>','<?=$aValue['FTCrdCode']?>')"></td>
							<?php endif; ?>
							<?php if($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1) : ?>
								<td><img class="xCNIconTable" src="<?= base_url().'/application/modules/common/assets/images/icons/edit.png'?>" onClick="JSvCallPageCreditcardEdit('<?=$aValue['FTCrdCode']?>')"></td>
							<?php endif; ?>
                        </tr>
                    <?php } ?>
                <?php else:?>
                    <tr><td class='text-center xCNTextDetail2' colspan='7'><?= language('common/main/main','tCMNNotFoundData')?></td></tr>
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
            <button onclick="JSvCDCClickPage('previous')" class="btn btn-white btn-sm" <?=$tDisabled?>><i class="fa fa-chevron-left f-s-14 t-plus-1"></i></button>

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
            		<button onclick="JSvCDCClickPage('<?=$i?>')" type="button" class="btn xCNBTNNumPagenation <?=$tActive?>" <?=$tDisPageNumber ?>><?=$i?></button>
			<?php } ?>

            <?php if($nPage >= $aDataList['rnAllPage']){ $tDisabled = 'disabled'; }else{ $tDisabled = '-'; } ?>
			<button onclick="JSvCDCClickPage('next')" class="btn btn-white btn-sm" <?=$tDisabled?>><i class="fa fa-chevron-right f-s-14 t-plus-1"></i></button>
        </div>
    </div>
</div>

<div class="modal fade" id="odvModalDelCreditcard">
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
				<button id="osmConfirm" type="button" class="btn xCNBTNPrimery" onClick="JSnCreditcardDelChoose('<?=$nCurrentPage?>')"><?=language('common/main/main', 'tModalConfirm')?></button>
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
		var tDataCode = $('#otrCreditcard'+$i).data('code')
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