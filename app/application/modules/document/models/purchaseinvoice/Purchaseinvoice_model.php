<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Purchaseinvoice_model extends CI_Model {

    // Functionality: Get Data Purchase Invoice HD List
    // Parameters: function parameters
    // Creator:  19/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified: -
    // Return: Data Array
    // Return Type: Array
    public function FSaMPIGetDataTableList($paDataCondition){
        $aRowLen                = FCNaHCallLenData($paDataCondition['nRow'],$paDataCondition['nPage']);
        $nLngID                 = $paDataCondition['FNLngID'];
        $aDatSessionUserLogIn   = $paDataCondition['aDatSessionUserLogIn'];
        $aAdvanceSearch         = $paDataCondition['aAdvanceSearch'];
        // Advance Search
        $tSearchList        = $aAdvanceSearch['tSearchAll'];
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tSearchStaDoc      = $aAdvanceSearch['tSearchStaDoc'];
        $tSearchStaApprove  = $aAdvanceSearch['tSearchStaApprove'];
        $tSearchStaPrcStk   = $aAdvanceSearch['tSearchStaPrcStk'];

        $tSQL = "   
            SELECT 
                c.* 
            FROM(
                SELECT  
                    ROW_NUMBER() OVER(ORDER BY FDCreateOn DESC , FTXphDocNo DESC) AS FNRowID,
                    * 
                FROM
                    (SELECT DISTINCT
                        PIHD.FTBchCode,
                        BCHL.FTBchName,
                        PIHD.FTXphDocNo,
                        CONVERT(CHAR(10),PIHD.FDXphDocDate,103) AS FDXphDocDate,
                        CONVERT(CHAR(5), PIHD.FDXphDocDate,108) AS FTXphDocTime,
                        PIHD.FTXphStaDoc,
                        PIHD.FTXphStaApv,
                        PIHD.FTXphStaPrcStk,
                        PIHD.FTCreateBy,
                        USRL.FTUsrName      AS FTCreateByName,
                        PIHD.FTXphApvCode,
                        USRLAPV.FTUsrName   AS FTXphApvName,
                        PIHD.FDCreateOn
                    FROM TAPTPiHD           PIHD    WITH (NOLOCK)
                    LEFT JOIN TCNMBranch_L  BCHL    WITH (NOLOCK) ON PIHD.FTBchCode     = BCHL.FTBchCode    AND BCHL.FNLngID    = $nLngID
                    LEFT JOIN TCNMUser_L    USRL    WITH (NOLOCK) ON PIHD.FTCreateBy    = USRL.FTUsrCode    AND USRL.FNLngID    = $nLngID
                    LEFT JOIN TCNMUser_L    USRLAPV WITH (NOLOCK) ON PIHD.FTXphApvCode  = USRLAPV.FTUsrCode AND USRLAPV.FNLngID = $nLngID
                    WHERE 1=1
        ";

        // Check User Login Branch
        if(isset($aDatSessionUserLogIn['FTBchCode']) && !empty($aDatSessionUserLogIn['FTBchCode'])){
            $tUserLoginBchCode  = $aDatSessionUserLogIn['FTBchCode'];
            $tSQL   .= " AND PIHD.FTBchCode = '$tUserLoginBchCode' ";
        }

        // Check User Login Shop
        if(isset($aDatSessionUserLogIn['FTShpCode']) && !empty($aDatSessionUserLogIn['FTShpCode'])){
            $tUserLoginShpCode  = $aDatSessionUserLogIn['FTShpCode'];
            $tSQL   .= " AND PIHD.FTShpCode = '$tUserLoginShpCode' ";
        }

        // นหารหัสเอกสาร,ชือสาขา,วันที่เอกสาร
        if(isset($tSearchList) && !empty($tSearchList)){
            $tSQL .= " AND ((PIHD.FTXphDocNo LIKE '%$tSearchList%') OR (BCHL.FTBchName LIKE '%$tSearchList%') OR (CONVERT(CHAR(10),PIHD.FDXphDocDate,103) LIKE '%$tSearchList%'))";
        }
        
        // ค้นหาจากสาขา - ถึงสาขา
        if(!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)){
            $tSQL .= " AND ((PIHD.FTBchCode BETWEEN $tSearchBchCodeFrom AND $tSearchBchCodeTo) OR (PIHD.FTBchCode BETWEEN $tSearchBchCodeTo AND $tSearchBchCodeFrom))";
        }

        // ค้นหาจากวันที่ - ถึงวันที่
        if(!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)){
            $tSQL .= " AND ((PIHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (PIHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 23:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 00:00:00')))";
        }

        // ค้นหาสถานะเอกสาร
        if(isset($tSearchStaDoc) && !empty($tSearchStaDoc)){
            if($tSearchStaDoc == 2){
                $tSQL .= " AND PIHD.FTXphStaDoc = '$tSearchStaDoc' OR PIHD.FTXphStaDoc = ''";
            }else{
                $tSQL .= " AND PIHD.FTXphStaDoc = '$tSearchStaDoc'";
            }
        }

        // ค้นหาสถานะอนุมัติ
        if(isset($tSearchStaApprove) && !empty($tSearchStaApprove)){
            if($tSearchStaApprove == 2){
                $tSQL .= " AND PIHD.FTXphStaApv = '$tSearchStaApprove' OR PIHD.FTXphStaApv = '' ";
            }else{
                $tSQL .= " AND PIHD.FTXphStaApv = '$tSearchStaApprove'";
            }
        }

        // ค้นหาสถานะประมวลผล
        if(!empty($tSearchStaPrcStk) && ($tSearchStaPrcStk != "0")){
            if($tSearchStaPrcStk == 3){
                $tSQL .= " AND PIHD.FTXphStaPrcStk = '$tSearchStaPrcStk' OR PIHD.FTXphStaPrcStk = '' ";
            }else{
                $tSQL .= " AND PIHD.FTXphStaPrcStk = '$tSearchStaPrcStk'";
            }
        }

        $tSQL   .=  ") Base) AS c WHERE c.FNRowID > $aRowLen[0] AND c.FNRowID <= $aRowLen[1]";

        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $oDataList          = $oQuery->result_array();
            $aDataCountAllRow   = $this->FSnMPICountPageDocListAll($paDataCondition);
            $nFoundRow          = ($aDataCountAllRow['rtCode'] == '1')? $aDataCountAllRow['rtCountData'] : 0;
            $nPageAll           = ceil($nFoundRow/$paDataCondition['nRow']);
            $aResult = array(
                'raItems'       => $oDataList,
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paDataCondition['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aResult = array(
                'rnAllRow'      => 0,
                'rnCurrentPage' => $paDataCondition['nPage'],
                "rnAllPage"     => 0,
                'rtCode'        => '800',
                'rtDesc'        => 'data not found',
            );
        }
        unset($oQuery);
        unset($oDataList);
        unset($aDataCountAllRow);
        unset($nFoundRow);
        unset($nPageAll);
        return $aResult;
    }

    // Functionality: Data Get Data Page All
    // Parameters: function parameters
    // Creator:  19/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified: -
    // Return: Data Array
    // Return Type: Array
    public function FSnMPICountPageDocListAll($paDataCondition){
        $nLngID                 = $paDataCondition['FNLngID'];
        $aDatSessionUserLogIn   = $paDataCondition['aDatSessionUserLogIn'];
        $aAdvanceSearch         = $paDataCondition['aAdvanceSearch'];
        // Advance Search
        $tSearchList        = $aAdvanceSearch['tSearchAll'];
        $tSearchBchCodeFrom = $aAdvanceSearch['tSearchBchCodeFrom'];
        $tSearchBchCodeTo   = $aAdvanceSearch['tSearchBchCodeTo'];
        $tSearchDocDateFrom = $aAdvanceSearch['tSearchDocDateFrom'];
        $tSearchDocDateTo   = $aAdvanceSearch['tSearchDocDateTo'];
        $tSearchStaDoc      = $aAdvanceSearch['tSearchStaDoc'];
        $tSearchStaApprove  = $aAdvanceSearch['tSearchStaApprove'];
        $tSearchStaPrcStk   = $aAdvanceSearch['tSearchStaPrcStk'];

        $tSQL   =   "   SELECT COUNT (PIHD.FTXphDocNo) AS counts
                        FROM TAPTPiHD PIHD WITH (NOLOCK)
                        LEFT JOIN TCNMBranch_L BCHL WITH (NOLOCK) ON PIHD.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID
                        WHERE 1=1
                    ";

        // Check User Login Branch
        if(isset($aDatSessionUserLogIn['FTBchCode']) && !empty($aDatSessionUserLogIn['FTBchCode'])){
            $tUserLoginBchCode  = $aDatSessionUserLogIn['FTBchCode'];
            $tSQL   .= " AND PIHD.FTBchCode = '$tUserLoginBchCode' ";
        }

        // Check User Login Shop
        if(isset($aDatSessionUserLogIn['FTShpCode']) && !empty($aDatSessionUserLogIn['FTShpCode'])){
            $tUserLoginShpCode  = $aDatSessionUserLogIn['FTShpCode'];
            $tSQL   .= " AND PIHD.FTShpCode = '$tUserLoginShpCode' ";
        }
        
        // นหารหัสเอกสาร,ชือสาขา,วันที่เอกสาร
        if(isset($tSearchList) && !empty($tSearchList)){
            $tSQL .= " AND ((PIHD.FTXphDocNo LIKE '%$tSearchList%') OR (BCHL.FTBchName LIKE '%$tSearchList%') OR (CONVERT(CHAR(10),PIHD.FDXphDocDate,103) LIKE '%$tSearchList%'))";
        }
        
        // ค้นหาจากสาขา - ถึงสาขา
        if(!empty($tSearchBchCodeFrom) && !empty($tSearchBchCodeTo)){
            $tSQL .= " AND ((PIHD.FTBchCode BETWEEN $tSearchBchCodeFrom AND $tSearchBchCodeTo) OR (PIHD.FTBchCode BETWEEN $tSearchBchCodeTo AND $tSearchBchCodeFrom))";
        }

        // ค้นหาจากวันที่ - ถึงวันที่
        if(!empty($tSearchDocDateFrom) && !empty($tSearchDocDateTo)){
            $tSQL .= " AND ((PIHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateFrom 00:00:00') AND CONVERT(datetime,'$tSearchDocDateTo 23:59:59')) OR (PIHD.FDXphDocDate BETWEEN CONVERT(datetime,'$tSearchDocDateTo 23:00:00') AND CONVERT(datetime,'$tSearchDocDateFrom 00:00:00')))";
        }

        // ค้นหาสถานะเอกสาร
        if(isset($tSearchStaDoc) && !empty($tSearchStaDoc)){
            if($tSearchStaDoc == 2){
                $tSQL .= " AND PIHD.FTXphStaDoc = '$tSearchStaDoc' OR PIHD.FTXphStaDoc = ''";
            }else{
                $tSQL .= " AND PIHD.FTXphStaDoc = '$tSearchStaDoc'";
            }
        }

        // ค้นหาสถานะอนุมัติ
        if(isset($tSearchStaApprove) && !empty($tSearchStaApprove)){
            if($tSearchStaApprove == 2){
                $tSQL .= " AND PIHD.FTXphStaApv = '$tSearchStaApprove' OR PIHD.FTXphStaApv = '' ";
            }else{
                $tSQL .= " AND PIHD.FTXphStaApv = '$tSearchStaApprove'";
            }
        }

        // ค้นหาสถานะประมวลผล
        if(!empty($tSearchStaPrcStk) && ($tSearchStaPrcStk != "0")){
            if($tSearchStaPrcStk == 3){
                $tSQL .= " AND PIHD.FTXphStaPrcStk = '$tSearchStaPrcStk' OR PIHD.FTXphStaPrcStk = '' ";
            }else{
                $tSQL .= " AND PIHD.FTXphStaPrcStk = '$tSearchStaPrcStk'";
            }
        }
        
        $oQuery = $this->db->query($tSQL);

        if($oQuery->num_rows() > 0) {
            $aDetail        = $oQuery->row_array();
            $aDataReturn    =  array(
                'rtCountData'   => $aDetail['counts'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aDataReturn    =  array(
                'rtCode'        => '800',
                'rtDesc'        => 'Data Not Found',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aDataReturn;
    }

    // Functionality : Delete Purchase Invoice Document
    // Parameters : function parameters
    // Creator : 19/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified : -
    // Return : Array Status Delete
    // Return Type : array
    public function FSnMPIDelDocument($paDataDoc){
        $tDataDocNo = $paDataDoc['tDataDocNo'];
        $this->db->trans_begin();

        // Document HD
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPiHD');
    
        // Document HD Spl
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPiHDSpl');

        // Document HD Discount
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPiHDDis');
        
        // Document DT
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPiDT');

        // Document DT Discount
        $this->db->where_in('FTXphDocNo',$tDataDocNo);
        $this->db->delete('TAPTPiDTDis');

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aStaDelDoc     = array(
                'rtCode'    => '905',
                'rtDesc'    => 'Cannot Delete Item.',
            );
        }else{
            $this->db->trans_commit();
            $aStaDelDoc     = array(
                'rtCode'    => '1',
                'rtDesc'    => 'Delete Complete.',
            );
        }
        return $aStaDelDoc;
    }

    // Functionality : Delete Purchase Invoice Document
    // Parameters : function parameters
    // Creator : 24/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified : -
    // Return : Array Status Delete
    // Return Type : array
    public function FSxMPIClearDataInDocTemp($paWhereClearTemp){
        $tPIDocNo       = $paWhereClearTemp['FTXthDocNo'];
        $tPiDocKey      = $paWhereClearTemp['FTXthDocKey'];
        $tPISessionID   = $paWhereClearTemp['FTSessionID'];

        // Query Delete DocTemp
        $tClearDocTemp  =   "   DELETE FROM TCNTDocDTTmp 
                                WHERE 1=1 
                                AND TCNTDocDTTmp.FTXthDocNo     = '$tPIDocNo'
                                AND TCNTDocDTTmp.FTXthDocKey    = '$tPiDocKey'
                                AND TCNTDocDTTmp.FTSessionID    = '$tPISessionID'
        ";
        $this->db->query($tClearDocTemp);

        // Query Delete Doc HD Discount Temp
        $tClearDocHDDisTemp =   "   DELETE FROM TCNTDocHDDisTmp
                                    WHERE 1=1
                                    AND TCNTDocHDDisTmp.FTXthDocNo  = '$tPIDocNo'
                                    AND TCNTDocHDDisTmp.FTSessionID = '$tPISessionID'
        ";
        $this->db->query($tClearDocHDDisTemp);

        // Query Delete Doc DT Discount Temp
        $tClearDocDTDisTemp =   "   DELETE FROM TCNTDocDTDisTmp
                                    WHERE 1=1
                                    AND TCNTDocDTDisTmp.FTXthDocNo  = '$tPIDocNo'
                                    AND TCNTDocDTDisTmp.FTSessionID = '$tPISessionID'
        ";
        $this->db->query($tClearDocDTDisTemp);
    
    }

    // Functionality: Get ShopCode From User Login
    // Parameters: function parameters
    // Creator: 24/06/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified: -
    // Return: Array Data Shop For User Login
    // ReturnType: array
    public function FSaPIGetShpCodeForUsrLogin($paDataShp){
        $nLngID     = $paDataShp['FNLngID'];
        $tUsrLogin  = $paDataShp['tUsrLogin'];
        $tSQL       = " SELECT
                            UGP.FTBchCode,
                            BCHL.FTBchName,
                            MER.FTMerCode,
                            MERL.FTMerName,
                            UGP.FTShpCode,
                            SHPL.FTShpName,
                            SHP.FTShpType,
                            SHP.FTWahCode   AS FTWahCode,
                            WAHL.FTWahName  AS FTWahName
                        FROM TCNTUsrGroup           UGP     WITH (NOLOCK)
                        LEFT JOIN TCNMBranch        BCH     WITH (NOLOCK) ON UGP.FTBchCode = BCH.FTBchCode 
                        LEFT JOIN TCNMBranch_L      BCHL    WITH (NOLOCK) ON UGP.FTBchCode = BCHL.FTBchCode AND BCHL.FNLngID = $nLngID
                        LEFT JOIN TCNMShop          SHP     WITH (NOLOCK) ON UGP.FTShpCode = SHP.FTShpCode
                        LEFT JOIN TCNMShop_L        SHPL    WITH (NOLOCK) ON SHP.FTShpCode = SHPL.FTShpCode AND SHP.FTBchCode = SHPL.FTBchCode AND SHPL.FNLngID = $nLngID
                        LEFT JOIN TCNMMerchant		MER		WITH (NOLOCK)	ON SHP.FTMerCode	= MER.FTMerCode
                        LEFT JOIN TCNMMerchant_L    MERL    WITH (NOLOCK) ON SHP.FTMerCode = MERL.FTMerCode AND MERL.FNLngID = $nLngID
                        LEFT JOIN TCNMWaHouse_L     WAHL    WITH (NOLOCK) ON SHP.FTWahCode = WAHL.FTWahCode AND SHP.FTBchCode = WAHL.FTBchCode
                        WHERE UGP.FTUsrCode = '$tUsrLogin' ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = $oQuery->row_array();
        }else{
            $aResult    = "";
        }
        unset($oQuery);
        return $aResult;
    }

    // Functionality: Get Data Config WareHouse TSysConfig
    // Parameters: function parameters
    // Creator: 25/07/2018 wasin (Yoshi AKA: Mr.JW)
    // Last Modified: -
    // Return: Array Data Default Config WareHouse
    // ReturnType: array
    public function FSaMPIGetDefOptionConfigWah($paConfigSys){
        $tSysCode       = $paConfigSys['FTSysCode'];
        $nSysSeq        = $paConfigSys['FTSysSeq'];
        $nLngID         = $paConfigSys['FNLngID'];
        $aDataReturn    = array();

        $tSQLUsrVal = " SELECT
                            SYSCON.FTSysStaUsrValue AS FTSysWahCode,
                            WAHL.FTWahName          AS FTSysWahName
                        FROM TSysConfig SYSCON          WITH(NOLOCK)
                        LEFT JOIN TCNMWaHouse   WAH     WITH(NOLOCK)    ON SYSCON.FTSysStaUsrValue  = WAH.FTWahCode     AND WAH.FTWahStaType = 1
                        LEFT JOIN TCNMWaHouse_L WAHL    WITH(NOLOCK)    ON WAH.FTWahCode            = WAHL.FTWahCode    AND WAHL.FNLngID = $nLngID
                        WHERE 1=1
                        AND SYSCON.FTSysCode    = '$tSysCode'
                        AND SYSCON.FTSysSeq     = $nSysSeq
        ";
        $oQuery1    = $this->db->query($tSQLUsrVal);
        if($oQuery1->num_rows() > 0){
            $aDataReturn    = $oQuery1->row_array();
        }else{
            $tSQLUsrDef =   "   SELECT
                                    SYSCON.FTSysStaDefValue AS FTSysWahCode,
                                    WAHL.FTWahName          AS FTSysWahName
                        FROM TSysConfig SYSCON          WITH(NOLOCK)
                        LEFT JOIN TCNMWaHouse   WAH     WITH(NOLOCK)    ON SYSCON.FTSysStaDefValue  = WAH.FTWahCode     AND WAH.FTWahStaType = 1
                        LEFT JOIN TCNMWaHouse_L WAHL    WITH(NOLOCK)    ON WAH.FTWahCode            = WAHL.FTWahCode    AND WAHL.FNLngID = $nLngID
                        WHERE 1=1
                        AND SYSCON.FTSysCode    = '$tSysCode'
                        AND SYSCON.FTSysSeq     = $nSysSeq
            ";
            $oQuery2    = $this->db->query($tSQLUsrDef);
            if($oQuery2->num_rows() > 0){
                $aDataReturn    = $oQuery2->row_array();
            }
        }
        unset($oQuery1);
        unset($oQuery2);
        return $aDataReturn;
    }

    // Functionality: Get Data In Doc DT Temp
    // Parameters: function parameters
    // Creator: 01/07/2019 wasin (Yoshi)
    // Last Modified: -
    // Return: Array Data Doc DT Temp
    // ReturnType: array
    public function FSaMPIGetDocDTTempListPage($paDataWhere){
        $tPIDocNo           = $paDataWhere['FTXthDocNo'];
        $tPIDocKey          = $paDataWhere['FTXthDocKey'];
        $tSearchPdtAdvTable = $paDataWhere['tSearchPdtAdvTable'];
        $tPISesSessionID    = $this->session->userdata('tSesSessionID');

        $aRowLen    = FCNaHCallLenData($paDataWhere['nRow'],$paDataWhere['nPage']);

        $tSQL       = " SELECT c.* FROM(
                            SELECT  ROW_NUMBER() OVER(ORDER BY FNXtdSeqNo ASC) AS rtRowID,* FROM (
                                SELECT
                                    DOCTMP.FTBchCode,
                                    DOCTMP.FTXthDocNo,
                                    DOCTMP.FNXtdSeqNo,
                                    DOCTMP.FTXthDocKey,
                                    DOCTMP.FTPdtCode,
                                    DOCTMP.FTXtdPdtName,
                                    DOCTMP.FTPunName,
                                    DOCTMP.FTXtdBarCode,
                                    DOCTMP.FTPunCode,
                                    DOCTMP.FCXtdFactor,
                                    DOCTMP.FCXtdQty,
                                    DOCTMP.FCXtdSetPrice,
                                    DOCTMP.FCXtdAmtB4DisChg,
                                    DOCTMP.FTXtdDisChgTxt,
                                    DOCTMP.FCXtdNet,
                                    DOCTMP.FCXtdNetAfHD,
                                    DOCTMP.FTXtdStaAlwDis,
                                    DOCTMP.FDLastUpdOn,
                                    DOCTMP.FDCreateOn,
                                    DOCTMP.FTLastUpdBy,
                                    DOCTMP.FTCreateBy
                                FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                                WHERE 1 = 1
                                AND DOCTMP.FTXthDocNo  = '$tPIDocNo'
                                AND DOCTMP.FTXthDocKey = '$tPIDocKey'
                                AND DOCTMP.FTSessionID = '$tPISesSessionID' ";
                                
        if(isset($tSearchPdtAdvTable) && !empty($tSearchPdtAdvTable)){
            $tSQL   .=  "   AND (
                                DOCTMP.FTPdtCode COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%'
                                OR DOCTMP.FTXtdPdtName COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%'
                                OR DOCTMP.FTXtdBarCode COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%'
                                OR DOCTMP.FTPunName COLLATE THAI_BIN LIKE '%$tSearchPdtAdvTable%' )
                        ";
            
        }
        $tSQL   .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
        $oQuery = $this->db->query($tSQL);

        if($oQuery->num_rows() > 0){
            $aDataList  = $oQuery->result_array();
            $aFoundRow  = $this->FSaMPIGetDocDTTempListPageAll($paDataWhere);
            $nFoundRow  = ($aFoundRow['rtCode'] == '1')? $aFoundRow['rtCountData'] : 0;
            $nPageAll   = ceil($nFoundRow/$paDataWhere['nRow']);
            $aDataReturn    = array(
                'raItems'       => $aDataList,
                'rnAllRow'      => $nFoundRow,
                'rnCurrentPage' => $paDataWhere['nPage'],
                'rnAllPage'     => $nPageAll,
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aDataReturn    = array(
                'rnAllRow'      => 0,
                'rnCurrentPage' => $paDataWhere['nPage'],
                "rnAllPage"     => 0,
                'rtCode'        => '800',
                'rtDesc'        => 'data not found',
            );
        }
        unset($oQuery);
        unset($aDataList);
        unset($aFoundRow);
        unset($nFoundRow);
        unset($nPageAll);
        return $aDataReturn;
    }   

    // Functionality : Count All Documeny DT Temp
    // Parameters : function parameters
    // Creator : 01/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array Data Count All Data
    // Return Type : array
    public function FSaMPIGetDocDTTempListPageAll($paDataWhere){
        $tPIDocNo           = $paDataWhere['FTXthDocNo'];
        $tPIDocKey          = $paDataWhere['FTXthDocKey'];
        $tSearchPdtAdvTable = $paDataWhere['tSearchPdtAdvTable'];
        $tPISesSessionID    = $this->session->userdata('tSesSessionID');

        $tSQL   = " SELECT COUNT (DOCTMP.FTXthDocNo) AS counts
                    FROM TCNTDocDTTmp DOCTMP
                    WHERE 1 = 1 ";
        
        $tSQL   .= " AND DOCTMP.FTXthDocNo  = '$tPIDocNo' ";
        $tSQL   .= " AND DOCTMP.FTXthDocKey = '$tPIDocKey' ";
        $tSQL   .= " AND DOCTMP.FTSessionID = '$tPISesSessionID' ";

        // if(isset($tSearchPdtAdvTable) && !empty($tSearchPdtAdvTable)){
        //     $tSQL   .= " AND ( DOCTMP.FTPdtCode LIKE '%$tSearchPdtAdvTable%' ";
        //     $tSQL   .= " OR DOCTMP.FTXtdPdtName LIKE '%$tSearchPdtAdvTable%' ";
        //     $tSQL   .= " OR DOCTMP.FTPunName    LIKE '%$tSearchPdtAdvTable%' ";
        //     $tSQL   .= " OR DOCTMP.FTXtdBarCode LIKE '%$tSearchPdtAdvTable%' ";
        // }
        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0) {
            $aDetail        = $oQuery->row_array();
            $aDataReturn    =  array(
                'rtCountData'   => $aDetail['counts'],
                'rtCode'        => '1',
                'rtDesc'        => 'success',
            );
        }else{
            $aDataReturn    =  array(
                'rtCode'    => '800',
                'rtDesc'    => 'Data Not Found',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aDataReturn;
    }

    // Functionality : Function Sum Amount DT Temp
    // Parameters : function parameters
    // Creator : 01/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPISumDocDTTemp($paDataWhere){
        $tPIDocNo           = $paDataWhere['FTXthDocNo'];
        $tPIDocKey          = $paDataWhere['FTXthDocKey'];
        $tPISesSessionID    = $this->session->userdata('tSesSessionID');
        $tSQL               = " SELECT
                                    SUM(FCXtdNetAfHD)       AS FCXtdSumNetAfHD,
                                    SUM(FCXtdAmtB4DisChg)   AS FCXtdSumAmtB4DisChg
                                FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                                WHERE 1 = 1 ";
        $tSQL   .= " AND DOCTMP.FTXthDocNo  = '$tPIDocNo' ";
        $tSQL   .= " AND DOCTMP.FTXthDocKey = '$tPIDocKey' ";
        $tSQL   .= " AND DOCTMP.FTSessionID = '$tPISesSessionID' ";
        
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $aResult    = $oQuery->row_array();
            $aDataReturn    =  array(
                'raDataSum' => $aResult,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aDataReturn    =  array(
                'rtCode'    => '800',
                'rtDesc'    => 'Data Sum Empty',
            );
        }
        unset($oQuery);
        unset($aResult);
        return $aDataReturn;
    }

    // Functionality : Function Get Max Seq From Doc DT Temp
    // Parameters : function parameters
    // Creator : 02/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPIGetMaxSeqDocDTTemp($paDataWhere){
        $tPIBchCode         = $paDataWhere['FTBchCode'];
        $tPIDocNo           = $paDataWhere['FTXthDocNo'];
        $tPIDocKey          = $paDataWhere['FTXthDocKey'];
        $tPISesSessionID    = $this->session->userdata('tSesSessionID');
        $tSQL   =   "   SELECT 
                            MAX(DOCTMP.FNXtdSeqNo) AS rnMaxSeqNo
                        FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                        WHERE 1 = 1 ";
        $tSQL   .= " AND DOCTMP.FTBchCode   = '$tPIBchCode'";
        $tSQL   .= " AND DOCTMP.FTXthDocNo  = '$tPIDocNo'";
        $tSQL   .= " AND DOCTMP.FTXthDocKey = '$tPIDocKey'";
        $tSQL   .= " AND DOCTMP.FTSessionID = '$tPISesSessionID'";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail    = $oQuery->row_array();
            $nResult    = $aDetail['rnMaxSeqNo'];
        }else{
            $nResult    = 0;
        }
        return empty($nResult)? 0 : $nResult;
    }

    // Functionality : Get Data Pdt
    // Parameters : function parameters
    // Creator : 02/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPIGetDataPdt($paDataPdtParams){
        $tPdtCode   = $paDataPdtParams['tPdtCode'];
        $FTPunCode  = $paDataPdtParams['tPunCode'];
        $FTBarCode  = $paDataPdtParams['tBarCode'];
        $nLngID     = $paDataPdtParams['nLngID'];
        $tSQL       = " SELECT
                            PDT.FTPdtCode,
                            PDT.FTPdtStkControl,
                            PDT.FTPdtGrpControl,
                            PDT.FTPdtForSystem,
                            PDT.FCPdtQtyOrdBuy,
                            PDT.FCPdtCostDef,
                            PDT.FCPdtCostOth,
                            PDT.FCPdtCostStd,
                            PDT.FCPdtMin,
                            PDT.FCPdtMax,
                            PDT.FTPdtPoint,
                            PDT.FCPdtPointTime,
                            PDT.FTPdtType,
                            PDT.FTPdtSaleType,
                            ISNULL(PRI4PDT.FCPgdPriceRet,0) AS FTPdtSalePrice,
                            PDT.FTPdtSetOrSN,
                            PDT.FTPdtStaSetPri,
                            PDT.FTPdtStaSetShwDT,
                            PDT.FTPdtStaAlwDis,
                            PDT.FTPdtStaAlwReturn,
                            PDT.FTPdtStaVatBuy,
                            PDT.FTPdtStaVat,
                            PDT.FTPdtStaActive,
                            PDT.FTPdtStaAlwReCalOpt,
                            PDT.FTPdtStaCsm,
                            PDT.FTTcgCode,
                            PDT.FTPtyCode,
                            PDT.FTPbnCode,
                            PDT.FTPmoCode,
                            PDT.FTVatCode,
                            PDT.FDPdtSaleStart,
                            PDT.FDPdtSaleStop,
                            PDTL.FTPdtName,
                            PDTL.FTPdtNameOth,
                            PDTL.FTPdtNameABB,
                            PDTL.FTPdtRmk,
                            PKS.FTPunCode,
                            PKS.FCPdtUnitFact,
                            VAT.FCVatRate,
                            UNTL.FTPunName,
                            BAR.FTBarCode,
                            BAR.FTPlcCode,
                            PDTLOCL.FTPlcName,
                            PDTSRL.FTSrnCode,
                            PDT.FCPdtCostStd,
                            CAVG.FCPdtCostEx,
                            CAVG.FCPdtCostIn,
                            SPL.FCSplLastPrice
                        FROM TCNMPdt PDT WITH (NOLOCK)
                        LEFT JOIN TCNMPdt_L PDTL        WITH (NOLOCK)   ON PDT.FTPdtCode      = PDTL.FTPdtCode    AND PDTL.FNLngID    = $nLngID
                        LEFT JOIN TCNMPdtPackSize  PKS  WITH (NOLOCK)   ON PDT.FTPdtCode      = PKS.FTPdtCode     AND PKS.FTPunCode   = '$FTPunCode'
                        LEFT JOIN TCNMPdtUnit_L UNTL    WITH (NOLOCK)   ON UNTL.FTPunCode     = '$FTPunCode'      AND UNTL.FNLngID    = $nLngID
                        LEFT JOIN TCNMPdtBar BAR        WITH (NOLOCK)   ON PKS.FTPdtCode      = BAR.FTPdtCode     AND BAR.FTPunCode   = '$FTPunCode'
                        LEFT JOIN TCNMPdtLoc_L PDTLOCL  WITH (NOLOCK)   ON PDTLOCL.FTPlcCode  = BAR.FTPlcCode     AND PDTLOCL.FNLngID = $nLngID
                        LEFT JOIN (
                            SELECT DISTINCT
                                FTVatCode,
                                FCVatRate,
                                FDVatStart
                            FROM TCNMVatRate WITH (NOLOCK)
                            WHERE CONVERT(VARCHAR(19),GETDATE(),121) > FDVatStart ) VAT
                        ON PDT.FTVatCode = VAT.FTVatCode
                        LEFT JOIN TCNTPdtSerial PDTSRL  WITH (NOLOCK)   ON PDT.FTPdtCode    = PDTSRL.FTPdtCode
                        LEFT JOIN TCNMPdtSpl SPL        WITH (NOLOCK)   ON PDT.FTPdtCode    = SPL.FTPdtCode AND BAR.FTBarCode = SPL.FTBarCode
                        LEFT JOIN TCNMPdtCostAvg CAVG   WITH (NOLOCK)   ON PDT.FTPdtCode    = CAVG.FTPdtCode
                        LEFT JOIN (
                            SELECT DISTINCT
                                P4PDT.FTPdtCode,
                                P4PDT.FTPunCode,
                                P4PDT.FDPghDStart,
                                P4PDT.FTPghTStart,
                                P4PDT.FCPgdPriceRet,
                                P4PDT.FCPgdPriceWhs,
                                P4PDT.FCPgdPriceNet
                            FROM TCNTPdtPrice4PDT P4PDT WITH (NOLOCK)
                            WHERE 1=1
                            AND (CONVERT(VARCHAR(10),GETDATE(),121) >= CONVERT(VARCHAR(10),P4PDT.FDPghDStart,121))
                            AND (CONVERT(VARCHAR(10),GETDATE(),121) <= CONVERT(VARCHAR(10),P4PDT.FDPghDStop,121))
                        ) AS PRI4PDT
                        ON PDT.FTPdtCode = PRI4PDT.FTPdtCode AND PRI4PDT.FTPunCode = PKS.FTPunCode
                        WHERE 1 = 1 ";
    
        if(isset($tPdtCode) && !empty($tPdtCode)){
            $tSQL   .= " AND PDT.FTPdtCode   = '$tPdtCode'";
        }

        if(isset($FTBarCode) && !empty($FTBarCode)){
            $tSQL   .= " AND BAR.FTBarCode = '$FTBarCode'";
        }

        $tSQL   .= " ORDER BY FDVatStart DESC";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail    = $oQuery->row_array();
            $aResult    = array(
                'raItem'    => $aDetail,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aResult = array(
                'rtCode' => '800',
                'rtDesc' => 'data not found.',
            );
        }
        unset($oQuery);
        unset($aDetail);
        return $aResult;
    }

    // Functionality : Insert Pdt To Doc DT Temp
    // Parameters : function parameters
    // Creator : 02/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPIInsertPDTToTemp($paDataPdtMaster,$paDataPdtParams){
        $paPIDataPdt    = $paDataPdtMaster['raItem'];
        if($paDataPdtParams['tPIOptionAddPdt'] == 1){
            // นำสินค้าเพิ่มจำนวนในแถวแรก
            $tSQL   =   "   SELECT
                                FNXtdSeqNo, 
                                FCXtdQty
                            FROM TCNTDocDTTmp
                            WHERE 1=1 
                            AND FTXthDocNo      = '".$paDataPdtParams['tDocNo']."'
                            AND FTBchCode       = '".$paDataPdtParams['tBchCode']."'
                            AND FTXthDocKey     = '".$paDataPdtParams['tDocKey']."'
                            AND FTSessionID     = '".$paDataPdtParams['tSessionID']."'
                            AND FTPdtCode       = '".$paPIDataPdt["FTPdtCode"]."'
                            AND FTXtdBarCode    = '".$paPIDataPdt["FTBarCode"]."'
                            ORDER BY FNXtdSeqNo
                        ";
            $oQuery = $this->db->query($tSQL);
            if($oQuery->num_rows() > 0){
                // เพิ่มจำนวนให้รายการที่มีอยู่แล้ว
                $aResult    = $oQuery->row_array();
                $tSQL       =   "   UPDATE TCNTDocDTTmp
                                    SET FCXtdQty = '".($aResult["FCXtdQty"] + 1 )."'
                                    WHERE 1=1
                                    AND FTBchCode       = '".$paDataPdtParams['tBchCode']."'
                                    AND FTXthDocNo      = '".$paDataPdtParams['tDocNo']."'
                                    AND FNXtdSeqNo      = '".$aResult["FNXtdSeqNo"]."'
                                    AND FTXthDocKey     = '".$paDataPdtParams['tDocKey']."'
                                    AND FTSessionID     = '".$paDataPdtParams['tSessionID']."'
                                    AND FTPdtCode       = '".$paPIDataPdt["FTPdtCode"]."'
                                    AND FTXtdBarCode    = '".$paPIDataPdt["FTBarCode"]."'
                                ";
                $this->db->query($tSQL);
                $aStatus = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Add Success.',
                );
            }else{
                // เพิ่มรายการใหม่
                $aDataInsert    = array(
                    'FTBchCode'         => $paDataPdtParams['tBchCode'],
                    'FTXthDocNo'        => $paDataPdtParams['tDocNo'],
                    'FNXtdSeqNo'        => $paDataPdtParams['nMaxSeqNo'],
                    'FTXthDocKey'       => $paDataPdtParams['tDocKey'],
                    'FTPdtCode'         => $paPIDataPdt['FTPdtCode'],
                    'FTXtdPdtName'      => $paPIDataPdt['FTPdtName'],
                    'FCXtdFactor'       => $paPIDataPdt['FCPdtUnitFact'],
                    'FTPunCode'         => $paPIDataPdt['FTPunCode'],
                    'FTPunName'         => $paPIDataPdt['FTPunName'],
                    'FTXtdBarCode'      => $paDataPdtParams['tBarCode'],
                    'FTXtdVatType'      => $paPIDataPdt['FTPdtStaVatBuy'],
                    'FTVatCode'         => $paPIDataPdt['FTVatCode'],
                    'FCXtdVatRate'      => $paPIDataPdt['FCVatRate'],
                    'FTXtdStaAlwDis'    => $paPIDataPdt['FTPdtStaAlwDis'],
                    'FTXtdSaleType'     => $paPIDataPdt['FTPdtSaleType'],
                    'FCXtdSalePrice'    => $paPIDataPdt['FTPdtSalePrice'],
                    'FCXtdQty'          => 1,
                    'FCXtdQtyAll'       => 1*$paPIDataPdt['FCPdtUnitFact'],
                    'FCXtdSetPrice'     => $paDataPdtParams['cPrice'] * 1,
                    'FTSessionID'       => $paDataPdtParams['tSessionID'],
                    'FDLastUpdOn'       => date('Y-m-d h:i:s'),
                    'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                    'FDCreateOn'        => date('Y-m-d h:i:s'),
                    'FTCreateBy'        => $this->session->userdata('tSesUsername')
                );
                $this->db->insert('TCNTDocDTTmp',$aDataInsert);

                $this->db->last_query();  
                if($this->db->affected_rows() > 0){
                    $aStatus = array(
                        'rtCode'    => '1',
                        'rtDesc'    => 'Add Success.',
                    );
                }else{
                    $aStatus = array(
                        'rtCode'    => '905',
                        'rtDesc'    => 'Error Cannot Add.',
                    );
                }
            }   
        }else{
            // เพิ่มแถวใหม่
            $aDataInsert    = array(
                'FTBchCode'         => $paDataPdtParams['tBchCode'],
                'FTXthDocNo'        => $paDataPdtParams['tDocNo'],
                'FNXtdSeqNo'        => $paDataPdtParams['nMaxSeqNo'],
                'FTXthDocKey'       => $paDataPdtParams['tDocKey'],
                'FTPdtCode'         => $paPIDataPdt['FTPdtCode'],
                'FTXtdPdtName'      => $paPIDataPdt['FTPdtName'],
                'FCXtdFactor'       => $paPIDataPdt['FCPdtUnitFact'],
                'FTPunCode'         => $paPIDataPdt['FTPunCode'],
                'FTPunName'         => $paPIDataPdt['FTPunName'],
                'FTXtdBarCode'      => $paDataPdtParams['tBarCode'],
                'FTXtdVatType'      => $paPIDataPdt['FTPdtStaVatBuy'],
                'FTVatCode'         => $paPIDataPdt['FTVatCode'],
                'FCXtdVatRate'      => $paPIDataPdt['FCVatRate'],
                'FTXtdStaAlwDis'    => $paPIDataPdt['FTPdtStaAlwDis'],
                'FTXtdSaleType'     => $paPIDataPdt['FTPdtSaleType'],
                'FCXtdSalePrice'    => $paPIDataPdt['FTPdtSalePrice'],
                'FCXtdQty'          => 1,
                'FCXtdQtyAll'       => 1*$paPIDataPdt['FCPdtUnitFact'],
                'FCXtdSetPrice'     => $paDataPdtParams['cPrice'] * 1,
                'FTSessionID'       => $paDataPdtParams['tSessionID'],
                'FDLastUpdOn'       => date('Y-m-d h:i:s'),
                'FTLastUpdBy'       => $this->session->userdata('tSesUsername'),
                'FDCreateOn'        => date('Y-m-d h:i:s'),
                'FTCreateBy'        => $this->session->userdata('tSesUsername')
            );
            $this->db->insert('TCNTDocDTTmp',$aDataInsert);
            $this->db->last_query();  
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Add Success.',
                );
            }else{
                $aStatus = array(
                    'rtCode'    => '905',
                    'rtDesc'    => 'Error Cannot Add.',
                );
            }
        }
        return $aStatus;
    }

    // Functionality : Update Document DT Temp by Seq
    // Parameters : function parameters
    // Creator : 02/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPIUpdateInlineDTTemp($paDataUpdateDT,$paDataWhere){
            $this->db->set($paDataUpdateDT['tPIFieldName'], $paDataUpdateDT['tPIValue']);
            $this->db->where('FTSessionID',$paDataWhere['tPISessionID']);
            $this->db->where('FTXthDocKey',$paDataWhere['tDocKey']);
            $this->db->where('FNXtdSeqNo',$paDataWhere['nPISeqNo']);
            $this->db->where('FTXthDocNo',$paDataWhere['tPIDocNo']);
            $this->db->where('FTBchCode',$paDataWhere['tPIBchCode']);
            $this->db->update('TCNTDocDTTmp');
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode'    => '1',
                    'rtDesc'    => 'Update Success',
                );
            }else{
                $aStatus = array(
                    'rtCode'    => '903',
                    'rtDesc'    => 'Update Fail',
                );
            }
            return $aStatus;
    }

    // Functionality : Count Check Data Product In Doc DT Temp Before Save
    // Parameters : function parameters
    // Creator : 03/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Count
    // Return Type : array
    public function FSnMPIChkPdtInDocDTTemp($paDataWhere){
        $tPIDocNo       = $paDataWhere['FTXthDocNo'];
        $tPIDocKey      = $paDataWhere['FTXthDocKey'];
        $tPISessionID   = $paDataWhere['FTSessionID'];
        $tSQL           = " SELECT
                                COUNT(FNXtdSeqNo) AS nCountPdt
                            FROM TCNTDocDTTmp DocDT
                            WHERE 1=1
                            AND DocDT.FTXthDocNo    = '$tPIDocNo'
                            AND DocDT.FTXthDocKey   = '$tPIDocKey'
                            AND DocDT.FTSessionID   = '$tPISessionID' ";
        $oQuery = $this->db->query($tSQL);
        if($oQuery->num_rows() > 0){
            $aDataQuery = $oQuery->row_array();
            return $aDataQuery['nCountPdt'];
        }else{
            return 0;
        }
    }

    // Functionality :  Delete Product Single Item In Doc DT Temp
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Delete
    // Return Type : array
    public function FSnMPIDelPdtInDTTmp($paDataWhere){
        // Delete Doc DT Temp
        $this->db->where_in('FTSessionID',$paDataWhere['tSessionID']);
        $this->db->where_in('FTPdtCode',$paDataWhere['tPdtCode']);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['nSeqNo']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTTmp');

        // Delete Doc DT Temp
        $this->db->where_in('FNXtdStaDis',1);
        $this->db->where_in('FTSessionID',$paDataWhere['tSessionID']);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['nSeqNo']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTDisTmp');
        return ;
    }

    // Functionality : Delete Product Multiple Items In Doc DT Temp
    // Parameters : function parameters
    // Creator : 30/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Delete
    // Return Type : array
    public function FSnMPIDelMultiPdtInDTTmp($paDataWhere){
        $tSessionID = $this->session->userdata('tSesSessionID');

        // Delete Doc DT Temp
        $this->db->where_in('FTSessionID',$tSessionID);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['aDataSeqNo']);
        $this->db->where_in('FTPunCode',$paDataWhere['aDataPunCode']);
        $this->db->where_in('FTPdtCode',$paDataWhere['aDataPdtCode']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTTmp');

        // Delete Doc DT Temp
        $this->db->where_in('FNXtdStaDis',1);
        $this->db->where_in('FTSessionID',$tSessionID);
        $this->db->where_in('FNXtdSeqNo',$paDataWhere['aDataSeqNo']);
        $this->db->where_in('FTXthDocNo',$paDataWhere['tDocNo']);
        $this->db->where_in('FTBchCode',$paDataWhere['tBchCode']);
        $this->db->delete('TCNTDocDTDisTmp');
        return ;
    }


    // ============================================================================== Calcurate HD Document =============================================================================

    // Functionality : Function Get Cal From DT Temp
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPICalInDTTemp($paParams){
        $tDocNo     = $paParams['tDocNo'];
        $tDocKey    = $paParams['tDocKey'];
        $tBchCode   = $paParams['tBchCode'];
        $tSessionID = $paParams['tSessionID'];
        $tSQL       = " SELECT
                            /* ยอดรวม ==============================================================*/
                            SUM(ISNULL(DTTMP.FCXtdNet, 0)) AS FCXphTotal,

                            /* ยอดรวมสินค้าไม่มีภาษี ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END) AS FCXphTotalNV,

                            /* ยอดรวมสินค้าห้ามลด ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 2 THEN ISNULL(DTTMP.FCXtdNet, 0) ELSE 0 END) AS FCXphTotalNoDis,

                            /* ยอมรวมสินค้าลดได้ และมีภาษี ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0) ELSE 0 END) AS FCXphTotalB4DisChgV,

                            /* ยอมรวมสินค้าลดได้ และไม่มีภาษี */
                            SUM(CASE WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0) ELSE 0 END) AS FCXphTotalB4DisChgNV,

                            /* ยอดรวมหลังลด และมีภาษี ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0) ELSE 0 END) AS FCXphTotalAfDisChgV,

                            /* ยอดรวมหลังลด และไม่มีภาษี ==============================================================*/
                            SUM(CASE WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0) ELSE 0 END) AS FCXphTotalAfDisChgNV,

                            /* ยอดรวมเฉพาะภาษี ==============================================================*/
                            (
                                (
                                    /* ยอดรวม */
                                    SUM(DTTMP.FCXtdNet)
                                    - 
                                    /* ยอดรวมสินค้าไม่มีภาษี */
                                    SUM(
                                        CASE
                                            WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                            ELSE 0
                                        END
                                    )
                                )
                                -
                                (
                                    /* ยอมรวมสินค้าลดได้ และมีภาษี */
                                    SUM(
                                        CASE
                                            WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0)
                                            ELSE 0
                                        END
                                    )
                                    -
                                    /* ยอมรวมสินค้าลดได้ และมีภาษี */
                                    SUM(
                                        CASE
                                            WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0)
                                            ELSE 0
                                        END
                                    )
                                )
                            ) AS FCXphAmtV,

                            /* ยอดรวมเฉพาะไม่มีภาษี ==============================================================*/
                            (
                                (
                                    /* ยอดรวมสินค้าไม่มีภาษี */
                                    SUM(
                                        CASE
                                            WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                            ELSE 0
                                        END
                                    )
                                )
                                -
                                (
                                    /* ยอมรวมสินค้าลดได้ และไม่มีภาษี */
                                    SUM(
                                        CASE
                                            WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0)
                                            ELSE 0
                                        END
                                    )
                                    -
                                    /* ยอดรวมหลังลด และไม่มีภาษี */
                                    SUM(
                                        CASE
                                            WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                            ELSE 0
                                        END
                                    )
                                )
                            ) AS FCXphAmtNV,

                            /* ยอดภาษี ==============================================================*/
                            SUM(ISNULL(DTTMP.FCXtdVat, 0)) AS FCXphVat,

                            /* ยอดแยกภาษี ==============================================================*/
                            (
                                (
                                    /* ยอดรวมเฉพาะภาษี */
                                    (
                                        (
                                            /* ยอดรวม */
                                            SUM(DTTMP.FCXtdNet)
                                            - 
                                            /* ยอดรวมสินค้าไม่มีภาษี */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0)
                                                    ELSE 0
                                                END
                                            )
                                        )
                                        -
                                        (
                                            /* ยอมรวมสินค้าลดได้ และมีภาษี */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0)
                                                    ELSE 0
                                                END
                                            )
                                            -
                                            /* ยอดรวมหลังลด และมีภาษี */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdVatType = 1 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                                    ELSE 0
                                                END
                                            )
                                        )
                                    )
                                    -
                                    /* ยอดภาษี */
                                    SUM(ISNULL(DTTMP.FCXtdVat, 0))	
                                )
                                +
                                (
                                    /* ยอดรวมเฉพาะไม่มีภาษี */
                                    (
                                        (
                                            /* ยอดรวมสินค้าไม่มีภาษี */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNet, 0)
                                                    ELSE 0
                                                END
                                            )
                                        )
                                        -
                                        (
                                            /* ยอมรวมสินค้าลดได้ และไม่มีภาษี */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdStaAlwDis = 1 AND DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdAmtB4DisChg, 0)
                                                    ELSE 0
                                                END
                                            )
                                            -
                                            /* ยอดรวมหลังลด และไม่มีภาษี */
                                            SUM(
                                                CASE
                                                    WHEN DTTMP.FTXtdVatType = 2 THEN ISNULL(DTTMP.FCXtdNetAfHD, 0)
                                                    ELSE 0
                                                END
                                            )
                                        )
                                    )
                                )
                            ) AS FCXphVatable,

                            /* รหัสอัตราภาษี ณ ที่จ่าย ==============================================================*/
                            STUFF((
                                SELECT  ',' + DOCCONCAT.FTXtdWhtCode
                                FROM TCNTDocDTTmp DOCCONCAT
                                WHERE  1=1 
                                AND DOCCONCAT.FTBchCode = '$tBchCode'
                                AND DOCCONCAT.FTXthDocNo = '$tDocNo'
                                AND DOCCONCAT.FTSessionID = '$tSessionID'
                            FOR XML PATH('')), 1, 1, '') AS FTXphWpCode,

                            /* ภาษีหัก ณ ที่จ่าย ==============================================================*/
                            SUM(ISNULL(DTTMP.FCXtdWhtAmt, 0)) AS FCXphWpTax

                        FROM TCNTDocDTTmp DTTMP
                        WHERE DTTMP.FTXthDocNo  = '$tDocNo' 
                        AND DTTMP.FTXthDocKey   = '$tDocKey' 
                        AND DTTMP.FTSessionID   = '$tSessionID'
                        AND DTTMP.FTBchCode     = '$tBchCode'
                        GROUP BY DTTMP.FTSessionID ";
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = $oQuery->result_array();
        }else{
            $aResult    = [];
        }
        return $aResult;
    }

    
    // Functionality : Function Get Cal From HDDis Temp
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : array
    // Return Type : array
    public function FSaMPICalInHDDisTemp($paParams){
        $tDocNo     = $paParams['tDocNo'];
        $tDocKey    = $paParams['tDocKey'];
        $tBchCode   = $paParams['tBchCode'];
        $tSessionID = $paParams['tSessionID']; 
        $tSQL       = " SELECT
                            /* ข้อความมูลค่าลดชาร์จ ==============================================================*/
                            STUFF((
                                SELECT  ',' + DOCCONCAT.FTXtdDisChgTxt
                                FROM TCNTDocHDDisTmp DOCCONCAT
                                WHERE  1=1 
                                AND DOCCONCAT.FTBchCode 		= '$tBchCode'
                                AND DOCCONCAT.FTXthDocNo		= '$tDocNo'
                                AND DOCCONCAT.FTSessionID		= '$tSessionID'
                            FOR XML PATH('')), 1, 1, '') AS FTXphDisChgTxt,
                            /* มูลค่ารวมส่วนลด ==============================================================*/
                            SUM( 
                                CASE 
                                    WHEN HDDISTMP.FTXtdDisChgType = 1 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    WHEN HDDISTMP.FTXtdDisChgType = 2 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    ELSE 0 
                                END
                            ) AS FCXphDis,
                            /* มูลค่ารวมส่วนชาร์จ ==============================================================*/
                            SUM( 
                                CASE 
                                    WHEN HDDISTMP.FTXtdDisChgType = 3 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    WHEN HDDISTMP.FTXtdDisChgType = 4 THEN ISNULL(HDDISTMP.FCXtdAmt, 0)
                                    ELSE 0 
                                END
                            ) AS FCXphChg
                        FROM TCNTDocHDDisTmp HDDISTMP
                        WHERE 1=1 
                        AND HDDISTMP.FTXthDocNo     = '$tDocNo' 
                        AND HDDISTMP.FTSessionID    = '$tSessionID'
                        AND HDDISTMP.FTBchCode      = '$tBchCode'
                        GROUP BY HDDISTMP.FTSessionID ";

        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aResult    = $oQuery->row_array();
        }else{
            $aResult    = [];
        }
        return $aResult;
    }
    
    // ============================================================================= Add/Edit Event Document =============================================================================

    // Functionality : Add/Update Data HD
    // Parameters : function parameters
    // Creator : 03/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Add/Update Document HD
    // Return Type : array
    public function FSxMPIAddUpdateHD($paDataMaster,$paDataWhere,$paTableAddUpdate){
        // Get Data PI HD
        $aDataGetDataHD     =   $this->FSaMPIGetDataDocHD(array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FNLngID'       => $this->session->userdata("tLangEdit")
        ));

        $aDataAddUpdateHD   = array();
        if(isset($aDataGetDataHD['rtCode']) && $aDataGetDataHD['rtCode'] == 1){
            $aDataHDOld         = $aDataGetDataHD['raItems'];
            $aDataAddUpdateHD   = array_merge($paDataMaster,array(
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
                'FDLastUpdOn'   => $paDataWhere['FDLastUpdOn'],
                'FTLastUpdBy'   => $paDataWhere['FTLastUpdBy'],
                'FDCreateOn'    => $aDataHDOld['FDCreateOn'],
                'FTCreateBy'    => $aDataHDOld['FTCreateBy']
            ));
        }else{
            $aDataAddUpdateHD   = array_merge($paDataMaster,array(
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
                'FDCreateOn'    => $paDataWhere['FDCreateOn'],
                'FTCreateBy'    => $paDataWhere['FTCreateBy'],
            ));
        }
        // Delete PI HD
        $this->db->where_in('FTBchCode',$aDataAddUpdateHD['FTBchCode']);
        $this->db->where_in('FTXphDocNo',$aDataAddUpdateHD['FTXphDocNo']);
        $this->db->delete($paTableAddUpdate['tTableHD']);

        // Insert PI HD Dis
        $this->db->insert($paTableAddUpdate['tTableHD'],$aDataAddUpdateHD);
        return;
    }

    // Functionality : Add/Update Data HD Supplier
    // Parameters : Controller function parameters
    // Creator : 03/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Add/Update Document Supplier
    // Return Type : array
    public function FSxMPIAddUpdateHDSpl($paDataHDSpl,$paDataWhere,$paTableAddUpdate){
        // Get Data PI HD
        $aDataGetDataSpl    =   $this->FSaMPIGetDataDocHDSpl(array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FNLngID'       => $this->session->userdata("tLangEdit")
        ));

        $aDataAddUpdateHDSpl    = array();
        if(isset($aDataAddUpdateHDSpl['rtCode']) && $aDataAddUpdateHDSpl['rtCode'] == 1){
            $aDataHDSplOld  = $aDataGetDataSpl['raItems'];
            $aDataAddUpdateHDSpl    = array_merge($paDataHDSpl,array(
                'FTBchCode'     => $aDataGetDataSpl['FTBchCode'],
                'FTXphDocNo'    => $aDataGetDataSpl['FTXphDocNo'],
            ));
        }else{
            $aDataAddUpdateHDSpl    = array_merge($paDataHDSpl,array(
                'FTBchCode'     => $paDataWhere['FTBchCode'],
                'FTXphDocNo'    => $paDataWhere['FTXphDocNo'],
            ));
        }

        // Delete PI HD Spl
        $this->db->where_in('FTBchCode',$aDataAddUpdateHDSpl['FTBchCode']);
        $this->db->where_in('FTXphDocNo',$aDataAddUpdateHDSpl['FTXphDocNo']);
        $this->db->delete($paTableAddUpdate['tTableHDSpl']);

        // Insert PI HD Dis
        $this->db->insert($paTableAddUpdate['tTableHDSpl'],$aDataAddUpdateHDSpl);
        return;
    }

    // Functionality : Update DocNo In Doc Temp
    // Parameters : function parameters
    // Creator : 03/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Update DocNo In Doc Temp
    // Return Type : array
    public function FSxMPIAddUpdateDocNoToTemp($paDataWhere,$paTableAddUpdate){
        // Update DocNo Into DTTemp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->where('FTXthDocKey',$paTableAddUpdate['tTableHD']);
        $this->db->update('TCNTDocDTTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FTBchCode'     => $paDataWhere['FTBchCode']
        ));

        // Update DocNo Into HDDisTemp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->update('TCNTDocHDDisTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FTBchCode'     => $paDataWhere['FTBchCode']
        ));

        // Update DocNo Into DTDisTemp
        $this->db->where('FTXthDocNo','');
        $this->db->where('FTSessionID',$paDataWhere['FTSessionID']);
        $this->db->update('TCNTDocDTDisTmp',array(
            'FTXthDocNo'    => $paDataWhere['FTXphDocNo'],
            'FTBchCode'     => $paDataWhere['FTBchCode']
        ));
        return;
    }

    // Functionality : Move Document HDDisTemp To Document HDDis
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Insert Tempt To DT
    // Return Type : array
    public function FSaMPIMoveHdDisTempToHdDis($paDataWhere,$paTableAddUpdate){
        $tPIDocNo       = $paDataWhere['FTXphDocNo'];
        $tPIBchCode     = $paDataWhere['FTBchCode'];
        $tPISessionID   = $this->session->userdata('tSesSessionID');
        if(isset($tPIDocNo) && !empty($tPIDocNo)){
            $this->db->where_in('FTXphDocNo',$tPIDocNo);
            $this->db->where_in('FTBchCode',$tPIBchCode);
            $this->db->delete($paTableAddUpdate['tTableHDDis']);
        }
        $tSQL   =   "   INSERT INTO ".$paTableAddUpdate['tTableHDDis']." (
                            FTBchCode,FTXphDocNo,FDXphDateIns,FTXphDisChgTxt,FTXphDisChgType,
                            FCXphTotalAfDisChg,FCXphDisChg,FCXphAmt )
                    ";
        $tSQL   .=  "   SELECT
                            HDDISTEMP.FTBchCode,
                            HDDISTEMP.FTXthDocNo,
                            HDDISTEMP.FDXtdDateIns,
                            HDDISTEMP.FTXtdDisChgTxt,
                            HDDISTEMP.FTXtdDisChgType,
                            HDDISTEMP.FCXtdTotalAfDisChg,
                            HDDISTEMP.FCXtdDisChg,
                            HDDISTEMP.FCXtdAmt
                        FROM TCNTDocHDDisTmp AS HDDISTEMP WITH (NOLOCK)
                        WHERE 1 = 1
                        AND HDDISTEMP.FTBchCode     = '$tPIBchCode'
                        AND HDDISTEMP.FTXthDocNo    = '$tPIDocNo'
                        AND HDDISTEMP.FTSessionID   = '$tPISessionID'
                    ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality : Move Document DTTemp To Document DT
    // Parameters : function parameters
    // Creator : 03/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Insert Tempt To DT
    // Return Type : array
    public function FSaMPIMoveDtTmpToDt($paDataWhere,$paTableAddUpdate){
        $tPIBchCode     = $paDataWhere['FTBchCode'];
        $tPIDocNo       = $paDataWhere['FTXphDocNo'];
        $tPIDocKey      = $paTableAddUpdate['tTableHD'];
        $tPISessionID   = $this->session->userdata('tSesSessionID');
        
        if(isset($tPIDocNo) && !empty($tPIDocNo)){
            $this->db->where_in('FTXphDocNo',$tPIDocNo);
            $this->db->delete($paTableAddUpdate['tTableDT']);
        }

        $tSQL   = " INSERT INTO ".$paTableAddUpdate['tTableDT']." (
                        FTBchCode,FTXphDocNo,FNXpdSeqNo,FTPdtCode,FTXpdPdtName,FTPunCode,FTPunName,FCXpdFactor,FTXpdBarCode,FTXpdVatType,FTVatCode,FCXpdVatRate,
                        FTXpdSaleType,FCXpdSalePrice,FCXpdQty,FCXpdQtyAll,FCXpdSetPrice,FCXpdAmtB4DisChg,FTXpdDisChgTxt,FCXpdDis,FCXpdChg,FCXpdNet,FCXpdNetAfHD,
                        FCXpdVat,FCXpdVatable,FCXpdWhtAmt,FTXpdWhtCode,FCXpdWhtRate,FCXpdCostIn,FCXpdCostEx,FCXpdQtyLef,FCXpdQtyRfn,FTXpdStaPrcStk,FTXpdStaAlwDis,
                        FNXpdPdtLevel,FTXpdPdtParent,FCXpdQtySet,FTPdtStaSet,FTXpdRmk,FDLastUpdOn,FTLastUpdBy,FDCreateOn,FTCreateBy ) ";
        $tSQL   .=  "   SELECT
                            DOCTMP.FTBchCode,
                            DOCTMP.FTXthDocNo,
                            ROW_NUMBER() OVER(ORDER BY DOCTMP.FNXtdSeqNo ASC) AS FNXtdSeqNo,
                            DOCTMP.FTPdtCode,
                            DOCTMP.FTXtdPdtName,
                            DOCTMP.FTPunCode,
                            DOCTMP.FTPunName,
                            DOCTMP.FCXtdFactor,
                            DOCTMP.FTXtdBarCode,
                            DOCTMP.FTXtdVatType,
                            DOCTMP.FTVatCode,
                            DOCTMP.FCXtdVatRate,
                            DOCTMP.FTXtdSaleType,
                            DOCTMP.FCXtdSalePrice,
                            DOCTMP.FCXtdQty,
                            DOCTMP.FCXtdQtyAll,
                            DOCTMP.FCXtdSetPrice,
                            DOCTMP.FCXtdAmtB4DisChg,
                            DOCTMP.FTXtdDisChgTxt,
                            DOCTMP.FCXtdDis,
                            DOCTMP.FCXtdChg,
                            DOCTMP.FCXtdNet,
                            DOCTMP.FCXtdNetAfHD,
                            DOCTMP.FCXtdVat,
                            DOCTMP.FCXtdVatable,
                            DOCTMP.FCXtdWhtAmt,
                            DOCTMP.FTXtdWhtCode,
                            DOCTMP.FCXtdWhtRate,
                            DOCTMP.FCXtdCostIn,
                            DOCTMP.FCXtdCostEx,
                            DOCTMP.FCXtdQtyLef,
                            DOCTMP.FCXtdQtyRfn,
                            DOCTMP.FTXtdStaPrcStk,
                            DOCTMP.FTXtdStaAlwDis,
                            DOCTMP.FNXtdPdtLevel,
                            DOCTMP.FTXtdPdtParent,
                            DOCTMP.FCXtdQtySet,
                            DOCTMP.FTXtdPdtStaSet,
                            DOCTMP.FTXtdRmk,
                            DOCTMP.FDLastUpdOn,
                            DOCTMP.FTLastUpdBy,
                            DOCTMP.FDCreateOn,
                            DOCTMP.FTCreateBy
                        FROM TCNTDocDTTmp DOCTMP WITH (NOLOCK)
                        WHERE 1 = 1
                        AND DOCTMP.FTBchCode    = '$tPIBchCode'
                        AND DOCTMP.FTXthDocNo   = '$tPIDocNo'
                        AND DOCTMP.FTXthDocKey  = '$tPIDocKey'
                        AND DOCTMP.FTSessionID  = '$tPISessionID'
                        ORDER BY DOCTMP.FNXtdSeqNo ASC
        ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality : Move Document DTDisTemp To Document DTDis
    // Parameters : function parameters
    // Creator : 03/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Status Insert Tempt To DT
    // Return Type : array
    public function FSaMPIMoveDtDisTempToDtDis($paDataWhere,$paTableAddUpdate){
        $tPIBchCode     = $paDataWhere['FTBchCode'];
        $tPIDocNo       = $paDataWhere['FTXphDocNo'];
        $tPISessionID   = $this->session->userdata('tSesSessionID');
        
        if(isset($tPIDocNo) && !empty($tPIDocNo)){
            $this->db->where_in('FTXphDocNo',$tPIDocNo);
            $this->db->where_in('FTBchCode',$tPIBchCode);
            $this->db->delete($paTableAddUpdate['tTableDTDis']);
        }

        $tSQL   =   "   INSERT INTO ".$paTableAddUpdate['tTableDTDis']." (FTBchCode,FTXphDocNo,FNXpdSeqNo,FDXpdDateIns,FNXpdStaDis,FTXpdDisChgTxt,FTXpdDisChgType,FCXpdNet,FCXpdValue) ";
        $tSQL   .=  "   SELECT
                            DOCDISTMP.FTBchCode,
                            DOCDISTMP.FTXthDocNo,
                            DOCDISTMP.FNXtdSeqNo,
                            DOCDISTMP.FDXtdDateIns,
                            DOCDISTMP.FNXtdStaDis,
                            DOCDISTMP.FTXtdDisChgTxt,
                            DOCDISTMP.FTXtdDisChgType,
                            DOCDISTMP.FCXtdNet,
                            DOCDISTMP.FCXtdValue
                        FROM TCNTDocDTDisTmp DOCDISTMP WITH (NOLOCK)
                        WHERE 1=1
                        AND DOCDISTMP.FTBchCode     = '$tPIBchCode'
                        AND DOCDISTMP.FTXthDocNo    = '$tPIDocNo'
                        AND DOCDISTMP.FTSessionID   = '$tPISessionID' 
                        ORDER BY DOCDISTMP.FNXtdSeqNo ASC ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // ============================================================================ Edit Page Query ============================================================================

    // Functionality : Get Data Document HD
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Data Document HD
    // Return Type : array
    public function FSaMPIGetDataDocHD($paDataWhere){
        $tPIDocNo   = $paDataWhere['FTXthDocNo'];
        $nLngID     = $paDataWhere['FNLngID'];

        $tSQL       = " SELECT
                            DOCHD.FTBchCode,
                            BCHL.FTBchName,
                            SHP.FTMerCode,
                            MERL.FTMerName,
                            SHP.FTShpType,
                            SHP.FTShpCode,
                            SHPL.FTShpName,
                            POS.FTWahRefCode,
                            POSL.FTPosComName,
                            DOCHD.FTWahCode,
                            WAHL.FTWahName,

                            DOCHD.FTXphDocNo,
                            DOCHD.FNXphDocType,
                            DOCHD.FDXphDocDate,
                            DOCHD.FTXphCshOrCrd,
                            DOCHD.FTXphVATInOrEx,
                            DOCHD.FTDptCode,
                            DPTL.FTDptName,
                            DOCHD.FTUsrCode,
                            USRL.FTUsrName,
                            DOCHD.FTXphApvCode,
                            USRAPV.FTUsrName	AS FTXphApvName,
                            DOCHD.FTSplCode,
                            SPLL.FTSplName,
                            DOCHD.FTXphRefExt,
                            DOCHD.FDXphRefExtDate,
                            DOCHD.FTXphRefInt,
                            DOCHD.FDXphRefIntDate,
                            DOCHD.FTXphRefAE,
                            DOCHD.FNXphDocPrint,
                            DOCHD.FTRteCode,
                            DOCHD.FCXphRteFac,
                            DOCHD.FTXphRmk,

                            DOCHD.FTXphStaRefund,
                            DOCHD.FTXphStaDoc,
                            DOCHD.FTXphStaApv,
                            DOCHD.FTXphStaDelMQ,
                            DOCHD.FTXphStaPrcStk,
                            DOCHD.FTXphStaPaid,
                            
                            DOCHD.FNXphStaDocAct,
                            DOCHD.FNXphStaRef,

                            DOCHD.FDLastUpdOn,
                            DOCHD.FTLastUpdBy,
                            DOCHD.FDCreateOn,
                            DOCHD.FTCreateBy
                            
                        FROM TAPTPiHD DOCHD WITH (NOLOCK)
                        LEFT JOIN TCNMBranch_L      BCHL    WITH (NOLOCK)   ON DOCHD.FTBchCode      = BCHL.FTBchCode    AND BCHL.FNLngID	    = $nLngID
                        LEFT JOIN TCNMShop          SHP     WITH (NOLOCK)   ON DOCHD.FTShpCode      = SHP.FTShpCode     AND DOCHD.FTBchCode      = SHP.FTBchCode
                        LEFT JOIN TCNMShop_L        SHPL    WITH (NOLOCK)   ON DOCHD.FTShpCode      = SHPL.FTShpCode	AND DOCHD.FTBchCode      = SHPL.FTBchCode AND SHPL.FNLngID	    = $nLngID
                        LEFT JOIN TCNMMerchant_L    MERL    WITH (NOLOCK)   ON SHP.FTMerCode        = MERL.FTMerCode	AND MERL.FNLngID	    = $nLngID

                        LEFT JOIN TCNMWaHouse_L     WAHL    WITH (NOLOCK)   ON DOCHD.FTWahCode      = WAHL.FTWahCode	AND DOCHD.FTBchCode      = WAHL.FTBchCode  AND WAHL.FNLngID	    = $nLngID
                        LEFT JOIN TCNMWaHouse       POS     WITH (NOLOCK)   ON DOCHD.FTWahCode      = POS.FTWahCode	    AND DOCHD.FTBchCode      = POS.FTBchCode	AND POS.FTWahStaType    = '6'
                        LEFT JOIN TCNMPosLastNo		POSL    WITH (NOLOCK)   ON POS.FTWahRefCode     = POSL.FTPosCode
                        LEFT JOIN TCNMUsrDepart_L	DPTL    WITH (NOLOCK)   ON DOCHD.FTDptCode      = DPTL.FTDptCode	AND DPTL.FNLngID	= $nLngID
                        LEFT JOIN TCNMUser_L        USRL    WITH (NOLOCK)   ON DOCHD.FTUsrCode      = USRL.FTUsrCode	AND USRL.FNLngID	= $nLngID
                        LEFT JOIN TCNMUser_L        USRAPV	WITH (NOLOCK)   ON DOCHD.FTXphApvCode	= USRL.FTUsrCode	AND USRL.FNLngID	= $nLngID
                        LEFT JOIN TCNMSpl_L         SPLL    WITH (NOLOCK)   ON DOCHD.FTSplCode		= SPLL.FTSplCode	AND SPLL.FNLngID	= $nLngID
                        WHERE 1=1 AND DOCHD.FTXphDocNo = '$tPIDocNo' ";
                        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail = $oQuery->row_array();
            $aResult    = array(
                'raItems'   => $aDetail,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;
    }

    // Functionality : Get Data Document HD Spl
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Data Document HD Ref
    // Return Type : array
    public function FSaMPIGetDataDocHDSpl($paDataWhere){
        $tPIDocNo   = $paDataWhere['FTXthDocNo'];
        $nLngID     = $paDataWhere['FNLngID'];
        $tSQL       = " SELECT
                            HDSPL.FTBchCode,
                            HDSPL.FTXphDocNo,
                            HDSPL.FTXphDstPaid,
                            HDSPL.FNXphCrTerm,
                            HDSPL.FDXphDueDate,
                            HDSPL.FDXphBillDue,
                            HDSPL.FTXphCtrName,
                            HDSPL.FDXphTnfDate,
                            HDSPL.FTXphRefTnfID,
                            HDSPL.FTXphRefVehID,
                            HDSPL.FTXphRefInvNo,
                            HDSPL.FTXphQtyAndTypeUnit,
                            HDSPL.FNXphShipAdd,
                            SHIP_Add.FTAddV1No              AS FTXphShipAddNo,
                            SHIP_Add.FTAddV1Soi				AS FTXphShipAddSoi,
                            SHIP_Add.FTAddV1Village         AS FTXphShipAddVillage,
                            SHIP_Add.FTAddV1Road			AS FTXphShipAddRoad,
                            SHIP_SUDIS.FTSudName			AS FTXphShipSubDistrict,
                            SHIP_DIS.FTDstName				AS FTXphShipDistrict,
                            SHIP_PVN.FTPvnName				AS FTXphShipProvince,
                            SHIP_Add.FTAddV1PostCode	    AS FTXphShipPosCode,
                            HDSPL.FNXphTaxAdd,
                            TAX_Add.FTAddV1No               AS FTXphTaxAddNo,
                            TAX_Add.FTAddV1Soi				AS FTXphTaxAddSoi,
                            TAX_Add.FTAddV1Village		    AS FTXphTaxAddVillage,
                            TAX_Add.FTAddV1Road				AS FTXphTaxAddRoad,
                            TAX_SUDIS.FTSudName				AS FTXphTaxSubDistrict,
                            TAX_DIS.FTDstName               AS FTXphTaxDistrict,
                            TAX_PVN.FTPvnName               AS FTXphTaxProvince,
                            TAX_Add.FTAddV1PostCode		    AS FTXphTaxPosCode
                        FROM TAPTPiHDSpl HDSPL  WITH (NOLOCK)
                        LEFT JOIN TCNMAddress_L			SHIP_Add    WITH (NOLOCK)   ON HDSPL.FNXphShipAdd       = SHIP_Add.FNAddSeqNo	AND SHIP_Add.FNLngID    = $nLngID
                        LEFT JOIN TCNMSubDistrict_L     SHIP_SUDIS 	WITH (NOLOCK)	ON SHIP_Add.FTAddV1SubDist	= SHIP_SUDIS.FTSudCode	AND SHIP_SUDIS.FNLngID  = $nLngID
                        LEFT JOIN TCNMDistrict_L        SHIP_DIS    WITH (NOLOCK)	ON SHIP_Add.FTAddV1DstCode	= SHIP_DIS.FTDstCode    AND SHIP_DIS.FNLngID    = $nLngID
                        LEFT JOIN TCNMProvince_L        SHIP_PVN    WITH (NOLOCK)	ON SHIP_Add.FTAddV1PvnCode	= SHIP_PVN.FTPvnCode    AND SHIP_PVN.FNLngID    = $nLngID
                        LEFT JOIN TCNMAddress_L			TAX_Add     WITH (NOLOCK)   ON HDSPL.FNXphTaxAdd        = TAX_Add.FNAddSeqNo	AND TAX_Add.FNLngID		= $nLngID
                        LEFT JOIN TCNMSubDistrict_L     TAX_SUDIS 	WITH (NOLOCK)	ON TAX_Add.FTAddV1SubDist   = TAX_SUDIS.FTSudCode	AND TAX_SUDIS.FNLngID	= $nLngID
                        LEFT JOIN TCNMDistrict_L        TAX_DIS     WITH (NOLOCK)	ON TAX_Add.FTAddV1DstCode   = TAX_DIS.FTDstCode     AND TAX_DIS.FNLngID     = $nLngID
                        LEFT JOIN TCNMProvince_L        TAX_PVN     WITH (NOLOCK)	ON TAX_Add.FTAddV1PvnCode   = TAX_PVN.FTPvnCode		AND TAX_PVN.FNLngID     = $nLngID
                        WHERE 1=1 AND HDSPL.FTXphDocNo = '$tPIDocNo'
        ";
        
        $oQuery = $this->db->query($tSQL);
        if ($oQuery->num_rows() > 0){
            $aDetail = $oQuery->row_array();
            $aResult    = array(
                'raItems'   => $aDetail,
                'rtCode'    => '1',
                'rtDesc'    => 'success',
            );
        }else{
            $aResult    = array(
                'rtCode'    => '800',
                'rtDesc'    => 'data not found.',
            );
        }
        return $aResult;

    }

    // Functionality : Move Data HD Dis To Temp
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : -
    // Return Type : None
    public function FSxMPIMoveHDDisToTemp($paDataWhere){
        $tPIDocNo       = $paDataWhere['FTXthDocNo'];
        // Delect Document HD DisTemp By Doc No
        $this->db->where('FTXthDocNo',$tPIDocNo);
        $this->db->delete('TCNTDocHDDisTmp');
        
        $tSQL       = " INSERT INTO TCNTDocHDDisTmp (
                            FTBchCode,
                            FTXthDocNo,
                            FDXtdDateIns,
                            FTXtdDisChgTxt,
                            FTXtdDisChgType,
                            FCXtdTotalAfDisChg,
                            FCXtdTotalB4DisChg,
                            FCXtdDisChg,
                            FCXtdAmt,
                            FTSessionID,
                            FDLastUpdOn,
                            FDCreateOn,
                            FTLastUpdBy,
                            FTCreateBy
                        )
                        SELECT 
                            PIHDDis.FTBchCode,
                            PIHDDis.FTXphDocNo,
                            PIHDDis.FDXphDateIns,
                            PIHDDis.FTXphDisChgTxt,
                            PIHDDis.FTXphDisChgType,
                            PIHDDis.FCXphTotalAfDisChg,
                            (ISNULL(NULL,0)) AS FCXtdTotalB4DisChg,
                            PIHDDis.FCXphDisChg,
                            PIHDDis.FCXphAmt,
                            CONVERT(VARCHAR,'".$this->session->userdata('tSesSessionID')."')    AS FTSessionID,
                            CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDLastUpdOn,
                            CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn,
                            CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTLastUpdBy,
                            CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTCreateBy
                        FROM TAPTPiHDDis PIHDDis WITH (NOLOCK)
                        WHERE 1=1 AND PIHDDis.FTXphDocNo = '$tPIDocNo'
        ";
        $oQuery = $this->db->query($tSQL);
        return;
    }

    // Functionality : Move Data DT To DTTemp
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : -
    // Return Type : None
    public function FSxMPIMoveDTToDTTemp($paDataWhere){
        $tPIDocNo       = $paDataWhere['FTXthDocNo'];
        $tPIDocKey      = $paDataWhere['FTXthDocKey'];

        // Delect Document DTTemp By Doc No
        $this->db->where('FTXthDocNo',$tPIDocNo);
        $this->db->delete('TCNTDocDTTmp');

        $tSQL   = " INSERT INTO TCNTDocDTTmp (
                        FTBchCode,FTXthDocNo,FNXtdSeqNo,FTXthDocKey,FTPdtCode,FTXtdPdtName,FTPunCode,FTPunName,FCXtdFactor,FTXtdBarCode,
                        FTXtdVatType,FTVatCode,FCXtdVatRate,FTXtdSaleType,FCXtdSalePrice,FCXtdQty,FCXtdQtyAll,FCXtdSetPrice,
                        FCXtdAmtB4DisChg,FTXtdDisChgTxt,FCXtdDis,FCXtdChg,FCXtdNet,FCXtdNetAfHD,FCXtdVat,FCXtdVatable,FCXtdWhtAmt,
                        FTXtdWhtCode,FCXtdWhtRate,FCXtdCostIn,FCXtdCostEx,FCXtdQtyLef,FCXtdQtyRfn,FTXtdStaPrcStk,FTXtdStaAlwDis,
                        FNXtdPdtLevel,FTXtdPdtParent,FCXtdQtySet,FTXtdPdtStaSet,FTXtdRmk,
                        FTSessionID,FDLastUpdOn,FDCreateOn,FTLastUpdBy,FTCreateBy )
                    SELECT
                        PIDT.FTBchCode,
                        PIDT.FTXphDocNo,
                        PIDT.FNXpdSeqNo,
                        CONVERT(VARCHAR,'".$tPIDocKey."') AS FTXthDocKey,
                        PIDT.FTPdtCode,
                        PIDT.FTXpdPdtName,
                        PIDT.FTPunCode,
                        PIDT.FTPunName,
                        PIDT.FCXpdFactor,
                        PIDT.FTXpdBarCode,
                        PIDT.FTXpdVatType,
                        PIDT.FTVatCode,
                        PIDT.FCXpdVatRate,
                        PIDT.FTXpdSaleType,
                        PIDT.FCXpdSalePrice,
                        PIDT.FCXpdQty,
                        PIDT.FCXpdQtyAll,
                        PIDT.FCXpdSetPrice,
                        PIDT.FCXpdAmtB4DisChg,
                        PIDT.FTXpdDisChgTxt,
                        PIDT.FCXpdDis,
                        PIDT.FCXpdChg,
                        PIDT.FCXpdNet,
                        PIDT.FCXpdNetAfHD,
                        PIDT.FCXpdVat,
                        PIDT.FCXpdVatable,
                        PIDT.FCXpdWhtAmt,
                        PIDT.FTXpdWhtCode,
                        PIDT.FCXpdWhtRate,
                        PIDT.FCXpdCostIn,
                        PIDT.FCXpdCostEx,
                        PIDT.FCXpdQtyLef,
                        PIDT.FCXpdQtyRfn,
                        PIDT.FTXpdStaPrcStk,
                        PIDT.FTXpdStaAlwDis,
                        PIDT.FNXpdPdtLevel,
                        PIDT.FTXpdPdtParent,
                        PIDT.FCXpdQtySet,
                        PIDT.FTPdtStaSet,
                        PIDT.FTXpdRmk,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesSessionID')."') AS FTSessionID,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDLastUpdOn,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTLastUpdBy,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTCreateBy
                    FROM TAPTPiDT AS PIDT WITH (NOLOCK)
                    WHERE 1=1 AND PIDT.FTXphDocNo = '$tPIDocNo'
                    ORDER BY PIDT.FNXpdSeqNo ASC ";
        $oQuery = $this->db->query($tSQL);
        return;
    }


    // Functionality : Move Data DT Dis To DT Dis Temp
    // Parameters : function parameters
    // Creator : 04/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : -
    // Return Type : None
    public function FSxMPIMoveDTDisToDTDisTemp($paDataWhere){
        $tPIDocNo       = $paDataWhere['FTXthDocNo'];
        
        // Delect Document DTDisTemp By Doc No
        $this->db->where('FTXthDocNo',$tPIDocNo);
        $this->db->delete('TCNTDocDTDisTmp');

        $tSQL   = " INSERT INTO TCNTDocDTDisTmp (
                        FTBchCode,
                        FTXthDocNo,
                        FNXtdSeqNo,
                        FTSessionID,
                        FDXtdDateIns,
                        FNXtdStaDis,
                        FTXtdDisChgType,
                        FCXtdNet,
                        FCXtdValue,
                        FDLastUpdOn,
                        FDCreateOn,
                        FTLastUpdBy,
                        FTCreateBy,
                        FTXtdDisChgTxt
                    )
                    SELECT
                        PIDTDis.FTBchCode,
                        PIDTDis.FTXphDocNo,
                        PIDTDis.FNXpdSeqNo,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesSessionID')."')    AS FTSessionID,
                        PIDTDis.FDXpdDateIns,
                        PIDTDis.FNXpdStaDis,
                        PIDTDis.FTXpdDisChgType,
                        PIDTDis.FCXpdNet,
                        PIDTDis.FCXpdValue,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDLastUpdOn,
                        CONVERT(DATETIME,'".date('Y-m-d H:i:s')."') AS FDCreateOn,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTLastUpdBy,
                        CONVERT(VARCHAR,'".$this->session->userdata('tSesUsername')."') AS FTCreateBy,
                        PIDTDis.FTXpdDisChgTxt
                    FROM TAPTPiDTDis PIDTDis
                    WHERE 1=1 AND PIDTDis.FTXphDocNo = '$tPIDocNo'
                    ORDER BY PIDTDis.FNXpdSeqNo ASC
            ";
        $oQuery = $this->db->query($tSQL);
        return;
    }
    
    // ============================================================================ Edit Page Query ============================================================================

    // Functionality : Cancel Document Data
    // Parameters : function parameters
    // Creator : 09/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : -
    // Return Type : None
    public function FSaMPICancelDocument($paDataUpdate){
        // TAPTPiHD
        $this->db->trans_begin();
        $this->db->set('FTXphStaDoc' , '3');
        $this->db->where('FTXphDocNo', $paDataUpdate['tDocNo']);
        $this->db->update('TAPTPiHD');
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aDatRetrun = array(
                'nStaEvent' => '900',
                'tStaMessg' => "Error Cannot Update Status Cancel Document."
            );
        }else{
            $this->db->trans_commit();
            $aDatRetrun = array(
                'nStaEvent' => '1',
                'tStaMessg' => "Update Status Document Cancel Success."
            );
        }
        return $aDatRetrun;
    }

    // Functionality : Approve Document Data
    // Parameters : function parameters
    // Creator : 09/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : -
    // Return Type : None
    public function FSaMPIApproveDocument($paDataUpdate){
        // TAPTPiHD
        $this->db->trans_begin();
        $dLastUpdOn = date('Y-m-d H:i:s');
        $tLastUpdBy = $this->session->userdata('tSesUsername');


        $this->db->set('FDLastUpdOn',$dLastUpdOn);
        $this->db->set('FTLastUpdBy',$tLastUpdBy);
        $this->db->set('FTXphStaPrcStk',2);
        $this->db->set('FTXphStaApv',2);
        $this->db->set('FTXphApvCode',$paDataUpdate['tApvCode']);
        $this->db->where('FTXphDocNo',$paDataUpdate['tDocNo']);


        $this->db->update('TAPTPiHD');
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $aDatRetrun = array(
                'nStaEvent' => '900',
                'tStaMessg' => "Error Cannot Update Status Approve Document."
            );
        }else{
            $this->db->trans_commit();
            $aDatRetrun = array(
                'nStaEvent' => '1',
                'tStaMessg' => "Update Status Document Approve Success."
            );
        }
        return $aDatRetrun;
    }

    // ================================================================== Search And Add Product In DT Temp ====================================================================

    // Functionality : Count Product Bar
    // Parameters : function parameters
    // Creator : 30/07/2019 Wasin(Yoshi)
    // Last Modified : -
    // Return : Array Data Find Product
    // Return Type : Array
    public function FSaCPICountPdtBarInTablePdtBar($paDataChkINDB){
        $tPIDataSearchAndAdd    = $paDataChkINDB['tPIDataSearchAndAdd'];
        $nLangEdit              = $paDataChkINDB['nLangEdit'];

        $tSQL   = " SELECT 
                        PDTBAR.FTPdtCode,
                        PDT_L.FTPdtName,
                        PDTBAR.FTBarCode,
                        PDTBAR.FTPunCode,
                        PUN_L.FTPunName
                    FROM TCNMPdtBar         PDTBAR  WITH(NOLOCK)
                    LEFT JOIN TCNMPdt		PDT     WITH(NOLOCK)	ON PDTBAR.FTPdtCode = PDT.FTPdtCode
                    LEFT JOIN TCNMPdt_L	    PDT_L   WITH(NOLOCK)	ON PDT.FTPdtCode	= PDT_L.FTPdtCode   AND PDT_L.FNLngID   = $nLangEdit
                    LEFT JOIN TCNMPdtUnit   PUN     WITH(NOLOCK)	ON PDTBAR.FTPunCode	= PUN.FTPunCode
                    LEFT JOIN TCNMPdtUnit_L	PUN_L   WITH(NOLOCK)	ON PUN.FTPunCode    = PUN_L.FTPunCode   AND PUN_L.FNLngID   = $nLangEdit
                    WHERE 1=1
                    AND PDTBAR.FTBarStaUse 	= 1
                    AND (PDTBAR.FTPdtCode = '$tPIDataSearchAndAdd' OR PDTBAR.FTBarCode = '$tPIDataSearchAndAdd')
        ";
        $oQuery         = $this->db->query($tSQL);
        $aDataReturn    = $oQuery->result_array();
        unset($oQuery);
        return $aDataReturn;
    }

    //เปิดมาหน้า ADD จะต้อง ลบสินค้าตัวเดิม where session
    public function FSaMCENDeletePDTInTmp($paParams){
        $tSessionID = $this->session->userdata('tSesSessionID');
        $this->db->where_in('FTSessionID', $tSessionID);
        $this->db->delete('TCNTDocDTTmp');
        if($this->db->affected_rows() > 0){
            $aStatus = array(
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        }else{
            $aStatus = array(
                'rtCode' => '905',
                'rtDesc' => 'cannot Delete Item.',
            );
        }
        return $aStatus;
    }
}