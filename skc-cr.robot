*** Settings ***
Documentation     SKC AUTOMATION TEST
Library           Selenium2Library    implicit_wait=50

*** Variables ***
${URL}            http://sit.ada-soft.com:8889/login
${BROWSER}        chrome
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
