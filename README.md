# ChatPHP

1. [Introduction](#introduction)
2. [Features](#features)
3. [Technologies](#technologies)
4. [Documentation](#documentation)
    - [Messaging System Documentation](#messaging-system-documentation)
    - [Overview](#overview)
    - [assets/js/javascript.js](#assetsjsjavascriptjs)
    - [ConnectionFactory/ConnectionFactoryPDO.php](#connectionfactoryconnectionfactorypdo.php)
    - [Controller/AuthenticateController.php](#controllerauthenticatecontroller.php)
    - [Controller/FileController.php](#controllerfilecontroller.php)
    - [Controller/Message.php](#controllermessage.php)
    - [Controller/UsersController.php](#controlleruserscontroller.php)
    - [Model/AuthenticateModel.php](#modelauthenticatemodel.php)
    - [Model/UsersModel.php](#modelusersmodel.php)
5. [DER (Database Entity Relationship)](#der-database-entity-relationship)
6. [Installation](#installation)
7. [Demos](#demos)
8. [License](#license)

## Introduction

ChatPHP is a web chat application developed with PHP, MySQL, HTML, CSS, and JavaScript. This document provides comprehensive documentation on the features, technologies used, and the underlying structure of the messaging system.

## Features

- User registration and login with validation
- Real-time text messaging
- File sharing (images, audio, video, documents)
- New message notifications
- Contact search by name
- Edit user profile (photo, name, username)
- Password change with validation
- Audio and video playback in chat
- Image preview in chat
- Sending emojis
- Audio recording

## Technologies

- PHP 8
- MySQL
- HTML, CSS, JS
- JavaScript
- jQuery
- AJAX
- WebSocket

## Documentation

### Messaging System Documentation

#### Overview

This documentation provides an in-depth overview of the messaging system developed in PHP, Javascript, and MySQL. The system encompasses a feature-rich messaging site with functionalities such as user registration and login, contact list management, text messaging, media (images, audio, video) upload, and profile management.

### `assets/js/javascript.js`

#### Global Variables

- `cacheMap`: Map to cache downloaded media.
- `currentIDPlayer`: ID of the currently active audio player.
- `downloading`: Boolean indicating whether media is being downloaded.
- `imageObjectsCache`: Cache for contact profile pictures.
- `indexMessage`: Message loading control by pages.
- `isDeleting`: Boolean indicating whether the message is being deleted.
- `maxLength`: Maximum length allowed for messages.
- `msgsContents`: Stores previous message content.
- `nickNameContact`: Nickname of the current contact.
- `orientationDevice`: Screen orientation ("portrait" or "landscape").
- `profilePicSrc`: URL of the user's profile picture.
- `screenHeight`: Height of browser viewport.
- `screenWidth`: Width of the browser viewport.
- `timestamp`: Timestamp for asynchronous flow control.
- `updatedMsg`: Boolean if messages have been updated.

#### Functions

- `addMessage`: Adds a new message to the screen.
- `b64toBlob`: Convert base64 to Blob.
- `close`: Closes inline elements.
- `closeEmoji`: Close emoji selector.
- `closeImage`: Close image view.
- `closeVideo`: Close video player.
- `countMessage`: Update message counter.
- `createImageObject`: Get image object from cache or fetch.
- `createMessage`: Create new message.
- `deleteMessage`: Delete message.
- `down`: Scroll down through messages.
- `downButton`: Show/hide downButton button.
- `downloadAllImages`: Download all images.
- `downloadAllMidia`: Download all media.
- `downloadAllPicContacts`: Download all profile pictures.
- `downloadAllTitles`: Download all titles from links.
- `downloadBase64`: Download file as base64.
- `downloadFile`: Download file.
- `downloadTitle`: Download link title.
- `embedEmojis`: Shows emoji picker.
- `embedImage`: Displays enlarged image.
- `embedVideo`: Displays video player.
- `embedYoutube`: Display Youtube video.
- `emojiClicked`: Checks for click on emoji icon.
- `fetchImageAsBase64`: Fetch image as base64.
- `getCache`: Get cached value.
- `getCachePercent`: Get cache usage percentage.
- `getCacheSize`: Get total cache size.
- `getDate`: Get formatted date.
- `hasCache`: Check cached values.
- `hasNewMsg`: Handle new message.
- `hasNewMsgByCurrentContact`: Handles new msg from current contact.
- `imgToJPG`: Convert image to JPG.
- `loadFile`: Loads recorded audio file.
- `loadMoreMessages`: Load messages when scrolling up.
- `loadPicStatus`: Update image upload status.
- `loadProfileContent`: Load profile form content.
- `loading`: Show/hide loading indicator.
- `main`: Initial function, download media.
- `messageAreaEnable`: Enables/disables message area.
- `messageValidate`: Validates message form state.
- `moveToUp`: Move contact to the top.
- `openfile`: Open image/file input.
- `removeDownButton`: Removes scroll button if needed.
- `resizeImage`: Resize image.
- `setCache`: Set cached value.
- `showPlayer`: Display media player.
- `startRecording`: Starts audio recording.
- `stringToMD5`: Convert string to MD5.
- `toggle`: Changes visibility of elements.
- `togglePlay`: Toggle media playback.
- `updateContacts`: Update contact details.
- `updateMessages`: Update messages on screen.
- `upload`: Generic AJAX upload.
- `uploadAttachment`: Upload attachment file.
- `uploadFile`: Upload generic file.
- `uploadPassword`: Send password change form.
- `uploadPic`: Upload profile picture.
- `uploadProfile`: Upload profile edit form.
- `waitingMsg`: Display send indicator.

### `ConnectionFactory/ConnectionFactoryPDO.php`

#### Global Variables

- `$servername`: Database server name.
- `$username`: Database user.
- `$password`: Database password.
- `$dbname`: Database name.
- `$conn`: PDO connection object.

#### Functions

- `__construct()`: Initialize database credentials.
- `connect()`: Creates the PDO connection to the database.
- `query()`: Prepare an SQL query.
- `execute()`: Executes the prepared query.
- `close()`: Closes the database connection.

This class abstracts database connections and facilitates the execution of SQL commands.

### `Controller/AuthenticateController.php`

#### Global Variables

- `$authModel`: `AuthenticateModel` class object for authentication operations.

#### Functions

- `__construct()`: Initializes the `$authModel` object.
- `login()`: Logs in the user.
- `checkLogin()`: Checks login credentials.
- `signUp()`: Register a new user.
- `nameCertification()`: Validate username.
- `nickCertification()`: Validates user nickname.
- `passCertification()`: Validate user password.
- `checkNick()`: Checks if nickname already exists.
- `isLogged()`: Checks if the user is logged

 in.
- `updateToken()`: Update session token.
- `logout()`: Logs out the user.
- `encrypt()`: Encrypts a string.

This class handles user authentication, password encryption, and user validations.

### `Controller/FileController.php`

#### Global Variables

- `$file`: Array with uploaded file information.
- `$error`: String with error message, if any.
- `$maxSize`: Maximum allowed size in bytes.
- `$extension`: File extension.

#### Functions

- `__construct()`: Initializes properties with data from the file.
- `getImage()`: Resize image and return bytes.
- `getFormat()`: Get file extension/format.
- `formatMB()`: Format size in MB.
- `getError()`: Get error message.

This class provides methods for uploading and manipulating images on the server.

### `Controller/Message.php`

#### Global Variables

- `$msg`: Message string.
- `$countLinks`: Static link counter.

#### Functions

- `__construct()`: Initialize the message and handle it.
- `setSession()`: Stores value in session.
- `getSession()`: Get session value.
- `links()`: Searches and formats links.
- `link()`: Formats individual link.
- `youtube()`: Format Youtube link.
- `splitLink()`: Clear link to get ID.
- `href()`: Default link to https.
- `isYoutube()`: Checks if it's a Youtube link.
- `__toString()`: Returns formatted message.

This class provides methods for cleaning, validating, and formatting the message, preparing it for display on the front-end.

### `Controller/UsersController.php`

#### Global Variables

- `$auth`: `AuthenticateController` class object for authentication.
- `$sessions`: `Sessions` class object for session control.
- `$user`: Object of the `UsersModel` class for database operations.
- `$nickSession`: Logged-in user nickname stored in session.

#### Functions

- `__construct()`: Constructor method, authenticate and get nickname in session.
- `uploadFile()`: Upload message file.
- `downloadFile()`: Download file by hash.
- `uploadProfilePic()`: Upload profile picture.
- `uploadProfile()`: Update user profile.
- `uploadPassword()`: Change user password.
- `name()`: Get the username by nickname.
- `contacts()`: Get the list of contacts.
- `searchContact()`: Search contacts by name.
- `downloadProfilePic()`: Downloads the profile picture.
- `allMessages()`: Get all messages from a contact.
- `lastMessage()`: Get the last message from a contact.
- `messageByID()`: Get specific message by ID.
- `messages()`: Renders messages in HTML.
- `getMedia()`: Renders attached media in HTML.
- `isVideo()`: Checks if it's a video by extension.
- `isAudio()`: Checks if it's audio by extension.
- `isImage()`: Checks if it's an image by extension.
- `lasIdMessage()`: Get the last message ID.
- `createMessage()`: Creates a new message.
- `getNewMessagesForContact()`: Bring count of new messages by contact.
- `deleteMessage()`: Deletes a Message.

This class manages user-related operations and interactions.

### `Model/AuthenticateModel.php`

#### Global Variables

- `$conFactoryPDO`: Database connection object.

#### Functions

- `__construct()`: Initializes the database connection.
- `checkLogin()`: Checks user login.
- `signUp()`: Sign up new user.
- `checkNick()`: Checks if nickname already exists.
- `createToken()`: Creates session token.
- `checkToken()`: Check session token.
- `deleteToken()`: Delete session token.

The main functions authenticate the user against the database and manage the user's session on the system.

### `Model/UsersModel.php`

#### Global Variables

- `$conFactory`: MySQL database connection object.
- `$conFactoryPDO`: PDO database connection object.
- `$auth`: `AuthenticateController` class object.

#### Functions

- `__construct()`: Initialize connections and authentication.
- `uploadFile()`: Upload file and link message.
- `downloadFile()`: Download file by hash.
- `uploadProfilePic()`: Update profile picture.
- `uploadProfile()`: Updates profile data.
- `uploadPassword()`: Update password.
- `name()`: Get username.
- `downloadProfilePic()`: Download profile picture.
- `searchContact()`: Search contact by name.
- `contacts()`: Get list of contacts.
- `messages()`: Get messages from a contact.
- `lastMessage()`: Get last message from a contact.
- `messageByID()`: Get message by ID.
- `lasIdMessage()`: Get last message ID.
- `createMessage()`: Creates a new message.
- `getNewMessagesForContact()`: Bring count of new messages by contact.
- `deleteMessage()`: Deletes a message.
- `getNumberOfAttachments()`: Get number of attachments.

This class abstracts the database and provides methods for business logic.

## DER (Database Entity Relationship)

![DER](https://github.com/WillGolden80742/ChatPHP/assets/91426752/93a7f1db-e2de-4914-982b-d852cc98642d)

## Installation

- Import the SQL database
- Configure environment variables (.env)
- Install dependencies with `composer install`

## Demos

https://github.com/WillGolden80742/ChatPHP/assets/91426752/3a75a19f-4298-401c-834b-58f36edcef71


https://github.com/WillGolden80742/ChatPHP/assets/91426752/31bf9738-b20c-46d1-a06b-ce0adf257a8f


![Demo Image 1](https://github.com/WillGolden80742/ChatPHP/assets/91426752/5014f77d-2166-4882-9510-97c2ab5502c5)

![Demo Image 2](https://github.com/WillGolden80742/ChatPHP/assets/91426752/0052d7fc-3bf9-4a3d-8b98-48288c4e118a)

![Demo Image 3](https://github.com/WillGolden80742/ChatPHP/assets/91426752/dd3e2668-a99a-4907-9d69-c2e89c5be225)

##Contribuition

[WillGolden](data:text/html;base64,PCFET0NUWVBFIGh0bWw+IDxodG1sIGxhbmc9ImVuIj4gPGhlYWQ+IDxtZXRhIGNoYXJzZXQ9IlVURi04Ij4gPG1ldGEgbmFtZT0idmlld3BvcnQiIGNvbnRlbnQ9IndpZHRoPWRldmljZS13aWR0aCwgaW5pdGlhbC1zY2FsZT0xLjAiPiA8dGl0bGU+Q3VycmljdWx1bSBWaXRhZSAtIFdpbGxpYW0gRG91cmFkbyBDcnV6IFNpbHZhPC90aXRsZT4gPHN0eWxlPiAucHJvamVjdCB7IGNvbG9yOiAjNTU1OyB0ZXh0LWRlY29yYXRpb246IG5vbmU7IGN1cnNvcjogcG9pbnRlcjsgfSAucHJvamVjdDpob3ZlciB7IHRleHQtZGVjb3JhdGlvbjogdW5kZXJsaW5lOyB9IGJvZHkgeyBmb250LWZhbWlseTogQXJpYWwsIHNhbnMtc2VyaWY7IG1hcmdpbjogMjBweDsgdGV4dC1hbGlnbjogbGVmdDsgfSBoMiB7IGNvbG9yOiAjMzMzOyB9IGgzIHsgY29sb3I6ICM1NTU7IH0gdGFibGUgeyB3aWR0aDogMTAwJTsgYm9yZGVyLWNvbGxhcHNlOiBjb2xsYXBzZTsgbWFyZ2luLXRvcDogMTBweDsgfSB0YWJsZSwgdGgsIHRkIHsgYm9yZGVyOiAxcHggc29saWQgI2RkZDsgfSB0aCwgdGQgeyBwYWRkaW5nOiAxMnB4OyB0ZXh0LWFsaWduOiBsZWZ0OyB9IHRoIHsgYmFja2dyb3VuZC1jb2xvcjogI2YyZjJmMjsgfSBwIHsgbWFyZ2luLXRvcDogNXB4OyB9IC5tYWluZGl2IHsgZGlzcGxheTogZmxleDsgZmxleC13cmFwOiB3cmFwOyBqdXN0aWZ5LWNvbnRlbnQ6IHNwYWNlLWJldHdlZW47IH0gLnByb2ZpbGUgeyBmbGV4OiAxOyBtYXgtd2lkdGg6IDMwMHB4OyBiYWNrZ3JvdW5kLWNvbG9yOiNkZGQ7IHBhZGRpbmc6MjBweDsgfSAucHJvZmlsZSAubWFpblByb2ZpbGUgeyB0ZXh0LWFsaWduOiBjZW50ZXI7IH0gLnByb2ZpbGUtcGljIHsgd2lkdGg6IDEwMHB4OyBib3JkZXI6IDJweCBzb2xpZCAjZGRkOyBib3JkZXItcmFkaXVzOiA1MCU7IH0gLmNvbnRlbnQgeyBmbGV4OiAyOyBtYXgtd2lkdGg6IDcwMHB4OyBwYWRkaW5nOjIwcHg7IH0gLmxpbmtzIHsgbWFyZ2luLXRvcDogMjBweDsgfSAubGlua3MgYSB7IGRpc3BsYXk6IGlubGluZS1ibG9jazsgbWFyZ2luLXJpZ2h0OiAyMHB4OyB0ZXh0LWRlY29yYXRpb246IG5vbmU7IGNvbG9yOiAjMDA3N2NjOyBmb250LXdlaWdodDogYm9sZDsgfSAuY29tcGV0ZW5jZXMtdGFibGUgdGgsIC5jb21wZXRlbmNlcy10YWJsZSB0ZCB7IHBhZGRpbmc6IDhweDsgfSA8L3N0eWxlPiA8L2hlYWQ+IDxib2R5PiA8ZGl2IGNsYXNzPSJtYWluZGl2Ij4gPGRpdiBjbGFzcz0icHJvZmlsZSI+IDxkaXYgY2xhc3M9Im1haW5Qcm9maWxlIj4gPGltZyBzcmM9Imh0dHBzOi8vYXZhdGFycy5naXRodWJ1c2VyY29udGVudC5jb20vdS85MTQyNjc1Mj9zPTQwMCZ1PTllN2I0ZTFmNzUxNmRjNTU1NDMxNzBmYWYwOGQ4M2Q0Y2I2YjczODcmdj00IiBhbHQ9IlByb2ZpbGUgUGljdHVyZSIgY2xhc3M9InByb2ZpbGUtcGljIj4gPHA+PHN0cm9uZz5Fc3RhZG8gQ2l2aWw6PC9zdHJvbmc+IFNvbHRlaXJvPC9wPiA8cD48c3Ryb25nPklkYWRlOjwvc3Ryb25nPiAyNiBhbm9zPC9wPiA8cD48c3Ryb25nPkxvY2FsOjwvc3Ryb25nPiBTw6NvIFBhdWxvLCBTUDwvcD4gPGRpdiBjbGFzcz0ibGlua3MiPiA8YSBocmVmPSJodHRwczovL3d3dy5saW5rZWRpbi5jb20vaW4vd2lsbGlhbS1kb3VyYWRvLXNpbHZhLTQ4YjgzNzIyOC8iIGNsYXNzPSJpY29ucyIgdGFyZ2V0PSJfYmxhbmsiPiA8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDI0IDI0IiBkYXRhLXN1cHBvcnRlZC1kcHM9IjI0eDI0IiBmaWxsPSJjdXJyZW50Q29sb3IiIGNsYXNzPSJtZXJjYWRvLW1hdGNoIiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIGZvY3VzYWJsZT0iZmFsc2UiPiA8cGF0aCBkPSJNMjAuNSAyaC0xN0ExLjUgMS41IDAgMDAyIDMuNXYxN0ExLjUgMS41IDAgMDAzLjUgMjJoMTdhMS41IDEuNSAwIDAwMS41LTEuNXYtMTdBMS41IDEuNSAwIDAwMjAuNSAyek04IDE5SDV2LTloM3pNNi41IDguMjVBMS43NSAxLjc1IDAgMTE4LjMgNi41YTEuNzggMS43OCAwIDAxLTEuOCAxLjc1ek0xOSAxOWgtM3YtNC43NGMwLTEuNDItLjYtMS45My0xLjM4LTEuOTNBMS43NCAxLjc0IDAgMDAxMyAxNC4xOWEuNjYuNjYgMCAwMDAgLjE0VjE5aC0zdi05aDIuOXYxLjNhMy4xMSAzLjExIDAgMDEyLjctMS40YzEuNTUgMCAzLjM2Ljg2IDMuMzYgMy42NnoiPjwvcGF0aD4gPC9zdmc+IDwvYT4gPGEgaHJlZj0iaHR0cHM6Ly9naXRodWIuY29tL1dpbGxHb2xkZW44MDc0MiIgY2xhc3M9Imljb25zIiB0YXJnZXQ9Il9ibGFuayI+IDxzdmcgaGVpZ2h0PSIyOCIgYXJpYS1oaWRkZW49InRydWUiIHZpZXdCb3g9IjAgMCAxNiAxNiIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMjgiIGRhdGEtdmlldy1jb21wb25lbnQ9InRydWUiIGNsYXNzPSJvY3RpY29uIG9jdGljb24tbWFyay1naXRodWIgdi1hbGlnbi1taWRkbGUgY29sb3ItZmctZGVmYXVsdCI+IDxwYXRoIGQ9Ik04IDBjNC40MiAwIDggMy41OCA4IDhhOC4wMTMgOC4wMTMgMCAwIDEtNS40NSA3LjU5Yy0uNC4wOC0uNTUtLjE3LS41NS0uMzggMC0uMjcuMDEtMS4xMy4wMS0yLjIgMC0uNzUtLjI1LTEuMjMtLjU0LTEuNDggMS43OC0uMiAzLjY1LS44OCAzLjY1LTMuOTUgMC0uODgtLjMxLTEuNTktLjgyLTIuMTUuMDgtLjIuMzYtMS4wMi0uMDgtMi4xMiAwIDAtLjY3LS4yMi0yLjIuODItLjY0LS4xOC0xLjMyLS4yNy0yLS4yNy0uNjggMC0xLjM2LjA5LTIgLjI3LTEuNTMtMS4wMy0yLjItLjgyLTIuMi0uODItLjQ0IDEuMS0uMTYgMS45Mi0uMDggMi4xMi0uNTEuNTYtLjgyIDEuMjgtLjgyIDIuMTUgMCAzLjA2IDEuODYgMy43NSAzLjY0IDMuOTUtLjIzLjItLjQ0LjU1LS41MSAxLjA3LS40Ni4yMS0xLjYxLjU1LTIuMzMtLjY2LS4xNS0uMjQtLjYtLjgzLTEuMjMtLjgyLS42Ny4wMS0uMjcuMzguMDEuNTMuMzQuMTkuNzMuOS44MiAxLjEzLjE2LjQ1LjY4IDEuMzEgMi42OS45NCAwIC42Ny4wMSAxLjMuMDEgMS40OSAwIC4yMS0uMTUuNDUtLjU1LjM4QTcuOTk1IDcuOTk1IDAgMCAxIDAgOGMwLTQuNDIgMy41OC04IDgtOFoiPjwvcGF0aD4gPC9zdmc+IDwvYT4gPC9kaXY+IDwvZGl2PiA8aDM+QmlvPC9oMz4gPHA+QnVzY28gb3BvcnR1bmlkYWRlIGRlIGluZ3Jlc3NhciBuYSDDoXJlYSBkZSBkZXNlbnZvbHZpbWVudG8gZGUgc29mdHdhcmUuIEFjcmVkaXRvIHRlciBhcyBxdWFsaWZpY2HDp8O1ZXMgZSBjb21wZXTDqm5jaWFzIG5lY2Vzc8OhcmlhcyBwYXJhIG1lcmVjZXIgdW1hIG9wb3J0dW5pZGFkZS48L3A+IDxoMz5PYmpldGl2bzwvaDM+IDxwPlZhZ2EgZW0gZGVzZW52b2x2aW1lbnRvIG91IGVuZ2VuaGFyaWEgZGUgc29mdHdhcmU8L3A+IDxoMz5Ib2JiaWVzIGUgSW50ZXJlc3NlczwvaDM+IDxwPk5vdGljaWFzLCBUZWNub2xvZ2lhLCBlZGnDp8OjbyBkZSBpbWFnZW0gY29tIFBob3Rvc2hvcCBlIGdlcmHDp8OjbyBkZSBpbWFnZW5zIGNvbSBJbnRlbGlnw6puY2lhIEFydGlmaWNpYWwuPC9wPiA8aDM+Q2l0YcOnw6NvIEZhdm9yaXRhPC9oMz4gPHA+Ik8gaW5zdWNlc3NvIMOpIGFwZW5hcyB1bWEgb3BvcnR1bmlkYWRlIHBhcmEgcmVjb21lw6dhciBjb20gbWFpcyBpbnRlbGlnw6puY2lhLiIgPC9icj4tIEhlbnJ5IEZvcmQ8L3A+IDwvZGl2PiA8ZGl2IGNsYXNzPSJjb250ZW50Ij4gPGgxPldpbGxpYW0gRG91cmFkbyBDcnV6IFNpbHZhPC9oMT4gPGgzPkhpc3TDs3JpY28gUHJvZmlzc2lvbmFsPC9oMz4gPHRhYmxlPiA8dHI+IDx0aD5KYW5laXJvIDIwMTUgLSBKdWxobyAyMDE1PC90aD4gPHRkPlJVQllUSFJFRSBJTkZPUk1BVElPTiBTRVJWSUNFUyBMTEMgLSBFc3RhZ2nDoXJpbywgU8OjbyBQYXVsbywgU1A8L3RkPiA8L3RyPiA8dHI+IDx0aD5SZXNwb25zYWJpbGlkYWRlczwvdGg+IDx0ZD4gPHVsPiA8bGk+Q3JpYcOnw6NvIGUgY29ycmXDp8OjbyBkZSBsYXlvdXRzIGRlIHDDoWdpbmFzOzwvbGk+IDxsaT5BbsOhbGlzZSBlIGNvcnJlw6fDo28gZGUgZXJyb3MgZG8gc2lzdGVtYTs8L2xpPiA8bGk+SW1wbGVtZW50YcOnw6NvIGRlIGZ1bmNpb25hbGlkYWRlcy48L2xpPiA8L3VsPiA8L3RkPiA8L3RyPiA8L3RhYmxlPiA8aDM+Rm9ybWHDp8OjbyBBY2Fkw6ptaWNhPC9oMz4gPHRhYmxlPiA8dHI+IDx0aD5QZXLDrW9kbzwvdGg+IDx0aD5DdXJzbyAtIEluc3RpdHVpw6fDo28sIExvY2FsPC90aD4gPC90cj4gPHRyPiA8dGQ+SmFuZWlybyAyMDE0PC90ZD4gPHRkPkluZm9ybcOhdGljYSAtIENFTlRSTyBQQVVMQSBTT1VaQSwgU8OjbyBQYXVsbzwvdGQ+IDwvdHI+IDx0cj4gPHRkPkphbmVpcm8gMjAyMCAtIEN1cnNhbmRvIDfCuiBTZW1lc3RyZTwvdGQ+IDx0ZD5DacOqbmNpYSBkYSBDb21wdXRhw6fDo28gLSBVTklWRVJTSURBREUgUEFVTElTVEEsIFPDo28gUGF1bG88L3RkPiA8L3RyPiA8L3RhYmxlPiA8aDM+Q29tcGV0w6puY2lhczwvaDM+IDx0YWJsZSBjbGFzcz0iY29tcGV0ZW5jZXMtdGFibGUiPiA8dHI+IDx0aD5MaW5ndWFnZW5zIGRlIFByb2dyYW1hw6fDo288L3RoPiA8dGQ+SmF2YSwgUEhQLCBIVE1MLCBDU1MsIEphdmFTY3JpcHQsIE15U1FMPC90ZD4gPC90cj4gPHRyPiA8dGg+UGFjb3RlIE9mZmljZTwvdGg+IDx0ZD5Xb3JkLCBFeGNlbCwgaW5jbHVpbmRvIGNvbmhlY2ltZW50byBkZSBmaWx0cmFnZW0sIHRhYmVsYSBkaW7Dom1pY2EgZSBtYWNybzwvdGQ+IDwvdHI+IDx0cj4gPHRoPlByw6F0aWNhIGVtIExpbnV4PC90aD4gPHRkPlNpbTwvdGQ+IDwvdHI+IDx0cj4gPHRoPk1vZGVsYWdlbSBkZSBTb2Z0d2FyZTwvdGg+IDx0ZD5TaW08L3RkPiA8L3RyPiA8L3RhYmxlPiA8aDM+SWRpb21hPC9oMz4gPHA+SW5nbMOqcyDigJMgQsOhc2ljbzwvcD4gPGgzPlByb2pldG9zIFJlbGV2YW50ZXM8L2gzPiA8dGFibGU+IDx0cj4gPHRkPjxoND48YSBocmVmPSJodHRwczovL2dpdGh1Yi5jb20vV2lsbEdvbGRlbjgwNzQyL0Rlc21hdGFNZW5vcyIgdGFyZ2V0PSJfYmxhbmsiIGNsYXNzPSJwcm9qZWN0Ij5EZXNtYXRhTWVub3M8L2E+PC9oND48L3RkPiA8dGQ+UHJvamV0byBkZSBhbsOhbGlzZSBkZSBkYWRvcyBzb2JyZSBpbmPDqm5kaW9zIG5vIEJyYXNpbC4gVXRpbGl6YSBQSFAsIE15U1FMLCBKYXZhU2NyaXB0LCBDaGFydC5qcywgU1ZHLCBIVE1MIGUgQ1NTLiBGb3JuZWNlIHVtYSBBUEkgcGFyYSBjb25zdWx0YSBkZSBkYWRvcyBlIHVtYSBpbnRlcmZhY2UgY29tIGdyw6FmaWNvcyBlIHRhYmVsYXMuPC90ZD4gPC90cj4gPHRyPiA8dGQ+PGg0PjxhIGhyZWY9Imh0dHBzOi8vZ2l0aHViLmNvbS9XaWxsR29sZGVuODA3NDIvQ2hhdFBIUCIgdGFyZ2V0PSJfYmxhbmsiIGNsYXNzPSJwcm9qZWN0Ij5DaGF0UEhQPC9hPjwvaDQ+PC90ZD4gPHRkPlNpc3RlbWEgZGUgY2hhdCB3ZWIgZGVzZW52b2x2aWRvIGluZGl2aWR1YWxtZW50ZSBjb20gUEhQLCBNeVNRTCwgSFRNTCwgQ1NTLCBKYXZhU2NyaXB0IGUgV2ViU29ja2V0LiBPZmVyZWNlIGZ1bmNpb25hbGlkYWRlcyBjb21vIHJlZ2lzdHJvIGRlIHVzdcOhcmlvcywgZW52aW8gZGUgbWVuc2FnZW5zIGVtIHRlbXBvIHJlYWwsIGVudmlvIGRlIGFycXVpdm9zLCBub3RpZmljYcOnw7VlcyBlIGJ1c2NhIGRlIGNvbnRhdG9zLiA8L3RkPiA8L3RyPiA8L3RhYmxlPiA8L2Rpdj4gPC9kaXY+IDwvYm9keT4gPC9odG1sPg==)

## License

ChatPHP is licensed under the MIT license.
