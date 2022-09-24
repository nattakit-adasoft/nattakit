<?php
    $aCompanyInfo = $aDataViewRpt['aCompanyInfo'];
    $aDataFilter = $aDataViewRpt['aDataFilter'];
    $aDataTextRef = $aDataViewRpt['aDataTextRef'];
    $aDataReport = $aDataViewRpt['aDataReport'];
    $nOptDecimalShow = $aDataViewRpt['nOptDecimalShow'];

?>

<style>
    .xCNFooterRpt {
        border-bottom : 7px double #ddd;
    }

    .table thead th, .table>thead>tr>th, .table tbody tr, .table>tbody>tr>td {
        border: 0px transparent !important;
    }

    .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th {
        border-top: 1px solid black !important;
        border-bottom : 1px solid black !important;
        
        /* background-color: #CFE2F3 !important; */
    }

    .table>tbody>tr.xCNTrSubFooter{
        border-top: 1px solid black !important;
        border-bottom : 1px solid black !important;
        /* background-color: #CFE2F3 !important; */
    }

    .table>tbody>tr.xCNTrFooter{
        /* background-color: #CFE2F3 !important; */
        /* border-bottom : 6px double black !important; */
        /* border-top: dashed 1px #333 !important; */
        border-top: 1px solid black !important;
        border-
         : 1px solid black !important;
    }
    .table tbody tr.xCNHeaderGroup, .table>tbody>tr.xCNHeaderGroup>td {
        /* color: #232C3D !important; */
        font-size: 18px !important;
        font-weight: 600;
    }
    .table>tbody>tr.xCNHeaderGroup>td:nth-child(4), .table>tbody>tr.xCNHeaderGroup>td:nth-child(5) {
        text-align: right;
    }

    /*แนวนอน*/
    @media print{@page {
        size: A4 portrait;
        margin: 5mm 5mm 5mm 5mm;
        }}

    } 
    /*แนวตั้ง*/
    /*@media print{@page {size: portrait}}*/
</style>

<div id="odvRptProductTransferHtml">
    <div class="container-fluid xCNLayOutRptHtml">

        <div class="xCNHeaderReport">
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                    <?php if (isset($aCompanyInfo) && !empty($aCompanyInfo)) { ?>

                        <div class="text-left">
                            <label class="xCNRptCompany"><?= $aCompanyInfo['FTCmpName'] ?></label>
                        </div>

                        <?php if ($aCompanyInfo['FTAddVersion'] == '1') { // ที่อยู่แบบแยก ?>
                            <div class="text-left xCNRptAddress">
                                <label>
                                    <?= $aCompanyInfo['FTAddV1No'] . ' ' . $aCompanyInfo['FTAddV1Road'] . ' ' . $aCompanyInfo['FTAddV1Soi'] ?>
                                    <?= $aCompanyInfo['FTSudName'] . ' ' . $aCompanyInfo['FTDstName'] . ' ' . $aCompanyInfo['FTPvnName'] . ' ' . $aCompanyInfo['FTAddV1PostCode'] ?>
                                </label>
                            </div>
                        <?php } ?>

                        <?php if ($aCompanyInfo['FTAddVersion'] == '2') { // ที่อยู่แบบรวม ?>
                            <div class="text-left xCNRptAddress">
                                <label><?= $aCompanyInfo['FTAddV2Desc1'] ?></label>
                            </div>
                            <div class="text-left xCNRptAddress">
                                <label><?= $aCompanyInfo['FTAddV2Desc2'] ?></label>
                            </div>
                        <?php } ?>

                        <div class="text-left xCNRptAddress">
                            <label><?= $aDataTextRef['tRptAddrTel'] . $aCompanyInfo['FTCmpTel'] ?> <?= $aDataTextRef['tRptAddrFax'] . $aCompanyInfo['FTCmpFax'] ?></label>
                        </div>
                        <div class="text-left xCNRptAddress">
                            <label><?= $aDataTextRef['tRptAddrBranch'] . $aCompanyInfo['FTBchName'] ?></label>
                        </div>

                        <div class="text-left xCNRptAddress">
                            <label><?= $aDataTextRef['tRptTaxSalePosTaxId'] . ' ' . $aCompanyInfo['FTAddTaxNo']?></label>
                        </div>

                    <?php } ?>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="text-center">
                                <label class="xCNRptTitle"><?php echo $aDataTextRef['tTitleReport']; ?></label>
                            </div>
                        </div>
                    </div>
                
                    <?php if ((isset($aDataFilter['tBchCodeFrom']) && !empty($aDataFilter['tBchCodeFrom'])) && (isset($aDataFilter['tBchCodeTo']) && !empty($aDataFilter['tBchCodeTo']))) { ?>
                        <!-- ============================ ฟิวเตอร์ข้อมูล สาขา ============================ -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="text-center">
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchFrom']?> : </label>   <label><?=$aDataFilter['tBchNameFrom'];?></label>&nbsp;
                                    <label class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptBchTo']?> : </label>     <label><?=$aDataFilter['tBchNameTo']; ?></label>
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
                        <?php date_default_timezone_set('Asia/Bangkok'); ?>
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
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:5%;" rowspan="2"><?php echo $aDataTextRef['tRptPdtCode']; ?></th>
                            <th nowrap class="text-left xCNRptColumnHeader" style="vertical-align : middle;text-align:left; width:40%;" rowspan="2"><?php echo $aDataTextRef['tRptPdtName']; ?></th>
                            <th nowrap class="text-center xCNRptColumnHeader" colspan="3" style='border-bottom: dashed 1px #333 !important;'><?php echo $aDataTextRef['tRptPdtInven'];?></th>
                        </tr>
                        <tr style="border-bottom : 1px solid black !important;">
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:7%;"><?php echo $aDataTextRef['tRptPosVendingCount']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptCabinetCostAvg']; ?></th>
                            <th nowrap class="text-right xCNRptColumnHeader" style="width:5%;"><?php echo $aDataTextRef['tRptCabinetCost'];   ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($aDataReport['aRptData']) && !empty($aDataReport['aRptData'])) { ?>
                            <?php
                            // Set ตัวแปร SumSubFooter และ ตัวแปร SumFooter
                            $nSubSumAjdWahB4Adj = 0;
                            $nSubSumAjdUnitQty  = 0;
                            $nVal               = 0;
                            $tWahCodeOld        = ''; 
                            $tPDTCodeOld        = ''; 
                            $tChainCodeName     = 'First';
                            ?>

                            <!--กลาง-->
                            <?php foreach ($aDataReport['aRptData'] as $nKey => $aValue) { ?>
                                <?php
                                    // Step 1 เตรียม Parameter สำหรับการ Groupping
                                    $tWahCode       = $aValue["FTWahName"];
                                    $tPDTCode       = $aValue["FTPdtCode"];
                                    $tPDTName       = $aValue["FTPdtName"];
                                    $nGroupMember   = $aValue["FNRptGroupMember"];
                                    $nRowPartID     = $aValue["FNRowPartID"];
                                ?>
                                <?php
                                    // Step 2 Groupping data
                                    $tChainCode         = $aValue["FTPgpChainName"];
                                    $aGroupChain        = array($tChainCode);
                                    if($tChainCodeName != $tChainCode ){
                                        echo "<tr>";
                                        for ($i = 0; $i < count($aGroupChain); $i++) {
                                            if ($aGroupChain[$i] !== 'N') {
                                                if($aGroupChain[$i] == ''){
                                                    $tGrounp = 'อื่นๆ';
                                                }else{
                                                    $tGrounp = $aGroupChain[$i];
                                                }

                                                $tNmaeLang = $aDataTextRef['tRptPdtGrp'];
                                                echo "<tr><td class='xCNRptGrouPing' colspan='5' style='border-top: dashed 1px #333 !important;'></td></tr>";
                                                echo "<td class='xCNRptGrouPing  text-left' style='padding: 5px;' colspan='4'>".$tNmaeLang." : " . $tGrounp . "</td>";
                                            } else {
                                                echo "<td></td>";
                                            }
                                        }
                                        echo "</tr>";
                                        $tChainCodeName = $tChainCode;
                                   }else{
                                         //echo "<tr><td>ไม่ขึ้นบรรทัดใหม่<td></tr>";
                                    }


                                    //สินค้า
                                    $tPDTCodeOnly   = $aValue["FTPdtCode"];
                                    if($tPDTCodeOld == $tPDTCodeOnly){
                                        // echo "<tr><td>ไม่ขึ้นบรรทัดใหม่<td></tr>";
                                    }else{
                                      
                                        $aGrouppingData     = array($tPDTName);

                                        //จำนวนสินค้า
                                        $nFCStkQty_SUM      = $aValue["FCStkQty_SUM"];
                                        if($nFCStkQty_SUM == null){
                                            $nFCStkQty_SUM  = $aValue["FCStkQty"];
                                        }else{
                                            $nFCStkQty_SUM  = $aValue["FCStkQty_SUM"];
                                        }

                                        //ต้นทุนเฉลี่ย
                                        $nFCPdtCostAVGEX_SUM = $aValue["FCPdtCostAVGEX_SUM"];

                                        //ทุนรวม
                                        $FCPdtCostTotal_SUM = $aValue["FCPdtCostTotal_SUM"];

                                        $tNmaeLangWah = 'สินค้า';
                                        echo "<tr>";
                                        for ($i = 0; $i < count($aGrouppingData); $i++) {
                                            if ($aGrouppingData[$i] !== 'N') {
                                                echo "<td class='xCNRptGrouPing  text-left' style='padding-left: 30px !important;' colspan='1'>".$tPDTCode. "</td>";
                                                echo "<td class='xCNRptGrouPing  text-left' style='padding-left: 0px !important;' colspan='1'>". $aGrouppingData[$i] . "</td>";
                                                echo "<td class='xCNRptGrouPing  text-right' style='padding-left: 30px !important;'>".number_format($nFCStkQty_SUM, $nOptDecimalShow)." ชิ้น</td>";
                                                echo "<td class='xCNRptGrouPing  text-right' style='padding-left: 30px !important;'>".number_format($nFCPdtCostAVGEX_SUM, $nOptDecimalShow)."</td>";
                                                echo "<td class='xCNRptGrouPing  text-right' style='padding-left: 30px !important;'>".number_format($FCPdtCostTotal_SUM, $nOptDecimalShow)."</td>";
                                            } else {
                                                echo "<td></td>";
                                            }
                                        }
                                        echo "</tr>";
                                        $tPDTCodeOld = $tPDTCodeOnly;
                                    }



                                    //คลังสินค้า
                                    // $tWahCodeOnly       = $aValue["FTWahCode"];
                                    // if($tWahCodeOld == $tWahCodeOnly){
                                    //     //echo "<tr><td>ไม่ขึ้นบรรทัดใหม่<td></tr>";
                                    // }else{
                                    //     $aGrouppingData     = array($tWahCode);
                                    //     if ($nRowPartID == 1) {


                                    //         $nFCStkQty_SUM      = $aValue["FCStkQty_SUM"];
                                    //         if($nFCStkQty_SUM == null){
                                    //             $nFCStkQty_SUM  = $aValue["FCStkQty"];
                                    //         }
                                    //         $FCPdtCostTotal_SUM = $aValue["FCPdtCostTotal_SUM"];

                                    //         $tNmaeLangWah = $aDataTextRef['tRptWahName'];
                                    //         echo "<tr>";
                                    //         for ($i = 0; $i < count($aGrouppingData); $i++) {
                                    //             if ($aGrouppingData[$i] !== 'N') {
                                    //                 echo "<td class='xCNRptGrouPing  text-left' style='padding-left: 30px !important;' colspan='2'>".$tNmaeLangWah." : " . $aGrouppingData[$i] . "</td>";
                                    //                 echo "<td class='xCNRptGrouPing  text-right' style='padding-left: 30px !important;'>".number_format($nFCStkQty_SUM, $nOptDecimalShow)."</td>";
                                    //                 echo "<td class='xCNRptGrouPing  text-left' style='padding-left: 30px !important;'></td>";
                                    //                 echo "<td class='xCNRptGrouPing  text-right' style='padding-left: 30px !important;'>".number_format($FCPdtCostTotal_SUM, $nOptDecimalShow)."</td>";
                                    //             } else {
                                    //                 echo "<td></td>";
                                    //             }
                                    //         }
                                    //         echo "</tr>";
                                    //     }
                                    //     $tWahCodeOld = $tWahCodeOnly;
                                    // }
                                ?>

                                <tr>
                                    <td nowrap class="text-left xCNRptDetail" style="padding-left: 50px !important;" colspan="2">(<?php echo $aValue["FTWahCode"]; ?>) <?php echo $aValue["FTWahName"]; ?></td>
                                    <td nowrap class="text-right xCNRptDetail"><?php echo number_format($aValue["FCStkQty"], $nOptDecimalShow) ?></td>
                                    <td nowrap class="text-right xCNRptDetail"></td>
                                    <td nowrap class="text-right xCNRptDetail"></td>
                                </tr>

                                <?php
                                    $nSubSumQty = number_format($aValue["FCStkQty_SubTotal"], $nOptDecimalShow);
                                    $aSumFooter = array($aDataTextRef['tRptTotal'], 'N' , $nSubSumQty);

                                    /*if($nRowPartID == $nGroupMember + 1){
                                        echo '<tr>';
                                        for($i = 0;$i<count($aSumFooter);$i++){
                                            if($aSumFooter[$i] !='N'){
                                                $tFooterVal =   $aSumFooter[$i];           
                                            }else{
                                                $tFooterVal =   '';
                                            }

                                            if(count($aSumFooter) - 1 == intval($i)){
                                                $tClassCss = "text-right";
                                            }else{
                                                $tClassCss = "text-left";
                                            }

                                            if(intval($i) == 0){
                                                $nColspan = "colspan=2";
                                            }else{
                                                $nColspan = "colspan=0";
                                            }
                                            
                                            echo "<td class='xCNRptGrouPing $tClassCss' $nColspan style='border-top: dashed 1px #333 !important;'>".$tFooterVal."</td>";
                                            // echo "<td class='xCNRptGrouPing'  style='border-top: dashed 1px #333 !important;'></td>";
                                        }
                                        echo "<td class='xCNRptGrouPing text-right' colspan=0 style='border-top: dashed 1px #333 !important;'></td>";
                                        echo "</tr>";


                                        $nCountDataAll = count($aDataReport['aRptData']);
                                        if($nCountDataAll - 1 != $nKey){
                                            echo "<tr><td class='xCNRptGrouPing' colspan='5' style='border-top: dashed 1px #333 !important;'></td></tr>";
                                        }
                                    }*/

                                    
                                    // Step 5 เตรียม Parameter สำหรับ SumFooter
                                    // $nSumFooterAjdWahB4Adj  = number_format($aValue["FCPdtCostEx_Footer"],2);
                                    // $nSumFooterAjdUnitQty   = number_format($aValue["FCStkQty_Footer"],2);

                                    $nVal = $nVal + $aValue["FCPdtCostTotal"];
                                    $tFCPdtCostTotal    = number_format($nVal, $nOptDecimalShow);
                                    $nSumCostExQtyQty   = number_format($aValue["FCStkQty_Footer"], $nOptDecimalShow);
                                    $paFooterSumData    = array($aDataTextRef['tRptTotalFooter'],'N',$nSumCostExQtyQty,'N', $tFCPdtCostTotal);

                                   
                                ?>
                            <?php } ?>

                            <!--ล่าง-->                
                            <?php
                                $nPageNo    = $aDataReport["aPagination"]["nDisplayPage"];
                                $nTotalPage = $aDataReport["aPagination"]["nTotalPage"];

                                if ($nPageNo == $nTotalPage) {
                                    echo "<tr class='xCNTrFooter'>";
                                    for ($i = 0; $i < count($paFooterSumData); $i++) {

                                        if ($i == 0) {
                                            $tStyle = 'text-align:left;border-top:1px solid #333;border-bottom:1px solid #333;/*background-color: #CFE2F3;*/';
                                        } else {
                                            $tStyle = 'text-align:right;border-top:1px solid #333;border-bottom:1px solid #333;/*background-color: #CFE2F3;*/';
                                        }
                                        if ($paFooterSumData[$i] != 'N') {
                                            $tFooterVal = $paFooterSumData[$i];
                                        } else {
                                            $tFooterVal = '';
                                        }
                                        if ($i == 0) {
                                            echo "<td class='xCNRptSumFooter text-left'>" . $tFooterVal . "</td>";
                                        } else {
                                            echo "<td class='xCNRptSumFooter text-right'>" . $tFooterVal . "</td>";
                                        }
                                    }
                                    echo "<tr>";
                                }
                                //FCNtHRPTSumFooter($nPageNo, $nTotalPage, $paFooterSumData);
                            ?>
                            
                        <?php } else { ?>
                            <tr><td class='text-center xCNTextDetail2' colspan='100%'><?php echo $aDataTextRef['tRptAdjStkNoData']; ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

           
            <?php if ((isset($aDataFilter['tWahNameFrom']) && !empty($aDataFilter['tWahNameFrom'])) && (isset($aDataFilter['tWahNameTo']) && !empty($aDataFilter['tWahNameTo']))
                        || (isset($aDataFilter['tMerCodeFrom']) && !empty($aDataFilter['tMerCodeFrom'])) && (isset($aDataFilter['tMerCodeTo']) && !empty($aDataFilter['tMerCodeTo']))
                        || (isset($aDataFilter['tShpCodeFrom']) && !empty($aDataFilter['tShpCodeFrom'])) && (isset($aDataFilter['tShpCodeTo']) && !empty($aDataFilter['tShpCodeTo']))
                        || (isset($aDataFilter['tPosCodeFrom']) && !empty($aDataFilter['tPosCodeFrom'])) && (isset($aDataFilter['tPosCodeTo']) && !empty($aDataFilter['tPosCodeTo']))
                        || (isset($aDataFilter['tWahCodeFrom']) && !empty($aDataFilter['tWahCodeFrom'])) && (isset($aDataFilter['tWahCodeTo']) && !empty($aDataFilter['tWahCodeTo']))
                        || (isset($aDataFilter['tBchCodeSelect']))
                        || (isset($aDataFilter['tMerCodeSelect']))
                        || (isset($aDataFilter['tShpCodeSelect'])) 
                        || (isset($aDataFilter['tPosCodeSelect'])) 
                        || (isset($aDataFilter['tWahCodeSelect']))
                        ){ ?>
                    <div class="xCNRptFilterTitle">
                        <div class="text-left">
                            <label class="xCNTextConsOth"><?=$aDataTextRef['tRptConditionInReport'];?></label>
                        </div>
                    </div>
                <?php }; ?>

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
                            <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjMerChantFrom'].' : </span>'.$aDataFilter['tMerNameFrom'];?></label>
                            <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjMerChantTo'].' : </span>'.$aDataFilter['tMerNameTo'];?></label>
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
                            <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjShopFrom'].' : </span>'.$aDataFilter['tShpNameFrom'];?></label>
                            <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjShopTo'].' : </span>'.$aDataFilter['tShpNameTo'];?></label>
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
                                <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjPosFrom'].' : </span>'.$aDataFilter['tPosCodeFrom'];?></label>
                                <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjPosTo'].' : </span>'.$aDataFilter['tPosCodeTo'];?></label>
                            </div>
                        </div>
                <?php endif; ?> 
                <?php if (isset($aDataFilter['tPosCodeSelect']) && !empty($aDataFilter['tPosCodeSelect'])) : ?>
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptPosFrom']; ?> : </span> <?php echo ($aDataFilter['bPosStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tPosCodeSelect']; ?></label>
                        </div>
                    </div>
                <?php endif; ?> 

                <!-- ============================ ฟิวเตอร์ข้อมูล คลังสินค้า ============================ -->
                <?php if ((isset($aDataFilter['tWahCodeFrom']) && !empty($aDataFilter['tWahCodeFrom'])) && (isset($aDataFilter['tWahCodeTo']) && !empty($aDataFilter['tWahCodeTo']))) : ?>
                    <div class="xCNRptFilterBox">
                        <div class="text-left xCNRptFilter">
                            <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjWahFrom'].' : </span>'.$aDataFilter['tWahNameFrom'];?></label>
                            <label class="xCNRptLabel xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjWahTo'].' : </span>'.$aDataFilter['tWahNameTo'];?></label>
                        </div>
                    </div>
                <?php endif; ?> 
                <?php if (isset($aDataFilter['tWahCodeSelect']) && !empty($aDataFilter['tWahCodeSelect'])) : ?>
                    <div class="xCNRptFilterBox">
                        <div class="xCNRptFilter">
                            <label class="xCNRptDisplayBlock"><span class="xCNRptFilterHead"><?php echo $aDataTextRef['tRptAdjWahFrom']; ?> : </span> <?php echo ($aDataFilter['bWahStaSelectAll']) ? $aDataTextRef['tRptAll'] : $aDataFilter['tWahNameSelect']; ?></label>
                        </div>
                    </div>
                <?php endif; ?>  
        </div>
        <div class="xCNFooterPageRpt">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="text-right">
                        <?php if ($aDataReport["aPagination"]["nTotalPage"] > 0) { ?>
                            <label class="xCNRptLabel"><?php echo $aDataReport["aPagination"]["nDisplayPage"] . ' / ' . $aDataReport["aPagination"]["nTotalPage"]; ?></label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var oFilterLabel = $('.report-filter .text-left label:first-child');
        var nMaxWidth = 0;
        oFilterLabel.each(function(index){
            var nLabelWidth = $(this).outerWidth();
            if(nLabelWidth > nMaxWidth){
                nMaxWidth = nLabelWidth;
            }
        });
        $('.report-filter .text-left label:first-child').width(nMaxWidth + 50);
    });
</script>