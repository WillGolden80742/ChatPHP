# ChatPHP 

ChatPHP is a web chat developed with PHP, MySQL, HTML, CSS and JavaScript.

## Features

- User registration and login with validation

- Send text messages in real time  

- Send files like images, audio, video and documents

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

# Documentation

## Messaging System Documentation

## Overview

This documentation provides an in-depth overview of the messaging system developed in PHP, Javascript, and MySQL. The system encompasses a feature-rich messaging site with functionalities such as user registration and login, contact list management, text messaging, media (images, audio, video) upload, and profile management.

The core functionalities are encapsulated in the following classes and files:

### `assets/js/javascript.js`

#### Global Variables

- `audioTime`: Map for storing audio playback times.
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
- `downButton`: Show/hide scroll button.
- `downloadAllAudios`: Download all audios.
- `downloadAllImages`: Download all images.
- `downloadAllMidia`: Download all media.
- `downloadAllPicContacts`: Download all profile pictures.
- `downloadAllTitles`: Download all titles from links.
- `downloadBase64`: Download file as base64.
- `downloadFile`: Download file.
- `downloadMedia`: Download specific media.
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
- `isLogged()`: Checks if the user is logged in.
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
-

 `$user`: Object of the `UsersModel` class for database operations.
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

# DER 

![DER](https://github.com/WillGolden80742/ChatPHP/assets/91426752/93a7f1db-e2de-4914-982b-d852cc98642d)

## Installation

- Import the SQL database

- Configure environment variables (.env)

- Install dependencies with `composer install`

## Demos

https://github.com/WillGolden80742/ChatPHP/assets/91426752/80863b05-3f7b-4618-ba58-45366a515df3

https://github.com/WillGolden80742/ChatPHP/assets/91426752/1e01b925-5ca7-47db-998a-04c68f13b2a9

![image](https://github.com/WillGolden80742/ChatPHP/assets/91426752/5014f77d-2166-4882-9510-97c2ab5502c5)

![image](https://github.com/WillGolden80742/ChatPHP/assets/91426752/0052d7fc-3bf9-4a3d-8b98-48288c4e118a)

![image](https://github.com/WillGolden80742/ChatPHP/assets/91426752/dd3e2668-a99a-4907-9d69-c2e89c5be225)

## License

ChatPHP is licensed under the MIT license.
