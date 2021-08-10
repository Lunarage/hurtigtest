function saveInfo(){
  const name = document.getElementById("input-name");
  const tlf = document.getElementById("input-tlf");
  const mail = document.getElementById("input-mail");

  let storage = window.localStorage;

  storage.setItem("name", name.value);
  storage.setItem("tlf", tlf.value);
  sotrage.setItem("mail", mail.value);
}

function validateForm(){
  // TODO: Check stuff?
  saveInfo();
  return true;
}

