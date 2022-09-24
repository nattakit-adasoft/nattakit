<?php



/*  Function : Delete Data All DocTemp
    create : 05-03-2019 Krit(Copter)
*/
function FCNoHDOCDeleteDocTmp(){

    $ci = &get_instance();
    $ci->load->database();
    $tUserSesstionID = $ci->session->userdata("tSesSessionID");
    $tDateNow = date('Y-m-d');

    /*===== Begin Clear Doc Temp =======================================================*/
    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTDocDTTmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTDocDTDisTmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTDocHDDisTmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TsysMasTmp');
    /*===== End Clear Doc Temp =========================================================*/

    /*===== Begin Clear Promotion Temp =================================================*/
    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTPdtPmtCB_Tmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTPdtPmtCG_Tmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTPdtPmtDT_Tmp');
    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTPdtPmtDT_Bin');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTPdtPmtHDBch_Tmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTPdtPmtHDCstPri_Tmp');

    $ci->db->where('FDCreateOn <', $tDateNow);
    $ci->db->delete('TCNTImpMasTmp');
    /*===== End Clear Promotion Temp ===================================================*/

    if ($ci->db->affected_rows() > 0) {
        return 'success';
    } else {
        return 'fail';
    }
}

//Function : ต้นทุนสำหรับ การสั่งซื้อ ซื้อ เพิ่มหนี้ (ผู้จำหน่าย)
//Creator: 07/02/2019 Krit(Copter)
function FCNnHDOCGetCostPurPO(){

    $ci = &get_instance();
    $ci->load->database();

    $tSQL = "SELECT FTSysStaDefValue,FTSysStaUsrValue
            FROM  TSysConfig 
            WHERE FTSysCode = 'ACostPur'
            AND FTSysKey = 'PO'";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
        if ($oRes[0]->FTSysStaUsrValue != '') {
            $tDataSavDec = $oRes[0]->FTSysStaUsrValue;
        } else {
            $tDataSavDec = $oRes[0]->FTSysStaDefValue;
        }
    } else {
        //Decimal Default = 2 
        $tDataSavDec = 2;
    }
    return $tDataSavDec;
}

//Function : Get Pdt Unit Data for Modal Pdt List
//Creator: 19/10/2018 Krit(Copter)
function FCNxHDOCGetPdtImg($paDataSearch){

    $tPdtCode    = $paDataSearch['tPdtCode'];

    $ci = &get_instance();
    $ci->load->database();

    $tSQL = "SELECT FNImgID,FTImgRefID,FNImgSeq,FNImgSeq,FTImgObj
             FROM TCNMImgPdt
             WHERE FTImgRefID = '$tPdtCode' ";

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {
        $oData    = $oQuery->result_array();
    } else {
        $oData = 0;
    }

    return $oData;
}

//Function : Get Pdt Unit Data for Modal Pdt List
//Creator: 19/10/2018 Krit(Copter)
function FCNxHDOCGetPdtUnit(){

    $ci = &get_instance();
    $ci->load->database();

    $tLangActive = $_SESSION['tLangEdit'];

    $tSQL = "SELECT PU.FTPunCode,
                    PUL.FTPunName
            FROM TCNMPdtUnit PU
            LEFT JOIN TCNMPdtUnit_L PUL ON PU.FTPunCode = PUL.FTPunCode AND PUL.FNLngID = $tLangActive";

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {
        $oData    = $oQuery->result();
    } else {
        $oData = 0;
    }

    return $oData;
}

//Functionality: Get Pdt List
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified :
//Return : 
//Return Type: Array
function FCNxHGetPdtBarCode($ptBarCode, $ptSplCode){

    $ci = &get_instance();
    $ci->load->database();

    $tLangActive = $_SESSION['tLangEdit'];

    if ($ptBarCode != '') {

        $tSQL = "SELECT PDT.FTPdtCode,
            
                        PDT.FTPdtForSystem,
                        PDT.FTPdtType,
                        PDT.FTPdtStaActive,
                        PPZ.FCPdtUnitFact,
                        PUL.FTPunCode,
                        PUL.FTPunName,
                        BAR.FTBarCode,
                        P4PDT.FCPgdPriceRet
                        
                    FROM TCNMPdt PDT
                    LEFT JOIN TCNMPdtPackSize PPZ 	    ON PDT.FTPdtCode = PPZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L PUL 	    ON PPZ.FTPunCode = PUL.FTPunCode AND PUL.FNLngID = $tLangActive
                    LEFT JOIN TCNMPdtBar BAR		    ON BAR.FTPdtCode = PDT.FTPdtCode AND BAR.FTPunCode = PPZ.FTPunCode 
                    LEFT JOIN TCNTPdtPrice4PDT P4PDT    ON PDT.FTPdtCode = P4PDT.FTPdtCode AND P4PDT.FTPunCode  = PPZ.FTPunCode AND P4PDT.FTPghDocType = 1 AND P4PDT.FDPghDStart <= GETDATE()
                    LEFT JOIN TCNMPdtSpl SPL            ON PDT.FTPdtCode = SPL.FTPdtCode AND BAR.FTBarCode = SPL.FTBarCode

                    WHERE PDT.FTPdtForSystem = '1'
                    AND PDT.FTPdtType IN('1','2','4')
                    AND PDT.FTPdtStaActive = '1'
                    AND SPL.FTSplStaAlwPO = '1'
                ";

        $tSQL .= " AND BAR.FTBarCode = '$ptBarCode' ";
        $tSQL .= " AND SPL.FTBarCode = '$ptBarCode' ";

        if ($ptSplCode != '') {
            $tSQL .= " AND SPL.FTSplCode = '$ptSplCode' ";
        }

        $tSQL .= " ORDER BY PPZ.FCPdtUnitFact ASC";

        $oQuery = $ci->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $oRes  = $oQuery->result();
        } else {
            $oRes = 0;
        }
        return $oRes;
    } else {
        return 0;
    }
}


//Functionality: Scan barcode for Get Pdt Data To Use
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified :
//Return : 
//Return Type: Array
function FCNxHGetPdtBarCodeForDocument($ptBarCode){

    $ci = &get_instance();
    $ci->load->database();

    $tLangActive = $_SESSION['tLangEdit'];

    if ($ptBarCode != '') {

        $tSQL = "SELECT PDT.FTPdtCode AS ptPdtCode,
            
                        PDT.FTPdtForSystem,
                        PDT.FTPdtType,
                        PDT.FTPdtStaActive,
                        PPZ.FCPdtUnitFact,
                        PUL.FTPunCode AS ptPunCode,
                        PUL.FTPunName AS ptPunName,
                        BAR.FTBarCode AS ptBarCode,
                        PDT.FCPdtCostStd AS pcPdtCostStd
                        
                    FROM TCNMPdt PDT
                    LEFT JOIN TCNMPdtPackSize PPZ 	    ON PDT.FTPdtCode = PPZ.FTPdtCode
                    LEFT JOIN TCNMPdtUnit_L PUL 	    ON PPZ.FTPunCode = PUL.FTPunCode AND PUL.FNLngID = $tLangActive
                    LEFT JOIN TCNMPdtBar BAR		    ON BAR.FTPdtCode = PDT.FTPdtCode AND BAR.FTPunCode = PPZ.FTPunCode 

                    WHERE PDT.FTPdtForSystem = '1'
                    AND PDT.FTPdtStaActive = '1'
                ";

        $tSQL .= " AND BAR.FTBarCode = '$ptBarCode' ";

        $tSQL .= " ORDER BY PPZ.FCPdtUnitFact ASC";

        $oQuery = $ci->db->query($tSQL);

        if ($oQuery->num_rows() > 0) {
            $oRes  = $oQuery->result();
        } else {
            $oRes = 0;
        }
        return $oRes;
    } else {
        return 0;
    }
}


function FCNxHGetPdtDetailList($aDataSearch){

    $ci = &get_instance();
    $ci->load->database();

    $tPdtCode       = $aDataSearch['tPdtCode'];
    $tSplCode       = $aDataSearch['tSplCode'];
    $tPdtBarCode    = $aDataSearch['tPdtBarCode'];
    $tPdtPunCode    = $aDataSearch['tPdtPunCode'];

    //Lang 
    $tLangActive = $_SESSION['tLangEdit'];
    //arm 10/12/18
    if (isset($aDataSearch['tStaPage'])) {
        $tStaPage = $aDataSearch['tStaPage'];
    } else {
        $tStaPage = 'PO';
    }

    $tSQL = "SELECT DISTINCT PDT.FTPdtCode,
                    PDT.FTPdtForSystem,
                    PDT.FTPdtType,
                    PDT.FTPdtStaActive,
                    PPZ.FCPdtUnitFact,
                    PUL.FTPunCode,
                    PUL.FTPunName,
                    BAR.FTBarCode,
                    CAVG.FCPdtQtyBal,
                    --ราคาต้นทุน
                    PDT.FCPdtCostStd,
                    CAVG.FCPdtCostEx,
                    CAVG.FCPdtCostIn,
                    SPL.FCSplLastPrice
                    --ราคาต้นทุน
                    
                FROM TCNMPdt PDT
                LEFT JOIN TCNMPdtPackSize PPZ 	 ON PDT.FTPdtCode = PPZ.FTPdtCode
                LEFT JOIN TCNMPdtUnit_L PUL 	 ON PPZ.FTPunCode = PUL.FTPunCode AND PUL.FNLngID = $tLangActive
                LEFT JOIN TCNMPdtBar BAR		 ON BAR.FTPdtCode = PDT.FTPdtCode AND BAR.FTPunCode = PPZ.FTPunCode 
                LEFT JOIN TCNMPdtCostAvg CAVG    ON PDT.FTPdtCode = CAVG.FTPdtCode
                ";

    if ($tStaPage != 'MasterSpl') {
        $tSQL .= " LEFT JOIN TCNMPdtSpl SPL ON PDT.FTPdtCode = SPL.FTPdtCode AND BAR.FTBarCode = SPL.FTBarCode";
    }

    $tSQL .= " WHERE PDT.FTPdtForSystem = '1' AND PDT.FTPdtStaActive = '1' ";
    if ($tStaPage != 'MasterSpl') {
        $tSQL .= " AND PDT.FTPdtType IN('1','2','4')
                    AND SPL.FTSplStaAlwPO = '1'
                    AND BAR.FTBarCode = SPL.FTBarCode
                ";
    }

    if ($tPdtCode != '') {
        $tSQL .= " AND PDT.FTPdtCode = '$tPdtCode' ";
    }

    if ($tSplCode != ''  && $tStaPage != 'MasterSpl') {
        $tSQL .= " AND SPL.FTSplCode = '$tSplCode' ";
    }

    if ($tPdtBarCode != '') {
        $tSQL .= " AND BAR.FTBarCode LIKE '%$tPdtBarCode%'";
    }

    if ($tPdtPunCode != '') {
        $tSQL .= " AND BAR.FTPunCode = '$tPdtPunCode' ";
    }

    $tSQL .= " ORDER BY PPZ.FCPdtUnitFact ASC";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
    } else {
        $oRes = 0;
    }
    return $oRes;
}


//Functionality: Get Pdt List
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified :
//Return : 
//Return Type: Array
function FCNxHGetPdtList($aDataSearch){

    $ci = &get_instance();
    $ci->load->database();

    $tLangActive = $_SESSION['tLangEdit'];

    $tSplCode    = $aDataSearch['tSplCode'];
    $tPdtBarCode = $aDataSearch['tPdtBarCode'];
    $tPdtCode    = $aDataSearch['tPdtCode'];
    $tPdtPdtName = $aDataSearch['tPdtPdtName'];
    $tPdtPunCode = $aDataSearch['tPdtPunCode'];

    //arm 10/12/18
    if (isset($aDataSearch['tStaPage'])) {
        $tStaPage = $aDataSearch['tStaPage'];
    } else {
        $tStaPage = 'PO';
    }

    $nPage = $aDataSearch['nPage'];
    $nRow  = $aDataSearch['nRow'];

    $aRowLen = FCNaHCallLenData($nRow, $nPage);

    $tSQL = "SELECT c.* FROM(
                SELECT  ROW_NUMBER() OVER(ORDER BY FTPdtCode ASC) AS FNRowID,* FROM
                    (SELECT DISTINCT
                            PDT.FTPdtCode,
                            PDT.FTPdtForSystem,
                            PDT.FCPdtMin,
                            PDT.FCPdtMax,
                            PDT.FTPdtPoint,
                            BAR.FTBarCode,
                            PDT.FCPdtPointTime,
                            PDT.FTPdtType,
                            PDT.FTPdtSaleType,
                            PDT.FTPdtSetOrSN,
                            PDTL.FTPdtName,
                            PDTL.FTPdtRmk
                            
                        FROM TCNMPdt PDT
                        LEFT JOIN TCNMPdt_L PDTL        ON PDT.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID = $tLangActive
                        LEFT JOIN TCNMPdtBar BAR        ON BAR.FTPdtCode = PDT.FTPdtCode ";
    if ($tStaPage != 'MasterSpl') {
        $tSQL .= " LEFT JOIN TCNMPdtSpl SPL ON PDT.FTPdtCode = SPL.FTPdtCode AND BAR.FTBarCode = SPL.FTBarCode ";
    }


    $tSQL .= " WHERE PDT.FTPdtStaActive = '1' AND PDT.FTPdtForSystem = '1' ";
    if ($tStaPage != 'MasterSpl') {
        $tSQL .= " AND PDT.FTPdtType IN('1','2','4')
                            AND SPL.FTSplStaAlwPO = '1'  ";
    }

    if ($tSplCode != '' && $tStaPage != 'MasterSpl') {
        $tSQL .= " AND SPL.FTSplCode = '$tSplCode' ";
    }

    if ($tPdtCode != '') {
        $tSQL .= " AND PDT.FTPdtCode LIKE '%$tPdtCode%' ";
    }

    if ($tPdtBarCode != '') {
        $tSQL .= " AND BAR.FTBarCode LIKE '%$tPdtBarCode%'";
    }

    if ($tPdtPdtName != '') {
        $tSQL .= " AND PDTL.FTPdtName LIKE '%$tPdtPdtName%' ";
    }

    if ($tPdtPunCode != '') {
        $tSQL .= " AND BAR.FTPunCode = '$tPdtPunCode' ";
    }

    $tSQL .= ") Base) AS c WHERE c.FNRowID > $aRowLen[0] AND c.FNRowID <= $aRowLen[1]";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
        $aFoundRow = FCNnHPdtGetPageAll($aDataSearch);
        $nFoundRow = $aFoundRow[0]->counts;
        $nPageAll = ceil($nFoundRow / $nRow); //หา Page All จำนวน Rec หาร จำนวนต่อหน้า
        $aResult = array(
            'raItems'       => $oRes,
            'rnAllRow'      => $nFoundRow,
            'rnCurrentPage' => $nPage,
            'rnAllPage'     => $nPageAll,
            'rtCode'        => '1',
            'rtDesc'        => 'success',
        );
    } else {
        $aResult = array(
            'rnAllRow' => 0,
            'rnCurrentPage' => $nPage,
            "rnAllPage" => 0,
            'rtCode' => '800',
            'rtDesc' => 'data not found',
        );
    }
    return $aResult;
}


//Functionality : All Page Of Subdistrict
//Parameters : function parameters
//Creator :  12/06/2018 Wasin
//Last Modified : -
//Return : Data Array
//Return Type : Array
function FCNnHPdtGetPageAll($paDataSearch){

    $ci = &get_instance();
    $ci->load->database();

    $tLangActive = $_SESSION['tLangEdit'];

    $tSplCode    = $paDataSearch['tSplCode'];
    $tPdtBarCode = $paDataSearch['tPdtBarCode'];
    $tPdtPdtName = $paDataSearch['tPdtPdtName'];
    $tPdtPunCode = $paDataSearch['tPdtPunCode'];

    //arm 10/12/18
    if (isset($paDataSearch['tStaPage'])) {
        $tStaPage = $paDataSearch['tStaPage'];
    } else {
        $tStaPage = 'PO';
    }


    $tSQL = "SELECT COUNT (DISTINCT PDT.FTPdtCode) AS counts
                        
                FROM TCNMPdt PDT
                LEFT JOIN TCNMPdt_L PDTL        ON PDT.FTPdtCode = PDTL.FTPdtCode AND PDTL.FNLngID = $tLangActive
                LEFT JOIN TCNMPdtBar BAR        ON BAR.FTPdtCode = PDT.FTPdtCode";

    if ($tStaPage != 'MasterSpl') {
        $tSQL .= " LEFT JOIN TCNMPdtSpl SPL        ON PDT.FTPdtCode = SPL.FTPdtCode AND BAR.FTBarCode = SPL.FTBarCode ";
    }

    $tSQL .= " WHERE PDT.FTPdtStaActive = '1' AND PDT.FTPdtForSystem = '1' ";

    if ($tStaPage != 'MasterSpl') {
        $tSQL .= " AND PDT.FTPdtType IN('1','2','4')
        AND SPL.FTSplStaAlwPO = '1'  ";
    }

    if ($tSplCode != '' && $tStaPage != 'MasterSpl') {
        $tSQL .= " AND SPL.FTSplCode = '$tSplCode' ";
    }

    if ($tPdtBarCode != '') {
        $tSQL .= " AND BAR.FTBarCode LIKE '%$tPdtBarCode%'";
    }

    if ($tPdtPdtName != '') {
        $tSQL .= " AND PDTL.FTPdtName LIKE '%$tPdtPdtName%' ";
    }

    if ($tPdtPunCode != '') {
        $tSQL .= " AND BAR.FTPunCode LIKE '%$tPdtPunCode%' ";
    }

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {
        return $oQuery->result();
    } else {
        //No Data
        return false;
    }
}


//Functionality: Get Option Decical Save
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified : 06/07/2020 Witsarut (Bell)
//Return : 
//Return Type: Array
function FCNxHGetOptionDecimalSave(){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FTSysStaDefValue,FTSysStaUsrValue
             FROM  TSysConfig 
             WHERE FTSysCode = 'ADecPntSav'
            ";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
        if ($oRes[0]->FTSysStaUsrValue != '') {
            $tDataSavDec = $oRes[0]->FTSysStaUsrValue;
        } else {
            $tDataSavDec = $oRes[0]->FTSysStaDefValue;
        }
    } else {
        //Decimal Default = 4
        $tDataSavDec = 4;
    }
    return $tDataSavDec;
}

//Functionality: Get Option Decical Show
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified :
//Return : 
//Return Type: Array
function FCNxHGetOptionDecimalShow(){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FTSysStaDefValue,FTSysStaUsrValue
             FROM  TSysConfig 
             WHERE FTSysCode = 'ADecPntShw'
            ";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
        if ($oRes[0]->FTSysStaUsrValue != '') {
            $tDataShwDec = $oRes[0]->FTSysStaUsrValue;
        } else {
            $tDataShwDec = $oRes[0]->FTSysStaDefValue;
        }
    } else {
        //Decimal Default = 2 
        $tDataShwDec = 2;
    }
    return $tDataShwDec;
}


//Functionality: Get Option Doc Save
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified :
//Return : 
//Return Type: Array
function FCNnHGetOptionDocSave(){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FTSysStaDefValue,FTSysStaUsrValue
             FROM  TSysConfig 
             WHERE  FTSysCode   = 'tDoc_AlwSavQty0'
             AND    FTSysKey    = 'TAPTOrdHD'";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
        if ($oRes[0]->FTSysStaUsrValue != '') {
            $tDataSavQty = $oRes[0]->FTSysStaUsrValue;
        } else {
            $tDataSavQty = $oRes[0]->FTSysStaDefValue;
        }
    } else {
        //Decimal Default = 2 
        $tDataSavQty = 1;
    }
    return $tDataSavQty;
}

//Functionality: Get Option Scan Sku
//Parameters:  Function Parameter
//Creator: 11/10/2018 Krit(Copter)
//Last Modified :
//Return : 
//Return Type: Array
function FCNnHGetOptionScanSku(){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FTSysStaDefValue,FTSysStaUsrValue
             FROM  TSysConfig 
             WHERE  FTSysCode   = 'tDoc_OptScanSku'
             AND    FTSysKey    = 'TAPTOrdHD'";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result();
        if ($oRes[0]->FTSysStaUsrValue != '') {
            $tDataScanSku = $oRes[0]->FTSysStaUsrValue;
        } else {
            $tDataScanSku = $oRes[0]->FTSysStaDefValue;
        }
    } else {
        //Decimal Default = 2 
        $tDataScanSku = 1;
    }
    return $tDataScanSku;
}


//Functionality: Get DocType
//Creator: 30/08/2018 Krit(Copter)
//Last Modified :
//Return : n
//Return Type: Array
function FCNnDOCGetDocType($ptTableName = ''){

    $tLangActive = $_SESSION['tLangEdit'];
    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FNSdtDocType FROM TSysDocType WHERE FTSdtTblName='$ptTableName'";

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {
        $oDetail = $oQuery->result();
        $nDetail = $oDetail[0]->FNSdtDocType;
    } else {
        $nDetail = '';
    }

    return $nDetail;
}

//Functionality: Get Rate Fact
//Creator: 30/08/2018 Krit(Copter)
//Last Modified :
//Return : t
//Return Type: Array
function FCNcDOCGetRateFac($ptRteCode = ''){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FCRteRate FROM TFNMRate WHERE FTRteCode='$ptRteCode'";

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {

        $oDetail = $oQuery->result();
        $cRteRate = $oDetail[0]->FCRteRate;
    } else {
        $cRteRate = '0.00';
    }

    return $cRteRate;
}


//Functionality: Get Vat Data
//Creator: 30/08/2018 Krit(Copter)
//Last Modified :
//Return : t
//Return Type: Array
function FCNcDOCGetVatData(){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT TOP 1 VAT.FTVatCode,VAT.FCVatRate,CMP.*  FROM
            (SELECT TOP 1 * FROM TCNMComp)CMP 
            INNER JOIN
            (SELECT  FTVatCode,FCVatRate,FDVatStart   FROM TCNMVatRate WHERE GETdate()> FDVatStart )VAT
            ON CMP.FTVatCode=VAT.FTVatCode
            ORDER BY FDVatStart DESC";

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {

        $oDetail = $oQuery->result();
        $tVatCode = $oDetail[0]->FTVatCode;
        $cVatRate = $oDetail[0]->FCVatRate;

        $aVatData = array(
            'tVatCode' => $tVatCode,
            'cVatRate' => $cVatRate,
        );
    } else {
        $aVatData = array(
            'tVatCode' => 0,
            'cVatRate' => 0,
        );
    }

    return $aVatData;
}


//Functionality: Get Department By User Login
//Creator: 30/08/2018 Krit(Copter)
//Last Modified :
//Return : n
//Return Type: Array
function FCNnDOCGetDepartmentByUser($ptUsrCode = ''){

    $ci = &get_instance();
    $ci->load->database();
    $tSQL = "SELECT FTDptCode  FROM TCNMUser WHERE FTUsrCode='$ptUsrCode'";

    $oQuery = $ci->db->query($tSQL);
    if ($oQuery->num_rows() > 0) {
        $oDetail = $oQuery->result();
        $tDptCode = $oDetail[0]->FTDptCode;
    } else {
        $tDptCode = '';
    }

    return $tDptCode;
}

function FCNtNumberToTextBaht($number){
    // Get Option Show Decimal  
    $nOptDecimalShow = FCNxHGetOptionDecimalShow();

    $ci = &get_instance();
    $ci->load->database();

    if (!preg_match('/^[0-9]+(?:\.[0-9]{2}){0,1}$/', $number = str_replace(',', '', $number))) {
        $number = number_format($number, $nOptDecimalShow);
        // return 'This is not currency format';
    }
    $num = explode(".", $number);
    $convert = FCNConvert($num[0]) . 'บาท' . ($st = FCNConvert($num[1])) . ($st > '' ? 'สตางค์' : '');

    return $convert;
}

function FCNConvert($num){
    $th_num = array('', array('หนึ่ง', 'เอ็ด'), array('สอง', 'ยี่'), 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ');
    $th_digit = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    $ln = strlen($num);
    $t = '';
    for ($i = $ln; $i > 0; $i--) {
        $x = $i - 1;
        $n = substr($num, $ln - $i, 1);
        $digit = $x % 6;
        if ($n != 0) {
            if ($n == 1) {
                $t .= $digit == 1 ? '' : $th_num[1][$digit == 0 ? 1 : 0];
            } elseif ($n == 2) {
                $t .= $th_num[2][$digit == 1 ? 1 : 0];
            } else {
                $t .= $th_num[$n];
            }
            $t .= $th_digit[($digit == 0 && $x > 0 ? 6 : $digit)];
        } else {
            $t .= $th_digit[$digit == 0 && $x > 0 ? 6 : 0];
        }
    }
    return $t;
}

//    ## วิธีใช้งาน
//    $y='10,000,021,654,321.50';
//    $z='10,000,011,000,321.25';
//    $a='10,000,000.00';

//    $x = new hk_baht( $y );
//    echo  $y . "=>" .$x->result;
//    echo '<br>', $z, "=>", $x->toBaht( $z ); 
//    echo '<br>', $a, "=>", $x->toBaht( $a );

/**
 * คำนวณ ภาษีท้ายบิล
 * ดึงข้อมูลจากตาราง TCNTDocDTTmp
 */
function FCNaDOCEndOfBillCalVat($paParams){

    $ci = &get_instance();
    $ci->load->model('document/document/mDocEndOfBill');

    $aParams = [
        'FTXthDocNo' => $paParams['tDocNo'],
        'FTXthDocKey' => $paParams['tDocKey'],
        'FNLngID' => $paParams['nLngID'],
        'FTSessionID' => $paParams['tSesSessionID'],
        'FTBchCode' => $paParams['tBchCode'],
    ];

    $aDTTemp = $ci->mDocEndOfBill->FSaMDOCEndOfBillGetDTSumVat($aParams);

    if (!empty($aDTTemp)) {
        $cVatSum = 0;
        foreach ($aDTTemp as $item) {
            $cVatSum += $item['FCXtdVat'];
        }
        $aResult['aItems'] = $aDTTemp;
        $aResult = array_merge($aResult, ['cVatSum' => number_format($cVatSum, 2)]);
    } else {
        $aResult['aItems'] = $aDTTemp;
        $aResult = array_merge($aResult, ['cVatSum' => number_format(0, 2)]);
    }


    return $aResult;
}

/**
 * คำนวณ รายการท้ายบิล
 * ดึงข้อมูลจากตาราง TCNTDocDTTmp
 */
function FCNaDOCEndOfBillCal($paParams){

    $ci = &get_instance();
    $ci->load->model('document/document/mDocEndOfBill');

    $tSplVatType = $paParams['tSplVatType'];

    $aParams = [
        'FTXthDocNo' => $paParams['tDocNo'],
        'FTXthDocKey' => $paParams['tDocKey'],
        'FNLngID' => $paParams['nLngID'],
        'FTSessionID' => $paParams['tSesSessionID'],
        'FTBchCode' => $paParams['tBchCode'],
    ];

    $aHDDisTemp = @$ci->mDocEndOfBill->FSaMDOCEndOfBillGetHDDisTmp($aParams)[0];
    $aDTTemp = @$ci->mDocEndOfBill->FSaMDOCEndOfBillGetDTTmp($aParams)[0];

    $aResult['cSumFCXtdNet'] = number_format($aDTTemp['FCXtdNet'], 2); // จำนวนเงินรวม

    $aResult['cSumFCXtdNetAfHD'] = number_format($aDTTemp['FCXtdNetAfHD'], 2); // ยอดรวมหลัง ลด/ชาร์จ

    $aResult['cSumFCXtdVat'] = number_format($aDTTemp['FCXtdVat'], 2); // ยอดรวมภาษีมูลค่าเพิ่ม

    if ($tSplVatType == '1') { // ภาษีรวมใน
        $aResult['cCalFCXphGrand'] = number_format($aDTTemp['FCXtdNetAfHD'], 2); // จำนวนเงินรวมที้งสิ้น
    }
    if ($tSplVatType == '2') { // ภาษีแยกนอก
        $aResult['cCalFCXphGrand'] = number_format($aDTTemp['FCXtdNetAfHD'] + $aDTTemp['FCXtdVat'], 2); // จำนวนเงินรวมที้งสิ้น
    }


    $aResult['cSumFCXtdAmt'] = number_format($aHDDisTemp['FCXtdAmt'], 2); // รวมมูลค่า ลด/ชาร์จ

    // $aResult['cSumFCXtdDisChg'] = number_format($aHDDisTemp['FCXtdDisChg'], 2); // รวม ลด/ชาร์จ

    $aResult['tDisChgTxt'] = $aHDDisTemp['FTXtdDisChgTxt']; // DisChg HD Text

    return $aResult;
}

/**
 * คำนวณ รายการท้ายบิล เมื่อมีการปรับรายการสินค้า
 * ดึงข้อมูลจากตาราง TCNTDocDTTmp
 */
function FSvCCreditNoteCalEndOfBillHDDis($paParams){

    $ci = &get_instance();
    $ci->load->model('document/document/mCreditNoteDisChgModal');

    $tDocNo = $paParams['tDocNo'];
    $tBchCode = $paParams['tBchCode'];
    $tSessionID = $paParams['tSessionID'];
    $tSplVatType = $paParams['tSplVatType'];
    $nLngID = $paParams['nLngID'];
    $tDocKey = $paParams['tDocKey'];
    $nSeqNo = $paParams['nSeqNo'];

    $ci->db->trans_begin();

    /*==================== Begin DB Process ==============================*/

    $aEndOfBillCalParams = [
        'tDocNo' => $tDocNo,
        'tDocKey' => $tDocKey,
        'nLngID' => $nLngID,
        'tSesSessionID' => $tSessionID,
        'tBchCode' => $tBchCode,
    ];
    $aEndOfBilCal = FCNaDOCEndOfBillCal($aEndOfBillCalParams);

    $aParams = [
        'tDocNo' => $tDocNo,
        'tBchCode' => $tBchCode,
        'tSessionID' => $tSessionID
    ];
    $aHDDisTmp = $ci->mCreditNoteDisChgModal->FSaMCreditNoteGetHDDisTemp($aParams);

    if (!empty($aHDDisTmp)) {

        $bFirstItem = true;
        $aItems = [];
        foreach ($aHDDisTmp as $key => $item) {

            $cDisChgBeforeDisChgOld = floatval($item['FCXtdTotalB4DisChg']); // ค่าก่อน ลด/ชาร์จ ก่อนปรับรายการสินค้า

            if ($bFirstItem) { // กำหนดค่าเริ่มต้น ปรับ ก่อนลด/ชาร์จ
                $cDisChgBeforeDisChg = floatval($aEndOfBilCal['cSumFCXtdNet']); // ค่าก่อน ลด/ชาร์จ หลังปรับรายการสินค้า
                echo $cDisChgBeforeDisChg;
                $bFirstItem = false;
            }

            $cCalc = 0.0;
            $nDisChgType = floatval($item['FTXtdDisChgType']);
            $cDisChgNum = floatval($item['FCXtdDisChg']);
            $cDisChgValue = floatval($item['FCXtdAmt']);
            $cDisChgAfterDisChg = floatval($item['FCXtdTotalAfDisChg']);



            switch ($nDisChgType) {
                case '1': { // ลดบาท
                        $cCalc = $cDisChgBeforeDisChg - $cDisChgNum;
                        $item['FCXtdTotalAfDisChg'] = $cCalc;
                        $item['FCXtdAmt'] = $cDisChgNum;
                        $item['FTXtdDisChgTxt'] = '-' . $cDisChgNum;
                        break;
                    }
                case '2': { // ลด %
                        $cDisChgPercent = ($cDisChgBeforeDisChg * $cDisChgNum) / 100;
                        $cCalc = $cDisChgBeforeDisChg - $cDisChgPercent;
                        $item['FCXtdTotalAfDisChg'] = $cCalc;
                        $item['FCXtdAmt'] = $cDisChgPercent;
                        $item['FTXtdDisChgTxt'] = '-' . $cDisChgNum . '%';
                        break;
                    }
                case '3': { // ชาร์จบาท
                        $cCalc = $cDisChgBeforeDisChg + $cDisChgNum;
                        $item['FCXtdTotalAfDisChg'] = $cCalc;
                        $item['FCXtdAmt'] = $cDisChgNum;
                        $item['FTXtdDisChgTxt'] = '+' . $cDisChgNum;
                        break;
                    }
                case '4': { // ชาร์จ %
                        $cDisChgPercent = ($cDisChgBeforeDisChg * $cDisChgNum) / 100;
                        $cCalc = $cDisChgBeforeDisChg + $cDisChgPercent;
                        $item['FCXtdTotalAfDisChg'] = $cCalc;
                        $item['FCXtdAmt'] = $cDisChgPercent;
                        $item['FTXtdDisChgTxt'] = '+' . $cDisChgNum . '%';
                        break;
                    }
            }

            $item['FCXtdTotalB4DisChg'] = $cDisChgBeforeDisChg;
            $item['FDLastUpdOn'] = date('Y-m-d H:i:s');
            $item['FTLastUpdBy'] = $ci->session->userdata('tSesUsername');

            $cDisChgBeforeDisChg = $cCalc; // 

            $aParams['tDateIns'] = $item['FDXtdDateIns'];

            $ci->mCreditNoteDisChgModal->FSaMCreditNoteUpdateHDDisTemp($aParams, $item);

            $aItems[$key] = $item;
        }

        // Prorat Call
        $aResProrat = FCNaHCalculateProrate('TAPTPcHD', $tDocNo);
        // var_dump($aResProrat);

        $aCalcDTParams = [
            'tDataDocEvnCall' => '',
            'tDataVatInOrEx' => $tSplVatType,
            'tDataDocNo' => $tDocNo,
            'tDataDocKey' => 'TAPTPcHD',
            'tDataSeqNo' => $nSeqNo
        ];
        FCNbHCallCalcDocDTTemp($aCalcDTParams);
    }

    /*==================== End DB Process ==============================*/

    if ($ci->db->trans_status() === FALSE) {
        $ci->db->trans_rollback();
        $aReturn = array(
            'nStaEvent'    => '900',
            'tStaMessg'    => "Unsucess process"
        );
    } else {
        $ci->db->trans_commit();
        $aReturn = array(
            'nStaEvent' => '1',
            'tStaMessg' => 'Success process'
        );
    }
}

// Function : เรียก TSysPdt
function FCNoHDOCGetTSysPdt($paParams){

    $ci = &get_instance();
    $ci->load->database();
    $nLngID = $paParams['nLngID'];
    $tPdtCode = $paParams['tPdtCode'];
    $tPdtSysTable = $paParams['tPdtSysTable'];
    $tSQL = "    SELECT 
                    *
                FROM  TSysPdt TSPDT WITH (NOLOCK)
                LEFT JOIN TSysPdt_L TSPDTL WITH (NOLOCK) ON TSPDT.FTPdtCode = TSPDTL.FTPdtCode AND TSPDTL.FNLngID = $nLngID
                WHERE TSPDT.FTPdtCode = '$tPdtCode' 
                AND TSPDT.FTPdtSysTable = '$tPdtSysTable'    
            ";

    $oQuery = $ci->db->query($tSQL);

    if ($oQuery->num_rows() > 0) {
        $oRes  = $oQuery->result()[0];
    } else {
        $oRes = NULL;
    }
    return $oRes;
}

/**
 * แปลงจำนวนเงินที่มี , ให้เป็น float
 * @param type $ptMoney
 * @return type
 */
function FCNcUnFormatMoneyToFloat($ptMoney){
    return floatval(preg_replace('/[^\d\.]/', '', $ptMoney));
}






///--------------StatDose Aprove------------------/////




    // Function: Check Approve Document And Load Format User Aprove From Roles To Trns
    // Parameters: tTableDocHD tApvCode tDocNo tBchCodes
    // Creator: 22/01/2020 Nattakit(Nale)
    // LastUpdate: -
    // Return: tReturnCode = 200 (success), tReturnMsg  (Dexcription)
    // ReturnType: Array
    function FSnDOHCheckLevelApr($paData){

        $ci = &get_instance();
        $ci->load->database();

        $tTableDocHD = $paData['tTableDocHD'];
        $tCreateBy   = $paData['tApvCode'];
        $tDocNo      = $paData['tDocNo'];
        $tBchCode    = $paData['tBchCode'];
        $dDocDate    = date('Y-m-d H:i:s');

        if(!empty($tTableDocHD)){

            $tSqlDocApr = "   SELECT
                            dbo.TCNMDocApvRole.FNDarApvSeq,
                            dbo.TCNMDocApvRole.FTDarUsrRole,
                            dbo.TCNMDocApvRole.FTDarRefType,
                            dbo.TSysDocApv.FTDapName,
                            dbo.TSysDocApv.FTDapNameOth
                        FROM
                            dbo.TCNMDocApvRole
                        INNER JOIN dbo.TSysDocApv ON dbo.TCNMDocApvRole.FNDarApvSeq = dbo.TSysDocApv.FNDapSeq
                        AND dbo.TCNMDocApvRole.FTDarTable = dbo.TSysDocApv.FTDapTable
                        WHERE
                            dbo.TCNMDocApvRole.FTDarTable = '$tTableDocHD'
                    ";

                   $oQuery = $ci->db->query($tSqlDocApr);
                   
                   $nNumrows = $oQuery->num_rows(); 
                
                if($nNumrows>0){

                    $aDataParam=array(
                        'tTableDocHD' => $tTableDocHD,
                        'tCreateBy'   => $tCreateBy,
                        'tDocNo'      => $tDocNo ,
                        'dDocDate'    => $dDocDate,
                        'tBchCode'    => $tBchCode
                    );

                    if(!empty($aDataParam)){

                      $aResult =  FSnDOHDMoveRoleToTrns($aDataParam);
                      
                      if($aResult==1){

                        $aReturn['tReturnCode'] = '200';
                        $aReturn['tReturnMsg'] = 'Success Function Insert Level Apr';
                        return $aReturn;

                      }else{

                        $aReturn['tReturnCode'] = '500';
                        $aReturn['tReturnMsg'] = 'This function error!';
                        return $aReturn;

                      }

                    }else{

                        $aReturn['tReturnCode'] = '202';
                        $aReturn['tReturnMsg'] = 'Doc Approve Only User';
                        return $aReturn;

                    }

                }else{
                    $aReturn['tReturnCode'] = '202';
                    $aReturn['tReturnMsg'] = 'Doc Approve Only User';
                    return $aReturn;
                }

        }else{

            $aReturn['tReturnCode'] = '404';
            $aReturn['tReturnMsg'] = 'Table Is Empty !';
            return $aReturn;
        }
    }


    // Function: Clone From table Role To Trns For Document.
    // Parameters: tTableDocHD tApvCode tDocNo dDocDate tBchCodes
    // Creator: 22/01/2020 Nattakit(Nale)
    // LastUpdate: -
    // Return: 1,2
    // ReturnType: number

    function FSnDOHDMoveRoleToTrns($paDataInsert){

        $ci = &get_instance();
        $ci->load->database();

        $tTableDocHD = $paDataInsert['tTableDocHD'];
        $tCreateBy   = $paDataInsert['tCreateBy'];
        $tDocNo      = $paDataInsert['tDocNo'];
        $dDocDate    = $paDataInsert['dDocDate'];
        $tBchCode    = $paDataInsert['tBchCode'];

         $nCountrow = FSnDOHNumRowTnxTable($paDataInsert);
     
        if($nCountrow<=0){

       
        $tSql ="INSERT INTO TARTDocApvTxn (
                    FTBchCode,
                    FTDatRefCode,
                    FTDatRefType,
                    FNDatApvSeq,
                    FDCreateOn,
                    FTCreateBy
                ) SELECT
                    '$tBchCode' AS FTBchCode,
                    '$tDocNo' AS FTDatRefCode,
                    TCNMDocApvRole.FTDarRefType,
                    TCNMDocApvRole.FNDarApvSeq,
                    GETDATE() AS FDCreateOn,
                    '$tCreateBy' AS FTCreateBy
                FROM
                    TCNMDocApvRole
                WHERE
                    TCNMDocApvRole.FTDarTable = '$tTableDocHD'
        ";


        $oQuery = $ci->db->query($tSql);
    //    echo  $ci->db->last_query(); 
    //    die(); 
        if($oQuery){
            $nReustl = 1;
        }else{
            $nReustl = 2;
        }
    }else{
        $nReustl = 1;
    }
        return $nReustl;

    }


    // Function: Clone From table Role To Trns For Document.
    // Parameters: tTableDocHD tApvCode tDocNo dDocDate tBchCodes
    // Creator: 23/01/2020 Nattakit(Nale)
    // LastUpdate: -
    // Return: $aResult
    // ReturnType: array
function FNaDOHNCheckSeqAprve($paData){

    $ci = &get_instance();
    $ci->load->database();

    $tDocNo = $paData['tDocNo'];
    $tApvCode = $paData['tApvCode'];
    $tTableDocHD = $paData['tTableDocHD'];
    $tBchCode = $paData['tBchCode'];
    $nLangEdit = $ci->session->userdata("tLangEdit");
    if($nLangEdit==1){
         $tFildName = 'FTDapName';
    }else{
         $tFildName = 'FTDapNameOth';
    }
            $tSql="  SELECT
                        dbo.TARTDocApvTxn.FNDatApvSeq,
                        dbo.TARTDocApvTxn.FTDatRefType,
                        dbo.TARTDocApvTxn.FTDatRefCode,
                        dbo.TARTDocApvTxn.FTBchCode,
                        dbo.TARTDocApvTxn.FTDatUsrApv,
                        dbo.TARTDocApvTxn.FDDatDateApv,
                        dbo.TCNMDocApvRole.FTDarTable,
                        dbo.TCNMDocApvRole.FTDarUsrRole,
                        dbo.TCNMDocApvRole.FNDarApvSeq,
                        (
                                        SELECT
                                            MAX (FNDatApvSeq)
                                        FROM
                                            TARTDocApvTxn
                                        WHERE
                                            TARTDocApvTxn.FTBchCode = '$tBchCode'
                                        AND TARTDocApvTxn.FTDatRefCode = '$tDocNo'
                                        AND TARTDocApvTxn.FTDatUsrApv IS NOT NULL
                                        GROUP BY
                                            TARTDocApvTxn.FTDatRefCode
                                    ) AS LastApvSeq,
                        dbo.TSysDocApv.$tFildName AS FTDapName,
                        dbo.TCNMUsrRole_L.FTRolName,
                        dbo.TCNMUser_L.FTUsrName

                        FROM
                        dbo.TARTDocApvTxn
                        INNER JOIN dbo.TCNMDocApvRole ON dbo.TARTDocApvTxn.FNDatApvSeq = dbo.TCNMDocApvRole.FNDarApvSeq AND dbo.TCNMDocApvRole.FTDarTable = '$tTableDocHD'
                        INNER JOIN dbo.TSysDocApv ON dbo.TCNMDocApvRole.FNDarApvSeq = dbo.TSysDocApv.FNDapSeq AND dbo.TSysDocApv.FTDapTable = '$tTableDocHD'
                        LEFT OUTER JOIN dbo.TCNMUsrRole_L ON dbo.TCNMDocApvRole.FTDarUsrRole = dbo.TCNMUsrRole_L.FTRolCode AND dbo.TCNMUsrRole_L.FNLngID='$nLangEdit'
                        LEFT OUTER JOIN dbo.TCNMUser_L ON dbo.TARTDocApvTxn.FTDatUsrApv = dbo.TCNMUser_L.FTUsrCode AND dbo.TCNMUser_L.FNLngID='$nLangEdit'
                        WHERE
                        dbo.TARTDocApvTxn.FTBchCode = '$tBchCode' AND
                        dbo.TARTDocApvTxn.FTDatRefCode = '$tDocNo'
                        ORDER BY dbo.TARTDocApvTxn.FNDatApvSeq ASC
                         ";

        $oQuery = $ci->db->query($tSql);

        $aResult = $oQuery->result_array();

        return $aResult;
}

    // Function: Clone From table Role To Trns For Document.
    // Parameters: tTableDocHD tApvCode tDocNo dDocDate tBchCodes
    // Creator: 22/01/2020 Nattakit(Nale)
    // LastUpdate: -
    // Return: 1,2
    // ReturnType: number
 function FSnDOHNumRowTnxTable($paDataInsert){

    $ci = &get_instance();
    $ci->load->database();

        // $tTableDocHD = $paDataInsert['tTableDocHD'];
        // $tCreateBy   = $paDataInsert['tCreateBy'];
        $tDocNo      = $paDataInsert['tDocNo'];
        // $dDocDate    = $paDataInsert['dDocDate'];
        $tBchCode    = $paDataInsert['tBchCode'];

        $tSqlCount = "
            SELECT COUNT(*) AS nNums FROM [dbo].[TARTDocApvTxn]
             WHERE FTBchCode='$tBchCode'
             AND FTDatRefCode = '$tDocNo';
              ";
       
       $oQuery = $ci->db->query($tSqlCount);
      $aRes = $oQuery->row_array();
       return $aRes['nNums'];

    }
    // Function: Clone From table Role To Trns For Document.
    // Parameters: tTableDocHD tApvCode tDocNo dDocDate tBchCodes
    // Creator: 22/01/2020 Nattakit(Nale)
    // LastUpdate: -
    // Return: 1,2
    // ReturnType: number
 function FSnDOHUpdateTableMutiAprve($paData){

    $ci = &get_instance();
    $ci->load->database();

    $tRoleCode   = $paData['tRoleCode'];
    $tDatRefCode = $paData['FTDatRefCode'];
    $tBchCode    = $paData['FTBchCode'];
    $tTableDocHD    = $paData['tTableDocHD'];
    
         $tSql="
                    SELECT
                        TOP 1
                        dbo.TARTDocApvTxn.FNDatApvSeq,
                        dbo.TARTDocApvTxn.FTDatRefType,
                        dbo.TARTDocApvTxn.FTDatRefCode,
                        dbo.TARTDocApvTxn.FTBchCode,
                        dbo.TARTDocApvTxn.FTDatUsrApv,
                        dbo.TARTDocApvTxn.FDDatDateApv,
                        dbo.TCNMDocApvRole.FTDarTable,
                        dbo.TCNMDocApvRole.FTDarUsrRole,
                        dbo.TCNMDocApvRole.FNDarApvSeq
                    FROM
                    dbo.TARTDocApvTxn
                       INNER JOIN dbo.TCNMDocApvRole ON dbo.TARTDocApvTxn.FNDatApvSeq = dbo.TCNMDocApvRole.FNDarApvSeq AND dbo.TCNMDocApvRole.FTDarTable='$tTableDocHD'
                    WHERE
                        dbo.TARTDocApvTxn.FTBchCode='$tBchCode'
                        AND dbo.TARTDocApvTxn.FTDatRefCode='$tDatRefCode'
                        AND dbo.TARTDocApvTxn.FDDatDateApv IS NULL
                        AND dbo.TARTDocApvTxn.FTDatUsrApv IS NULL
                        ORDER BY dbo.TARTDocApvTxn.FNDatApvSeq
        ";

        $oQuery = $ci->db->query($tSql);

        // echo $ci->db->last_query();
        // die();
        $aTnx = $oQuery->row_array();

        if(!empty($aTnx)){

            if($aTnx['FTDarUsrRole']=='' || $aTnx['FTDarUsrRole']==$tRoleCode){
                 $aResult =  array(
                                    'nReturnCode' => 1 ,
                                    'FNDatApvSeq' => $aTnx['FNDatApvSeq']
                                    );
            }else{
                $aResult = array(
                    'nReturnCode' => 2,
                    'FNDatApvSeq' => ''
                    );
            }

        }else{
            $aResult = array(
                'nReturnCode' => 2 ,
                'FNDatApvSeq' => ''
                );
        }

        return $aResult;


}


    // Function: Clone From table Role To Trns For Document.
    // Parameters: tTableDocHD tApvCode tDocNo dDocDate tBchCodes
    // Creator: 22/01/2020 Nattakit(Nale)
    // LastUpdate: -
    // Return: 1,2
    // ReturnType: number

function FSnDOHAInsertForMultiAprve($paData){
        // TARTSoHD
        $ci = &get_instance();
        $ci->load->database();
    $nCheckPerAprv = FSnDOHUpdateTableMutiAprve($paData);//ตรวจสอบลำดับที่จะอนุมัติ
    // echo '<pre>';
    //     print_r($nCheckPerAprv);
    // echo '</pre>';
    // die();
    if($nCheckPerAprv['nReturnCode']==1){

        $ci->db->trans_begin();
        $tRoleCode = $paData['tRoleCode'];
        $tDatRefCode = $paData['FTDatRefCode'];
        $tBchCode = $paData['FTBchCode'];
        $nDatApvSeq = $nCheckPerAprv['FNDatApvSeq'];

        $dLastUpdOn = date('Y-m-d H:i:s');
        $tLastUpdBy = $ci->session->userdata('tSesUsername');
        $tDatUsrApv = $paData['FTDatUsrApv'];
        $dDatDateApv = $paData['FDDatDateApv'];
        $tDatRmk = $paData['FTDatRmk'];

        $ci->db->set('FDLastUpdOn',$dLastUpdOn);
        $ci->db->set('FTLastUpdBy',$tLastUpdBy);
        $ci->db->set('FTDatUsrApv',$tDatUsrApv);
        $ci->db->set('FDDatDateApv',$dDatDateApv);
        $ci->db->set('FTDatRmk',$tDatRmk);
        $ci->db->set('FTDatStaPrc',1);
        $ci->db->where('FTDatRefCode',$tDatRefCode);
        $ci->db->where('FTBchCode',$tBchCode);
        $ci->db->where('FNDatApvSeq',$nDatApvSeq);

        $ci->db->update('TARTDocApvTxn');

        // echo '<pre>';
        // print_r($nCheckPerAprv);
        // echo '</pre>';
        // echo $ci->db->last_query();
        // die();
        if($ci->db->trans_status() === FALSE){
            $ci->db->trans_rollback();
            $aDatRetrun = array(
                'nStaEvent' => '900',
                'tStaMessg' => "Error Cannot Update Status Approve Document."
            );
        }else{
            $ci->db->trans_commit();
            $aDatRetrun = array(
                'nStaEvent' => '1',
                'tStaMessg' => "Update Status Document Approve Success."
            );
        }

    }else{
        $aDatRetrun = array(
            'nStaEvent' => '990',
            'tStaMessg' => "You don't have permission to approve document."
        );
    }
        return $aDatRetrun;
    


}

    // Function: Load call List product.
    // Parameters: ptOpen , ptClose 
    // Creator: 30/04/2020 Saharat(Golf)
    // LastUpdate: -
    // Return: view
    // ReturnType: view
function FSvPDTLoadingPage($paData){

    $CI            =  &get_instance();
    $tPageLoading  =  ['tPageLoading' => $paData];
    $tviewOpen     = 'common/wOpenLoading'; 
    $oOutput       = 'true';

    $tPageLoading  = $CI->load->view($tviewOpen, $tPageLoading, $oOutput);
    echo  $tPageLoading;
}



