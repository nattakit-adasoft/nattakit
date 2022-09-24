<div class="panel-heading">
    <div class="row">
        <div class="col-xs-8 col-md-4 col-lg-4">
            <div class="form-group">
                <label class="xCNLabelFrm"><?php echo @$aTextLang['tTCGSearch'];?></label>
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control xCNInputWithoutSpc"
                        id="oetTCGSearchAll"
                        name="oetTCGSearchAll"
                        placeholder="<?php echo @$aTextLang['tPlaceholder'];?>"
                    >
                    <span class="input-group-btn">
                        <button id="oimTCGSearchAll" class="btn xCNBtnSearch" type="button"><img class="xCNIconSearch"></button>
                    </span>
                </div>
            </div>
        </div>
        <?php if($aAlwEvent['tAutStaDelete'] == 1 ): ?>
        <div class="col-xs-4 col-md-8 col-lg-8 text-right" style="margin-top:34px;">
            <div id="odvTCGMngTableList" class="btn-group xCNDropDrownGroup">
                <button type="button" class="btn xCNBTNMngTable" data-toggle="dropdown">
                    <?php echo @$aTextLang['tCMNOption'];?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li id="oliTCGBtnDeleteAll" class="disabled">
                        <a data-toggle="modal" data-target="#odvTCGModalDelDocMultiple"><?php echo @$aTextLang['tDelAll'];?></a>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="panel-body">
    <section id="ostTCGDataTable"></section>
</div>
<script src="<?php echo base_url(); ?>application/modules/common/assets/js/jquery.mask.js"></script>
<script src="<?php echo base_url(); ?>application/modules/common/assets/src/jFormValidate.js"></script>
<script>
	$('#oimTCGSearchAll').click(function(){
		JCNxOpenLoading();
		JSvTCGCallPageDataTable();
	});
	$('#oetTCGSearchAll').keypress(function(event){
		if(event.keyCode == 13){
			JCNxOpenLoading();
			JSvTCGCallPageDataTable();
		}
	});
</script>
