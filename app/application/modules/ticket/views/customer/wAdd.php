<script type="text/javascript" src="<?php echo base_url() ?>application/modules/ticket/assets/src/zone/jZoneNew.js"></script>
<style>
    .invalid {
        display: inline-block;
        margin-bottom: 0;
        margin-top: 5px;
        font-weight: 400;
        color: #d9534f;
    }
</style>
<script>
    $(document).ready(function () {
        $(".xWAddCustomer").validate({
            rules: {
                oetFTCstName: "required",
                "opwFTCstPwd": {
                    required: true,
                    minlength: 3
                },
                oetFTCstEmail: {
                    required: true,
                    email: true,
                    remote: {
                        url: "<?php echo base_url(); ?>EticketCustomer/checkemail",
                        type: "post",
                        data: {
                            email: function () {
                                return $("#oetFTCstEmail").val();

                            }
                        },
                        complete: function (data) {
                            if (data.responseText == 'false') {
                                $('#oChkCstEmail').attr('data-validate', 'ไม่สามารถใช้อีเมลนี้ได้');
                            }
                        }
                    },
                    complete: function (data) {
                        $("#oetFTCstEmail-error").removeClass('input-invalid').addClass('invalid');
                    }
                }
            },
            messages: {
                oetFTCstName: "<?= language('ticket/user/user', 'tPleaseEnterYourName') ?>",
                oetFTCstEmail: {
                    required: "",
                    email: "",
                    remote: ""
                },
                opwFTCstPwd: "<?= language('ticket/about/about', 'tYourPasswordMustBeAtLeast3Characters') ?>",
            },
            errorClass: "alert-validate",
            validClass: "",
            highlight: function (element, errorClass, validClass) {
                $(element).parent('.validate-input').addClass(errorClass).removeClass(validClass);
                $(element).parent().parent('.validate-input').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent('.validate-input').removeClass(errorClass).addClass(validClass);
                $(element).parent().parent('.validate-input').removeClass(errorClass).addClass(validClass);
            },
            submitHandler: function (form) {
                $('button[type=submit]').attr('disabled', true);
                $('.xCNOverlay').show();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>EticketCustomer/addAjax",
                    data: $(".xWAddCustomer").serialize(),
                    cache: false,
                    success: function (msg) {
                        var nDataId = $('.xWBtnSaveActive').data('id');
                        if (nDataId == '1') {
                            JSxCallPage('<?= base_url() ?>EticketCustomer/edit/' + msg);
                        } else if (nDataId == '2') {
                            JSxCallPage('<?= base_url() ?>EticketCustomer/add');
                        } else if (nDataId == '3') {
                            JSxCallPage('<?php echo base_url('EticketCustomer') ?>');
                        }
                        $('.xCNOverlay').hide();
                    },
                    error: function (data) {
                        console.log(data);
                        $('.xCNOverlay').hide();
                    }
                });
                return false;
            }
        });
        $('.selectpicker').selectpicker();
        $('[title]').tooltip();
        $('#oetFDCstDob').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: '<?php echo ($this->session->userdata("lang") == "cn" ? "zh-cn" : $this->session->userdata("lang")); ?>'
        });
        $('#oetFDCstCrdExpire').datetimepicker({
            format: 'YYYY-MM-DD',
            locale: '<?php echo ($this->session->userdata("lang") == "cn" ? "zh-cn" : $this->session->userdata("lang")); ?>'
        });
    });
</script>
<div class="main-menu">
    <form action="" method="post" autocorrect="off" autocapitalize="off" autocomplete="off" class="xWAddCustomer">	
        <div class="xCNMrgNavMenu">
            <div class="row xCNavRow" style="width:inherit;">
                <div class="xCNBchVMaster">
                    <div class="col-xs-8 col-md-8">
                        <ol id="oliMenuNav" class="breadcrumb">
                            <li id="oliBCHTitle" class="xCNLinkClick" onclick="JSxCallPage('<?php echo base_url('EticketCustomer') ?>')"><?= language('ticket/customer/customer', 'tCustomerInformation') ?></li> 
                            <li  class="xCNLinkClick"><?= language('ticket/customer/customer', 'tAddCustomer') ?></li>
                            </ol>       
                            </div>
                            <div class="col-xs-12 col-md-4 text-right p-r-0">
                            <div class="demo-button xCNBtngroup">
                            <button type="button" onclick="JSxCallPage('<?php echo base_url('EticketCustomer') ?>')" class="btn btn-default xCNBTNDefult"><?= language('common/main/main', 'tBack') ?></button>
                            <div class="btn-group">
                            <button class="btn btn-default xWBtnGrpSaveLeft" type="submit"><?= language('ticket/user/user', 'tSave') ?></button>
                            <button type="button" class="btn btn-default xWBtnGrpSaveRight dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu xWDrpDwnMenuMargLft">
                            <li class="xWolibtnsave1 xWBtnSaveActive" data-id="1" onclick="JSvChangeBtnSaveAction(1)"><a href="#"><?= language('common/main/main', 'tCMNSaveAndView') ?></a></li>
                            <li class="xWolibtnsave2" data-id="2" onclick="JSvChangeBtnSaveAction(2)"><a href="#"><?= language('common/main/main', 'tCMNSaveAndNew') ?></a></li>
                            <li class="xWolibtnsave3" data-id="3" onclick="JSvChangeBtnSaveAction(3)"><a href="#"><?= language('common/main/main', 'tCMNSaveAndBack') ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
<div class="main-content">
    <div class="panel panel-headline">
        <div class="panel-heading"> 
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="upload-img" id="oImgUpload">
                                <img src="<?php echo base_url('application/modules/common/assets/images/Noimage.png'); ?>" style="width: 100%;" id="oimImgMasterMain">				 
                                    <span class="btn-file">
                                        <input type="hidden" name="ohdCstImg" id="oetImgInputMain">
                                    </span>
                                </div>
                            <div class="xCNUplodeImage">
                        <button type="button" class="btn xCNBTNDefult" onclick="JSvImageCallTempNEW('', '', 'Main', '4/4')"><i class="fa fa-camera"></i> เลือกรูป</button>
                    </div>
                        </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input100 validate-input" data-validate="<?= language('ticket/customer/customer', 'tPleaseEnterName') ?>">
                                        <label class="xCNLabelFrm"><span style="color:red">*</span><?= language('ticket/about/about', 'tName') ?></label>
                                        <input class="form-control" type="text" name="oetFTCstName" id="oetFTCstName" data-validate="<?= language('ticket/customer/customer', 'tPleaseEnterName') ?>">
                                        <span class="focus-input100"></span>
                                        </div>
                                    </div>
                                <div class="form-group">
                            <!-- <div class="input100 validate-input" data-validate="<?= language('ticket/customer/customer', 'tPleaseEnterEmail') ?>"> -->
                        <label class="xCNLabelFrm"><span style="color:red">*</span><?= language('ticket/about/about', 'tEmail') ?></label>
                    <input class="form-control" type="text" name="oetFTCstEmail" id="oetFTCstEmail" data-validate="<?= language('ticket/customer/customer', 'tPleaseEnterEmail') ?>">
                        <!-- <span class="focus-input100"></span>
                            </div> -->
                                </div>
                                    <div class="form-group">
                                        <div class="input100 validate-input" data-validate="<?= language('ticket/customer/customer', 'tPleaseEnterPassword') ?>">
                                        <label class="xCNLabelFrm"><span style="color:red">*</span><?= language('ticket/about/about', 'tPassword') ?></label>
                                        <input class="form-control" type="password" name="opwFTCstPwd" id="opwFTCstPwd" data-validate="<?= language('ticket/about/about', 'tPassword') ?>">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        <div class="form-group">  
                    <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tIDCardOrPassportCode') ?></label>
                        <input class="form-control" type="text" name="oetFTCstCardID" id="oetFTCstCardID">
                            <span class="focus-input100"></span>
                                </div> 
                                    <div class="form-group">  
                                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tPhoneNumber') ?></label>
                                        <input class="form-control" type="number" min="0" maxlength="13" name="oetFTCstMo" id="oetFTCstMo">
                                        <span class="focus-input100"></span>
                                    </div> 
                                <div class="form-group">  
                            <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tTelephoneNumber') ?></label>
                        <input class="form-control" type="number" min="0" maxlength="13" name="oetFTCstTel" id="oetFTCstTel">
                    <span class="focus-input100"></span>
                        </div> 
                            <div class="form-group">  
                                <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tFaxNumber') ?></label>
                                    <input class="form-control" type="number" min="0" maxlength="13" name="oetFTCstFax" id="oetFTCstFax">
                                        <span class="focus-input100"></span>
                                        </div> 
                                        <div class="form-group">  
                                    <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tTaxIdentificationNumber') ?></label>
                                <input class="form-control" type="text"  name="oetFTCstTaxNo" id="oetFTCstTaxNo">
                            <span class="focus-input100"></span>
                        </div> 
                    <div class="form-group">  
                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tBirthday') ?></label>
                            <input class="form-control" type="text" name="oetFDCstDob" id="oetFDCstDob">
                                <span class="focus-input100"></span>
                                    </div> 
                                        <div class="form-group">  
                                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tCareer') ?></label>
                                        <input class="form-control" type="text" name="oetFTCstCareer" id="oetFTCstCareer">
                                    <span class="focus-input100"></span>
                                </div> 
                            <div class="form-group">  
                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tCardNumber') ?></label>
                    <input class="form-control" type="text" name="oetFTCstCrdNo" id="oetFTCstCrdNo">
                        <span class="focus-input100"></span>
                            </div> 
                                <div class="form-group">  
                                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tCardExpirationDate') ?></label>
                                        <input class="form-control" type="text" name="oetFDCstCrdExpire" id="oetFDCstCrdExpire">
                                </div> 
                            <div class="form-group">  
                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tSelectGender') ?></label>
                    <select class="selectpicker form-control" name="ocmFTCstSex" id="ocmFTCstSex">
                        <option value="" selected="selected"><?= language('ticket/customer/customer', 'tSelectGender') ?></option>
                            <option value="1"><?= language('ticket/customer/customer', 'tMale') ?></option>
                                <option value="2"><?= language('ticket/customer/customer', 'tFemale') ?></option>
                                    </select>
                                        <span class="focus-input100"></span>
                                        </div>
                                        <div class="form-group">  
                                    <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tSelectCustomerStatus') ?></label>
                                <select class="selectpicker form-control" name="ocmFTCstStaLocal" id="ocmFTCstStaLocal">
                            <option value="" selected="selected"><?= language('ticket/customer/customer', 'tSelectCustomerStatus') ?></option>
                        <option value="1"><?= language('ticket/customer/customer', 'tThaiPeople') ?></option>
                    <option value="2"><?= language('ticket/customer/customer', 'tForeigner') ?></option>
                        </select>
                            <span class="focus-input100"></span>
                                </div>
                                    <div class="form-group">  
                                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tSelectBusinessCategory') ?></label>
                                    <select class="selectpicker form-control" name="ocmFTCstBusiness" id="ocmFTCstBusiness">
                                <option value=""><?= language('ticket/customer/customer', 'tSelectBusinessCategory') ?></option>
                            <option value="1"><?= language('ticket/customer/customer', 'tJuristicPerson') ?></option>
                        <option value="2"><?= language('ticket/customer/customer', 'tNaturalPerson') ?></option>
                    </select>
                        <span class="focus-input100"></span>
                            </div>
                                <div class="form-group">  
                                    <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tUsageStatus') ?><?= language('ticket/customer/customer', 'tUsageStatus') ?></label>
                                        <select class="selectpicker form-control" name="ocmFTCstStaAge" id="ocmFTCstStaAge">
                                        <option value="1" selected="selected"><?= language('ticket/customer/customer', 'tOpening') ?></option>
                                        <option value="2"><?= language('ticket/customer/customer', 'tExpire') ?></option>
                                        </select>
                                    <span class="focus-input100"></span>
                                </div>
                            <div class="form-group">  
                        <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tSelectContactStatus') ?></label>
                    <select class="selectpicker form-control" name="ocmFTCstStaActive" id="ocmFTCstStaActive">
                        <option value="" selected="selected"><?= language('ticket/customer/customer', 'tSelectContactStatus') ?></option>
                            <option value="1"><?= language('ticket/customer/customer', 'tStillContact') ?></option>
                                <option value="2"><?= language('ticket/customer/customer', 'tStopContact') ?></option>
                                    </select>
                                        <span class="focus-input100"></span>
                                        </div>
                                    <div class="form-group">
                                <div class="input100 input100-select validate-input" data-validate="กรุณาเลือกประเภท">  
                            <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tSelectCustomerCategory') ?></label>
                        <select class="selectpicker form-control" name="ocmFNCtyID" id="ocmFNCtyID">
                    <option value=""><?= language('ticket/customer/customer', 'tSelectCustomerCategory') ?></option>
                        <?php foreach ($oTye AS $oValue): ?>
                            <option value="<?= $oValue->FNCtyID ?>"><?= $oValue->FTCtyName ?></option>
                                <?php endforeach; ?>	
                                    </select>
                                        <span class="focus-input100"></span>
                                        </div>
                                        </div>
                                    <div class="form-group">
                                <div class="input100 input100-select validate-input" data-validate="<?= language('ticket/customer/customer', 'tPleaseSelectGroup') ?>">
                            <label class="xCNLabelFrm"><?= language('ticket/customer/customer', 'tSelectCustomerGroup') ?></label>
                        <select class="selectpicker form-control" name="ocmFNCgpID" id="ocmFNCgpID">
                    <option value=""><?= language('ticket/customer/customer', 'tSelectCustomerGroup') ?></option>
                        <?php foreach ($oGrp AS $oValue): ?>
                            <option value="<?= $oValue->FNCgpID ?>"><?= $oValue->FTCgpName ?></option>
                                <?php endforeach; ?>	
                                    </select>
                                        <span class="focus-input100"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div> 
            </div> 
        </div>  
    </form>    
</div>
