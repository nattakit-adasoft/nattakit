<div id="odvRsnMainMenu" class="main-menu">
	<div class="xCNMrgNavMenu">
		<div class="row xCNavRow" style="width:inherit;">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
				<ol id="oliMenuNav" class="breadcrumb">
					<?php FCNxHADDfavorite('movement/0/0');?> 
					<li id="oliMmtTitle" onclick="JSxMmtRenderContentTab()" style="cursor:pointer">
						<?php echo language('movement/movement/movement','tMMTTitle')?>
					</li>
				</ol>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 text-right p-r-0"></div> 
		</div>
	</div>
</div>
<div class="xCNMenuCump xCNRsnBrowseLine" id="odvMenuCump">&nbsp;</div>
<div class="main-content">
	<!-- เนื้อหาส่วนตาราง -->
	<!-- <div id="odvContentPageMovement" class="panel panel-headline"></div> -->
	<div id="odvMmtContentTabContainer" class="panel panel-headline"></div>
</div>

<!-- <script src="<?php //echo base_url('application/modules/movement/assets/src/jInv.js'); ?>"></script>  -->
<script src="<?php echo base_url('application/modules/movement/assets/src/jMovement.js'); ?>"></script> 