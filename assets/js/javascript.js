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


function loadPicStatus (value, keepPic=false) {
  if (typeof profilePic !== 'undefined') {
    if (value) {
      profilePic.src = "Images/remove.png";
      document.getElementById('stylePic').innerHTML+=".salvar {display:block;}";
      profilePicSrc = profilePic.style.backgroundImage;
    } else {
      profilePic.src = "Images/edit.png";
      document.getElementById('stylePic').innerHTML+=".salvar {display:none;}";
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
  var formData = new FormData();
  var arquivoInput = document.getElementById('editProfilePic');
  var arquivo = arquivoInput.files[0];
  formData.append('pic', arquivo);
  uploadFile('uploadPic.php',formData);
}

function uploadFile(url,formData) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url);
  xhr.onload = function() {
    if (xhr.status === 200) {
      if(xhr.responseText.length > 16) {
        alert(xhr.responseText);
      } else {
        loadPicStatus(false,true);
      }
    } else { 
      // Ocorreu um erro ao enviar o arquivo
      console.error(xhr.responseText);
    }
  };
  xhr.send(formData);
}

function uploadAttachment(url,formData) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', url);
  xhr.onload = function() {
    if (xhr.status === 200) {
      loading(false);
      updateMsg();
      var attachmentDiv = document.getElementById('attachment');
      attachmentDiv.style.backgroundColor = "";
    } else { 
      // Ocorreu um erro ao enviar o arquivo
      console.error(xhr.responseText);
    }
  };
  xhr.send(formData);
}


function down () {
  document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: none; }";
  document.getElementById("messages").scrollTo(0,document.getElementById('messages').scrollHeight);
  document.getElementById("down").innerHTML="";
  h =  document.getElementById("messages").scrollTop;
  
} 

function removeButtonDown () {
  if (((document.getElementById("messages").scrollTop)/h)*100 >= 99) {
    document.getElementById("down").innerHTML="";
    document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: none; }";
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



function embedYoutube (id) {
    updatedMsg = true;
    fetchNewMessages=false;
    scrollPos = document.getElementById('messages').scrollTop;
    msgsContents=document.getElementById('messages').innerHTML;
    style = "position: relative; border-radius: 100%; background-color: #285d3350; box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);width:70px; height:70px; top:0px;  margin-left: auto; margin-right: auto;background-size:50%; background-repeat:no-repeat;background-position-x: 50%; background-position-y: 50%; backdrop-filter: blur(5px);";
    document.getElementById('messages').innerHTML="<a href=\"https://youtu.be/"+id+"\" target=\"_blank\" style=\""+style+";float:left;background-image: url('Images/link.svg');\" ></a> <div onClick=\"closeVideo()\" style=\""+style+";float:right;background-image: url('Images/close.svg');\" ></div><iframe style=\"position: relative; margin-top: auto; margin-bottom: auto; top:0; bottom:0; left: 0; right:0; width:100%; height:100%; margin-left: auto; margin-right: auto;\" src=\"https://www.youtube.com/embed/"+id+"\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
}

function embedVideo(link,id) {
    updatedMsg = true;
    fetchNewMessages = false;
    scrollPos = document.getElementById('messages').scrollTop;
    msgsContents=document.getElementById('messages').innerHTML;
    style = "position: relative; border-radius: 100%; background-color: #285d3350; box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);width:70px; height:70px; top:0px;  margin-left: auto; margin-right: auto;background-size:50%; background-repeat:no-repeat;background-position-x: 50%; background-position-y: 50%; backdrop-filter: blur(5px);";
    document.getElementById('messages').innerHTML = "<a href=\"" + link + "\" target=\"_blank\" style=\"" + style + ";float:left;background-image: url('Images/link.svg');\" ></a> <div onClick=\"closeVideo()\" style=\"" + style + ";float:right;background-image: url('Images/close.svg');\" ></div><iframe style=\"position: relative; margin-top: auto; margin-bottom: auto; top:0; bottom:0; left: 0; right:0; width:100%; height:100%; margin-left: auto; margin-right: auto;\" src=\"" + id + "\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
}

function embedImage(id,hash) {
    updatedMsg = true;
    var imageSrc = document.getElementById(id).src;
    fetchNewMessages = false;
    scrollPos = document.getElementById('messages').scrollTop;
    msgsContents=document.getElementById('messages').innerHTML;
    style = "z-index:10000;position: relative; border-radius: 100%; background-color: #285d3350; box-shadow: 0px 0px 10px 5px rgb(0 0 0 / 35%);width:70px; height:70px; top:0px;  margin-left: auto; margin-right: auto;background-size:50%; background-repeat:no-repeat;background-position-x: 50%; background-position-y: 50%; backdrop-filter: blur(5px);";
    document.getElementById('messages').innerHTML = "<a href=\"#\" onClick=\"downloadFile('"+hash+"','"+hash+"');\" style=\"" + style + ";float:left;background-image: url('Images/link.svg');\" ></a> <div onClick=\"closeImage()\" style=\"" + style + ";float:right;background-image: url('Images/close.svg');\" ></div><div style=\"overflow-x: auto; position: relative; margin-top: auto; margin-bottom: auto; top:0; bottom:0; left: 0; right:0; width:100%; height:100%; margin-left: auto; margin-right: auto;\"><center style=\"background-color: rgb(0,0,0);\"><img height=100% src=\""+imageSrc+"\"></center></div>";
}

function closeVideo() {
    updatedMsg = false;
    fetchNewMessages = true;
    newContact();
    document.getElementById('messages').innerHTML=msgsContents;
    document.getElementById('messages').scrollTo(0, scrollPos);
    newContact();
    downloadAllMidia();
}

function closeImage() {
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
            date = getDate ();
            id = result;
            $.ajax({
              url: 'getThumb.php?',
              method: 'GET',
              data: {msg: messageText},
              dataType: 'html'
            }).done(function(text) {
              document.getElementById('messages').innerHTML+="<div class='delete' id=\"del"+id+"\" style='color:grey;margin-left:45%;margin-right:2%;float:right;'> ●●●<a href='#' style='background-color:#1d8634' onclick=\"deleteMessage('"+id+"');\"><b>Apagar</b></a></div><br id='br"+id+"'><div class=\"msg msg-left\" id=\"msg"+id+"\" style=\"background-color:#1d8634;\"><span class=\"from\">You : </span><p>"+text+"<br><span style=\"float:right;\">"+ date +"</span></p></div>"
              down ();
            });
            loading (false);
      });
  } else {
    loading(true);
    var formData = new FormData();
    var arquivo = inputFile.files[0];
    formData.append('arquivo',arquivo);
    formData.append('messageText',messageText);
    formData.append('contactNickName',nickNameContact);
    uploadAttachment('uploadfile.php',formData);
    waitingMsg();
    inputFile.value="";
  }
}        

function waitingMsg () {
  date = getDate ();
  document.getElementById('messages').innerHTML+="<div onclick=\"updateMsg()\" class=\"attachment_file uploading\"> <img class=\"fileIcon\" src=\"Images/loading.gif\"/> <a href=\"#\" >Enviado</a> </div>";
  down();
}

function updateMsg () {
  $.ajax({
    url: 'updateMsg.php',
    method: 'POST',
    data: {contactNickName: nickNameContact},
    dataType: 'html'
  }).done(function(result) {
    document.getElementById('messages').innerHTML=result;
    down();
    updatedMsg=true;
  });
}

function newContact() {
    if (fetchNewMessages) {
        $.ajax({
          url: 'newContact.php?',
          method: 'POST',
          data: {nickNameContact: nickNameContact},
          dataType: 'json'
        }).done(function(result) {
          if (result !== "0") {
            document.getElementById('contacts').innerHTML=result;
            $.ajax({
              url: 'newMsg.php?',
              method: 'POST',
              data: {nickNameContact: nickNameContact},
              dataType: 'json'
            }).done(function(result) {
              if (result[0] == "1") {
                document.getElementById('messages').innerHTML=result[1];
                if (((document.getElementById("messages").scrollTop)/h)*100 >= 90) {
                  down();
                } else {
                  document.getElementById("styleIndex").innerHTML+="#messages {box-shadow: inset 0px -20px 8px 0px rgb(0 0 0 / 35%) }";
                  document.getElementById("down").innerHTML="<img  onclick='down();' style='position:fixed;margin-top:2%;box-shadow: 0px 2px 13px 15px rgb(0 0 0 / 35%); border-radius: 100%; background:white;' width='50px' src='Images/down.svg'/> ";
                }
              } else if (result[0] == "2")  {
                document.getElementById("styleIndex").innerHTML+="#messages {box-shadow:none }";
                document.getElementById('messages').innerHTML=result[1];
                document.getElementById("down").innerHTML="";
              }
            });
          }
          newContact();
        });
    }    
}         

function loading (b) {
    if (b) {
        document.getElementById("styleIndex").innerHTML+=".send {background:none; background-size:100%; background-repeat:no-repeat; background-image: url(\"Images/loading.gif\"); background-position-y:50%; background-position-x: 50%; }";
    } else {
        document.getElementById("styleIndex").innerHTML="";
    }
}  