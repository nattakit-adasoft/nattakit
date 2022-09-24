<script type="text/javascript">
    $(document).ready(function(){

        if(JSbGroupSupplierSgpIsCreatePage()){
            //supplierlev Code
            $("#oetSgpCode").attr("disabled", true);
            $('#ocbGroupSupplierAutoGenCode').change(function(){
                if($('#ocbGroupSupplierAutoGenCode').is(':checked')) {
                    $('#oetSgpCode').val('');
                    $("#oetSgpCode").attr("disabled", true);
                    $('#odvGroupSupplierCodeForm').removeClass('has-error');
                    $('#odvGroupSupplierCodeForm em').remove();
                }else{
                    $("#oetSgpCode").attr("disabled", false);
                }
            });
            JSxGroupSupplierSgpVisibleComponent('#odvGroupSupplierAutoGenCode', true);
        }
        
        if(JSbGroupSupplierSgplIsUpdatePage()){
      
            // supplierlev Code
            $("#oetSgpCode").attr("readonly", true);
            $('#odvGroupSupplierAutoGenCode input').attr('disabled', true);
            JSxGroupSupplierSgpVisibleComponent('#odvGroupSupplierAutoGenCode', false);    
        }
    });

    $('#oetSgpCode').blur(function(){
        JSxCheckGroupSupplierSgpCodeDupInDB();
    });

    //Functionality : Event Check GroupSupplier
    //Parameters : Event Blur Input Voucher Code
    //Creator : 29/05/2019 saharat (Golf)
    //Return : -
    //Return Type : -
    function JSxCheckGroupSupplierSgpCodeDupInDB(){
        if(!$('#ocbGroupSupplierAutoGenCode').is(':checked')){
            $.ajax({
                type: "POST",
                url: "CheckInputGenCode",
                data: { 
                    tTableName: "TCNMSplGrp",
                    tFieldName: "FTSgpCode",
                    tCode: $("#oetSgpCode").val()
                },
                cache: false,
                timeout: 0,
                success: function(tResult){
                    var aResult = JSON.parse(tResult);
                    $("#ohdCheckDuplicateSgpCode").val(aResult["rtCode"]);
                    // Set Validate  Code
                    $.validator.addMethod('dublicateCode', function(value, element) {
                        if($("#ohdCheckDuplicateSgpCode").val() == 1){
                            return false;
                        }else{
                            return true;
                        }
                    },'');
                    // From Summit Validate
                    $('#ofmAddGroupSupplier').validate({
                        rules: {
                            oetSgpCode : {
                                "required" :{
                                    // ตรวจสอบเงื่อนไข validate
                                    depends: function(oElement) {
                                    if($('#ocbGroupSupplierAutoGenCode').is(':checked')){
                                            return false;
                                    }else{
                                            return true;
                                        }
                                    }
                                },
                                "dublicateCode" :{}
                            },
                            oetSgpName:     {"required" :{ }},
                        },
                        messages: {
                            oetSgpCode : {
                                "required"      : $('#oetSgpCode').attr('data-validate-required'),
                                "dublicateCode" : $('#oetSgpCode').attr('data-validate-dublicateCode')
                            },
                            oetSgpName : {
                                "required"      : $('#oetSgpName').attr('data-validate-required'),
                            },
                        },
                        errorElement: "em",
                        errorPlacement: function (error, element ) {
                            error.addClass( "help-block" );
                            if ( element.prop( "type" ) === "checkbox" ) {
                                error.appendTo( element.parent( "label" ) );
                            } else {
                                var tCheck = $(element.closest('.form-group')).find('.help-block').length;
                                if(tCheck == 0){
                                    error.appendTo(element.closest('.form-group')).trigger('change');
                                }
                            }
                        },
                        highlight: function ( element, errorClass, validClass ) {
                            $( element ).closest('.form-group').addClass( "has-error" ).removeClass( "has-success" );
                        },
                        unhighlight: function (element, errorClass, validClass) {
                            $( element ).closest('.form-group').addClass( "has-success" ).removeClass( "has-error" );
                        },
                        submitHandler: function(form){}
                    });

                    // Submit From
                    $('#ofmAddGroupSupplier').submit();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    JCNxResponseError(jqXHR, textStatus, errorThrown);
                }
            });
        }    
    }
</script>