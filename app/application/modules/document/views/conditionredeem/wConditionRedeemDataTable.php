<?php
if ($aDataList['rtCode'] == '1') {
    $nCurrentPage   = $aDataList['rnCurrentPage'];
} else {
    $nCurrentPage   = '1';
}
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="table-responsive">
            <table id="otbRDHTblDataDocHDList" class="table table-striped">
                <thead>
                    <tr class="xCNCenter">
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                            <th nowrap class="xCNTextBold" style="width:5%;"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBChoose') ?></th>
                        <?php endif; ?>
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBBchCreate') ?></th>
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBDocNo') ?></th>
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBDocDate') ?></th>
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHName') ?></th>
                        <!-- <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBStatusDoc') ?></th> -->
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBStatusApv') ?></th>
                        <!-- <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBStatusActive') ?></th> -->
                        <!-- <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTBStatusExp') ?></th> -->
                        <th class="xCNTextBold"><?= language('document/promotion/promotion', 'tLabel17') ?></th>
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTUserCreate') ?></th>
                        <th nowrap class="xCNTextBold"><?php echo language('document/conditionredeem/conditionredeem', 'tRDHTUserAppove') ?></th>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                            <th nowrap class="xCNTextBold" style="width:5%;"><?php echo language('common/main/main', 'tCMNActionDelete') ?></th>
                        <?php endif; ?>
                        <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1 || $aAlwEvent['tAutStaEdit'] == 1) : ?>
                            <th nowrap class="xCNTextBold" style="width:5%;"><?php echo language('common/main/main', 'tCMNActionEdit') ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($aDataList['rtCode'] == 1) : ?>
                        <?php foreach ($aDataList['raItems'] as $nKey => $aValue) : ?>

                            <?php
                            $tRDHDocNo  = $aValue['FTRdhDocNo'];
                            if (!empty($aValue['FTRdhStaApv']) || $aValue['FTRdhStaDoc'] == 3) {
                                $tCheckboxDisabled = "disabled";
                                $tClassDisabled = 'xCNDocDisabled';
                                $tTitle = language('document/document/document', 'tDOCMsgCanNotDel');
                                $tOnclick = '';
                            } else {
                                $tCheckboxDisabled = "";
                                $tClassDisabled = '';
                                $tTitle = '';
                                $tOnclick = "onclick=JSoRDHDelDocSingle('" . $nCurrentPage . "','" . $tRDHDocNo . "')";
                            }

                            // FTRdhStaDoc
                            if ($aValue['FTRdhStaDoc'] == 1) {
                                $tClassStaDoc   = 'text-success';
                            } else if ($aValue['FTRdhStaDoc'] == 2) {
                                $tClassStaDoc   = 'text-warning';
                            } else if ($aValue['FTRdhStaDoc'] == 3) {
                                $tClassStaDoc   = 'text-danger';
                            }

                            // FTRdhStaApv
                            if ($aValue['FTRdhStaApv'] == 1) {
                                $tClassStaApv = 'text-success';
                            } else if ($aValue['FTRdhStaApv'] == 2 || $aValue['FTRdhStaApv'] == '') {
                                $tClassStaApv = 'text-danger';
                            }

                            // FTRdhStaPrcDoc
                            if ($aValue['FTRdhStaPrcDoc'] == 1) {
                                $tClassPrcStk = 'text-success';
                            } else if ($aValue['FTRdhStaPrcDoc'] == 2) {
                                $tClassPrcStk = 'text-warning';
                            } else if ($aValue['FTRdhStaPrcDoc'] == '') {
                                $tClassPrcStk = 'text-danger';
                            } else if ($aValue['FTRdhStaPrcDoc'] == 0) {
                                $tClassPrcStk = 'text-danger';
                            }

                            /*===== Begin UsedStatus ===================================*/
                            if ($aValue['UsedStatus'] == "1") {
                                $tClassStaUse = 'text-warning';
                                $tUsedStatusShow = language('document/promotion/promotion', 'tPausedTemporarily');
                            }

                            if (in_array($aValue['UsedStatus'], ["2","3"])) {
                                $tClassStaUse = 'text-success';
                                if($aValue['UsedStatus'] == "2"){
                                    $tUsedStatusShow = language('document/promotion/promotion', 'tActive');
                                }else{
                                    $tUsedStatusShow = language('document/promotion/promotion', 'tLabel12');
                                }  
                            }

                            if (in_array($aValue['UsedStatus'], ["4","5"])) {
                                $tClassStaUse = 'text-danger';
                                if($aValue['UsedStatus'] == "4"){
                                    $tUsedStatusShow = language('document/promotion/promotion', 'tPmhDateExp');
                                }else{
                                    $tUsedStatusShow = language('document/promotion/promotion', 'tStaDoc3');
                                }
                            }
                            /*===== End UsedStatus =====================================*/
                            ?>
                            <tr class="text-center xCNTextDetail2 xWRDHDocItems" id="otrCouponSetUp<?php echo $nKey ?>" data-code="<?php echo $aValue['FTRdhDocNo'] ?>" data-name="<?php echo $aValue['FTRdhDocNo'] ?>">
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                                    <td nowrap class="text-center">
                                        <label class="fancy-checkbox ">
                                            <input id="ocbListItem<?php echo $nKey ?>" type="checkbox" class="ocbListItem" name="ocbListItem[]" <?php echo $tCheckboxDisabled; ?>>
                                            <span class="<?php echo $tClassDisabled ?>">&nbsp;</span>
                                        </label>
                                    </td>
                                <?php endif; ?>
                                <td nowrap class="text-left"><?php echo (!empty($aValue['FTBchName'])) ? $aValue['FTBchName'] : '-' ?></td>
                                <td nowrap class="text-left"><?php echo (!empty($aValue['FTRdhDocNo'])) ? $aValue['FTRdhDocNo'] : '-' ?></td>
                                <td nowrap class="text-center"><?php echo (!empty($aValue['FDRdhDocDate'])) ? $aValue['FDRdhDocDate'] : '-' ?></td>
                                <td nowrap class="text-left"><?php echo (!empty($aValue['FTRdhName'])) ? $aValue['FTRdhName'] : '-' ?></td>
                                <!-- <td nowrap class="text-center">
                                    <label class="xCNTDTextStatus <?php echo $tClassStaDoc; ?>">
                                        <?php echo language('document/conditionredeem/conditionredeem', 'tRDHStaDoc' . $aValue['FTRdhStaDoc']) ?>
                                    </label>
                                </td> -->
                                <td nowrap class="text-center">
                                    <label class="xCNTDTextStatus <?php echo $tClassStaApv; ?>">
                                        <?php echo language('document/conditionredeem/conditionredeem', 'tRDHStaApv' . $aValue['FTRdhStaApv']) ?>
                                    </label>
                                </td>

                                <?php
                                // FTPmhStaClosed,FNPmhStaDocAct

                                // echo $aValue['FTRdhStaClosed'] .' '. 'afaeff';
                                // if ($aValue['FNRdhStaDocAct'] == 1) {
                                //     if ($aValue['FTRdhStaClosed'] == 0) {
                                //         $tClassStaUse = 'text-success';
                                //         $tPmtStaUseShow = language('document/promotion/promotion', 'tActive');
                                //     } else {
                                //         $tClassStaUse = 'text-warning';
                                //         $tPmtStaUseShow = language('document/promotion/promotion', 'tPausedTemporarily');
                                //     }
                                // } else {
                                //     $tClassStaUse = 'text-danger';
                                //     $tPmtStaUseShow = language('document/promotion/promotion', 'tNotActive');
                                // }

                                // $dPmhDStop = date('Y-m-d', strtotime($aValue['FDRdhDStop']));
                                // $dPmhTStop = date('H:i:s', strtotime($aValue['FDRdhTStop']));
                                // $dPmhExp = date('Y-m-d H:i:s', strtotime($dPmhDStop . ' ' . $dPmhTStop));

                                // if (strtotime(date('Y-m-d')) <=  strtotime($dPmhExp)) {
                                //     $tClassStaExp = 'text-success';
                                //     $tPmtStaExpShow = language('document/promotion/promotion', 'tPmhDateNonExp');
                                // } else {
                                //     $tClassStaExp = 'text-danger';
                                //     $tPmtStaExpShow = language('document/promotion/promotion', 'tPmhDateExp');
                                // }
                                ?>

                                <!-- <td class="text-left"><label class="xCNTDTextStatus <?= $tClassStaUse ?>"><?php echo $tPmtStaUseShow; ?></label></td> -->
                                <!-- <td class="text-left"><label class="xCNTDTextStatus <?= $tClassStaExp ?>"><?php echo $tPmtStaExpShow; ?></label></td> -->

                                <td class="text-left"><label class="xCNTDTextStatus <?= $tClassStaUse ?>"><?php echo $tUsedStatusShow; ?></label></td>
                                <td nowrap class="text-left"><?php echo (!empty($aValue['FTUsrNameIns'])) ? $aValue['FTUsrNameIns'] : '-' ?></td>
                                <td nowrap class="text-left"><?php echo (!empty($aValue['FTUsrNameApv'])) ? $aValue['FTUsrNameApv'] : '-' ?></td>

                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaDelete'] == 1) : ?>
                                    <td nowrap>
                                        <img class="xCNIconTable xCNIconDel <?php echo $tClassDisabled ?>" src="<?php echo  base_url() . '/application/modules/common/assets/images/icons/delete.png' ?>" <?php echo $tOnclick ?> title="<?php echo $tTitle ?>">
                                    </td>
                                <?php endif; ?>
                                <?php if ($aAlwEvent['tAutStaFull'] == 1 || $aAlwEvent['tAutStaRead'] == 1 || $aAlwEvent['tAutStaEdit'] == 1) : ?>
                                    <td nowrap>
                                        <img class="xCNIconTable xCNIconEdit" onClick="JSvRDHCallPageEditDocument('<?php echo $aValue['FTRdhDocNo'] ?>','<?php echo $aValue['FTBchCode'] ?>')">
                                    </td>
                                <?php endif; ?>
                            <tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class='text-center xCNTextDetail2' colspan='100%'><?php echo language('common/main/main', 'tCMNNotFoundData') ?></td>
                            </tr>
                        <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <p><?php echo language('common/main/main', 'tResultTotalRecord') ?> <?php echo $aDataList['rnAllRow'] ?> <?php echo language('common/main/main', 'tRecord') ?> <?php echo language('common/main/main', 'tCurrentPage') ?> <?php echo $aDataList['rnCurrentPage'] ?> / <?php echo $aDataList['rnAllPage'] ?></p>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="xWRDHPageDataTable btn-toolbar pull-right">
            <?php if ($nPage == 1) {
                $tDisabledLeft = 'disabled';
            } else {
                $tDisabledLeft = '-';
            } ?>
            <button onclick="JSvRDHClickPageList('previous')" class="btn btn-white btn-sm" <?php echo $tDisabledLeft ?>>
                <i class="fa fa-chevron-left f-s-14 t-plus-1"></i>
            </button>

            <?php for ($i = max($nPage - 2, 1); $i <= max(0, min($aDataList['rnAllPage'], $nPage + 2)); $i++) { ?>
                <?php
                if ($nPage == $i) {
                    $tActive = 'active';
                    $tDisPageNumber = 'disabled';
                } else {
                    $tActive = '';
                    $tDisPageNumber = '';
                }
                ?>
                <button onclick="JSvRDHClickPageList('<?php echo $i ?>')" type="button" class="btn xCNBTNNumPagenation <?php echo $tActive ?>" <?php echo $tDisPageNumber ?>><?php echo $i ?></button>
            <?php } ?>

            <?php if ($nPage >= $aDataList['rnAllPage']) {
                $tDisabledRight = 'disabled';
            } else {
                $tDisabledRight = '-';
            } ?>
            <button onclick="JSvRDHClickPageList('next')" class="btn btn-white btn-sm" <?php echo $tDisabledRight ?>>
                <i class="fa fa-chevron-right f-s-14 t-plus-1"></i>
            </button>
        </div>
    </div>
</div>
<!-- ===================================================== Modal Delete Document Single ===================================================== -->
<div id="odvRDHModalDelDocSingle" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?php echo language('common/main/main', 'tModalDelete') ?></label>
            </div>
            <div class="modal-body">
                <span id="ospTextConfirmDelSingle" class="xCNTextModal" style="display: inline-block; word-break:break-all"></span>
            </div>
            <div class="modal-footer">
                <button id="osmConfirmDelSingle" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button"><?php echo language('common/main/main', 'tModalConfirm') ?></button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?php echo language('common/main/main', 'tModalCancel') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ======================================================================================================================================== -->

<!-- ===================================================== Modal Delete Document Multiple =================================================== -->
<div id="odvRDHModalDelDocMultiple" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header xCNModalHead">
                <label class="xCNTextModalHeard"><?php echo language('common/main/main', 'tModalDelete') ?></label>
            </div>
            <div class="modal-body">
                <span id="ospTextConfirmDelMultiple" class="xCNTextModal" style="display: inline-block; word-break:break-all"></span>
                <input type='hidden' id="ohdConfirmIDDelMultiple">
            </div>
            <div class="modal-footer">
                <button id="osmConfirmDelMultiple" class="btn xCNBTNPrimery xCNBTNPrimery2Btn" type="button"><?php echo language('common/main/main', 'tModalConfirm') ?></button>
                <button class="btn xCNBTNDefult xCNBTNDefult2Btn" type="button" data-dismiss="modal"><?php echo language('common/main/main', 'tModalCancel') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- ======================================================================================================================================== -->
<script type="text/javascript">
    $(document).ready(function() {
        $('.ocbListItem').unbind().click(function() {
            var nCode = $(this).parents('xWRDHDocItems').data('code'); //code
            var tName = $(this).parents('xWRDHDocItems').data('name'); //code
            $(this).prop('checked', true);
            var LocalItemData = localStorage.getItem("LocalItemData");
            var obj = [];
            if (LocalItemData) {
                obj = JSON.parse(LocalItemData);
            } else {}
            var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
            if (aArrayConvert == '' || aArrayConvert == null) {
                obj.push({
                    "nCode": nCode,
                    "tName": tName
                });
                localStorage.setItem("LocalItemData", JSON.stringify(obj));
                JSxRDHTextinModal();
            } else {
                var aReturnRepeat = JStRDHFindObjectByKey(aArrayConvert[0], 'nCode', nCode);
                if (aReturnRepeat == 'None') { //ยังไม่ถูกเลือก
                    obj.push({
                        "nCode": nCode,
                        "tName": tName
                    });
                    localStorage.setItem("LocalItemData", JSON.stringify(obj));
                    JSxRDHTextinModal();
                } else if (aReturnRepeat == 'Dupilcate') { //เคยเลือกไว้แล้ว
                    localStorage.removeItem("LocalItemData");
                    $(this).prop('checked', false);
                    var nLength = aArrayConvert[0].length;
                    for ($i = 0; $i < nLength; $i++) {
                        if (aArrayConvert[0][$i].nCode == nCode) {
                            delete aArrayConvert[0][$i];
                        }
                    }
                    var aNewarraydata = [];
                    for ($i = 0; $i < nLength; $i++) {
                        if (aArrayConvert[0][$i] != undefined) {
                            aNewarraydata.push(aArrayConvert[0][$i]);
                        }
                    }
                    localStorage.setItem("LocalItemData", JSON.stringify(aNewarraydata));
                    JSxRDHTextinModal();
                }
            }
            JSxRDHShowButtonChoose();
        });

        $('#odvRDHModalDelDocMultiple #osmConfirmDelMultiple').unbind().click(function() {
            var nStaSession = JCNxFuncChkSessionExpired();
            if (typeof(nStaSession) !== 'undefined' && nStaSession == 1) {
                JSoRDHDelDocMultiple();
            } else {
                JCNxShowMsgSessionExpired();
            }
        });
    });
</script>