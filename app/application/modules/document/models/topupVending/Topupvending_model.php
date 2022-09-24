<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Topupvending_model extends CI_Model
{

    /**
     * Functionality : HD List
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : HD List
     * Return Type : Array
     */
    public function FSaMHDList($paParams = [])
    {
        $aRowLen = FCNaHCallLenData($paParams['nRow'], $paParams['nPage']);
        $nLngID = $paParams['FNLngID'];

        $tSQL = "
            SELECT c.* FROM(
                SELECT  ROW_NUMBER() OVER(ORDER BY FTXthDocNo DESC) AS FNRowID,* FROM
                    (SELECT DISTINCT
                        HD.*,
                        BCHL.FTBchName,
                        USRL.FTUsrName AS FTCreateByName,
                        USRLAPV.FTUsrName AS FTXthApvName
                    FROM TVDTPdtTwxHD HD WITH (NOLOCK)
                    LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON BCHL.FTBchCode = HD.FTBchCode AND BCHL.FNLngID = $nLngID
                    LEFT JOIN TCNMUser_L USRL WITH (NOLOCK) ON USRL.FTUsrCode = HD.FTCreateBy AND USRL.FNLngID = $nLngID
                    LEFT JOIN TCNMUser_L USRLAPV WITH (NOLOCK) ON HD.FTXthApvCode = USRLAPV.FTUsrCode AND USRLAPV.FNLngID = $nLngID
                    WHERE 1=1
        ";

        if ($this->session->userdata('tSesUsrLevel') != "HQ") { // ไม่ใช่ผู้ใช้ระดับ HQ ดูได้แค่สาขาที่ login
            $tBchCode = $this->session->userdata('tSesUsrBchCode');
            $tSQL .= "
                AND HD.FTBchCode = '$tBchCode'
            ";
        }

        $aAdvanceSearch = $paParams['aAdvanceSearch'];

        $tSearchList = $aAdvanceSearch['tSearchAll'];
        if ($tSearchList != '') {
            $tSQL .= " AND ((HD.FTXthDocNo COLLATE THAI_BIN LIKE '%$tSearchList%') OR (BCHL.FTBchName COLLATE THAI_BIN LIKE '%$tSearchList%') OR (USRL.FTUsrName COLLATE THAI_BIN LIKE '%$tSearchList%') OR (USRLAPV.FTUsrName COLLATE THAI_BIN LIKE '%$tSearchList%'))";
        }

        // จากสาขา - ถึงสาขา
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo = $aAdvanceSearch['tSearchBchCodeTo'];
        if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)) {
            $tSQL .= " AND ((HD.FTBchCode BETWEEN $tSearchBchCodeFrom AND $tSearchBchCodeTo) OR (HD.FTBchCode BETWEEN $tSearchBchCodeTo AND $tSearchBchCodeFrom))";
        }

        // จากวันที่ - ถึงวันที่
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo = $aAdvanceSearch['tSearchDocDateTo'];

        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tSQL .= " AND ((HD.FDXthDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (HD.FDXthDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 00:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 23:59:59')))";
        }

        // สถานะเอกสาร
        $tSearchStaDoc = $aAdvanceSearch['tSearchStaDoc'];
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if ($tSearchStaDoc == 2) {
                $tSQL .= " AND HD.FTXthStaDoc = '$tSearchStaDoc' OR HD.FTXthStaDoc = ''";
            } else {
                $tSQL .= " AND HD.FTXthStaDoc = '$tSearchStaDoc'";
            }
        }

        // สถานะอนุมัติ
        $tSearchStaApprove = $aAdvanceSearch['tSearchStaApprove'];
        if (!empty($tSearchStaApprove) && ($tSearchStaApprove != "0")) {
            if ($tSearchStaApprove == 2) {
                $tSQL .= " AND HD.FTXthStaApv = '$tSearchStaApprove'";
            } else {
                $tSQL .= " AND HD.FTXthStaApv = '$tSearchStaApprove'";
            }
        }

        // สถานะประมวลผล
        $tSearchStaPrcStk = $aAdvanceSearch['tSearchStaPrcStk'];
        if (!empty($tSearchStaPrcStk) && ($tSearchStaPrcStk != "0")) {

            if ($tSearchStaPrcStk == 3) {
                $tSQL .= " AND HD.FTXthStaPrcStk = '$tSearchStaPrcStk' OR HD.FTXthStaPrcStk = '' ";
            } else {
                $tSQL .= " AND HD.FTXthStaPrcStk = '$tSearchStaPrcStk'";
            }
        }

        $tSQL .= ") Base) AS c WHERE c.FNRowID > $aRowLen[0] AND c.FNRowID <= $aRowLen[1]";

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oList = $oQuery->result();
            $nFoundRow = $this->FSnMHDListGetPageAll($paParams);
            $nPageAll = ceil($nFoundRow / $paParams['nRow']); // หา Page All จำนวน Rec หาร จำนวนต่อหน้า
            $aResult = array(
                'raItems' => $oList,
                'rnAllRow' => $nFoundRow,
                'rnCurrentPage' => $paParams['nPage'],
                'rnAllPage' => $nPageAll,
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        } else {
            // No Data
            $aResult = array(
                'rnAllRow' => 0,
                'rnCurrentPage' => $paParams['nPage'],
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
     * Functionality : Count HD Row
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Count Row
     * Return Type : Number
     */
    public function FSnMHDListGetPageAll($paParams = [])
    {
        $tSQL = "
            SELECT 
                HD.FTXthDocNo
            FROM TVDTPdtTwxHD HD WITH (NOLOCK)
            WHERE 1=1
        ";

        if ($this->session->userdata('tSesUsrLevel') != "HQ") { // ไม่ใช่ผู้ใช้ระดับ HQ ดูได้แค่สาขาที่ login
            $tBchCode = $this->session->userdata('tSesUsrBchCode');
            $tSQL .= "
                AND HD.FTBchCode = '$tBchCode'
            ";
        }

        $aAdvanceSearch = $paParams['aAdvanceSearch'];

        $tSearchList = $aAdvanceSearch['tSearchAll'];
        if ($tSearchList != '') {
            $tSQL .= " AND ((HD.FTXthDocNo COLLATE THAI_BIN LIKE '%$tSearchList%') OR (BCHL.FTBchName COLLATE THAI_BIN LIKE '%$tSearchList%') OR (USRL.FTUsrName COLLATE THAI_BIN LIKE '%$tSearchList%') OR (USRLAPV.FTUsrName COLLATE THAI_BIN LIKE '%$tSearchList%'))";
        }

        // จากสาขา - ถึงสาขา
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo = $aAdvanceSearch['tSearchBchCodeTo'];
        if (!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)) {
            $tSQL .= " AND ((HD.FTBchCode BETWEEN $tSearchBchCodeFrom AND $tSearchBchCodeTo) OR (HD.FTBchCode BETWEEN $tSearchBchCodeTo AND $tSearchBchCodeFrom))";
        }

        // จากวันที่ - ถึงวันที่
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo = $aAdvanceSearch['tSearchDocDateTo'];

        if (!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)) {
            $tSQL .= " AND ((HD.FDXthDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (HD.FDXthDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 00:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 23:59:59')))";
        }

        // สถานะเอกสาร
        $tSearchStaDoc = $aAdvanceSearch['tSearchStaDoc'];
        if (!empty($tSearchStaDoc) && ($tSearchStaDoc != "0")) {
            if ($tSearchStaDoc == 2) {
                $tSQL .= " AND HD.FTXthStaDoc = '$tSearchStaDoc' OR HD.FTXthStaDoc = ''";
            } else {
                $tSQL .= " AND HD.FTXthStaDoc = '$tSearchStaDoc'";
            }
        }

        // สถานะอนุมัติ
        $tSearchStaApprove = $aAdvanceSearch['tSearchStaApprove'];
        if (!empty($tSearchStaApprove) && ($tSearchStaApprove != "0")) {
            if ($tSearchStaApprove == 2) {
                $tSQL .= " AND HD.FTXthStaApv = '$tSearchStaApprove' OR HD.FTXthStaApv = '' ";
            } else {
                $tSQL .= " AND HD.FTXthStaApv = '$tSearchStaApprove'";
            }
        }

        // สถานะประมวลผล
        $tSearchStaPrcStk = $aAdvanceSearch['tSearchStaPrcStk'];
        if (!empty($tSearchStaPrcStk) && ($tSearchStaPrcStk != "0")) {

            if ($tSearchStaPrcStk == 3) {
                $tSQL .= " AND HD.FTXthStaPrcStk = '$tSearchStaPrcStk' OR HD.FTXthStaPrcStk = '' ";
            } else {
                $tSQL .= " AND HD.FTXthStaPrcStk = '$tSearchStaPrcStk'";
            }
        }

        $oQuery = $this->db->query($tSQL);
        return $oQuery->num_rows();
    }

    // ข้อมูลของริษัท
    public function FStTFWGetShpCodeForUsrLogin($paParams = [])
    {
        $nLngID     = $paParams['FNLngID'];
        $tUsrLogin  = $paParams['tUsrLogin'];

        $tSQL = "
            SELECT UGP.FTBchCode,
                BCHL.FTBchName,
                MCHL.FTMerCode,
                MCHL.FTMerName,
                UGP.FTShpCode,
                SHPL.FTShpName,
                SHP.FTShpType,
                SHP.FTWahCode AS FTWahCode,
                WAHL.FTWahName AS FTWahName
                /* BCH.FTWahCode AS FTWahCode_Bch, 
                BWAHL.FTWahName AS FTWahName_Bch  */
                        
            FROM TCNTUsrGroup UGP WITH (NOLOCK)
            LEFT JOIN TCNMBranch  BCH WITH (NOLOCK) ON UGP.FTBchCode = BCH.FTBchCode 
            LEFT JOIN TCNMBranch_L  BCHL WITH (NOLOCK) ON UGP.FTBchCode = BCHL.FTBchCode 
            /* LEFT JOIN TCNMWaHouse_L BWAHL ON BCH.FTWahCode = BWAHL.FTWahCode */
            LEFT JOIN TCNMShop      SHP WITH (NOLOCK) ON UGP.FTShpCode = SHP.FTShpCode AND BCH.FTBchCode = SHP.FTBchCode
            LEFT JOIN TCNMMerchant_L  MCHL WITH (NOLOCK) ON SHP.FTMerCode = MCHL.FTMerCode AND  MCHL.FNLngID = '" . $nLngID . "'
            LEFT JOIN TCNMShop_L    SHPL WITH (NOLOCK) ON SHP.FTShpCode = SHPL.FTShpCode AND SHP.FTBchCode = SHPL.FTBchCode AND SHPL.FNLngID = '" . $nLngID . "'
            LEFT JOIN TCNMWaHouse_L WAHL WITH (NOLOCK) ON SHP.FTWahCode = WAHL.FTWahCode AND BCH.FTBchCode = WAHL.FTBchCode
            WHERE FTUsrCode = '$tUsrLogin'
        ";

        $aResult = [];

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aResult = $oQuery->row_array();
        }

        return $aResult;
    }

    /**
     * Functionality : Get PDT Layout in Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Data List Product Layout
     * Return Type : Array
     */
    public function FSaMGetPdtLayoutInTmp($paParams = [])
    {
        $tUserSessionID = $paParams['tUserSessionID'];
        $aRowLen = FCNaHCallLenData($paParams['nRow'], $paParams['nPage']);
        // $nLngID = $paParams['FNLngID'];

        $tSQL = "
            SELECT c.* FROM(
                SELECT  ROW_NUMBER() OVER(ORDER BY FNCabSeqForTWXVD ASC, FNLayRowForTWXVD ASC, FNLayColForTWXVD ASC) AS FNRowID,* FROM
                    (SELECT DISTINCT
                        TMP.FTBchCode,
                        TMP.FTXthDocNo,
                        TMP.FNXtdSeqNo,
                        TMP.FTXthDocKey,
                        TMP.FTPdtCode,
                        TMP.FTXtdPdtName,
                        TMP.FNCabSeqForTWXVD,
                        TMP.FTCabNameForTWXVD,
                        TMP.FNLayRowForTWXVD,
                        TMP.FNLayColForTWXVD,
                        TMP.FCStkQty,
                        TMP.FCXtdQty,
                        TMP.FCLayColQtyMaxForTWXVD,
                        TMP.FTSessionID
                    FROM TCNTDocDTTmp TMP WITH(NOLOCK)
                    WHERE TMP.FTSessionID = '$tUserSessionID'
                    AND TMP.FTXthDocKey = 'TVDTPdtTwxHD'
        ";

        $tSearchList = $paParams['tSearchAll'];

        if ($tSearchList != '') {
            $tSQL .= " AND ((TMP.FTPdtCode COLLATE THAI_BIN LIKE '%$tSearchList%') OR (TMP.FTXtdPdtName COLLATE THAI_BIN LIKE '%$tSearchList%') OR (TMP.FTCabNameForTWXVD COLLATE THAI_BIN LIKE '%$tSearchList%'))";
        }

        $tSQL .= ") Base) AS c WHERE c.FNRowID > $aRowLen[0] AND c.FNRowID <= $aRowLen[1]";

        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $oList = $oQuery->result();
            $nFoundRow = $this->FSnMTFWGetPdtLayoutInTmpPageAll($paParams);
            $nPageAll = ceil($nFoundRow / $paParams['nRow']); // หา Page All จำนวน Rec หาร จำนวนต่อหน้า
            $aResult = array(
                'raItems'       => $oList,
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paParams['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        } else {
            // No Data
            $aResult = array(
                'rnAllRow' => 0,
                'rnCurrentPage' => $paParams['nPage'],
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
     * Functionality : Count PDT Layout in Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Count PDT Layout
     * Return Type : Number
     */
    public function FSnMTFWGetPdtLayoutInTmpPageAll($paParams = [])
    {
        $tUserSessionID = $paParams['tUserSessionID'];
        $nLngID = $paParams['FNLngID'];

        $tSQL = "
            SELECT 
                FTSessionID
            FROM TCNTDocDTTmp TMP WITH(NOLOCK) 
            WHERE TMP.FTSessionID = '$tUserSessionID' 
        ";

        $tSearchList = $paParams['tSearchAll'];

        if ($tSearchList != '') {
            $tSQL .= " AND ((TMP.FTPdtCode COLLATE THAI_BIN LIKE '%$tSearchList%') OR (TMP.FTXtdPdtName COLLATE THAI_BIN LIKE '%$tSearchList%') OR (TMP.FTCabNameForTWXVD COLLATE THAI_BIN LIKE '%$tSearchList%'))";
        }

        $oQuery = $this->db->query($tSQL);
        return $oQuery->num_rows();
    }

    /**
     * Functionality : Get HD Detail
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : HD Detail
     * Return Type : Array
     */
    public function FSaMGetHD($paParams = [])
    {
        $tDocNo = $paParams['tDocNo'];
        $nLngID = $paParams['nLngID'];

        $tSQL = "
            SELECT
                TWXVD.FTBchCode,
                BCHL.FTBchName,
                TWXVD.FTXthDocNo,
                TWXVD.FDXthDocDate,
                convert(CHAR(5), TWXVD.FDXthDocDate, 108)  AS FTXthDocTime,
                TWXVD.FTDptCode,
                DPTL.FTDptName,
                TWXVD.FTXthMerCode,
                MCHL.FTMerName,
                TWXVD.FTXthShopFrm,
                FSHP.FTShpType AS FTShpTypeFrm,
                FSHPL.FTShpName AS FTShpNameFrm,
                TWXVD.FTXthPosFrm,
                TWXVD.FTXthPosFrm AS FTPosComNameF,
                /*TWXVD.FTXthWhFrm,*/

                /*(CASE WHEN TWXVD.FTXthPosFrm !=''
                    THEN (SELECT TCNMWaHouse_L.FTWahName FROM TCNMWaHouse
                        LEFT JOIN TCNMWaHouse_L ON TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode
                        WHERE TCNMWaHouse.FTWahStaType=6 AND TCNMWaHouse.FTWahCode = TWXVD.FTXthWhFrm 
                        AND FNLngID = $nLngID) 
                    ELSE (SELECT TCNMWaHouse_L.FTWahName FROM TCNMWaHouse
                        LEFT JOIN TCNMWaHouse_L ON TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode
                        WHERE TCNMWaHouse.FTWahStaType=4 AND TCNMWaHouse.FTWahCode = TWXVD.FTXthWhFrm 
                        AND FNLngID = $nLngID) 
                    END
                ) AS FTXthWhNameFrm,*/

                TWXVD.FTXthShopTo,
                TSHP.FTShpType AS FTShpTypeTo,
                TSHPL.FTShpName AS FTShpNameTo,
                TWXVD.FTXthPosTo,
                TWXVD.FTXthPosTo AS FTPosComNameT,
                /*TWXVD.FTXthWhTo,*/

                /*(CASE WHEN TWXVD.FTXthPosTo !=''
                    THEN (SELECT TCNMWaHouse_L.FTWahName FROM TCNMWaHouse
                        LEFT JOIN TCNMWaHouse_L ON TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode
                        WHERE TCNMWaHouse.FTWahStaType=6 AND TCNMWaHouse.FTWahCode = TWXVD.FTXthWhTo 
                        AND FNLngID = $nLngID) 
                    ELSE (SELECT TCNMWaHouse_L.FTWahName FROM TCNMWaHouse
                        LEFT JOIN TCNMWaHouse_L ON TCNMWaHouse.FTWahCode = TCNMWaHouse_L.FTWahCode
                        WHERE TCNMWaHouse.FTWahStaType=4 AND TCNMWaHouse.FTWahCode = TWXVD.FTXthWhTo 
                        AND FNLngID = $nLngID) 
                    END
                ) AS FTXthWhNameTo,*/
                
                TWXVD.FTUsrCode,
                TWXVD.FTSpnCode,
                TWXVD.FTXthApvCode,
                TWXVD.FTXthRefExt,
                TWXVD.FDXthRefExtDate,
                TWXVD.FTXthRefInt,
                TWXVD.FDXthRefIntDate,
                TWXVD.FNXthDocPrint,
                TWXVD.FCXthTotal,
                TWXVD.FTXthRmk,
                TWXVD.FTXthStaDoc,
                TWXVD.FTXthStaApv,
                TWXVD.FTXthStaPrcStk,
                TWXVD.FTXthStaDelMQ,
                TWXVD.FNXthStaDocAct,
                TWXVD.FNXthStaRef,
                TWXVD.FTRsnCode,
                TWXVD.FDLastUpdOn,
                TWXVD.FTLastUpdBy,
                TWXVD.FDCreateOn,
                TWXVD.FTCreateBy,
                USRL.FTUsrName,
                USRAPV.FTUsrName AS FTUsrNameApv
            FROM TVDTPdtTwxHD TWXVD WITH (NOLOCK)

            LEFT JOIN TCNMBranch_L     BCHL WITH (NOLOCK)   ON TWXVD.FTBchCode   = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID
            LEFT JOIN TCNMMerchant_L   MCHL WITH (NOLOCK)   ON TWXVD.FTXthMerCode = MCHL.FTMerCode AND MCHL.FNLngID = $nLngID

            /* ต้นทาง */
            LEFT JOIN TCNMShop         FSHP WITH (NOLOCK)   ON TWXVD.FTXthShopFrm = FSHP.FTShpCode AND TWXVD.FTBchCode = FSHP.FTBchCode
            LEFT JOIN TCNMShop_L       FSHPL WITH (NOLOCK)  ON TWXVD.FTXthShopFrm = FSHPL.FTShpCode AND TWXVD.FTBchCode = FSHPL.FTBchCode AND FSHPL.FNLngID = $nLngID
            LEFT JOIN TVDMPosShop      PSHPLF WITH (NOLOCK) ON TWXVD.FTXthShopFrm = PSHPLF.FTShpCode AND TWXVD.FTBchCode = PSHPLF.FTBchCode

            /* ปลายทาง */
            LEFT JOIN TCNMShop         TSHP WITH (NOLOCK)   ON TWXVD.FTXthShopTo = TSHP.FTShpCode AND TWXVD.FTBchCode = TSHP.FTBchCode
            LEFT JOIN TCNMShop_L       TSHPL WITH (NOLOCK)  ON TWXVD.FTXthShopTo = TSHPL.FTShpCode AND TWXVD.FTBchCode = TSHPL.FTBchCode AND TSHPL.FNLngID = $nLngID
            LEFT JOIN TVDMPosShop      PSHPLT WITH (NOLOCK) ON TWXVD.FTXthShopTo = PSHPLT.FTShpCode AND TWXVD.FTBchCode = PSHPLT.FTBchCode
            
            LEFT JOIN TCNMUser_L       USRL WITH (NOLOCK)   ON TWXVD.FTCreateBy   = USRL.FTUsrCode AND USRL.FNLngID = $nLngID
            LEFT JOIN TCNMUser_L       USRAPV WITH (NOLOCK) ON TWXVD.FTXthApvCode = USRAPV.FTUsrCode AND USRAPV.FNLngID = $nLngID
            LEFT JOIN TCNMUsrDepart_L  DPTL WITH (NOLOCK)   ON TWXVD.FTDptCode = DPTL.FTDptCode AND DPTL.FNLngID = $nLngID
            WHERE 1=1
        ";

        if ($tDocNo != "") {
            $tSQL .= " AND TWXVD.FTXthDocNo = '$tDocNo'";
        }

        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $oDetail = $oQuery->row_array();
            $aResult = array(
                'raItems' => $oDetail,
                'rtCode' => '1',
                'rtDesc' => 'success',
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

    /**
     * Functionality : Get WahCode by RefCode
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : WahCode
     * Return Type : String
     */
    public function FStMGetWahCodeByRefCode($paParams = [])
    {
        $tRefCode = $paParams['tRefCode'];

        $tSQL = "
            SELECT
                FTWahCode
            FROM TCNMWaHouse WITH(NOLOCK)   
            WHERE FTWahRefCode = '$tRefCode' 
        ";

        $oQuery = $this->db->query($tSQL);
        $oRow = $oQuery->row();

        $tResult = '';

        if (isset($oRow)) {
            $tResult = $oRow->FTWahCode;
        }

        return $tResult;
    }

    /**
     * Functionality : Get Wah by RefCode
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Wah Data
     * Return Type : Array
     */
    public function FSaMGetWahByRefCode($paParams = [])
    {
        $tRefCode = $paParams['tRefCode'];
        $tBchCode = $paParams['tBchCode'];

        $tSQL = "
            SELECT
                WAH.FTWahCode,
                WAHL.FTWahName
            FROM TCNMWaHouse WAH WITH(NOLOCK)   
            LEFT JOIN TCNMWaHouse_L WAHL WITH(NOLOCK) ON WAHL.FTWahCode = WAH.FTWahCode AND WAH.FTBchCode = WAHL.FTBchCode
            WHERE WAH.FTWahRefCode = '$tRefCode' 
            AND WAH.FTBchCode = '$tBchCode'
            AND WAH.FTWahStaType = '4'
        ";

        $oQuery = $this->db->query($tSQL);

        $aResult = [];

        if ($oQuery->num_rows() > 0) {
            $aResult = $oQuery->result_array();
        }

        return $aResult;
    }

    /**
     * Functionality : Get Wah in DT
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Wah Data
     * Return Type : Array
     */
    public function FSaMGetWahInDT($paParams = [])
    {
        $tDocNo = $paParams['tDocNo'];
        $tBchCode = $paParams['tBchCode'];

        $tSQL = "
            SELECT DISTINCT
                DT.FTXthWhFrm AS FTWahCode,
                WAHL.FTWahName
            FROM TVDTPdtTwxDT DT WITH(NOLOCK) 
            LEFT JOIN TCNMWaHouse_L WAHL WITH(NOLOCK) ON WAHL.FTWahCode = DT.FTXthWhFrm AND DT.FTBchCode = WAHL.FTBchCode  
            WHERE DT.FTBchCode = '$tBchCode' 
            AND DT.FTXthDocNo = '$tDocNo'
        ";

        $oQuery = $this->db->query($tSQL);

        $aResult = [];

        if ($oQuery->num_rows() > 0) {
            $aResult = $oQuery->result_array();
        }

        return $aResult;
    }

    /**
     * Functionality : Insert PDT Layout to Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMPdtlayoutToTemp($paParams = [])
    {
        $tDocNo = empty($paParams['tDocNo']) ? 'VWDOCTEMP' : $paParams['tDocNo']; // เลขที่เอกสาร
        $tDocKey = $paParams['tDocKey']; // ชื่อตาราง HD
        $tBchCode = $paParams['tBchCode']; // สาขาที่เลือก
        $tShpCode = $paParams['tShpCode']; // ร้านค้าที่เลือก
        $tPosCode = $paParams['tPosCode']; // ตู้ขายที่เลือก
        $tWahCode = $this->FStMGetWahCodeByRefCode(['tRefCode' => $tPosCode]); // คลังสินค้าของ ตู้ขายที่เลือก
        $tWahCodeInShop = FCNtAddSingleQuote($paParams['tWahCodeInShop']); // คลังสินค้าที่อยู่ในร้านค้าที่เลือก
        $tBchCodeLogin = $paParams['tBchCodeLogin'];
        $tUserSessionID = $paParams['tUserSessionID']; // User Session
        $tUserSessionDate = $paParams['tUserSessionDate'];
        $tUserLoginCode = $paParams['tUserLoginCode'];
        $nLngID = $paParams['nLngID'];

        // ทำการลบ ใน DT Temp ก่อนการย้าย DT ไป DT Temp
        // $this->db->where('FTXthDocKey', $tDocKey);
        $this->db->where('FTSessionID', $tUserSessionID);
        // $this->db->where('FTXthDocNo', $tDocNo);
        $this->db->delete('TCNTDocDTTmp');

        $tSQL = "   
            INSERT TCNTDocDTTmp 
                (FTBchCode,
                FTXthDocNo,
                FNXtdSeqNo,
                FTXthDocKey,
                FTPdtCode,
                FTXtdPdtName,
                FNCabSeqForTWXVD,
                FTCabNameForTWXVD,
                FNLayRowForTWXVD,
                FNLayColForTWXVD,
                FCStkQty,
                FCLayColQtyMaxForTWXVD,
                FTXthWhFrmForTWXVD,
                FTXthWhToForTWXVD,
                FCXtdQty,

                /*FCMaxTransferForTWXVD,
                FCUserInPutTransferForTWXVD,*/

                FDCreateOn,
                FTSessionID)
        ";

        $tSQL .= "  
            SELECT 
                '$tBchCodeLogin' AS FTBchCode,
                '$tDocNo' AS FTXthDocNo,
                ROW_NUMBER() OVER(ORDER BY SHPCABL.FNCabSeq ASC) AS FNXtdSeqNo,
                '$tDocKey' AS FTXthDocKey,
                PDT.FTPdtCode,
                PDTL.FTPdtName AS FTXtdPdtName,
                PDTLAY.FNCabSeq AS FNCabSeqForTWXVD,
                SHPCABL.FTCabName AS FTCabNameForTWXVD,
                PDTLAY.FNLayRow AS FNLayRowForTWXVD,
                PDTLAY.FNLayCol AS FNLayColForTWXVD,
                ISNULL(
                    (SELECT 
                        FCStkQty 
                    FROM TVDTPdtStkBal WITH(NOLOCK) 
                    WHERE FTWahCode = '$tWahCode' 
                    AND FTBchCode = '$tBchCode'
                    AND FNLayRow = PDTLAY.FNLayRow
                    AND FNLayCol = PDTLAY.FNLayCol
                    AND FNCabSeq = PDTLAY.FNCabSeq
                    AND FTPdtCode = PDTLAY.FTPdtCode)
                ,0) AS FCStkQty,
                PDTLAY.FCLayDim AS FCLayColQtyMaxForTWXVD, 
                PDTLAY.FTWahCode AS FTXthWhFrmForTWXVD,
                '$tWahCode' AS FTXthWhToForTWXVD,
                NULL AS FCXtdQty,
                '$tUserSessionDate' AS FDCreateOn,
                '$tUserSessionID' AS FTSessionID
            FROM TVDMPdtLayout PDTLAY WITH(NOLOCK)

            LEFT JOIN TVDMShopCabinet_L SHPCABL WITH(NOLOCK) ON SHPCABL.FNCabSeq = PDTLAY.FNCabSeq AND SHPCABL.FTShpCode = PDTLAY.FTShpCode AND SHPCABL.FNLngID = $nLngID
            LEFT JOIN TCNMPdt PDT WITH(NOLOCK) ON PDTLAY.FTPdtCode = PDT.FTPdtCode
            LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON PDT.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID = $nLngID
            
            WHERE PDTLAY.FTBchCode = '$tBchCode'
            AND PDTLAY.FTShpCode = '$tShpCode'
            AND PDTLAY.FTWahCode IN ($tWahCodeInShop)
            ORDER BY PDTLAY.FNCabSeq ASC, PDTLAY.FNLayRow ASC, PDTLAY.FNLayCol ASC
        ";

        $this->db->query($tSQL);

        // Update Qty
        $tTempSQL = "
            SELECT
                TMP.FTBchCode,
                TMP.FTXthDocNo,
                TMP.FNXtdSeqNo,
                TMP.FTXthDocKey,
                TMP.FTPdtCode,
                TMP.FTXtdPdtName,
                TMP.FNCabSeqForTWXVD,
                TMP.FTCabNameForTWXVD,
                TMP.FNLayRowForTWXVD,
                TMP.FNLayColForTWXVD,
                TMP.FCStkQty,
                TMP.FCLayColQtyMaxForTWXVD,
                TMP.FTXthWhFrmForTWXVD,
                TMP.FTXthWhToForTWXVD,
                TMP.FCXtdQty,
                TMP.FTSessionID
            FROM TCNTDocDTTmp TMP WITH(NOLOCK)
            WHERE TMP.FTSessionID = '$tUserSessionID'
            ORDER BY TMP.FNCabSeqForTWXVD ASC, TMP.FNLayRowForTWXVD ASC, TMP.FNLayColForTWXVD ASC
        ";

        $oTemp = $this->db->query($tTempSQL);
        $aTemp = $oTemp->result_array();

        foreach ($aTemp as $aItem) {
            $aGetPdtStkBalWithCheckInTmp = [
                'tBchCode' => $aItem['FTBchCode'],
                'tWahCode' => $tWahCodeInShop, // คลังสินค้าต้นทาง เพื่อใช้ในการเติมให้กับ คลังตู้สินค้า
                'tPdtCode' => $aItem['FTPdtCode'],
                'tUserSessionID' => $aItem['FTSessionID'],
            ];
            $nStkBal = $this->FSnGetPdtStkBalWithCheckInTmp($aGetPdtStkBalWithCheckInTmp);

            $nBal = $aItem['FCLayColQtyMaxForTWXVD'] - $aItem['FCStkQty'];

            if ($nBal <= $nStkBal) {

                $aUpdateQtyInTmpBySeqParams = [
                    'cQty' => $nBal,
                    'tUserLoginCode' => $tUserLoginCode,
                    'tUserSessionID' => $aItem['FTSessionID'],
                    'nSeqNo' => $aItem['FNXtdSeqNo'],
                ];
                $this->FSbUpdateQtyInTmpBySeq($aUpdateQtyInTmpBySeqParams);
            } else {
                $aUpdateQtyInTmpBySeqParams = [
                    'cQty' => ($nStkBal < 0) ? 0 : $nStkBal,
                    'tUserLoginCode' => $tUserLoginCode,
                    'tUserSessionID' => $aItem['FTSessionID'],
                    'nSeqNo' => $aItem['FNXtdSeqNo'],
                ];
                $this->FSbUpdateQtyInTmpBySeq($aUpdateQtyInTmpBySeqParams);
            }
        }
    }

    /**
     * Functionality : Insert DT to Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMDTToTemp($paParams = [])
    {
        $tDocNo = $paParams['tDocNo']; // เลขที่เอกสาร
        $tDocKey = $paParams['tDocKey']; // ชื่อตาราง HD
        $tBchCode = $paParams['tBchCode']; // สาขาที่เลือก
        // $tShpCode = $paParams['tShpCode']; // ร้านค้าที่เลือก
        // $tPosCode = $paParams['tPosCode']; // ตู้ขายที่เลือก
        // $tWahCode = $this->FStMGetWahCodeByRefCode(['tRefCode' => $tPosCode]); // คลังสินค้าของ ตู้ขายที่เลือก
        // $tBchCodeLogin = $paParams['tBchCodeLogin'];
        $tUserSessionID = $paParams['tUserSessionID']; // User Session
        $tUserSessionDate = $paParams['tUserSessionDate'];
        $nLngID = $paParams['nLngID'];

        // ทำการลบ ใน DT Temp ก่อนการย้าย DT ไป DT Temp
        $this->db->where('FTSessionID', $tUserSessionID);
        $this->db->delete('TCNTDocDTTmp');

        $tSQL = "   
            INSERT TCNTDocDTTmp
                (FTBchCode,
                FTXthDocNo,
                FNXtdSeqNo,
                FTXthDocKey,
                FTPdtCode,
                FTXtdPdtName,
                FNCabSeqForTWXVD,
                FTCabNameForTWXVD,
                FNLayRowForTWXVD,
                FNLayColForTWXVD,
                FCStkQty,
                FCLayColQtyMaxForTWXVD,
                FTXthWhFrmForTWXVD,
                FTXthWhToForTWXVD,
                FCXtdQty,

                /*FCMaxTransferForTWXVD,
                FCUserInPutTransferForTWXVD,*/
                
                FDCreateOn,
                FTSessionID)
        ";

        $tSQL .= "  
            SELECT
                DT.FTBchCode,
                'VWDOCTEMP' AS FTXthDocNo,
                DT.FNXtdSeqNo,
                '$tDocKey' AS FTXthDocKey,
                DT.FTPdtCode,
                PDTL.FTPdtName AS FTXtdPdtName,
                DT.FNCabSeq AS FNCabSeqForTWXVD,
                SHPCABL.FTCabName AS FTCabNameForTWXVD,
                DT.FNLayRow AS FNLayRowForTWXVD,
                DT.FNLayCol AS FNLayColForTWXVD,
                ISNULL( 
                    (SELECT 
                        FCStkQty 
                    FROM TVDTPdtStkBal WITH(NOLOCK) 
                    WHERE FTWahCode = DT.FTXthWhTo 
                    AND FTBchCode = DT.FTBchCode
                    AND FNLayRow = DT.FNLayRow
                    AND FNLayCol = DT.FNLayCol
                    AND FNCabSeq = DT.FNCabSeq
                    AND FTPdtCode = DT.FTPdtCode)
                ,0) AS FCStkQty,
                PDTLAY.FCLayDim AS FCLayColQtyMaxForTWXVD,
                DT.FTXthWhFrm AS FTXthWhFrmForTWXVD,
                DT.FTXthWhTo AS FTXthWhToForTWXVD,
                DT.FCXtdQty,
                '$tUserSessionDate' AS FDCreateOn,
                '$tUserSessionID' AS FTSessionID
            FROM TVDTPdtTwxDT DT WITH(NOLOCK)
            LEFT JOIN TVDTPdtTwxHD HD WITH(NOLOCK) ON HD.FTXthDocNo = DT.FTXthDocNo 
            AND HD.FTBchCode = DT.FTBchCode   

            LEFT JOIN TVDMPdtLayout PDTLAY WITH(NOLOCK) ON PDTLAY.FTBchCode = DT.FTBchCode 
            AND PDTLAY.FTShpCode = HD.FTXthShopTo AND PDTLAY.FNCabSeq = DT.FNCabSeq
            AND PDTLAY.FNLayRow = DT.FNLayRow AND PDTLAY.FNLayCol = DT.FNLayCol

            LEFT JOIN TVDMShopCabinet_L SHPCABL WITH(NOLOCK) ON SHPCABL.FNCabSeq = PDTLAY.FNCabSeq 
            AND SHPCABL.FTShpCode = PDTLAY.FTShpCode AND SHPCABL.FNLngID = $nLngID

            LEFT JOIN TCNMPdt_L PDTL WITH(NOLOCK) ON PDTL.FTPdtCode = DT.FTPdtCode AND PDTL.FNLngID = $nLngID
            
            WHERE DT.FTBchCode = '$tBchCode'
            AND DT.FTXthDocNo = '$tDocNo'
            ORDER BY DT.FNCabSeq ASC, DT.FNLayRow ASC, DT.FNLayCol ASC
        ";

        $this->db->query($tSQL);
    }

    /**
     * Functionality : Insert Temp to DT
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMTempToDT($paParams = [])
    {
        $tDocNo = $paParams['tDocNo']; // เลขที่เอกสาร
        $tDocKey = $paParams['tDocKey']; // ชื่อตาราง HD
        $tBchCode = $paParams['tBchCode']; // สาขาที่เลือก
        // $tShpCode = $paParams['tShpCode']; // ร้านค้าที่เลือก
        // $tPosCode = $paParams['tPosCode']; // ตู้ขายที่เลือก
        // $tWahCode = $this->FStMGetWahCodeByRefCode(['tRefCode' => $tPosCode]); // คลังสินค้าของ ตู้ขายที่เลือก
        // $tBchCodeLogin = $paParams['tBchCodeLogin'];
        $tUserSessionID = $paParams['tUserSessionID']; // User Session
        // $nLngID = $paParams['nLngID'];

        // ทำการลบ ใน DT Temp ก่อนการย้าย DT ไป DT Temp
        $this->db->where('FTXthDocNo', $tDocNo);
        $this->db->delete('TVDTPdtTwxDT');

        $tSQL = "   
            INSERT TVDTPdtTwxDT 
                (FTBchCode,
                FTXthDocNo,
                FNXtdSeqNo,
                FNCabSeq,
                FNLayRow,
                FNLayCol,
                FTPdtCode,
                FCXtdQty,
                FTXthWhFrm,
                FTXthWhTo)
        ";

        $tSQL .= "  
            SELECT
                TMP.FTBchCode,
                TMP.FTXthDocNo,
                TMP.FNXtdSeqNo,
                FNCabSeqForTWXVD AS FNCabSeq,
                FNLayRowForTWXVD AS FNLayRow,
                FNLayColForTWXVD AS FNLayCol,
                TMP.FTPdtCode,
                FCXtdQty,
                TMP.FTXthWhFrmForTWXVD AS FTXthWhFrm,
                TMP.FTXthWhToForTWXVD AS FTXthWhTo
            FROM TCNTDocDTTmp TMP WITH(NOLOCK)
            WHERE TMP.FTBchCode = '$tBchCode'
            AND TMP.FTXthDocKey = '$tDocKey'
            AND TMP.FTSessionID = '$tUserSessionID'
            ORDER BY TMP.FNCabSeqForTWXVD ASC, TMP.FNLayRowForTWXVD ASC, TMP.FNLayColForTWXVD ASC
        ";

        $this->db->query($tSQL);

        // ทำการลบ ใน DT Temp หลังการย้าย DT Temp ไป DT
        $this->db->where('FTSessionID', $tUserSessionID);
        $this->db->delete('TCNTDocDTTmp');
    }

    public function FSxMDeleteDoctemForNewEvent($paInfor)
    {
        $tSQL = "
            DELETE FROM TCNTDocDTTmp 
            WHERE FTBchCode = '" . $paInfor["tBchCode"] . "' 
            AND FTSessionID = '" . $this->session->userdata('tSesSessionID') . "' 
            AND FTXthDocKey = '" . $paInfor["FTXthDocKey"] . "'
        ";
        $this->db->query($tSQL);
    }

    /**
     * Functionality : ล้างข้อมูลในตาราง tmp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : -
     * Return Type : -
     */
    public function FSxMClearPdtLayoutInTmp($aParams = [])
    {
        $tUserSessionID = $aParams['tUserSessionID'];
        $tDocKey = $aParams['tDocKey'];

        $tSQL = "
            DELETE FROM TCNTDocDTTmp 
            WHERE FTSessionID = '$tUserSessionID' AND FTXthDocKey = '$tDocKey'
        ";
        $this->db->query($tSQL);
    }

    /**
     * Functionality : Check DocNo is Duplicate
     * Parameters : DocNo
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Boolean
     */
    public function FSbMCheckDuplicate($ptDocNo = '')
    {
        $tSQL = "   
            SELECT 
                FTXthDocNo
            FROM TVDTPdtTwxHD
            WHERE FTXthDocNo = '$ptDocNo'
        ";

        $bStatus = false;
        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $bStatus = true;
        }

        return $bStatus;
    }

    /**
     * Functionality : Add or Update HD
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMAddUpdateHD($paParams = [])
    {
        try {
            // Update Master
            $this->db->set('FTBchCode', $paParams['FTBchCode']);
            $this->db->set('FDXthDocDate', $paParams['FDXthDocDate']);
            $this->db->set('FTDptCode', $paParams['FTDptCode']);
            $this->db->set('FTXthMerCode', $paParams['FTXthMerCode']);
            $this->db->set('FTXthShopFrm', $paParams['FTXthShopFrm']);
            $this->db->set('FTXthShopTo', $paParams['FTXthShopTo']);
            // $this->db->set('FTXthWhFrm', $paParams['FTXthWhFrm']);
            // $this->db->set('FTXthWhTo', $paParams['FTXthWhTo']);
            $this->db->set('FTXthPosFrm', $paParams['FTXthPosFrm']);
            $this->db->set('FTXthPosTo', $paParams['FTXthPosTo']);
            $this->db->set('FTUsrCode', $paParams['FTUsrCode']);
            $this->db->set('FTXthRefExt', $paParams['FTXthRefExt']);
            $this->db->set('FDXthRefExtDate', $paParams['FDXthRefExtDate']);
            $this->db->set('FTXthRefInt', $paParams['FTXthRefInt']);
            $this->db->set('FDXthRefIntDate', $paParams['FDXthRefIntDate']);
            $this->db->set('FNXthDocPrint', $paParams['FNXthDocPrint']);
            $this->db->set('FCXthTotal', $paParams['FCXthTotal']);
            $this->db->set('FTXthRmk', $paParams['FTXthRmk']);
            $this->db->set('FTXthStaDoc', $paParams['FTXthStaDoc']);
            $this->db->set('FTXthStaApv', $paParams['FTXthStaApv']);
            $this->db->set('FTXthStaPrcStk', $paParams['FTXthStaPrcStk']);
            $this->db->set('FNXthStaDocAct', $paParams['FNXthStaDocAct']);
            $this->db->set('FNXthStaRef', $paParams['FNXthStaRef']);
            $this->db->set('FTRsnCode', $paParams['FTRsnCode']);
            $this->db->set('FDLastUpdOn', $paParams['FDLastUpdOn']);
            $this->db->set('FTLastUpdBy', $paParams['FTLastUpdBy']);
            $this->db->where('FTXthDocNo', $paParams['FTXthDocNo']);
            $this->db->update('TVDTPdtTwxHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Update Master Success',
                );
            } else {
                // Add Master
                $this->db->insert('TVDTPdtTwxHD', array(
                    'FTXthDocNo'            => $paParams['FTXthDocNo'],
                    'FTBchCode'             => $paParams['FTBchCode'],
                    'FDXthDocDate'          => $paParams['FDXthDocDate'],
                    'FTDptCode'             => $paParams['FTDptCode'],
                    'FTXthMerCode'      => $paParams['FTXthMerCode'],
                    'FTXthShopFrm'          => $paParams['FTXthShopFrm'],
                    'FTXthShopTo'           => $paParams['FTXthShopTo'],
                    // 'FTXthWhFrm'            => $paParams['FTXthWhFrm'],
                    // 'FTXthWhTo'             => $paParams['FTXthWhTo'],
                    'FTXthPosFrm'            => $paParams['FTXthPosFrm'],
                    'FTXthPosTo'             => $paParams['FTXthPosTo'],
                    'FTUsrCode'             => $paParams['FTUsrCode'],
                    'FTXthRefExt'           => $paParams['FTXthRefExt'],
                    'FDXthRefExtDate'       => $paParams['FDXthRefExtDate'],
                    'FTXthRefInt'           => $paParams['FTXthRefInt'],
                    'FDXthRefIntDate'       => $paParams['FDXthRefIntDate'],
                    'FNXthDocPrint'         => $paParams['FNXthDocPrint'],
                    'FCXthTotal'            => $paParams['FCXthTotal'],
                    'FTXthRmk'              => $paParams['FTXthRmk'],
                    'FTXthStaDoc'           => $paParams['FTXthStaDoc'],
                    'FTXthStaApv'           => $paParams['FTXthStaApv'],
                    'FTXthStaPrcStk'        => $paParams['FTXthStaPrcStk'],
                    'FNXthStaDocAct'        => $paParams['FNXthStaDocAct'],
                    'FNXthStaRef'           => $paParams['FNXthStaRef'],
                    'FTRsnCode'             => $paParams['FTRsnCode'],
                    'FDLastUpdOn'           => $paParams['FDLastUpdOn'],
                    'FDCreateOn'            => $paParams['FDCreateOn'],
                    'FTCreateBy'            => $paParams['FTCreateBy'],
                    'FTLastUpdBy'           => $paParams['FTLastUpdBy']
                ));
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
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    /**
     * Functionality : Add or Update HDRef
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMAddUpdateHDRef($paParams = [])
    {
        // Update Master
        $this->db->set('FTBchCode', $paParams['FTBchCode']);
        $this->db->set('FTXthDocNo', $paParams['FTXthDocNo']);
        $this->db->set('FTXthCtrName', $paParams['FTXthCtrName']);
        $this->db->set('FDXthTnfDate', $paParams['FDXthTnfDate']);
        $this->db->set('FTXthRefTnfID', $paParams['FTXthRefTnfID']);
        $this->db->set('FTXthRefVehID', $paParams['FTXthRefVehID']);
        $this->db->set('FTXthQtyAndTypeUnit', $paParams['FTXthQtyAndTypeUnit']);
        $this->db->set('FNXthShipAdd', $paParams['FNXthShipAdd']);
        $this->db->set('FTViaCode', $paParams['FTViaCode']);

        $this->db->where('FTXthDocNo', $paParams['FTXthDocNo']);
        $this->db->update('TVDTPdtTwxHDRef');

        if ($this->db->affected_rows() > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Update Master Success',
            );
        } else {
            // Add Master
            $this->db->insert('TVDTPdtTwxHDRef', array(
                'FTBchCode' => $paParams['FTBchCode'],
                'FTXthDocNo' => $paParams['FTXthDocNo'],
                'FTXthCtrName' => $paParams['FTXthCtrName'],
                'FDXthTnfDate' => $paParams['FDXthTnfDate'],
                'FTXthRefTnfID' => $paParams['FTXthRefTnfID'],
                'FTXthRefVehID' => $paParams['FTXthRefVehID'],
                'FTXthQtyAndTypeUnit' => $paParams['FTXthQtyAndTypeUnit'],
                'FNXthShipAdd' => $paParams['FNXthShipAdd'],
                'FTViaCode' => $paParams['FTViaCode']
            ));

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
        return $aStatus;
    }

    /**
     * Functionality : Get HDRef
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : HDRef Data
     * Return Type : Array
     */
    public function FSaMGetHDRef($paParams = [])
    {
        $tDocNo = $paParams['tDocNo'];

        $tSQL = "
            SELECT
                TFWR.FTBchCode,
                TFWR.FTXthDocNo,
                TFWR.FTXthCtrName,
                TFWR.FDXthTnfDate,
                TFWR.FTXthRefTnfID,
                TFWR.FTXthRefVehID,
                TFWR.FTXthQtyAndTypeUnit,
                TFWR.FNXthShipAdd,
                TFWR.FTViaCode,
                TADD.FNAddSeqNo,
                TADD.FTAddV1No,
                TADD.FTAddV1Soi,
                TADD.FTAddV1Village,
                TADD.FTAddV1Road,
                TSUD.FTSudName,
                TDST.FTDstName,
                TPVC.FTPvnName,
                TADD.FTAddV1PostCode,
                TSPVL.FTViaName
            FROM [TVDTPdtTwxHDRef] TFWR WITH (NOLOCK)
            LEFT JOIN TCNMAddress_L TADD WITH (NOLOCK) ON TFWR.FNXthShipAdd = TADD.FNAddSeqNo
            LEFT JOIN TCNMSubDistrict_L TSUD WITH (NOLOCK) ON TADD.FTAddV1SubDist = TSUD.FTSudCode
            LEFT JOIN TCNMDistrict_L TDST WITH (NOLOCK) ON TADD.FTAddV1DstCode = TDST.FTDstCode
            LEFT JOIN TCNMProvince_L TPVC WITH (NOLOCK) ON TADD.FTAddV1PvnCode = TPVC.FTPvnCode
            LEFT JOIN TCNMShipVia_L  TSPVL WITH (NOLOCK) ON TFWR.FTViaCode = TSPVL.FTViaCode
        ";

        if ($tDocNo != '') {
            $tSQL .= " WHERE (TFWR.FTXthDocNo = '$tDocNo')";
        }

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $oDetail = $oQuery->result();
            $aResult = array(
                'raItems'   => $oDetail[0],
                'rtCode' => '1',
                'rtDesc' => 'Get HDRef Sucess',
            );
        } else {
            //Not Found
            $aResult = array(
                'rtCode' => '800',
                'rtDesc' => 'HDRef Not Found',
            );
        }
        $jResult = json_encode($aResult);
        $aResult = json_decode($jResult, true);
        return $aResult;
    }

    /**
     * Functionality : Update DocNo in Temp
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMUpdateDocNoInTmp($paParams = [])
    {
        $this->db->set('FTXthDocNo', $paParams['FTXthDocNo']);
        // $this->db->set('FTBchCode', $paParams['FTBchCode']);
        $this->db->where('FTXthDocNo', 'VWDOCTEMP');
        $this->db->where('FTSessionID', $paParams['tUserSessionID']);
        $this->db->where('FTXthDocKey', $paParams['FTXthDocKey']);
        $this->db->update('TCNTDocDTTmp');

        if ($this->db->affected_rows() > 0) {
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'Update DocNo Success',
            );
        } else {
            $aStatus = array(
                'rtCode' => '903',
                'rtDesc' => 'Update DocNo Fail',
            );
        }
        return $aStatus;
    }

    /**
     * Functionality : Update Refill Qty in Temp by SeqNo
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status Update
     * Return Type : Boolean
     */
    public function FSbUpdateQtyInTmpBySeq($paParams = [])
    {
        $this->db->set('FCXtdQty', $paParams['cQty']);
        $this->db->set('FDLastUpdOn', 'GETDATE()', false);
        $this->db->set('FTLastUpdBy', $paParams['tUserLoginCode']);
        $this->db->where('FTSessionID', $paParams['tUserSessionID']);
        $this->db->where('FNXtdSeqNo', $paParams['nSeqNo']);
        $this->db->update('TCNTDocDTTmp');

        $bStatus = false;

        if ($this->db->affected_rows() > 0) {
            $bStatus = true;
        }

        return $bStatus;
    }

    /**
     * Functionality : Delete Refill Qty in Temp by SeqNo
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status Delete
     * Return Type : Boolean
     */
    public function FSbDeleteInTmpBySeq($paParams = [])
    {
        $this->db->where('FTSessionID', $paParams['tUserSessionID']);
        $this->db->where('FNXtdSeqNo', $paParams['nSeqNo']);
        $this->db->delete('TCNTDocDTTmp');

        $bStatus = false;

        if ($this->db->affected_rows() > 0) {
            $bStatus = true;
        }

        return $bStatus;
    }

    /**
     * Functionality : Get PDT Stock Balance with Check in Temp
     * ดึงข้อมูลจาก FCStkQty in TCNTPdtStkBal 
     * เปรียบเทียบกับ FCStkQty in TCNTDocDTTmp ว่ามีการเติมไปแล้วเท่าไหร่
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Stock Balance
     * Return Type : Number
     */
    public function FSnGetPdtStkBalWithCheckInTmp($paParams = [])
    {
        $tWahCode = $paParams['tWahCode']; // คลังสินค้าต้นทาง เพื่อใช้ในการเติมให้กับ คลังตู้สินค้า
        $tBchCode = $paParams['tBchCode'];
        $tPdtCode = $paParams['tPdtCode'];
        $tUserSessionID = $paParams['tUserSessionID'];
        $tNotInSeqNo = isset($paParams['nNotInSelfSeqNo']) ? " AND TMP.FNXtdSeqNo NOT IN (" . $paParams['nNotInSelfSeqNo'] . ")" : "";

        $tPdtStkBalSQL = "
            SELECT 
                SUM(ISNULL(FCStkQty, 0)) AS FCStkQty 
            FROM TCNTPdtStkBal WITH(NOLOCK) 
            WHERE FTWahCode IN ($tWahCode)
            AND FTBchCode = '$tBchCode'
            AND FTPdtCode = '$tPdtCode'
            GROUP BY FTPdtCode
        ";

        $oPdtStkBal = $this->db->query($tPdtStkBalSQL);
        $oPdtStkBalRow = $oPdtStkBal->row();

        $tQtyInTempSQL = "
            SELECT DISTINCT
                SUM(ISNULL(TMP.FCXtdQty, 0)) AS FCXtdQty
            FROM TCNTDocDTTmp TMP WITH(NOLOCK)
            WHERE TMP.FTSessionID = '$tUserSessionID'
            AND TMP.FTBchCode = '$tBchCode'
            AND TMP.FTPdtCode = '$tPdtCode'
            $tNotInSeqNo
            GROUP BY TMP.FTPdtCode
        ";

        $oQtyInTemp = $this->db->query($tQtyInTempSQL);
        $oQtyInTempRow = $oQtyInTemp->row();

        $nResult = (empty($oPdtStkBalRow->FCStkQty) ? 0 : $oPdtStkBalRow->FCStkQty) - (empty($oQtyInTempRow->FCXtdQty) ? 0 : $oQtyInTempRow->FCXtdQty);
        return $nResult;
    }

    /**
     * Functionality : Cancel Document
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status
     * Return Type : Array
     */
    public function FSaMDocCancel($paParams = [])
    {
        try {
            // TVDTPdtTwxHD
            $this->db->set('FTXthStaDoc', '3');
            $this->db->where('FTXthDocNo', $paParams['tDocNo']);
            $this->db->update('TVDTPdtTwxHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Cancel Success',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Cancel Fail',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    /**
     * Functionality : Approve Document
     * Parameters : -
     * Creator : 04/02/2020 piya
     * Last Modified : -
     * Return : Status Update
     * Return Type : Array
     */
    public function FSaMDocApprove($paParams = [])
    {
        try {
            // TVDTPdtTwxHD
            $this->db->set('FTXthStaPrcStk', '2');
            $this->db->set('FTXthStaApv', '2');
            $this->db->set('FTXthApvCode', $paParams['tApvCode']);
            $this->db->where('FTXthDocNo', $paParams['tDocNo']);

            $this->db->update('TVDTPdtTwxHD');
            if ($this->db->affected_rows() > 0) {
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Approve Success',
                );
            } else {
                $aStatus = array(
                    'rtCode' => '903',
                    'rtDesc' => 'Approve Fail',
                );
            }
            return $aStatus;
        } catch (Exception $Error) {
            return $Error;
        }
    }

    /**
     * Functionality : Del Document by DocNo
     * Parameters : function parameters
     * Creator : 04/02/2020 Piya
     * Return : Status Delete
     * Return Type : array
     */
    public function FSaMDelMaster($paParams = [])
    {
        try {
            $tDocNo = $paParams['tDocNo'];

            $this->db->where('FTXthDocNo', $tDocNo);
            $this->db->delete('TVDTPdtTwxHD');

            $this->db->where('FTXthDocNo', $tDocNo);
            $this->db->delete('TVDTPdtTwxDT');

            $this->db->where('FTXthDocNo', $tDocNo);
            $this->db->delete('TVDTPdtTwxHDRef');
        } catch (Exception $Error) {
            return $Error;
        }
    }
}
