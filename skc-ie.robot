*** Settings ***
Documentation     SKC AUTOMATION TEST
Library           Selenium2Library    implicit_wait=50

*** Variables ***
${URL}            https://dev.ada-soft.com/AdaSiamKubota/login
${BROWSER}        ie
${SELSPEED}       0.5s
${USERNAME}       009
${PASSWORD}       123456
${NPASS}          123456789

*** Test Cases ***
Login_SC
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    009
    type    id=oetPassword    123456
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Page Contains    AdaSoft    50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    [Teardown]    Close Browser

Login_FC
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    009
    type    id=oetPassword    123456789
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Element Is Visible    xpath=//*[@id="ospUsrOrPwNotCorrect"]    20
    Wait Until Page Contains    ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง    50
    ${response}    Get Text    xpath=//*[@id="ospUsrOrPwNotCorrect"]
    Should Be Equal As Strings    ${response}    ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
    [Teardown]    Close Browser

AD_Cre_Receipt_Suc
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    009
    type    id=oetPassword    123456
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Element Is Visible    xpath=//*[@id="spnCompanyName"]    20
    Wait Until Page Contains    AdaSoft    50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Wait Until Page Contains    ข้อมูลจุดขาย    50
    ${response}    Get Text    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Should Be Equal As Strings    ${response}    ข้อมูลจุดขาย
    #----คลิกกำหนดหัวท้ายใบเสร็จ
    click    xpath=//*[@id="MASSPS"]/ul/li[2]/a
    Wait Until Page Contains    ค้นหา    50
    #----เพิ่มข้อมูล
    click    xpath=//*[@id="obtSmgAdd"]
    Wait Until Page Contains    *ชื่อหัวท้ายใบเสร็จ    50
    ${response}    Get Text    xpath=//*[@id="ofmAddSlipMessage"]/div/div[1]/div/div[5]/div/label
    Should Be Equal As Strings    ${response}    *ชื่อหัวท้ายใบเสร็จ
    #----เพิ่มหัวท้ายใบเสร็จ
    click    id=oetSmgFontsSize
    input text    id=oetSmgFontsSize    16
    click    id=oetSmgTitle
    input text    id=oetSmgTitle    SKC RC_SUC01
    Execute JavaScript    window.scrollTo(0,200)
    click    id=oetSmgSlipHead1
    input text    id=oetSmgSlipHead1    Receipt/Tax
    click    id=xWSmgAddHeadRow
    click    id=oetSmgSlipHead2
    input text    id=oetSmgSlipHead2    SiamKubota
    click    id=oetSmgSlipEnd1
    input text    id=oetSmgSlipEnd1    WELCOME
    click    id=xWSmgAddEndRow
    click    id=oetSmgSlipEnd2
    input text    id=oetSmgSlipEnd2    THANK YOU
    Execute JavaScript    window.scrollTo(0,0)
    #----กดบันทึก
    click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    หัวท้ายใบเสร็จ    50
    click    id=oliSmgTitle
    Wait Until Element Is Visible    xpath=//*[@id="oliSmgTitle"]    50
    [Teardown]    Close Browser

AD_Cre_Sale_Ch_Suc
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    009
    type    id=oetPassword    123456
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Element Is Visible    xpath=//*[@id="spnCompanyName"]    20
    Wait Until Page Contains    AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Wait Until Page Contains    ข้อมูลจุดขาย    50
    ${response}    Get Text    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Should Be Equal As Strings    ${response}    ข้อมูลจุดขาย
    #----คลิกช่องทางการขาย
    click    xpath=//*[@id="MASSPS"]/ul/li[5]/a
    Wait Until Page Contains    ค้นหา    50
    #----เพิ่มข้อมูล
    click    xpath=//*[@id="obtChnAdd"]
    Wait Until Page Contains    *ชื่อช่องทางการขาย    50
    ${response}    Get Text    xpath=//*[@id="ofmAddChanel"]/div/div/div/div[3]/div/label
    Should Be Equal As Strings    ${response}    *ชื่อช่องทางการขาย
    #----เพิ่มช่องทางการขาย
    click    id=oetChnName
    input text    id=oetChnName    SKC CS_SUC01
    Execute JavaScript    window.scrollTo(0,200)
    click    id=oimChnBrowseApp
    #----แสดงข้อมูลระบบ
    Wait Until Page Contains    แสดงข้อมูล : ระบบ    50
    ${response}    Get Text    xpath=//*[@id="odvModalContent"]/div[1]/div/div[1]/label
    Should Be Equal As Strings    ${response}    แสดงข้อมูล : ระบบ
    click    //*[@id="otbBrowserList"]/tbody/tr[6]/td[2]
    click    //*[@id="odvModalContent"]/div[1]/div/div[2]/button[1]
    Execute JavaScript    window.scrollTo(0,0)
    #----กดบันทึก
    click    //*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    ช่องทางการขาย    50
    click    id=oliChnTitle
    Wait Until Element Is Visible    xpath=//*[@id="oliChnTitle"]    50
    [Teardown]    Close Browser

AD_Cre_Receipt_Fail
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    009
    type    id=oetPassword    123456
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Element Is Visible    xpath=//*[@id="spnCompanyName"]    20
    Wait Until Page Contains    AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Wait Until Page Contains    ข้อมูลจุดขาย    50
    ${response}    Get Text    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Should Be Equal As Strings    ${response}    ข้อมูลจุดขาย
    #----คลิกกำหนดหัวท้ายใบเสร็จ
    click    xpath=//*[@id="MASSPS"]/ul/li[2]/a
    Wait Until Page Contains    ค้นหา    50
    #----เพิ่มข้อมูล
    click    xpath=//*[@id="obtSmgAdd"]
    Wait Until Page Contains    *ชื่อหัวท้ายใบเสร็จ    50
    ${response}    Get Text    xpath=//*[@id="ofmAddSlipMessage"]/div/div[1]/div/div[5]/div/label
    Should Be Equal As Strings    ${response}    *ชื่อหัวท้ายใบเสร็จ
    #----เพิ่มหัวท้ายใบเสร็จ
    #----กดบันทึก
    click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    กรุณากรอกขนาดตัวอักษร    50
    click    id=oetSmgFontsSize
    input text    id=oetSmgFontsSize    16
    Wait Until Page Contains    กรุณากรอกชื่อหัวท้ายใบเสร็จ    50
    click    id=oetSmgTitle
    input text    id=oetSmgTitle    SKC RC_FAIL01
    Execute JavaScript    window.scrollTo(0,200)
    Wait Until Page Contains    กรุณากรอกชื่อหัวท้ายใบเสร็จ    50
    click    id=oetSmgSlipHead1
    input text    id=oetSmgSlipHead1    Receipt/Tax02
    Wait Until Page Contains    กรุณากรอกชื่อหัวท้ายใบเสร็จ    50
    click    id=oetSmgSlipEnd1
    input text    id=oetSmgSlipEnd1    WELCOME02
    click    id=xWSmgAddHeadRow
    #----กดบันทึก
    Execute JavaScript    window.scrollTo(0,0)
    click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    กรุณากรอกชื่อหัวท้ายใบเสร็จ    50
    Execute JavaScript    window.scrollTo(0,200)
    click    id=oetSmgSlipHead2
    input text    id=oetSmgSlipHead2    SiamKubota02
    click    id=xWSmgAddEndRow
    #----กดบันทึก
    Execute JavaScript    window.scrollTo(0,0)
    click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    กรุณากรอกชื่อหัวท้ายใบเสร็จ    50
    Execute JavaScript    window.scrollTo(0,300)
    click    id=oetSmgSlipEnd2
    input text    id=oetSmgSlipEnd2    THANK YOU02
    Execute JavaScript    window.scrollTo(0,0)
    #----กดบันทึก
    click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    หัวท้ายใบเสร็จ    50
    click    id=oliSmgTitle
    Wait Until Element Is Visible    xpath=//*[@id="oliSmgTitle"]    50
    [Teardown]    Close Browser

AD_Cre_Sale_Ch_Fail
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    009
    type    id=oetPassword    123456
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Element Is Visible    xpath=//*[@id="spnCompanyName"]    20
    Wait Until Page Contains    AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Wait Until Page Contains    ข้อมูลจุดขาย    50
    ${response}    Get Text    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a
    Should Be Equal As Strings    ${response}    ข้อมูลจุดขาย
    #----คลิกช่องทางการขาย
    click    xpath=//*[@id="MASSPS"]/ul/li[5]/a
    Wait Until Page Contains    ค้นหา    50
    #----เพิ่มข้อมูล
    #----กดบันทึก
    click    //*[@id="obtChnAdd"]
    click    //*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    กรุณากรอกชื่อหัวท้ายใบเสร็จ    50
    Wait Until Page Contains    *ชื่อช่องทางการขาย    50
    ${response}    Get Text    xpath=//*[@id="ofmAddChanel"]/div/div/div/div[3]/div/label
    Should Be Equal As Strings    ${response}    *ชื่อช่องทางการขาย
    #----เพิ่มช่องทางการขาย
    click    id=oetChnName
    input text    id=oetChnName    SKC CS_FAIL01
    Execute JavaScript    window.scrollTo(0,200)
    Wait Until Page Contains    กรุณากรอกระบบ    50
    click    id=oimChnBrowseApp
    #----แสดงข้อมูลระบบ
    Wait Until Page Contains    แสดงข้อมูล : ระบบ    50
    ${response}    Get Text    xpath=//*[@id="odvModalContent"]/div[1]/div/div[1]/label
    Should Be Equal As Strings    ${response}    แสดงข้อมูล : ระบบ
    click    //*[@id="otbBrowserList"]/tbody/tr[6]/td[2]
    click    //*[@id="odvModalContent"]/div[1]/div/div[2]/button[1]
    Execute JavaScript    window.scrollTo(0,0)
    #----กดบันทึก
    click    //*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Page Contains    ช่องทางการขาย    50
    click    id=oliChnTitle
    Wait Until Element Is Visible    xpath=//*[@id="oliChnTitle"]    50
    [Teardown]    Close Browser

AD_Create_SC
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    ${USERNAME}
    type    id=oetPassword    ${PASSWORD}
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Element Is Visible    xpath=//*[@id="spnCompanyName"]    20
    Wait Until Page Contains    AdaSoft    50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Wait Until Page Contains    สื่อ,ข้อความ หน้าจอลูกค้า    50
    ${response}    Get Text    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Should Be Equal As Strings    ${response}    สื่อ,ข้อความ หน้าจอลูกค้า
    #----คลิกสื่อ,ข้อความ หน้าจอลูกค้า
    click    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Wait Until Page Contains    ค้นหา    50
    Sleep    5s
    #${response}    Get Text    xpath=//*[@id="odvContentPageAdMessage"]/div[1]/div/div[1]/div/label
    #Should Be Equal As Strings    ${response}    ค้นหา
    #----เพิ่มข้อมูล
    click    xpath=//*[@id="obtAdvAdd"]
    sleep    5s
    Wait Until Element Is Visible    xpath=//*[@id="obtBarSubmitAdv"]/div/button[1]    20
    Wait Until Page Contains    รหัสโฆษณา    50
    Wait Until Page Contains    บันทึก    50
    ${response}    Get Text    xpath=//*[@id="ofmAddAdMessage"]/div/div/div[1]/label
    Should Be Equal As Strings    ${response}    * รหัสโฆษณา
    #----หน้ากรอกข้อมูล
    click    id=oetAdvName
    input text    id=oetAdvName    AD TEST
    click    id=oetAdvMsg
    Input Text    id=oetAdvMsg    สื่อโฆษณาทั่วไป
    Sleep    5s
    Wait Until Element Is Visible    xpath=//button[@type='submit']
    #----กดบันทึก
    click    xpath=//button[@type='submit']
    Wait Until Element Is Visible    xpath=//*[@id="oetAdvName"]    50
    Wait Until Page Contains    ชื่อโฆษณา    50
    #${response}    Get Text    xpath=//*[@id="oetAdvName"]
    #${ExceptionValue}    Get Element Attribute    xpath=//*[@id="oetAdvName"]    AD test
    #Should Be Equal As Strings    ${ExceptionValue}    AD test
    #---คลิก Title เพื่อกลับมาตรวจสอบข้อมูล
    Sleep    5s
    click    xpath=//*[@id="odvBtnAddEdit"]/button
    Wait Until Element Is Visible    xpath=//*[@id="odvContentAdMessageData"]/div[1]/div/div/table/thead/tr/th[2]    50
    Wait Until Page Contains    AD TEST    50
    #${Value} =    Set Variable    AD test
    #Run Keyword IF    ‘${Value}’ == Wait Until Page Contains
    [Teardown]    Close Browser

AD_Create_FC_NameAD
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    ${USERNAME}
    type    id=oetPassword    ${PASSWORD}
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Page Contains   AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Wait Until Page Contains    สื่อ,ข้อความ หน้าจอลูกค้า    50
    ${response}    Get Text    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Should Be Equal As Strings    ${response}    สื่อ,ข้อความ หน้าจอลูกค้า
    #----คลิกสื่อ,ข้อความ หน้าจอลูกค้า
    click    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Wait Until Page Contains    ค้นหา    50
    sleep    5s
    #${response}    Get Text    xpath=//*[@id="odvContentPageAdMessage"]/div[1]/div/div[1]/div/label
    #Should Be Equal As Strings    ${response}    ค้นหา
    #----เพิ่มข้อมูล
    click    xpath=//*[@id="obtAdvAdd"]
    #Wait Until Page Contains    บันทึก    50
    Wait Until Page Contains    รหัสโฆษณา    50
    ${response}    Get Text    xpath=//*[@id="ofmAddAdMessage"]/div/div/div[1]/label
    Should Be Equal As Strings    ${response}    * รหัสโฆษณา
    #----หน้ากรอกข้อมูล
    click    id=oetAdvName
    #input text    id=oetAdvName    AD TEST
    #click    id=oetAdvMsg
    Input Text    id=oetAdvMsg    สื่อโฆษณาทั่วไป
    #----กดบันทึก
    click    xpath=//button[@type='submit']
    Wait Until Page Contains    ชื่อโฆษณา    50
    ${response}    Get Text    xpath=//*[@id="oetAdvName-error"]
    Should Be Equal As Strings    ${response}    กรุณากรอกชื่อ
    [Teardown]    Close Browser

AD_Create_FC_TextAD
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    ${USERNAME}
    type    id=oetPassword    ${PASSWORD}
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Page Contains    AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Wait Until Page Contains    สื่อ,ข้อความ หน้าจอลูกค้า    50
    ${response}    Get Text    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Should Be Equal As Strings    ${response}    สื่อ,ข้อความ หน้าจอลูกค้า
    #----คลิกสื่อ,ข้อความ หน้าจอลูกค้า
    click    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Wait Until Page Contains    ค้นหา    50
    sleep    5s
    #${response}    Get Text    xpath=//*[@id="odvContentPageAdMessage"]/div[1]/div/div[1]/div/label
    #Should Be Equal As Strings    ${response}    ค้นหา
    #----เพิ่มข้อมูล
    click    xpath=//*[@id="obtAdvAdd"]
    #Wait Until Page Contains    บันทึก    50
    Wait Until Page Contains    รหัสโฆษณา    50
    ${response}    Get Text    xpath=//*[@id="ofmAddAdMessage"]/div/div/div[1]/label
    Should Be Equal As Strings    ${response}    * รหัสโฆษณา
    sleep    5s
    #----หน้ากรอกข้อมูล
    click    id=oetAdvName
    input text    id=oetAdvName    AD TEST
    #click    id=oetAdvMsg
    #Input Text    id=oetAdvMsg    สื่อโฆษณาทั่วไป
    #----กดบันทึก
    click    xpath=//button[@type='submit']
    Wait Until Page Contains    ข้อความ    50
    ${response}    Get Text    xpath=//*[@id="oetAdvMsg-error"]
    Should Be Equal As Strings    ${response}    This field is required.
    [Teardown]    Close Browser

AD_Del
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    ${USERNAME}
    type    id=oetPassword    ${PASSWORD}
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Page Contains    AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Wait Until Page Contains    สื่อ,ข้อความ หน้าจอลูกค้า    50
    ${response}    Get Text    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Should Be Equal As Strings    ${response}    สื่อ,ข้อความ หน้าจอลูกค้า
    #----คลิกสื่อ,ข้อความ หน้าจอลูกค้า
    click    xpath=//*[@id="MASSPS"]/ul/li[3]/a
    Wait Until Page Contains    ค้นหา    50
    #---ลบข้อมูลล่าสุดที่สร้าง
    click    xpath=//*[@id="otrAdMessage0"]/td[8]/img
    Wait Until Element Is Visible    xpath=//*[@id="odvModalDelAdMessage"]/div/div/div[1]/label    20
    ${cf}    set variable    ยืนยันการลบข้อมูล : 010 ( AD test )
    ${response}    Get Text    xpath=//*[@id="ospConfirmDelete"]
    #Should Be Equal As Strings    ${response}    ยืนยันการลบข้อมูล : 010 ( AD test )
    IF    "${response}" =="${cf}"
    click    xpath=//*[@id="osmConfirm"]
    sleep    5s
    ELSE
    click    xpath=//*[@id="odvModalDelAdMessage"]/div/div/div[3]/button[2]
    sleep    5s
    END
    sleep    5s
    [Teardown]    Close Browser

Printer_SC
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    ${USERNAME}
    type    id=oetPassword    ${PASSWORD}
    #----
    click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Page Contains    AdaSoft   50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Wait Until Page Contains    เครื่องพิมพ์    50
    ${response}    Get Text    xpath=//*[@id="MASSPS"]/ul/li[6]/a
    Should Be Equal As Strings    ${response}    เครื่องพิมพ์
    #----คลิกเครื่องพิมพ์
    click    xpath=//*[@id="MASSPS"]/ul/li[6]/a
    Wait Until Element Is Visible    xpath=//*[@id="oliSprTitle"]    50
    Wait Until Page Contains    ปริ้นเตอร์    50
    #---เพิ่มไม่กรอกข้อมูล
    click    xpath=//*[@id="obtSprAdd"]
    Wait Until Element Is Visible    xpath=//*[@id="oetSprName"]    50
    click    xpath=//button[@type='submit']
    Wait Until Element Is Visible    xpath=//*[@id="oetSprName-error"]    50
    ${response}    Get Text    xpath=//*[@id="oetSprName-error"]
    Should Be Equal As Strings    ${response}    กรุณากรอกชื่อปริ้นเตอร์
    click    xpath=//*[@id="odvBtnAddEdit"]/button
    #---เพิ่ม กรอกข้อมูล
    click    xpath=//*[@id="obtSprAdd"]
    Wait Until Element Is Visible    xpath=//*[@id="oetSprName"]    50
    click    xpath=//*[@id="oetSprName"]
    input text    id=oetSprName    Start-Bar printer
    #---เลือกจากระบบ
    click    xpath=//*[@id="ofmSetprinter"]/div/div[2]/div/div[2]/div/button
    click    xpath=//*[@id="ofmSetprinter"]/div/div[2]/div/div[2]/div/div/div/ul/li[2]/a/span[2]
    #---เลือกจากแอพ
    click    xpath=//*[@id="ofmSetprinter"]/div/div[2]/div/div[2]/div/button
    Wait Until Element Is Visible    xpath=//*[@id="ofmSetprinter"]/div/div[2]/div/div[2]/div/div/div/ul/li[1]/a/span[2]    50
    click    xpath=//*[@id="ofmSetprinter"]/div/div[2]/div/div[2]/div/div/div/ul/li[1]/a/span[2]
    Wait Until Element Is Visible    xpath=//*[@id="obtBrowseRefApplication"]    50
    click    xpath=//*[@id="obtBrowseRefApplication"]
    Wait Until Element Is Visible    xpath=//*[@id="odvModalContent2"]/div[1]/div/div[1]/label    50
    Wait Until Page Contains    แสดงข้อมูล : Port Printer    50
    #---เลือกปริ้นเตอร์รุ่น TM81
    click    xpath=//table[@id='otbBrowserList']/tbody/tr[10]/td[2]
    Wait Until Element Is Visible    xpath=//button[@onclick="JCNxConfirmSelected('oCmpBrowsePortPrint')"]    50
    click    xpath=//button[@onclick="JCNxConfirmSelected('oCmpBrowsePortPrint')"]
    Wait Until Element Is Visible    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]    50
    click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Element Is Visible    xpath=//*[@id="odvBtnAddEdit"]/button    50
    click    xpath=//*[@id="odvBtnAddEdit"]/button
    [Teardown]    Close Browser

Printer_Edit_Del
    [Setup]    Run Keywords    Open Browser    ${URL}    ${BROWSER}
    ...    AND    Set Selenium Speed    ${SELSPEED}
    # open    https://dev.ada-soft.com/AdaSiamKubota/login
    Maximize Browser Window
    type    id=oetUsername    ${USERNAME}
    type    id=oetPassword    ${PASSWORD}
    #----
    ${ECK}    Get WebElement    xpath=//button[@id='obtLOGConfirmLogin']/span
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//button[@id='obtLOGConfirmLogin']/span
    Wait Until Page Contains    AdaSoft    50
    ${response}    Get Text    xpath=//*[@id="spnCompanyName"]
    Should Be Equal As Strings    ${response}    AdaSoft
    #----คลิกข้อมูลหลัก
    ${ECK}    Get WebElement    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//div[@id='wrapper']/div[2]/div[3]/button/img
    Wait Until Page Contains    ข้อมูลหลัก    50
    #----คลิกข้อมูลจุดขาย
    ${ECK}    Get WebElement    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//*[@id="oNavMenuMAS"]/ul/li/ul/li[3]/a/span
    Wait Until Page Contains    เครื่องพิมพ์    50
    ${response}    Get Text    xpath=//*[@id="MASSPS"]/ul/li[6]/a
    Should Be Equal As Strings    ${response}    เครื่องพิมพ์
    #----คลิกเครื่องพิมพ์
    ${ECK}    Get WebElement    xpath=//*[@id="MASSPS"]/ul/li[6]/a
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//*[@id="MASSPS"]/ul/li[6]/a
    Wait Until Element Is Visible    xpath=//*[@id="oliSprTitle"]    50
    Wait Until Page Contains    ปริ้นเตอร์    50
    ${ECK}    Get WebElement    xpath=//button[@onclick="JSvSetprinterClickPage('next')"]
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//button[@onclick="JSvSetprinterClickPage('next')"]
    Wait Until Element Is Visible    xpath=//*[@id="oliSprTitle"]    50
    #---แก้ไขชื่อเครื่องพิมพ์
    ${EDCK}    Get WebElement    xpath=//img[@title='แก้ไข']
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${EDCK}
    #click    xpath=//img[@title='แก้ไข']
    Wait Until Element Is Visible    xpath=//*[@id="oliSprTitleEdit"]/a    50
    Wait Until Page Contains    แก้ไขปริ้นเตอร์    50
    ${EDCK}    Get WebElement    xpath=//*[@id="oetSprName"]
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${EDCK}
    #click    xpath=//*[@id="oetSprName"]
    clear Element Text    xpath=//*[@id="oetSprName"]
    input text    id=oetSprName    Bar-Printer
    ${ECK}    Get WebElement    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//*[@id="odvBtnAddEdit"]/div/button[1]
    Wait Until Element Is Visible    xpath=//*[@id="odvBtnAddEdit"]/button    50
    ${ECK}    Get WebElement    xpath=//*[@id="odvBtnAddEdit"]/button
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//*[@id="odvBtnAddEdit"]/button
    #---ลบเครื่องพิมพ์
    Wait Until Element Is Visible    xpath=//*[@id="oliSprTitle"]    50
    Wait Until Page Contains    ปริ้นเตอร์    50
    ${ECK}    Get WebElement    xpath=//button[@onclick="JSvSetprinterClickPage('next')"]
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//button[@onclick="JSvSetprinterClickPage('next')"]
    Wait Until Element Is Visible    xpath=//*[@id="oliSprTitle"]    50
    ${ECK}    Get WebElement    xpath=//img[@title='ลบ']
    Execute Javascript    arguments[0].click();    ARGUMENTS    ${ECK}
    #click    xpath=//img[@title='ลบ']
    Wait Until Element Is Visible    xpath=//*[@id="ospConfirmDelete"]    50
    #---ถ้าเครื่องพิมพ์ชื่อ Bar-Printer ให้ลบ
    #${response}    Get Text    xpath=//*[@id="ospConfirmDelete"]
    #Should Be Equal As Strings    ${response}    ยืนยันการลบข้อมูล :018 (Bar-Printer)
    Wait Until Element Is Visible    xpath=//*[@id="osmConfirm"]    50
    ${pNameChk}    Set Variable    ยืนยันการลบข้อมูล :030 (Bar-Printer)    #----ค่า Default ที่ต้องการเปรียบเทียบ
    ${pName}    Get Text    xpath=//*[@id="ospConfirmDelete"]
    IF    "${pName}" == "${pNameChk}"
    TextEQ
    sleep    5s
    ELSE
    TextNEQ
    sleep    5s
    END
    [Teardown]    Close Browser



*** Keywords ***
TextEQ
    click    xpath=//*[@id="osmConfirm"]

TextNEQ
    click    xpath=//*[@id="odvModalDelSetprinter"]/div/div/div[3]/button[2]

open
    [Arguments]    ${element}
    Go To    ${element}

clickAndWait
    [Arguments]    ${element}
    Click Element    ${element}

click
    [Arguments]    ${element}
    Click Element    ${element}

sendKeys
    [Arguments]    ${element}    ${value}
    Press Keys    ${element}    ${value}

submit
    [Arguments]    ${element}
    Submit Form    ${element}

type
    [Arguments]    ${element}    ${value}
    Input Text    ${element}    ${value}

selectAndWait
    [Arguments]    ${element}    ${value}
    Select From List    ${element}    ${value}

select
    [Arguments]    ${element}    ${value}
    Select From List    ${element}    ${value}

verifyValue
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

verifyText
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

verifyElementPresent
    [Arguments]    ${element}
    Page Should Contain Element    ${element}

verifyVisible
    [Arguments]    ${element}
    Page Should Contain Element    ${element}

verifyTitle
    [Arguments]    ${title}
    Title Should Be    ${title}

verifyTable
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

assertConfirmation
    [Arguments]    ${value}
    Alert Should Be Present    ${value}

assertText
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

assertValue
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

assertElementPresent
    [Arguments]    ${element}
    Page Should Contain Element    ${element}

assertVisible
    [Arguments]    ${element}
    Page Should Contain Element    ${element}

assertTitle
    [Arguments]    ${title}
    Title Should Be    ${title}

assertTable
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

waitForText
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

waitForValue
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

waitForElementPresent
    [Arguments]    ${element}
    Page Should Contain Element    ${element}

waitForVisible
    [Arguments]    ${element}
    Page Should Contain Element    ${element}

waitForTitle
    [Arguments]    ${title}
    Title Should Be    ${title}

waitForTable
    [Arguments]    ${element}    ${value}
    Element Should Contain    ${element}    ${value}

doubleClick
    [Arguments]    ${element}
    Double Click Element    ${element}

doubleClickAndWait
    [Arguments]    ${element}
    Double Click Element    ${element}

goBack
    Go Back

goBackAndWait
    Go Back

runScript
    [Arguments]    ${code}
    Execute Javascript    ${code}

runScriptAndWait
    [Arguments]    ${code}
    Execute Javascript    ${code}

setSpeed
    [Arguments]    ${value}
    Set Selenium Timeout    ${value}

setSpeedAndWait
    [Arguments]    ${value}
    Set Selenium Timeout    ${value}

verifyAlert
    [Arguments]    ${value}
    Alert Should Be Present    ${value}
