var scrollPosition = 0;
var msgsContents = "";
var profilePicSrc;
var orientationDevice = "landscape";
var timestamp = new Date().getTime();
var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

main();
async function main() {
  try {
    await downloadAllMidia();
  } catch (erro) {
    console.error(erro);
  }
  function handleScreenResolutionChange() {

    screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

    if (screenWidth > screenHeight && orientationDevice == "portrait") {
      if (window.location.href.includes("index.php") && window.location.href.includes("messages.php"))
        toggle(true, true);
      orientationDevice = "landscape";
    } else if (screenHeight > screenWidth) {
      orientationDevice = "portrait";
    }
    const down = document.querySelector("#down");
    if (down) {
      const width = document.querySelector('#down img').offsetWidth / 2;
      const messagesElement = document.querySelector("#messages");
      down.style.top = (messagesElement.offsetTop + width) + "px";
      down.style.left = (messagesElement.offsetLeft + (messagesElement.offsetWidth / 2)) - width + "px";
    }
    const loading = document.querySelector("#loading");
    if (loading) {
      const width = document.querySelector('#loading img').offsetWidth / 2;
      const messagesElement = document.querySelector("#messages");
      loading.style.top = (messagesElement.offsetTop + width) + "px";
      loading.style.left = (messagesElement.offsetLeft + (messagesElement.offsetWidth / 2)) - width + "px";
    }
  }
  window.addEventListener('resize', handleScreenResolutionChange);
}


function openfile(value) {
  if (profilePicSrc == null) {
    if (value == 'file') {
      if (document.querySelector('.msg')) {
        document.getElementById(value).click();
      }
    } else {
      document.getElementById(value).click();
    }
  } else {
    document.getElementById('stylePic').innerHTML += ".salvar {display:none;}";
    loadPicStatus(false);
  }
}


function loadPicStatus(value, keepPic = false) {
  if (typeof profilePic !== 'undefined') {
    if (value) {
      profilePic.src = "Images/remove.png";
      document.querySelector(".salvar").style.display = "block";
      profilePicSrc = profilePic.style.backgroundImage;
    } else {
      profilePic.src = "Images/edit.png";
      document.querySelector(".salvar").style.display = "none";
      if (keepPic) {
        profilePic = document.getElementById("profilePic");
        profilePicSrc = profilePic.style.backgroundImage;
      } else {
        profilePic.style.backgroundImage = profilePicSrc;
      }
      profilePicSrc = null;
    }
  }
}

function handlePhotoUpload(event) {
  const fileInput = event.target;
  const file = fileInput.files[0];

  if (file) {
    const reader = new FileReader();

    reader.onload = function (e) {
      const imageSrc = e.target.result;
      const profilePic = document.getElementById("profilePic");
      profilePic.style.backgroundImage = `url(${imageSrc})`;
    };

    reader.readAsDataURL(file);
    loadPicStatus(true);
  }
}

function uploadPic() {
  var arquivoInput = document.getElementById('editProfilePic');
  var arquivo = arquivoInput.files[0];
  var formData = new FormData();

  if (arquivo.type === 'image/gif' || arquivo.type === 'image/png' || arquivo.type === 'image/webp') {
    imgToJPG(arquivo, 'profilepic.jpg', function (file) {
      formData.append('pic', file);
      formData.append('action', 'uploadPic');
      uploadFile('actions.php', formData);
    });
  } else {
    formData.append('pic', arquivo);
    formData.append('action', 'uploadPic');
    uploadFile('actions.php', formData);
  }
}

var editProfileMessage = "";

function uploadProfile() {
  var name = document.querySelector("#profileTab input[name=name]").value;
  var nick = document.querySelector("#profileTab input[name=nick]").value;
  var pass = document.querySelector("#profileTab input[name=pass]").value;

  var formData = new FormData();
  formData.append('name', name);
  formData.append('nick', nick);
  formData.append('pass', pass);
  formData.append('action', 'uploadProfile');

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function (content) {
    if (this.readyState == 4 && this.status == 200) {
      loadProfileContent(); // Atualiza o conteúdo após enviar o formulário
      editProfileMessage = this.responseText;
    }
  };

  xhttp.open("POST", "actions.php", true);
  xhttp.send(formData);
}


function uploadPassword() {
  var currentPass = document.querySelector("#passwordTab input[name=currentPass]").value;
  var newPass = document.querySelector("#passwordTab input[name=pass]").value;
  var passConfirmation = document.querySelector("#passwordTab input[name=passConfirmation]").value;

  var formData = new FormData();
  formData.append('currentPass', currentPass);
  formData.append('pass', newPass);
  formData.append('passConfirmation', passConfirmation);
  formData.append('action', 'uploadPassword');

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      loadProfileContent(); // Atualiza o conteúdo após enviar o formulário
      editProfileMessage = this.responseText;
    }
  };
  xhttp.open("POST", "actions.php", true);
  xhttp.send(formData);
}

function loadProfileContent() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      var profileContent = this.responseText + editProfileMessage;
      document.getElementById("profileContent").innerHTML = profileContent;
      if (editProfileMessage.length > 0) {
        setTimeout(function () {
          removerStatusMsg();
        }, 2000);
      }
    }
  };
  xhttp.open("GET", "profileEditForm.php", true);
  xhttp.send();
}

function removerStatusMsg() {
  var statusMsgElement = document.querySelector('.statusMsg');
  if (statusMsgElement) {
    statusMsgElement.remove();
  }
}

function imgToJPG(inputFile, fileName, callback) {
  var reader = new FileReader();
  reader.onload = function (event) {
    var img = new Image();
    img.onload = function () {
      var canvas = document.createElement('canvas');
      canvas.width = img.width;
      canvas.height = img.height;
      var context = canvas.getContext('2d');
      context.drawImage(img, 0, 0);
      canvas.toBlob(function (blob) {
        var file = new File([blob], fileName, { type: 'image/jpeg' });
        callback(file);
      }, 'image/jpeg', 0.8);
    };
    img.src = event.target.result;
  };
  reader.readAsDataURL(inputFile);
}

function resizeImage(file, maxWidth, callback) {
  var reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = function (event) {
    var img = new Image();
    img.src = event.target.result;
    img.onload = function () {
      var width = img.width;
      var height = img.height;

      if (width > maxWidth) {
        height *= maxWidth / width;
        width = maxWidth;
      }

      var canvas = document.createElement('canvas');
      canvas.width = width;
      canvas.height = height;

      var ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, width, height);
      canvas.toBlob(function (blob) {
        var resizedFile = new File([blob], file.name, { type: file.type });
        callback(resizedFile);
      }, file.type);
    };
  };
}


function upload(url, formData, successCallback, errorCallback) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url);
  xhr.onload = function () {
    if (xhr.status === 200) {
      if (xhr.responseText.length > 16) {
        alert(xhr.responseText);
      } else {
        successCallback();
      }
      sendSocket("create_message:msg" + xhr.responseText);
    } else {
      errorCallback(xhr.responseText);
    }

  };
  xhr.send(formData);
}

function uploadAttachment(url, formData) {
  upload(url, formData, function (response) {
    loading(false);
    updateMessages();
    var attachmentDiv = document.getElementById('attachment');
    attachmentDiv.style.backgroundColor = "";
  }, function (errorText) {
    console.error(errorText);
  });
}

function uploadFile(url, formData) {
  upload(url, formData, function () {
    loadPicStatus(false, true);
  }, function (errorText) {
    console.error(errorText);
  });
}

function down() {
  const messagesElement = document.getElementById("messages");
  if (messagesElement) {
    messagesElement.scrollTo(0, messagesElement.scrollHeight);
    downButton(false);
  }
}

function removeDownButton() {
  if (getScrollPercentage() >= 90) {
    downButton(false);
  } else if (getScrollPercentage() === 0) {
    loadMoreMessages();
  }
}

let indexMessage = 1;

function loadMoreMessages() {
  if (indexMessage === 0) return;

  const messagesDiv = document.querySelector('#messages');
  const msg = document.querySelector('.msg');
  if (!msg) return;

  const formData = new FormData();
  formData.append('action', 'messageByPag');
  formData.append('nickNameContact', nickNameContact);
  formData.append('pagIndex', ++indexMessage);
  loadingButton(true);
  fetch('actions.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(result => {
      if (result == "") {
        indexMessage = 0;
        const noMoreMessagesText = "Você já viu todas mensagens de @" + nickNameContact;
        const h3Element = document.createElement('h3');
        const centerElement = document.createElement('div');
        const textNode = document.createTextNode(noMoreMessagesText);
        centerElement.classList.add('center');
        centerElement.appendChild(textNode);
        h3Element.appendChild(centerElement);
        messagesDiv.insertBefore(h3Element, messagesDiv.firstChild);
      }
      msgsContents = result + msgsContents;

      const msgElements = document.querySelectorAll(".msg");
      if (msgElements.length) {
        getAudioTimes();
        const idLastMsg = document.querySelector('.msg').id;
        messagesDiv.insertAdjacentHTML('afterbegin', result);
        loadingButton(false);
        timestamp = new Date().getTime();
        downloadAllMidia();
        adjustScrollPosition(idLastMsg);
      }
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
    });
}

function adjustScrollPosition(value) {
  var messagesDiv = document.querySelector('.messages');
  var childDiv = document.getElementById(value);
  var offsetTop = childDiv.offsetTop - 200;
  messagesDiv.scrollTop = offsetTop;
}

var isDeleting = false;

async function deleteMessage(id) {
  if (!isDeleting && confirm("Tem certeza de que deseja apagar esta mensagem?")) {
    isDeleting = true;
    sendSocket("delete_message:msg" + id);
    document.getElementById("msg" + id).remove();
    loading(true);

    try {
      await $.ajax({
        url: 'actions.php',
        method: 'POST',
        data: { action: 'deleteMessage', id: id, nickNameContact: nickNameContact },
        dataType: 'json'
      });

      isDeleting = false;
      loading(false);
    } catch (error) {
      console.error(error);
      isDeleting = false;
      loading(false);
    }
  }
}


function getDate() {
  currentDate = new Date();
  currentDate = currentDate.toLocaleString('pt-BR');
  currentDate = currentDate.split(" ")[1];
  currentDate = currentDate.split(":")[0] + ":" + currentDate.split(":")[1];
  return currentDate;
}

var downloading = false;

async function showPlayer(hash, event) {
  if (!downloading) {
    try {
      downloading = true;
      var videoDiv = event.target.querySelector("img");

      if (videoDiv) {
        videoDiv.style.backgroundImage = 'url(Images/loading.gif)';
      }

      const parts = hash.split('.');
      const format = parts[parts.length - 1].toLowerCase();

      if (cacheMap.has(hash)) {
        if (videoDiv) {
          videoDiv.style.backgroundImage = 'url(Images/video.svg)';
        }

        var url = cacheMap.get(hash);
        embedVideo(url, url);

        downloading = false;
      } else {
        const dados = await downloadBase64(hash);
        const contentBlob = b64toBlob(dados, type(format) + "/" + format);
        const urlContent = URL.createObjectURL(contentBlob);

        if (videoDiv) {
          videoDiv.style.backgroundImage = 'url(Images/video.svg)';
        }

        embedVideo(urlContent, urlContent);
        cacheMap.set(hash, urlContent);

        downloading = false;
      }
    } catch (error) {
      console.error(error);
      // Trate o erro aqui, se necessário
    }
  }
}


function downloadFile(nomeHash, nome) {
  if (cacheMap.has(nomeHash)) {
    var downloadLink = document.createElement('a');
    downloadLink.href = cacheMap.get(nomeHash);
    downloadLink.download = nome;
    downloadLink.click();
  } else {
    var xhr = new XMLHttpRequest();
    var url = 'actions.php';
    var formData = new FormData();
    formData.append('hashName', nomeHash);
    formData.append('action', 'downloadFile');

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var base64Data = xhr.responseText;
        var byteCharacters = atob(base64Data);
        var byteNumbers = new Array(byteCharacters.length);

        for (var i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);
        var blob = new Blob([byteArray], { type: 'application/octet-stream' });

        // Cria um link para download e simula o clique nele
        var downloadLink = document.createElement('a');
        downloadLink.href = window.URL.createObjectURL(blob);
        downloadLink.download = nome;
        downloadLink.click();
      }
    };

    xhr.open('POST', url, true);
    xhr.send(formData);
  }
}



function b64toBlob(b64Data, contentType) {
  contentType = contentType || '';
  var sliceSize = 512;
  var byteCharacters = atob(b64Data);
  var byteArrays = [];

  for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    var slice = byteCharacters.slice(offset, offset + sliceSize);

    var byteNumbers = new Array(slice.length);
    for (var i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }

    var byteArray = new Uint8Array(byteNumbers);
    byteArrays.push(byteArray);
  }

  var blob = new Blob(byteArrays, { type: contentType });
  return blob;
}

async function downloadBase64(nomeHash) {
  try {
    const formData = new FormData();
    formData.append('hashName', nomeHash);
    formData.append('action', 'downloadFile');

    const dados = await $.ajax({
      url: 'actions.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'text'
    });

    return dados;
  } catch (error) {
    throw error;
  }
}

var currentIDPlayer = "";

async function togglePlay(hash, event) {
  var playButton;
  var playerMidia;

  if (document.querySelector('.embed-video')) {
    playerMidia = document.querySelector('.embed-video');
    playButton = document.querySelector('.play-button');
  } else {
    if (event) {
      playButton = event.target;
      playerMidia = playButton.querySelector('audio');
    } else {
      playButton = document.getElementById(hash).parentNode;
      playerMidia = document.getElementById(hash);
    }
  }

  var progressBar = playButton.closest('.player').querySelector('.progress-bar');
  var progress = playButton.closest('.player').querySelector('.progress-bar .progress');
  var currentTimeElement = playButton.closest('.player').querySelector('.time .current-time');
  var durationElement = playButton.closest('.player').querySelector('.time .duration');

  const playIcon = "url('Images/Player/play-button.svg')";
  const pauseIcon = "url('Images/Player/pause-button.svg')";

  if (currentIDPlayer !== hash) {
    document.querySelectorAll(".play-button").forEach(function (playButton) {
      playButton.style.backgroundImage = "url('Images/Player/play-button.svg')";
    });
    document.querySelectorAll('.audioPlayer').forEach(function (audio) {
      audio.pause();
    });
    playButton.style.backgroundImage = "url('Images/Player/pause-button.svg')";
    playerMidia.play();

    currentIDPlayer = hash;
  } else {
    if (playerMidia.paused) {
      playerMidia.play();
      playButton.style.backgroundImage = pauseIcon;
    } else {
      playerMidia.pause();
      playButton.style.backgroundImage = playIcon;
    }
  }


  // Update progress bar
  playerMidia.addEventListener('timeupdate', function () {
    if (currentIDPlayer === hash) {
      var duration = playerMidia.duration;
      var currentTime = playerMidia.currentTime;
      if (duration === 0 || currentTime === 0) {
        currentTimeElement.textContent = "0:00";
        durationElement.textContent = "0:00";
        progress.style.width = "0%";
      } else {
        var progressPercentage = (currentTime / duration) * 100;
        progress.style.width = progressPercentage + "%";

        // Update current time
        var currentMinutes = Math.floor(currentTime / 60);
        var currentSeconds = Math.floor(currentTime % 60);
        currentTimeElement.textContent = currentMinutes + ":" + (currentSeconds < 10 ? "0" : "") + currentSeconds;

        // Update duration
        var durationMinutes = Math.floor(duration / 60);
        var durationSeconds = Math.floor(duration % 60);
        durationElement.textContent = durationMinutes + ":" + (durationSeconds < 10 ? "0" : "") + durationSeconds;
      }
    }
  });

  // Monitor the audio playback completion event
  playerMidia.addEventListener('ended', function () {
    if (currentIDPlayer === hash) {
      playButton.style.backgroundImage = playIcon;
      currentIDPlayer = ""; // Clear the current player ID
      progress.style.width = "0%"; // Set the progress bar width to 100%
    }
  });

  // Seek to a specific time on progress bar click
  progressBar.addEventListener('click', function (e) {
    if (currentIDPlayer === hash) {
      var progressWidth = progressBar.offsetWidth;
      var clickPosition = e.offsetX;
      var seekTime = (clickPosition / progressWidth) * playerMidia.duration;

      if (!isNaN(seekTime) && isFinite(seekTime)) {
        playerMidia.currentTime = seekTime;
      }
    }
  });
}


function getCache(key) {
  const storage = localStorage.getItem(key);
  if (storage !== null) {
    return storage;
  }
  return cacheMap.get(key) || null;
}

function hasCache(key) {
  return getCache(key) !== null;
}

function setCache(key, value) {
  try {
    localStorage.setItem(key, value);
  } catch (ex) {
    cacheMap.set(key, value);
  }
}

function getCacheSize() {
  let totalSize = 0;

  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    const value = localStorage.getItem(key);
    totalSize += key.length + value.length;
  }
  return totalSize;
}

function getCachePercent() {
  return (getCacheSize() / (5242880)) * 100;
}


async function downloadMidia(id, hash, cacheMap) {
  try {
    const elements = Array.from(document.querySelectorAll('[id="' + id + '"]'));

    elements.forEach(async function (element) {
      if (cacheMap.has(hash)) {
        element.src = cacheMap.get(hash);
      } else {
        const parts = hash.split('.');
        const format = parts[parts.length - 1].toLowerCase();
        const dados = await downloadBase64(hash);
        const contentBlob = b64toBlob(dados, type(format) + '/' + format);
        const url = URL.createObjectURL(contentBlob);
        element.src = url;
        cacheMap.set(hash, url);
      }
      elements.forEach(function (audio) {
        const parentElement = audio.closest(".player");

        if (parentElement) {
          const playButton = parentElement.querySelector(".controls .play-button");
          if (playButton) {
            playButton.style.backgroundImage = "url('Images/Player/play-button.svg')";
          }

          const downloadButton = parentElement.querySelector(".controls .download-button");
          if (downloadButton) {
            downloadButton.style.backgroundImage = "url('Images/Player/download-button.svg')";
          }
        }
      });
    });
  } catch (erro) {
    console.error(erro);
    // Trate o erro aqui, se necessário
  }
}

async function downloadAllMidia() {
  const time = timestamp;
  await Promise.all([
    downloadAllTitles(time),
    downloadAllImages(time),
    downloadAllAudios(time),
  ]);
}

async function downloadAllImages(time) {
  const imageElements = Array.from(document.querySelectorAll('.image_file')).reverse();

  for (const imageElement of imageElements) {
    if (time !== timestamp) {
      return;
    }

    try {
      const hash = imageElement.querySelector('img').getAttribute('id');
      const id = hash; // ou qualquer outra lógica para obter o ID desejado
      await downloadMidia(id, hash, cacheMap);
    } catch (error) {
      console.error(error);
      // Trate o erro aqui, se necessário
    }
  }
}

async function downloadAllAudios(time) {
  const audioElements = Array.from(document.querySelectorAll('.media_file audio')).reverse();

  for (const audioElement of audioElements) {
    if (time !== timestamp) {
      return;
    }

    try {
      const hash = audioElement.getAttribute('id');
      const id = audioElement.getAttribute('id'); // ou qualquer outra lógica para obter o ID desejado
      await downloadMidia(id, hash, cacheMap);

      if (audioTime.has(hash)) {
        const audioTimeData = audioTime.get(hash);

        if (audioTimeData[0] !== 0 && audioTimeData[0] !== audioElement.duration) {
          audioElement.currentTime = audioTimeData[0];

          if (!audioTimeData[1]) {
            togglePlay(hash);
          }
        }
      }
    } catch (error) {
      console.error(error);
      // Trate o erro aqui, se necessário
    }
  }
}

async function downloadLastTitle() {
  const elementos = Array.from(document.getElementsByClassName('linkMsg')).reverse();

  if (!elementos[0]) {
    return;
  }

  const linkElemento = document.getElementById(elementos[0].id);
  const link = linkElemento.href;

  if (hasCache(link)) {
    linkElemento.innerHTML = getCache(link);
  } else {
    await downloadTitle(linkElemento, link);
  }
}

async function downloadAllTitles(time) {
  const elementos = Array.from(document.getElementsByClassName('linkMsg')).reverse();

  for (const elemento of elementos) {
    if (time !== timestamp) {
      return;
    }

    const linkElemento = document.getElementById(elemento.id);

    if (!linkElemento) {
      continue;
    }

    const link = linkElemento.href;

    if (hasCache(link)) {
      linkElemento.innerHTML = getCache(link);
    } else {
      await downloadTitle(linkElemento, link);
    }
  }
}

async function downloadTitle(linkElemento, link) {
  try {
    const result = await $.ajax({
      url: 'getTitle.php',
      method: 'GET',
      data: { link },
      dataType: 'json'
    });
    const formattedResult = `${result}<span style='opacity:0.5;'>${link}</span>`;
    setCache(link, formattedResult);
    linkElemento.innerHTML = formattedResult;
  } catch (error) {
    console.error(error);
  }
}

function getAudioTimes() {
  var audioElements = Array.from(document.querySelectorAll('.media_file audio')).reverse();
  for (let i = 0; i < audioElements.length; i++) {
    try {
      var hash = audioElements[i].getAttribute('id');
      audioTime.set(hash, [audioElements[i].currentTime, audioElements[i].paused]);
    } catch (erro) {
      console.error(erro);
      // Trate o erro aqui, se necessário
    }
  }
}

const imageObjectsCache = {};

async function fetchImageAsBase64(nickNameContact) {
  try {
    const formData = new FormData();
    formData.append('nickNameContact', nickNameContact);
    formData.append('action', 'downloadProfilePic');

    const dados = await $.ajax({
      url: 'actions.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json'
    });

    return dados;
  } catch (error) {
    throw error;
  }
}

async function createImageObject(picContactId) {
  if (!imageObjectsCache[picContactId]) {
    const blob = await fetchImageAsBase64(picContactId);
    if (blob) {
      const contentBlob = b64toBlob(blob, type('jpg') + "/" + 'jpg');
      const imageUrlObject = URL.createObjectURL(contentBlob);
      imageObjectsCache[picContactId] = imageUrlObject;
    } else {
      imageObjectsCache[picContactId] = null; // Para indicar que não há imagem
    }
  }
  return imageObjectsCache[picContactId];
}

async function downloadAllPicContacts() {
  const imgElements = document.querySelectorAll('.picContact img');
  const imageUrlObjects = await Promise.all(Array.from(imgElements).map(async (imgElement) => {
    const picContactId = imgElement.closest('.picContact').id;
    return createImageObject(picContactId);
  }));

  imgElements.forEach((picContactElement, index) => {
    const imageUrlObject = imageUrlObjects[index];
    if (imageUrlObject !== null) {
      picContactElement.src = `${imageUrlObject}`;
    } else {
      picContactElement.src = `Images/blank.png`;
    }
    picContactElement.style.backgroundImage = "url(Images/blank.png);";
  });
}


function type(format) {
  format = format.toLowerCase();
  switch (format) {
    case 'mp3':
    case 'wav':
    case 'ogg':
      return 'audio';
    case 'mp4':
    case 'avi':
    case 'mov':
      return 'video';
    case 'jpg':
    case 'jpeg':
    case 'png':
      return 'image';
    default:
      return 'unknown';
  }
}


function emojiClicked(event) {
  const currentTextElement = document.querySelector(".text");
  const xClick = event.offsetX;
  const Width = currentTextElement.offsetWidth;
  const xDiff = Width - xClick;
  const yClick = event.offsetY;
  const size = parseInt(window.getComputedStyle(textElement).backgroundSize.replace("px", ""));

  if (screenWidth > screenHeight) {
    if (yClick <= (size + 20) && xDiff <= (size + 20)) {
      embedEmojis();
    }
  } else {
    if (yClick <= (size + 40) && xDiff <= (size + 40)) {
      embedEmojis();
    }
  }
}


function embedYoutube(id) {
  messageAreaEnable(false);
  getAudioTimes();
  timestamp = new Date().getTime();
  var messagesElement = document.getElementById('messages');
  if (document.querySelector(".msg")) {
    msgsContents = messagesElement.innerHTML;
  }
  scrollPosition = messagesElement.scrollTop;
  messagesElement.innerHTML = '';

  var aElement = document.createElement('a');
  aElement.href = 'https://youtu.be/' + id;
  aElement.target = '_blank';
  aElement.classList.add('embed-link');
  messagesElement.appendChild(aElement);


  var divElement = document.createElement('div');
  divElement.onclick = function () { 
    closeVideo(scrollPosition); 
  };
  divElement.classList.add('embed-close');
  messagesElement.appendChild(divElement);

  var iframeElement = document.createElement('iframe');
  iframeElement.src = 'https://www.youtube.com/embed/' + id;
  iframeElement.title = 'YouTube video player';
  iframeElement.style.border = 0;
  iframeElement.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
  iframeElement.allowFullscreen = true;
  iframeElement.classList.add('embed-iframe');
  messagesElement.appendChild(iframeElement);
}
function embedEmojis() {
  const isOpen = document.querySelector(".emoji-div");
  if (!isOpen) {
    document.querySelector('.text').style.backgroundImage = "url('Images/emojiSelectedIcon.svg')";
    getAudioTimes();
    timestamp = new Date().getTime();
    // Array com emojis
    const emojis = [
      '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '🫠', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '☺️', '😚', '😙', '🥲', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🫢', '🫣', '🤫', '🤔', '🫡', '🤐', '🤨', '😐', '😑', '😶', '🫥', '😶‍🌫️', '😏', '😒', '🙄', '😬', '😮‍💨', '🤥', '🫨', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '😵‍💫', '🤯', '🤠', '🥳', '🥸', '😎', '🤓', '🧐', '😕', '🫤', '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '🥹', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', '☠️', '💩', '🤡', '👹', '👺', '👻', '👽', '👾', '🤖', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾', '🙈', '🙉', '🙊', '💋', '💯', '💢', '💥', '💫', '💦', '💨', '🕳️', '💤', '👋', '🤚', '🖐️', '✋', '🖖', '🫱', '🫲', '🫳', '🫴', '🫷', '🫸', '👌', '🤌', '🤏', '✌️', '🤞', '🫰', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '🫵', '👍', '👎', '✊', '👊', '🤛', '🤜', '👏', '🙌', '🫶', '👐', '🤲', '🤝', '🙏', '✍️', '💅', '🤳', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', '🧠', '🫀', '🫁', '🦷', '🦴', '👀', '👁️', '👅', '👄', '🫦', '👶', '🧒', '👦', '👧', '🧑', '👱', '👨', '🧔', '🧔‍♂️', '🧔‍♀️', '👨‍🦰', '👨‍🦱', '👨‍🦳', '👨‍🦲', '👩', '👩‍🦰', '🧑‍🦰', '👩‍🦱', '🧑‍🦱', '👩‍🦳', '🧑‍🦳', '👩‍🦲', '🧑‍🦲', '👱‍♀️', '👱‍♂️', '🧓', '👴', '👵', '🙍', '🙍‍♂️', '🙍‍♀️', '🙎', '🙎‍♂️', '🙎‍♀️', '🙅', '🙅‍♂️', '🙅‍♀️', '🙆', '🙆‍♂️', '🙆‍♀️', '💁', '💁‍♂️', '💁‍♀️', '🙋', '🙋‍♂️', '🙋‍♀️', '🧏', '🧏‍♂️', '🧏‍♀️', '🙇', '🙇‍♂️', '🙇‍♀️', '🤦', '🤦‍♂️', '🤦‍♀️', '🤷', '🤷‍♂️', '🤷‍♀️', '🫅', '🤴', '👸', '👳', '👳‍♂️', '👳‍♀️', '👲', '🧕', '🤵', '🤵‍♂️', '🤵‍♀️', '👰', '👰‍♂️', '👰‍♀️', '🤰', '🫃', '🫄', '🤱', '👩‍🍼', '👨‍🍼', '🧑‍🍼', '🧍', '🧍‍♂️', '🧍‍♀️', '🧎', '🧎‍♂️', '🧎‍♀️', '💃', '🕺', '🛀', '🛌', '🧑‍🤝‍🧑', '👭', '👫', '👬', '💏', '👩‍❤️‍💋‍👨', '👨‍❤️‍💋‍👨', '👩‍❤️‍💋‍👩', '💑', '👩‍❤️‍👨', '👨‍❤️‍👨', '👩‍❤️‍👩', '💌', '💘', '💝', '💖', '💗', '💓', '💞', '💕', '💟', '❣️', '💔', '❤️‍🔥', '❤️‍🩹', '❤️', '🩷', '🧡', '💛', '💚', '💙', '🩵', '💜', '🤎', '🖤', '🩶', '🤍', '🐵', '🐒', '🦍', '🦧', '🐶', '🐕', '🦮', '🐕‍🦺', '🐩', '🐺', '🦊', '🦝', '🐱', '🐈', '🐈‍⬛', '🦁', '🐯', '🐅', '🐆', '🐴', '🫎', '🫏', '🐎', '🦄', '🦓', '🦌', '🦬', '🐮', '🐂', '🐃', '🐄', '🐷', '🐖', '🐗', '🐽', '🐏', '🐑', '🐐', '🐪', '🐫', '🦙', '🦒', '🐘', '🦣', '🦏', '🦛', '🐭', '🐁', '🐀', '🐹', '🐰', '🐇', '🐿️', '🦫', '🦔', '🦇', '🐻', '🐻‍❄️', '🐨', '🐼', '🦥', '🦦', '🦨', '🦘', '🦡', '🐾', '🦃', '🐔', '🐓', '🐣', '🐤', '🐥', '🐦', '🐧', '🕊️', '🦅', '🦆', '🦢', '🦉', '🦤', '🪶', '🦩', '🦚', '🦜', '🪽', '🐦‍⬛', '🪿', '🐸', '🐊', '🐢', '🦎', '🐍', '🐲', '🐉', '🦕', '🦖', '🐳', '🐋', '🐬', '🦭', '🐟', '🐠', '🐡', '🦈', '🐙', '🐚', '🪸', '🪼', '🐌', '🦋', '🐛', '🐜', '🐝', '🪲', '🐞', '🦗', '🪳', '🕷️', '🕸️', '🦂', '🦟', '🪰', '🪱', '🦠', '💐', '🌸', '💮', '🪷', '🏵️', '🌹', '🥀', '🌺', '🌻', '🌼', '🌷', '🪻', '🌱', '🪴', '🌲', '🌳', '🌴', '🌵', '🌾', '🌿', '☘️', '🍀', '🍁', '🍂', '🍃', '🪹', '🪺', '🍄', '🍇', '🍈', '🍉', '🍊', '🍋', '🍌', '🍍', '🥭', '🍎', '🍏', '🍐', '🍑', '🍒', '🍓', '🫐', '🥝', '🍅', '🫒', '🥥', '🥑', '🍆', '🥔', '🥕', '🌽', '🌶️', '🫑', '🥒', '🥬', '🥦', '🧄', '🧅', '🥜', '🫘', '🌰', '🫚', '🫛', '🍞', '🥐', '🥖', '🫓', '🥨', '🥯', '🥞', '🧇', '🧀', '🍖', '🍗', '🥩', '🥓', '🍔', '🍟', '🍕', '🌭', '🥪', '🌮', '🌯', '🫔', '🥙', '🧆', '🥚', '🍳', '🥘', '🍲', '🫕', '🥣', '🥗', '🍿', '🧈', '🧂', '🥫', '🍱', '🍘', '🍙', '🍚', '🍛', '🍜', '🍝', '🍠', '🍢', '🍣', '🍤', '🍥', '🥮', '🍡', '🥟', '🥠', '🥡', '🦀', '🦞', '🦐', '🦑', '🦪', '🍦', '🍧', '🍨', '🍩', '🍪', '🎂', '🍰', '🧁', '🥧', '🍫', '🍬', '🍭', '🍮', '🍯', '🍼', '🥛', '☕', '🫖', '🍵', '🍶', '🍾', '🍷', '🍸', '🍹', '🍺', '🍻', '🥂', '🥃', '🫗', '🥤', '🧋', '🧃', '🧉', '🧊', '🥢', '🍽️', '🍴', '🥄', '🔪', '🫙', '🏺', '🎃', '🎄', '🎆', '🎇', '🧨', '✨', '🎈', '🎉', '🎊', '🎋', '🎍', '🎎', '🎏', '🎐', '🎑', '🧧', '🎀', '🎁', '🎗️', '🎟️', '🎫', '🎖️', '🏆', '🏅', '🥇', '🥈', '🥉', '⚽', '⚾', '🥎', '🏀', '🏐', '🏈', '🏉', '🎾', '🥏', '🎳', '🏏', '🏑', '🏒', '🥍', '🏓', '🏸', '🥊', '🥋', '🥅', '⛳', '⛸️', '🎣', '🤿', '🎽', '🎿', '🛷', '🥌', '🎯', '🪀', '🪁', '🔫', '🎱', '🔮', '🪄', '🎮', '🕹️', '🎰', '🎲', '🧩', '🧸', '🪅', '🪩', '🪆', '♠️', '♥️', '♦️', '♣️', '♟️', '🃏', '🀄', '🎴', '🎭', '🖼️', '🎨', '🧵', '🪡', '🧶', '🪢', '🧑‍⚕️', '👨‍⚕️', '👩‍⚕️', '🧑‍🎓', '👨‍🎓', '👩‍🎓', '🧑‍🏫', '👨‍🏫', '👩‍🏫', '🧑‍⚖️', '👨‍⚖️', '👩‍⚖️', '🧑‍🌾', '👨‍🌾', '👩‍🌾', '🧑‍🍳', '👨‍🍳', '👩‍🍳', '🧑‍🔧', '👨‍🔧', '👩‍🔧', '🧑‍🏭', '👨‍🏭', '👩‍🏭', '🧑‍💼', '👨‍💼', '👩‍💼', '🧑‍🔬', '👨‍🔬', '👩‍🔬', '🧑‍💻', '👨‍💻', '👩‍💻', '🧑‍🎤', '👨‍🎤', '👩‍🎤', '🧑‍🎨', '👨‍🎨', '👩‍🎨', '🧑‍✈️', '👨‍✈️', '👩‍✈️', '🧑‍🚀', '👨‍🚀', '👩‍🚀', '🧑‍🚒', '👨‍🚒', '👩‍🚒', '👮', '👮‍♂️', '👮‍♀️', '🕵️', '🕵️‍♂️', '🕵️‍♀️', '💂', '💂‍♂️', '💂‍♀️', '🥷', '👷', '👷‍♂️', '👷‍♀️', '👼', '🎅', '🤶', '🧑‍🎄', '🦸', '🦸‍♂️', '🦸‍♀️', '🦹', '🦹‍♂️', '🦹‍♀️', '🧙', '🧙‍♂️', '🧙‍♀️', '🧚', '🧚‍♂️', '🧚‍♀️', '🧛', '🧛‍♂️', '🧛‍♀️', '🧜', '🧜‍♂️', '🧜‍♀️', '🧝', '🧝‍♂️', '🧝‍♀️', '🧞', '🧞‍♂️', '🧞‍♀️', '🧟', '🧟‍♂️', '🧟‍♀️', '🧌', '💆', '💆‍♂️', '💆‍♀️', '🧑‍🦯', '👨‍🦯', '👩‍🦯', '🧑‍🦼', '👨‍🦼', '👩‍🦼', '🧑‍🦽', '👨‍🦽', '👩‍🦽', '🏃', '🏃‍♂️', '🏃‍♀️', '🚶', '🚶‍♂️', '🚶‍♀️', '💇', '💇‍♂️', '💇‍♀️', '🕴️', '👯', '👯‍♂️', '👯‍♀️', '🧖', '🧖‍♂️', '🧖‍♀️', '🧗', '🧗‍♂️', '🧗‍♀️', '🤺', '🏇', '⛷️', '🏂', '🏌️', '🏌️‍♂️', '🏌️‍♀️', '🏄', '🏄‍♂️', '🏄‍♀️', '🚣', '🚣‍♂️', '🚣‍♀️', '🏊', '🏊‍♂️', '🏊‍♀️', '⛹️', '⛹️‍♂️', '⛹️‍♀️', '🏋️', '🏋️‍♂️', '🏋️‍♀️', '🚴', '🚴‍♂️', '🚴‍♀️', '🚵', '🚵‍♂️', '🚵‍♀️', '🤸', '🤸‍♂️', '🤸‍♀️', '🤼', '🤼‍♂️', '🤼‍♀️', '🤽', '🤽‍♂️', '🤽‍♀️', '🤾', '🤾‍♂️', '🤾‍♀️', '🤹', '🤹‍♂️', '🤹‍♀️', '🧘', '🧘‍♂️', '🧘‍♀️', '👪', '👨‍👩‍👦', '👨‍👩‍👧', '👨‍👩‍👧‍👦', '👨‍👩‍👦‍👦', '👨‍👩‍👧‍👧', '👨‍👨‍👦', '👨‍👨‍👧', '👨‍👨‍👧‍👦', '👨‍👨‍👦‍👦', '👨‍👨‍👧‍👧', '👩‍👩‍👦', '👩‍👩‍👧', '👩‍👩‍👧‍👦', '👩‍👩‍👦‍👦', '👩‍👩‍👧‍👧', '👨‍👦', '👨‍👦‍👦', '👨‍👧', '👨‍👧‍👦', '👨‍👧‍👧', '👩‍👦', '👩‍👦‍👦', '👩‍👧', '👩‍👧‍👦', '👩‍👧‍👧', '🌍', '🌎', '🌏', '🌐', '🗺️', '🗾', '🧭', '🏔️', '⛰️', '🌋', '🗻', '🏕️', '🏖️', '🏜️', '🏝️', '🏞️', '🏟️', '🏛️', '🏗️', '🧱', '🪨', '🪵', '🛖', '🏘️', '🏚️', '🏠', '🏡', '🏢', '🏣', '🏤', '🏥', '🏦', '🏨', '🏩', '🏪', '🏫', '🏬', '🏭', '🏯', '🏰', '💒', '🗼', '🗽', '⛪', '🕌', '🛕', '🕍', '⛩️', '🕋', '⛲', '⛺', '🌁', '🌃', '🏙️', '🌄', '🌅', '🌆', '🌇', '🌉', '♨️', '🎠', '🛝', '🎡', '🎢', '💈', '🎪', '🚂', '🚃', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋', '🚌', '🚍', '🚎', '🚐', '🚑', '🚒', '🚓', '🚔', '🚕', '🚖', '🚗', '🚘', '🚙', '🛻', '🚚', '🚛', '🚜', '🏎️', '🏍️', '🛵', '🦽', '🦼', '🛺', '🚲', '🛴', '🛹', '🛼', '🚏', '🛣️', '🛤️', '🛢️', '⛽', '🛞', '🚨', '🚥', '🚦', '🛑', '🚧', '⚓', '🛟', '⛵', '🛶', '🚤', '🛳️', '⛴️', '🛥️', '🚢', '✈️', '🛩️', '🛫', '🛬', '🪂', '💺', '🚁', '🚟', '🚠', '🚡', '🛰️', '🚀', '🛸', '🛎️', '🧳', '⌛', '⏳', '⌚', '⏰', '⏱️', '⏲️', '🕰️', '🕛', '🕧', '🕐', '🕜', '🕑', '🕝', '🕒', '🕞', '🕓', '🕟', '🕔', '🕠', '🕕', '🕡', '🕖', '🕢', '🕗', '🕣', '🕘', '🕤', '🕙', '🕥', '🕚', '🕦', '🌑', '🌒', '🌓', '🌔', '🌕', '🌖', '🌗', '🌘', '🌙', '🌚', '🌛', '🌜', '🌡️', '☀️', '🌝', '🌞', '🪐', '⭐', '🌟', '🌠', '🌌', '☁️', '⛅', '⛈️', '🌤️', '🌥️', '🌦️', '🌧️', '🌨️', '🌩️', '🌪️', '🌫️', '🌬️', '🌀', '🌈', '🌂', '☂️', '☔', '⛱️', '⚡', '❄️', '☃️', '⛄', '☄️', '🔥', '💧', '🌊', '👓', '🕶️', '🥽', '🥼', '🦺', '👔', '👕', '👖', '🧣', '🧤', '🧥', '🧦', '👗', '👘', '🥻', '🩱', '🩲', '🩳', '👙', '👚', '🪭', '👛', '👜', '👝', '🛍️', '🎒', '🩴', '👞', '👟', '🥾', '🥿', '👠', '👡', '🩰', '👢', '🪮', '👑', '👒', '🎩', '🎓', '🧢', '🪖', '⛑️', '📿', '💄', '💍', '💎', '🔇', '🔈', '🔉', '🔊', '📢', '📣', '📯', '🔔', '🔕', '🎼', '🎵', '🎶', '🎙️', '🎚️', '🎛️', '🎤', '🎧', '📻', '🎷', '🪗', '🎸', '🎹', '🎺', '🎻', '🪕', '🥁', '🪘', '🪇', '🪈', '📱', '📲', '☎️', '📞', '📟', '📠', '🔋', '🪫', '🔌', '💻', '🖥️', '🖨️', '⌨️', '🖱️', '🖲️', '💽', '💾', '💿', '📀', '🧮', '🎥', '🎞️', '📽️', '🎬', '📺', '📷', '📸', '📹', '📼', '🔍', '🔎', '🕯️', '💡', '🔦', '🏮', '🪔', '📔', '📕', '📖', '📗', '📘', '📙', '📚', '📓', '📒', '📃', '📜', '📄', '📰', '🗞️', '📑', '🔖', '🏷️', '💰', '🪙', '💴', '💵', '💶', '💷', '💸', '💳', '🧾', '💹', '✉️', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳️', '✏️', '✒️', '🖋️', '🖊️', '🖌️', '🖍️', '📝', '💼', '📁', '📂', '🗂️', '📅', '📆', '🗒️', '🗓️', '📇', '📈', '📉', '📊', '📋', '📌', '📍', '📎', '🖇️', '📏', '📐', '✂️', '🗃️', '🗄️', '🗑️', '🔒', '🔓', '🔏', '🔐', '🔑', '🗝️', '🔨', '🪓', '⛏️', '⚒️', '🛠️', '🗡️', '⚔️', '💣', '🪃', '🏹', '🛡️', '🪚', '🔧', '🪛', '🔩', '⚙️', '🗜️', '⚖️', '🦯', '🔗', '⛓️', '🪝', '🧰', '🧲', '🪜', '⚗️', '🧪', '🧫', '🧬', '🔬', '🔭', '📡', '💉', '🩸', '💊', '🩹', '🩼', '🩺', '🩻', '🚪', '🛗', '🪞', '🪟', '🛏️', '🛋️', '🪑', '🚽', '🪠', '🚿', '🛁', '🪤', '🪒', '🧴', '🧷', '🧹', '🧺', '🧻', '🪣', '🧼', '🫧', '🪥', '🧽', '🧯', '🛒', '🚬', '⚰️', '🪦', '⚱️', '🧿', '🪬', '🗿', '🪧', '🪪', '🏧', '🚮', '🚰', '♿', '🚹', '🚺', '🚻', '🚼', '🚾', '🛂', '🛃', '🛄', '🛅', '🗣️', '👤', '👥', '🫂', '👣', '⚠️', '🚸', '⛔', '🚫', '🚳', '🚭', '🚯', '🚱', '🚷', '📵', '🔞', '☢️', '☣️', '⬆️', '↗️', '➡️', '↘️', '⬇️', '↙️', '⬅️', '↖️', '↕️', '↔️', '↩️', '↪️', '⤴️', '⤵️', '🔃', '🔄', '🔙', '🔚', '🔛', '🔜', '🔝', '🛐', '⚛️', '🕉️', '✡️', '☸️', '☯️', '✝️', '☦️', '☪️', '☮️', '🕎', '🔯', '🪯', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '⛎', '🔀', '🔁', '🔂', '▶️', '⏩', '⏭️', '⏯️', '◀️', '⏪', '⏮️', '🔼', '⏫', '🔽', '⏬', '⏸️', '⏹️', '⏺️', '⏏️', '🎦', '🔅', '🔆', '📶', '🛜', '📳', '📴', '♀️', '♂️', '⚧️', '✖️', '➕', '➖', '➗', '🟰', '♾️', '‼️', '⁉️', '❓', '❔', '❕', '❗', '〰️', '💱', '💲', '⚕️', '♻️', '⚜️', '🔱', '📛', '🔰', '⭕', '✅', '☑️', '✔️', '❌', '❎', '➰', '➿', '〽️', '✳️', '✴️', '❇️', '©️', '®️', '™️', '#️⃣', '*️⃣', '0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟', '🔠', '🔡', '🔢', '🔣', '🔤', '🅰️', '🆎', '🅱️', '🆑', '🆒', '🆓', 'ℹ️', '🆔', 'Ⓜ️', '🆕', '🆖', '🅾️', '🆗', '🅿️', '🆘', '🆙', '🆚', '🈁', '🈂️', '🈷️', '🈶', '🈯', '🉐', '🈹', '🈚', '🈲', '🉑', '🈸', '🈴', '🈳', '㊗️', '㊙️', '🈺', '🈵', '🔴', '🟠', '🟡', '🟢', '🔵', '🟣', '🟤', '⚫', '⚪', '🟥', '🟧', '🟨', '🟩', '🟦', '🟪', '🟫', '⬛', '⬜', '◼️', '◻️', '◾', '◽', '▪️', '▫️', '🔶', '🔷', '🔸', '🔹', '🔺', '🔻', '💠', '🔘', '🔳', '🔲', '💬', '👁️‍🗨️', '🗨️', '🗯️', '💭', '🏁', '🚩', '🎌', '🏴', '🏳️', '🏳️‍🌈', '🏳️‍⚧️', '🏴‍☠️', '🇦🇨', '🇦🇩', '🇦🇪', '🇦🇫', '🇦🇬', '🇦🇮', '🇦🇱', '🇦🇲', '🇦🇴', '🇦🇶', '🇦🇷', '🇦🇸', '🇦🇹', '🇦🇺', '🇦🇼', '🇦🇽', '🇦🇿', '🇧🇦', '🇧🇧', '🇧🇩', '🇧🇪', '🇧🇫', '🇧🇬', '🇧🇭', '🇧🇮', '🇧🇯', '🇧🇱', '🇧🇲', '🇧🇳', '🇧🇴', '🇧🇶', '🇧🇷', '🇧🇸', '🇧🇹', '🇧🇻', '🇧🇼', '🇧🇾', '🇧🇿', '🇨🇦', '🇨🇨', '🇨🇩', '🇨🇫', '🇨🇬', '🇨🇭', '🇨🇮', '🇨🇰', '🇨🇱', '🇨🇲', '🇨🇳', '🇨🇴', '🇨🇵', '🇨🇷', '🇨🇺', '🇨🇻', '🇨🇼', '🇨🇽', '🇨🇾', '🇨🇿', '🇩🇪', '🇩🇬', '🇩🇯', '🇩🇰', '🇩🇲', '🇩🇴', '🇩🇿', '🇪🇦', '🇪🇨', '🇪🇪', '🇪🇬', '🇪🇭', '🇪🇷', '🇪🇸', '🇪🇹', '🇪🇺', '🇫🇮', '🇫🇯', '🇫🇰', '🇫🇲', '🇫🇴', '🇫🇷', '🇬🇦', '🇬🇧', '🇬🇩', '🇬🇪', '🇬🇫', '🇬🇬', '🇬🇭', '🇬🇮', '🇬🇱', '🇬🇲', '🇬🇳', '🇬🇵', '🇬🇶', '🇬🇷', '🇬🇸', '🇬🇹', '🇬🇺', '🇬🇼', '🇬🇾', '🇭🇰', '🇭🇲', '🇭🇳', '🇭🇷', '🇭🇹', '🇭🇺', '🇮🇨', '🇮🇩', '🇮🇪', '🇮🇱', '🇮🇲', '🇮🇳', '🇮🇴', '🇮🇶', '🇮🇷', '🇮🇸', '🇮🇹', '🇯🇪', '🇯🇲', '🇯🇴', '🇯🇵', '🇰🇪', '🇰🇬', '🇰🇭', '🇰🇮', '🇰🇲', '🇰🇳', '🇰🇵', '🇰🇷', '🇰🇼', '🇰🇾', '🇰🇿', '🇱🇦', '🇱🇧', '🇱🇨', '🇱🇮', '🇱🇰', '🇱🇷', '🇱🇸', '🇱🇹', '🇱🇺', '🇱🇻', '🇱🇾', '🇲🇦', '🇲🇨', '🇲🇩', '🇲🇪', '🇲🇫', '🇲🇬', '🇲🇭', '🇲🇰', '🇲🇱', '🇲🇲', '🇲🇳', '🇲🇴', '🇲🇵', '🇲🇶', '🇲🇷', '🇲🇸', '🇲🇹', '🇲🇺', '🇲🇻', '🇲🇼', '🇲🇽', '🇲🇾', '🇲🇿', '🇳🇦', '🇳🇨', '🇳🇪', '🇳🇫', '🇳🇬', '🇳🇮', '🇳🇱', '🇳🇴', '🇳🇵', '🇳🇷', '🇳🇺', '🇳🇿', '🇴🇲', '🇵🇦', '🇵🇪', '🇵🇫', '🇵🇬', '🇵🇭', '🇵🇰', '🇵🇱', '🇵🇲', '🇵🇳', '🇵🇷', '🇵🇸', '🇵🇹', '🇵🇼', '🇵🇾', '🇶🇦', '🇷🇪', '🇷🇴', '🇷🇸', '🇷🇺', '🇷🇼', '🇸🇦', '🇸🇧', '🇸🇨', '🇸🇩', '🇸🇪', '🇸🇬', '🇸🇭', '🇸🇮', '🇸🇯', '🇸🇰', '🇸🇱', '🇸🇲', '🇸🇳', '🇸🇴', '🇸🇷', '🇸🇸', '🇸🇹', '🇸🇻', '🇸🇽', '🇸🇾', '🇸🇿', '🇹🇦', '🇹🇨', '🇹🇩', '🇹🇫', '🇹🇬', '🇹🇭', '🇹🇯', '🇹🇰', '🇹🇱', '🇹🇲', '🇹🇳', '🇹🇴', '🇹🇷', '🇹🇹', '🇹🇻', '🇹🇼', '🇹🇿', '🇺🇦', '🇺🇬', '🇺🇲', '🇺🇳', '🇺🇸', '🇺🇾', '🇺🇿', '🇻🇦', '🇻🇨', '🇻🇪', '🇻🇬', '🇻🇮', '🇻🇳', '🇻🇺', '🇼🇫', '🇼🇸', '🇽🇰', '🇾🇪', '🇾🇹', '🇿🇦', '🇿🇲', '🇿🇼',
    ];

    var messagesElement = document.getElementById('messages');
    if (document.querySelector(".msg")) {
      msgsContents = messagesElement.innerHTML;
    }
    // Limpa o conteúdo atual
    scrollPosition = messagesElement.scrollTop;
    messagesElement.innerHTML = '';
    // Cria o div com scrollbar
    let emojiDiv = document.createElement('div');
    emojiDiv.classList.add('emoji-div', 'media');

    // Adiciona os emojis
    emojis.forEach(emoji => {
      let div = document.createElement('div');
      div.innerText = emoji;
      div.classList.add('emoji');
      emojiDiv.appendChild(div);

      div.onclick = () => {
        if (send !== "url(\"Images/micSelectedIcon.svg\")") {
          document.querySelector(".text").value += emoji;
          messageValidate();
        }
      }
    });

    messagesElement.appendChild(emojiDiv);
  } else {
    closeEmoji(scrollPosition);
  }
}

function embedVideo(link, id) {
  messageAreaEnable(false);
  getAudioTimes();
  timestamp = new Date().getTime();
  var messagesElement = document.getElementById('messages'); 
  if (document.querySelector(".msg")) {
    msgsContents = messagesElement.innerHTML;
  }
  scrollPosition = messagesElement.scrollTop;
  messagesElement.innerHTML = '';
  var aElement = document.createElement('a');
  aElement.href = link;
  aElement.target = '_blank';
  aElement.classList.add('embed-link');
  messagesElement.appendChild(aElement);
  var divElement = document.createElement('div');
  divElement.onclick =  function () { 
    closeVideo(scrollPosition);
  };
  divElement.classList.add('embed-close');
  messagesElement.appendChild(divElement);
  var videoElement = document.createElement('video');
  videoElement.src = id;
  videoElement.controls = true;
  videoElement.controls = false;
  videoElement.classList.add('embed-video', 'media');
  videoElement.onclick = function (event) {
    togglePlay(id,event);
  };
  messagesElement.appendChild(videoElement);
  var mediaFileDiv = document.createElement('div');
  mediaFileDiv.classList.add('media_file');
  var playerDiv = document.createElement('div');
  playerDiv.classList.add('player');
  var controlsDiv = document.createElement('div');
  controlsDiv.classList.add('controls');
  var playButtonDiv = document.createElement('div');
  playButtonDiv.style.backgroundImage = "url('Images/Player/play-button.svg')";
  playButtonDiv.classList.add('play-button');
  playButtonDiv.onclick = function (event) {
    togglePlay(id,event);
  };
  var timeDiv = document.createElement('div');
  timeDiv.classList.add('time');
  var currentTimeSpan = document.createElement('span');
  currentTimeSpan.classList.add('current-time');
  currentTimeSpan.textContent = '0:00';
  var durationSpan = document.createElement('span');
  durationSpan.classList.add('duration');
  durationSpan.textContent = '0:00';
  timeDiv.appendChild(currentTimeSpan);
  timeDiv.appendChild(durationSpan);
  var progressBarDiv = document.createElement('div');
  progressBarDiv.classList.add('progress-bar');
  var progressDiv = document.createElement('div');
  progressDiv.classList.add('progress');
  progressBarDiv.appendChild(progressDiv);
  controlsDiv.appendChild(playButtonDiv);
  controlsDiv.appendChild(timeDiv);
  controlsDiv.appendChild(progressBarDiv);
  playerDiv.appendChild(controlsDiv);
  playerDiv.style.backgroundColor = "rgb(23, 37, 52)";
  playerDiv.style.marginBottom = "-10px";
  mediaFileDiv.appendChild(playerDiv);
  mediaFileDiv.style.marginTop = "-1px";
  messagesElement.appendChild(mediaFileDiv);
}

function embedImage(hash, event) {
  messageAreaEnable(false);
  getAudioTimes();
  timestamp = new Date().getTime();
  var imageSrc = event.target.src;
  var messagesElement = document.getElementById('messages'); 
  if (document.querySelector(".msg")) {
    msgsContents = messagesElement.innerHTML;
  }
  scrollPosition = messagesElement.scrollTop;
  messagesElement.innerHTML = '';
  var aElement = document.createElement('a');
  aElement.href = '#';
  aElement.onclick = function () {
    var downloadLink = document.createElement('a');
    downloadLink.href = imageSrc;
    downloadLink.download = hash;
    downloadLink.click();
  };
  aElement.classList.add('embed-download');
  messagesElement.appendChild(aElement); 
  var divElement = document.createElement('div');
  divElement.onclick =  function () {  
    closeImage(scrollPosition);
  };
  divElement.classList.add('embed-close');
  messagesElement.appendChild(divElement);

  var imageContainer = document.createElement('div');
  imageContainer.classList.add('embed-image-container', 'media');
  messagesElement.appendChild(imageContainer);

  var centerElement = document.createElement('div');
  centerElement.classList.add('center');
  imageContainer.appendChild(centerElement);

  var imgElement = document.createElement('img');
  imgElement.height = '100%';
  imgElement.src = imageSrc;
  imgElement.classList.add('embed-image');
  centerElement.appendChild(imgElement);
}

function closeImage(scroll) {
  close(scroll);
}

function closeVideo(scroll) {
  close(scroll);
}

function closeEmoji(scroll) {
  const isOpen = document.querySelector(".emoji-div");
  if (isOpen) {
    document.querySelector('.text').style.backgroundImage = "url('Images/emojiIcon.svg')";
    close(scroll);
  }
}

function close(scroll) {
  messageAreaEnable(true);
  var messagesElement = document.getElementById('messages')
  messagesElement.innerHTML = msgsContents;
  messagesElement.scrollTo(0,scroll);
  downloadAllMidia();
}

function messageAreaEnable(value) {
  document.querySelector('.text').disabled = !value;
  document.querySelector('.send').disabled = !value;
  document.querySelector('.attachment').disabled = !value;
}

const maxLength = 4096
function messageValidate() {
  const textLength = document.getElementById("text").value.length;
  const inputFile = document.getElementById('file');
  const sendButton = document.getElementById('send');
  const attachmentDiv = document.getElementById('attachment');

  if (textLength > 0 && textLength <= maxLength && sendButton.style.backgroundImage !== "url(\"Images/send_vectorized.svg\")" || inputFile.files.length > 0) {
    sendButton.style.backgroundImage = "url(\"Images/send_vectorized.svg\")";
  } else if (textLength == 0 || textLength > maxLength && sendButton.style.backgroundImage !== "url(\"Images/micIcon.svg\")") {
    sendButton.style.backgroundImage = "url(\"Images/micIcon.svg\")";
  }

  if (inputFile.files.length > 0) {
    attachmentDiv.style.backgroundColor = "#30a3e7";
  } else {
    attachmentDiv.style.backgroundColor = ""; // Volta à cor padrão, se necessário
  }
}

function stringToMD5(inputString) {
  const md5Hash = md5(inputString);
  return md5Hash;
}

async function createMessage() {
  const messageText = document.getElementById('text').value;
  const send = document.querySelector(".send").style.backgroundImage;

  if (send === "url(\"Images/send_vectorized.svg\")") {
      await processTextMessage(messageText);
  } else {
      startRecording();
  }
}

async function processTextMessage(messageText) {
  const inputFile = document.getElementById('file');
  closeEmoji();

  if (isMessageValid(messageText, inputFile)) {
      loading(true);
      document.getElementById('text').value = "";

      const randID = "a" + parseInt(Math.random() * 100);
      addMessage(randID, messageText, true);
      down();

      try {
          const result = await createMessageOnServer(messageText);
          const text = await getMessageText (messageText);
          console.log("text"+text+"result"+result);
          document.querySelector("#msg" + randID).remove();
          addMessage(result, text, false);

          sendSocket("create_message:msg" + result);
          loading(false);
      } catch (error) {
          console.error(error);
          loading(false);
      }
  } else {
      await processFileMessage(inputFile, messageText);
  }

  refreshContacts();
}

function isMessageValid(messageText, inputFile) {
  const maxLength = 100; // Assuming maxLength is defined somewhere
  return (
      (messageText.trim().length > 0 && messageText.length <= maxLength && inputFile.files.length === 0) ||
      (messageText.trim() === "" && messageText.length <= maxLength && inputFile.files.length === 0)
  );
}

async function createMessageOnServer(messageText) {
  try {
      const result = await $.ajax({
          url: 'actions.php',
          method: 'POST',
          data: {
              action: "createMessage",
              nickNameContact: nickNameContact, // Assuming nickNameContact is defined somewhere
              messageText: messageText
          },
          dataType: 'json'
      });

      return result;
  } catch (error) {
      throw error;
  }
}

async function getMessageText (getMessageText) {
  try {
      const text = await $.ajax({
          url: 'actions.php?',
          method: 'POST',
          data: {
              action: 'getThumb',
              msg: getMessageText
          },
          dataType: 'html'
      });

      return text;
  } catch (error) {
      throw error;
  }
}

async function processFileMessage(inputFile, messageText) {
  loading(true);
  document.getElementById('text').value = "";

  const formData = new FormData();
  const arquivo = inputFile.files[0];
  formData.append('arquivo', arquivo);
  formData.append('messageText', messageText);
  formData.append('contactNickName', nickNameContact); // Assuming nickNameContact is defined somewhere
  formData.append('action', 'uploadFile');

  const fileExtension = arquivo.name.split('.').pop().toLowerCase();
  const imageFormats = ['webp', 'png', 'jpeg', 'jpg'];

  if (imageFormats.includes(fileExtension)) {
      try {
          const resizedFile = await resizeAndConvertToJPG(arquivo);
          formData.set('arquivo', resizedFile);
          await uploadAttachment('actions.php', formData);
      } catch (error) {
          console.error(error);
      }
  } else {
      try {
          await uploadAttachment('actions.php', formData);
      } catch (error) {
          console.error(error);
      }
  }

  waitingMsg();
  inputFile.value = "";
}

async function resizeAndConvertToJPG(file) {
  const resizedFile = await new Promise((resolve) => {
      imgToJPG(file, 'resizedImage.jpg', function (resizedFile) {
          resolve(resizedFile);
      });
  });

  return new Promise((resolve) => {
      resizeImage(resizedFile, 1280, function (finalFile) {
          resolve(finalFile);
      });
  });
}



async function startRecording() {
  const sendButton = document.querySelector(".send");
  const textInput = document.querySelector(".text");

  sendButton.style.backgroundImage = "url(\"Images/micSelectedIcon.svg\")";
  textInput.disabled = true;

  const permissionsGranted = await detectInputAudioPermissions();

  if (permissionsGranted) {
      console.log('Audio input device has permissions.');
      const mediaRecorder = await startAudioRecording();
      setupRecordingHandlers(sendButton, textInput, mediaRecorder);
  } else {
      alert("The HTML5 audio API does not work in this browser or the connection is not secure.");
      sendButton.style.backgroundImage = "url(\"Images/micDisabled.svg\")";
      textInput.disabled = false;
  }
}

async function detectInputAudioPermissions() {
  const constraints = { audio: true };

  try {
      const stream = await navigator.mediaDevices?.getUserMedia?.(constraints);
      if (stream) {
          for (const track of stream.getTracks()) {
              track.stop();
          }
          return true;
      }
      return false;
  } catch (error) {
      return false;
  }
}

async function startAudioRecording() {
  const options = { audio: true };
  const stream = await navigator.mediaDevices.getUserMedia(options);
  const mediaRecorder = new MediaRecorder(stream);
  const chunks = [];

  mediaRecorder.ondataavailable = function (event) {
      chunks.push(event.data);
  };

  mediaRecorder.onstop = function () {
      const blob = new Blob(chunks, { 'type': 'audio/wav' });
      loadFile(blob);
  };

  mediaRecorder.start();
  return mediaRecorder;
}

function setupRecordingHandlers(sendButton, textInput, mediaRecorder) {
  sendButton.onclick = () => {
      mediaRecorder.stop();
      sendButton.style.backgroundImage = "url(\"Images/micIcon.svg\")";
      textInput.disabled = false;
      sendButton.onclick = () => {
          createMessage();
      };
  };
}

function loadFile(blob) {
  loading(true);
  var formData = new FormData();
  formData.append('arquivo', new File([blob], "audio." + stringToMD5(Math.random() + "") + ".wav", { type: 'audio/wav' }));
  formData.append('messageText', '');
  formData.append('contactNickName', nickNameContact);
  formData.append('action', 'uploadFile');
  uploadAttachment('actions.php', formData);
  waitingMsg();
}

async function addMessage(id, text, loading) {
  var msgElement = document.createElement('div');
  msgElement.classList.add('msg', 'msg-left');
  msgElement.id = 'msg' + id;

  var deleteLink = document.createElement('a');
  deleteLink.href = '#';
  deleteLink.classList.add('delete');
  deleteLink.onclick = function () {
    deleteMessage(id);
  };

  var deleteText = document.createElement('b');
  deleteText.appendChild(document.createTextNode('Apagar'));
  deleteLink.appendChild(deleteText);
  deleteLink.appendChild(document.createElement('br'));

  var textParagraph = document.createElement('p');
  textParagraph.innerHTML = text;
  textParagraph.appendChild(document.createElement('br'));

  if (loading) {
    var loadingImage = document.createElement('img');
    loadingImage.src = 'Images/loading.gif';
    loadingImage.className = 'loadingMessage';
    loadingImage.style.float = 'right';
    textParagraph.appendChild(loadingImage);
  } else {
    var dateSpan = document.createElement('span');
    dateSpan.style.float = 'right';
    dateSpan.appendChild(document.createTextNode(getDate()));
    textParagraph.appendChild(dateSpan);
  }

  msgElement.appendChild(deleteLink);
  msgElement.appendChild(textParagraph);

  var messagesElement = document.getElementById('messages');
  messagesElement.appendChild(msgElement);
  await downloadLastTitle();
}


function waitingMsg() {
  var messagesElement = document.getElementById('messages');
  var newDiv = document.createElement('div');
  newDiv.className = 'attachment_file uploading';
  newDiv.onclick = function () {
    updateMessages();
  };

  var newImg = document.createElement('img');
  newImg.className = 'loadIcon';
  newImg.src = "Images/blank.png";

  var newLink = document.createElement('a');
  newLink.href = '#';

  newLink.appendChild(newImg);;
  newDiv.appendChild(newLink);
  messagesElement.appendChild(newDiv);
  down();
}

async function updateMessages(contact = nickNameContact, name = nickNameContact) {
  indexMessage = 1;
  closeEmoji();

  if (contact !== nickNameContact) {
    document.title = contact;

    for (let key of audioTime.keys()) {
      audioTime.set(key, [0, true]);
    }
  } else {
    getAudioTimes();
  }

  timestamp = new Date().getTime();
  nickNameContact = contact;
  const currentUrl = window.location.href;

  if (currentUrl.includes('messages.php')) {

    loadingButton(true);

    try {
      const result = await $.ajax({
        url: 'actions.php',
        method: 'POST',
        data: { action: 'updateMsg', contactNickName: contact },
        dataType: 'html'
      });

      loadingButton(false);

      document.getElementById('messages').innerHTML = result;
      msgsContents = result;

      if (document.getElementById('login') !== null) {
        window.location.href = 'authenticate.php';
      }

      downloadAllMidia();

      var newUrl = 'messages.php?contactNickName=' + contact;
      history.pushState(null, '', newUrl);

      updateContacts(contact, name);
    } catch (error) {
      console.error(error);
      // Trate o erro aqui, se necessário
    }
  } else {
    window.location.href = 'messages.php?contactNickName=' + contact;
  }
}


function updateContacts(contact = nickNameContact, name = nickNameContact) {
  const currentContact = document.querySelector("#contact" + contact + " h2");
  const count = currentContact.querySelector(".newMsg");
  if (count) {
    count.remove();
  }
  var h2Elements = document.querySelectorAll('.contacts h2');
  h2Elements.forEach(function (h2) {
    h2.style.background = 'none';
    h2.style.color = '#2b5278';
    h2.style.boxShadow = 'none';
  });
  var h2Element = document.querySelector('#contact' + contact + ' h2');
  h2Element.style.color = 'white';
  h2Element.style.backgroundColor = '#2b5278';
  h2Element.style.boxShadow = '0px 0px 10px 5px rgba(0, 0, 0, 0.35)';
  document.getElementById('userName').innerHTML = name;
  var imgElement = document.querySelector('#' + contact + ' img');
  var imgContacts = document.querySelector('.picMessage img');
  imgContacts.src = imgElement.src;
  toggle(false);
}

function toggle(value = true, landscape = false) {
  const screenOrientation = window.screen.orientation;
  const screenWidth = window.innerWidth;
  const screenHeight = window.innerHeight;

  const hideDisplay = "none";
  const blockDisplay = "block";
  const inlineDisplay = "inline";
  const flexDisplay = "flex";

  const elementsToToggle = document.querySelectorAll('.text, .send, .attachment, .messages, .editProfile');
  const elementsToHide = document.querySelectorAll('.picMessage, .back');
  const elementsToHide2 = document.querySelectorAll('.username');
  const elementsToShow = document.querySelectorAll('.contacts, .search, .logout, .user');
  const homeElement = document.querySelector('.home');

  homeElement.style.display = landscape ? flexDisplay : hideDisplay;
  if (landscape) {
    value = true;
  }

  if (screenOrientation.type.includes("portrait") || screenHeight > screenWidth || landscape) {

    elementsToToggle.forEach(function (element) {
      element.style.display = value ? hideDisplay : blockDisplay;
    });

    elementsToHide.forEach(function (element) {
      element.style.display = value ? hideDisplay : inlineDisplay;
    });

    elementsToHide2.forEach(function (element) {
      element.style.display = value ? hideDisplay : flexDisplay;
    });

    elementsToShow.forEach(function (element) {
      element.style.display = !value ? hideDisplay : inlineDisplay;
    });
  } else if (screenOrientation.type.includes("landscape") || screenWidth > screenHeight) {
    elementsToToggle.forEach(function (element) {
      element.style.display = blockDisplay;
    });
  }
  down();
}

function hasNewMsg(msg) {
  const from = msg.from;
  const message = msg.message;
  const contact = document.querySelector("#contact" + from);
  if (!contact && !message.includes("delete_message") && message.includes("create_message")) {
    refreshContacts();
    if (from === nickNameContact) {
      hasNewMsgByCurrentContact(from, message);
    }
  } else {
    if (from === nickNameContact) {
      hasNewMsgByCurrentContact(from, message);
      if (!message.includes("delete_message") && message.includes("create_message")) {
        countMessage(contact, true);
      }
    } else {
      if (!message.includes("delete_message") && message.includes("create_message")) {
        if (contact) {
          countMessage(contact, false);
        } else {
          console.error("Elemento de contato não encontrado para: " + from);
        }
        moveToUp(contact);
      }
    }
  }
}

function refreshContacts() {
  const formData = new FormData();
  formData.append('contactNickName', nickNameContact);
  formData.append('action', 'contacts');
  fetch('actions.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(result => {
      document.querySelector('.contacts').innerHTML = result;
      downloadAllPicContacts();
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
    });
}

function countMessage(contact, isCurrentContact) {
  if (contact) {
    var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    if (screenHeight > screenWidth || !isCurrentContact) {
      const count = contact.querySelector(".newMsg");
      if (count) {
        count.innerHTML = + (parseInt(count.innerHTML) + 1);
      } else {
        const newCount = document.createElement("span");
        newCount.id = nickNameContact;
        newCount.className = "newMsg";
        newCount.innerHTML = "1";
        contact.querySelector("h2").appendChild(newCount);
      }
    }
  }
}

function moveToUp(contact) {
  if (contact) {
    // Remove form e adicion contato no top
    const contactsContainer = document.querySelector(".contacts");
    contactsContainer.querySelector('form').remove();
    contactsContainer.insertBefore(contact, contactsContainer.firstChild);
    // Criação do elemento <form>
    var formElement = document.createElement("form");
    formElement.setAttribute("action", "index.php");
    formElement.setAttribute("method", "post");

    // Criação do elemento <input> para pesquisa
    var inputElement = document.createElement("input");
    inputElement.setAttribute("class", "search");
    inputElement.setAttribute("placeholder", "Pesquisar contatos ...");
    inputElement.setAttribute("type", "text");
    inputElement.setAttribute("name", "search");

    formElement.appendChild(inputElement);

    var contactsElement = document.querySelector(".contacts");
    contactsElement.insertBefore(formElement, contactsElement.firstChild);
  }
}

function hasNewMsgByCurrentContact(from, message) {

  if (message.includes("create_message") || message.includes("delete_message")) {
    const idMsg = message.split(":")[1].replace("msg", "");
    if (message.includes("create_message")) {
      const formData = new FormData();
      formData.append('action', 'messageByID');
      formData.append('nickNameContact', from);
      formData.append('idMsg', idMsg);
      fetch('actions.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(result => {
          if (!document.getElementById("msg" + idMsg)) {
            var messagesDiv = document.querySelector('#messages');
            msgsContents += result;
            if (messagesDiv) {
              if (!messagesDiv.querySelector(".media")) {
                getAudioTimes();
                const scrollPercentage = getScrollPercentage();
                messagesDiv.innerHTML += result;
                messagesElement = document.getElementById("messages");
                console.log("scrollPercentage:" + getScrollPercentage());
                if (scrollPercentage > 90) {
                  down();
                } else {
                  downButton(true);
                }
                timestamp = new Date().getTime();
                downloadAllMidia();
              }
            }
          }
        })
        .catch(error => {
          console.error('Erro na requisição:', error);
        });
    }
    if (message.includes("delete_message")) {
      const element = document.getElementById("msg" + idMsg);
      if (element) {
        element.remove();
      }
      downButton(false);
    }
  }
}


function getScrollPercentage() {
  const element = document.querySelector("#messages");
  const hasVerticalScrollbar = element.scrollHeight > element.clientHeight;
  if (!hasVerticalScrollbar) {
    return 100;
  }
  var contentHeight = element.scrollHeight - element.clientHeight;
  var scrollPosition = element.scrollTop;
  return (scrollPosition / contentHeight) * 100;
}

function createRoundButton(elementId, imageSrc, clickHandler) {
  const messagesElement = document.getElementById("messages");
  const existingElement = document.getElementById(elementId);

  if (existingElement) {
    existingElement.remove();
  }

  if (imageSrc) {
    messagesElement.style.boxShadow = `inset 0px ${elementId === "down" ? "-20px" : "20px"} 8px 0px rgba(0, 0, 0, 0.35)`;

    const center = document.createElement("div");
    center.id = elementId;
    center.classList.add("center");

    const img = document.createElement("img");
    img.onclick = clickHandler;
    img.src = imageSrc;

    center.appendChild(img);
    messagesElement.appendChild(center);

    const imgWidth = document.querySelector(`#${elementId} img`).offsetWidth / 2;
    const leftPosition = messagesElement.offsetLeft + messagesElement.offsetWidth / 2 - imgWidth;

    center.style.top = `${document.querySelector("#messages").offsetTop + imgWidth}px`;
    center.style.left = `${leftPosition}px`;
  } else {
    messagesElement.style.boxShadow = "none";
    if (existingElement) {
      existingElement.remove();
    }
  }
}

function downButton(value) {
  const imageSrc = value ? "Images/down.svg" : null;
  createRoundButton("down", imageSrc, down);
}

function loadingButton(value) {
  const imageSrc = value ? "Images/roundLoading.gif" : null;
  createRoundButton("loading", imageSrc, removeLoadingButton);
}

function removeLoadingButton() {
  createRoundButton("loading", null, null);
}



function loading(b) {
  var sendElement = document.querySelector(".send");
  if (b) {
    sendElement.style.background = "none";
    sendElement.style.backgroundSize = "100%";
    sendElement.style.backgroundRepeat = "no-repeat";
    sendElement.style.backgroundImage = "url('Images/loading.gif')";
    sendElement.style.backgroundPositionY = "50%";
    sendElement.style.backgroundPositionX = "50%";
  } else {
    sendElement.style.background = "";
    sendElement.style.backgroundSize = "";
    sendElement.style.backgroundRepeat = "";
    sendElement.style.backgroundImage = "";
    sendElement.style.backgroundPositionY = "";
    sendElement.style.backgroundPositionX = "";
  }
}




