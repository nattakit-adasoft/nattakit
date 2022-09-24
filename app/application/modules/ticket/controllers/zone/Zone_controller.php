<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Zone_controller extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->model ( 'eticket/zone/mZone', 'mZone' );
		$this->load->library ( "session" );
	}
	public function FSxCZNE($nLocID) {
		$oHeader = $this->Zone_model->FSxMZNEHeader ( $nLocID );
		$oArea = $this->Zone_model->FSxMZNEArea ( $nLocID );
		$oAuthen = FCNaHCheckAlwFunc('EticketBranch');
		$this->load->view ( 'eticket/zone/wZone', array (
			'oAuthen' => $oAuthen,
			'oHeader' => $oHeader,
			'oArea' => $oArea,
			'nLocID' => $nLocID 
		) );
	}
	
	/**
	 * FS แสดงข้อมูล Table ในโซน
	 */
	public function FSxCZNEList() {
		$tFTZneName = $this->input->post ( 'tFTZneName' );
		$nLocID = $this->input->post ( 'nLocID' );
		$nPrkID = $this->input->post ( 'nPrkID' );
		$nPageNo = $this->input->post ( 'nPageNo' );
		$nPageActive = $nPageNo;
		$oZneList = $this->Zone_model->FSxCZNEList ( $tFTZneName, $nLocID, $nPageActive );
		$oAuthen = FCNaHCheckAlwFunc('EticketBranch');
		$this->load->view ( 'eticket/zone/wZoneList', array (
			'oAuthen' => $oAuthen,
			'oZneList' => $oZneList,
			'nPrkID' => $nPrkID 
		) );
	}
	public function FStCZNECount() {
		$tFTZneName = $this->input->post ( 'tFTZneName' );
		$nLocID = $this->input->post ( 'nLocID' );
		$oZneCntSh = $this->Zone_model->FStMZNECount ( $tFTZneName, $nLocID );
		$tZneCount = $oZneCntSh [0]->counts;
		echo $tZneCount;
	}
	
	/**
	 * FS เพิ่มข้อมูลโซน
	 */
	public function FSxCZNEAdd($nLocID) {
		$oHeader = $this->Zone_model->FSxMZNEHeader ( $nLocID );
		$oArea = $this->Zone_model->FSxMZNEArea ( $nLocID );
		$oLev = $this->Zone_model->FSxMZNELoadLev ( $nLocID );
		$this->load->view ( 'eticket/zone/wAdd', array (
			'oHeader' => $oHeader,
			'oArea' => $oArea,
			'nLocID' => $nLocID,
			'oLev' => $oLev 
		) );
	}
	public function FSxCZNEAddAjax() {
		if ($this->input->post ( 'oetFTZneName' )) {
			$aData = array (
				'FTZneName' => $this->input->post ( 'oetFTZneName' ),
				'FTZneRmk' => $this->input->post ( 'otaFTZneRmk' ),
				'FNLevID' => ($this->input->post ( 'ocmFNLevID' ) == '' ? '' : $this->input->post ( 'ocmFNLevID' )),
				'FTZneBookingType' => $this->input->post ( 'ocmFTZneBookingType' ),
				'FNZneRow' => ($this->input->post ( 'oetFNZneRow' ) == "" ? 0 : $this->input->post ( 'oetFNZneRow' )),
				'FNZneCol' => ($this->input->post ( 'oetFNZneCol' ) == "" ? 0 : $this->input->post ( 'oetFNZneCol' )),
				'FNZneRowStart' => ($this->input->post ( 'oetFNZneRowStart' ) == "" ? 0 : $this->input->post ( 'oetFNZneRowStart' )),
				'FNZneColStart' => ($this->input->post ( 'oetFNZneColStart' ) == "" ? 0 : $this->input->post ( 'oetFNZneColStart' )),

				'FNLocID' => $this->input->post ( 'ohdFNLocID' ) 
			);
			$nZneID = $this->Zone_model->FSxMZNEAdd ( $aData );
			if ($this->input->post ( 'ohdZoneImg' )) {
				$tImg = $this->input->post ( 'ohdZoneImg' );
				FSaHAddImgObj($nZneID, 1, 'TTKMImgObj', 3,'main' , $tImg, 'zone');
			}
			echo $nZneID;
		}
	}
	
	/**
	 * FS สร้างที่นั่ง
	 */
	public function FSxCZNECreateSeat() {
		if ($this->input->post ( 'ohdFNZneRow' )) {
			$nSeatPerCol = $this->input->post ( 'ohdFNZneCol' ); // จำนวนที่นั่งต่อแถว
			$nTotalRows = $this->input->post ( 'ohdFNZneRow' ); // จำนวนแถว
			$nTotalSeat = $nSeatPerCol * $nTotalRows; // จำนวนแถวทั้งหมด
			$nSeatStartNo = $this->input->post ( 'ohdFNZneRowStart' ); // เลขที่นั่งเริ่มต้น
			$nSeatPerRows = ceil ( $nTotalSeat / $nTotalRows );
			$s = 0;
			for($t = 0; $t < $nTotalRows; $t ++) {
				$tSignName = GenSignName ( $t );
				for($i = 0; $i < $nSeatPerCol; $i ++) {
					$nSeatNo = $nSeatStartNo ++;
					$n = $i + 1;
					$tSeat = array (
						'FNLevID' => $this->input->post ( 'ohdFNLevIDSet' ),
						'FNLocID' => $this->input->post ( 'ohdFNLocIDSet' ),
						'FNZneID' => $this->input->post ( 'ohdFNZneIDSet' ),
						'FTSetRowChr' => $tSignName,
						'FNSetRowSeq' => $t + 1,
						'FTSetColChr' => $i + 1,
						'FNSetColSeq' => $i + 1,
						'FTSetName' => $tSignName . $n 
					);
					$this->Zone_model->FSxMZNEAddSeat ( $tSeat );
				}
			}
			echo 'Save Success';
		}
	}
	
	/**
	 * FS แก้ไขข้อมูลโซน
	 */
	public function FSxCZNEEdit($nLocID, $nZneID) {
		$oHeader = $this->Zone_model->FSxMZNEHeader ( $nLocID );
		$oArea = $this->Zone_model->FSxMZNEArea ( $nLocID );
		$oLev = $this->Zone_model->FSxMZNELoadLev ( $nLocID );
		$oShow = $this->Zone_model->FSxMZNEShowEdit ( $nZneID );
		$this->load->view ( 'eticket/zone/wEdit', array (
			'oHeader' => $oHeader,
			'oArea' => $oArea,
			'nLocID' => $nLocID,
			'oLev' => $oLev,
			'oShow' => $oShow,
			'nZneID' => $nZneID 
		) );
	}
	public function FSxCZNEEditAjax() {
		if ($this->input->post ( 'oetHDFNZneID' )) {
			$aData = array (
				'FNZneID' => $this->input->post ( 'oetHDFNZneID' ),
				'FNLevID' => $this->input->post ( 'ocmFNLevID' ),
				'FTZneName' => $this->input->post ( 'oetFTZneName' ),
				'FTZneRmk' => $this->input->post ( 'otaFTZneRmk' ),
				'FNZneRow' => $this->input->post ( 'oetFNZneRow' ),
				'FNZneCol' => $this->input->post ( 'oetFNZneCol' ),
				'FNZneRowStart' => $this->input->post ( 'oetFNZneRowStart' ),
				'FNZneColStart' => $this->input->post ( 'oetFNZneColStart' ) 
			);
			$this->Zone_model->FSxMZneEdit ( $aData );
			if ($this->input->post ( 'ohdZoneImg' )) {
				$tImg = $this->input->post ( 'ohdZoneImg' );				
				FSaHUpdateImgObj($aData['FNZneID'], 'TTKMImgObj', 3, 'main', $tImg, 'zone');
			}
		}
	}
	
	/**
	 * FS ลบโซน
	 */
	public function FSxCZNEDel() {
		if ($this->input->post ( 'tZoneID' )) {
			$ocbListItem = $this->input->post ( 'tZoneID' );
			$aCode = explode(',', $ocbListItem);
			foreach ($aCode as $key => $oValue) {
				$aData = array (
					'FNZneID' => $oValue
				);
				$o = $this->Zone_model->FSxMZneDel ( $aData );
				$aJson = array (
					'count' => $o,
					'msg' => language ( 'ticket/center/center', 'CheckDel' ) 
				);
				if ($o == 0) {
					FSaDelImg($aData['FNZneID'], 'TTKMImgObj', 3, 'main', 'zone');
				}
				echo json_encode ( $aJson );
			}
		}
	}	
	/**
	 * FS แสดงที่นั่ง
	 */
	public function FSxCZNESeat() {
		if ($this->input->post ( 'tFNZneID' )) {
			$aData = array (
				'FNLocID' => $this->input->post ( 'tFNLocID' ),
				'FNLevID' => $this->input->post ( 'tFNLevID' ),
				'FNZneID' => $this->input->post ( 'tFNZneID' ) 
			);
			$oRow = $this->Zone_model->FSxMZNESeat ( $aData );
			$oShow = $this->Zone_model->FSxMZNEShow ( $aData );
		}
		$this->load->view ( 'park/zone/wSeat', array (
			'oRow' => $oRow,
			'oShow' => $oShow 
		) );
	}
	
	/**
	 * FS แก้ไขที่นั่ง
	 */
	public function FSxCZNEEditSeat() {
		if ($this->input->post ( 'ohdFNSetID' )) {
			$aData = array (
				'FNSetID' => $this->input->post ( 'ohdFNSetID' ),
				'FTSetName' => $this->input->post ( 'oetFTSetName' ),
				'FTSetStaAlw' => $this->input->post ( 'ocmFTSetStaAlw' ) 
			);
			$this->Zone_model->FSxMZNEEditSeat ( $aData );
		}
	}
	
	/**
	 * FS แก้ไข FTSetRowChr
	 */
	public function FSxCZNEEditRowChr() {
		if ($this->input->post ( 'oetFTSetRowChr' )) {
			$aRowChr = $this->input->post ( 'oetFTSetRowChr' );
			$aRowChrHD = $this->input->post ( 'ohdFTSetRowChr' );
			foreach ( $aRowChr as $key => $tValue ) {
				$aData = array (
					'FTSetRowChr' => $tValue,
					'ohdFTSetRowChr' => $aRowChrHD [$key],
					'FNLocID' => $this->input->post ( 'ohdFNLocID' ),
					'FNLevID' => $this->input->post ( 'ohdFNLevID' ),
					'FNZneID' => $this->input->post ( 'ohdFNZneID' ) 
				);
				$this->Zone_model->FSxMZNEEditRowChr ( $aData );
			}
		}
		/*
		 * if ($this->input->post ( 'oetFTSetRowChr' )) {
		 * $aFNSetColSeq = $this->input->post ( 'ohdFNSetColSeq' );
		 * $aFTSetColChr = $this->input->post ( 'oetFTSetColChr' );
		 * foreach ( $aFTSetColChr as $key => $tValue ) {
		 * $aData = array (
		 * 'FTSetColChr' => $tValue,
		 * 'FNSetColSeq' => $aFNSetColSeq [$key],
		 * 'FNLocID' => $this->input->post ( 'ohdFNLocID' ),
		 * 'FNLevID' => $this->input->post ( 'ohdFNLevID' ),
		 * 'FNZneID' => $this->input->post ( 'ohdFNZneID' )
		 * );
		 * $this->Zone_model->FSxMLOCZNEEditColChr ( $aData );
		 * }
		 * }
		 */
		
		if ($this->input->post ( 'ohdFNSetID' )) {
			$aFNSetID = $this->input->post ( 'ohdFNSetID' );
			foreach ( $aFNSetID as $key => $tValue ) {
				$aData = array (
					'FNSetID' => $tValue 
				);
				$View = $this->Zone_model->FSxMZNEViewSet ( $aData );
				
				foreach ( $View as $key => $tView ) {
					$aSet = array (
						'FNSetID' => $tView->FNSetID,
						'FTSetName' => $tView->FTSetRowChr . $tView->FNSetColSeq 
					);
					$this->Zone_model->FSxMZNEUpdateNameSet ( $aSet );
				}
			}
		}
	}
	public function FStCZNECheck() {
		if ($this->input->post ( 'oetFTZneName' )) {
			$tData = array (
				'FTZneName' => $this->input->post ( 'oetFTZneName' ),
				'FNLevID' => $this->input->post ( 'nFNFNLevID' ),
				'FNLocID' => $this->input->post ( 'nFNLocID' ) 
			);
			$tCheck = $this->Zone_model->FSnMZNECheck ( $tData );
			if (@$tCheck [0]->counts > 0) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}
	public function FStCZNECheckSeat() {
		if ($this->input->post ( 'oetFTSetRowChr' )) {
			$tData = array (
				'FTSetRowChr' => $this->input->post ( 'oetFTSetRowChr' ),
				'FNLocID' => $this->input->post ( 'ohdFNLocID' ),
				'FNLevID' => $this->input->post ( 'ohdFNLevID' ),
				'FNZneID' => $this->input->post ( 'ohdFNZneID' ) 
			);
			$tCheck = $this->Zone_model->FStMZNECheckSeat ( $tData );
			if (@$tCheck [0]->counts > 0) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}
	public function FStCZNECheckEdtSeat() {
		if ($this->input->post ( 'oetFTSetName' )) {
			$tData = array (
				'FTSetName' => $this->input->post ( 'oetFTSetName' ),
				'FNLocID' => $this->input->post ( 'ohdFNLocIDSet' ),
				'FNLevID' => $this->input->post ( 'ohdFNLevIDSet' ),
				'FNZneID' => $this->input->post ( 'ohdFNZneIDSet' ) 
			);
			$tCheck = $this->Zone_model->FStMZNECheckEdtSeat ( $tData );
			if (@$tCheck [0]->counts > 0) {
				echo 'false';
			} else {
				echo 'true';
			}
		}
	}
}
