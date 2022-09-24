<?php
    $aDataReport = $aDataViewRpt['aDataReport'];
    $aDataTextRef = $aDataViewRpt['aDataTextRef'];
    $aDataFilter = $aDataViewRpt['aDataFilter'];
?>
<style>
    .xCNFooterRpt {
        border-bottom: 7px double #ddd;
    }

    .table thead th,
    .table>thead>tr>th,
    .table tbody tr,
    .table>tbody>tr>td {
        border: 0px transparent !important;
    }

    .table>thead:first-child>tr:first-child>td,
    .table>thead:first-child>tr:first-child>th {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;

        /* background-color: #CFE2F3 !important; */
    }

    .table>tbody>tr.xCNTrSubFooter {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
        /* background-color: #CFE2F3 !important; */
    }

    .table>tbody>tr.xCNTrFooter {
        /* background-color: #CFE2F3 !important; */
        /* border-bottom : 6px double black !important; */
        /* border-top: dashed 1px #333 !important; */
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }

    .table tbody tr.xCNHeaderGroup,
    .table>tbody>tr.xCNHeaderGroup>td {
        /* color: #232C3D !important; */
        font-size: 18px !important;
        font-weight: 600;
    }

    .table>tbody>tr.xCNHeaderGroup>td:nth-child(4),
    .table>tbody>tr.xCNHeaderGroup>td:nth-child(5) {
        text-align: right;
    }

    /*แนวนอน*/
    @media print {
        @page {
            size: A4 landscape;
        }

        body {
            margin: 0;
            color: #000;
            background-color: #fff;
        }

    }

    /*แนวตั้ง*/
    /*@media print{@page {size: portrait}}*/
</style>
<div id="odvRptTaxSalePosHtml">
    <div class="container-fluid xCNLayOutRptHtml">
        <div class="xCNHeaderReport">
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php if (isset($aCompanyInfo) && !empty($aCompanyInfo)) { ?>

                        <div class="text-left">
                            <label class="xCNRptCompany"><?= $aCompanyInfo['FTCmpName'] ?></label>
                        </div>

                        <?php if ($aCompanyInfo['FTAddVersion'] == '1') { // ที่อยู่แบบแยก 
                        ?>
                            <div class="text-left xCNRptAddress">
                                <label><?= $aCompanyInfo['FTAddV1No'] . ' ' . $aCompanyInfo['FTAddV1Road'] . ' ' . $aCompanyInfo['FTAddV1Soi'] ?>
                                    <?= $aCompanyInfo['FTSudName'] . ' ' . $aCompanyInfo['FTDstName'] . ' ' . $aCompanyInfo['FTPvnName'] . ' ' . $aCompanyInfo['FTAddV1PostCode'] ?></label>
                            </div>
                        <?php } ?>

                        <?php if ($aCompanyInfo['FTAddVersion'] == '2') { // ที่อยู่แบบรวม 
                        ?>
                            <div class="text-left xCNRptAddress">
                                <label><?= $aCompanyInfo['FTAddV2Desc1'] ?></label>
                            </div>
                            <div class="text-left xCNRptAddress">
                                <label><?= $aCompanyInfo['FTAddV2Desc2'] ?></label>
                            </div>
                        <?php } ?>

                        <div class="text-left xCNRptAddress">
                            <label><?= $aDataTextRef['tRptTaxSalePosTel'] . $aCompanyInfo['FTCmpTel'] ?> <?= $aDataTextRef['tRptTaxSalePosFax'] . $aCompanyInfo['FTCmpFax'] ?></label>
                        </div>
                        <div class="text-left xCNRptAddress">
                            <label><?= $aDataTextRef['tRptTaxSalePosBch'] . $aCompanyInfo['FTBchName'] ?></label>
                            <!-- <label><?= $aCompanyInfo['FTBchName'] ?></label> -->
                        </div>

                        <div class="text-left xCNRptAddress">
                            <label><?= $aDataTextRef['tRptTaxSalePosTaxId'] . $aCompanyInfo['FTAddTaxNo'] ?></label>
                        </div>

                    <?php } ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <div class="text-center">
                        <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                    </div>

                    <?php if ((isset($aDataFilter['tDocDateFrom']) && !empty($aDataFilter['tDocDateFrom'])) && (isset($aDataFilter['tDocDateTo']) && !empty($aDataFilter['tDocDateTo']))) : ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล วันที่สร้างเอกสาร ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center xCNRptFilter">
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSaleByCashierAndPosFilterDocDateFrom']; ?> </span> <?php echo date("d/m/Y", strtotime($aDataFilter['tDocDateFrom'])); ?></label>
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptSaleByCashierAndPosFilterDocDateTo']; ?> </span> <?php echo date("d/m/Y", strtotime($aDataFilter['tDocDateTo'])); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ((isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center xCNRptFilter">
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptTaxSalePosFilterBchFrom'] . ' </span>' . $aDataFilter['tBchNameFrom']; ?></label>&nbsp;&nbsp;
                                    <label><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptTaxSalePosFilterBchTo'] . ' </span>' . $aDataFilter['tBchNameTo']; ?></label>
                                </div>
                            </div>
                        </div>
                    <?php }; ?>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "></div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 table-responsive">
                    <div class="text-right">
                        <label class="xCNRptDataPrint"><?php echo $aDataTextRef['tDatePrint'] . ' ' . date('d/m/Y') . ' ' . $aDataTextRef['tTimePrint'] . ' ' . date('H:i:s'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="xCNContentReport">
            <div id="odvRptTableAdvance" class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:5%;"><?php echo $aDataTextRef['tRptTaxSalePosSeq']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader" style="vertical-align : middle;text-align:center; width:5%;"><?php echo $aDataTextRef['tRptDate']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:5%;"><?php echo $aDataTextRef['tRptBillNo']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:5%;"><?php echo $aDataTextRef['tRptRefDoc']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:10%;"><?php echo $aDataTextRef['tRptSOKADSNumber']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:10%;"><?php echo $aDataTextRef['tRptCustCode']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:10%;"><?php echo $aDataTextRef['tRptBillNote']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptValue']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptVat']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptDisChgNotVat']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptRnd']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptTotal']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) { ?>
                            <?php
                            $nSeq = "";
                            $tGrouppingBch = "";
                            $tPosCodeSub = "";
                            $nCountBch = 0;
                            $nPosCodeOld = "";
                            ?>
                            <?php foreach ($aDataReport['aRptData'] as $nKey => $aValue) { ?>
                                <?php
                                // Step 1 เตรียม Parameter สำหรับการ Groupping
                                $tXrcNet = 0;
                                $tDocNo = $aValue["FTXshDocNo"];
                                $tPosCode = $aValue["FTPosCode"];
                                $tBchCode = $aValue["FTBchCode"];
                                $tBchName = $aValue["FTBchName"];
                                $tDocDate = date("d/m/Y", strtotime($aValue['FDXshDocDate']));
                                $nRowPartID = $aValue["FNRowPartID"];
                                $nGroupMember = $aValue['FNRptGroupMember'];

                                $cBchVatable = number_format($aValue["FCXshVatable_SUMBCH"], $nOptDecimalShow);
                                $cBchVat = number_format($aValue["FCXshVat_SUMBCH"], $nOptDecimalShow);
                                $cBchAmtNV = number_format($aValue["FCXshAmtNV_SUMBCH"], $nOptDecimalShow);
                                $cBchRnd = number_format($aValue["FCXshRnd_SUMBCH"], $nOptDecimalShow);
                                $cBchGrand = number_format($aValue["FCXshGrand_SUMBCH"], $nOptDecimalShow);

                                $cPosVatable = number_format($aValue["FCXshVatable_SUMPOS"], $nOptDecimalShow);
                                $cPosVat = number_format($aValue["FCXshVat_SUMPOS"], $nOptDecimalShow);
                                $cPosAmtNV = number_format($aValue["FCXshAmtNV_SUMPOS"], $nOptDecimalShow);
                                $cPosRnd = number_format($aValue["FCXshRnd_SUMPOS"], $nOptDecimalShow);
                                $cPosGrand = number_format($aValue["FCXshGrand_SUMPOS"], $nOptDecimalShow);
                                ?>

                                <?php
                                // Step 2 Groupping data
                                $aGrouppingDataBch = array($aDataTextRef['tRptTaxSalePosBch'] . '(' . $tBchCode . ') ' .  $tBchName, $cBchVatable, $cBchVat, $cBchAmtNV, $cBchRnd, $cBchGrand);
                                $aGrouppingData = array($aDataTextRef['tRptTaxSalePosSale'] .' '. $tPosCode, $cPosVatable, $cPosVat, $cPosAmtNV, $cPosRnd, $cPosGrand);

                                if ($tGrouppingBch == $tBchCode && $aValue['FNRowPartID'] == 1) {
                                    // $tSumFooter  = array('N','N','N','N','N','N','N','N','N','N','N');
                                    // if($nRowPartID == $nGroupMember){
                                    //     echo '<tr>';
                                    //     for($i = 0;$i<count($tSumFooter);$i++){
                                    //         if($tSumFooter[$i] !='N'){
                                    //             $tFooterVal =   $tSumFooter[$i];           
                                    //         }else{
                                    //             $tFooterVal =   '';
                                    //         }
                                    //             echo "<td class='xCNRptGrouPing'  style='border-bottom: dashed 1px #333 !important;'>".$tFooterVal."</td>";
                                    //     }
                                    //     echo '</tr>';
                                    // }
                                }

                                if ($tGrouppingBch != $tBchCode && $nCountBch > 0) {
                                    $aSumFooter = array('N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N');
                                    if ($nRowPartID == $nGroupMember) {
                                        echo '<tr>';
                                        for ($i = 0; $i < count($aSumFooter); $i++) {
                                            if ($aSumFooter[$i] != 'N') {
                                                $tFooterVal = $aSumFooter[$i];
                                            } else {
                                                $tFooterVal = '';
                                            }
                                            echo "<td class='xCNRptGrouPing' style='border-bottom: dashed 1px #333 !important;'>" . $tFooterVal . "</td>";
                                        }
                                        echo '</tr>';
                                    }
                                }

                                if ($tGrouppingBch != $tBchCode) {
                                    FCNtHRPTHeadGrouppingRptTSPBch($nRowPartID, $aGrouppingDataBch, 7);
                                    $tGrouppingBch = $tBchCode;
                                    $nCountBch++;
                                    $nSeq = 1;
                                    $nPosCodeOld = '';
                                }

                                // เรียงตาม pos ใหม่
                                if ($nPosCodeOld != $aValue["FTPosCode"]) {
                                    echo "<tr><td class='xCNRptGrouPing' colspan='12' style='border-bottom: dashed 1px #333 !important;'></td></tr>";
                                    echo "<tr>";
                                    for ($i = 0; $i < count($aGrouppingData); $i++) {
                                        //echo '<td>'.$nPosXshAmt.'</td>';
                                        if ($aGrouppingData[$i] == $aGrouppingData[0]) {
                                            echo "<td class='xCNRptGrouPing' colspan='7' style='padding: 5px; text-indent:22px;'>" . $aGrouppingData[$i] . "</td>";
                                        } else {
                                            echo "<td class='xCNRptGrouPing text-right' style='padding: 5px; text-indent:22px;'>" . $aGrouppingData[$i] . "</td>";
                                        }
                                    }
                                    echo "</tr>";
                                    $nPosCodeOld = $aValue["FTPosCode"];
                                    $nSeq = 1;
                                }
                                ?>
                                <!--  Step 2 แสดงข้อมูลใน TD  -->
                                <tr>
                                    <td class="text-left xCNRptDetail" style="text-indent:22px;"><?php echo $nSeq++; ?></td>
                                    <td class="text-center xCNRptDetail"><?php echo date("d/m/Y", strtotime($aValue['FDXshDocDate'])); ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue['FTXshDocNo']; ?></td>
                                    <td class="text-left xCNRptDetail"><?php echo $aValue["FTXshRefInt"]; ?></td>

                                    <td class="text-left xCNRptDetail">
                                        <?php 
                                            echo !empty($aValue["FTXshSOKADS"])?$aValue["FTXshSOKADS"]:'-';
                                        ?>
                                    </td>

                                    <td class="text-left xCNRptDetail">
                                        <?php 
                                            echo !empty($aValue["FTCstCode"])?$aValue["FTCstCode"]:'-';
                                        ?>
                                    </td>

                                    <td class="text-left xCNRptDetail">
                                        <?php 
                                            echo !empty($aValue["FTXshRmk"])?$aValue["FTXshRmk"]:'-';
                                        ?>
                                    </td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXshVatable"], $nOptDecimalShow); ?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXshVat"], $nOptDecimalShow); ?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXshAmtNV"], $nOptDecimalShow); ?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXshRnd"], $nOptDecimalShow); ?></td>
                                    <td class="text-right xCNRptDetail"><?php echo number_format($aValue["FCXshGrand"], $nOptDecimalShow); ?></td>
                                </tr>
                                <?php
                                // Step 3 : เตรียม Parameter สำหรับ Summary Sub Footer
                                // $aSumFooter1 = array('N','N', 'เงินสด', number_format($aValue["rcCash"], 2));
                                // $aSumFooter2 = array('N','N', 'บัตรเครดิต', number_format($aValue["rcCredit"], 2));

                                // Step 4 : สั่ง Summary Sub Footer
                                // Parameter 
                                // $nGroupMember = จำนวนข้อมูลทั้งหมดในกลุ่ม
                                // $nRowPartID = ลำดับข้อมูลในกลุ่ม

                                $tPosCodeSub = $aValue["FTPosCode"];

                                // Step 5 เตรียม Parameter สำหรับ SumFooter
                                $aFooterSumData = array($aDataTextRef['tRptTaxSalePosByDateTotalSub'], 'N', 'N', 'N', 'N', 'N', 'N', number_format($aValue['FCXshVatable_Footer'], $nOptDecimalShow), number_format($aValue['FCXshVat_Footer'], $nOptDecimalShow), number_format($aValue['FCXshAmtNV_Footer'], $nOptDecimalShow), number_format($aValue['FCXshRnd_Footer'], $nOptDecimalShow), number_format($aValue['FCXshGrand_Footer'], $nOptDecimalShow));
                                ?>
                            <?php } ?>
                            <?php
                            // Step 6 : สั่ง Summary Footer
                            $nPageNo = $aDataReport["aPagination"]["nDisplayPage"];
                            $nTotalPage = $aDataReport["aPagination"]["nTotalPage"];
                            FCNtHRPTSumFooter($nPageNo, $nTotalPage, $aFooterSumData);
                            ?>
                        <?php } else { ?>
                            <tr>
                                <td class='text-center xCNTextDetail2' colspan='100%'><?php echo $aDataTextRef['tRptTaxSalePosNoData']; ?></td>
                            </tr>
                        <?php }; ?>
                    </tbody>
                </table>
            </div>

            <!--เเสดงหน้า-->
            <div class="xCNRptFilterTitle">
                <div class="text-right">
                    <label><?= language('report/report/report', 'tRptPage') ?> <?= $aDataReport["aPagination"]["nDisplayPage"] ?> <?= language('report/report/report', 'tRptTo') ?> <?= $aDataReport["aPagination"]["nTotalPage"] ?> </label>
                </div>
            </div>

            <div class="xCNRptFilterTitle">
                <div class="text-left">
                    <label class="xCNTextConsOth"><?= $aDataTextRef['tRptConditionInReport']; ?></label>
                </div>
            </div>

            <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
            <?php if (isset($aDataFilter['tBchCodeSelect']) && !empty($aDataFilter['tBchCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']; ?> : </span> <?php echo ($aDataFilter['bBchStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tBchNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล กลุ่มธุรกิจ ============================ -->
            <?php if ((isset($aDataFilter['tMerCodeFrom']) && !empty($aDataFilter['tMerCodeFrom'])) && (isset($aDataFilter['tMerCodeTo']) && !empty($aDataFilter['tMerCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjMerChantFrom'] . ' : </span>' . $aDataFilter['tMerNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjMerChantTo'] . ' : </span>' . $aDataFilter['tMerNameTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tMerCodeSelect']) && !empty($aDataFilter['tMerCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptMerFrom']; ?> : </span> <?php echo ($aDataFilter['bMerStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tMerNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล ร้านค้า ============================ -->
            <?php if ((isset($aDataFilter['tShpCodeFrom']) && !empty($aDataFilter['tShpCodeFrom'])) && (isset($aDataFilter['tShpCodeTo']) && !empty($aDataFilter['tShpCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjShopFrom'] . ' : </span>' . $aDataFilter['tShpNameFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjShopTo'] . ' : </span>' . $aDataFilter['tShpNameTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tShpCodeSelect']) && !empty($aDataFilter['tShpCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptShopFrom']; ?> : </span> <?php echo ($aDataFilter['bShpStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tShpNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล จุดขาย ============================ -->
            <?php if ((isset($aDataFilter['tPosCodeFrom']) && !empty($aDataFilter['tPosCodeFrom'])) && (isset($aDataFilter['tPosCodeTo']) && !empty($aDataFilter['tPosCodeTo']))) : ?>
                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjPosFrom'] . ' : </span>' . $aDataFilter['tPosCodeFrom']; ?></label>
                        <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjPosTo'] . ' : </span>' . $aDataFilter['tPosCodeTo']; ?></label>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($aDataFilter['tPosCodeSelect']) && !empty($aDataFilter['tPosCodeSelect'])) : ?>
                <div class="xCNRptFilterBox">
                    <div class="xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptPosFrom']; ?> : </span> <?php echo ($aDataFilter['bPosStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tPosNameSelect']; ?></label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ============================ ฟิวเตอร์ข้อมูล ประเภทจุดขาย ============================ -->
            <?php if (isset($aDataFilter['tPosType']) && false) { ?>

                <div class="xCNRptFilterBox">
                    <div class="text-left xCNRptFilter">
                        <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptPosTypeName'] . ' : </span>' . $aDataTextRef['tRptPosType' . $aDataFilter['tPosType']]; ?></label>
                    </div>
                </div>

            <?php } ?>

        </div>
        <div class="xCNFooterPageRpt">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <?php if ($aDataReport["aPagination"]["nTotalPage"] > 0) : ?>
                            <label class="xCNRptLabel"><?php echo $aDataReport["aPagination"]["nDisplayPage"] . ' / ' . $aDataReport["aPagination"]["nTotalPage"]; ?></label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var oFilterLabel = $('.report-filter .text-left label:first-child');
        var nMaxWidth = 0;
        oFilterLabel.each(function(index) {
            var nLabelWidth = $(this).outerWidth();
            if (nLabelWidth > nMaxWidth) {
                nMaxWidth = nLabelWidth;
            }
        });
        $('.report-filter .text-left label:first-child').width(nMaxWidth + 50);
    });
</script>