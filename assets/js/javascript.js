var msgsContents = "";
var fetchNewMessages = true;
var scrollPos = 0;
var h;
var profilePicSrc;
var updatedMsg = false;
main();

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

function down () {
  document.getElementById("messages").scrollTo(0,document.getElementById('messages').scrollHeight);
  downButton(false);
  h =  document.getElementById("messages").scrollTop;
} 

function removeDownButton () {
  if (((document.getElementById("messages").scrollTop)/h)*100 >= 99) {
    downButton(false);
    h =  document.getElementById("messages").scrollTop;
  }
}

function deleteMessage (id) {
  document.getElementById("msg"+id).remove();
  document.getElementById("del"+id).remove();
  document.getElementById("br"+id).remove();
  loading (true);
  $.ajax({
    url: 'delete.php?id='+id,
    method: 'POST',
    data: {nickNameContact: nickNameContact},
    dataType: 'json'
  }).done(function(result) {
    loading (false);
  });
}


function getDate () {
  currentDate = new Date();
  currentDate = currentDate.toLocaleString('pt-BR');
  currentDate = currentDate.split(" ")[1];
  currentDate = currentDate.split(":")[0]+":"+currentDate.split(":")[1];
  return currentDate;
}


var downloading = false;

function showPlayer(hash,tipo,extensao) {
    if (!downloading) {
        downloading = true;
        var videoDiv = document.getElementById(hash);
        videoDiv.style.backgroundImage = 'url(Images/download.gif)';
        videoDiv.style.backgroundSize = '40%';
        videoDiv.style.backgroundPositionY = '50%';
        videoDiv.style.backgroundRepeat = 'no-repeat';
        downloadBase64(hash)
        .then(function(dados) {
            var contentBlob = b64toBlob(dados, tipo+"/"+extensao);
            urlContent = URL.createObjectURL(contentBlob);
            videoDiv.style.backgroundImage = 'url(Images/play.svg)';
            videoDiv.style.backgroundSize = '';
            videoDiv.style.backgroundPositionY = '';
            embedVideo(urlContent,urlContent);
            downloading = false;
        })
        .catch(function(erro) {
            console.error(erro);
            // Trate o erro aqui, se necessário
        });
    }
}

function downloadFile(nomeHash, nome) {
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

async function downloadMidia(id,hash) {
  try {
    var parts = hash.split('.');
    var format = parts[parts.length - 1].toLowerCase();
    var dados = await downloadBase64(hash);
    var contentBlob = b64toBlob(dados, type(format) + '/' + format);
    var url = URL.createObjectURL(contentBlob);
    document.getElementById(id).src = url;
    usedURLs.set(hash, url); 
  } catch (erro) {
    console.error(erro);
    // Trate o erro aqui, se necessário
  }
}

async function downloadAllMidia() {
  if (typeof arrMidia !== 'undefined') {
    for (let i = arrMidia.length - 1; i >= 0; i--) {
      if (!updatedMsg) {
        let hash = arrMidia[i];
        let id = indexMidia[i];
        if (usedURLs.has(hash)) {
          document.getElementById(id).src = usedURLs.get(hash);
        } else {
          await downloadMidia(id, hash, usedURLs);
        }
      }
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

// Chame a função downloadAllMidia de uma função assíncrona
async function main() {
  try {
    await downloadAllMidia();
    // Outras operações após o download das mídias
  } catch (erro) {
    console.error(erro);
    // Trate o erro aqui, se necessário
  }
}

function embedYoutube(id) {
  updatedMsg = true;
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
  updatedMsg = true;
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

function embedImage(hash, id) {
  updatedMsg = true;
  var imageSrc = document.getElementById(id).src;
  fetchNewMessages = false;
  scrollPos = document.getElementById('messages').scrollTop;
  msgsContents = document.getElementById('messages').innerHTML;
  document.getElementById('messages').innerHTML = '';

  var aElement = document.createElement('a');
  aElement.href = '#';
  aElement.onclick = function() {
    downloadFile(hash, hash);
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
  updatedMsg = false;
  fetchNewMessages = true;
  newContact();
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
        attachmentDiv.style.backgroundColor = "hsl(132, 40%, 26%)";
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


function addMessage(msgID, text) {
  var deleteElement = document.createElement('div');
  deleteElement.classList.add('delete');
  deleteElement.id = 'del' + msgID;
  deleteElement.style.color = 'grey';
  deleteElement.style.marginLeft = '45%';
  deleteElement.style.marginRight = '2%';
  deleteElement.style.float = 'right';

  var deleteLink = document.createElement('a');
  deleteLink.href = '#';
  deleteLink.style.backgroundColor = '#1d8634';

  var deleteText = document.createElement('b');
  deleteText.appendChild(document.createTextNode('Apagar'));

  deleteLink.appendChild(deleteText);
  deleteLink.onclick = function() {
    deleteMessage(msgID);
  };

  deleteElement.appendChild(document.createTextNode('●●●'));
  deleteElement.appendChild(deleteLink);

  var brElement = document.createElement('br');
  brElement.id = 'br' + msgID;

  var msgElement = document.createElement('div');
  msgElement.classList.add('msg', 'msg-left');
  msgElement.id = 'msg' + msgID;
  msgElement.style.backgroundColor = '#1d8634';

  var fromSpan = document.createElement('span');
  fromSpan.classList.add('from');
  fromSpan.appendChild(document.createTextNode('You : '));

  var textParagraph = document.createElement('p');
  textParagraph.innerHTML = text; // Use innerHTML para inserir HTML

  var dateSpan = document.createElement('span');
  dateSpan.style.float = 'right';
  dateSpan.appendChild(document.createTextNode(getDate()));

  textParagraph.appendChild(document.createElement('br')); // Adiciona uma quebra de linha
  textParagraph.appendChild(dateSpan);

  msgElement.appendChild(fromSpan);
  msgElement.appendChild(textParagraph);

  var messagesElement = document.getElementById('messages');
  messagesElement.appendChild(deleteElement);
  messagesElement.appendChild(brElement);
  messagesElement.appendChild(msgElement);
}

function waitingMsg() {
  var messagesElement = document.getElementById('messages');
  var newDiv = document.createElement('div');
  newDiv.className = 'attachment_file uploading';
  newDiv.onclick = function() {
    updateMessages ();
  };

  var newImg = document.createElement('img');
  newImg.className = 'fileIcon';
  newImg.src = 'Images/loading.gif';

  var newLink = document.createElement('a');
  newLink.href = '#';
  newLink.appendChild(document.createTextNode('Enviado'));

  newDiv.appendChild(newImg);
  newDiv.appendChild(newLink);
  messagesElement.appendChild(newDiv);

  down();
}

function updateMessages (contact = nickNameContact) {
  $.ajax({
    url: 'updateMsg.php',
    method: 'POST',
    data: {contactNickName: contact},
    dataType: 'html'
  }).done(function(result) {
    document.getElementById('messages').innerHTML=result;
    down();
    updatedMsg=true;
    var newUrl = 'messages.php?contactNickName=' + contact;
    history.pushState(null, '', newUrl);
    updateContacts(contact);
  });
}

function updateContacts (contact = nickNameContact) {
    var h2Elements = document.querySelectorAll('.contacts h2');
    h2Elements.forEach(function(h2) {
      h2.style.background = 'none';
      h2.style.color = '#285d33';
      h2.style.boxShadow = 'none';
    }); 
    var h2Element = document.querySelector('#contact'+contact+' h2');
    h2Element.style.color = 'white';
    h2Element.style.backgroundColor = '#285d33';
    h2Element.style.boxShadow = '0px 0px 10px 5px rgba(0, 0, 0, 0.35)';
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
      }
      newContact();
    });
  }
}

function newMessages() {
  $.ajax({
    url: 'newMsg.php?',
    method: 'POST',
    data: { nickNameContact: nickNameContact },
    dataType: 'json'
  }).done(function(result) {
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