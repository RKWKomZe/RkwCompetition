# RkwCompetition

## Development

**Local "OwnCloud" install; add OwnCloud-Container**

See: https://doc.owncloud.com/server/next/admin_manual/installation/docker/

**Based on RKW basic install**

Required: https://github.com/rkw-kompetenzzentrum/RKW-Website-TYPO3-DDEV

**DO NOT USE :8080 because it's already in use (use something other instead like "8989")**
```bash
  docker run --rm --name oc-eval -d -p8989:8989 owncloud/server
```
http://localhost:8989

## 1. Extension usage for integrators

1. Create or use an existing folder in your TYPO3 Backend
2. Set up your local TypoScript like the following example:

```typo3_typoscript
plugin.tx_rkwcompetition {
    persistence {
        # The competition basic folder
        storagePid = 11923 
    }
    settings {
        # The general webPage loginPid
        loginPid = 10513
        # The side where you've place your competition plugin
        competitionPid = 2547
        # The register PID (could by a subpage of the competitionPid)
        registerPid = 11924 
        mandatoryFields {
            # set mandatory fields for the register form
            register = firstName, lastName 
            # additional mandatory fields which only trigger if groupWork is selected
            registerGroupWork = groupWorkInsurance, groupWorkAddPersons
        }
    }
}
```

### 1.1 Create basic records inside a folder of your choice

1. Create a ***FrontendUserGroup*** for FrontendUser ***Participants***
2. Create a ***FrontendUserGroup*** for FrontendUser ***JuryMember*** 
3. You will need at least ***one FrontendUser*** for the usage as ***Jury-Member*** to create a competition
4. Create "Sector"-Records ("Bereich") ***before*** creating a competition
5. Create your first "Competition"-Record ("Wettbewerb")

### 1.2 Frontend Part One: Competition & Register Plugins

1. Insert the plugin to show the competitions itself on your chosen page ("settings.competitionPid")
   1. Plugin Name: "RKW Competition: Wettbewerb [rkwcompetition_competition]"   
   2. Select a competition record inside the FlexForm of the plugin
2. Insert the register plugin to the other created page ("settings.registerPid")
   1. Plugin Name: "RKW Competition: Anmeldung [rkwcompetition_register]"

### 1.3 Frontend Part Two: Plugins for Participants & Jury-Member (Login Area)

1. Create new page "Mein Wettbewerb"
   1. Plugin Name: "RKW Competition: Teilnehmer (Login-Bereich)"
   2. Set the page access rights for specific FrontendUserGroup of ***Participants***
2. Create new page "Meine Jurytätigkeit"
   1. Plugin Name: "RKW Competition: Jury (Login-Bereich)"
   2. Set the page access rights for specific FrontendUserGroup of ***Jury-Member***

***

## 2. The Jury

Jury-Member are basically normal FrontendUsers with a certain FrontendUserGroup.

They can be added to a ***Competition*** as a ***candidate*** ("jury_member_candidate"). Through a Cronjob ("JuryNotifyCommand") they will get E-Mails with deadline information and invitation to accept the membership.

If a FrontendUser and Candidate is accepting the membership, the FrontendUser will be added as a ***confirmed jury member*** to the ***competition***.

***@toDo: Do we need a function for Admins to notify them if a jury member does not accept the invitation?***

***

## 3. Explore the Backend Module

The Backend Module is used by BackendUsers (also called "Admins") to take a first look the documents of the registrations.

The Admins have the ***task*** of ***accepting*** registrations in the first instance or ***rejecting*** them with reasons. This is an important part of the extension functionality.

Hint for initial usage: It is advisable to have ***at least created a registration*** before the backend module is assessed for ***test purpose***.

1. Click on "RKW Competition" inside "Web"-Area
2. A list of ***competitions*** shows up as a table. Select one by using the show link on the right
3. This leads to a list of ***registrations*** is shown. You can show into by using the show link
4. Now an Admin can get a ***detailed overview*** of the registration including a ***file download***
5. The Admin can determine to ***accept*** or to ***reject*** the registration

***

## 4. Cronjobs

This extension coming with a bunch of cronjobs the manage the several steps from initial registration until the assignment to the Jury-Member
* rkw_competition:incompleteUserRegistration
  * Sends notify mails to FrontendUser if they have not submitted their registration yet
* rkw_competition:removalDeadlineWarningAdmin
  * Only if a data removal date is set: Send a reminder to admins before an expired competition is about to be deleted together with the documents
* rkw_competition:juryNotify
  * Sends notify mails to Jury-Member when a register period ends (and their work starts)
  * Sends also reminder messages to Jury-Member which does not have accepted the Jury-Member-Terms yet
* rkw_competition:closingDay
  * Triggers on the closing day of the register period of a competition
  * Sends E-Mails to Admins with reference to the possibility of downloading all submitted documents in the Backend
  * Info (success; accepted to participation) E-Mail to participants with complete and verified data
  * Info (failed; excluded from competition) E-Mail to participants with incomplete or incorrect data
* rkw_competition:cleanup
  * Removes Competitions with their Registration records (+ with uploaded stuff) from HD

***

# 5. Workflow

A step by step journey with all components through the RkwCompetition after all configuration steps are done (steps 1-4)

HINT: If the tester has to do something manually which should be done automatically ("cronjobs"), this is displayed below as ***TASK***. If the system does something by its own, this will be displayed as ***TRIGGER***.

## 5.1 Initial Register Procedure (as a FrontendUser)
1. Go to the competition page (Plugin: rkwcompetition_competition; CompetitionController->showAction)
2. Click the link to the registration page (Plugin: rkwcompetition_register; RegisterController->newAction)
3. Fill out the form including all mandatory fields and send it off (RegisterController->createAction)
   1. ***TASK:*** Kick off cronjob "postmaster:send" manually
   2. ***TRIGGER:*** OptIn E-Mail is sent to the register E-Mail Address (RkwMailService->optInRequest)
4. Go to you E-Mail program open the E-Mail. Choose one of the both links:
   1. Decline: The registration will be removed (RegisterController->optInAction)
   2. Accept: The registration will be executed (RegisterController->optInAction)
      1. ***TASK:*** Kick off cronjob "postmaster:send" manually
      2. ***TRIGGER:*** Confirmation E-Mail is sent to FrontendUser (RkwMailService->confirmRegisterUser)
      3. ***TRIGGER:*** Notify E-Mail is sent to FrontendUser ("Upload") (RkwMailService->uploadDocumentsUser)
      4. ***TRIGGER:*** Notify E-Mail to Admin ("New Competition register") (RkwMailService->confirmRegisterAdmin)

## 5.2 Upload and Submit Registration (as a FrontendUser)
1. Login to "Mein RKW" with the E-Mail Address of the done registration (@see 5.1)
2. Go to the sub area "Mein Wettbewerb" (@see 1.3). You should see at least the competition you have registered to (ParticipantController->listAction)
3. Click on the button for file uploads ("Dateien hochladen") (UploadController->editAction)


