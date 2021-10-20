

   MEMBER('bolapp.clw')                               ! This is a MEMBER module


   INCLUDE('ABBROWSE.INC'),ONCE
   INCLUDE('ABPOPUP.INC'),ONCE
   INCLUDE('ABRESIZE.INC'),ONCE
   INCLUDE('ABTOOLBA.INC'),ONCE
   INCLUDE('ABWINDOW.INC'),ONCE

                     MAP
                       INCLUDE('BOLAP029.INC'),ONCE        !Local module prodecure declarations
                       INCLUDE('BOLAP002.INC'),ONCE        !Req'd for module callout resolution
                       INCLUDE('BOLAP011.INC'),ONCE        !Req'd for module callout resolution
                       INCLUDE('BOLAP030.INC'),ONCE        !Req'd for module callout resolution
                       INCLUDE('BOLAP031.INC'),ONCE        !Req'd for module callout resolution
                       INCLUDE('BOLAP032.INC'),ONCE        !Req'd for module callout resolution
                       INCLUDE('BOLAP033.INC'),ONCE        !Req'd for module callout resolution
                     END


PrintingOptions PROCEDURE                             !Generated from procedure template - Window

FilesOpened          BYTE
Window               WINDOW('Select Printing Options'),AT(,,185,92),FONT('MS Sans Serif',8,,FONT:regular),CENTER,SYSTEM,GRAY,MAX,MDI,IMM
                       OPTION('When Printing'),AT(12,15,162,44),USE(GLO:SelectPrinter),BOXED
                         RADIO('Select Printer When printing'),AT(20,25),USE(?Option1:Radio1)
                         RADIO('Use default printer when printing'),AT(22,43),USE(?Option1:Radio2)
                       END
                       BUTTON('Close'),AT(146,71,35,14),USE(?OkButton),DEFAULT
                     END

ThisWindow           CLASS(WindowManager)
Init                   PROCEDURE(),BYTE,PROC,DERIVED
Kill                   PROCEDURE(),BYTE,PROC,DERIVED
TakeAccepted           PROCEDURE(),BYTE,PROC,DERIVED
                     END

Toolbar              ToolbarClass

  CODE
  GlobalResponse = ThisWindow.Run()


ThisWindow.Init PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  GlobalErrors.SetProcedureName('PrintingOptions')
  SELF.Request = GlobalRequest
  ReturnValue =PARENT.Init()
  IF ReturnValue THEN RETURN ReturnValue.
  SELF.FirstField = ?Option1:Radio1
  SELF.VCRRequest &= VCRRequest
  SELF.Errors &= GlobalErrors
  SELF.AddItem(Toolbar)
  CLEAR(GlobalRequest)
  CLEAR(GlobalResponse)
  OPEN(Window)
  SELF.Opened=True
  INIMgr.Fetch('PrintingOptions',Window)
  SELF.SetAlerts()
  RETURN ReturnValue


ThisWindow.Kill PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  ReturnValue =PARENT.Kill()
  IF ReturnValue THEN RETURN ReturnValue.
  IF SELF.Opened
    INIMgr.Update('PrintingOptions',Window)
  END
  GlobalErrors.SetProcedureName
  RETURN ReturnValue


ThisWindow.TakeAccepted PROCEDURE

ReturnValue          BYTE,AUTO
Looped BYTE
  CODE
  LOOP
    IF Looped
      RETURN Level:Notify
    ELSE
      Looped = 1
    END
    CASE ACCEPTED()
    OF ?OkButton
       POST(Event:CloseWindow)
    END
  ReturnValue =PARENT.TakeAccepted()
    RETURN ReturnValue
  END
  ReturnValue = Level:Fatal
  RETURN ReturnValue

GetEDIPackage PROCEDURE (Desc)                        !Generated from procedure template - Window

FilesOpened          BYTE
LOC:Commodity        STRING(80)
BOLId                LONG
loc:paCKAGE          STRING(20)
Window               WINDOW('Get Commodity'),AT(,,257,93),FONT('MS Sans Serif',8,,FONT:regular),SYSTEM,GRAY,MDI
                       PROMPT('Import Package :'),AT(4,16),USE(?Prompt1)
                       STRING(@s80),AT(67,16,175,10),USE(LOC:Commodity),FONT(,,COLOR:Blue,,CHARSET:ANSI)
                       PROMPT('Select the appropriate Matching Package'),AT(3,37),USE(?Prompt3),FONT(,,COLOR:Maroon,FONT:bold,CHARSET:ANSI)
                       PROMPT('BOL Application Package Type'),AT(2,48),USE(?Prompt2)
                       ENTRY(@s20),AT(107,48,125,10),USE(loc:paCKAGE),REQ
                       BUTTON('...'),AT(237,47,12,12),USE(?CallLookup)
                       BUTTON('Update'),AT(215,73,35,14),USE(?OkButton),DEFAULT
                     END

ThisWindow           CLASS(WindowManager)
Init                   PROCEDURE(),BYTE,PROC,DERIVED
Kill                   PROCEDURE(),BYTE,PROC,DERIVED
Run                    PROCEDURE(USHORT Number,BYTE Request),BYTE,PROC,DERIVED
TakeAccepted           PROCEDURE(),BYTE,PROC,DERIVED
TakeWindowEvent        PROCEDURE(),BYTE,PROC,DERIVED
                     END

Toolbar              ToolbarClass

  CODE
  GlobalResponse = ThisWindow.Run()
  RETURN(BOLId)


ThisWindow.Init PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  GlobalErrors.SetProcedureName('GetEDIPackage')
  SELF.Request = GlobalRequest
  ReturnValue =PARENT.Init()
  IF ReturnValue THEN RETURN ReturnValue.
  SELF.FirstField = ?Prompt1
  SELF.VCRRequest &= VCRRequest
  SELF.Errors &= GlobalErrors
  SELF.AddItem(Toolbar)
  CLEAR(GlobalRequest)
  CLEAR(GlobalResponse)
  Relate:PackageType.Open
  Relate:TranslationFile.Open
  FilesOpened = True
  OPEN(Window)
  SELF.Opened=True
  INIMgr.Fetch('GetEDIPackage',Window)
  SELF.SetAlerts()
  RETURN ReturnValue


ThisWindow.Kill PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  ReturnValue =PARENT.Kill()
  IF ReturnValue THEN RETURN ReturnValue.
  IF FilesOpened
    Relate:PackageType.Close
    Relate:TranslationFile.Close
  END
  IF SELF.Opened
    INIMgr.Update('GetEDIPackage',Window)
  END
  GlobalErrors.SetProcedureName
  RETURN ReturnValue


ThisWindow.Run PROCEDURE(USHORT Number,BYTE Request)

ReturnValue          BYTE,AUTO
  CODE
  ReturnValue =PARENT.Run(Number,Request)
  GlobalRequest = Request
  SelectPAckageType
  ReturnValue = GlobalResponse
  RETURN ReturnValue


ThisWindow.TakeAccepted PROCEDURE

ReturnValue          BYTE,AUTO
Looped BYTE
  CODE
  LOOP
    IF Looped
      RETURN Level:Notify
    ELSE
      Looped = 1
    END
    CASE ACCEPTED()
    OF ?OkButton
      if ~PACKAGETYPE:packID
      select(?loc:paCKAGE)
      cycle
      end
      TRA:Id          =1
      TRA:Type        ='PCK'
      TRA:InternalCod =clip(PACKAGETYPE:packCode)
      TRA:ExternCod   =clip(Desc)
      TRA:CodeId      =PACKAGETYPE:packID
      add(TranslationFile)
      
      BOLId=PACKAGETYPE:packID
       POST(Event:CloseWindow)
    END
  ReturnValue =PARENT.TakeAccepted()
    CASE ACCEPTED()
    OF ?loc:paCKAGE
      IF loc:paCKAGE OR ?loc:paCKAGE{Prop:Req}
        PACKAGETYPE:packDescription = loc:paCKAGE
        IF Access:PackageType.TryFetch(PACKAGETYPE:byPrincipalDescription)
          IF SELF.Run(1,SelectRecord) = RequestCompleted
            loc:paCKAGE = PACKAGETYPE:packDescription
          ELSE
            SELECT(?loc:paCKAGE)
            CYCLE
          END
        END
      END
      ThisWindow.Reset()
    OF ?CallLookup
      ThisWindow.Update
      PACKAGETYPE:packDescription = loc:paCKAGE
      IF SELF.Run(1,SelectRecord) = RequestCompleted
        loc:paCKAGE = PACKAGETYPE:packDescription
      END
      ThisWindow.Reset(1)
    END
    RETURN ReturnValue
  END
  ReturnValue = Level:Fatal
  RETURN ReturnValue


ThisWindow.TakeWindowEvent PROCEDURE

ReturnValue          BYTE,AUTO
Looped BYTE
  CODE
  LOOP
    IF Looped
      RETURN Level:Notify
    ELSE
      Looped = 1
    END
    CASE EVENT()
    OF EVENT:OpenWindow
          LOC:Commodity=Desc
    END
  ReturnValue =PARENT.TakeWindowEvent()
    RETURN ReturnValue
  END
  ReturnValue = Level:Fatal
  RETURN ReturnValue

ExportToCustoms PROCEDURE                             !Generated from procedure template - Window

FilesOpened          BYTE
fExcludeTranshipment BYTE
fValid               BYTE
fDontSkip            BYTE
LOC:WhatToDo         SHORT
fASingleErrorFound   BYTE
LOC:SAV:Count        LONG
LOC:VoyageDisplay    STRING(80)
LOC:CompletedString  STRING(20)
LOC:CurrentStatus    STRING(100)
LOC:Count            LONG
LOC:Progress         LONG
LOC:ANSIX12Count     LONG
LOC:Current_Loop_ID  LONG
LOC:InfoType         STRING(15)
LOC:SAVPOD           STRING(30)
LOC:SAV:PORT         STRING(20)
LOC:PrincipalCode    STRING(40)
iCounter             LONG
sCrap                STRING(10)
fBreakdown           BYTE
TransmissionTypeString STRING(30)
LOC:SavMasterBOLID   LONG
LOC:SavMasterBOLNUM  STRING(25)
LOC:SAV:Transmission STRING(20)
LOC:SAV:ManifestType STRING(20)
LOC:SCAC             STRING(20)
LOC:Address3        STRING(80)    
LOC:NotBreakDown    BYTE
SelectTag           LONG
DetailAlreadyAssigned   BYTE
NewBLAmendment      BYTE
AGSINIFileName string(100)
pEDILink  long
window               WINDOW('Exporting Manifest To ANSIX12 Format'),AT(,,294,160),FONT('MS Sans Serif',8,,FONT:regular),IMM,GRAY,MDI
                       GROUP,AT(157,0,129,23),USE(?Group1),BOXED,HIDE
                         STRING(@s30),AT(161,15,123,15),USE(TransmissionTypeString),FONT('Times New Roman',9,COLOR:Yellow,FONT:regular)
                       END
                       PROMPT('S.C.A.C  code:'),AT(9,9),USE(?Prompt1)
                       GROUP('Ship Report Information'),AT(8,25,281,41),USE(?Group2),BOXED
                         STRING('Vessel Code:'),AT(13,37),USE(?String6)
                         STRING(@s10),AT(58,38),USE(VESSEL:vesCode),FONT(,,COLOR:Navy,)
                         STRING(@s50),AT(108,38),USE(VESSEL:vesName),FONT(,,COLOR:Blue,)
                         STRING('Report Date:'),AT(13,50),USE(?String7)
                         STRING(@s20),AT(108,50),USE(SHIPREPORT:shrptVoyNoArr),FONT(,,COLOR:Blue,)
                         BUTTON('&Select'),AT(238,49,44,12),USE(?SelectVoyageButton),TIP('Select The Voyage To Be Exported')
                         STRING(@d17),AT(58,50),USE(SHIPREPORT:shrptADA),FONT(,,COLOR:Navy,)
                       END
                       STRING(@s30),AT(59,71),USE(BOLPARENT:bolNumber)
                       BUTTON('Select Parent BOL'),AT(191,70,93,12),USE(?Button4),LEFT,FONT('Arial',10,,FONT:regular,CHARSET:ANSI)
                       STRING(@s100),AT(5,100,274,10),USE(LOC:CurrentStatus),FONT('Arial',,COLOR:Navy,)
                       STRING('Exported File:'),AT(5,110),USE(?String10)
                       STRING(@s255),AT(54,110,223,10),USE(data_AGSEDI07),FONT(,,04000H,)
                       PROGRESS,USE(LOC:Progress),AT(5,121,155,8),RANGE(0,100)
                       STRING(@s20),AT(5,131,155,8),USE(LOC:CompletedString),CENTER,FONT('Arial',,COLOR:Blue,)
                       CHECK('Breakdown'),AT(166,6,55,10),USE(fBreakdown),HIDE
                       ENTRY(@s20),AT(64,9,72,10),USE(LOC:SCAC),UPR
                       CHECK('Exclude Transhipment'),AT(197,7),USE(fExcludeTranshipment),HIDE
                       BUTTON('&Start'),AT(197,141,44,12),USE(?OkButton),DISABLE,TIP('Start The Export'),DEFAULT
                       BUTTON('&Close'),AT(245,141,44,12),USE(?CloseButton),TIP('Close The Window')
                       STRING(@s30),AT(5,142,121,10),USE(BILLOFLADING:bolNumber),HIDE,FONT(,,COLOR:Blue,)
                     END

ThisWindow           CLASS(WindowManager)
Init                   PROCEDURE(),BYTE,PROC,DERIVED
Kill                   PROCEDURE(),BYTE,PROC,DERIVED
TakeAccepted           PROCEDURE(),BYTE,PROC,DERIVED
TakeWindowEvent        PROCEDURE(),BYTE,PROC,DERIVED
                     END

Toolbar              ToolbarClass

  CODE
  GlobalResponse = ThisWindow.Run()

iGetParentContainer    ROUTINE
 clear(BOLCONTAINER:record)
 BOLCONTAINER:bolconVgePtr= SHIPREPORT:shrptVgePtr
 BOLCONTAINER:bolconBOLID=LOC:SavMasterBOLID
 get(BillOfLadingContainer,BOLCONTAINER:byBOL)


iPrereadRecords       ROUTINE
! preread the file and report on it
 STREAM(BillOfLading)


 EMPTY(ErrorLog)
 fASingleErrorFound = FALSE
 iCount# = 0

 CLEAR(BILLOFLADING:Record)
 BILLOFLADING:bolVgePointer = SHIPREPORT:shrptVgePtr
 SET(BILLOFLADING:byVgePtr,BILLOFLADING:byVgePtr)
 LOOP
   NEXT(BillOfLading)
   IF ERRORCODE() THEN BREAK.
   IF ~(BILLOFLADING:bolVgePointer = SHIPREPORT:shrptVgePtr) THEN BREAK.
   IF BILLOFLADING:ParentBOL THEN CYCLE.
   fValid = TRUE

   IF fBreakdown
     IF ~(LOC:SavMasterBOLID = BILLOFLADING:bolParentID)
       fValid = FALSE
     END
   ELSE
     IF BILLOFLADING:bolParentID
       BOLPARENT:bolID = BILLOFLADING:bolParentID
       GET(BillOfLadingParent, BOLPARENT:PrimaryKey)
       IF ~ERRORCODE() AND ~BILLOFLADING:ParentBOL
         fValid = FALSE
       END
     END
   END

   IF fValid
     DO iCheckCodes
     iCount# +=1
   END
   LOC:CurrentStatus = 'Getting bills of lading ' & FORMAT(iCount#,@N5)
   DISPLAY()
 END
 LOC:SAV:Count = iCount#

 FLUSH(BillOfLading)



! [Priority 4000]      
iCheckCodes             ROUTINE
   ! Here we check for the ports
   fDontSkip = 1
                      !message(BILLOFLADING:bolPortOrigin)
   CASE LOC:WhatToDo
   OF -1
     IF ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortOrigin, TRUE, 'PORT'))
       CLEAR(ERRORLOG:errLine)
       ERRORLOG:errLine = 'Untranslated Port Of Origin Code for ' & CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortOrigin, FALSE, 'PORT'))
       ADD(ErrorLog)
       fASingleErrorFound = TRUE
     END

     IF ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortLoading, TRUE,  'PORT'))
       CLEAR(ERRORLOG:errLine)
       ERRORLOG:errLine = 'Untranslated Port Of Loading Code for ' & CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortLoading, FALSE, 'PORT'))
       ADD(ErrorLog)
       fASingleErrorFound = TRUE
     END

     IF ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDischarge, TRUE, 'PORT'))
       CLEAR(ERRORLOG:errLine)
       ERRORLOG:errLine = 'Untranslated Port Of Discharge Code for ' & CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDischarge, FALSE, 'PORT'))
       ADD(ErrorLog)
       fASingleErrorFound = TRUE
     END

     IF ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDelivery, TRUE, 'PORT'))
       CLEAR(ERRORLOG:errLine)
       ERRORLOG:errLine = 'Untranslated Port Of Delivery Code for ' & CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDelivery, FALSE, 'PORT'))
       ADD(ErrorLog)
       fASingleErrorFound = TRUE
     END

   OF 2
     IF ( ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortOrigin, TRUE, 'PORT')) AND fDontSkip AND ~GLO:fAbort)
       GlobalRequest = InsertRecord
       fDontSkip = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, BILLOFLADING:bolPortOrigin,'PORT')
     END

     IF ( ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortLoading, TRUE, 'PORT')) AND fDontSkip AND ~GLO:fAbort)
       GlobalRequest = InsertRecord
       fDontSkip = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, BILLOFLADING:bolPortLoading,'PORT')
     END

     IF ( ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDischarge, TRUE, 'PORT')) AND fDontSkip AND ~GLO:fAbort)
       GlobalRequest = InsertRecord
       fDontSkip = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, BILLOFLADING:bolPortDischarge,'PORT')
     END

     IF ( ~CLIP(iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDelivery, TRUE, 'PORT')) AND fDontSkip AND ~GLO:fAbort)
       GlobalRequest = InsertRecord
       fDontSkip = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, BILLOFLADING:bolPortDelivery,'PORT')
     END



   OF 3

   END

! [Priority 4000]
iMain                        ROUTINE
 DISABLE(?OkButton)
 LOC:PrincipalCode = clip(LOC:SCAC)
 IF ~LOC:PrincipalCode
    MESSAGE('SCAC cannot be blank , process aborted ','Error')
 ELSE
  DO iInitialize
  DO iPrime_SegmentST
  DO iWrite_Segment
  DO iPrime_SegmentM10
  DO iWrite_Segment
  DO iPrime_SegmentLS
  DO iWrite_Segment
  LOC:Count = 0

  IF LOC:SAV:Count = 0
    DO iPrime_SegmentP4
    DO iWrite_Segment
  END


  CLEAR(EDIEXWORK2:RECORD)
  SET(EDIEXWORK2:byPortCode,EDIEXWORK2:byPortCode)
  LOOP
    NEXT(EDIExportWorkFile2)
    IF ERRORCODE() THEN BREAK.
    DO iPrime_SegmentP4
    DO iWrite_Segment
    DO iPrime_SegmentLS
    DO iWrite_Segment
    CLEAR(EDIEXWORK1:RECORD)
    EDIEXWORK1:bolPODischargeCode = EDIEXWORK2:PortCode
    SET(EDIEXWORK1:ByPort,EDIEXWORK1:ByPort)
    LOOP
      NEXT(EDIExportWorkFile1)
      IF ~(CLIP(UPPER(EDIEXWORK1:bolPODischargeCode)) = CLIP(UPPER(EDIEXWORK2:PortCode))) THEN BREAK .
      IF ERRORCODE() THEN BREAK.
      LOC:CurrentStatus = 'Bill of lading.... ' & EDIEXWORK1:bolNumber
      DISPLAY()

      LOC:Count += 1
      DO iUpdateProgress

      DO iPrime_SegmentLX
      DO iWrite_Segment
      DO iPrime_SegmentM11
      DO iWrite_Segment
!      DO iPrime_SegmentLS
      ANSIX12:LINE = 'LS*3'
      DO iWrite_Segment
        LOC:InfoType = 'SHIPPER'
        DO iPrime_SegmentN1
        DO iWrite_Segment
        DO iPrime_SegmentN2
        DO iWrite_Segment
        DO iPrimeWrite_SegmentN3
        DO iPrime_SegmentN4
        DO iWrite_Segment
        LOC:InfoType = 'CONSIGNEE'
        DO iPrime_SegmentN1
        DO iWrite_Segment
        DO iPrime_SegmentN2
        DO iWrite_Segment
        DO iPrimeWrite_SegmentN3
        DO iPrime_SegmentN4
        DO iWrite_Segment
        LOC:InfoType = 'NOTIFY'
        DO iPrime_SegmentN1
        DO iWrite_Segment
        DO iPrime_SegmentN2
        DO iWrite_Segment
        DO iPrimeWrite_SegmentN3
        DO iPrime_SegmentN4
        DO iWrite_Segment
!      DO iPrime_SegmentLE
      ANSIX12:LINE = 'LE*3'
      DO iWrite_Segment
!      DO iPrime_SegmentLS
!      DO iWrite_Segment
!        DO iPrime_SegmentP5
!        DO iWrite_Segment
!      DO iPrime_SegmentLE
!      DO iWrite_Segment
!      DO iPrime_SegmentLS
      ANSIX12:LINE = 'LS*4'
      DO iWrite_Segment
        EDIEXWORK3:bolID = EDIEXWORK1:bolID
        SET(EDIEXWORK3:byBOL,EDIEXWORK3:byBOL)
        LOOP
          NEXT(EDIExportWorkFile3)
          IF ~(EDIEXWORK3:bolID = EDIEXWORK1:bolID) THEN BREAK.
          IF ERRORCODE() THEN BREAK.
          DO iPrime_SegmentVID


          IF ANSIX12:LINE ~= 'VID*OB*@@@@*000000*0000000*0000000'
             DO iWrite_Segment
          END
          sCrap='DESC'
          iCounter = 1
          LOOP UNTIL ~(iCounter < LEN(CLIP(EDIEXWORK3:DetDescription)))
            DO iPrime_SegmentN10
            DO iWrite_Segment
          END
          sCrap='MARKS'
          iCounter = 1
          LOOP UNTIL ~(iCounter < LEN(CLIP(EDIEXWORK1:bolMarks)))
            DO iPrime_SegmentN10
            DO iWrite_Segment
          END
        END
!      DO iPrime_SegmentLE
      ANSIX12:LINE = 'LE*4'
      DO iWrite_Segment
    END
    DO iPrime_SegmentLE                                                         
    DO iWrite_Segment
  END
  DO iPrime_SegmentLE
  DO iWrite_Segment
  DO iPrime_SegmentSE
  DO iWrite_Segment
  DO iCleanup
 END

  HIDE(?OkButton)
  MESSAGE('ANSIX12 Export Completed.')
  LOC:CurrentStatus =  FORMAT(LOC:Count,@N5) & ' Bills of Lading exported'
  UPDATE()


!MAIN FOR AMENDMENT
iMainAmend                 ROUTINE


 DISABLE(?OkButton)
 LOC:PrincipalCode = iGetTheSCAC()
 IF ~LOC:PrincipalCode
    MESSAGE('No SCAC for export has been supplied.|Export aborted.','Problem')
 ELSE
  DO iInitialize
  DO iPrime_SegmentST
  DO iWrite_Segment
  DO iPrime_SegmentM10
  DO iWrite_Segment
  DO iPrime_SegmentLS
  DO iWrite_Segment
  LOC:Count = 0

  CLEAR(EDIEXWORK2:RECORD)
  SET(EDIEXWORK2:byPortCode,EDIEXWORK2:byPortCode)
  LOOP
    NEXT(EDIExportWorkFile2)
    IF ERRORCODE() THEN BREAK.
    DO iPrime_SegmentP4
    DO iWrite_Segment
    DO iPrime_SegmentLS
    DO iWrite_Segment
    CLEAR(EDIEXWORK1:RECORD)
    EDIEXWORK1:bolPODischargeCode = EDIEXWORK2:PortCode
    SET(EDIEXWORK1:ByPort,EDIEXWORK1:ByPort)
    LOOP
      NEXT(EDIExportWorkFile1)
      IF ~(CLIP(UPPER(EDIEXWORK1:bolPODischargeCode)) = CLIP(UPPER(EDIEXWORK2:PortCode))) THEN BREAK .
      IF ERRORCODE() THEN BREAK.
      LOC:CurrentStatus = 'Exporting B/L ..... ' & EDIEXWORK1:bolNumber
      DISPLAY()

      LOC:Count += 1
      DO iUpdateProgress

      DO iPrime_SegmentLX
      DO iWrite_Segment

      IF ~NewBLAmendment

        DO iPrime_SegmentM13D
        DO iWrite_Segment

        ANSIX12:LINE = 'LS*3'
        DO iWrite_Segment
        ANSIX12:LINE = 'LE*3'
        DO iWrite_Segment
        ANSIX12:LINE = 'LS*4'
        DO iWrite_Segment
        ANSIX12:LINE = 'LE*4'
        DO iWrite_Segment
      END

      IF ~EDIEXWORK1:bolID=0

        DO iPrime_SegmentM13A
        DO iWrite_Segment

        DO iPrime_SegmentM11
        DO iWrite_Segment
!       DO iPrime_SegmentLS
        ANSIX12:LINE = 'LS*3'
        DO iWrite_Segment
        LOC:InfoType = 'SHIPPER'
        DO iPrime_SegmentN1
        DO iWrite_Segment
        DO iPrime_SegmentN2
        DO iWrite_Segment
        DO iPrimeWrite_SegmentN3
        DO iPrime_SegmentN4
        DO iWrite_Segment
        LOC:InfoType = 'CONSIGNEE'
        DO iPrime_SegmentN1
        DO iWrite_Segment
        DO iPrime_SegmentN2
        DO iWrite_Segment
        DO iPrimeWrite_SegmentN3
        DO iPrime_SegmentN4
        DO iWrite_Segment
        LOC:InfoType = 'NOTIFY'
        DO iPrime_SegmentN1
        DO iWrite_Segment
        DO iPrime_SegmentN2
        DO iWrite_Segment
        DO iPrimeWrite_SegmentN3
        DO iPrime_SegmentN4
        DO iWrite_Segment
!       DO iPrime_SegmentLE
        ANSIX12:LINE = 'LE*3'
        DO iWrite_Segment
        ANSIX12:LINE = 'LS*4'
        DO iWrite_Segment
        EDIEXWORK3:bolID = EDIEXWORK1:bolID
        SET(EDIEXWORK3:byBOL,EDIEXWORK3:byBOL)
        LOOP
          NEXT(EDIExportWorkFile3)
          IF ~(EDIEXWORK3:bolID = EDIEXWORK1:bolID) THEN BREAK.
          IF ERRORCODE() THEN BREAK.
          DO iPrime_SegmentVID


          IF ANSIX12:LINE ~= 'VID*OB*@@@@*000000*0000000*0000000'
             DO iWrite_Segment
          END
          sCrap='DESC'
          iCounter = 1
          LOOP UNTIL ~(iCounter < LEN(CLIP(EDIEXWORK3:DetDescription)))
            DO iPrime_SegmentN10
            DO iWrite_Segment
          END
          sCrap='MARKS'
          iCounter = 1
          LOOP UNTIL ~(iCounter < LEN(CLIP(EDIEXWORK1:bolMarks)))
            DO iPrime_SegmentN10
            DO iWrite_Segment
          END
        END

        ANSIX12:LINE = 'LE*4'
        DO iWrite_Segment
      END
    END
    DO iPrime_SegmentLE
    DO iWrite_Segment
  END
  DO iPrime_SegmentLE
  DO iWrite_Segment
  DO iPrime_SegmentSE
  DO iWrite_Segment
  DO iCleanup
 END

  HIDE(?OkButton)
  MESSAGE('ANSIX12 Export Completed.')
  LOC:CurrentStatus = 'Exported ' & FORMAT(LOC:Count,@N5) & ' Bills of Lading export completed'
  UPDATE()


! [Priority 4000]
iInitialize                   ROUTINE
 ! How this routine will work is that we will first copy the B/L which qualify to the work file.
 ! We then run through this limited list to fetch all the container info needed for the VID segment.
 ! In doing this we also lookup the related B/L Detail info for these B/L containers.
 ! We then will run through the limited list again and examine from the B/L details and add
 ! info for those B/L details which have no Container Association.
 ! We will then do another pass to count the details and store the amounts in the header
 ! Not very efficient but works quite well

 DISPLAY()
 STREAM(EDIExportWorkFile1)
 STREAM(EDIExportWorkFile2)
 STREAM(EDIExportWorkFile3)
 STREAM(EDIAnsiX12)
 EMPTY(EDIExportWorkFile1)
 EMPTY(EDIExportWorkFile2)
 EMPTY(EDIExportWorkFile3)
 EMPTY(EDIAnsiX12)

 DISPLAY()
 iCount# = 0
 LOC:Current_Loop_ID = 0
 LOC:ANSIX12Count  = 0

 
 DISPLAY()
 ! Create B/L Work File (the limited list)
 CLEAR(BILLOFLADING:RECORD)
 BILLOFLADING:bolVgePointer = SHIPREPORT:shrptVgePtr
 SET(BILLOFLADING:byVgePtr,BILLOFLADING:byVgePtr)
 LOOP
   NEXT(BillOfLading)
   IF ERRORCODE() THEN BREAK.
   IF ~(BILLOFLADING:bolVgePointer = SHIPREPORT:shrptVgePtr) THEN BREAK.
   IF BILLOFLADING:ParentBOL THEN CYCLE.
   !IF ~UPPER(CLIP(BILLOFLADING:bolType)) = 'IMPORT'  THEN CYCLE.

   IF AmendAppend
      BLTAG:TagID= SelectTag
      BLTAG:SortField = BILLOFLADING:bolNumber
      GET(BLTagFile,BLTAG:TagKey)
      IF ERRORCODE() THEN CYCLE.
   END

   fValid = TRUE

   IF fBreakdown
     IF ~(LOC:SavMasterBOLID = BILLOFLADING:bolParentID)
       fValid = FALSE
     END
   ELSE
     IF BILLOFLADING:bolParentID
       BOLPARENT:bolID = BILLOFLADING:bolParentID
       GET(BillOfLadingParent, BOLPARENT:PrimaryKey)
       IF ~ERRORCODE() AND ~BILLOFLADING:ParentBOL
         fValid = FALSE
       END
     END
   END

   IF fExcludeTranshipment
     IF BILLOFLADING:bolTranshipment
       fValid = FALSE
     END
   END
     
   IF fValid
     DO iCheckCodes
     IF GLO:fAbort THEN BREAK.
     IF fDontSkip
       EDIEXWORK1:RECORD :=: BILLOFLADING:RECORD

       !Fetch The Bill of Lading Parties (i.e. Shipper, Consignee, Notify Party)
       !And Assign then to the export file fields
       !GetBillOfLadingParties(BILLOFLADING:bolID)

       CLEAR(LOC:Address3)
       EDIEXWORK1:bolShipperName = BILLOFLADING:bolShipperName
       removeNewLine(BILLOFLADING:bolAShipperAddress, EDIEXWORK1:bolShprAddress1, EDIEXWORK1:bolShprAddress2)
       !AddressSplit(BILLOFLADING:bolAShipperAddress, EDIEXWORK1:bolShprAddress1, EDIEXWORK1:bolShprAddress2, LOC:Address3)
       !EDIEXWORK1:bolShprAddress2 = AddressConcat(EDIEXWORK1:bolShprAddress2, LOC:Address3)
       

       CLEAR(LOC:Address3)
       EDIEXWORK1:bolConName = BILLOFLADING:bolConsigneeName
       removeNewLine( BILLOFLADING:bolAConsigneeAddress, EDIEXWORK1:bolConAddress1, EDIEXWORK1:bolConAddress2)
       !AddressSplit(BILLOFLADING:bolAConsigneeAddress, EDIEXWORK1:bolConAddress1, EDIEXWORK1:bolConAddress2, LOC:Address3)
       !EDIEXWORK1:bolConAddress2 = AddressConcat(EDIEXWORK1:bolConAddress2, LOC:Address3)

       CLEAR(LOC:Address3)
       EDIEXWORK1:bolNotifyName = BILLOFLADING:bolANotifyName
       removeNewLine(BILLOFLADING:bolANotifyAddress, EDIEXWORK1:bolNotifyAddrerss1, EDIEXWORK1:bolNotifyAddress2)
       !AddressSplit(BILLOFLADING:bolANotifyAddress, EDIEXWORK1:bolNotifyAddrerss1, EDIEXWORK1:bolNotifyAddress2, LOC:Address3)
       !EDIEXWORK1:bolNotifyAddress2 = AddressConcat(EDIEXWORK1:bolNotifyAddress2, LOC:Address3)

       EDIEXWORK1:bolPreCarrierPlace = 'KINGSTON'

       ! Here we assign the PORTS
       EDIEXWORK1:bolPOriginCode = iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortOrigin, TRUE, 'PORT')
       EDIEXWORK1:bolPOLCode = iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortLoading, TRUE, 'PORT')
       ! here we will set the discharge port as the port of delivery to acommodate transhipment stuff
       ! i.e. discharge somewhere else but delivered to Kingston
       EDIEXWORK1:bolPODischargeCode = iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDischarge, TRUE, 'PORT')
       !message(EDIEXWORK1:bolPODischargeCode &'    '&pEDILink&'    '& BILLOFLADING:bolPortDischarge,'OC')
       EDIEXWORK1:bolPODeliveryCode = iEDITranslationExternal(pEDILink, BILLOFLADING:bolPortDelivery, TRUE, 'PORT')
       ADD(EDIExportWorkFile1)
       ! Add the ports of discharge as they are encountered
       
       EDIEXWORK2:PortCode = EDIEXWORK1:bolPODischargeCode
       ADD(EDIExportWorkFile2)

       iCount# +=1
       LOC:CurrentStatus =  FORMAT(iCount#,@N5)
       DISPLAY()
     END
   END
 END
 LOC:SAV:Count = iCount#
 
 DISPLAY()
 do iGetParentContainer
 ! At this point we will make the assumption that there are no containers aboard the vessel
 ! The flag will be made true once at least one container is found
 EDIEXWORK3:fContainer = FALSE
 LOC:NotBreakDown = FALSE
 ! Now for the first pass through the work file - Get Container info and related details
 CLEAR(EDIEXWORK1:RECORD, -1)
 SET(EDIEXWORK1:PrimaryKey,EDIEXWORK1:PrimaryKey)
 LOOP
   NEXT(EDIExportWorkFile1)
   IF ERRORCODE() THEN BREAK.
   CLEAR(BOLCONTAINER:RECORD)
   CLEAR(EDIEXWORK3:RECORD)
   
   DetailAlreadyAssigned = FALSE
   BOLCONTAINER:bolconBOLID = EDIEXWORK1:bolID
   SET(BOLCONTAINER:byBOL,BOLCONTAINER:byBOL)
   LOOP
     NEXT(BillOfLadingContainer)
     IF ERRORCODE() THEN BREAK.
     IF ~(BOLCONTAINER:bolconBOLID = EDIEXWORK1:bolID)     THEN BREAK.
     EDIEXWORK3:bolID = EDIEXWORK1:bolID
     EDIEXWORK3:fContainer = TRUE
     LOC:NotBreakDown = TRUE
     EDIEXWORK3:EQInitial = SUB(BOLCONTAINER:bolconContainer,1,4)
     EDIEXWORK3:EQNumber = SUB(BOLCONTAINER:bolconContainer,5,10)
   
     !seals now reside in VoaygeContainerSeals File
     !message('in loop')


     !Related Detail Info
     IF DetailAlreadyAssigned=false
       BOLDETAIL:boldtlBOLID = BILLOFLADING:bolID
       GET(BillOfLadingDetail,BOLDETAIL:byBOL)
       IF ~ERRORCODE()
         EDIEXWORK3:DetNumber = BOLDETAIL:boldtlNoItems
         EDIEXWORK3:DetWeight = BOLDETAIL:boldtlWeight
         IF CLIP(BOLDETAIL:boldtlWeightUnit) = 'lbs'
           EDIEXWORK3:DetWeightUnitCode = 'L'
         ELSE
           EDIEXWORK3:DetWeightUnitCode = 'K'
         END
         COMMODITY:cmdtyID = BOLDETAIL:boldtlCommodityID
         GET(Commodity,COMMODITY:PrimaryKey)
         EDIEXWORK3:defCommodityCode = COMMODITY:cmdtyCode
         EDIEXWORK3:defCommodityCodeQualifier = 'S'
         EDIEXWORK3:DetDescription = BOLDETAIL:boldtlDescription
         PUT(EDIExportWorkFile3)
         DetailAlreadyAssigned = TRUE
         ! Unit Code
         EDIEXWORK1:bolDetails = BOLDETAIL:boldtlNoItems

         PACKAGETYPE:packID = BOLDETAIL:boldtlPackageType
         GET(PackageType,PACKAGETYPE:PrimaryKey)

        
         IF ~BOLDETAIL:boldtlPackageType
           MESSAGE('No package code found for B/L# ' &  CLIP(EDIEXWORK1:bolNumber),'ERROR:Package Code')
         ELSE
           IF (CLIP(iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')) = ' ')
             GlobalRequest = InsertRecord
             I# = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, PACKAGETYPE:packID,'PACKAGE')
           END

           EDIEXWORK1:bolUnitCode = iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')

           IF ~EDIEXWORK1:bolUnitCode
             IF (iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, PACKAGETYPE:packID, 'PACKAGE'))
               EDIEXWORK1:bolUnitCode = iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')
             ELSE
               EDIEXWORK1:bolUnitCode = CLIP(PACKAGETYPE:packCode)
             END
           END
         END
         !EDIEXWORK1:bolUnitCode = 'CNT'
         EDIEXWORK1:bolWeightUnitCode = EDIEXWORK3:DetWeightUnitCode
         EDIEXWORK1:bolContainers=1
         PUT(EDIExportWorkFile1)
       END
  !MODIFICATION STARTED HERE.....

     ELSE
       DetailAlreadyAssigned = TRUE
       BOLDETAIL:boldtlBOLID = BOLCONTAINER:bolconBOLID
       GET(BillOfLadingDetail,BOLDETAIL:byBOL)
       IF ~ERRORCODE()
         EDIEXWORK3:DetNumber = BOLDETAIL:boldtlNoItems
         EDIEXWORK3:DetWeight = BOLDETAIL:boldtlWeight
         IF CLIP(BOLDETAIL:boldtlWeightUnit) = 'lbs'
           EDIEXWORK3:DetWeightUnitCode = 'L'
         ELSE
           EDIEXWORK3:DetWeightUnitCode = 'K'
         END
         COMMODITY:cmdtyID = BOLDETAIL:boldtlCommodityID
         GET(Commodity,COMMODITY:PrimaryKey)
         EDIEXWORK3:defCommodityCode = COMMODITY:cmdtyCode
         EDIEXWORK3:defCommodityCodeQualifier = 'S'
         EDIEXWORK3:DetDescription = BOLDETAIL:boldtlDescription
         PUT(EDIExportWorkFile3)
         ! Unit Code
         EDIEXWORK1:bolDetails = BOLDETAIL:boldtlNoItems

         PACKAGETYPE:packID = BOLDETAIL:boldtlPackageType
         GET(PackageType,PACKAGETYPE:PrimaryKey)
          
          IF ~BOLDETAIL:boldtlPackageType
           MESSAGE('No package code found for B/L# ' &  CLIP(EDIEXWORK1:bolNumber),'ERROR:Package Code')
          ELSE
           IF (CLIP(iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')) = ' ')
             GlobalRequest = InsertRecord
             I# = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, PACKAGETYPE:packID,'PACKAGE')
           END

           EDIEXWORK1:bolUnitCode = iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')

           IF ~EDIEXWORK1:bolUnitCode
             IF (iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, PACKAGETYPE:packID, 'PACKAGE'))
               EDIEXWORK1:bolUnitCode = iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')
             ELSE
               EDIEXWORK1:bolUnitCode = CLIP(PACKAGETYPE:packCode)
             END
           END
         END
        !EDIEXWORK1:bolUnitCode = 'CNT'
         EDIEXWORK1:bolWeightUnitCode = EDIEXWORK3:DetWeightUnitCode
         EDIEXWORK1:bolContainers=1
         PUT(EDIExportWorkFile1)
       END                                                                                            
   !END OF MODIFICATIONS                                                                  

     END
   END
 END

! Now for the second pass through the work file - Get details unrelated to containers
 
 Display()

 CLEAR(EDIEXWORK1:RECORD, -1)
 SET(EDIEXWORK1:PrimaryKey,EDIEXWORK1:PrimaryKey)
 LOOP
   NEXT(EDIExportWorkFile1)
   IF ERRORCODE() THEN BREAK.
   IF EDIEXWORK1:bolContainers THEN CYCLE.
   BOLDETAIL:boldtlBOLID = EDIEXWORK1:bolID
   SET(BOLDETAIL:byBOL,BOLDETAIL:byBOL)
   LOOP
     NEXT(BillofLadingDetail)
     IF ERRORCODE() THEN BREAK.
     IF ~(BOLDETAIL:boldtlBOLID = EDIEXWORK1:bolID) THEN BREAK.
     
     IF  ~EDIEXWORK1:bolContainers
       if ~fBreakdown OR LOC:NotBreakDown THEN CLEAR(EDIEXWORK3:RECORD).


       EDIEXWORK3:bolID = EDIEXWORK1:bolID
       EDIEXWORK3:DetNumber = BOLDETAIL:boldtlNoItems
       EDIEXWORK3:DetWeight = BOLDETAIL:boldtlWeight
       IF CLIP(BOLDETAIL:boldtlWeightUnit) = 'lbs'
         EDIEXWORK3:DetWeightUnitCode = 'L'
       ELSE
         EDIEXWORK3:DetWeightUnitCode = 'K'
       END
       COMMODITY:cmdtyID = BOLDETAIL:boldtlCommodityID
       GET(Commodity,COMMODITY:PrimaryKey)
       EDIEXWORK3:defCommodityCode = COMMODITY:cmdtyCode
       EDIEXWORK3:defCommodityCodeQualifier = 'S'

       EDIEXWORK3:DetDescription= BOLDETAIL:boldtlDescription

  !Container ifo
       do iGetParentContainer
       EDIEXWORK3:fContainer=true
      EDIEXWORK3:EQInitial = SUB(BOLCONTAINER:bolconContainer,1,4)
      EDIEXWORK3:EQNumber  = SUB(BOLCONTAINER:bolconContainer,5,10)
       ADD(EDIExportWorkFile3)
       ! Unit Code

       PACKAGETYPE:packID = BOLDETAIL:boldtlPackageType
       GET(PackageType,PACKAGETYPE:PrimaryKey)
        
       IF ~BOLDETAIL:boldtlPackageType
         MESSAGE('No package code found for B/L# ' &  CLIP(EDIEXWORK1:bolNumber),'ERROR:Package Code')
       ELSE
         IF (CLIP(iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')) = ' ')
           GlobalRequest = InsertRecord
           I# = iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, PACKAGETYPE:packID,'PACKAGE')
         END

         EDIEXWORK1:bolUnitCode = iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')

         IF ~EDIEXWORK1:bolUnitCode
           IF (iGetEDITranslationExternal(PRINCIPAL:pcplID, pEDILink, PACKAGETYPE:packID, 'PACKAGE'))
             EDIEXWORK1:bolUnitCode = iEDITranslationExternal(pEDILink, PACKAGETYPE:packID, TRUE, 'PACKAGE')
           ELSE
             EDIEXWORK1:bolUnitCode = CLIP(PACKAGETYPE:packCode)
           END
         END
       END

       EDIEXWORK1:bolDetails = BOLDETAIL:boldtlNoItems
       EDIEXWORK1:bolWeightUnitCode = EDIEXWORK3:DetWeightUnitCode
       PUT(EDIExportWorkFile1)
     END
   END
 END


 IF fbreakdown
   CLEAR(EDIEXWORK3:RECORD)
   Set(EDIEXWORK3:byBOL,EDIEXWORK3:byBOL)
   LOOP
     NEXT(EDIExportWorkFile3)
     IF ERRORCODE() THEN BREAK.
     IF EDIEXWORK3:fContainer THEN CYCLE.

    
     Put(EDIExportWorkFile3)
   END
 END

 IF AmendAppend
     CLEAR(EDIEXWORK1:Record)
    
     SET(BLTagFile)
     LOOP
       NEXT(BLTagFile)
       IF ERRORCODE() THEN BREAK.
       IF ~BLTAG:DeletedBL THEN CYCLE.
        EDIEXWORK1:bolID = 0
        EDIEXWORK1:bolNumber = CLIP(BLTAG:SortField)
        EDIEXWORK1:bolPOriginCode = iEDITranslationExternal(pEDILink, SHIPREPORT:shrptLastPort, TRUE, 'PORT')
        EDIEXWORK1:bolPODischargeCode =  EDIEXWORK2:PortCode
        EDIEXWORK1:bolDetails = BOLDETAIL:boldtlNoItems
        ADD(EDIExportWorkFile1)
     END
 END


 ! Now for the third pass through the work file - tie up loose ends i.e. counts etc.
 
 DISPLAY()
 CLEAR(EDIEXWORK2:RECORD,-1)
 SET(EDIEXWORK2:byPortCode,EDIEXWORK2:byPortCode)
 LOOP
   NEXT(EDIExportWorkFile2)
   IF ERRORCODE() THEN BREAK.
   iBOLCount# = 0
   CLEAR(EDIEXWORK1:RECORD,-1)
   EDIEXWORK1:bolPODischargeCode = EDIEXWORK2:PortCode
   SET(EDIEXWORK1:ByPort,EDIEXWORK1:ByPort)
   LOOP
     NEXT(EDIExportWorkFile1)
     IF ~(CLIP(UPPER(EDIEXWORK1:bolPODischargeCode)) = CLIP(UPPER(EDIEXWORK2:PortCode))) THEN BREAK .
     IF ERRORCODE() THEN BREAK.
     iCount# = 0
     iTotal# = 0
     EDIEXWORK3:bolID = EDIEXWORK1:bolID
     SET(EDIEXWORK3:byBOL,EDIEXWORK3:byBOL)
     LOOP
       NEXT(EDIExportWorkFile3)
       IF ~(EDIEXWORK3:bolID = EDIEXWORK1:bolID) THEN BREAK.
       IF ERRORCODE() THEN BREAK .
       iCount# += EDIEXWORK3:DetNumber  ! OC Change iCount# += 1
       iTotal# += EDIEXWORK3:DetWeight
     END
     EDIEXWORK1:bolDetails = iCount#
     EDIEXWORK1:bolWeight = iTotal#
     PUT(EDIExportWorkFile1)
     iBOLCount# += 1
   END
   EDIEXWORK2:BOLCount = iBOLCount#
   PUT(EDIExportWorkFile2)
 END

  


! [Priority 4000]
iPrime_SegmentST     ROUTINE
! Sample
! ST*309*0000001

 GlobalAutoAddOnly = TRUE
 GlobalRequest = InsertRecord
 !UpdateEDITransmission
 Access:EDITransmission.PrimeAutoInc()
 
 EDITRANS:Date = TODAY()
 EDITRANS:Time = CLOCK()
 EDITRANS:Comments  = 'TDCC 309 B/L Export - Customs'
 !PUT(EDITransmission)
 Access:EDITransmission.Insert()
 ANSIX12:LINE = 'ST*309*' & CLIP(LEFT(FORMAT(EDITRANS:ID,@N07)))
  

! [Priority 4000]
iPrime_SegmentM10   ROUTINE

! Sample
! M10*SMLU*O*PA*91500080*SEABOARD UNITY*011**1*Y*L*N
! ANSIX12:LINE = 'M10*' & CLIP(SUB(PRINCIPAL:pcplCode,1,4)) & '*O*' & 'JAM' & |
!                '*' & CLIP(SUB(VESSEL:vesCode,1,7)) & '*' & CLIP(SUB(VESSEL:vesName,1,28)) & |
!                '*' & CLIP(SUB(SHIPREPORT:shrptVoyNoArr,1,10)) & '**' & CLIP(LEFT(FORMAT(LOC:SAV:Count,@N15))) & |
!                '*W*L*N'

 ANSIX12:LINE = 'M10*' & CLIP(SUB(LOC:PrincipalCode,1,4)) & '*O*' & 'JAM' & |
                '*' & CLIP(SUB(VESSEL:vesLloydNumber,1,7)) & '*' & CLIP(SUB(VESSEL:vesName,1,28)) & |
                '*' & CLIP(SUB(SHIPREPORT:shrptVoyNoArr,1,10)) & '**' & CLIP(LEFT(FORMAT(LOC:SAV:Count,@N15))) & |
                '*' & CLIP(SUB(LOC:SAV:ManifestType,1,1)) & |
                '*L*N'



! [Priority 4000]
iPrime_SegmentP4     ROUTINE
! Sample
! P4*5201*970925*1

  TheReportDate# = 0
  IF ~SHIPREPORT:shrptADA
    TheReportDate# = SHIPREPORT:shrptADA
  ELSE
    TheReportDate# = SHIPREPORT:shrptADA
  END

  !VOYBER:voyberVgePtr =  SHIPREPORT:shrptVgePtr
  !GET(VoyageBerth,VOYBER:byVoyageptr)
  !IF ERRORCODE() THEN CLEAR(VOYBER:Record).
      
  IF LOC:SAV:Count = 0
    ANSIX12:LINE = 'P4*' & '0008' & '*' & CLIP(LEFT(FORMAT(TheReportDate#,@D11))) & |
                  '*0'
  ELSE
    ANSIX12:LINE = 'P4*' & CLIP(SUB(EDIEXWORK2:PortCode,1,30)) & '*' & CLIP(LEFT(FORMAT(TheReportDate#,@D11))) & |
                '*' & CLIP(LEFT(FORMAT(EDIEXWORK2:BOLCount,@N15)))

  END
   !message(ANSIX12:LINE,LOC:SAV:Count)


! [Priority 4000]
iPrime_SegmentLX     ROUTINE
! LX*1
 ANSIX12:LINE = 'LX*' & CLIP(LEFT(FORMAT(LOC:Count,@N2)))



! [Priority 4000]
iPrime_SegmentM13D   ROUTINE
! Sample
! M13*EISU*22500*A*003100191317*1*1*****
! M13*EISU*22500*D*003100191317*1*1*****
! M13*SMLU*0000000000000000000024128*A*KGT001A41798*3000*03***SMLU

 IF fbreakdown
   ANSIX12:LINE = 'M13*' & CLIP(SUB(LOC:PrincipalCode,1,4)) &|
                  '*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30)) & '*D' &|
                  '*' & CLIP(LOC:SavMasterBOLNUM) &|
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolDetails,@N15))) &|
                  '*01**' & CLIP(EDIEXWORK1:bolNumber) &|                            
                  '*' &  CLIP(SUB(LOC:PrincipalCode,1,4))
 ELSE
   ANSIX12:LINE = 'M13*' & CLIP(SUB(LOC:PrincipalCode,1,4)) &|
                  '*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30)) & '*D' &|
                  '*' & CLIP(EDIEXWORK1:bolNumber) &|
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolDetails,@N15))) &|
                  '*01***' & CLIP(SUB(LOC:PrincipalCode,1,4))
 END


! [Priority 4000]
iPrime_SegmentM13A ROUTINE
! Sample
! M13*EISU*22500*A*003100191317*1*1*****
! M13*EISU*22500*D*003100191317*1*1*****
! M13*SMLU*0000000000000000000024128*A*KGT001A41798*3000*03***SMLU

 IF fbreakdown
   ANSIX12:LINE = 'M13*' & CLIP(SUB(LOC:PrincipalCode,1,4)) &|
                  '*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30)) & '*A' &|
                  '*' & CLIP(LOC:SavMasterBOLNUM) &|
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolDetails,@N15))) &|
                  '*01**' & CLIP(EDIEXWORK1:bolNumber) &|
                  '*' &  CLIP(SUB(LOC:PrincipalCode,1,4))
 ELSE
   ANSIX12:LINE = 'M13*' & CLIP(SUB(LOC:PrincipalCode,1,4)) &|
                  '*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30)) & '*A' &|
                  '*' & CLIP(EDIEXWORK1:bolNumber) &|
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolDetails,@N15))) &|
                  '*01***' & CLIP(SUB(LOC:PrincipalCode,1,4))
 END

! [Priority 4000]
iPrime_SegmentM11   ROUTINE
! Sample
! M11*KGTN00402328*24145*1151*CNT*37922*L***00*KINGSTON**SMLU

 ! unused
 CLEAR(EDIEXWORK1:bolType)
 ! the standard was deliberately broken to accomodate longer B/L numbers
 ! more than 12 digits (the norm for some like CGM)

 IF fBreakdown
   ! Then it must be breakdown
   ANSIX12:LINE = 'M11*' & CLIP(LOC:SavMasterBOLNUM) &  |
                  '*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30)) &|
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolDetails,@N15))) & |
                  '*' & CLIP(SUB(EDIEXWORK1:bolUnitCode,1,4)) & |
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolWeight,@N10.3))) & |
                  '*' & CLIP(SUB(EDIEXWORK1:bolWeightUnitCode,1,3)) & |
                  '***' & CLIP(SUB(EDIEXWORK1:bolType,1,2)) & |
                  '*' & CLIP(SUB(EDIEXWORK1:bolPreCarrierPlace,1,17)) & |
                  '*' & CLIP(EDIEXWORK1:bolNumber) &  |
                  '*' & CLIP(SUB(LOC:PrincipalCode,1,4)) & |
                  '*' & CLIP(SUB(LOC:PrincipalCode,1,4)) & |
                  '**'
 ELSE
  ! the regular stuff
   ANSIX12:LINE = 'M11*' & CLIP(EDIEXWORK1:bolNumber) &  |
                  '*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30)) &|
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolDetails,@N15))) & |
                  '*' & CLIP(SUB(EDIEXWORK1:bolUnitCode,1,4)) & |
                  '*' & CLIP(LEFT(FORMAT(EDIEXWORK1:bolWeight,@N10.3))) & |
                  '*' & CLIP(SUB(EDIEXWORK1:bolWeightUnitCode,1,3)) & |
                  '***' & CLIP(SUB(EDIEXWORK1:bolType,1,2)) & |
                  '*' & CLIP(SUB(EDIEXWORK1:bolPreCarrierPlace,1,17)) & |
                  '**' & CLIP(SUB(LOC:PrincipalCode,1,4)) & |
                  '***'
 END

! [Priority 4000]
iPrime_SegmentN1     ROUTINE
! Sample
! N1*SH*SARA LEE KNIT PRODUCTS
! N1*CN*SARA LEE KNIT PRODUCTS
! N1*N1*SARA LEE KNIT PRODUCTS
 CASE CLIP(LOC:InfoType)
 OF  'SHIPPER'
    IF CLIP(EDIEXWORK1:bolShipperName)
      ANSIX12:LINE = 'N1*SH*' & CLIP(SUB(EDIEXWORK1:bolShipperName,1,35)) & |
                     '**'
    ELSE
      ANSIX12:LINE = 'N1*SH*UNKNOWN**'
    END

 OF 'CONSIGNEE'
    IF CLIP(EDIEXWORK1:bolConName)
      ANSIX12:LINE = 'N1*CN*' & CLIP(SUB(EDIEXWORK1:bolConName,1,35)) & |
                     '**'
    ELSE
      ANSIX12:LINE = 'N1*CN*UNKNOWN**'
    END

 OF 'NOTIFY'
    IF CLIP(EDIEXWORK1:bolNotifyName)
      ANSIX12:LINE = 'N1*N1*' & CLIP(SUB(EDIEXWORK1:bolNotifyName,1,35)) & |
                     '**'
    ELSE
      ANSIX12:LINE = 'N1*N1*UNKNOWN**'
    END
 END

! [Priority 4000]

iPrime_SegmentN2     ROUTINE
! Sample
! N2*SARA LEE KNIT PRODUCTS*
 CASE CLIP(LOC:InfoType)
 OF 'SHIPPER'
    IF CLIP(EDIEXWORK1:bolShprAddress1)
      ANSIX12:LINE = 'N2*' & CLIP(SUB(EDIEXWORK1:bolShprAddress1,1,35)) & |
                   '*'
    ELSE
      ANSIX12:LINE = 'N2*UNKNOWN*'
    END

 OF 'CONSIGNEE'
    IF CLIP(EDIEXWORK1:bolConAddress1)
    ANSIX12:LINE = 'N2*' & CLIP(SUB(EDIEXWORK1:bolConAddress1,1,35)) & |
                   '*'
    ELSE
      ANSIX12:LINE = 'N2*UNKNOWN*'
    END

 OF 'NOTIFY'
    IF CLIP(EDIEXWORK1:bolNotifyAddrerss1)
    ANSIX12:LINE = 'N2*' & CLIP(SUB(EDIEXWORK1:bolNotifyAddrerss1,1,35)) & |
                   '*'
    ELSE
      ANSIX12:LINE = 'N2*UNKNOWN*'
    END
 END

iPrimeWrite_SegmentN3     ROUTINE
! Sample
! N3*SARA LEE KNIT PRODUCTS*
 CASE CLIP(LOC:InfoType)
 OF 'SHIPPER'
    ANSIX12:LINE = 'N3*UNKNOWN*'
    IF CLIP(EDIEXWORK1:bolShprAddress1)  and len(CLIP(EDIEXWORK1:bolNotifyAddrerss1)) > 35
      IF CLIP(SUB(EDIEXWORK1:bolShprAddress1,36,len(CLIP(EDIEXWORK1:bolNotifyAddrerss1))-35))
        ANSIX12:LINE = 'N3*' & CLIP(SUB(EDIEXWORK1:bolShprAddress1,36,len(CLIP(EDIEXWORK1:bolNotifyAddrerss1))-35)) &  '*'
      END
    END
    DO iWrite_Segment

 OF 'CONSIGNEE'
     ANSIX12:LINE = 'N3*UNKNOWN*'
     IF CLIP(EDIEXWORK1:bolConAddress1) and  len(CLIP(EDIEXWORK1:bolConAddress1)) > 35
       IF CLIP(SUB(EDIEXWORK1:bolConAddress1,36,len(CLIP(EDIEXWORK1:bolConAddress1))-35))
         ANSIX12:LINE = 'N3*' & CLIP(SUB(EDIEXWORK1:bolConAddress1,36,len(CLIP(EDIEXWORK1:bolConAddress1))-35)) &  '*'
       END
    END
    DO iWrite_Segment
  

 OF 'NOTIFY'
    ANSIX12:LINE = 'N3*UNKNOWN*'
    IF CLIP(EDIEXWORK1:bolNotifyAddrerss1)   AND len(CLIP(EDIEXWORK1:bolNotifyAddrerss1)) > 35
      IF CLIP(SUB(EDIEXWORK1:bolNotifyAddrerss1,36,len(CLIP(EDIEXWORK1:bolNotifyAddrerss1))-35))
        ANSIX12:LINE = 'N3*' & CLIP(SUB(EDIEXWORK1:bolNotifyAddrerss1,36,len(CLIP(EDIEXWORK1:bolNotifyAddrerss1))-35)) &  '*'
      END
    END
    DO iWrite_Segment
 END



! [Priority 4000]
iPrime_SegmentN4     ROUTINE
! Sample
! N4*SARA LEE KNIT PRODUCTS*
 CASE CLIP(LOC:InfoType)
 OF 'SHIPPER'
    IF CLIP(EDIEXWORK1:bolShprAddress2)
      ANSIX12:LINE = 'N4*' & CLIP(SUB(EDIEXWORK1:bolShprAddress2,1,35)) & |
                   '*'
    ELSE
      ANSIX12:LINE = 'N4*UNKNOWN*'
    END

 OF 'CONSIGNEE'
    IF CLIP(EDIEXWORK1:bolConAddress2)
      ANSIX12:LINE = 'N4*' & CLIP(SUB(EDIEXWORK1:bolConAddress2,1,35)) & |
                   '*'
    ELSE
      ANSIX12:LINE = 'N4*UNKNOWN*'
    END

 OF 'NOTIFY'
    IF CLIP(EDIEXWORK1:bolNotifyAddress2)
      ANSIX12:LINE = 'N4*' & CLIP(SUB(EDIEXWORK1:bolNotifyAddress2,1,35)) & |
                   '*'
    ELSE
      ANSIX12:LINE = 'N4*UNKNOWN*'
    END
 END

! [Priority 4000]
iPrime_SegmentM12   ROUTINE

! [Priority 4000]
iPrime_SegmentP5     ROUTINE
! Sample
! P5*L*ZZ*KINGSTON

 ANSIX12:LINE = 'P5*L*ZZ*' & CLIP(SUB(EDIEXWORK1:bolPOLCode,1,30))

! [Priority 4000]
iPrime_SegmentVID    ROUTINE
! Sample - Container Information
! VID*CN*KLIZ*110019*0024051*0024052
 IF EDIEXWORK3:fContainer
   ANSIX12:LINE = 'VID*CN*' & CLIP(SUB(EDIEXWORK3:EQInitial,1,4)) &  |
                '*' & CLIP(SUB(EDIEXWORK3:EQNumber,1,10)) & |
                '*' & CLIP(SUB(EDIEXWORK3:EQSeal1,1,15)) & |
                '*' & CLIP(SUB(EDIEXWORK3:EQSeal2,1,15))
 ELSE

    ANSIX12:LINE = 'VID*OB*@@@@*000000*0000000*0000000'
 END

! [Priority 4000]
iPrime_SegmentN10   ROUTINE
! Sample - B/L Details
! N10*1000*WEARING APPAREL*NONE

! ANSIX12:LINE = 'N10*' & CLIP(SUB(EDIEXWORK3:DetNumber,1,15)) &  |
!                '*' & CLIP(SUB(EDIEXWORK3:DetDescription,1,255)) &|
!                '*' & CLIP(SUB(EDIEXWORK1:bolMarks,1,255)) &|
!                '*Z*' & CLIP(SUB(EDIEXWORK3:defCommodityCode,1,10)) & |
!                '**' & CLIP(SUB(EDIEXWORK3:DetWeightUnitCode,1,1)) & |
!                '*' & CLIP(SUB(EDIEXWORK3:DetWeight,1,5))

! The above code replaced by the code below. I think its crappy but it is what customs say they want.
! Its so silly.

 CASE CLIP(sCrap)

 OF 'DESC'
   IF (iCounter = 1)
     ANSIX12:LINE = 'N10*' & CLIP(SUB(EDIEXWORK3:DetNumber,1,15)) & '*' & CLIP(SUB(EDIEXWORK3:DetDescription,iCounter,30))
   ELSE
     ANSIX12:LINE = 'N10**' & CLIP(SUB(EDIEXWORK3:DetDescription,iCounter,30))
   END
!                  '*' & CLIP(SUB(EDIEXWORK1:bolMarks,1,30)) &|
!                  '*Z*' & CLIP(SUB(EDIEXWORK3:defCommodityCode,1,10)) & |
!                  '**' & CLIP(SUB(EDIEXWORK3:DetWeightUnitCode,1,1)) & |
!                  '*' & CLIP(SUB(EDIEXWORK3:DetWeight,1,5))

 OF 'MARKS'
   ANSIX12:LINE = 'N10***' & CLIP(SUB(EDIEXWORK1:bolMarks,iCounter,30))
!                  '*' & CLIP(SUB(EDIEXWORK3:DetDescription,1,30)) &|
!                  '*' & CLIP(SUB(EDIEXWORK1:bolMarks,1,30)) !&|
!                  '*Z*' & CLIP(SUB(EDIEXWORK3:defCommodityCode,1,10)) & |
!                  '**' & CLIP(SUB(EDIEXWORK3:DetWeightUnitCode,1,1)) & |
!                  '*' & CLIP(SUB(EDIEXWORK3:DetWeight,1,5))

 END

 iCounter += 30

! [Priority 4000]
iPrime_SegmentH1     ROUTINE

! [Priority 4000]
iPrime_SegmentH2     ROUTINE

! [Priority 4000]
iPrime_SegmentSE     ROUTINE
! Sample
! SE*25*0000001

 ANSIX12:LINE = 'SE*' &  CLIP(LEFT(FORMAT(LOC:ANSIX12Count+1,@N7))) & |
                '*' & CLIP(LEFT(FORMAT(EDITRANS:ID,@N07)))



! [Priority 4000]
iPrime_SegmentLS     ROUTINE
! Sample
! LS*1
 LOC:Current_Loop_ID +=1
 ANSIX12:LINE = 'LS*' & CLIP(LEFT(FORMAT(LOC:Current_Loop_ID,@N2)))

! [Priority 4000]
iPrime_SegmentLE     ROUTINE
! Sample
! LE*1
 ANSIX12:LINE = 'LE*' & CLIP(LEFT(FORMAT(LOC:Current_Loop_ID,@N2)))
 LOC:Current_Loop_ID -=1

! [Priority 4000]
iWrite_Segment         ROUTINE
 ! Line Count updated after each line is written
  LOC:ANSIX12Count +=1
  ANSIX12:LINE = CLIP(iFlattenString(ANSIX12:LINE))
  ADD(EDIAnsiX12)

! [Priority 4000]
iUpdateProgress        ROUTINE
 LOC:Progress = (LOC:Count / LOC:SAV:Count) * 100
 LOC:CompletedString = FORMAT(LOC:Progress,@N3) & '% completed'
 DISPLAY()





! [Priority 4000]
iCleanup                   ROUTINE
 LOC:CurrentStatus = ''
 DISPLAY()

 FLUSH(EDIExportWorkFile1)
 FLUSH(EDIExportWorkFile2)
 FLUSH(EDIExportWorkFile3)
 FLUSH(EDIAnsiX12)

 CLOSE(EDIExportWorkFile1)
 CLOSE(EDIExportWorkFile2)
 CLOSE(EDIExportWorkFile3)
 CLOSE(EDIAnsiX12)

 IF  ~GETINI('EDI','KeepTempFiles',0,CLIP(AGSINIFileName))
   REMOVE(EDIExportWorkFile1)
   REMOVE(EDIExportWorkFile2)
   REMOVE(EDIExportWorkFile3)
 END


ThisWindow.Init PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  GlobalErrors.SetProcedureName('ExportToCustoms')
  SELF.Request = GlobalRequest
  ReturnValue =PARENT.Init()
  IF ReturnValue THEN RETURN ReturnValue.
  SELF.FirstField = ?TransmissionTypeString
  SELF.VCRRequest &= VCRRequest
  SELF.Errors &= GlobalErrors
  SELF.AddItem(Toolbar)
  CLEAR(GlobalRequest)
  CLEAR(GlobalResponse)
  remove('Customs_ANSIX12.txt')
  data_AGSEDI04 = '.\editmpfile01.tmp'
  data_AGSEDI05 = '.\editmpfile02.tmp'
  data_AGSEDI06 = '.\editmpfile03.tmp'
  GLO:BLTagFile  = '.\BLTAG.tmp'
  
  pEDILink =1
  OPEN(EDILink)
  EDILINK:LINKID =1
  GET(EDILink,EDILINK:PrimaryKey)
  IF ~ERRORCODE()
    data_AGSEDI07 = EDILINK:LINKDEFOUTFILE
  ELSE
    EDILINK:LINKID        =1
    EDILINK:LINKNAME      ='Export to Customs'
    EDILINK:LINKTYPE      ='Export'
    EDILINK:LINKDEFINFILE =' '
    EDILINK:LINKDEFOUTFILE='Customs_ANSIX12.txt'
    data_AGSEDI07 = EDILINK:LINKDEFOUTFILE
  END
  
  CLEAR(SHIPREPORT:Record)
  
  CLEAR(AmendAppend)
  open(ErrorLog)
  EMPTY(ErrorLog)
  close(ErrorLog)
  
  
  Relate:BLTagFile.Open
  Relate:BillOfLading.Open
  Relate:EDIAnsiX12.Open
  Relate:EDIExportWorkFile1.Open
  Relate:EDIExportWorkFile2.Open
  Relate:EDIExportWorkFile3.Open
  Relate:EDILink.Open
  Relate:EDITranslation.Open
  Relate:EDITransmission.Open
  Relate:ErrorLog.Open
  Access:Client.UseFile
  Access:ShipReport.UseFile
  Access:Country.UseFile
  Access:ContainerSizeType.UseFile
  Access:Container.UseFile
  Access:Vessel.UseFile
  Access:PackageType.UseFile
  Access:Port.UseFile
  Access:BillOfLadingContainer.UseFile
  Access:BillOfLadingDetail.UseFile
  Access:BillOfLadingParent.UseFile
  FilesOpened = True
  OPEN(window)
  SELF.Opened=True
   fExcludeTranshipment = TRUE
   DISPLAY()
   GLO:fAbort = FALSE
  INIMgr.Fetch('ExportToCustoms',window)
  SELF.SetAlerts()
  RETURN ReturnValue


ThisWindow.Kill PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  ReturnValue =PARENT.Kill()
  IF ReturnValue THEN RETURN ReturnValue.
  IF FilesOpened
    Relate:BLTagFile.Close
    Relate:BillOfLading.Close
    Relate:EDIAnsiX12.Close
    Relate:EDIExportWorkFile1.Close
    Relate:EDIExportWorkFile2.Close
    Relate:EDIExportWorkFile3.Close
    Relate:EDILink.Close
    Relate:EDITranslation.Close
    Relate:EDITransmission.Close
    Relate:ErrorLog.Close
  END
  IF SELF.Opened
    INIMgr.Update('ExportToCustoms',window)
  END
  GlobalErrors.SetProcedureName
  RETURN ReturnValue


ThisWindow.TakeAccepted PROCEDURE

ReturnValue          BYTE,AUTO
Looped BYTE
  CODE
  LOOP
    IF Looped
      RETURN Level:Notify
    ELSE
      Looped = 1
    END
    CASE ACCEPTED()
    OF ?Button4
        GlobalRequest = SelectRecord
           SelectBillOfLadingParent !(SHIPREPORT:shrptVgePtr)
           LOC:SavMasterBOLID = BOLPARENT:bolID
           LOC:SavMasterBOLNUM = BOLPARENT:bolNumber
    OF ?OkButton
       !Just before starting lets gather some last minute information
        if ~LOC:SCAC
            message('SCAC cannot be blank')
            cycle
        end
      
        IF ~LOC:SavMasterBOLID
           GlobalRequest = SelectRecord
           SelectBillOfLadingParent !(SHIPREPORT:shrptVgePtr)
           LOC:SavMasterBOLID = BOLPARENT:bolID
           LOC:SavMasterBOLNUM = BOLPARENT:bolNumber
         end
        
        LOC:WhatToDo = -1
        VESSEL:vesID = SHIPREPORT:shrptVessel
        GET(Vessel, VESSEL:PrimaryKey)
        
        
        DO iPrereadRecords
        
        LOC:WhatToDo=2
        
        IF ~(LOC:WhatToDo = 1) AND LOC:WhatToDo
          ENABLE(?OkButton)
        END
        
        
        LOC:CurrentStatus = 'Processing...'
        
        CASE MESSAGE('Is this the first time this manifest is being transmitted?','Confirmation',ICON:Question,BUTTON:Yes+BUTTON:NO,BUTTON:Yes,1)
        OF BUTTON:Yes
          TransmissionTypeString = 'First Transmission'
          IF fBreakdown
            LOC:SAV:ManifestType = 'b'
          ELSE
            LOC:SAV:ManifestType = 'W'
          END
          DO iMain
        
        OF BUTTON:No
          TransmissionTypeString = 'Subsequest Transmission'
        !   IF fBreakdown
        !     LOC:SAV:ManifestType = 'b'
        !   ELSE
        
            SelectTag=SelectAmendBL(SHIPREPORT:shrptVgePtr)
        
            IF CLIP(AmendAppend) = 'AMEND'
              LOC:SAV:ManifestType = 'Y'
        
            CASE MESSAGE('Is this Amendment for new Bills of Lading?','AMENDMENT',ICON:Question,BUTTON:Yes+BUTTON:NO,BUTTON:No,1)
              OF BUTTON:Yes
               NewBLAmendment = TRUE
        
              OF BUTTON:No
               NewBLAmendment = FALSE
            END
        
              DO iMainAmend           !Main for amendments
        
           ! ELSIF UPPER(CLIP(AmendAppend)) = 'PRINTAMEND'
        
           !   PrintAmendmentform(SHIPREPORT:shrptVgePtr)
            !  DO ProcedureReturn
        
            ELSE
              LOC:SAV:ManifestType = 'b'
              DO iMain
            END
        !  END
          
        END !CASE
        
        DISPLAY()
        
        
        !      IF LOC:SAV:ManifestType = 'Y'
        !       DO iMainAmend           !Main for amendments
        !      ELSE
        !        DO iMain
        !      END
    END
  ReturnValue =PARENT.TakeAccepted()
    CASE ACCEPTED()
    OF ?SelectVoyageButton
      ThisWindow.Update
       GlobalRequest = SelectRecord
      SelectShipReport
      
      IF SHIPREPORT:shrptVgePtr
      ENABLE(?OkButton)
      DISPLAY()
      END
    OF ?fBreakdown
       IF fBreakdown
           GlobalRequest = SelectRecord
           SelectBillOfLadingParent !(SHIPREPORT:shrptVgePtr)
           LOC:SavMasterBOLID = BOLPARENT:bolID
           LOC:SavMasterBOLNUM = BOLPARENT:bolNumber
         ELSE
           CLEAR(LOC:SAV:Transmission)
           CLEAR(LOC:SavMasterBOLID)
           CLEAR(LOC:SavMasterBOLNUM)                                
         END
    OF ?CloseButton
      ThisWindow.Update
       POST(Event:CloseWindow)
    END
    RETURN ReturnValue
  END
  ReturnValue = Level:Fatal
  RETURN ReturnValue


ThisWindow.TakeWindowEvent PROCEDURE

ReturnValue          BYTE,AUTO
Looped BYTE
  CODE
  LOOP
    IF Looped
      RETURN Level:Notify
    ELSE
      Looped = 1
    END
    CASE EVENT()
    OF EVENT:CloseWindow
       REMOVE(data_AGSEDI04)
       REMOVE(data_AGSEDI05)
       REMOVE(data_AGSEDI06)
       REMOVE(BLTagFile)
    OF EVENT:OpenWindow
         fBreakdown=true
    END
  ReturnValue =PARENT.TakeWindowEvent()
    RETURN ReturnValue
  END
  ReturnValue = Level:Fatal
  RETURN ReturnValue

SelectEDILink PROCEDURE                               !Generated from procedure template - Window

CurrentTab           STRING(80)
FilesOpened          BYTE
BRW1::View:Browse    VIEW(EDILink)
                       PROJECT(EDILINK:LINKID)
                       PROJECT(EDILINK:LINKNAME)
                       PROJECT(EDILINK:LINKTYPE)
                       PROJECT(EDILINK:LINKDEFINFILE)
                       PROJECT(EDILINK:LINKDEFOUTFILE)
                     END
Queue:Browse:1       QUEUE                            !Queue declaration for browse/combo box using ?Browse:1
EDILINK:LINKID         LIKE(EDILINK:LINKID)           !List box control field - type derived from field
EDILINK:LINKNAME       LIKE(EDILINK:LINKNAME)         !List box control field - type derived from field
EDILINK:LINKTYPE       LIKE(EDILINK:LINKTYPE)         !List box control field - type derived from field
EDILINK:LINKDEFINFILE  LIKE(EDILINK:LINKDEFINFILE)    !List box control field - type derived from field
EDILINK:LINKDEFOUTFILE LIKE(EDILINK:LINKDEFOUTFILE)   !List box control field - type derived from field
Mark                   BYTE                           !Stores entry's marked status
ViewPosition           STRING(1024)                   !Entry's file position string
                     END
QuickWindow          WINDOW('Browse the EDILink File'),AT(,,365,183),FONT('MS Sans Serif',8,,),IMM,HLP('SelectEDILink'),SYSTEM,GRAY,RESIZE,MDI
                       LIST,AT(8,20,342,142),USE(?Browse:1),IMM,HVSCROLL,MSG('Browsing Records'),FORMAT('0R(2)|M~LINKID~C(0)@n-13@80L(2)|M~Name~@s40@44L(2)|M~Type~@s10@80L(2)|M~Default ' &|
   'Input file~@s80@80L(2)|M~Default Output File~@s80@'),FROM(Queue:Browse:1)
                       BUTTON('&Select'),AT(312,168,45,14),USE(?Select:2)
                       BUTTON('&Insert'),AT(207,130,45,14),USE(?Insert:3),HIDE
                       BUTTON('&Change'),AT(256,130,45,14),USE(?Change:3),HIDE,DEFAULT
                       BUTTON('&Delete'),AT(305,130,45,14),USE(?Delete:3),HIDE
                       SHEET,AT(4,4,350,162),USE(?CurrentTab)
                         TAB('EDI Links'),USE(?Tab:2)
                         END
                       END
                       BUTTON('Close'),AT(260,152,45,14),USE(?Close),HIDE
                       BUTTON('Help'),AT(309,152,45,14),USE(?Help),HIDE,STD(STD:Help)
                     END

ThisWindow           CLASS(WindowManager)
Init                   PROCEDURE(),BYTE,PROC,DERIVED
Kill                   PROCEDURE(),BYTE,PROC,DERIVED
Run                    PROCEDURE(USHORT Number,BYTE Request),BYTE,PROC,DERIVED
                     END

Toolbar              ToolbarClass
BRW1                 CLASS(BrowseClass)               ! Browse using ?Browse:1
Q                      &Queue:Browse:1                !Reference to browse queue
                     END

BRW1::Sort0:Locator  StepLocatorClass ! Default Locator
BRW1::Sort0:StepClass StepLongClass ! Default Step Manager
BRW1::EIPManager     BrowseEIPManager ! Browse EIP Manager for Browse using ?Browse:1
Resizer              CLASS(WindowResizeClass)
Init                   PROCEDURE(BYTE AppStrategy=AppStrategy:Resize,BYTE SetWindowMinSize=False,BYTE SetWindowMaxSize=False)
                     END


  CODE
  GlobalResponse = ThisWindow.Run()


ThisWindow.Init PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  GlobalErrors.SetProcedureName('SelectEDILink')
  SELF.Request = GlobalRequest
  ReturnValue =PARENT.Init()
  IF ReturnValue THEN RETURN ReturnValue.
  SELF.FirstField = ?Browse:1
  SELF.VCRRequest &= VCRRequest
  SELF.Errors &= GlobalErrors
  SELF.AddItem(Toolbar)
  CLEAR(GlobalRequest)
  CLEAR(GlobalResponse)
  SELF.AddItem(?Close,RequestCancelled)
  Relate:EDILink.Open
  FilesOpened = True
  BRW1.Init(?Browse:1,Queue:Browse:1.ViewPosition,BRW1::View:Browse,Queue:Browse:1,Relate:EDILink,SELF)
  OPEN(QuickWindow)
  SELF.Opened=True
  BRW1.Q &= Queue:Browse:1
  BRW1::Sort0:StepClass.Init(+ScrollSort:AllowAlpha)
  BRW1.AddSortOrder(BRW1::Sort0:StepClass,EDILINK:PRIMARYKEY)
  BRW1.AddLocator(BRW1::Sort0:Locator)
  BRW1::Sort0:Locator.Init(,EDILINK:LINKID,1,BRW1)
  BRW1.AddField(EDILINK:LINKID,BRW1.Q.EDILINK:LINKID)
  BRW1.AddField(EDILINK:LINKNAME,BRW1.Q.EDILINK:LINKNAME)
  BRW1.AddField(EDILINK:LINKTYPE,BRW1.Q.EDILINK:LINKTYPE)
  BRW1.AddField(EDILINK:LINKDEFINFILE,BRW1.Q.EDILINK:LINKDEFINFILE)
  BRW1.AddField(EDILINK:LINKDEFOUTFILE,BRW1.Q.EDILINK:LINKDEFOUTFILE)
  Resizer.Init(AppStrategy:Surface,Resize:SetMinSize)
  SELF.AddItem(Resizer)
  INIMgr.Fetch('SelectEDILink',QuickWindow)
  Resizer.Resize                                      !Resize/Reset required after window size altered by INI manager
  Resizer.Reset
  BRW1.SelectControl=?Select:2
  BRW1.HideSelect = 1
  BRW1.InsertControl=?Insert:3
  BRW1.ChangeControl=?Change:3
  BRW1.DeleteControl=?Delete:3
  BRW1.AskProcedure = 1
  BRW1.AddToolbarTarget(Toolbar)
  BRW1.ToolbarItem.HelpButton = ?Help
  SELF.SetAlerts()
  RETURN ReturnValue


ThisWindow.Kill PROCEDURE

ReturnValue          BYTE,AUTO
  CODE
  ReturnValue =PARENT.Kill()
  IF ReturnValue THEN RETURN ReturnValue.
  IF FilesOpened
    Relate:EDILink.Close
  END
  IF SELF.Opened
    INIMgr.Update('SelectEDILink',QuickWindow)
  END
  GlobalErrors.SetProcedureName
  RETURN ReturnValue


ThisWindow.Run PROCEDURE(USHORT Number,BYTE Request)

ReturnValue          BYTE,AUTO
  CODE
  ReturnValue =PARENT.Run(Number,Request)
  GlobalRequest = Request
  UpdateEDILink
  ReturnValue = GlobalResponse
  RETURN ReturnValue


Resizer.Init PROCEDURE(BYTE AppStrategy=AppStrategy:Resize,BYTE SetWindowMinSize=False,BYTE SetWindowMaxSize=False)


  CODE
  PARENT.Init(AppStrategy,SetWindowMinSize,SetWindowMaxSize)
  SELF.AutoTransparent=True
  SELF.SetParentDefaults

