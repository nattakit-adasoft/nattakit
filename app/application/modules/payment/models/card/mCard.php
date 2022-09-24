<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class mCard extends CI_Model {

    //Functionality : list Data Card
    //Parameters : function parameters
    //Creator :  10/10/2018 Wasin
    //Return : data
    //Return Type : Array
    public function FSaMCRDList($paData){
        try{
            $aRowLen        = FCNaHCallLenData($paData['nRow'],$paData['nPage']);
            $nLngID         = $paData['FNLngID'];
            $tSearchList    = $paData['tSearchAll'];
            $tSQL           = "SELECT c.* FROM(
                                    SELECT  ROW_NUMBER() OVER(ORDER BY FDCreateOn DESC) AS rtRowID,* FROM
                                        (SELECT DISTINCT
                                            CRD.FTCrdCode       AS rtCrdCode,
                                            CRD.FTCrdHolderID   AS rtCrdHolderID,
                                            CRD_L.FTCrdName     AS rtCrdName,
                                            CTY_L.FTCtyName     AS rtCrdCtyName,
                                            CRD.FCCrdValue      AS rtCrdValue,
                                            CRD.FDCrdExpireDate AS rtCrdExpireDate,
                                            CRD.FTCrdStaActive  AS rtCrdStaActive,
                                            CRD.FDCreateOn   AS FDCreateOn
                                        FROM TFNMCard CRD
                                        LEFT JOIN TFNMCard_L CRD_L ON CRD.FTCrdCode = CRD_L.FTCrdCode AND CRD_L.FNLngID = $nLngID
                                        LEFT JOIN TFNMCardType_L CTY_L ON CRD.FTCtyCode = CTY_L.FTCtyCode AND CTY_L.FNLngID = $nLngID
                                        WHERE 1=1 ";
            if(isset($tSearchList) && !empty($tSearchList)){

                if($tSearchList == 'ใช้งาน'){
                    $tSQL .= " AND (CRD.FTCrdStaActive = 1) "; 
                }else if($tSearchList == 'ไม่ใช้งาน'){
                    $tSQL .= " AND (CRD.FTCrdStaActive = 2) "; 
                }else if($tSearchList == 'ยกเลิก'){
                    $tSQL .= " AND (CRD.FTCrdStaActive = 3) "; 
                }else{
                    $tSQL .= " AND (CRD.FTCrdCode   LIKE '%$tSearchList%'";
                    $tSQL .= " OR CRD.FTCrdHolderID LIKE '%$tSearchList%'";
                    $tSQL .= " OR CRD_L.FTCrdName   LIKE '%$tSearchList%'";
                    $tSQL .= " OR CTY_L.FTCtyName   LIKE '%$tSearchList%')";
                }

            }
            $tSQL .= ") Base) AS c WHERE c.rtRowID > $aRowLen[0] AND c.rtRowID <= $aRowLen[1]";
            $oQuery = $this->db->query($tSQL);
            if($oQuery->num_rows() > 0){
                $aList = $oQuery->result_array();
                $oFoundRow = $this->FSoMCRDGetPageAll($tSearchList,$nLngID);
                $nFoundRow = $oFoundRow[0]->counts;
                $nPageAll = ceil($nFoundRow/$paData['nRow']); //หา Page All จำนวน Rec หาร จำนวนต่อหน้า
                $aResult = array(
                    'raItems'       => $aList,
                    'rnAllRow'      => $nFoundRow,
                    'rnCurrentPage' => $paData['nPage'],
                    'rnAllPage'     => $nPageAll,
                    'rtCode'        => '1',
                    'rtDesc'        => 'success',
                );
            }else{
                //No Data
                $aResult = array(
                    'rnAllRow' => 0,
                    'rnCurrentPage' => $paData['nPage'],
                    "rnAllPage"=> 0,
                    'rtCode' => '800',
                    'rtDesc' => 'data not found',
                );
            }
            return $aResult;
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : All Page Of Card
    //Parameters : function parameters
    //Creator :  10/10/2018 Wasin
    //Return : object Count All Product Type
    //Return Type : Object
    public function FSoMCRDGetPageAll($ptSearchList,$pnLngID){
        try{
            $tSQL = "SELECT COUNT (CRD.FTCrdCode) AS counts
                     FROM TFNMCard CRD
                     LEFT JOIN TFNMCard_L CRD_L ON CRD.FTCrdCode = CRD_L.FTCrdCode AND CRD_L.FNLngID = $pnLngID
                     LEFT JOIN TFNMCardType_L CTY_L ON CRD.FTCtyCode = CTY_L.FTCtyCode AND CTY_L.FNLngID = $pnLngID
                     WHERE 1=1 ";
            if(isset($ptSearchList) && !empty($ptSearchList)){

                if($ptSearchList == 'ใช้งาน'){
                    $tSQL .= " AND (CRD.FTCrdStaActive = 1) "; 
                }else if($ptSearchList == 'ไม่ใช้งาน'){
                    $tSQL .= " AND (CRD.FTCrdStaActive = 2) "; 
                }else if($ptSearchList == 'ยกเลิก'){
                    $tSQL .= " AND (CRD.FTCrdStaActive = 3) "; 
                }else{
                    $tSQL .= " AND (CRD.FTCrdCode   LIKE '%$ptSearchList%'";
                    $tSQL .= " OR CRD.FTCrdHolderID LIKE '%$ptSearchList%'";
                    $tSQL .= " OR CRD_L.FTCrdName   LIKE '%$ptSearchList%'";
                    $tSQL .= " OR CTY_L.FTCtyName   LIKE '%$ptSearchList%')";
                }
            }
            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0) {
                return $oQuery->result();
            }else{
                return false;
            }
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Get Data Card By ID
    //Parameters : function parameters
    //Creator : 10/10/2018 Wasin
    //Return : data
    //Return Type : Array
    public function FSaMCRDGetDataByID($paData){
        try{
            $tCrdCode   = $paData['FTCrdCode'];
            $nLngID     = $paData['FNLngID'];
            $tSQL       = " SELECT
                                CRD.FTCrdCode       AS rtCrdCode,
                                CRD.FTCrdHolderID   AS rtCrdHolderID,
                                CRD.FTCrdRefID      AS rtCrdRefID,
                                CRD_L.FTCrdName     AS rtCrdName,
                                CRD.FTCtyCode       AS rtCrdCtyCode,
                                CTY_L.FTCtyName     AS rtCrdCtyName,
                                CRD.FCCrdValue      AS rtCrdValue,
                                CRD.FCCrdDeposit    AS rtCrdDeposit,
                                CRD.FDCrdStartDate  AS rtCrdStartDate,
                                CRD.FDCrdExpireDate AS rtCrdExpireDate,
                                CRD.FTCrdStaType    AS rtCrdStaType,
                                CRD.FTCrdStaLocate  AS rtCrdStaLocate,
                                CRD.FTCrdStaShift   AS rtCrdStaShift,
                                CRD.FTCrdStaActive  AS rtCrdStaActive,
                                CRD_L.FTCrdRmk      AS rtCrdRmk,
                                CRD.FTDptCode       AS rtCrdDepartmentCode,
                                DPT_L.FTDptName     AS rtCrdDepartmentName,
                                IMG.FTImgObj        AS rtCrdImgObj
                            FROM TFNMCard CRD
                            LEFT JOIN TFNMCard_L CRD_L ON CRD.FTCrdCode = CRD_L.FTCrdCode AND CRD_L.FNLngID = $nLngID
                            LEFT JOIN TFNMCardType_L CTY_L ON CRD.FTCtyCode = CTY_L.FTCtyCode AND CTY_L.FNLngID = $nLngID
                            LEFT JOIN TCNMUsrDepart_L DPT_L ON CRD.FTDptCode = DPT_L.FTDptCode AND DPT_L.FNLngID = $nLngID
                            LEFT JOIN TCNMImgObj IMG ON IMG.FTImgRefID = CRD.FTCrdCode AND IMG.FTImgTable = 'TFNMCard'
                            WHERE 1=1 AND CRD.FTCrdCode = '$tCrdCode' ";
            $oQuery = $this->db->query($tSQL);
            if ($oQuery->num_rows() > 0){
                $aDetail = $oQuery->row_array();
                $aResult = array(
                    'raItems'   => $aDetail,
                    'rtCode'    => '1',
                    'rtDesc'    => 'success',
                );
            }else{
                $aResult = array(
                    'rtCode' => '800',
                    'rtDesc' => 'Data not found.',
                );
            }
            return $aResult;
        }catch(Exception $Error){
            echo $Error;
        }
    }


    //Functionality : Checkduplicate Cards
    //Parameters : function parameters
    //Creator : 10/10/2018 Wasin
    //Return : data
    //Return Type : Array
    public function  FSnMCRDCheckDuplicate($ptCrdCode){
        try{
            $tSQL = "SELECT COUNT(CRD.FTCrdCode) AS counts
                 FROM TFNMCard CRD 
                 WHERE CRD.FTCrdCode = '$ptCrdCode' ";
            $oQuery = $this->db->query($tSQL);
            if($oQuery->num_rows() > 0){
                return $oQuery->row_array();
            }else{
                return FALSE;
            }
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Add/Update Table Card
    //Parameters : function parameters
    //Creator : 10/10/2018 Wasin
    //Return : Array Stutus Add/Update
    //Return Type : Array
    public function FSaMCRDAddUpdateMaster($paDataCard){
        try{
            // Update Card Main Table
            $this->db->where('FTCrdCode', $paDataCard['FTCrdCode']);
            $this->db->update('TFNMCard',array(
                'FDCrdStartDate'    => $paDataCard['FDCrdStartDate'],
                'FDCrdExpireDate'   => $paDataCard['FDCrdExpireDate'],
                'FTCtyCode'         => $paDataCard['FTCtyCode'],
                'FCCrdDeposit'      => $paDataCard['FCCrdDeposit'],
                'FTCrdHolderID'     => $paDataCard['FTCrdHolderID'],
                'FTCrdRefID'        => $paDataCard['FTCrdRefID'],
                'FTCrdStaType'      => $paDataCard['FTCrdStaType'],
                'FTDptCode'         => $paDataCard['FTDptCode'],
                //'FTCrdStaLocate'    => $paDataCard['FTCrdStaLocate'],
                'FTCrdStaActive'    => $paDataCard['FTCrdStaActive'],
                'FDLastUpdOn'       => $paDataCard['FDLastUpdOn'],
                'FTLastUpdBy'       => $paDataCard['FTLastUpdBy']
            ));
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Update Card Success.',
                );
            }else{
                //Add Card Main Table
                $this->db->insert('TFNMCard', array(
                    'FTCrdCode'         => $paDataCard['FTCrdCode'],
                    'FDCrdStartDate'    => $paDataCard['FDCrdStartDate'],
                    'FDCrdExpireDate'   => $paDataCard['FDCrdExpireDate'],
                    'FTCtyCode'         => $paDataCard['FTCtyCode'],
                    'FCCrdDeposit'      => $paDataCard['FCCrdDeposit'],
                    'FTCrdHolderID'     => $paDataCard['FTCrdHolderID'],
                    'FTCrdRefID'        => $paDataCard['FTCrdRefID'],
                    'FTCrdStaType'      => $paDataCard['FTCrdStaType'],
                    'FTDptCode'         => $paDataCard['FTDptCode'],
                    //'FTCrdStaLocate'    => $paDataCard['FTCrdStaLocate'],
                    'FTCrdStaShift'     => $paDataCard['FTCrdStaShift'],
                    'FTCrdStaActive'    => $paDataCard['FTCrdStaActive'],
                    'FDCreateOn'        => $paDataCard['FDCreateOn'],
                    'FTCreateBy'        => $paDataCard['FTCreateBy']
                ));
                if($this->db->affected_rows() > 0){
                    $aStatus = array(
                        'rtCode' => '1',
                        'rtDesc' => 'Add Card Success.',
                    );
                }else{
                    $aStatus = array(
                        'rtCode' => '905',
                        'rtDesc' => 'Error Cannot Add/Edit Card.',
                    );
                }
            }
            return $aStatus;
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Update Card Lang
    //Parameters : function parameters
    //Creator : 10/10/2018 Wasin
    //Return : Array Stutus Add Update
    //Return Type : array
    public function FSaMCRDAddUpdateLang($paDataCard){
        try{
            //Update Card Lang
            $this->db->where('FNLngID', $paDataCard['FNLngID']);
            $this->db->where('FTCrdCode', $paDataCard['FTCrdCode']);
            $this->db->update('TFNMCard_L',array(
                'FTCrdName' => $paDataCard['FTCrdName'],
                'FTCrdRmk'  => $paDataCard['FTCrdRmk']
            ));
            if($this->db->affected_rows() > 0){
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Update Card Lang Success.',
                );
            }else{
                //Add Card Lang
                $this->db->insert('TFNMCard_L', array(
                    'FTCrdCode' => $paDataCard['FTCrdCode'],
                    'FNLngID'   => $paDataCard['FNLngID'],
                    'FTCrdName' => $paDataCard['FTCrdName'],
                    'FTCrdRmk'  => $paDataCard['FTCrdRmk']
                ));
                if($this->db->affected_rows() > 0){
                    $aStatus = array(
                        'rtCode' => '1',
                        'rtDesc' => 'Add Card Lang Success.',
                    );
                }else{
                    $aStatus = array(
                        'rtCode' => '905',
                        'rtDesc' => 'Error Cannot Add/Edit Card Lang.',
                    );
                }
            }
            return $aStatus;
        }catch(Exception $Error){
            echo $Error;
        }
    }

    //Functionality : Delete Card
    //Parameters : function parameters
    //Creator : 10/10/2018 Wasin
    //Return : Status Delete
    //Return Type : array
    public function FSnMCRDDel($paData){
        try{
            $this->db->trans_begin();
            $this->db->where_in('FTCrdCode', $paData['FTCrdCode']);
            $this->db->delete('TFNMCard');

            $this->db->where_in('FTCrdCode', $paData['FTCrdCode']);
            $this->db->delete('TFNMCard_L');

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $aStatus = array(
                    'rtCode' => '905',
                    'rtDesc' => 'Delete Unsuccess.',
                );
            }else{
                $this->db->trans_commit();
                $aStatus = array(
                    'rtCode' => '1',
                    'rtDesc' => 'Delete Success.',
                );
            }
            return $aStatus;
        }catch(Exception $Error){
            echo $Error;
        }
    }


    
    public function FSnMCRDChkStaCrd($ptCrdHolderID){
        $tSQL = "SELECT COUNT(FTCrdCode) AS nCardActived 
        FROM TFNMCard 
        WHERE FTCrdHolderID = '$ptCrdHolderID' AND FTCrdStaActive = 1";
        $oQuery = $this->db->query($tSQL);

        if ($oQuery->num_rows() > 0){
            return $oQuery->result_array();
        }

    }

}