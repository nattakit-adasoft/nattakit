<script type="text/javascript">
    $(document).ready(function(){
        $('.ocbListItem').unbind().click(function(){
            var nCode = $(this).parent().parent().parent().data('code');  //code
            var tName = $(this).parent().parent().parent().data('name');  //code
            $(this).prop('checked', true);
            var LocalItemData = localStorage.getItem("LocalItemData");
            var obj = [];
            if(LocalItemData){
                obj = JSON.parse(LocalItemData);
            }else{ }
            var aArrayConvert = [JSON.parse(localStorage.getItem("LocalItemData"))];
            if(aArrayConvert == '' || aArrayConvert == null){
                obj.push({"nCode": nCode, "tName": tName });
                localStorage.setItem("LocalItemData",JSON.stringify(obj));
                JSxTXOTextinModal();
            }else{
                var aReturnRepeat = JStTXOFindObjectByKey(aArrayConvert[0],'nCode',nCode);
                if(aReturnRepeat == 'None' ){           //ยังไม่ถูกเลือก
                    obj.push({"nCode": nCode, "tName": tName });
                    localStorage.setItem("LocalItemData",JSON.stringify(obj));
                    JSxTXOTextinModal();
                }else if(aReturnRepeat == 'Dupilcate'){	//เคยเลือกไว้แล้ว
                    localStorage.removeItem("LocalItemData");
                    $(this).prop('checked', false);
                    var nLength = aArrayConvert[0].length;
                    for($i=0; $i<nLength; $i++){
                        if(aArrayConvert[0][$i].nCode == nCode){
                            delete aArrayConvert[0][$i];
                        }
                    }
                    var aNewarraydata = [];
                    for($i=0; $i<nLength; $i++){
                        if(aArrayConvert[0][$i] != undefined){
                            aNewarraydata.push(aArrayConvert[0][$i]);
                        }
                    }
                    localStorage.setItem("LocalItemData",JSON.stringify(aNewarraydata));
                    JSxTXOTextinModal();
                }
            }
            JSxTXOShowButtonChoose();
        });

        $('#odvTxoModalDelDocMultiple #osmConfirmDelMultiple').unbind().click(function(){
            JSnTXODelDocMultiple();
        });
    });
</script>