var msgsContents = "";
var fetchNewMessages = true;
var scrollPos = 0;
var h;
var profilePicSrc;
var updatedMsg = false;
var orientationDevice = "landscape";
var timestamp = new Date().getTime();

main();
// Chame a função downloadAllMidia de uma função assíncrona
async function main() {
  try {
    await downloadAllMidia();
    // Outras operações após o download das mídias
  } catch (erro) {
    console.error(erro);
    // Trate o erro aqui, se necessário
  }
    // Função para lidar com a mudança de resolução da tela
  function handleScreenResolutionChange() {
    // Obtenha a nova largura e altura da tela
    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
  
    if (screenWidth > screenHeight && orientationDevice == "portrait") {
      toggle(true,true);
      orientationDevice = "landscape";
    } else if (screenHeight > screenWidth) {
      orientationDevice = "portrait";
      toggle(false,false);
    } 
    // Se necessário, atualize o layout da página ou execute outras ações com base na nova resolução
    // ...
  }

  // Adicione um listener para o evento 'resize' que é acionado quando a resolução da tela é alterada
  window.addEventListener('resize', handleScreenResolutionChange);

}


function openfile(value) {   
    if (profilePicSrc == null) {
      document.getElementById(value).click();
    } else {
      document.getElementById('stylePic').innerHTML+=".salvar {display:none;}";
      loadPicStatus (false);
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
        const profilePic = document.getElementById("profilePic");
        profilePicSrc = profilePic.style.backgroundImage;
      } else {
        profilePic.style.backgroundImage = profilePicSrc;
      }
      profilePicSrc = null;
    }
  }
}


function loadingPicStatus (status) {
    if (status) {
      profilePic.src = "Images/loadingProfilePic.webp";
    } else {
      profilePic.src = "Images/edit.png";
    }  
}


function handlePhotoUpload(event) {
  const fileInput = event.target;
  const file = fileInput.files[0];
  
  if (file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
      const imageSrc = e.target.result;
      const profilePic = document.getElementById("profilePic");
      profilePic.style.backgroundImage = `url(${imageSrc})`;
    };
    
    reader.readAsDataURL(file);
    loadPicStatus (true);
  }
}

function uploadPic() {
  var arquivoInput = document.getElementById('editProfilePic');
  var arquivo = arquivoInput.files[0];
  var formData = new FormData();

  if (arquivo.type === 'image/gif' || arquivo.type === 'image/png' || arquivo.type === 'image/webp') {
    imgToJPG(arquivo, 'profilepic.jpg', function(file) {
      formData.append('pic', file);
      uploadFile('uploadPic.php', formData);
    });
  } else {
    formData.append('pic', arquivo);
    uploadFile('uploadPic.php', formData);
  }
}

function imgToJPG(inputFile, fileName, callback) {
  var reader = new FileReader();
  reader.onload = function(event) {
    var img = new Image();
    img.onload = function() {
      var canvas = document.createElement('canvas');
      canvas.width = img.width;
      canvas.height = img.height;
      var context = canvas.getContext('2d');
      context.drawImage(img, 0, 0);
      canvas.toBlob(function(blob) {
        var file = new File([blob], fileName, {type: 'image/jpeg'});
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
  reader.onload = function(event) {
    var img = new Image();
    img.src = event.target.result;
    img.onload = function() {
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
      canvas.toBlob(function(blob) {
        var resizedFile = new File([blob], file.name, { type: file.type });
        callback(resizedFile);
      }, file.type);
    };
  };
}


function upload(url, formData, successCallback, errorCallback) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url);
  xhr.onload = function() {
    if (xhr.status === 200) {
      if (xhr.responseText.length > 16) {
        alert(xhr.responseText);
      } else {
        successCallback();
      }
    } else {
      errorCallback(xhr.responseText);
    }
  };
  xhr.send(formData);
}

function uploadAttachment(url, formData) {
  upload(url, formData, function() {
    loading(false);
    updateMessages();
    var attachmentDiv = document.getElementById('attachment');
    attachmentDiv.style.backgroundColor = "";
    var sendButton = document.getElementById('send');
    sendButton.disabled = true;
  }, function(errorText) {
    console.error(errorText);
  });
}

function uploadFile(url, formData) {
  upload(url, formData, function() {
    loadPicStatus(false, true);
  }, function(errorText) {
    console.error(errorText);
  });
}

function down() {
  const messagesElement = document.getElementById("messages");
  if (messagesElement) {
    messagesElement.scrollTo(0, messagesElement.scrollHeight);
    downButton(false);
    h = messagesElement.scrollTop;
  }
}

function removeDownButton () {
  if (((document.getElementById("messages").scrollTop)/h)*100 >= 99) {
    downButton(false);
    h =  document.getElementById("messages").scrollTop;
  }
}

function deleteMessage(id) {
  if (confirm("Tem certeza de que deseja apagar esta mensagem?")) {
    document.getElementById("msg" + id).remove();
    loading(true);
    $.ajax({
      url: 'delete.php?id=' + id,
      method: 'POST',
      data: { nickNameContact: nickNameContact },
      dataType: 'json'
    }).done(function(result) {
      loading(false);
    });
  }
}


function getDate () {
  currentDate = new Date();
  currentDate = currentDate.toLocaleString('pt-BR');
  currentDate = currentDate.split(" ")[1];
  currentDate = currentDate.split(":")[0]+":"+currentDate.split(":")[1];
  return currentDate;
}


var downloading = false;

function showPlayer(hash,event) {
    if (!downloading) {
        downloading = true;
        var videoDiv = event.target.querySelector('img');
        videoDiv.style.backgroundImage = 'url(Images/loading.gif)';
        const parts = hash.split('.');
        const format = parts[parts.length - 1].toLowerCase();
        if (cookie.has(hash)) {
          var videoDiv = event.target.querySelector('img');
          videoDiv.style.backgroundImage = 'url(Images/video.svg)';
          var url = cookie.get(hash);
          embedVideo(url,url);
          downloading = false;
        } else {
          downloadBase64(hash)
          .then(function(dados) {
              var contentBlob = b64toBlob(dados, type(format)+"/"+format);
              urlContent = URL.createObjectURL(contentBlob);
              var videoDiv = event.target.querySelector('img');
              videoDiv.style.backgroundImage = 'url(Images/video.svg)';
              embedVideo(urlContent,urlContent);
              cookie.set(hash,urlContent);
              downloading = false;
          }) .catch(function(erro) {
            console.error(erro);
            // Trate o erro aqui, se necessário
          });
        }
    }
}

function downloadFile(nomeHash, nome) {
  if (cookie.has(nomeHash)) {
    var downloadLink = document.createElement('a');
    downloadLink.href = cookie.get(nomeHash);
    downloadLink.download = nome;
    downloadLink.click();
  } else {
    var xhr = new XMLHttpRequest();
    var url = 'downloadFile.php?hashName=' + nomeHash;
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
    xhr.open('GET', url, true);
    xhr.send();
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
    const dados = await $.ajax({
      url: `downloadFile.php?hashName=${nomeHash}`,
      method: 'POST',
      dataType: 'text'
    });
    return dados;
  } catch (error) {
    throw error;
  }
}

var currentIDPlayer = "";

async function togglePlay(hash,event) {
  var playButton;
  var playerAudio;
  
  if (event) {
    playButton = event.target;
    playerAudio = playButton.querySelector('audio');
  } else {
    playButton = document.getElementById(hash).parentNode;
    playerAudio = document.getElementById(hash);
  }
  
  var progressBar = playButton.closest('.player').querySelector('.progress-bar');
  var progress = playButton.closest('.player').querySelector('.progress-bar .progress');
  var currentTimeElement = playButton.closest('.player').querySelector('.time .current-time');
  var durationElement = playButton.closest('.player').querySelector('.time .duration');
  
  const playIcon = "url('Images/Player/play-button.svg')";
  const pauseIcon = "url('Images/Player/pause-button.svg')";

  if (currentIDPlayer !== hash) {
    document.querySelectorAll(".play-button").forEach(function(playButton) {
      playButton.style.backgroundImage = "url('Images/Player/play-button.svg')";
    });
    document.querySelectorAll('.audioPlayer').forEach(function(audio) {
      audio.pause();
    });
    playButton.style.backgroundImage = "url('Images/Player/pause-button.svg')";
    playerAudio.play();

    currentIDPlayer = hash;
  } else {
    if (playerAudio.paused) {
      playerAudio.play();
      playButton.style.backgroundImage = pauseIcon;
    } else {
      playerAudio.pause();
      playButton.style.backgroundImage = playIcon;
    }
  }


  // Update progress bar
  playerAudio.addEventListener('timeupdate', function() {
    if (currentIDPlayer === hash) {
      var duration = playerAudio.duration;
      var currentTime = playerAudio.currentTime;
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
  playerAudio.addEventListener('ended', function() {
    if (currentIDPlayer === hash) {
      playButton.style.backgroundImage = playIcon;
      currentIDPlayer = ""; // Clear the current player ID
      progress.style.width = "0%"; // Set the progress bar width to 100%
    }
  });

  // Seek to a specific time on progress bar click
  progressBar.addEventListener('click', function(e) {
    if (currentIDPlayer === hash) {
      var progressWidth = progressBar.offsetWidth;
      var clickPosition = e.offsetX;
      var seekTime = (clickPosition / progressWidth) * playerAudio.duration;

      if (!isNaN(seekTime) && isFinite(seekTime)) {
        playerAudio.currentTime = seekTime;
      }
    }
  });
}



async function downloadMidia(id, hash, cookie) {
  try {
    const elements = Array.from(document.querySelectorAll('[id="' + id + '"]'));
    
    elements.forEach(async function (element) {
      if (cookie.has(hash)) {
        element.src = cookie.get(hash);
      } else {
        const parts = hash.split('.');
        const format = parts[parts.length - 1].toLowerCase();
        const dados = await downloadBase64(hash);
        const contentBlob = b64toBlob(dados, type(format) + '/' + format);
        const url = URL.createObjectURL(contentBlob);
        element.src = url;
        cookie.set(hash, url);
      }
      elements.forEach(function(audio) {
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
  await downloadAllTitles(time);
  await downloadAllImages(time);
  await downloadAllAudios(time);
}


async function downloadAllImages(time) {
  const imageElements = Array.from(document.querySelectorAll('.image_file img')).reverse();

  for (const imageElement of imageElements) {
    if (time !== timestamp) {
      return;
    }

    try {
      const hash = imageElement.getAttribute('id');
      const id = hash; // ou qualquer outra lógica para obter o ID desejado
      await downloadMidia(id, hash, cookie);
    } catch (error) {
      console.error(error);
      // Trate o erro aqui, se necessário
    }
  }
}

async function downloadAllAudios(time) {
  const audioElements = Array.from(document.querySelectorAll('.audio_file audio')).reverse();

  for (const audioElement of audioElements) {
    if (time !== timestamp) {
      return;
    }

    try {
      const hash = audioElement.getAttribute('id');
      const id = audioElement.getAttribute('id'); // ou qualquer outra lógica para obter o ID desejado
      await downloadMidia(id, hash, cookie);

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
  const ultimoElemento = elementos[0];

  if (!ultimoElemento) {
    return;
  }

  const linkElemento = document.getElementById(ultimoElemento.id);
  const link = linkElemento.href;

  if (cookie.has(link)) {
    linkElemento.innerHTML = cookie.get(link);
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
    const link = linkElemento.href;

    if (cookie.has(link)) {
      linkElemento.innerHTML = cookie.get(link);
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
    cookie.set(link, formattedResult);
    linkElemento.innerHTML = formattedResult;
  } catch (error) {
    console.error(error);
  }
}

function getAudioTimes () {
  var audioElements = Array.from(document.querySelectorAll('.audio_file audio')).reverse();
  for (let i = 0; i < audioElements.length; i++) {
    try {
      var hash = audioElements[i].getAttribute('id');
      audioTime.set(hash,[audioElements[i].currentTime,audioElements[i].paused]);
    } catch (erro) {
      console.error(erro);
      // Trate o erro aqui, se necessário
    }
  }
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

function embedYoutube(id) {
  getAudioTimes();
  timestamp = new Date().getTime();
  fetchNewMessages = false;
  scrollPos = document.getElementById('messages').scrollTop;
  msgsContents = document.getElementById('messages').innerHTML;
  document.getElementById('messages').innerHTML = '';

  var aElement = document.createElement('a');
  aElement.href = 'https://youtu.be/' + id;
  aElement.target = '_blank';
  aElement.classList.add('embed-link');
  document.getElementById('messages').appendChild(aElement);

  var divElement = document.createElement('div');
  divElement.onclick = closeVideo;
  divElement.classList.add('embed-close');
  document.getElementById('messages').appendChild(divElement);

  var iframeElement = document.createElement('iframe');
  iframeElement.src = 'https://www.youtube.com/embed/' + id;
  iframeElement.title = 'YouTube video player';
  iframeElement.frameBorder = 0;
  iframeElement.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
  iframeElement.allowFullscreen = true;
  iframeElement.classList.add('embed-iframe');
  document.getElementById('messages').appendChild(iframeElement);
}

function embedVideo(link, id) {
  getAudioTimes();
  timestamp = new Date().getTime();
  fetchNewMessages = false;
  scrollPos = document.getElementById('messages').scrollTop;
  msgsContents = document.getElementById('messages').innerHTML;
  document.getElementById('messages').innerHTML = '';

  var aElement = document.createElement('a');
  aElement.href = link;
  aElement.target = '_blank';
  aElement.classList.add('embed-link');
  document.getElementById('messages').appendChild(aElement);

  var divElement = document.createElement('div');
  divElement.onclick = closeVideo;
  divElement.classList.add('embed-close');
  document.getElementById('messages').appendChild(divElement);

  var iframeElement = document.createElement('iframe');
  iframeElement.src = id;
  iframeElement.frameBorder = 0;
  iframeElement.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
  iframeElement.allowFullscreen = true;
  iframeElement.classList.add('embed-iframe');
  document.getElementById('messages').appendChild(iframeElement);
}

function embedImage(hash,event) {
  getAudioTimes();
  timestamp = new Date().getTime();
  var imageSrc = event.target.src;
  fetchNewMessages = false;
  scrollPos = document.getElementById('messages').scrollTop;
  msgsContents = document.getElementById('messages').innerHTML;
  document.getElementById('messages').innerHTML = '';

  var aElement = document.createElement('a');
  aElement.href = '#';
  aElement.onclick = function() {
    var downloadLink = document.createElement('a');
    downloadLink.href = imageSrc;
    downloadLink.download = hash;
    downloadLink.click();
  };
  aElement.classList.add('embed-download');
  document.getElementById('messages').appendChild(aElement);

  var divElement = document.createElement('div');
  divElement.onclick = closeImage;
  divElement.classList.add('embed-close');
  document.getElementById('messages').appendChild(divElement);

  var imageContainer = document.createElement('div');
  imageContainer.classList.add('embed-image-container');
  document.getElementById('messages').appendChild(imageContainer);

  var centerElement = document.createElement('center');
  imageContainer.appendChild(centerElement);

  var imgElement = document.createElement('img');
  imgElement.height = '100%';
  imgElement.src = imageSrc;
  imgElement.classList.add('embed-image');
  centerElement.appendChild(imgElement);
}

function closeVideo() {
  close();
}

function closeImage() {
  close();
}

function close() {
  fetchNewMessages = true;
  document.getElementById('messages').innerHTML=msgsContents;
  document.getElementById('messages').scrollTo(0, scrollPos);
  newContact();
  downloadAllMidia();
}


function messageValidate() {
    var textLength = document.getElementById("text").value.length;
    var inputFile = document.getElementById('file');
    var sendButton = document.getElementById('send');
    var attachmentDiv = document.getElementById('attachment');

    if (textLength > 500 || textLength < 1 && inputFile.files.length == 0) {
        sendButton.disabled = true;
    } else {
        sendButton.disabled = false;
    }

    if (inputFile.files.length > 0) {
        attachmentDiv.style.backgroundColor = "#30a3e7";
    } else {
        attachmentDiv.style.backgroundColor = ""; // Volta à cor padrão, se necessário
    }
}



function createMessage () {
  var inputFile = document.getElementById('file');

  // Verifica se foi selecionado pelo menos um arquivos
  var messageText = document.getElementById('text').value;

  if (messageText.length > 0 && messageText.length <= 500 && !(inputFile.files.length > 0) || messageText  == " " && messageText.length <= 500 && !(inputFile.files.length > 0) ) {
      loading (true);
      document.getElementById('text').value="";
      $.ajax({
        url: 'new.php',
        method: 'POST',
        data: {nickNameContact: nickNameContact, messageText: messageText},
        dataType: 'json'
      }).done(function(result) {
            date = 
            id = result;
            $.ajax({
              url: 'getThumb.php?',
              method: 'GET',
              data: {msg: messageText},
              dataType: 'html'
            }).done(function(text) {
               addMessage(id, text);
               down ();
            });
            loading (false);
      });
  } else {
    loading(true);
    document.getElementById('text').value="";
    var formData = new FormData();
    var arquivo = inputFile.files[0];
    formData.append('arquivo',arquivo);
    formData.append('messageText',messageText);
    formData.append('contactNickName',nickNameContact);
    var file = formData.get('arquivo');
    var fileExtension = file.name.split('.').pop().toLowerCase();
    var imageFormats = ['webp', 'png', 'jpeg', 'jpg'];
    
    if (imageFormats.includes(fileExtension)) {
      imgToJPG(file, 'resizedImage.jpg', function(resizedFile) {
        resizeImage(resizedFile, 1280, function(finalFile) {
          formData.set('arquivo', finalFile);
          uploadAttachment('uploadfile.php', formData);
        });
      });
    } else {
      uploadAttachment('uploadfile.php', formData);
    }    
    waitingMsg();
    inputFile.value="";
  }
}    


async function addMessage(id, text) {
  var msgElement = document.createElement('div');
  msgElement.classList.add('msg', 'msg-left');
  msgElement.id = 'msg' + id;

  var deleteLink = document.createElement('a');
  deleteLink.href = '#';
  deleteLink.classList.add('delete');
  deleteLink.onclick = function() {
    deleteMessage(id);
  };

  var deleteText = document.createElement('b');
  deleteText.appendChild(document.createTextNode('Apagar'));
  deleteLink.appendChild(deleteText);
  deleteLink.appendChild(document.createElement('br'));

  var textParagraph = document.createElement('p');
  textParagraph.innerHTML=text;
  textParagraph.appendChild(document.createElement('br'));

  var dateSpan = document.createElement('span');
  dateSpan.style.float = 'right';
  dateSpan.appendChild(document.createTextNode(getDate()));
  textParagraph.appendChild(dateSpan);

  msgElement.appendChild(deleteLink);
  msgElement.appendChild(textParagraph);

  var messagesElement = document.getElementById('messages');
  messagesElement.appendChild(msgElement);

  var sendButton = document.getElementById('send');
  sendButton.disabled = true;

  await downloadLastTitle();
}





function waitingMsg() {
  var messagesElement = document.getElementById('messages');
  var newDiv = document.createElement('div');
  newDiv.className = 'attachment_file uploading';
  newDiv.onclick = function() {
    updateMessages ();
  };

  var newImg = document.createElement('img');
  newImg.className = 'loadIcon';
  newImg.src = "Images/blank.png";

  var newLink = document.createElement('a');
  newLink.href = '#';
  
  newLink.appendChild(newImg);
  newLink.appendChild(document.createTextNode('Enviado'));
  newDiv.appendChild(newLink);
  messagesElement.appendChild(newDiv);
  down();
}

function updateMessages (contact = nickNameContact, name=nickNameContact) {
  if (contact !== nickNameContact) {
    for (let key of audioTime.keys()) {
      audioTime.set(key,[0,true]);
    }  
  } else {
    getAudioTimes();
  }
  timestamp = new Date().getTime();
  nickNameContact = contact;
  const currentUrl = window.location.href;
  if (currentUrl.includes('messages.php')) {
    $.ajax({
      url: 'updateMsg.php',
      method: 'POST',
      data: {contactNickName: contact},
      dataType: 'html'
    }).done(function(result) {
      document.getElementById('messages').innerHTML=result;
      if (document.getElementById('login') !== null) {
        window.location.href = 'login.php';
      } 
      downloadAllMidia();
      var newUrl = 'messages.php?contactNickName=' + contact;
      history.pushState(null, '', newUrl);
      updateContacts(contact,name);
    }); 
  } else {
    window.location.href = 'messages.php?contactNickName='+contact;
  }
}

function updateContacts (contact = nickNameContact,name=nickNameContact) {
    var h2Elements = document.querySelectorAll('.contacts h2');
    h2Elements.forEach(function(h2) {
      h2.style.background = 'none';
      h2.style.color = '#2b5278';
      h2.style.boxShadow = 'none';
    }); 
    var spanElements = document.querySelectorAll('span[id^=\"'+contact+'\"]');
      spanElements.forEach(function (span) {
      span.remove();
    });
    var h2Element = document.querySelector('#contact'+contact+' h2');
    h2Element.style.color = 'white';
    h2Element.style.backgroundColor = '#2b5278';
    h2Element.style.boxShadow = '0px 0px 10px 5px rgba(0, 0, 0, 0.35)';
    document.getElementById('userName').innerHTML=name;
    var imgElement = document.querySelector('#picContact'+contact+' img');
    var imgContacts = document.querySelector('.picMessage img');
    imgContacts.style.backgroundImage = imgElement.style.backgroundImage;
    toggle(false);
}

function toggle(value = true, landscape=false) {
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
  const homeElement= document.querySelector('.home');

  homeElement.style.display = landscape? flexDisplay: hideDisplay;
  if (landscape) {
    value=true;
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

function newContact() {
  if (fetchNewMessages) {
    $.ajax({
      url: 'newContact.php?',
      method: 'POST',
      data: { nickNameContact: nickNameContact },
      dataType: 'json'
    }).done(function(result) {
      if (result !== "0") {
        document.getElementById('contacts').innerHTML = result;
        newMessages();
        responsiveCont();
      }
      newContact();
    });
  }
}

function responsiveCont () {
  var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
  var contactID = document.getElementById(nickNameContact);
  if (contactID) {
    if (screenWidth > screenHeight) {
      contactID.style.display='none';
    } else {
      contactID.style.display='inline';
    } 
  }
}

function newMessages() {
  $.ajax({
    url: 'newMsg.php?',
    method: 'POST',
    data: { nickNameContact: nickNameContact },
    dataType: 'json'
  }).done(function(result) {
    getAudioTimes();
    if (result[0] == "1") {
      document.getElementById('messages').innerHTML = result[1];
      if (((document.getElementById("messages").scrollTop) / h) * 100 >= 90) {
        down();
      } else {
        downButton(true);
      }
    } else if (result[0] == "2") {
      document.getElementById('messages').innerHTML = result[1];
      downButton(false);
    }
    timestamp = new Date().getTime();
    downloadAllMidia();
  });
}

function downButton(value) {
  var element = document.getElementById("down");
  var messagesElement = document.getElementById("messages");

  if (value) {
    messagesElement.style.boxShadow = "inset 0px -20px 8px 0px rgba(0, 0, 0, 0.35)";

    var img = document.createElement("img");
    img.onclick = down;
    img.style.position = "fixed";
    img.style.marginTop = "2%";
    img.style.boxShadow = "0px 2px 13px 15px rgba(0, 0, 0, 0.35)";
    img.style.borderRadius = "100%";
    img.style.background = "white";
    img.width = "50";
    img.src = "Images/down.svg";

    element.innerHTML = "";
    element.appendChild(img);
  } else {
    messagesElement.style.boxShadow = "none";
    element.innerHTML = "";
  }
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





