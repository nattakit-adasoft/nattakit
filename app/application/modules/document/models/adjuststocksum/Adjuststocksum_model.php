<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Adjuststocksum_model extends CI_Model
{
    /**
     * Functionality : Data List Product Adjust Stock HD
     * Parameters : function parameters
     * Creator :  22/05/2019 Piya
     * Last Modified : -
     * Return : Data Array
     * Return Type : Array
     */
    public function FSaMAdjStkSumDataTableList($paData)
    {
        $aRowLen = FCNaHCallLenData($paData['nRow'], $paData['nPage']);
        $nLngID = $paData['FNLngID'];
        $tSQL = "
            SELECT c.* FROM(
                SELECT  ROW_NUMBER() OVER(ORDER BY FDCreateOn DESC) AS FNRowID,* FROM
                    (SELECT DISTINCT
                        ADJSTK.FTBchCode,
                        BCHL.FTBchName,
                        ADJSTK.FTAjhDocNo,
                        CONVERT(CHAR(10), ADJSTK.FDAjhDocDate, 103) AS FDAjhDocDate,
                        CONVERT(CHAR(5), ADJSTK.FDAjhDocDate, 108) AS FTAjhDocTime,
                        ADJSTK.FTAjhStaDoc,
                        ADJSTK.FTAjhStaApv,
                        ADJSTK.FTAjhStaPrcStk,
                        ADJSTK.FTCreateBy,
                        USRL.FTUsrName AS FTCreateByName,
                        ADJSTK.FTAjhApvCode,
                        USRLAPV.FTUsrName AS FTAjhApvName,
                        ADJSTK.FDCreateOn

                    FROM [TCNTPdtAdjStkHD] ADJSTK WITH (NOLOCK)
                    LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON ADJSTK.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID 
                    LEFT JOIN TCNMUser_L USRL WITH (NOLOCK) ON ADJSTK.FTCreateBy = USRL.FTUsrCode AND USRL.FNLngID = $nLngID 
                    LEFT JOIN TCNMUser_L USRLAPV WITH (NOLOCK) ON ADJSTK.FTAjhApvCode = USRLAPV.FTUsrCode AND USRLAPV.FNLngID = $nLngID
                    
                    WHERE 1=1 
                    AND  ADJSTK.FTAjhDocType = 2
        ";


        // Check User Login Branch
        if ($this->session->userdata('tSesUsrLevel') != 'HQ') {
            $tUserLoginBchCode = $this->session->userdata('tSesUsrBchCodeMulti');
            $tSQL .= " AND ADJSTK.FTBchCode IN($tUserLoginBchCode)";
        }

        $aAdvanceSearch = $paData['aAdvanceSearch'];

        $tSearchList = isset($aAdvanceSearch['tSearchAll'])?$aAdvanceSearch['tSearchAll']:'';
        if (!empty($tSearchList)) {
            $tSQL .= " AND ((ADJSTK.FTAjhDocNo LIKE '%$tSearchList%') OR (BCHL.FTBchName LIKE '%$tSearchList%') OR (CONVERT(CHAR(10),ADJSTK.FDAjhDocDate,103) LIKE '%$tSearchList%'))";
        }
        // จากสาขา - ถึงสาขา
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo = $aAdvanceSearch['tSearchBchCodeTo'];
        if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeFrom)) {
            $tSQL .= " AND ((ADJSTK.FTBchCode BETWEEN $tSearchBchCodeFrom AND $tSearchBchCodeTo) OR (ADJSTK.FTBchCode BETWEEN $tSearchBchCodeTo AND $tSearchBchCodeFrom))";
        }

        // จากวันที่ - ถึงวันที่
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo = $aAdvanceSearch['tSearchDocDateTo'];
        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tSQL .= " AND ((ADJSTK.FDAjhDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (ADJSTK.FDAjhDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 00:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 23:59:59')))";
        }

        // สถานะเอกสาร
        $tSearchStaDoc = $aAdvanceSearch['tSearchStaDoc'];
        if(!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")){
            switch($tSearchStaDoc){
                case "1": {
                    $tSQL .= " AND (ADJSTK.FTAjhStaDoc <> '3' AND ADJSTK.FTAjhStaApv = '1')";
                    break;
                }
                case "2": {
                    $tSQL .= " AND (ADJSTK.FTAjhStaDoc <> '3' AND ISNULL(ADJSTK.FTAjhStaApv,'') = '')";
                    break;
                }
                case "3": {
                    $tSQL .= " AND ADJSTK.FTAjhStaDoc = '3'";
                    break;
                }
            }
        }

        // ค้นหาสถานะเคลื่อนไหว
        $tSearchStaDocAct = $aAdvanceSearch['tSearchStaDocAct'];
        if(!empty($tSearchStaDocAct) && ($tSearchStaDocAct != "0")){
            if($tSearchStaDocAct == 1){
                $tSQL .= " AND ADJSTK.FNAjhStaDocAct = 1";
            }else{
                $tSQL .= " AND ADJSTK.FNAjhStaDocAct = 0";
            }
        }

        // สถานะอนุมัติ
        $tSearchStaApprove = $aAdvanceSearch['tSearchStaApprove'];
        if (!empty($tSearchStaApprove) && ($tSearchStaApprove != "0")) {
            if ($tSearchStaApprove == 2) {
                $tSQL .= " AND ADJSTK.FTAjhStaApv = '$tSearchStaApprove' OR ADJSTK.FTAjhStaApv = '' ";
            } else {
                $tSQL .= " AND ADJSTK.FTAjhStaApv = '$tSearchStaApprove'";
            }
        }

        // สถานะประมวลผล
        $tSearchStaPrcStk = $aAdvanceSearch['tSearchStaPrcStk'];
        if (!empty($tSearchStaPrcStk) && ($tSearchStaPrcStk != "0")) {
            if ($tSearchStaPrcStk == 3) {
                $tSQL .= " AND ADJSTK.FTAjhStaPrcStk = '$tSearchStaPrcStk' OR ADJSTK.FTAjhStaPrcStk = '' ";
            } else {
                $tSQL .= " AND ADJSTK.FTAjhStaPrcStk = '$tSearchStaPrcStk'";
            }
        }

        $tSQL .= ") Base) AS c WHERE c.FNRowID > $aRowLen[0] AND c.FNRowID <= $aRowLen[1]";

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oList = $oQuery->result();
            $aFoundRow = $this->FSnMAdjStkSumGetPageAll($paData);
            $nFoundRow = $aFoundRow[0]->counts;
            $nPageAll = ceil($nFoundRow / $paData['nRow']); // หา Page All จำนวน Rec หาร จำนวนต่อหน้า
            $aResult = array(
                'raItems' => $oList,
                'rnAllRow' => $nFoundRow,
                'rnCurrentPage' => $paData['nPage'],
                'rnAllPage' => $nPageAll,
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        } else {
            // No Data
            $aResult = array(
                'rnAllRow' => 0,
                'rnCurrentPage' => $paData['nPage'],
                "rnAllPage" => 0,
                'rtCode' => '800',
                'rtDesc' => 'data not found',
            );
        }
        $jResult = json_encode($aResult);
        $aResult = json_decode($jResult, true);
        return $aResult;
    }

    /**
     * Functionality : All Page Of Product Adjust Stock HD
     * Parameters : function parameters
     * Creator :  12/06/2018 Piya
     * Last Modified : -
     * Return : Data Array
     * Return Type : Array
     */
    public function FSnMAdjStkSumGetPageAll($paData)
    {
        $nLngID = $paData['FNLngID'];

        $tSQL = "
            SELECT 
                COUNT (ADJSTK.FTAjhDocNo) AS counts
            FROM [TCNTPdtAdjStkHD] ADJSTK WITH (NOLOCK)
            LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON ADJSTK.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID 
            WHERE 1=1 
            AND ADJSTK.FTAjhDocType = 2
        ";

        // Check User Login Branch
        if ($this->session->userdata('tSesUsrLevel') != 'HQ') {
            $tUserLoginBchCode  = $this->session->userdata('tSesUsrBchCodeMulti');
            $tSQL .= " AND ADJSTK.FTBchCode IN($tUserLoginBchCode)";
        }

        $aAdvanceSearch = $paData['aAdvanceSearch'];
        @$tSearchList = $aAdvanceSearch['tSearchAll'];

        if (@$tSearchList != '') {
            $tSQL .= " AND ((ADJSTK.FTAjhDocNo LIKE '%$tSearchList%') OR (BCHL.FTBchName LIKE '%$tSearchList%') OR (ADJSTK.FDAjhDocDate LIKE '%$tSearchList%'))";
        }

        // จากสาขา - ถึงสาขา
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo = $aAdvanceSearch['tSearchBchCodeTo'];
        if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeFrom)) {
            $tSQL .= " AND ((ADJSTK.FTBchCode BETWEEN $tSearchBchCodeFrom AND $tSearchBchCodeTo) OR (ADJSTK.FTBchCode BETWEEN $tSearchBchCodeTo AND $tSearchBchCodeFrom))";
        }

        // จากวันที่ - ถึงวันที่
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo = $aAdvanceSearch['tSearchDocDateTo'];
        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tSQL .= " AND ((ADJSTK.FDAjhDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (ADJSTK.FDAjhDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 00:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 23:59:59')))";
        }

        // สถานะเอกสาร
        $tSearchStaDoc = $aAdvanceSearch['tSearchStaDoc'];
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            $tSQL .= " AND ADJSTK.FTAjhStaDoc = '$tSearchStaDoc'";
        }

        // สถานะอนุมัติ
        $tSearchStaApprove = $aAdvanceSearch['tSearchStaApprove'];
        if (!empty($tSearchStaApprove) && ($tSearchStaApprove != "0")) {
            if ($tSearchStaApprove == 2) {
                $tSQL .= " AND ADJSTK.FTAjhStaApv = '$tSearchStaApprove' OR ADJSTK.FTAjhStaApv = '' ";
            } else {
                $tSQL .= " AND ADJSTK.FTAjhStaApv = '$tSearchStaApprove'";
            }
        }

        // สถานะประมวลผล
        $tSearchStaPrcStk = $aAdvanceSearch['tSearchStaPrcStk'];
        if (!empty($tSearchStaPrcStk) && ($tSearchStaPrcStk != "0")) {

            if ($tSearchStaPrcStk == 3) {
                $tSQL .= " AND ADJSTK.FTAjhStaPrcStk = '$tSearchStaPrcStk' OR ADJSTK.FTAjhStaPrcStk = '' ";
            } else {
                $tSQL .= " AND ADJSTK.FTAjhStaPrcStk = '$tSearchStaPrcStk'";
            }
        }

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            return $oQuery->result();
        } else {
            // No Data
            return false;
        }
    }

    //Functionality : Function Get Count From Temp
    //Parameters : function parameters
    //Creator : 25/01/2019 Krit
    //Last Modified : -
    //Return : array
    //Return Type : array
    public function FSaMAdjStkSumGetCountDTTemp($paDataWhere)
    {
        $tSQL = "SELECT 
                        COUNT(DOCTMP.FTXthDocNo) AS counts
                    FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                    WHERE 1 = 1
                    ";

        $tAjhDocNo      = $paDataWhere['FTAjhDocNo'];
        $tXthDocKey     = $paDataWhere['FTXthDocKey'];
        $tSesSessionID  = $this->session->userdata('tSesSessionID');

        $tSQL .= " AND DOCTMP.FTXthDocNo = '$tAjhDocNo'";

        $tSQL .= " AND DOCTMP.FTXthDocKey = '$tXthDocKey'";

        $tSQL .= " AND DOCTMP.FTSessionID = '$tSesSessionID'";

        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $oDetail = $oQuery->result_array();
            $aResult = $oDetail[0]['counts'];
        } else {
            $aResult = 0;
        }

        return $aResult;
    }


    /**
     * Functionality : Function Add DT To Temp
     * Parameters : function parameters
     * Creator : 29/05/2019 Piya
     * Last Modified : -
     * Return : Status Add
     * Return Type : array
     */
    public function FSaMAdjStkSumInsertTmpToDT($paDataWhere)
    {

        // ทำการลบ ใน DT ก่อนการย้าย Tmp ไป DT
        if ($paDataWhere['FTAjhDocNo'] != '') {
            $this->db->where_in('FTAjhDocNo', $paDataWhere['FTAjhDocNo']);
            $this->db->delete('TCNTPdtAdjStkDT');
        }

        $tSQL = "INSERT TCNTPdtAdjStkDT 
                            (FTBchCode, FTAjhDocNo, FNAjdSeqNo, FTPdtCode, FTPdtName, FTPunName, FTAjdBarcode, FTPunCode,
                            FCPdtUnitFact, FTPgpChain, FTAjdPlcCode, FNAjdLayRow, FNAjdLayCol, FCAjdWahB4Adj, FCAjdSaleB4AdjC1,
                            FDAjdDateTimeC1, FCAjdUnitQtyC1, FCAjdQtyAllC1, FCAjdSaleB4AdjC2, FDAjdDateTimeC2, FCAjdUnitQtyC2, FCAjdQtyAllC2, FCAjdUnitQty,
                            FCAjdQtyAll, FCAjdQtyAllDiff, FDLastUpdOn, FTLastUpdBy, FDCreateOn, FTCreateBy) ";

        $tSQL .= "SELECT 
                            DOCTMP.FTBchCode,
                            DOCTMP.FTXthDocNo AS FTAjhDocNo,
                            ROW_NUMBER() OVER(ORDER BY DOCTMP.FNXtdSeqNo ASC) AS FNAjdSeqNo,
                            DOCTMP.FTPdtCode,
                            DOCTMP.FTXtdPdtName,
                            DOCTMP.FTPunName,
                            DOCTMP.FTXtdBarCode,
                            DOCTMP.FTPunCode,
                            DOCTMP.FCXtdFactor,
                            DOCTMP.FTPgpChain,
                            DOCTMP.FTAjdPlcCode,
                            DOCTMP.FNAjdLayRow,
                            DOCTMP.FNAjdLayCol,
                            DOCTMP.FCAjdWahB4Adj,
                            DOCTMP.FCAjdSaleB4AdjC1,
                            DOCTMP.FDAjdDateTimeC1,
                            DOCTMP.FCAjdUnitQtyC1,
                            DOCTMP.FCAjdQtyAllC1,
                            DOCTMP.FCAjdSaleB4AdjC2,
                            DOCTMP.FDAjdDateTimeC2,
                            DOCTMP.FCAjdUnitQtyC2,
                            DOCTMP.FCAjdQtyAllC2,
                            DOCTMP.FCAjdUnitQty,
                            DOCTMP.FCAjdQtyAll,
                            DOCTMP.FCAjdQtyAllDiff,
                            DOCTMP.FDLastUpdOn,
                            DOCTMP.FTLastUpdBy,
                            DOCTMP.FDCreateOn,
                            DOCTMP.FTCreateBy

                    FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                    WHERE 1 = 1
                    ";

        $tAjhDocNo      = $paDataWhere['FTAjhDocNo'];
        $tXthDocKey     = $paDataWhere['FTXthDocKey'];
        $tSesSessionID  = $this->session->userdata('tSesSessionID');


        $tSQL .= " AND DOCTMP.FTSessionID = '$tSesSessionID'";

        $tSQL .= " AND DOCTMP.FTXthDocNo = '$tAjhDocNo'";

        $tSQL .= " AND DOCTMP.FTXthDocKey = '$tXthDocKey'";

        $tSQL .= " ORDER BY DOCTMP.FNXtdSeqNo ASC";

        $oQuery = $this->db->query($tSQL);

        if ($oQuery > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Add Success.',
            );
        } else {
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Error Cannot Add.',
            );
        }
        return $aStatus;
    }

    /**
     * Functionality : Function Get Pdt From Temp
     * Parameters : function parameters
     * Creator : 22/05/2019 Piya
     * Last Modified : -
     * Return : array
     * Return Type : array
     */
    public function FSaMAdjStkSumGetDTTempListPage($paData)
    {
        $tWahCode = $paData['FTWahCode'];
        $nAdjCheckTime = $paData['nAdjCheckTime'];
        try {
            $aRowLen = FCNaHCallLenData($paData['nRow'], $paData['nPage']);
            $tSQL = "SELECT c.* FROM(
                        SELECT  ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo ASC) AS rtRowID,* FROM
                            (SELECT DOCTMP.FTBchCode,
                                    DOCTMP.FTXthDocNo,
                                    DOCTMP.FNXtdSeqNo,
                                    DOCTMP.FTXthDocKey,
                                    DOCTMP.FTPdtCode,
                                    DOCTMP.FTXtdPdtName,
                                    DOCTMP.FTPunName,
                                    DOCTMP.FTXtdBarCode,
                                    DOCTMP.FCPdtUnitFact,
                                    DOCTMP.FTPunCode,
                                    DOCTMP.FCXtdFactor,
                                    DOCTMP.FTPgpChain,
                                    DOCTMP.FTAjdPlcCode,
                                    DOCTMP.FNAjdLayRow,
                                    DOCTMP.FNAjdLayCol,
                                    CASE 
                                        WHEN ISNULL(FCAjdWahB4Adj,0) <> 0 THEN FCAjdWahB4Adj
                                        ELSE
                                    (
                                        SELECT
                                            FCStkQty
                                        FROM
                                            TCNTPdtStkBal
                                        WHERE
                                            FTBchCode = DOCTMP.FTBchCode
                                        AND FTWahCode = '$tWahCode'
                                        AND FTPdtCode = DOCTMP.FTPdtCode
                                    )
                                    END AS FCAjdWahB4Adj,
                                    DOCTMP.FCAjdSaleB4AdjC1,
                                    DOCTMP.FDAjdDateTimeC1,
                                    DOCTMP.FCAjdUnitQtyC1,
                                    DOCTMP.FCAjdQtyAllC1,
                                    DOCTMP.FCAjdSaleB4AdjC2,
                                    DOCTMP.FDAjdDateTimeC2,
                                    DOCTMP.FCAjdUnitQtyC2,
                                    DOCTMP.FCAjdQtyAllC2,
                                    DOCTMP.FDAjdDateTime,
                                    DOCTMP.FCAjdUnitQty,
                                    DOCTMP.FCAjdQtyAll,
                                    CASE 
                                        WHEN ISNULL(FCAjdQtyAllDiff,0) <> 0 THEN FCAjdQtyAllDiff
                                        ELSE
                                            -- Diff = (b-c) + (s-r)
                                    (
                                    (
                                        (
                                            ISNULL(
                                                (
                                                    SELECT
                                                        FCStkQty
                                                    FROM
                                                        TCNTPdtStkBal
                                                    WHERE
                                                        FTBchCode = DOCTMP.FTBchCode
                                                    AND FTWahCode = '$tWahCode'
                                                    AND FTPdtCode = DOCTMP.FTPdtCode
                                                ),
                                                0
                                            ) --ก่อนปรับ
                                        - 
                                        ISNULL(DOCTMP.FCAjdQtyAll$nAdjCheckTime, 0) --ยอดนับ
                                        ) 
                                            +
                                            ISNULL(
                                                (
                                                    SELECT
                                                        SUM (
                                                            CASE
                                                            WHEN FTStkType = 3 THEN
                                                                FCStkQty
                                                            ELSE
                                                                FCStkQty * (- 1)
                                                            END
                                                        ) AS FCStkQty
                                                    FROM
                                                        TCNTPdtStkCrd
                                                    WHERE
                                                        FTBchCode = DOCTMP.FTBchCode
                                                    AND FTWahCode = ''
                                                    AND FTPdtCode = DOCTMP.FTPdtCode
                                                    AND FDStkDate >= DOCTMP.FDAjdDateTime$nAdjCheckTime
                                                    AND FDStkDate <= GETDATE()
                                                    AND FTStkType NOT IN (1, 2, 5)
                                                    GROUP BY
                                                        FTBchCode,
                                                        FTWahCode,
                                                        FTPdtCode
                                                ),
                                                0
                                            ) --ขายหักคืน

                                      ) * (-1)
                                      )
                                    END
                                    AS FCAjdQtyAllDiff,
                                    DOCTMP.FDLastUpdOn,
                                    DOCTMP.FDCreateOn,
                                    DOCTMP.FTLastUpdBy,
                                    DOCTMP.FTCreateBy

                                FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                                WHERE 1 = 1";

            $tAjhDocNo      = $paData['FTAjhDocNo'];
            $tXthDocKey     = $paData['FTXthDocKey'];
            $tSesSessionID  = $this->session->userdata('tSesSessionID');

            $tSQL .= " AND DOCTMP.FTXthDocNo = '$tAjhDocNo'";

            $tSQL .= " AND DOCTMP.FTXthDocKey = '$tXthDocKey'";

            $tSQL .= " AND DOCTMP.FTSessionID = '$tSesSessionID'";

            $tSearchList = $paData['tSearchAll'];

            if ($tSearchList != '') {
                $tSQL .= " AND ( DOCTMP.FTPdtCode LIKE '%$tSearchList%'";
                $tSQL .= " OR DOCTMP.FTXtdPdtName LIKE '%$tSearchList%' ";
                $tSQL .= " OR DOCTMP.FTXtdBarCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR DOCTMP.FTAjdPlcCode LIKE '%$tSearchList%' )";
            }

            $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";

            $oQuery = $this->db->query($tSQL);
            // echo $this->db->last_query();
            // die();
            if ($oQuery->num_rows() > 0) {
                $aList          = $oQuery->result_array();
                $oFoundRow      = $this->FSoMAdjStkSumGetDTTempListPageAll($paData);
                $nFoundRow      = $oFoundRow[0]->counts;
                $nPageAll       = ceil($nFoundRow / $paData['nRow']); //หา Page All จำนวน Rec หาร จำนวนต่อหน้า
                $aResult        = array(
                    'raItems'           => $aList,
                    'rnAllRow'          => $nFoundRow,
                    'rnCurrentPage'     => $paData['nPage'],
                    'rnAllPage'         => $nPageAll,
                    'rtCode'            => '1',
                    'rtDesc'            => 'success',
                );
            } else {
                //No Data
                $aResult = array(
                    'rnAllRow' => 0,
                    'rnCurrentPage' => $paData['nPage'],
                    "rnAllPage" => 0,
                    'rtCode' => '800',
                    'rtDesc' => 'data not found',
                );
            }

            return $aResult;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    /**
     * Functionality : Function Get Pdt From Temp
     * Parameters : function parameters
     * Creator : 22/05/2019 Piya
     * Last Modified : -
     * Return : array
     * Return Type : array
     */
    public function FSnMAdjStkSumFine4BalanceDTTemp($paData)
    {

        try {

            $tWahCode = $paData['tWahCode'];
            $tPdtCode = $paData['FTPdtCode'];
            $tPunCode = $paData['FTPunCode'];
            $FCAjdUnitQty = $paData['FCAjdUnitQty'];

            $nAdjCheckTime = '';

            $tSQL = "SELECT 
                              (
                                    SELECT
                                        FCStkQty
                                    FROM
                                        TCNTPdtStkBal
                                    WHERE
                                        FTBchCode = DOCTMP.FTBchCode
                                    AND FTWahCode = '$tWahCode'
                                    AND FTPdtCode = DOCTMP.FTPdtCode
                                ) AS FCAjdWahB4Adj,
                              -- Diff = (b-c) + (s-r)
                                  (
                                    (
                                        (
                                            ISNULL(
                                                (
                                                    SELECT
                                                        FCStkQty
                                                    FROM
                                                        TCNTPdtStkBal
                                                    WHERE
                                                        FTBchCode = DOCTMP.FTBchCode
                                                    AND FTWahCode = '$tWahCode'
                                                    AND FTPdtCode = DOCTMP.FTPdtCode
                                                ),
                                                0
                                            ) --ก่อนปรับ
                                        - 
                                        ( ISNULL($FCAjdUnitQty,0) * ISNULL(DOCTMP.FCPdtUnitFact,0) ) --ยอดนับ
                                        ) 
                                            +
                                            ISNULL(
                                                (
                                                    SELECT
                                                        SUM (
                                                            CASE
                                                            WHEN FTStkType = 3 THEN
                                                                FCStkQty
                                                            ELSE
                                                                FCStkQty * (- 1)
                                                            END
                                                        ) AS FCStkQty
                                                    FROM
                                                        TCNTPdtStkCrd
                                                    WHERE
                                                        FTBchCode = DOCTMP.FTBchCode
                                                    AND FTWahCode = ''
                                                    AND FTPdtCode = DOCTMP.FTPdtCode
                                                    AND FDStkDate >= DOCTMP.FDAjdDateTime$nAdjCheckTime
                                                    AND FDStkDate <= GETDATE()
                                                    AND FTStkType NOT IN (1, 2, 5)
                                                    GROUP BY
                                                        FTBchCode,
                                                        FTWahCode,
                                                        FTPdtCode
                                                ),
                                                0
                                            ) --ขายหักคืน

                                      ) * (-1)
                                      )
                                      AS FCAjdQtyAllDiff

                            FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                            WHERE 1 = 1";

            $tAjhDocNo      = $paData['FTAjhDocNo'];
            $tXthDocKey     = $paData['FTXthDocKey'];
            $tSesSessionID  = $paData['FTSessionID'];

            $tSQL .= " AND DOCTMP.FTXthDocNo = '$tAjhDocNo'";

            $tSQL .= " AND DOCTMP.FTXthDocKey = '$tXthDocKey'";

            $tSQL .= " AND DOCTMP.FTSessionID = '$tSesSessionID'";

            $tSQL .= " AND DOCTMP.FTPdtCode = '$tPdtCode'";
            $tSQL .= " AND DOCTMP.FTPunCode = '$tPunCode'";


            $oQuery = $this->db->query($tSQL);
            // echo $this->db->last_query();
            // die();
            if ($oQuery->num_rows() > 0) {
                $aList          = $oQuery->result_array()[0];

                $aResult        = array(
                    'raItems'           => $aList,
                    'rtCode'            => '1',
                    'rtDesc'            => 'success',
                );
            } else {
                //No Data
                $aResult = array(
                    'rtCode' => '800',
                    'rtDesc' => 'data not found',
                );
            }

            return $aResult;
        } catch (Exception $Error) {
            echo $Error;
        }
    }

    //Functionality : All Page Of Product Size
    //Parameters : function parameters
    //Creator :  25/02/2019 Napat(Jame)
    //Return : object Count All Product Model
    //Return Type : Object
    public function FSoMAdjStkSumGetDTTempListPageAll($paData)
    {
        try {

            $tSQL = "SELECT COUNT (DOCTMP.FTXthDocNo) AS counts
                     FROM TCNTDocDTTmp DOCTMP
                     WHERE 1 = 1";

            $tAjhDocNo      = $paData['FTAjhDocNo'];
            $tXthDocKey     = $paData['FTXthDocKey'];
            $tSesSessionID  = $this->session->userdata('tSesSessionID');

            $tSQL .= " AND DOCTMP.FTXthDocNo = '$tAjhDocNo'";

            $tSQL .= " AND DOCTMP.FTXthDocKey = '$tXthDocKey'";

            $tSQL .= " AND DOCTMP.FTSessionID = '$tSesSessionID'";

            $tSearchList = $paData['tSearchAll'];

            if ($tSearchList != '') {
                $tSQL .= " AND ( DOCTMP.FTPdtCode LIKE '%$tSearchList%'";
                $tSQL .= " OR DOCTMP.FTXtdPdtName LIKE '%$tSearchList%' ";
                $tSQL .= " OR DOCTMP.FTXtdBarCode LIKE '%$tSearchList%' ";
                $tSQL .= " OR DOCTMP.FTAjdPlcCode LIKE '%$tSearchList%' )";
            }

            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                return $oQuery->result();
            } else {
                return false;
            }
        } catch (Exception $Error) {
            echo $Error;
        }
    }




    function FSnMAdjStkSumUpdateInlineDTTemp($aDataUpd, $aDataWhere)
    {

        try {
            $nOptDecimalShow = FCNxHGetOptionDecimalShow();

            $this->db->set('FCAjdUnitQty', $aDataUpd['FCAjdUnitQty']);
            $this->db->set('FCAjdQtyAll', $aDataUpd['FCAjdUnitQty'] * $aDataUpd['cUnitfact']);
            $this->db->set('FDAjdDateTime', $aDataUpd['FDAjdDateTime']);
            $this->db->set('FDLastUpdOn', $aDataUpd['FDLastUpdOn']);
            $this->db->set('FTLastUpdBy', $aDataUpd['FTLastUpdBy']);
            $this->db->where('FTSessionID', $aDataWhere['FTSessionID']);
            $this->db->where('FTXthDocNo', $aDataWhere['FTAjhDocNo']);
            $this->db->where('FNXtdSeqNo', $aDataWhere['tSeq']);
            $this->db->where('FTXthDocKey', $aDataWhere['FTXthDocKey']);
            $this->db->update('TCNTDocDTTmp');

            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'OK',
                    'rcAjdUnitQty' => number_format($aDataUpd['FCAjdUnitQty'], $nOptDecimalShow),
                    'rdAjdDateC3' => date('d/m/Y', strtotime($aDataUpd['FDAjdDateTime'])),
                    'rdAjdTimeC3' => date('H:i', strtotime($aDataUpd['FDAjdDateTime'])),
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Not Update',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }


    //Functionality : Function Add DT To Temp
    //Parameters : function parameters
    //Creator : 25/01/2019 Krit
    //Last Modified : -
    //Return : Status Add
    //Return Type : array
    public function FSaMAdjStkSumInsertPDTToTemp($paDataWhere)
    {
        try {

            $TSqlDel = " DELETE FROM TCNTDocDTTmp WHERE  FTXthDocNo='" . $paDataWhere['FTAjhDocNo'] . "'  AND  FTXthDocKey = '" . $paDataWhere['FTXthDocKey'] . "' AND  FTSessionID = '" . $paDataWhere['FTSessionID'] . "' ";
            $this->db->query($TSqlDel);

            $tSql = "INSERT INTO TCNTDocDTTmp (
                        FTBchCode,
                        FTXthDocNo,
                        FTXthDocKey,
                        FTSessionID,
                        FNXtdSeqNo,
                        FTPdtCode,
                        FTXtdPdtName,
                        FTPunCode,
                        FTPunName,
                        FTXtdBarCode,
                        FCPdtUnitFact,
                        FTPgpChain,
                        FTAjdPlcCode,
                        FDAjdDateTimeC1,
                        FCAjdUnitQtyC1,
                        FCAjdQtyAllC1,
                        FDAjdDateTimeC2,
                        FCAjdUnitQtyC2,
                        FCAjdQtyAllC2
                    ) SELECT
                        '" . $paDataWhere['FTBchCode'] . "',
                        '" . $paDataWhere['FTAjhDocNo'] . "',
                        '" . $paDataWhere['FTXthDocKey'] . "',
                        '" . $paDataWhere['FTSessionID'] . "',
                        ROW_NUMBER () OVER (ORDER BY FTPdtCode,FTPunCode ASC) AS FNXtdSeqNo,
                        DT.FTPdtCode,
                        DT.FTPdtName,
                        DT.FTPunCode,
                        DT.FTPunName,
                        DT.FTAjdBarcode,
                        DT.FCPdtUnitFact,
                        DT.FTPgpChain,
                        DT.FTAjdPlcCode,
                        --SUM(DT.FCAjdSaleB4AdjC1) AS FCAjdSaleB4AdjC1,
                        MAX (DT.FDAjdDateTimeC1) AS FDAjdDateTimeC1,
                        SUM (DT.FCAjdUnitQtyC1) AS FCAjdUnitQtyC1,
                        SUM (DT.FCAjdQtyAllC1) AS FCAjdQtyAllC1,
                        --SUM(DT.FCAjdSaleB4AdjC2) AS FCAjdSaleB4AdjC2,
                        MAX (DT.FDAjdDateTimeC2) AS FDAjdDateTimeC2,
                        SUM (DT.FCAjdUnitQtyC2) AS FCAjdUnitQtyC2,
                        SUM (DT.FCAjdQtyAllC2) AS FCAjdQtyAllC2 
                        --DT.FCAjdWahB4Adj,
                        --DT.FCAjdUnitQty,
                        --DT.FDAjdDateTime,
                        --DT.FCAjdQtyAll,
                        --DT.FCAjdQtyAllDiff
                    FROM
                        TCNTPdtAdjStkDT DT
                    LEFT JOIN TCNTPdtAdjStkHD HD ON DT.FTBchCode = HD.FTBchCode
                    AND DT.FTAjhDocNo = HD.FTAjhDocNo
                    WHERE
                        1 = 1
                    AND HD.FTAjhDocType = 1
                    AND ISNULL(HD.FTAjhDocRef,'') = ''
                    AND HD.FTAjhStaApv = 1
                    AND HD.FTAjhBchTo = '" . $paDataWhere['FTBchCode'] . "'
                    AND HD.FTAjhWhTo = '" . $paDataWhere['FTWahCode'] . "'
                    GROUP BY
                        DT.FTPdtName,
                        DT.FTPunName,
                        DT.FTPdtCode,
                        DT.FTPunCode,
                        DT.FTAjdBarcode,
                        DT.FCPdtUnitFact,
                        DT.FTPgpChain,
                        DT.FTAjdPlcCode
            
            ";

            $oQuery = $this->db->query($tSql);

            // echo $this->db->last_query();
            // die();

            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Add Success.',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Error Cannot Add.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    public function FSaMAdjStkSumFindDocRef($paDataWhere)
    {

        try {
            $tSQl = "SELECT  
                        HD.FTAjhDocNo
                        FROM 
                        TCNTPdtAdjStkHD HD 
                        WHERE
                            1 = 1
                        AND HD.FTAjhDocType = 1
                        AND ISNULL(HD.FTAjhDocRef,'') = ''
                        AND HD.FTAjhStaApv = 1
                        AND HD.FTAjhBchTo = '" . $paDataWhere['FTBchCode'] . "'
                        AND HD.FTAjhWhTo = '" . $paDataWhere['FTWahCode'] . "' ";

            $oQuery = $this->db->query($tSQl);
            if ($oQuery->num_rows() > 0) {
                return $oQuery->result();
            } else {
                return false;
            }
        } catch (Exception $Error) {
            return $Error;
        }
    }


    public function FSaMAdjStkSumAddUpdateDocNoInDocTemp($aDataWhere)
    {

        try {

            $this->db->set('FTXthDocNo', $aDataWhere['FTAjhDocNo']);
            $this->db->set('FTBchCode', $aDataWhere['FTBchCode']);
            $this->db->where('FTXthDocNo', '');
            $this->db->where('FTSessionID', $this->session->userdata('tSesSessionID'));
            $this->db->where('FTXthDocKey', $aDataWhere['FTXthDocKey']);
            $this->db->update('TCNTDocDTTmp');

            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'OK',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Not Update',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }




    //Function : Cancel Doc
    public function FSvMAdjStkSumCancel($paDataUpdate)
    {
        try {
            //DT Dis
            $this->db->set('FTAjhStaDoc', 3);
            $this->db->where('FTAjhDocNo', $paDataUpdate['FTAjhDocNo']);
            $this->db->update('TCNTPdtAdjStkHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'OK',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Not Approve',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }


    //Function : Approve Doc
    public function FSvMAdjStkSumApprove($paDataUpdate)
    {
        try {
            //DT Dis
            $this->db->set('FTAjhStaPrcStk', 2);
            $this->db->set('FTAjhStaApv', 2);
            $this->db->set('FTAjhApvCode', $paDataUpdate['FTXthApvCode']);
            $this->db->where('FTAjhDocNo', $paDataUpdate['FTAjhDocNo']);

            $this->db->update('TCNTPdtAdjStkHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'OK',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Not Approve',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }


    /**
     * Functionality : Search AdjStkSum By ID
     * Parameters : function parameters
     * Creator : 22/05/2019 Piya
     * Last Modified : -
     * Return : data
     * Return Type : Array
     */
    public function FSaMAdjStkSumGetHD($paData)
    {

        $tAjhDocNo  = $paData['FTAjhDocNo'];
        $nLngID     = $paData['FNLngID'];

        $tSQL = "SELECT
                    ADJSTK.FTBchCode,
                    ADJSTK.FTAjhDocNo,
                    ADJSTK.FTAjhDocType,
                    CONVERT(CHAR(5), ADJSTK.FDAjhDocDate, 108) AS FTAjhDocTime,
                    ADJSTK.FDAjhDocDate,
                    ADJSTK.FTAjhBchTo,
     
                    ADJSTK.FTAjhShopTo,
                    ADJSTK.FTAjhPosTo,
                    POSLTO.FTPosName,
                    ADJSTK.FTAjhWhTo,
                    ADJSTK.FTAjhPlcCode,
                    ADJSTK.FTDptCode,
                    ADJSTK.FTUsrCode,
                    ADJSTK.FTRsnCode,
                    ADJSTK.FTAjhRmk,
                    ADJSTK.FNAjhDocPrint,
                    ADJSTK.FTAjhApvSeqChk,
                    ADJSTK.FTAjhApvCode,
                    ADJSTK.FTAjhStaApv,
                    ADJSTK.FTAjhStaPrcStk,
                    ADJSTK.FTAjhStaDoc,
                    ADJSTK.FNAjhStaDocAct,
                    ADJSTK.FTAjhDocRef,
                    /*ADJSTK.FTAjhStaDelMQ,*/
                    ADJSTK.FDLastUpdOn,
                    ADJSTK.FTLastUpdBy,
                    ADJSTK.FDCreateOn,
                    ADJSTK.FTCreateBy,
                    RSNL.FTRsnName,
                    BCHLDOC.FTBchName,
                    BCHLTO.FTBchName AS FTAjhBchNameTo,
                    USRLCREATE.FTUsrName AS FTCreateByName,
                    USRLKEY.FTUsrName AS FTUsrName,
                    USRAPV.FTUsrName AS FTAjhStaApvName,
                    WAHLTO.FTWahName AS FTAjhWhNameTo
                    
                FROM [TCNTPdtAdjStkHD] ADJSTK

                LEFT JOIN TCNMBranch_L      BCHLDOC ON ADJSTK.FTBchCode = BCHLDOC.FTBchCode AND BCHLDOC.FNLngID = $nLngID
                LEFT JOIN TCNMBranch_L      BCHLTO ON ADJSTK.FTAjhBchTo = BCHLTO.FTBchCode AND BCHLTO.FNLngID = $nLngID
                LEFT JOIN TCNMUser_L        USRLCREATE ON ADJSTK.FTCreateBy = USRLCREATE.FTUsrCode AND USRLCREATE.FNLngID = $nLngID
                LEFT JOIN TCNMUser_L        USRLKEY ON ADJSTK.FTUsrCode = USRLKEY.FTUsrCode AND USRLKEY.FNLngID = $nLngID
                LEFT JOIN TCNMUser_L        USRAPV ON ADJSTK.FTAjhApvCode = USRAPV.FTUsrCode AND USRAPV.FNLngID = $nLngID
                LEFT JOIN TCNMWaHouse_L     WAHLTO ON ADJSTK.FTAjhWhTo = WAHLTO.FTWahCode AND ADJSTK.FTAjhBchTo = WAHLTO.FTBchCode AND WAHLTO.FNLngID = $nLngID
                LEFT JOIN TCNMRsn_L         RSNL ON ADJSTK.FTRsnCode = RSNL.FTRsnCode AND RSNL.FNLngID = 1
                LEFT JOIN TCNMPos_L         POSLTO      ON ADJSTK.FTAjhPosTo = POSLTO.FTPosCode AND ADJSTK.FTAjhBchTo = POSLTO.FTBchCode AND POSLTO.FNLngID = $nLngID
                WHERE 1=1 ";

        if ($tAjhDocNo != "") {
            $tSQL .= "AND ADJSTK.FTAjhDocNo = '$tAjhDocNo'";
        }

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oDetail = $oQuery->result();

            $aResult = array(
                'raItems'   => $oDetail[0],
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        } else {
            // Not Found
            $aResult = array(
                'rtCode' => '800',
                'rtDesc' => 'data not found.',
            );
        }
        $jResult = json_encode($aResult);
        $aResult = json_decode($jResult, true);
        return $aResult;
    }


    //Functionality : Function Add DT To Temp
    //Parameters : function parameters
    //Creator : 03/04/2019 Krit(Copter)
    //Last Modified : -
    //Return : Status Add
    //Return Type : array
    public function FSaMAdjStkSumInsertDTToTemp($paDataWhere)
    {

        $tAjhDocNo   = $paDataWhere['FTAjhDocNo'];
        $tBchCode    = $paDataWhere['tBchCode'];
        $FNLngID     = $paDataWhere['FNLngID'];
        $tSessionID  = $paDataWhere['tSessionID'];
        $tSesUsername = $this->session->userdata('tSesUsername');
        $tSql = "INSERT INTO TCNTDocDTTmp (
            FTBchCode,
            FTXthDocNo,
            FNXtdSeqNo,
            FTPdtCode,
            FTXtdPdtName,
            FTPunName,
            FTXtdBarCode,
            FTPunCode,
            FCPdtUnitFact,
            FTPgpChain,
            FTAjdPlcCode,
            FCAjdSaleB4AdjC1,
            FDAjdDateTimeC1,
            FCAjdUnitQtyC1,
            FCAjdQtyAllC1,
            FCAjdSaleB4AdjC2,
            FDAjdDateTimeC2,
            FCAjdUnitQtyC2,
            FCAjdQtyAllC2,
            FCAjdUnitQty,
            FDAjdDateTime,
            FCAjdQtyAll,
            FCAjdWahB4Adj,
            FCAjdQtyAllDiff,
            FNAjdLayRow, 
            FNAjdLayCol,
            FDLastUpdOn,
            FTLastUpdBy,
            FDCreateOn,
            FTCreateBy,
            FTSessionID,
            FTXthDocKey
            )
            SELECT
                ADJSTK.FTBchCode,
                ADJSTK.FTAjhDocNo,
                ADJSTK.FNAjdSeqNo,
                ADJSTK.FTPdtCode,
                ADJSTK.FTPdtName,
                ADJSTK.FTPunName,
                ADJSTK.FTAjdBarcode,
                ADJSTK.FTPunCode,
                ADJSTK.FCPdtUnitFact,
                ADJSTK.FTPgpChain,
                ADJSTK.FTAjdPlcCode,
                ADJSTK.FCAjdSaleB4AdjC1,
                ADJSTK.FDAjdDateTimeC1,
                ADJSTK.FCAjdUnitQtyC1,
                ADJSTK.FCAjdQtyAllC1,
                ADJSTK.FCAjdSaleB4AdjC2,
                ADJSTK.FDAjdDateTimeC2,
                ADJSTK.FCAjdUnitQtyC2,
                ADJSTK.FCAjdQtyAllC2,
                ADJSTK.FCAjdUnitQty,
                ADJSTK.FDAjdDateTime,
                ADJSTK.FCAjdQtyAll,
                ADJSTK.FCAjdWahB4Adj,
                ADJSTK.FCAjdQtyAllDiff,
                0,
                0,
                GETDATE(),
                '$tSesUsername',
                GETDATE(),
                '$tSesUsername',
                '$tSessionID',
                'TCNTPdtAdjStkHD'
            FROM
                TCNTPdtAdjStkDT ADJSTK WITH (NOLOCK)
            WHERE 1=1
            AND ADJSTK.FTAjhDocNo = '$tAjhDocNo' ";

        $this->db->query($tSql);
        if ($this->db->affected_rows() > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Add Success.',
            );
        } else {
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Error Cannot Add.',
            );
        }
    }





    public function FSxMClearPdtInTmp()
    {
        $tSQL = "DELETE FROM TCNTDocDTTmp WHERE FTSessionID = '" . $this->session->userdata('tSesSessionID') . "' AND FTXthDocKey = 'TCNTPdtAdjStkHD'";
        $this->db->query($tSQL);
    }

    //Functionality : Function Add/Update HD
    //Parameters : function parameters
    //Creator : 17/07/2020 nale
    //Last Modified : -
    //Return : Status Add/Update Master
    //Return Type : array
    public function FSaMAjhAddUpdateHD($paData)
    {
        //Add Master
        $nCounRow = $this->db
        ->where('FTAjhDocNo', $paData['FTAjhDocNo'])
        ->where('FTBchCode', $paData['FTBchCode'])
        ->get('TCNTPdtAdjStkHD')->num_rows();

        if ($nCounRow == 0) {
            $this->db->insert('TCNTPdtAdjStkHD', $paData);
        } else {
            $this->db->set('FTAjhBchTo', $paData['FTAjhBchTo']);
            $this->db->set('FTAjhWhTo', $paData['FTAjhWhTo']);
            $this->db->set('FTAjhApvSeqChk', $paData['FTAjhApvSeqChk']);
            $this->db->set('FTRsnCode', $paData['FTRsnCode']);
            $this->db->set('FNAjhStaDocAct', 1);
            $this->db->set('FTAjhRmk', $paData['FTAjhRmk']);
            $this->db->set('FTAjhPosTo', $paData['FTAjhPosTo']);
            $this->db->set('FDLastUpdOn', $paData['FDLastUpdOn']);
            $this->db->set('FTLastUpdBy', $paData['FTLastUpdBy']);
            $this->db->where('FTAjhDocNo', $paData['FTAjhDocNo'])->where('FTBchCode', $paData['FTBchCode']);
            $this->db->update('TCNTPdtAdjStkHD');
        }

        if ($this->db->affected_rows() > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Add Master Success',
            );
        } else {
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Error Cannot Add/Edit Master.',
            );
        }
        return $aStatus;
    }

    //Functionality : Function Add/Update OrdDT
    //Parameters : function parameters
    //Creator : 03/08/2018 Krit(Copter)
    //Last Modified : -
    //Return : Status Add/Update Master
    //Return Type : array
    public function FSaMAjhAddUpdateDT($paData)
    {
        //Add Master
        $tSesSessionID = $this->session->userdata('tSesSessionID');
        $tDocNoForm  = $this->input->post('oetAdjStkSumAjhDocNo');

        $tAjhDocNo = $paData['FTAjhDocNo'];
        $tBchCode  = $paData['FTBchCode'];

        $this->db
        ->where('FTBchCode', $tBchCode)
        ->where('FTAjhDocNo', $tAjhDocNo)
        ->delete('TCNTPdtAdjStkDT');

        $tSql = "INSERT INTO TCNTPdtAdjStkDT (
            FTBchCode,
            FTAjhDocNo,
            FNAjdSeqNo,
            FTPdtCode,
            FTPdtName,
            FTPunName,
            FTAjdBarcode,
            FTPunCode,
            FCPdtUnitFact,
            FTPgpChain,
            FTAjdPlcCode,
            FCAjdSaleB4AdjC1,
            FDAjdDateTimeC1,
            FCAjdUnitQtyC1,
            FCAjdQtyAllC1,
            FCAjdSaleB4AdjC2,
            FDAjdDateTimeC2,
            FCAjdUnitQtyC2,
            FCAjdQtyAllC2,
            FCAjdUnitQty,
            FDAjdDateTime,
            FCAjdQtyAll,--FCAjdWahB4Adj,--FCAjdQtyAllDiff,
            FNAjdLayRow, 
            FNAjdLayCol,
            FDLastUpdOn,
            FTLastUpdBy,
            FDCreateOn,
            FTCreateBy
            )
            SELECT
                '$tBchCode' AS FTBchCode,
                '$tAjhDocNo' AS FTAjhDocNo,
                ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo ASC) AS FNXtdSeqNo , 
                DOCTMP.FTPdtCode,
                DOCTMP.FTXtdPdtName,
                DOCTMP.FTPunName,
                DOCTMP.FTXtdBarCode,
                DOCTMP.FTPunCode,
                DOCTMP.FCPdtUnitFact,
                DOCTMP.FTPgpChain,
                DOCTMP.FTAjdPlcCode,
                DOCTMP.FCAjdSaleB4AdjC1,
                DOCTMP.FDAjdDateTimeC1,
                DOCTMP.FCAjdUnitQtyC1,
                DOCTMP.FCAjdQtyAllC1,
                DOCTMP.FCAjdSaleB4AdjC2,
                DOCTMP.FDAjdDateTimeC2,
                DOCTMP.FCAjdUnitQtyC2,
                DOCTMP.FCAjdQtyAllC2,
                DOCTMP.FCAjdUnitQty,
                DOCTMP.FDAjdDateTime,
                DOCTMP.FCAjdQtyAll,
                0,
                0,
                GETDATE(),
                '00001',
                GETDATE(),
                '00001'
            FROM
                TCNTDocDTTmp DOCTMP WITH (NOLOCK)
            WHERE 1=1
            AND DOCTMP.FTXthDocKey = 'TCNTPdtAdjStkHD'
            AND DOCTMP.FTSessionID = '$tSesSessionID'
            AND DOCTMP.FTXthDocNo = '$tDocNoForm'";

        $this->db->query($tSql);

        if ($this->db->affected_rows() > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Add Master Success',
            );
        } else {
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'Error Cannot Add/Edit Master.',
            );
        }
    }

    //Functionality : Delete PurchaseOrder
    //Parameters : function parameters
    //Creator : 29/08/2018 Krit(Copter)
    //Last Modified : -
    //Return : Array Status Delete
    //Return Type : array
    public function FSnMAdjStkSumDel($paData)
    {
        try {
            $this->db->trans_begin();

            $this->db->where_in('FTAjhDocNo', $paData['FTAjhDocNo']);
            $this->db->delete('TCNTPdtAdjStkHD');

            /*$this->db->where_in('FTXthDocNo', $paData['FTAjhDocNo']);
            $this->db->delete('TCNTPdtTwxHDRef');*/

            $this->db->where_in('FTAjhDocNo', $paData['FTAjhDocNo']);
            $this->db->delete('TCNTPdtAdjStkDT');

            $this->db->where_in('FTXthDocNo', $paData['FTAjhDocNo']);
            $this->db->delete('TCNTDocDTTmp');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Cannot Delete Item.',
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Complete.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    //Functionality : Delete PurchaseOrder
    //Parameters : function parameters
    //Creator : 04/04/2019 Krit(Copter)
    //Last Modified : -
    //Return : Array Status Delete
    //Return Type : array
    public function FSnMAdjStkSumDelDTTmp($paData)
    {
        try {
            $this->db->trans_begin();

            $this->db->where_in('FTXthDocNo', $paData['FTAjhDocNo']);
            $this->db->where_in('FNXtdSeqNo', $paData['FNXtdSeqNo']);
            $this->db->where_in('FTPdtCode',  $paData['FTPdtCode']);
            $this->db->where_in('FTSessionID', $paData['FTSessionID']);
            $this->db->delete('TCNTDocDTTmp');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Cannot Delete Item.',
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Complete.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    //Functionality : Multi Pdt Del Temp
    //Parameters : function parameters
    //Creator : 25/03/2019 Krit(Copter)
    //Return : Status Delete
    //Return Type : array
    public function FSaMAdjStkSumPdtTmpMultiDel($paData)
    {
        try {
            $this->db->trans_begin();

            //Del DTTmp
            $this->db->where('FTXthDocNo', $paData['FTAjhDocNo']);
            $this->db->where('FNXtdSeqNo', $paData['FNXtdSeqNo']);
            $this->db->where('FTXthDocKey', $paData['FTXthDocKey']);
            $this->db->delete('TCNTDocDTTmp');

            //Del DTDisTmp
            /*$this->db->where('FTXthDocNo', $paData['FTAjhDocNo']);
            $this->db->where('FNXtdSeqNo', $paData['FNXtdSeqNo']);
            $this->db->delete('TCNTDocDTDisTmp');*/

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Cannot Delete Item.',
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Complete.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    public function FSnMAdjStkSumGetDocType($ptTableName)
    {

        $tSQL = "SELECT FNSdtDocType FROM TSysDocType WHERE FTSdtTblName='$ptTableName'";

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oDetail = $oQuery->result();
            $nDetail = $oDetail[0]->FNSdtDocType;
        } else {
            $nDetail = '';
        }

        return $nDetail;
    }

    /**
     * Functionality : Checkduplicate
     * Parameters : function parameters
     * Creator : 28/05/2019 Piya
     * Last Modified : -
     * Return : data
     * Return Type : Array
     */
    public function FSnMAdjStkSumCheckDuplicate($ptCode)
    {
        $tSQL = "SELECT COUNT(FTAjhDocNo)AS counts
                 FROM TCNTPdtAdjStkHD
                 WHERE FTAjhDocNo = '$ptCode'";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            return $oQuery->result();
        } else {
            return false;
        }
    }

    public function FSaMUpdateDTBal($aResultHd)
    {
        $FTAjhDocNo = $aResultHd['FTAjhDocNo'];
        $FTAjhBchTo = $aResultHd['FTAjhBchTo'];
        $tFTAjhWhTo = $aResultHd['FTAjhWhTo'];

        $tSQL = "UPDATE 
                    STKDTL
                SET 
                    STKDTL.FCAjdWahB4Adj = STKBAL.FCAjdWahB4Adj
                FROM 
                TCNTPdtAdjStkDT AS STKDTL
                INNER JOIN  
                    (   SELECT 
                            Bal.FTBchCode,
                            Bal.FTPdtCode,
                            Bal.FTWahCode,
                            Bal.FCStkQty AS FCAjdWahB4Adj
                            FROM
                            TCNTPdtStkBal Bal
                
                )AS STKBAL ON  STKDTL.FTBchCode = STKBAL.FTBchCode AND STKBAL.FTWahCode = '$tFTAjhWhTo' AND STKDTL.FTPdtCode = STKBAL.FTPdtCode
                WHERE  STKDTL.FTAjhDocNo ='$FTAjhDocNo' ";
        $oQuery = $this->db->query($tSQL);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function FSaMAjhUpdateRefDocStockSubHD($paUpdateRefDocSub)
    {
        try {
            $aSMXtdDocNoRef =  $paUpdateRefDocSub['aSMXtdDocNoRef'];
            $FTAjhDocNo     =  $paUpdateRefDocSub['FTAjhDocNo'];
            $this->db->set('FTAjhDocRef', $FTAjhDocNo);
            $this->db->where_in('FTAjhDocNo', $aSMXtdDocNoRef);
            $this->db->update('TCNTPdtAdjStkHD');

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Cannot Update Item.',
                );
            } else {
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Update Complete.',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }
}
