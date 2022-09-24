<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptrentamountdetail_model extends CI_Model {

    // Functionality: Call Stored Procedure
    // Parameters:  Function Parameter
    // Creator: 15/07/2019 Wasin(Yoshi)
    // Last Modified : - 
    // Return : Status Return Call Stored Procedure
    // Return Type: Array
    public function FSnMExecStoreReport($paDataFilter) {
        $tCallStore = "{ CALL SP_RPTxRentalDetail(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) }";
        $aDataStore = array(
            'pnLngID' => $paDataFilter['nLangID'],
            'pnComName' => $paDataFilter['tCompName'],
            'ptRptCode' => $paDataFilter['tRptCode'],
            'ptUsrSession' => $paDataFilter['tUsrSessionID'],
            'ptBchF' => $paDataFilter['tBchCodeFrom'],
            'ptBchT' => $paDataFilter['tBchCodeTo'],
            'ptShpF' => $paDataFilter['tShopCodeFrom'],
            'ptShpT' => $paDataFilter['tShopCodeTo'],
            'ptPosCodeF' => $paDataFilter['tPosCodeFrom'],
            'ptPosCodeT' => $paDataFilter['tPosCodeTo'],
            'ptRackCodeF' => $paDataFilter['tRackCodeFrom'],
            'ptRackCodeT' => $paDataFilter['tRackCodeTo'],
            'ptDocDateF' => $paDataFilter['tDocDateFrom'],
            'ptDocDateT' => $paDataFilter['tDocDateTo'],
            'FNResult' => $paDataFilter['FNResult']
        );
        $oQuery = $this->db->query($tCallStore, $aDataStore);

        if ($oQuery !== FALSE) {
            unset($oQuery);
            return 1;
        } else {
            unset($oQuery);
            return 0;
        }
    }

    // Functionality: Get Data Page Co
    // Parameters:  Function Parameter
    // Creator: 18/07/2019 Wasin(Yoshi)
    // Last Modified : - 
    // Return : Array Data Page Nation
    // Return Type: Array
    public function FMaMRPTPagination($paDataWhere) {
        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        $tSQL = "   SELECT
        
                                     
                                    COUNT(TRHD_TMP.FTXshDocNo) AS rnCountPage
                                FROM TRPTRTSalDTTmp TRHD_TMP WITH(NOLOCK)
                                WHERE 1=1
                                AND TRHD_TMP.FTComName    = '$tComName'
                                AND TRHD_TMP.FTRptCode    = '$tRptCode'
                                AND TRHD_TMP.FTUsrSession = '$tUsrSession'
                            ";
        $oQuery = $this->db->query($tSQL);
        $nRptAllRecord = $oQuery->row_array()['rnCountPage'];
        $nPage = $paDataWhere['nPage'];
        $nPerPage = $paDataWhere['nPerPage'];
        $nPrevPage = $nPage - 1;
        $nNextPage = $nPage + 1;
        $nRowIDStart = (($nPerPage * $nPage) - $nPerPage); //RowId Start
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
            "nNextPage" => $nNextPage,
            "nPerPage" => $paDataWhere['nPerPage'],
        );

        unset($oQuery);
        return $aRptMemberDet;
    }

    // Functionality: Get Data Page Co
    // Parameters:  Function Parameter
    // Creator: 18/07/2019 Wasin(Yoshi)
    // Last Modified : - 
    // Return : Array Data Page Nation
    // Return Type: Array
    public function FMxMRPTSetPriorityGroup($paDataWhere) {
        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        $tSQL = "   
            UPDATE TRPTPdtAdjStkTmp SET 
                FNRowPartID = B.PartID
            FROM(
                SELECT 
                    ROW_NUMBER() OVER(PARTITION BY FTAjhDocNo ORDER BY FTAjhDocNo DESC) AS PartID,
                    FTRptRowSeq 
                FROM TRPTPdtAdjStkTmp TMP WITH(NOLOCK)
                WHERE TMP.FTComName = '$tComName' 
                AND TMP.FTRptCode = '$tRptCode'
                AND TMP.FTUsrSession = '$tUsrSession'
            ) AS B
            WHERE TRPTPdtAdjStkTmp.FTRptRowSeq = B.FTRptRowSeq
            AND TRPTPdtAdjStkTmp.FTComName = '$tComName' 
            AND TRPTPdtAdjStkTmp.FTRptCode = '$tRptCode'
            AND TRPTPdtAdjStkTmp.FTUsrSession = '$tUsrSession'
        ";
        $this->db->query($tSQL);
    }

    // Functionality: Call Stored Procedure
    // Parameters:  Function Parameter
    // Creator: 18/07/2019 Wasin(Yoshi)
    // Last Modified : - 
    // Return : Status Return Call Stored Procedure
    // Return Type: Array
    public function FSaMGetDataReport($paDataWhere) {

        $nPage = $paDataWhere['nPage'];

        // Call Data Pagination 
        $aPagination = $this->FMaMRPTPagination($paDataWhere);
        $nRowIDStart = $aPagination["nRowIDStart"];
        $nRowIDEnd = $aPagination["nRowIDEnd"];
        $nTotalPage = $aPagination["nTotalPage"];
        $tComName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tUsrSession = $paDataWhere['tUsrSessionID'];

        // Set Priority
        $this->FMxMRPTSetPriorityGroup($paDataWhere);
        $tSQL = "   SELECT
                            TRPTRTSalDTTmpSplit.*
                        FROM (
                                SELECT  
                                    ROW_NUMBER() OVER(ORDER BY FTRptRowSeq) AS RowID,*
                                FROM (
                                    SELECT 
                                        FTRptRowSeq,
                                        FTPosCode,
                                        FTXshFrmLogin,
                                        FTXshDocNo,
                                        FDXshDocDate,
                                        FTRakName,
                                        FNLayNo,
                                        FTPzeName,
                                        FDXshDatePick,
                                        FTXshToLogin,
                                        FTXshStaPaid,
                                        FCXshPrePaid
                                    FROM TRPTRTSalDTTmp
                                    WHERE FTComName = '" . $tComName . "'
                                    AND FTRptCode = '" . $tRptCode . "'
                                    AND FTUsrSession = '" . $tUsrSession . "'
                                ) Base
                            ) AS TRPTRTSalDTTmpSplit";
        $tSQL .= "   WHERE TRPTRTSalDTTmpSplit.RowID > $nRowIDStart AND TRPTRTSalDTTmpSplit.RowID <= $nRowIDEnd ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aData = $oQuery->result_array();
            $tSQL = "SELECT FTPosCode,SUM(FCXshPrePaid) AS FCXshSumPrePaid,COUNT(FTPosCode) AS FNXshNumDoc
                     FROM TRPTRTSalDTTmp 
                     WHERE FTComName = '" . $tComName . "'
                            AND FTRptCode = '" . $tRptCode . "'
                            AND FTUsrSession = '" . $tUsrSession . "' GROUP BY FTPosCode ORDER BY FTPosCode";
            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                $aSumData = $oQuery->result_array();
                $tSQL = "SELECT SUM(FCXshPrePaid) AS FCXshSumAllPrePaid FROM TRPTRTSalDTTmp 
                         WHERE FTComName = '" . $tComName . "'
                            AND FTRptCode = '" . $tRptCode . "'
                            AND FTUsrSession = '" . $tUsrSession . "'";
                $oQuery = $this->db->query($tSQL);
                $aSumAllPrePaid = $oQuery->row_array()["FCXshSumAllPrePaid"];
                for ($nI = 0; $nI < count($aSumData); $nI++) {
                    $aSumData[$nI]["FCXshSumAllPrePaid"] = $aSumAllPrePaid;
                    $tSQL = "SELECT  
                                    ROW_NUMBER() OVER(ORDER BY FTRptRowSeq) AS SeqID,*
                            FROM (
                                SELECT 
                                    FTRptRowSeq
                                FROM TRPTRTSalDTTmp
                                WHERE FTPosCode = '" . $aSumData[$nI]["FTPosCode"] . "'
                                    AND FTComName = '" . $tComName . "'
                                    AND FTRptCode = '" . $tRptCode . "'
                                    AND FTUsrSession = '" . $tUsrSession . "'
                            ) AS TRPTRTSalHDTmpAll";
                    $oQuery = $this->db->query($tSQL);
                    $aSeqAllData = $oQuery->result_array();
                    for ($nJ = 0; $nJ < count($aSeqAllData); $nJ++) {
                        for ($nZ = 0; $nZ < count($aData); $nZ++) {
                            if ($aSumData[$nI]["FTPosCode"] == $aData[$nZ]["FTPosCode"]) {
                                if ($aSeqAllData[$nJ]["FTRptRowSeq"] == $aData[$nZ]["FTRptRowSeq"]) {
                                    $aData[$nZ]["FTRptSeqOfGroupPos"] = $aSeqAllData[$nJ]["SeqID"];
                                }
                            }
                        }
                    }
                }
            } else {
                $aSumData = false;
            }
        } else {
            $aData = false;
        }
        $aDataSend = array();
        if ($aData) {
            $aDataSend["aData"] = $aData;
            $aDataSend["aSumData"] = $aSumData;
        } else {
            $aDataSend = false;
        }
        $aErrorList = array(
            "nErrInvalidPage" => ""
        );

        $aResualt = array(
            "aPagination" => $aPagination,
            "aRptData" => $aDataSend,
            "aError" => $aErrorList
        );
        unset($oQuery);
        unset($aData);
        return $aResualt;
    }

    // Functionality: Get Data address
    // Parameters: Function Parameter
    // Creator: 11/04/2019 Wasin(Yoshi)
    // Last Modified: -
    // Return: Data Report All
    // ReturnType: Array
    public function FSaMCMPAddress($paData) {

        try {
            $tRefCode = $paData['tAddRef'];
            $nLngID = $paData['nLangID'];
            $tSQL = "SELECT
                        ADDL.FTAddRefCode       AS rtAddRefCode,
                        ADDL.FTAddTaxNo         AS rtAddTaxNo,
                        ADDL.FTAddVersion       AS rtAddVersion,
                        ADDL.FTAddV1No          AS rtAddV1No,
                        ADDL.FTAddV1Soi         AS rtAddV1Soi,
                        ADDL.FTAddV1Village     AS rtAddV1Village,
                        ADDL.FTAddV1Road        AS rtAddV1Road,
                        ADDL.FTAddV1SubDist     AS rtAddV1SubDist,
                        SUBDSTL.FTSudName       AS rtAddV1SudName,
                        ADDL.FTAddV1DstCode     AS rtAddV1DstCode,
                        DSTL.FTDstName          AS rtAddV1DstName,
                        ADDL.FTAddV1PvnCode     AS rtAddV1PvnCode,
                        PVNL.FTPvnName          AS rtAddV1PvnName,
                        ADDL.FTAddCountry       AS rtAddV1CntName,
                        ADDL.FTAddV1PostCode    AS rtAddV1PostCode,
                        ADDL.FTAddV2Desc1       AS rtAddV2Desc1,
                        ADDL.FTAddV2Desc2       AS rtAddV2Desc2,
                        ADDL.FTAddWebsite       AS rtAddWebsite,
                        ADDL.FTAddLongitude     AS rtAddLongitude,
                        ADDL.FTAddLatitude      AS rtAddLatitude

                    FROM [TCNMAddress_L] ADDL
                    LEFT JOIN [TCNMSubDistrict_L] SUBDSTL ON ADDL.FTAddV1SubDist = SUBDSTL.FTSudCode AND SUBDSTL.FNLngID = $nLngID
                    LEFT JOIN [TCNMDistrict_L] DSTL ON ADDL.FTAddV1DstCode = DSTL.FTDstCode AND DSTL.FNLngID = $nLngID
                    LEFT JOIN [TCNMProvince_L] PVNL ON ADDL.FTAddV1PvnCode = PVNL.FTPvnCode AND PVNL.FNLngID = $nLngID
                    WHERE 1=1  AND ADDL.FNLngID = $nLngID AND ADDL.FTAddRefCode = '$tRefCode' 
                    ";
            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                $oList = $oQuery->result();
                $aResult = array(
                    'raItems' => $oList[0],
                    'rtCode' => '1',
                    'rtDesc' => 'success',
                );
            } else {
                //No Data
                $aResult = array(
                    'rtCode' => '800',
                    'rtDesc' => 'data not found'
                );
            }
            $jResult = json_encode($aResult);
            $aResult = json_decode($jResult, true);
            return $aResult;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    // Functionality: Count Data Report All
    // Parameters: Function Parameter
    // Creator: 14/08/2019 Witsarut (Bell)
    // Last Modified: -
    // Return: Data Report All
    // ReturnType: Array
    public function FSaMCountDataReportAll($paDataWhere) {
        $tSessionID = $paDataWhere['tSessionID'];
        $tCompName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];

        $tSQL = " SELECT *
                        FROM TRPTRTSalDTTmp 
                        WHERE 1 = 1
                        AND FTUsrSession = '$tSessionID' 
                        AND FTComName = '$tCompName' 
                        AND FTRptCode = '$tRptCode' ";
        $oQuery = $this->db->query($tSQL);
        $nCountData = $oQuery->num_rows();
        unset($oQuery);
        return $nCountData;
    }

}





