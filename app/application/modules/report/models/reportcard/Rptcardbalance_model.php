<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rptcardbalance_model extends CI_Model {

    /**
     * Functionality: Delete Temp Report
     * Parameters:  Function Parameter
     * Creator: 28/10/2019 Piya
     * Last Modified : -
     * Return : Call Store Proce
     * Return Type: Array
     */
    public function FSnMExecStoreReport($paDataFilter) {

        $nLngID = $paDataFilter['nLngID'];
        $tComName = $paDataFilter['tCompName'];
        $tRptCode = $paDataFilter['tRptCode'];
        // สถานะบัตร
        $tStaCardFrom = empty($paDataFilter['tStaCardFrom']) ? '' : $paDataFilter['tStaCardFrom']; 
        $tStaCardTo = empty($paDataFilter['tStaCardTo']) ? '' : $paDataFilter['tStaCardTo'];
        // วันที่สร้างเอกสาร
        $tDocDateFrom = empty($paDataFilter['tDocDateFrom']) ? '' : $paDataFilter['tDocDateFrom'];
        $tDocDateTo = empty($paDataFilter['tDocDateTo']) ? '' : $paDataFilter['tDocDateTo'];
        $tDbName = $this->db->database;
        
        $tSQL = " 
            USE [$tDbName]

            DECLARE	@return_value int,
                    @FNResult int

            SELECT	@FNResult = 0

            EXEC	@return_value = SP_RPTxCardBalance
                    @pnLngID = $nLngID,
                    @pnComName = N'$tComName',
                    @ptRptName = N'$tRptCode',
                    @ptCrdStaActF = N'$tStaCardFrom',
                    @ptCrdStaActT = N'$tStaCardTo',
                    @ptDocDateF = N'$tDocDateFrom',
                    @ptDocDateT = N'$tDocDateTo',
                    @FNResult = @FNResult OUTPUT

            SELECT	@FNResult as N'@FNResult'

            SELECT	'Return Value' = @return_value
        ";
        
        $oQuery = $this->db->query($tSQL);

        if (false !== $oQuery) {
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
     * Creator: 28/10/2019 Piya
     * Last Modified : -
     * Return : Get Data Rpt Temp
     * Return Type: Array
     */
    public function FSaMGetDataReport($paDataWhere, $paDataFilter) {

        $aRowLen = FCNaHCallLenData($paDataWhere['nRow'], $paDataWhere['nPage']);
        $nLngID = $paDataWhere['nLngID'];
        $tUserCode = $paDataWhere['tUserCode'];
        $tCompName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];
        $tSessionID = $paDataWhere['tUserSession'];

        $tStaActiveText = 'FTCrdStaActiveTH ';
        if($nLngID == 1){$tStaActiveText = 'FTCrdStaActiveTH ';}
        if($nLngID == 2){$tStaActiveText = 'FTCrdStaActiveEN ';}
            
        $tSQL = "
            SELECT
                RPT.* 
            FROM( 
                SELECT 
                    ROW_NUMBER() OVER(ORDER BY TMP.FTCrdStaActive ASC) AS rtRowID,
                    $tStaActiveText AS FTCrdStaActiveText,
                    FNCrdBalanceQty, /*จำนวนคงเหลือปัจจุบัน*/
                    FCCrdBalanceValue, /*มูลค่าคงเหลือปัจจุบัน*/
                    FNCrdInOutAdjQty, /*จำนวน ยอดเข้า/ออก/ปรับปรุง*/
                    FCCrdInOutAdjValue, /*มูลคา ยอดเข้า/ออก/ปรับปรุง*/
                    FNCrdSaleQty, /*จำนวนขาย*/
                    FCCrdSaleValue, /*มูลค่าขาย*/
                    FNCrdRetQty, /*จำนวนคืน*/
                    FNCrdRetValue, /*มูลค่าคืน*/
                    FNCrdSpendQty, /*จำนวนการใช้จ่าย*/
                    FCCrdSpendValue, /*มูลค่าการใช้จ่าย*/ 
                    FNCrdExpireQty, /*จำนวนหมดอายุ*/
                    FCCrdExpireValue /*มูลค่าหมดอายุ*/
                FROM TFCTRptBalCrdTemp TMP WITH (NOLOCK)
                WHERE TMP.FTComName = '$tCompName' AND TMP.FTRptName = '$tRptCode'
            ) AS RPT
        ";
            
        $tSQL .= " WHERE 1=1 ";
        $tSQL .= " AND RPT.rtRowID > $aRowLen[0] AND RPT.rtRowID <= $aRowLen[1]";

        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $aDataRpt = $oQuery->result_array();

            $oCountRowRpt = $this->FSaMCountDataReportAll($paDataWhere);
            $nFoundRow = $oCountRowRpt;
            $nPageAll = ceil($nFoundRow / $paDataWhere['nRow']);
            $aReturnData = array(
                'raItems' => $aDataRpt,
                'rnAllRow' => $nFoundRow,
                'rnCurrentPage' => $paDataWhere['nPage'],
                'rnAllPage' => $nPageAll,
                'rtCode' => '1',
                'rtDesc' => 'success',
            );
        } else {
            $aReturnData = array(
                'rnAllRow' => 0,
                'rnCurrentPage' => $paDataWhere['nPage'],
                "rnAllPage" => 0,
                'rtCode' => '800',
                'rtDesc' => 'data not found',
            );
        }
        unset($oQuery);
        unset($oCountRowRpt);
        unset($nFoundRow);
        unset($nPageAll);
        return $aReturnData;
    }

    /**
     * Functionality: Count Data Report All
     * Parameters: Function Parameter
     * Creator: 28/10/2019 Piya
     * Last Modified: -
     * Return: Data Report All
     * ReturnType: Array
     */
    public function FSaMCountDataReportAll($paDataWhere) {

        $tSessionID = $paDataWhere['tUserSession'];
        $tCompName = $paDataWhere['tCompName'];
        $tRptCode = $paDataWhere['tRptCode'];

        $tSQL = "   
            SELECT
                ROW_NUMBER() OVER(ORDER BY TMP.FTComName ASC) AS rtRowID 
            FROM TFCTRptBalCrdTemp TMP WITH (NOLOCK)
            WHERE FTComName = '$tCompName' AND FTRptName = '$tRptCode'
        ";

        $oQuery = $this->db->query($tSQL);

        $nCountData = $oQuery->num_rows();
        unset($oQuery);
        return $nCountData;
    }
}
