<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptbankdepositbch_model extends CI_Model {

     /**
     * Functionality: Delete Temp Report
     * Parameters:  Function Parameter
     * Creator: 25/02/2020  Nonpawich 
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
    
         $tCallStore = "{ CALL SP_RPTxBnkDplTmp_Moshi(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) }";
         $aDataStore = array(
            'pnLngID'       => $nLangID,
            'ptComName'     => $tComName,
            'ptRptCode'     => $tRptCode,
            'ptUsrSession'  => $tUserSession,
            'pnFilterType'  => $paDataFilter['nFilterType'],
            //bch
            'ptBchL'        => $tBchCodeSelect,
            'ptBchF'        => $paDataFilter['tBchCodeFrom'],
            'ptBchT'        => $paDataFilter['tBchCodeTo'],
            //mer
            'ptMerL'        => $tMerCodeSelect,
            'ptMerF'        => $paDataFilter['tMerCodeFrom'],
            'ptMerT'        => $paDataFilter['tMerCodeTo'],
           
            //shop
            'ptShpL'        => $tShpCodeSelect,
            'ptShpF'        => $paDataFilter['tShpCodeFrom'],
            'ptShpT'        => $paDataFilter['tShpCodeTo'],
           
            //เลขที่ บช
            'ptAccNoF'    => $paDataFilter['tAccNameFrom'],
            'ptAccNoT'    => $paDataFilter['tAccNameTo'],

            //date
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
     * Creator: 25/02/2020  Nonpawich 
     * Return : Get Data Rpt Temp
     * Return Type: Array
     */

     //TRPTBnkDplTmp_Moshi
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
  
        
        // Set Priority
        $aData = $this->FMxMRPTSetPriorityGroup($tComName, $tRptCode, $tSession);

      
        // Check ว่าเป็นหน้าสุดท้ายหรือไม่ ถ้าเป็นหน้าสุดท้ายให้ไป Sum footer ข้อมูลมา 
        if ($nPage == $nTotalPage) {

            $tJoinFoooter = "   
                SELECT 
                    FTUsrSession AS FTUsrSession_Footer,
                    SUM(FCBddRefAmt) AS FCXrcNet_Footer
                FROM TRPTBnkDplTmp_Moshi WITH(NOLOCK)
                WHERE FTComName = '$tComName'
                AND FTRptCode = '$tRptCode'
                AND FTUsrSession = '$tSession'";
         
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
                    ROW_NUMBER() OVER(ORDER BY FTBdhDocNo ASC) AS RowID,
                    A.*,
                    S.FNRptGroupMember,
                    (SELECT SUM(FCBddRefAmt) FROM TRPTBnkDplTmp_Moshi SUMSUB WHERE SUMSUB.FTBddType='เงินสด'
                    
                    AND SUMSUB.FTComName = '$tComName'    AND SUMSUB.FTRptCode = '$tRptCode' AND SUMSUB.FTUsrSession = '$tSession'
                    ) AS Subtype1 ,
                    (SELECT SUM(FCBddRefAmt) FROM TRPTBnkDplTmp_Moshi SUMSUB WHERE SUMSUB.FTBddType='เช็ค' 
                    AND SUMSUB.FTComName = '$tComName'    AND SUMSUB.FTRptCode = '$tRptCode' AND SUMSUB.FTUsrSession = '$tSession'
                    ) AS Subtype2  ,
                    S.FCXrcNet_SubTotal
                FROM TRPTBnkDplTmp_Moshi A WITH(NOLOCK)
                    /* Calculate Misures */
                    
                LEFT JOIN (
                    SELECT
                    FTBddType AS FTBddType_SUM,
                        COUNT(FTBddType) AS FNRptGroupMember,

                        SUM(FCBddRefAmt) AS FCXrcNet_SubTotal
                        
                    FROM TRPTBnkDplTmp_Moshi WITH(NOLOCK)
                    WHERE FTComName = '$tComName'
                    AND FTRptCode = '$tRptCode'
                    AND FTUsrSession = '$tSession'";
                    
          
                 $tSQL .= "GROUP BY FTBddType
                ) AS S ON A.FTBddType = S.FTBddType_SUM
                WHERE A.FTComName = '$tComName'
                AND   A.FTRptCode = '$tRptCode'
                AND   A.FTUsrSession = '$tSession'";
           
             $tSQL .= " /* End Calculate Misures */
            ) AS L 
            LEFT JOIN (
                " . $tJoinFoooter . "
        ";

        // WHERE เงื่อนไข Page
        $tSQL .= " WHERE L.RowID > $nRowIDStart AND L.RowID <= $nRowIDEnd ";

        // AND FNAppType=1
        // สั่ง Order by ตามข้อมูลหลัก
        $tSQL .= " ORDER BY FTBdhDocNo ";

        // echo $tSQL;
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
                FCBddRefAmt   AS rtRcvrcNet 
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
            SAL.FTBdhDocNo
            FROM TRPTBnkDplTmp_Moshi SAL WITH(NOLOCK)
            WHERE SAL.FTComName = '$tComName'
            AND SAL.FTRptCode = '$tRptCode'
            AND SAL.FTUsrSession = '$tUsrSession'
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
            UPDATE TRPTBnkDplTmp_Moshi SET 
                FNRowPartID = B.PartID
            FROM( 
                SELECT 
                    ROW_NUMBER() OVER(PARTITION BY FTBdhDocNo ORDER BY FTBdhDocNo ASC) AS PartID, 
                    FTRptRowSeq  
                FROM TRPTBnkDplTmp_Moshi TMP WITH(NOLOCK)
                WHERE TMP.FTComName = '$ptComName' 
                AND TMP.FTRptCode = '$ptRptCode'
                AND TMP.FTUsrSession = '$ptUsrSession' 
            ) AS B
            WHERE TRPTBnkDplTmp_Moshi.FTRptRowSeq = B.FTRptRowSeq 
            AND TRPTBnkDplTmp_Moshi.FTComName = '$ptComName' 
            AND TRPTBnkDplTmp_Moshi.FTRptCode = '$ptRptCode'
            AND TRPTBnkDplTmp_Moshi.FTUsrSession = '$ptUsrSession'
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
            FROM TRPTBnkDplTmp_Moshi AS DTTMP WITH(NOLOCK)
            WHERE FTUsrSession = '$tUserSession'
            AND FTComName = '$tCompName'
            AND FTRptCode = '$tRptCode'
         ";

        $oQuery = $this->db->query($tSQL);

        $nRptAllRecord = $oQuery->num_rows();
        unset($oQuery);
        return $nRptAllRecord;
    }

    public function FSnMCountRowInTemp($paParams){

        $tComName    = $paParams['tCompName'];
        $tRptCode    = $paParams['tRptCode'];
        $tUsrSession = $paParams['tUserSession'];
        
        $tSQL = "   
            SELECT
                TMP.FTRptCode
            FROM TRPTBnkDplTmp_Moshi TMP WITH(NOLOCK)
            WHERE TMP.FTComName  = '$tComName'
            AND TMP.FTRptCode    = '$tRptCode'
            AND TMP.FTUsrSession = '$tUsrSession'
        ";
    
        $oQuery = $this->db->query($tSQL);
        return $oQuery->num_rows();
        
    }

}


