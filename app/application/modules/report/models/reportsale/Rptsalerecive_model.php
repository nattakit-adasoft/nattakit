<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptsalerecive_model extends CI_Model {

    /**
     * Functionality: Delete Temp Report
     * Parameters:  Function Parameter
     * Creator: 10/07/2019 Saharat(Golf)
     * Last Modified :
     * Return : Call Store Proce
     * Return Type: Array
     */
    public function FSnMExecStoreReport($paDataFilter) {
        $nLangID      = $paDataFilter['nLangID'];
        $tComName     = $paDataFilter['tCompName'];
        $tRptCode     = $paDataFilter['tRptCode'];
        $tUserSession = $paDataFilter['tUserSession'];

         // สาขา
         $tBchCodeSelect = ($paDataFilter['bBchStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tBchCodeSelect']); 
         // ร้านค้า
         $tShpCodeSelect = ($paDataFilter['bShpStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tShpCodeSelect']);
         // กลุ่มธุรกิจ
         $tMerCodeSelect = ($paDataFilter['bMerStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tMerCodeSelect']);
         // ประเภทเครื่องจุดขาย
         $tPosCodeSelect = ($paDataFilter['bPosStaSelectAll']) ? '' : FCNtAddSingleQuote($paDataFilter['tPosCodeSelect']);
 
  
        $tCallStore = "{ CALL SP_RPTxPaymentDET1001005(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) }";
        $aDataStore = array(
            'pnLngID'       => $nLangID,
            'ptComName'     => $tComName,
            'ptRptCode'     => $tRptCode,
            'ptUserSession' => $tUserSession,

            'pnFilterType'  => $paDataFilter['tTypeSelect'],
            'ptBchL'        => $tBchCodeSelect,
            'ptBchF'        => $paDataFilter['tBchCodeFrom'],
            'ptBchT'        => $paDataFilter['tBchCodeTo'],
            'ptMerL'        => $tMerCodeSelect,
            'ptMerF'        => $paDataFilter['tMerCodeFrom'],
            'ptMerT'        => $paDataFilter['tMerCodeTo'],
            'ptShpL'        => $tShpCodeSelect,
            'ptShpF'        => $paDataFilter['tShpCodeFrom'],
            'ptShpT'        => $paDataFilter['tShpCodeTo'],
            'ptPosL'        => $tPosCodeSelect,
            'ptPosF'        => $paDataFilter['tPosCodeFrom'],
            'ptPosT'        => $paDataFilter['tPosCodeTo'],

            'ptRcvF'        => $paDataFilter['tRcvCodeFrom'],
            'ptRcvT'        => $paDataFilter['tRcvCodeTo'],
            'ptDocDateF'    => $paDataFilter['tDocDateFrom'],
            'ptDocDateT'    => $paDataFilter['tDocDateTo'],
            'FNResult'      => 0
        );

        $oQuery = $this->db->query($tCallStore, $aDataStore);

        if ($oQuery != FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }
    }

    /**
     * Functionality: Get Data Report
     * Parameters:  Function Parameter
     * Creator: 10/07/2019 Saharat(Golf)
     * Last Modified : 13/11/2019 Piya
     * Return : Get Data Rpt Temp
     * Return Type: Array
     */
    public function FSaMGetDataReport($paDataWhere) {

        $nPage = $paDataWhere['nPage'];
        // Call Data Pagination 
        $aPagination = $this->FMaMRPTPagination($paDataWhere);

        $nRowIDStart = $aPagination["nRowIDStart"];
        $nRowIDEnd = $aPagination["nRowIDEnd"];
        $nTotalPage = $aPagination["nTotalPage"];

        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tSession = $paDataWhere['tUsrSessionID'];
        /// ค่า Apptype
        $nApptype = $paDataWhere['paDataFilter']['nPosType'];
        
        // Set Priority
        
        $aData = $this->FMxMRPTSetPriorityGroup($tComName, $tRptCode, $tSession);
        

        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา 
        if ($nPage == $nTotalPage) {

            $tJoinFoooter = "   
                SELECT 
                    FTUsrSession AS FTUsrSession_Footer,
                    SUM(FCXrcNet) AS FCXrcNet_Footer
                FROM TRPTSalRCTmp WITH(NOLOCK)
                WHERE FTComName = '$tComName'
                AND FTRptCode = '$tRptCode'
                AND FTUsrSession = '$tSession'";
                if(!empty($nApptype)){
                 $tJoinFoooter .= " AND FNAppType='".$nApptype."' ";
                   }
                $tJoinFoooter .= "GROUP BY FTUsrSession ) T 
                ON L.FTUsrSession = T.FTUsrSession_Footer
            ";

        } else {
            // ถ้าไม่ใช่ให้ Select 0 เพื่อให้ Join ได้แต่จะไม่มีการ Sum 
            $tJoinFoooter = "   
                SELECT
                    '$tSession' AS FTUsrSession_Footer,
                    '0' AS FCXrcNet_Footer
                ) T ON  L.FTUsrSession = T.FTUsrSession_Footer
            ";
            if(!empty($nApptype)){
                $tJoinFoooter .= " AND FNAppType='".$nApptype."' ";
                  }
        }

        // L = List ข้อมูลทั้งหมด
        // A = SaleDT
        // S = Misures Summary
        $tSQL = "   
            SELECT
                L.*,
                T.FCXrcNet_Footer
            FROM (
                SELECT  
                    ROW_NUMBER() OVER(ORDER BY FTRcvCode) AS RowID,
                    A.*,
                    S.FNRptGroupMember,
                    S.FCXrcNet_SubTotal
                FROM TRPTSalRCTmp A WITH(NOLOCK)
                    /* Calculate Misures */
                LEFT JOIN (
                    SELECT
                        FTRcvCode AS FTRcvCode_SUM,
                        COUNT(FTRcvCode) AS FNRptGroupMember,
                        SUM(FCXrcNet) AS FCXrcNet_SubTotal
                    FROM TRPTSalRCTmp WITH(NOLOCK)
                    WHERE FTComName = '$tComName'
                    AND FTRptCode = '$tRptCode'
                    AND FTUsrSession = '$tSession'";
             if(!empty($nApptype)){
                  $tSQL .= " AND FNAppType='".$nApptype."' ";
                 }
                 $tSQL .= "GROUP BY FTRcvCode
                ) AS S ON A.FTRcvCode = S.FTRcvCode_SUM
                WHERE A.FTComName = '$tComName'
                AND   A.FTRptCode = '$tRptCode'
                AND   A.FTUsrSession = '$tSession'";
                if(!empty($nApptype)){
                    $tSQL .= " AND A.FNAppType='".$nApptype."' ";
                   }
             $tSQL .= " /* End Calculate Misures */
            ) AS L 
            LEFT JOIN (
                " . $tJoinFoooter . "
        ";

        // WHERE เงื่อนไข Page
        $tSQL .= " WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";
     if(!empty($nApptype)){
        $tSQL .= " AND FNAppType='".$nApptype."' ";
         }
        // AND FNAppType=1
        // สั่ง Order by ตามข้อมูลหลัก
        $tSQL .= " ORDER BY FTRcvCode , FNXshDocType , FDXrcRefDate ";



                // echo  $tSQL;
                // die();
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aData = $oQuery->result_array();
        } else {
            $aData = NULL;
        }

        $aErrorList = array(
            "nErrInvalidPage" => ""
        );

        $aResualt = array(
            "aPagination" => $aPagination,
            "aRptData" => $aData,
            "aError" => $aErrorList
        );
        unset($oQuery);
        unset($aData);
        return $aResualt;
    }
   
    /**
     * Functionality: Count Data Report All
     * Parameters: Function Parameter
     * Creator: 22/04/2019 Wasin(Yoshi)
     * Last Modified: 13/11/2019 Piya
     * Return: Data Report All
     * ReturnType: Array
     */
    public function FSaMCountDataReportAll($paDataWhere) {
        $tUserCode = $paDataWhere['tUserCode'];
        $tCompName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUserSession = $paDataWhere['tUserSession'];

        $tSQL = " 
            SELECT 
                FTRcvName  AS rtRcvName,
                FTXshDocNo AS rtRcvDocNo,
                FDCreateOn AS rtRcvCreateOn,
                FCXrcNet   AS rtRcvrcNet 
            FROM TRPTSalRCTmp  
            WHERE FTUsrSession = '$tUserSession' 
            AND FTComName = '$tCompName' 
            AND FTRptCode = '$tRptCode'
        ";

        $oQuery = $this->db->query($tSQL);
        $nCountData = $oQuery->num_rows();
        unset($oQuery);
        return $nCountData;
    }

    public function FMaMRPTPagination($paDataWhere) {

        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        $tSQL = "   
            SELECT
                RCV.FTRcvCode
            FROM TRPTSalRCTmp RCV WITH(NOLOCK)
            WHERE RCV.FTComName = '$tComName'
            AND RCV.FTRptCode = '$tRptCode'
            AND RCV.FTUsrSession = '$tUsrSession'
        ";

        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->num_rows();

        $nPage = $paDataWhere['nPage'];
        $nPerPage = $paDataWhere['nPerPage'];
        $nPrevPage = $nPage - 1;
        $nNextPage = $nPage + 1;
        $nRowIDStart = (($nPerPage * $nPage) - $nPerPage); // RowId Start
        if ($nRptAllRecord <= $nPerPage) {
            $nTotalPage = 1;
        } else if (($nRptAllRecord % $nPerPage) == 0) {
            $nTotalPage = ($nRptAllRecord / $nPerPage);
        } else {
            $nTotalPage = ($nRptAllRecord / $nPerPage) + 1;
            $nTotalPage = (int) $nTotalPage;
        }

        // get rowid end
        $nRowIDEnd = $nPerPage * $nPage;
        if ($nRowIDEnd > $nRptAllRecord) {
            $nRowIDEnd = $nRptAllRecord;
        }

        $aRptMemberDet = array(
            "nTotalRecord" => $nRptAllRecord,
            "nTotalPage" => $nTotalPage,
            "nDisplayPage" => $paDataWhere['nPage'],
            "nRowIDStart" => $nRowIDStart,
            "nRowIDEnd" => $nRowIDEnd,
            "nPrevPage" => $nPrevPage,
            "nNextPage" => $nNextPage
        );
        unset($oQuery);
        return $aRptMemberDet;
    }


    public function FMxMRPTSetPriorityGroup($ptComName, $ptRptCode, $ptUsrSession) {

        $tSQL = "
            UPDATE TRPTSalRCTmp SET 
                FNRowPartID = B.PartID
            FROM( 
                SELECT 
                    ROW_NUMBER() OVER(PARTITION BY FTRcvCode ORDER BY FTRcvCode ASC) AS PartID, 
                    FTRptRowSeq  
                FROM TRPTSalRCTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName = '$ptComName' 
                AND TMP.FTRptCode = '$ptRptCode'
                AND TMP.FTUsrSession = '$ptUsrSession' 
            ) AS B
            WHERE TRPTSalRCTmp.FTRptRowSeq = B.FTRptRowSeq 
            AND TRPTSalRCTmp.FTComName = '$ptComName' 
            AND TRPTSalRCTmp.FTRptCode = '$ptRptCode'
            AND TRPTSalRCTmp.FTUsrSession = '$ptUsrSession'
        ";
        $this->db->query($tSQL);
    }



    /**
     * Functionality: Count Data Report All
     * Parameters: Function Parameter
     * Creator: 21/08/2019 Saharat(Golf)
     * Last Modified: 13/11/2019 Piya
     * Return: Data Report All
     * ReturnType: Array
     */
    public function FSnMCountDataReportAll($paDataWhere) {

        $tUserSession = $paDataWhere['tUserSession'];
        $tCompName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];

        $tSQL = "   
            SELECT 
                DTTMP.FTRptCode
            FROM TRPTSalRCTmp AS DTTMP WITH(NOLOCK)
            WHERE FTUsrSession = '$tUserSession'
            AND FTComName = '$tCompName'
            AND FTRptCode = '$tRptCode'
         ";

        $oQuery = $this->db->query($tSQL);

        $nRptAllRecord = $oQuery->num_rows();
        unset($oQuery);
        return $nRptAllRecord;
    }


    public function FMxMRPTGetDataStat($paDataWhere) {
        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUserSession = $paDataWhere['tUserSession'];
        $tSQL = "
           
                SELECT 
                    * 
                FROM TRPTSalShiftTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName = '$tComName' 
                AND TMP.FTRptCode = '$tRptCode'
                AND TMP.FTUsrSession = '$tUserSession' ";

        $oQuery =  $this->db->query($tSQL);
        $aData = $oQuery->result_array();
        return $aData;
    }

}


