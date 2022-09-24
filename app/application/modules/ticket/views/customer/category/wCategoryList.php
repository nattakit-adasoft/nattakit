<?php if (@$oCstTyList[0]->FNCtyID != ''): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="xCNTextBold" style="width:5%;text-align:center;"><?= language('ticket/customer/customer', 'tSelect') ?></th>
                    <th class="xCNTextBold" style="width:60%;text-align:center;"><?= language('ticket/customer/customer', 'tName') ?></th>		
                    <?php if ($oAuthen['tAutStaDelete'] == '1'): ?>
                    <th class="xCNTextBold" style="width:6%;text-align:center;"><?= language('ticket/customer/customer', 'tDelete') ?></th>
                    <?php endif; ?>
                    <?php if ($oAuthen['tAutStaEdit'] == '1'): ?>
                    <th class="xCNTextBold" style="width:6%;text-align:center;"><?= language('ticket/customer/customer', 'tEdit') ?></th>
                    <?php endif; ?>				
                </tr>
            </thead>
        <tbody>		
    <?php foreach ($oCstTyList as $key => $tValue) : ?>	
        <tr class="xCNTextDetail2 otrDistrict"  data-name="<?= $tValue->FTCtyName ?>" data-code="<?=$tValue->FNCtyID;?>">
            <td class="text-center">
                <label class="fancy-checkbox">
                    <input id="ocbListItem<?= $key ?>" type="checkbox" data-name="<?= $tValue->FTCtyName ?>" value="<?= $tValue->FNCtyID; ?>" class="ocbListItem" name="ocbListItem[]">
                        <span>&nbsp;</span>
                        </label>
                    </td>
                <td>
            <?php if ($tValue->FTCtyName): ?>
        <?= $tValue->FTCtyName ?>
    <?php else: ?>
        <?= language('ticket/zone/zone', 'tNoData') ?>
            <?php endif; ?>
                </td>
                    <?php if ($oAuthen['tAutStaDelete'] == '1'): ?>
                        <td  class="text-center">	
                        <!-- เปลี่ยน -->
                    <img class="xCNIconTable" src="<?php echo  base_url().'application/modules/common/assets/images/icons/delete.png'?>" onclick="FSxCstCategoryDel('<?= $nPageNo ?>','<?= $tValue->FNCtyID ?>', '<?= $tValue->FTCtyName ?>')">						   	
                 </td>
            <?php endif; ?>
        <?php if ($oAuthen['tAutStaEdit'] == '1'): ?>
    <td class="text-center">
        <!-- เปลี่ยน -->
            <img class="xCNIconTable" src="<?php echo  base_url().'application/modules/common/assets/images/icons/edit.png'?>" onclick="JSxCallPage('<?php echo base_url() ?>EticketCustomer/EditCategory/<?= $tValue->FNCtyID ?>');">     
                </td>
                    <?php endif; ?>
                    </tr>
                    <?php endforeach ?>		
                    </tbody>
                </table>
            </div>
    <?php else: ?>
<div style="margin: auto; text-align: center; padding: 50px;">
        <?= language('ticket/user/user', 'tDataNotFound') ?>
            </div>	
                <?php endif ?>
                    <div class="modal fade" id="odvmodaldelete">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header xCNModalHead">
                                <label class="xCNTextModalHeard"><?=language('common/main/main', 'tModalDelete')?></label>
                                </div>
                                <div class="modal-body">
                                <span id="ospConfirmDelete" class="xCNTextModal"> - </span>
                                <input type='hidden' id="ospConfirmIDDelete">
                                </div>
                                <div class="modal-footer">
                                <!-- แก้ -->
                                <button id="osmConfirm" onclick="FSxDelAllOnCheckCategory('<?= $nPageNo ?>')" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button">
                                <?php echo language('common/main/main', 'tModalConfirm')?>
                                </button>
                            <!-- แก้ -->
                        <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button"  data-dismiss="modal">
                    <?php echo language('common/main/main', 'tModalCancel')?>
                </button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
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
            JSxTextinModal();
        }else{
            var aReturnRepeat = findObjectByKey(aArrayConvert[0],'nCode',nCode);
            if(aReturnRepeat == 'None' ){           //ยังไม่ถูกเลือก
                obj.push({"nCode": nCode, "tName": tName });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxTextinModal();
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
                JSxTextinModal();
            }
        }
        JSxShowButtonChoose();
    })
</script>